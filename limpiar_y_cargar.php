<?php
/**
 * Script para limpiar y cargar catálogo de cuentas
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Conexión a la base de datos
    $conn = new mysqli('localhost', 'root', '', 'u987557742_testsystem');
    if ($conn->connect_error) {
        throw new Exception('Error de conexión: ' . $conn->connect_error);
    }
    
    $conn->set_charset('utf8mb4');
    
    echo "=================================================\n";
    echo "LIMPIEZA Y CARGA DEL CATALOGO DE CUENTAS\n";
    echo "=================================================\n\n";
    
    // Paso 1: Limpiar todas las tablas
    echo "PASO 1: Limpiando tablas...\n";
    $conn->query("DELETE FROM tb_journal_entry");
    $conn->query("DELETE FROM tb_journal");
    $conn->query("DELETE FROM tb_account");
    $conn->query("ALTER TABLE tb_account AUTO_INCREMENT = 1");
    $conn->query("ALTER TABLE tb_journal AUTO_INCREMENT = 1");
    $conn->query("ALTER TABLE tb_journal_entry AUTO_INCREMENT = 1");
    echo "  ✓ Tablas limpiadas\n\n";
    
    // Paso 2: Leer el CSV y crear las cuentas
    echo "PASO 2: Cargando cuentas desde CSV...\n\n";
    
    $csvFile = 'c:\xampp\htdocs\Conta\temp\saldos_iniciales_balance.csv';
    $handle = fopen($csvFile, 'r');
    
    if (!$handle) {
        throw new Exception('No se pudo abrir el archivo CSV');
    }
    
    // Leer la primera línea (encabezados)
    $headers = fgetcsv($handle);
    
    $insertadas = 0;
    $omitidas = 0;
    
    // Procesar cada línea
    while (($row = fgetcsv($handle)) !== false) {
        if (count($row) < 3) {
            continue;
        }
        
        $codigo = trim($row[0]);
        $denominacion = trim($row[1]);
        $saldo = trim($row[2]);
        
        // Saltar líneas vacías
        if (empty($codigo)) {
            $omitidas++;
            continue;
        }
        
        // Determinar el tipo de cuenta
        $primerDigito = substr($codigo, 0, 1);
        switch ($primerDigito) {
            case '1':
                $tipo = 'activo';
                break;
            case '2':
                $tipo = 'pasivo';
                break;
            case '3':
                $tipo = 'patrimonio';
                break;
            case '4':
                $tipo = 'ingreso';
                break;
            case '5':
            case '6':
            case '7':
                $tipo = 'gasto';
                break;
            default:
                $tipo = 'activo';
        }
        
        // Insertar la cuenta
        $stmt = $conn->prepare("INSERT INTO tb_account (code, name, type, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param('sss', $codigo, $denominacion, $tipo);
        
        if ($stmt->execute()) {
            echo "  ✓ $codigo - $denominacion\n";
            $insertadas++;
        } else {
            echo "  ✗ Error: $codigo - " . $stmt->error . "\n";
            $omitidas++;
        }
    }
    
    fclose($handle);
    
    echo "\n=================================================\n";
    echo "PROCESO COMPLETADO\n";
    echo "=================================================\n";
    echo "  Cuentas insertadas: $insertadas\n";
    echo "  Cuentas omitidas: $omitidas\n\n";
    
    // Verificar resultado
    $result = $conn->query("SELECT COUNT(*) as total FROM tb_account");
    $row = $result->fetch_assoc();
    echo "Total de cuentas en la base de datos: " . $row['total'] . "\n\n";
    
    // Mostrar algunas cuentas
    echo "Primeras 10 cuentas:\n";
    echo "----------------------------------------\n";
    $result = $conn->query("SELECT id, code, name, type FROM tb_account ORDER BY code LIMIT 10");
    while ($row = $result->fetch_assoc()) {
        echo "  {$row['code']} - {$row['name']} ({$row['type']})\n";
    }
    
    echo "\n";
    echo "Últimas 10 cuentas:\n";
    echo "----------------------------------------\n";
    $result = $conn->query("SELECT id, code, name, type FROM tb_account ORDER BY code DESC LIMIT 10");
    while ($row = $result->fetch_assoc()) {
        echo "  {$row['code']} - {$row['name']} ({$row['type']})\n";
    }
    
    echo "\n";
    echo "Accede a: http://localhost/Conta/contabilidad/catalogo\n";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
