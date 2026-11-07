<?php
// Raw paths (no Laravel helpers needed)
$pythonScript = __DIR__ . '/../app/scripts/ocr_engine.py';
$imagePath    = __DIR__ . '/nationalid.png';
$typedName    = 'MYKA NICHOLE CABRERA';

echo "==================================================\n";
echo "TEST: PaddleOCR PHP Integration\n";
echo "Image: " . basename($imagePath) . "\n";
echo "Typed Name: \"$typedName\"\n";
echo "--------------------------------------------------\n";

if (!file_exists($imagePath)) {
    die("❌ Image not found at: $imagePath\n");
}

if (!file_exists($pythonScript)) {
    die("❌ Python script not found at: $pythonScript\n");
}

// Build the command to run Python 3.12
$command = 'py -3.12 ' . escapeshellarg($pythonScript) . ' ' . escapeshellarg($imagePath) . ' 2>&1';

echo "Running PaddleOCR... (This may take a few seconds)\n";

// Execute the Python script
$output = shell_exec($command);

if (empty(trim($output))) {
    die("❌ OCR returned no text. Check that Python 3.12 and PaddleOCR are installed.\n");
}

echo "✅ OCR Successful.\n\n";
echo "--- RAW OCR TEXT ---\n";
echo $output . "\n";
echo "--------------------\n\n";

// Normalize and match
function normalizeName(string $s): string
{
    $s = strtoupper($s);
    $s = preg_replace('/[^A-Z\s]/', '', $s);
    $s = preg_replace('/\s+/', ' ', $s);
    return trim($s);
}

$normalizedTyped = normalizeName($typedName);
$normalizedOcr   = normalizeName($output);

$words   = explode(' ', $normalizedTyped);
$found   = [];
$missing = [];

foreach ($words as $word) {
    if ($word === '') continue;
    if (strpos($normalizedOcr, $word) !== false) {
        $found[] = $word;
    } else {
        $missing[] = $word;
    }
}

if (count($missing) === 0) {
    echo "✅ MATCH — Name found on ID.\n";
    echo "   Found words: " . implode(', ', $found) . "\n";
} else {
    echo "❌ MISMATCH — Name not found on ID.\n";
    echo "   Found: "   . implode(', ', $found) . "\n";
    echo "   Missing: " . implode(', ', $missing) . "\n";
}
