<?php
$mysqli = new mysqli('localhost', 'root', '', 'u987557742_testsystem');
$mysqli->query("UPDATE tb_tasa_cambio SET tasa_venta = 37.0000 WHERE tasa_venta IS NULL OR tasa_venta = 0");
echo "✓ Registros actualizados: " . $mysqli->affected_rows . "\n";
$mysqli->close();
