<?php
$mysqli = new mysqli('localhost', 'root', '', 'crediblamen');
if ($mysqli->connect_error) {
    http_response_code(500);
    echo "ERROR: " . $mysqli->connect_error;
    exit;
}
$csv = isset($_GET['csv']) ? $_GET['csv'] : '';
$allowed = [
    'CargaCredito13p1.csv',
    'CargaCredito14p.csv',
    'CargaCredito15p.csv',
    'CargaCredito12p.csv',
    'CargaCredito.csv'
];
if ($csv === '' || !in_array($csv, $allowed, true)) {
    echo "Falta parametro ?csv=... (permitidos: " . implode(', ', $allowed) . ")";
    exit;
}
$tmpPath = "C:/xampp/htdocs/Crediblamen/temp/{$csv}";
if (!file_exists($tmpPath)) {
    echo "No existe el archivo: {$csv}";
    exit;
}
// Solo cuenta lo que está en staging actual
$stgCount = 0;
$impCount = 0;
$res = $mysqli->query("SELECT COUNT(DISTINCT NULLIF(TRIM(num_prestamo_raw), '')) AS c FROM stg_carga_credito WHERE TRIM(num_prestamo_raw) REGEXP '^[0-9]+$'");
if ($res) {
    $row = $res->fetch_assoc();
    $stgCount = (int)$row['c'];
}
$res = $mysqli->query("SELECT COUNT(*) AS c FROM tb_prestamos WHERE idprestamo IN (SELECT DISTINCT CAST(num_prestamo_raw AS UNSIGNED) FROM stg_carga_credito WHERE NULLIF(TRIM(num_prestamo_raw),'') IS NOT NULL AND TRIM(num_prestamo_raw) REGEXP '^[0-9]+$')");
if ($res) {
    $row = $res->fetch_assoc();
    $impCount = (int)$row['c'];
}
header('Content-Type: text/plain; charset=utf-8');
echo "csv={$csv}\nprestamos_csv={$stgCount}\nprestamos_importados={$impCount}\n";
$mysqli->close();
