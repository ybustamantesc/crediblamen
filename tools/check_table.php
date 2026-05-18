<?php
$mysqli = new mysqli('localhost','root','');
$mysqli->select_db('crediblamen.db');
$res = $mysqli->query("SHOW TABLES LIKE 'tb_garantias'");
if (!$res) { echo "Query failed: " . $mysqli->error . "\n"; exit(1); }
$row = $res->fetch_row();
if ($row) {
    echo "Table exists: " . $row[0] . "\n";
    $rs = $mysqli->query("SHOW COLUMNS FROM tb_garantias");
    while ($c = $rs->fetch_assoc()) {
        echo $c['Field'] . "\t" . $c['Type'] . "\n";
    }
} else {
    echo "Table tb_garantias not found\n";
}
$mysqli->close();
?>