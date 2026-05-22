<?php
$db = new mysqli('localhost', 'root', '', 'u987557742_crediblamensis');
if ($db->connect_errno) {
    echo 'CONNECT_ERROR: ' . $db->connect_error . "\n";
    exit(1);
}
$res = $db->query('SELECT DISTINCT garantia FROM tb_solicitudes WHERE garantia IS NOT NULL AND garantia <> "" LIMIT 100');
if (!$res) {
    echo 'QUERY_ERROR: ' . $db->error . "\n";
    exit(1);
}
while ($row = $res->fetch_assoc()) {
    echo '[' . $row['garantia'] . "]\n";
}
$db->close();
