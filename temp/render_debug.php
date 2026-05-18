<?php
require_once __DIR__ . '/../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$in = __DIR__ . '/estado_cuenta_debug.html';
$out = __DIR__ . '/estado_cuenta_rendered.pdf';
if (!file_exists($in)) { echo "Missing input HTML: $in\n"; exit(1); }
$html = file_get_contents($in);
$dom = new Dompdf();
$options = $dom->getOptions();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');
$dom->setOptions($options);
$dom->loadHtml($html);
$dom->setPaper('A4', 'portrait');
try {
    $dom->render();
    file_put_contents($out, $dom->output());
    echo "Rendered to: $out\n";
} catch (Exception $e) {
    echo "Render error: " . $e->getMessage() . "\n";
    exit(2);
}

?>
