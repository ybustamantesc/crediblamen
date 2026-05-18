<?php
$mysqli = new mysqli('localhost','root','');
$mysqli->select_db('crediblamen.db');
$res = $mysqli->query("SELECT COUNT(*) AS c FROM tb_garantias WHERE id = 0");
$r = $res->fetch_assoc();
echo "Rows with id=0: " . $r['c'] . "\n";
$res2 = $mysqli->query("SELECT MAX(id) AS maxid FROM tb_garantias");
$r2 = $res2->fetch_assoc();
echo "Max id: " . $r2['maxid'] . "\n";
$mysqli->close();
?>