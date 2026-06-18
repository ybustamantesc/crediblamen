<?php
/**
 * Script para aplicar las columnas faltantes en teso_pagos
 * Ejecutar desde: http://localhost/Crediblamen/ejecutar_add_columnas_teso_pagos.php
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

echo "<h2>Ejecutando alteraciones en teso_pagos...</h2>";
echo "<pre>";

$sqls = [
    // Agregar columnas para recepción de pagos
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `idprestamo` INT NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `idcuota` INT NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `idcliente` INT NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `beneficiario` VARCHAR(255) NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `concepto` VARCHAR(255) NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `medio_pago` VARCHAR(50) NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `documento_numero` VARCHAR(100) NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `moneda` VARCHAR(10) NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `idusuario` INT NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `idserie` INT NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `serie_codigo` VARCHAR(20) NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `tc_compra` DECIMAL(10,4) NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `tc_venta` DECIMAL(10,4) NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `tc_aplicada` DECIMAL(10,4) NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `monto_usd_aplicado` DECIMAL(18,2) NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `monto_usd` DECIMAL(18,2) NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `monto_nio` DECIMAL(18,2) NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `monto_total_usd` DECIMAL(18,2) NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `dato_adicional` TEXT NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `monto_recibido` DECIMAL(18,2) NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `fecha_recepcion` DATE NULL",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `recibo_revisado` TINYINT(1) NOT NULL DEFAULT 0",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `recepcion_validada` TINYINT(1) NOT NULL DEFAULT 0",
    "ALTER TABLE `teso_pagos` ADD COLUMN IF NOT EXISTS `recepcion_guardada_at` DATETIME NULL",
    
    // Agregar índices para mejor rendimiento
    "ALTER TABLE `teso_pagos` ADD INDEX IF NOT EXISTS `idx_idprestamo` (`idprestamo`)",
    "ALTER TABLE `teso_pagos` ADD INDEX IF NOT EXISTS `idx_idcuota` (`idcuota`)",
    "ALTER TABLE `teso_pagos` ADD INDEX IF NOT EXISTS `idx_idcliente` (`idcliente`)",
    "ALTER TABLE `teso_pagos` ADD INDEX IF NOT EXISTS `idx_concepto` (`concepto`)",
    "ALTER TABLE `teso_pagos` ADD INDEX IF NOT EXISTS `idx_idusuario` (`idusuario`)",
    "ALTER TABLE `teso_pagos` ADD INDEX IF NOT EXISTS `idx_idserie` (`idserie`)",
    "ALTER TABLE `teso_pagos` ADD INDEX IF NOT EXISTS `idx_moneda` (`moneda`)",
    "ALTER TABLE `teso_pagos` ADD INDEX IF NOT EXISTS `idx_fecha_recepcion` (`fecha_recepcion`)"
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
echo "\n\n=== ESTRUCTURA ACTUAL DE teso_pagos ===\n\n";
$result = $mysqli->query("DESCRIBE `teso_pagos`");
echo sprintf("%-30s %-35s %s\n", "Campo", "Tipo", "Nulo");
echo str_repeat("-", 80) . "\n";
while ($row = $result->fetch_assoc()) {
    printf("%-30s %-35s %s\n", $row['Field'], $row['Type'], $row['Null']);
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
    font-size: 12px;
}
body {
    font-family: Arial, sans-serif;
    padding: 20px;
}
</style>
