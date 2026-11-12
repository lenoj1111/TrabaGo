import contextlib
import json
import io
import logging
import os
import tempfile
import threading
from email import policy
from email.parser import BytesParser
from http.server import BaseHTTPRequestHandler, ThreadingHTTPServer

os.environ["PADDLE_PDX_DISABLE_MODEL_SOURCE_CHECK"] = "True"
os.environ.setdefault("OMP_NUM_THREADS", "4")
os.environ.setdefault("OPENBLAS_NUM_THREADS", "4")

logging.getLogger("ppocr").setLevel(logging.ERROR)
logging.getLogger("paddle").setLevel(logging.ERROR)

from paddleocr import PaddleOCR

print("Loading PaddleOCR models. Please wait...")
ocr = PaddleOCR(
    lang="en",
    text_detection_model_name="PP-OCRv4_mobile_det",
    text_recognition_model_name="en_PP-OCRv4_mobile_rec",
    use_doc_orientation_classify=False,
    use_doc_unwarping=False,
    use_textline_orientation=False,
    text_det_limit_side_len=960,
    enable_mkldnn=False,
)
print("Models loaded! Server is ready.")

ocr_lock = threading.Lock()

MAX_REQUEST_BYTES = 5 * 1024 * 1024 + 64 * 1024


class OcrRequestHandler(BaseHTTPRequestHandler):
    def send_json(self, status_code, payload):
        body = json.dumps(payload).encode("utf-8")
        self.send_response(status_code)
        self.send_header("Content-Type", "application/json; charset=utf-8")
        self.send_header("Content-Length", str(len(body)))
        self.end_headers()
        self.wfile.write(body)

    def do_POST(self):
        if self.path != "/scan":
            self.send_json(404, {"error": "Endpoint not found."})
            return

        try:
            content_length = int(self.headers.get("Content-Length", "0"))
        except ValueError:
            self.send_json(400, {"error": "Invalid content length."})
            return

        if content_length <= 0 or content_length > MAX_REQUEST_BYTES:
            self.send_json(413, {"error": "Request is empty or exceeds the 5 MB upload limit."})
            return

        content_type = self.headers.get("Content-Type", "")
        try:
            request_body = self.rfile.read(content_length)
            mime_message = BytesParser(policy=policy.default).parsebytes(
                f"Content-Type: {content_type}\r\nMIME-Version: 1.0\r\n\r\n".encode("ascii")
                + request_body
            )
        except (UnicodeEncodeError, ValueError):
            self.send_json(400, {"error": "Invalid multipart upload."})
            return

        image_part = next(
            (
                part for part in mime_message.iter_parts()
                if part.get_param("name", header="content-disposition") == "image"
            ),
            None,
        ) if mime_message.is_multipart() else None

        image_bytes = image_part.get_payload(decode=True) if image_part else None
        if not image_bytes:
            self.send_json(400, {"error": "No image file provided."})
            return

        filename = image_part.get_filename() or "upload.png"
        suffix = os.path.splitext(filename)[1].lower()
        if suffix not in {".jpg", ".jpeg", ".png"}:
            self.send_json(415, {"error": "Only JPG, JPEG, and PNG images are supported."})
            return

        temp_path = None
        try:
            with tempfile.NamedTemporaryFile(delete=False, suffix=suffix) as temp_file:
                temp_path = temp_file.name
                temp_file.write(image_bytes)

            output = io.StringIO()
            with ocr_lock, contextlib.redirect_stdout(output):
                results = ocr.predict(temp_path)

            lines = []
            if results:
                for result in results:
                    texts = result.get("rec_texts", [])
                    if isinstance(texts, list):
                        lines.extend(text for text in texts if isinstance(text, str))

            self.send_json(200, {"text": lines})
        except Exception:
            logging.exception("OCR processing failed.")
            self.send_json(500, {"error": "OCR processing failed."})
        finally:
            if temp_path and os.path.exists(temp_path):
                os.remove(temp_path)


if __name__ == "__main__":
    server = ThreadingHTTPServer(("127.0.0.1", 5000), OcrRequestHandler)
    print("OCR service listening on http://127.0.0.1:5000")
    try:
        server.serve_forever()
    except KeyboardInterrupt:
        pass
    finally:
        server.server_close()
