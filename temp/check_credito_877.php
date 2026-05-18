<?php
$mysqli = new mysqli('localhost', 'root', '', 'crediblamen');
if ($mysqli->connect_error) {
    file_put_contents(__DIR__ . '/credito_877.txt', 'ERROR');
    exit;
}
$enCsv = 0;
$enBd = 0;
$res = $mysqli->query("SELECT COUNT(*) AS c FROM stg_carga_credito WHERE CAST(NULLIF(TRIM(num_prestamo_raw),'') AS UNSIGNED)=877");
if ($res) {
    $row = $res->fetch_assoc();
    $enCsv = (int)$row['c'];
}
$res = $mysqli->query("SELECT COUNT(*) AS c FROM tb_prestamos WHERE idprestamo=877");
if ($res) {
    $row = $res->fetch_assoc();
    $enBd = (int)$row['c'];
}
file_put_contents(__DIR__ . '/credito_877.txt', $enCsv . "\t" . $enBd);
$mysqli->close();
