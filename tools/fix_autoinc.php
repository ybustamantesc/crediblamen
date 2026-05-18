<?php
$mysqli = new mysqli('localhost','root','');
$mysqli->select_db('crediblamen.db');
$mysqli->autocommit(false);
try {
    // set id of rows with id=0 to a new unique id
    $res = $mysqli->query("SELECT MAX(id) AS maxid FROM tb_garantias");
    $r = $res->fetch_assoc();
    $maxid = intval($r['maxid']);
    $newid = $maxid + 1;
    echo "Max id before: $maxid\n";
    $mysqli->query("UPDATE tb_garantias SET id = $newid WHERE id = 0");
    echo "Updated rows with id=0 to id=$newid\n";
    // alter column to auto_increment
    $mysqli->query("ALTER TABLE tb_garantias MODIFY id INT(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=".($newid+1));
    echo "Altered table to set AUTO_INCREMENT start to ".($newid+1)."\n";
    $mysqli->commit();
    echo "Done.\n";
} catch (Exception $e) {
    $mysqli->rollback();
    echo "Error: " . $e->getMessage() . "\n";
}
$mysqli->close();
?>