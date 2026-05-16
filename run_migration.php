<?php
// Configuración básica
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'crediblamen';

// Conexión a MySQL
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

// SQL a ejecutar
$sql = "ALTER TABLE `tb_prestamo_cuotas`
  ADD COLUMN `dias_mora_manual` INT NULL DEFAULT NULL AFTER `dias_mora_raw`,
  ADD COLUMN `monto_mora` DECIMAL(14,2) NULL DEFAULT NULL AFTER `dias_mora_manual`";

if ($conn->query($sql) === TRUE) {
    echo "✓ Columnas agregadas exitosamente a tb_prestamo_cuotas\n";
} else {
    // Verificar si ya existen (no es error fatal)
    if (strpos($conn->error, 'Duplicate column name') !== false) {
        echo "✓ Las columnas ya existen en la tabla\n";
    } else {
        echo "Error: " . $conn->error . "\n";
    }
}

$conn->close();
?>
