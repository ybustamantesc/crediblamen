<?php
$mysqli = new mysqli('localhost', 'root', '', 'crediblamen');
if ($mysqli->connect_error) {
    file_put_contents(__DIR__ . '/stg_sample_13p1.txt', 'ERROR');
    exit;
}
$sample = [];
$res = $mysqli->query("SELECT num_prestamo_raw FROM stg_carga_credito WHERE NULLIF(TRIM(num_prestamo_raw),'') IS NOT NULL LIMIT 10");
if ($res) {
    while ($row = $res->fetch_assoc()) {
        $sample[] = $row['num_prestamo_raw'];
    }
}
$countNull = 0;
$res = $mysqli->query("SELECT COUNT(*) AS c FROM stg_carga_credito WHERE NULLIF(TRIM(num_prestamo_raw),'') IS NULL");
if ($res) {
    $row = $res->fetch_assoc();
    $countNull = (int)$row['c'];
}
file_put_contents(__DIR__ . '/stg_sample_13p1.txt', 'nulls=' . $countNull . "\n" . implode("\n", $sample));
$mysqli->close();
