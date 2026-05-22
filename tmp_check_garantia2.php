<?php
$db = new mysqli('localhost', 'root', '', 'u987557742_crediblamensis');
if ($db->connect_errno) {
    echo 'CONNECT_ERROR: ' . $db->connect_error . "\n";
    exit(1);
}
$res = $db->query('SHOW COLUMNS FROM tb_solicitudes');
if (!$res) {
    echo 'SHOW_COLUMNS_ERROR: ' . $db->error . "\n";
    exit(1);
}
while ($row = $res->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
echo "---\n";
$res2 = $db->query('SELECT idsolicitud, garantia, garantia_hipotecaria, garantia_mobiliaria, garantia_sin, garantia_prendaria, garantia_fiador, garantia_otra FROM tb_solicitudes LIMIT 20');
if (!$res2) {
    echo 'SELECT_ERROR: ' . $db->error . "\n";
    exit(1);
}
while ($row2 = $res2->fetch_assoc()) {
    print_r($row2);
    echo "-----\n";
}
$db->close();
