<?php
// Simple standalone PDF generator: one image per page (no CSS grid)
// Usage: http://localhost/servicredit/generate_photos_pdf_simple.php?solicitud=6

ini_set('display_errors', 1);
error_reporting(E_ALL);
$sol = isset($_GET['solicitud']) ? intval($_GET['solicitud']) : 0;
if (! $sol) { echo 'Missing solicitud'; exit; }
$upload_dir = __DIR__ . '/uploads/garantias/solicitud_' . $sol . '/';
if (! is_dir($upload_dir)) { echo 'No dir'; exit; }
$files = glob($upload_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
if (empty($files)) { echo 'No files'; exit; }

if (file_exists(__DIR__ . '/vendor/autoload.php')) require_once __DIR__ . '/vendor/autoload.php';
if (! class_exists('\\Dompdf\\Dompdf')) {
    if (file_exists(__DIR__ . '/dompdf/autoload.inc.php')) require_once __DIR__ . '/dompdf/autoload.inc.php';
}
if (! class_exists('\\Dompdf\\Dompdf')) { echo 'no dompdf'; exit; }

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$photos = [];
foreach ($files as $f) {
    $mime = finfo_file($finfo, $f) ?: 'image/jpeg';
    $data = base64_encode(file_get_contents($f));
    $photos[] = 'data:' . $mime . ';base64,' . $data;
}

$html = '<html><head><meta charset="utf-8"><style>body{margin:0;padding:0} .page{page-break-after:always;padding:10px} img{width:100%;height:auto}</style></head><body>';
foreach ($photos as $p) {
    $html .= '<div class="page"><img src="' . $p . '" alt="foto"></div>';
}
$html .= '</body></html>';

$options = new Dompdf\Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('tempDir', __DIR__ . '/tmp');
$dompdf = new Dompdf\Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('garantias_simple_' . $sol . '.pdf', ['Attachment' => 1]);
exit;
