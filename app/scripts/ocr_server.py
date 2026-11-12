import contextlib
import io
import logging
import os
import tempfile
import threading

from flask import Flask, jsonify, request

os.environ["PADDLE_PDX_DISABLE_MODEL_SOURCE_CHECK"] = "True"
os.environ.setdefault("OMP_NUM_THREADS", "4")
os.environ.setdefault("OPENBLAS_NUM_THREADS", "4")

logging.getLogger("ppocr").setLevel(logging.ERROR)
logging.getLogger("paddle").setLevel(logging.ERROR)

from paddleocr import PaddleOCR

app = Flask(__name__)
app.config["MAX_CONTENT_LENGTH"] = 5 * 1024 * 1024

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


@app.post("/scan")
def scan():
    uploaded_file = request.files.get("image")
    if uploaded_file is None:
        return jsonify({"error": "No image file provided."}), 400

    suffix = os.path.splitext(uploaded_file.filename or "")[1][:10]
    temp_path = None

    try:
        with tempfile.NamedTemporaryFile(delete=False, suffix=suffix) as temp_file:
            temp_path = temp_file.name
            uploaded_file.save(temp_file)

        output = io.StringIO()
        with ocr_lock, contextlib.redirect_stdout(output):
            results = ocr.predict(temp_path)

        lines = []
        if results:
            for result in results:
                texts = result.get("rec_texts", [])
                if isinstance(texts, list):
                    lines.extend(text for text in texts if isinstance(text, str))

        return jsonify({"text": lines})
    except Exception:
        app.logger.exception("OCR processing failed.")
        return jsonify({"error": "OCR processing failed."}), 500
    finally:
        if temp_path and os.path.exists(temp_path):
            os.remove(temp_path)


if __name__ == "__main__":
    app.run(host="127.0.0.1", port=5000, threaded=True, use_reloader=False)
