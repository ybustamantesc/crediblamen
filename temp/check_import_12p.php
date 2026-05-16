<?php
$mysqli = new mysqli('localhost', 'root', '', 'crediblamen');
if ($mysqli->connect_error) {
    file_put_contents(__DIR__ . '/import_count_12p.txt', 'ERROR');
    exit;
}
$res = $mysqli->query("SELECT COUNT(DISTINCT NULLIF(TRIM(num_prestamo_raw), '')) AS c FROM stg_carga_credito");
if ($res) {
    $row = $res->fetch_assoc();
    file_put_contents(__DIR__ . '/import_count_12p.txt', $row['c']);
}
$mysqli->close();
