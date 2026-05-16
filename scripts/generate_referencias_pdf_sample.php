<?php
// Script para generar un PDF de ejemplo usando dompdf (CLI)
require_once __DIR__ . '/../dompdf/autoload.inc.php';
use Dompdf\Dompdf;

$html = '<!doctype html><html><head><meta charset="utf-8"><style>body{font-family:Arial,Helvetica,sans-serif;font-size:12px;} .card{border:1px solid #ddd;padding:8px;margin:8px;} .title{font-weight:bold}</style></head><body>';
$html .= '<div style="text-align:center;"><h2>Referencias - Ejemplo</h2></div>';
for ($i=1;$i<=2;$i++) {
    $html .= '<div class="card" style="page-break-after:always">';
    $html .= '<div class="title">Referencia ' . $i . '</div>';
    $html .= '<table style="width:100%;border-collapse:collapse;margin-top:6px;">';
    $html .= '<tr><td style="border:1px solid #ddd;padding:6px;width:30%"><strong>Nombre</strong></td><td style="border:1px solid #ddd;padding:6px">Ejemplo Nombre ' . $i . '</td></tr>';
    $html .= '<tr><td style="border:1px solid #ddd;padding:6px"><strong>Cédula</strong></td><td style="border:1px solid #ddd;padding:6px">123456789' . $i . '</td></tr>';
    $html .= '<tr><td style="border:1px solid #ddd;padding:6px"><strong>Teléfono</strong></td><td style="border:1px solid #ddd;padding:6px">0999123' . $i . '</td></tr>';
    $html .= '</table>';
    $html .= '<div style="margin-top:8px;display:flex;gap:8px;"><div style="flex:1;height:200px;border:1px dashed #ccc;display:flex;align-items:center;justify-content:center;color:#999">Frontal (ejemplo)</div><div style="flex:1;height:200px;border:1px dashed #ccc;display:flex;align-items:center;justify-content:center;color:#999">Trasera (ejemplo)</div></div>';
    $html .= '</div>';
}
$html .= '</body></html>';

$dompdf = new Dompdf();
$dompdf->setPaper('A4', 'portrait');
$dompdf->loadHtml($html);
$dompdf->render();
$out = $dompdf->output();
$tmpdir = __DIR__ . '/../temp/';
if (!is_dir($tmpdir)) @mkdir($tmpdir, 0755, true);
$file = $tmpdir . 'Referencias_sample.pdf';
file_put_contents($file, $out);
echo "PDF generado en: " . $file . PHP_EOL;
