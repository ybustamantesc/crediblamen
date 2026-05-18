<?php
$mysqli = new mysqli('localhost', 'root', '', 'crediblamen');
if ($mysqli->connect_error) {
    file_put_contents(__DIR__ . '/import_counts.txt', "ERROR: {$mysqli->connect_error}\n");
    echo "ERROR: {$mysqli->connect_error}\n";
    exit;
}
$queries = [
    "SELECT COUNT(*) AS stg_rows FROM stg_carga_credito",
    "SELECT COUNT(DISTINCT CAST(NULLIF(TRIM(num_prestamo_raw), '') AS UNSIGNED)) AS prestamos_csv FROM stg_carga_credito WHERE NULLIF(TRIM(num_prestamo_raw),'') IS NOT NULL AND TRIM(num_prestamo_raw) REGEXP '^[0-9]+$'",
    "SELECT COUNT(*) AS prestamos_importados FROM tb_prestamos WHERE idprestamo IN (SELECT DISTINCT CAST(NULLIF(TRIM(num_prestamo_raw), '') AS UNSIGNED) FROM stg_carga_credito WHERE NULLIF(TRIM(num_prestamo_raw),'') IS NOT NULL AND TRIM(num_prestamo_raw) REGEXP '^[0-9]+$')"
];
$out = [];
foreach ($queries as $q) {
    $res = $mysqli->query($q);
    if (!$res) {
        $out[] = "ERROR: {$mysqli->error}";
        break;
    }
    $row = $res->fetch_assoc();
    $out[] = implode('=', [key($row), current($row)]);
}
$mysqli->close();
file_put_contents(__DIR__ . '/import_counts.txt', implode("\n", $out) . "\n");
echo implode("\n", $out) . "\n";
