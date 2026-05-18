<?php
require_once __DIR__ . '/../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$in = __DIR__ . '/estado_cuenta_debug.html';
$out = __DIR__ . '/estado_cuenta_text.pdf';
if (!file_exists($in)) { echo "Missing input HTML: $in\n"; exit(1); }
$html = file_get_contents($in);

// Extract tbody rows as plain text
$rows_text = '';
if (preg_match('#<tbody>(.*?)</tbody>#si', $html, $m)) {
    $tbody = $m[1];
    // remove tags, collapse spaces
    $plain = trim(strip_tags($tbody));
    $plain = preg_replace('/\s{2,}/', ' ', $plain);
    $rows_text = $plain;
} else {
    echo "No tbody found\n"; exit(2);
}

$doc = '<!doctype html><html><head><meta charset="utf-8"/><title>Text Table</title>';
$doc .= '<style>body{font-family: DejaVu Sans, Arial, sans-serif; font-size:10px; white-space:pre-wrap;}</style></head><body>';
$doc .= "ESTADO DE CUENTA - TEXT VERSION\n\n";
$doc .= '<pre>' . htmlspecialchars($rows_text) . '</pre>';
$doc .= '</body></html>';

$dom = new Dompdf();
$options = $dom->getOptions();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');
$dom->setOptions($options);
$dom->loadHtml($doc);
$dom->setPaper('A4', 'portrait');
try {
    $dom->render();
    file_put_contents($out, $dom->output());
    echo "Rendered text PDF to: $out\n";
} catch (Exception $e) {
    echo "Render error: " . $e->getMessage() . "\n";
    exit(3);
}

?>
