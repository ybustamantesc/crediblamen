<?php
// Analiza el archivo CSV de saldos iniciales y muestra el resumen por tipo de cuenta y diferencia
$csvFile = 'c:\xampp\htdocs\Conta\temp\saldos_iniciales_importar.csv';
if (!file_exists($csvFile)) {
    die("No se encontró el archivo CSV\n");
}
$handle = fopen($csvFile, 'r');
$headers = fgetcsv($handle);
$col_codigo = 0;
$col_nombre = 1;
$col_saldo = 2;
$resumen = [
    'activo' => 0.0,
    'pasivo' => 0.0,
    'patrimonio' => 0.0,
    'ingreso' => 0.0,
    'gasto' => 0.0
];
while (($row = fgetcsv($handle)) !== false) {
    if (count($row) < 3) continue;
    $codigo = trim($row[$col_codigo]);
    $saldo_str = str_replace([',', '"', ' ', '$'], '', trim($row[$col_saldo]));
    if ($saldo_str === '-' || $saldo_str === '' || $saldo_str === 'nan') {
        $saldo = 0.0;
    } else {
        $saldo = floatval($saldo_str);
    }
    $primer_digito = substr($codigo, 0, 1);
    switch ($primer_digito) {
        case '1': $tipo = 'activo'; break;
        case '2': $tipo = 'pasivo'; break;
        case '3': $tipo = 'patrimonio'; break;
        case '4': $tipo = 'ingreso'; break;
        case '5':
        case '6':
        case '7': $tipo = 'gasto'; break;
        default: $tipo = 'activo';
    }
    // Sumar según naturaleza
    if (in_array($tipo, ['activo', 'gasto'])) {
        $resumen[$tipo] += $saldo;
    } else {
        $resumen[$tipo] -= $saldo;
    }
}
fclose($handle);
$total_debe = $resumen['activo'] + $resumen['gasto'];
$total_haber = -($resumen['pasivo'] + $resumen['patrimonio'] + $resumen['ingreso']);
$diferencia = $total_debe - $total_haber;
echo "Total Debe: $total_debe\n";
echo "Total Haber: $total_haber\n";
echo "Diferencia: $diferencia\n";
echo "Resumen por tipo:\n";
foreach ($resumen as $tipo => $valor) {
    echo "$tipo: $valor\n";
}
?>