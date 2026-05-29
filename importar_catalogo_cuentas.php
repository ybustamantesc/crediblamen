<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Configuración de base de datos
$host = 'localhost';
$user = 'root';
$pass = '';
$database = 'u987557742_crediblamensis';
$excelFile = 'C:\Users\yolib\OneDrive - Cloud\CrediBlamen\Proyecto CrediBlamen\Nivel Contable\Para_importar.xlsx';

// Verificar que el archivo existe
if (!file_exists($excelFile)) {
    die("Error: El archivo Excel no existe en: $excelFile\n");
}

// Conectar a la base de datos
$mysqli = new mysqli($host, $user, $pass, $database);
if ($mysqli->connect_error) {
    die('Error de conexión: ' . $mysqli->connect_error);
}

// Leer el archivo Excel
try {
    $spreadsheet = IOFactory::load($excelFile);
    $worksheet = $spreadsheet->getActiveSheet();
    $rows = $worksheet->toArray();
    
    if (empty($rows)) {
        die("El archivo Excel está vacío\n");
    }
    
    // Encontrar índices de columnas
    $headers = $rows[0];
    $codigoIdx = array_search('Código', $headers);
    $denominacionIdx = array_search('Denominación', $headers);
    
    if ($codigoIdx === false || $denominacionIdx === false) {
        die("Error: No se encontraron las columnas 'Código' o 'Denominación' en el Excel\n");
    }
    
    echo "Columnas encontradas: Código (índice $codigoIdx), Denominación (índice $denominacionIdx)\n";
    
    // Procesar cada fila
    $contador = 0;
    $errores = 0;
    
    for ($i = 1; $i < count($rows); $i++) {
        $row = $rows[$i];
        
        // Saltar filas vacías
        if (empty($row[$codigoIdx]) && empty($row[$denominacionIdx])) {
            continue;
        }
        
        $code = trim($row[$codigoIdx]);
        $name = trim($row[$denominacionIdx]);
        
        if (empty($code)) {
            continue;
        }
        
        // Determinar type y naturaleza según el primer dígito
        $firstDigit = substr($code, 0, 1);
        
        switch ($firstDigit) {
            case '1':
                $type = 'Activo';
                $naturaleza = 'deudora';
                break;
            case '2':
                $type = 'Pasivo';
                $naturaleza = 'acreedora';
                break;
            case '3':
                $type = 'Patrimonio';
                $naturaleza = 'acreedora';
                break;
            case '4':
                $type = 'Ingreso';
                $naturaleza = 'acreedora';
                break;
            case '5':
            case '6':
                $type = 'Gasto';
                $naturaleza = 'deudora';
                break;
            case '8':
                $type = 'Orden';
                $naturaleza = 'deudora';
                break;
            default:
                echo "Advertencia: Primer dígito '$firstDigit' no reconocido para código: $code\n";
                continue 2;
        }
        
        // Verificar si el código ya existe
        $checkStmt = $mysqli->prepare('SELECT id FROM tb_account WHERE code = ?');
        $checkStmt->bind_param('s', $code);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        $checkStmt->close();
        
        if ($checkResult->num_rows > 0) {
            // Actualizar si existe
            $updateStmt = $mysqli->prepare('UPDATE tb_account SET name = ?, type = ?, naturaleza = ? WHERE code = ?');
            $updateStmt->bind_param('ssss', $name, $type, $naturaleza, $code);
            
            if (!$updateStmt->execute()) {
                echo "Error al actualizar código $code: " . $updateStmt->error . "\n";
                $errores++;
                $updateStmt->close();
                continue;
            }
            $updateStmt->close();
            $contador++;
        } else {
            // Insertar si no existe
            $insertStmt = $mysqli->prepare('INSERT INTO tb_account (code, name, type, naturaleza) VALUES (?, ?, ?, ?)');
            $insertStmt->bind_param('ssss', $code, $name, $type, $naturaleza);
            
            if (!$insertStmt->execute()) {
                echo "Error al insertar código $code: " . $insertStmt->error . "\n";
                $errores++;
                $insertStmt->close();
                continue;
            }
            $insertStmt->close();
            $contador++;
        }
    }
    
    echo "\n=== Resumen ===\n";
    echo "Registros procesados: $contador\n";
    echo "Errores: $errores\n";
    
} catch (Exception $e) {
    echo 'Error al leer el Excel: ' . $e->getMessage();
}

$mysqli->close();
?>
