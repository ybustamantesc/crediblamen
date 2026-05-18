<?php
require_once __DIR__ . '/../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$in = __DIR__ . '/estado_cuenta_debug.html';
$out = __DIR__ . '/estado_cuenta_minimal.pdf';
if (!file_exists($in)) { echo "Missing input HTML: $in\n"; exit(1); }
$html = file_get_contents($in);

// Extract the big-table contents (thead + tbody + tfoot)
$matches = array();
if (preg_match('#<table[^>]*class="big-table"[^>]*>(.*?)</table>#si', $html, $matches)) {
    $table_html = $matches[1];
} elseif (preg_match('#<table[^>]*class="ec-big-table"[^>]*>(.*?)</table>#si', $html, $matches)) {
    $table_html = $matches[1];
} else {
    echo "Could not find main table in debug HTML.\n"; exit(2);
}

$minimal = '<!doctype html><html><head><meta charset="utf-8"/><title>Minimal Estado</title>';
$minimal .= '<style>body{font-family: DejaVu Sans, Arial, sans-serif; font-size:10px;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid #999;padding:6px;} .text-right{text-align:right;}</style></head><body>';
$minimal .= '<h3 style="text-align:center">ESTADO DE CUENTA - MINIMAL</h3>';
$minimal .= '<table>' . $table_html . '</table>';
$minimal .= '</body></html>';

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
    echo "Rendered minimal PDF to: $out\n";
} catch (Exception $e) {
    echo "Render error: " . $e->getMessage() . "\n";
    exit(3);
}

?>
