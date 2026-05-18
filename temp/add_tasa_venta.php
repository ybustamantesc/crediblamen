<?php
$mysqli = new mysqli('localhost', 'root', '', 'u987557742_testsystem');
if ($mysqli->connect_error) die('Error de conexión: ' . $mysqli->connect_error);

$sql = "ALTER TABLE tb_tasa_cambio ADD COLUMN tasa_venta DECIMAL(10,4) NULL AFTER tasa_cambio";

if ($mysqli->query($sql)) {
    echo "✓ Columna tasa_venta agregada exitosamente a tb_tasa_cambio\n";
} else {
    echo "Error al agregar columna: " . $mysqli->error . "\n";
}

$mysqli->close();
