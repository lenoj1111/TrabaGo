import sys
import os
import io
import contextlib
import logging

# Silence logs
logging.getLogger("ppocr").setLevel(logging.ERROR)
logging.getLogger("paddle").setLevel(logging.ERROR)
os.environ["PADDLE_PDX_DISABLE_MODEL_SOURCE_CHECK"] = "True"

try:
    from paddleocr import PaddleOCR
except ImportError:
    print("ERROR: PaddleOCR is not installed.", file=sys.stderr)
    sys.exit(1)

def main():
    if len(sys.argv) < 2:
        print("ERROR: No image path provided.", file=sys.stderr)
        sys.exit(1)

    image_path = sys.argv[1]
    if not os.path.exists(image_path):
        print(f"ERROR: Image not found at {image_path}", file=sys.stderr)
        sys.exit(1)

    try:
        buffer = io.StringIO()
        with contextlib.redirect_stdout(buffer):
            ocr = PaddleOCR(
                lang='en',
                # --- SPEED OPTIMIZATIONS ---
                text_detection_model_name="PP-OCRv4_mobile_det",
                text_recognition_model_name="en_PP-OCRv4_mobile_rec",
                use_doc_orientation_classify=False,
                use_doc_unwarping=False,
                use_textline_orientation=False,
                text_det_limit_side_len=960,
                # Keep this to avoid the Windows crash
                enable_mkldnn=False
            )
            result = ocr.predict(image_path)

        extracted_lines = []
        if result:
            for res in result:
                if 'rec_texts' in res:
                    extracted_lines.extend(res['rec_texts'])

        for text in extracted_lines:
            print(text)

    except Exception as e:
        print(f"ERROR: OCR Processing failed - {str(e)}", file=sys.stderr)
        sys.exit(1)

if __name__ == "__main__":
    main()