<?php
$db = new mysqli('localhost', 'root', '', 'u987557742_crediblamensis');
if ($db->connect_errno) {
    echo 'CONNECT_ERROR: ' . $db->connect_error . "\n";
    exit(1);
}
$query = 'SELECT DISTINCT garantia FROM tb_solicitudes WHERE garantia LIKE "\\%Mobiliaria\\%" OR garantia LIKE "\\%MOBILIARIA\\%" OR garantia LIKE "\\%mobiliaria\\%" LIMIT 100';
$res = $db->query($query);
if (!$res) {
    echo 'QUERY_ERROR: ' . $db->error . "\n";
    exit(1);
}
while ($row = $res->fetch_assoc()) {
    echo '[' . $row['garantia'] . "]\n";
}
echo "---\n";
$query2 = 'SELECT idsolicitud, garantia FROM tb_solicitudes WHERE garantia LIKE "\\%Mobiliaria\\%" OR garantia LIKE "\\%MOBILIARIA\\%" OR garantia LIKE "\\%mobiliaria\\%" LIMIT 20';
$res2 = $db->query($query2);
if ($res2) {
    while ($row = $res2->fetch_assoc()) {
        echo 'ID=' . $row['idsolicitud'] . ' garantia=[' . $row['garantia'] . "]\n";
    }
} else {
    echo 'QUERY2_ERROR: ' . $db->error . "\n";
}
$db->close();
