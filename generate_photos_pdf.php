<?php
// Standalone PDF generator for guarantees photos (2x2 per page)
// Usage: http://localhost/servicredit/generate_photos_pdf.php?solicitud=6

ini_set('display_errors', 1);
error_reporting(E_ALL);

$sol = isset($_GET['solicitud']) ? intval($_GET['solicitud']) : 0;
if (! $sol) {
    echo "Missing solicitud id";
    exit;
}

$upload_dir = __DIR__ . '/uploads/garantias/solicitud_' . $sol . '/';
if (! is_dir($upload_dir)) {
    echo "No upload directory: " . htmlspecialchars($upload_dir);
    exit;
}

$files = glob($upload_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
if (empty($files)) {
    echo "No image files found in " . htmlspecialchars($upload_dir);
    exit;
}

// load dompdf autoload
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}
if (! class_exists('\\Dompdf\\Dompdf')) {
    if (file_exists(__DIR__ . '/dompdf/autoload.inc.php')) {
        require_once __DIR__ . '/dompdf/autoload.inc.php';
    } elseif (file_exists(__DIR__ . '/application/third_party/dompdf/autoload.inc.php')) {
        require_once __DIR__ . '/application/third_party/dompdf/autoload.inc.php';
    }
}
if (! class_exists('\\Dompdf\\Dompdf')) {
    echo "Dompdf not available";
    exit;
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$photos = [];
$use_file_uri = isset($_GET['use_file']) && $_GET['use_file'] == '1';
foreach ($files as $f) {
    if ($use_file_uri) {
        // build a file:// URI (Windows-safe)
        $path = str_replace('\\', '/', $f);
        if (strpos($path, '/') !== 0 && preg_match('#^[A-Za-z]:/#', $path) !== 1) {
            $path = '/' . $path;
        }
        $photos[] = 'file://' . $path;
        continue;
    }
    $mime = finfo_file($finfo, $f) ?: 'image/jpeg';
    $data = base64_encode(file_get_contents($f));
    $photos[] = 'data:' . $mime . ';base64,' . $data;
}
finfo_close($finfo);

// Build minimal HTML
$html = '<!doctype html><html><head><meta charset="utf-8"><style>
body{font-family:Arial,Helvetica,sans-serif;margin:0;padding:0}
.header{padding:10px 20px;border-bottom:1px solid #ddd}
.title{font-size:18px}
.page{page-break-after:always;padding:10px}
.grid{display:grid;grid-template-columns:1fr 1fr;grid-gap:10px}
.grid img{width:100%;height:auto;border:1px solid #ccc}
</style></head><body>';
$html .= '<div class="header"><div class="title">Formato de Garantías - Solicitud ' . htmlspecialchars($sol) . '</div></div>';

// chunk photos 4 per page
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
if ($limit > 0) {
    $photos = array_slice($photos, 0, $limit);
}

$chunks = array_chunk($photos, 4);
foreach ($chunks as $chunk) {
    $html .= '<div class="page"><div class="grid">';
    // ensure 4 cells
    for ($i = 0; $i < 4; $i++) {
        if (isset($chunk[$i])) {
            $html .= '<div><img src="' . $chunk[$i] . '" alt="foto"></div>';
        } else {
            $html .= '<div></div>';
        }
    }
    $html .= '</div></div>';
}

$html .= '</body></html>';

// Debug output: if ?debug=1 present, output HTML for inspection
if (isset($_GET['debug']) && $_GET['debug'] == '1') {
    header('Content-Type: text/html; charset=utf-8');
    echo $html;
    exit;
}

// Render with Dompdf
$options = new Dompdf\Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$tempDir = __DIR__ . '/tmp';
if (! is_dir($tempDir)) @mkdir($tempDir, 0755, true);
$options->set('tempDir', $tempDir);

$dompdf = new Dompdf\Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('garantias_solicitud_' . $sol . '.pdf', ['Attachment' => 1]);

exit;
