<?php
/**
 * Script para aplicar las columnas faltantes en teso_movimientos
 * Ejecutar desde: http://localhost/Crediblamen/ejecutar_add_columnas_teso.php
 */

// Cargar configuración de CodeIgniter
define('BASEPATH', dirname(__FILE__) . '/system/');
define('APPPATH', dirname(__FILE__) . '/application/');
define('FCPATH', dirname(__FILE__) . '/');

// Incluir configuración de base de datos
require_once(APPPATH . 'config/database.php');

// Conectar a la base de datos
$mysqli = new mysqli(
    $db['default']['hostname'],
    $db['default']['username'],
    $db['default']['password'],
    $db['default']['database']
);

if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

$mysqli->set_charset("utf8mb4");

echo "<h2>Ejecutando alteraciones en teso_movimientos...</h2>";
echo "<pre>";

$sqls = [
    // Agregar columnas faltantes
    "ALTER TABLE `teso_movimientos` ADD COLUMN IF NOT EXISTS `usuario_id` INT NULL AFTER `creado_por`",
    "ALTER TABLE `teso_movimientos` ADD COLUMN IF NOT EXISTS `conciliado` TINYINT(1) NOT NULL DEFAULT 0 AFTER `contabilizado`",
    "ALTER TABLE `teso_movimientos` ADD COLUMN IF NOT EXISTS `moneda` VARCHAR(10) DEFAULT 'NIO' AFTER `tipo`",
    "ALTER TABLE `teso_movimientos` ADD COLUMN IF NOT EXISTS `tc_aplicada` DECIMAL(10,4) NULL AFTER `moneda`",
    "ALTER TABLE `teso_movimientos` ADD COLUMN IF NOT EXISTS `monto_nio` DECIMAL(18,2) NULL AFTER `tc_aplicada`",
    "ALTER TABLE `teso_movimientos` ADD COLUMN IF NOT EXISTS `monto_usd` DECIMAL(18,2) NULL AFTER `monto_nio`",
    "ALTER TABLE `teso_movimientos` ADD COLUMN IF NOT EXISTS `observaciones` TEXT NULL AFTER `monto_usd`",
    "ALTER TABLE `teso_movimientos` ADD COLUMN IF NOT EXISTS `idserie` INT NULL AFTER `observaciones`",
    
    // Agregar índices
    "ALTER TABLE `teso_movimientos` ADD INDEX IF NOT EXISTS `idx_usuario_id` (`usuario_id`)",
    "ALTER TABLE `teso_movimientos` ADD INDEX IF NOT EXISTS `idx_concepto` (`concepto`)",
    "ALTER TABLE `teso_movimientos` ADD INDEX IF NOT EXISTS `idx_cuenta_id` (`cuenta_id`)",
    "ALTER TABLE `teso_movimientos` ADD INDEX IF NOT EXISTS `idx_idserie` (`idserie`)",
    "ALTER TABLE `teso_movimientos` ADD INDEX IF NOT EXISTS `idx_moneda` (`moneda`)"
];

$success_count = 0;
$error_count = 0;

foreach ($sqls as $sql) {
    echo "\nEjecutando: " . htmlspecialchars($sql) . "\n";
    
    if ($mysqli->query($sql)) {
        echo "✓ OK\n";
        $success_count++;
    } else {
        echo "✗ ERROR: " . $mysqli->error . "\n";
        $error_count++;
    }
}

echo "\n\n=== RESUMEN ===\n";
echo "Exitosos: " . $success_count . "\n";
echo "Errores: " . $error_count . "\n";

// Verificar estructura de la tabla
echo "\n\n=== ESTRUCTURA ACTUAL DE teso_movimientos ===\n\n";
$result = $mysqli->query("DESCRIBE `teso_movimientos`");
while ($row = $result->fetch_assoc()) {
    printf("%-20s %-30s %s\n", $row['Field'], $row['Type'], $row['Null']);
}

$mysqli->close();
?>
<style>
pre {
    background: #f4f4f4;
    padding: 15px;
    border-radius: 5px;
    font-family: monospace;
    overflow-x: auto;
}
</style>
