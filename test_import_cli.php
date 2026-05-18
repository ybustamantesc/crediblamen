<?php
// Test REAL de importación
$_FILES['balanzaFile'] = [
    'name' => 'test.csv',
    'type' => 'text/csv',
    'tmp_name' => 'C:/xampp/htdocs/Servicredit/temp/ejemplo_balanza_enero_2026.csv',
    'error' => 0,
    'size' => filesize('C:/xampp/htdocs/Servicredit/temp/ejemplo_balanza_enero_2026.csv')
];

$_POST['periodoMes'] = '01';
$_POST['periodoAnio'] = '2026';
$_POST['tipoImportacion'] = 'apertura';

// Cargar CodeIgniter
define('BASEPATH', 'dummy');
require_once 'C:/xampp/htdocs/Servicredit/application/controllers/Contabilidad.php';

// Simular entorno CI
class CI_Controller {}
$GLOBALS['CI'] = new stdClass();

// Ejecutar
echo "Test de importación directa\n";
echo "============================\n\n";

// Ver si el archivo existe
echo "Archivo existe: " . (file_exists($_FILES['balanzaFile']['tmp_name']) ? 'SÍ' : 'NO') . "\n";
echo "Tamaño: " . $_FILES['balanzaFile']['size'] . " bytes\n\n";

// Leer primeras líneas
$content = file_get_contents($_FILES['balanzaFile']['tmp_name']);
$lines = explode("\n", $content);
echo "Primeras 3 líneas:\n";
for ($i = 0; $i < 3 && $i < count($lines); $i++) {
    echo ($i+1) . ": " . $lines[$i] . "\n";
}
