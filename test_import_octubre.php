<?php
// Script de prueba para importar la balanza de octubre 2025 desde el archivo en temp/
// Ejecutar: php test_import_octubre.php

$csvPath = 'C:/xampp/htdocs/lasminitas/temp/ejemplo_balanza_Octubre_2025.csv';

if (!file_exists($csvPath)) {
    echo "ERROR: Archivo no encontrado: $csvPath\n";
    exit(1);
}

$_FILES['balanzaFile'] = [
    'name' => basename($csvPath),
    'type' => 'text/csv',
    'tmp_name' => $csvPath,
    'error' => 0,
    'size' => filesize($csvPath)
];

// Período: octubre 2025
$_POST['periodoMes'] = '10';
$_POST['periodoAnio'] = '2025';
$_POST['tipoImportacion'] = 'cierre';

// Incluir el procesador completo (crea el asiento y las cuentas si es necesario)
include __DIR__ . '/importar_balanza_completo.php';

?>