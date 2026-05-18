<?php
// Simular POST desde CLI
$_FILES['balanzaFile'] = [
    'name' => 'ejemplo_balanza_abril_2025.csv',
    'type' => 'text/csv',
    'tmp_name' => 'C:/xampp/htdocs/Servicredit/temp/ejemplo_balanza_abril_2025.csv',
    'error' => 0,
    'size' => filesize('C:/xampp/htdocs/Servicredit/temp/ejemplo_balanza_abril_2025.csv')
];

$_POST['periodoMes'] = '04';
$_POST['periodoAnio'] = '2025';
$_POST['tipoImportacion'] = 'apertura';

// Incluir el procesador
include 'C:/xampp/htdocs/Servicredit/importar_balanza_directo.php';
