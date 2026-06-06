<?php
$mysqli = new mysqli('localhost', 'root', '', 'u987557742_crediblamensis');
if ($mysqli->connect_error) {
    echo 'CONNECT ERROR: ' . $mysqli->connect_error . "\n";
    exit(1);
}
$res = $mysqli->query('SELECT id, idsolicitud, cobertura_deuda FROM tb_analisis_financiero_asalariado ORDER BY id DESC LIMIT 5');
if (!$res) {
    echo 'QUERY ERROR: ' . $mysqli->error . "\n";
    exit(1);
}
while ($row = $res->fetch_assoc()) {
    echo json_encode($row) . "\n";
}
$mysqli->close();
