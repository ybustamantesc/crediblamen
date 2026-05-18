<?php
// Simple test: render a single image into PDF using Dompdf
$sol = isset($_GET['sol']) ? intval($_GET['sol']) : 6;
$upload_dir = __DIR__ . '/uploads/garantias/solicitud_' . $sol . '/';
$files = glob($upload_dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);
if (empty($files)) { echo "no files"; exit; }
$f = $files[0];

if (file_exists(__DIR__ . '/vendor/autoload.php')) require_once __DIR__ . '/vendor/autoload.php';
if (! class_exists('\\Dompdf\\Dompdf')) {
    if (file_exists(__DIR__ . '/dompdf/autoload.inc.php')) require_once __DIR__ . '/dompdf/autoload.inc.php';
}
if (! class_exists('\\Dompdf\\Dompdf')) { echo "no dompdf"; exit; }

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $f) ?: 'image/png';
$data = base64_encode(file_get_contents($f));
$uri = 'data:' . $mime . ';base64,' . $data;

$html = '<html><body><h3>Test image</h3><img src="' . $uri . '" style="width:400px"></body></html>';

$options = new Dompdf\Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$options->set('tempDir', __DIR__ . '/tmp');
$dompdf = new Dompdf\Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->render();
$dompdf->stream('testimg.pdf', ['Attachment' => 1]);

exit;
