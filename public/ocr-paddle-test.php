#!/usr/bin/env python3
import sys
import os

def main():
    if len(sys.argv) < 2:
        print("Usage: ocr_engine.py <image_path>", file=sys.stderr)
        sys.exit(2)

    image_path = sys.argv[1]
    if not os.path.isfile(image_path):
        print(f"ERROR: image not found: {image_path}", file=sys.stderr)
        sys.exit(2)

    try:
        from paddleocr import PaddleOCR
    except ImportError as e:
        print(f"ERROR: PaddleOCR not installed: {e}", file=sys.stderr)
        sys.exit(3)

    # Key fix: enable_mkldnn=False avoids the PaddlePaddle 3.3.x oneDNN bug
    # use_textline_orientation=True replaces the old use_angle_cls / cls=True
    ocr = PaddleOCR(
        use_doc_orientation_classify=False,
        use_doc_unwarping=False,
        use_textline_orientation=True,
        enable_mkldnn=False,          # 👈 disables oneDNN, resolves the crash
        lang="en",
    )

    lines = []

    try:
        # 3.x uses predict(); completely remove cls=True
        result = ocr.predict(image_path)
        for res in result:
            texts = res.get("rec_texts") if hasattr(res, "get") else None
            if texts:
                lines.extend(texts)
    except Exception as e:
        print(f"ERROR: predict failed: {e}", file=sys.stderr)

    if not lines:
        print("ERROR: no text detected", file=sys.stderr)
        sys.exit(5)

    for line in lines:
        print(line)

if __name__ == "__main__":
    main()