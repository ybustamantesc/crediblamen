<?php
$mysqli = new mysqli('localhost', 'root', '', 'crediblamen');
if ($mysqli->connect_error) {
    http_response_code(500);
    echo "ERROR: " . $mysqli->connect_error;
    exit;
}
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    echo "Falta parametro ?id=...";
    exit;
}
$res = $mysqli->query("SELECT COUNT(*) AS c FROM tb_prestamos WHERE idprestamo = {$id}");
$row = $res ? $res->fetch_assoc() : ['c' => 0];
$exists = (int)$row['c'] > 0 ? 'SI' : 'NO';
header('Content-Type: text/plain; charset=utf-8');
echo "idprestamo={$id}\nexiste={$exists}\n";
$mysqli->close();
