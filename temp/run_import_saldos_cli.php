<?php
// Wrapper CLI para simular subida de archivo a importar_saldos_iniciales.php
// Ajusta la ruta del CSV y la fecha según necesites.

$csv = __DIR__ . DIRECTORY_SEPARATOR . 'saldos_iniciales_importar.csv';
if (!file_exists($csv)) {
    echo json_encode(['status' => 'error', 'message' => "CSV not found: $csv"]);
    exit(1);
}

// Simular estructura de $_FILES
$_FILES = [
    'saldosFile' => [
        'name' => basename($csv),
        'type' => 'text/csv',
        'tmp_name' => $csv,
        'error' => 0,
        'size' => filesize($csv),
    ]
];

// Parámetros POST requeridos por el script
$_POST['fechaApertura'] = '2025-04-01';
$_POST['descripcion'] = 'Import CLI - Saldos Iniciales';

// Ejecutar el script de importación
chdir(dirname(__DIR__)); // mover al directorio principal del proyecto
ob_start();
include 'importar_saldos_iniciales.php';
$output = ob_get_clean();

// Mostrar salida
echo $output . PHP_EOL;

?>
