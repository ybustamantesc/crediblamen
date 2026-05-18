<?php
require_once __DIR__ . '/../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$in = __DIR__ . '/estado_cuenta_debug.html';
$minimal_html_file = __DIR__ . '/estado_cuenta_minimal.html';
$out = __DIR__ . '/estado_cuenta_minimal2.pdf';
if (!file_exists($in)) { echo "Missing input HTML: $in\n"; exit(1); }
$html = file_get_contents($in);

$matches = array();
if (preg_match('#<table[^>]*class="big-table"[^>]*>(.*?)</table>#si', $html, $matches)) {
    $table_html = $matches[0]; // include the whole table tag
} elseif (preg_match('#<table[^>]*class="ec-big-table"[^>]*>(.*?)</table>#si', $html, $matches)) {
    $table_html = $matches[0];
} else {
    echo "Could not find main table in debug HTML.\n"; exit(2);
}

$minimal = '<!doctype html><html><head><meta charset="utf-8"/><title>Minimal Estado</title>';
$minimal .= '<style>body{font-family: DejaVu Sans, Arial, sans-serif; font-size:10px;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid #999;padding:6px;} .text-right{text-align:right;}</style></head><body>';
$minimal .= '<h3 style="text-align:center">ESTADO DE CUENTA - MINIMAL</h3>';
$minimal .= $table_html;
$minimal .= '</body></html>';

file_put_contents($minimal_html_file, $minimal);
echo "Saved minimal HTML to: $minimal_html_file\n\n---- START OF MINIMAL HTML ----\n";
// print first 200 lines
$lines = preg_split('/\r?\n/', $minimal);
$max = min(200, count($lines));
for ($i=0;$i<$max;$i++) echo $lines[$i] . "\n";
echo "---- END OF PREVIEW ----\n";

$dom = new Dompdf();
$options = $dom->getOptions();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');
$dom->setOptions($options);
$dom->loadHtml($minimal);
$dom->setPaper('A4', 'portrait');
try {
    $dom->render();
    file_put_contents($out, $dom->output());
    echo "\nRendered minimal PDF to: $out\n";
} catch (Exception $e) {
    echo "Render error: " . $e->getMessage() . "\n";
    exit(3);
}

?>
