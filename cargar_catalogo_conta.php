<?php
/**
 * Cargar catálogo de cuentas en base de datos CONTA
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Conexión a la base de datos CONTA
    $conn = new mysqli('localhost', 'root', '', 'conta');
    if ($conn->connect_error) {
        throw new Exception('Error de conexión: ' . $conn->connect_error);
    }
    
    $conn->set_charset('utf8mb4');
    
    echo "=================================================\n";
    echo "CARGANDO CATALOGO DE CUENTAS EN BASE CONTA\n";
    echo "=================================================\n\n";
    
    $csvFile = 'c:\xampp\htdocs\Conta\temp\saldos_iniciales_importar.csv';
    $handle = fopen($csvFile, 'r');
    
    if (!$handle) {
        throw new Exception('No se pudo abrir el archivo CSV');
    }
    
    // Leer encabezados
    $headers = fgetcsv($handle);
    
    $insertadas = 0;
    $omitidas = 0;
    
    echo "Cargando cuentas...\n\n";
    
    // Procesar cada línea
    while (($row = fgetcsv($handle)) !== false) {
        if (count($row) < 3) {
            continue;
        }
        $codigo = trim($row[0]);
        $denominacion = trim($row[1]);
        $saldo = trim($row[2]);
        if (empty($codigo)) {
            $omitidas++;
            continue;
        }
        $primerDigito = substr($codigo, 0, 1);
        switch ($primerDigito) {
            case '1':
                $tipo = 'activo';
                $naturaleza = 'deudora';
                $agrupador = 'activo';
                break;
            case '2':
                $tipo = 'pasivo';
                $naturaleza = 'acreedora';
                $agrupador = 'pasivo';
                break;
            case '3':
                $tipo = 'patrimonio';
                $naturaleza = 'acreedora';
                $agrupador = 'patrimonio';
                break;
            case '4':
                $tipo = 'ingreso';
                $naturaleza = 'acreedora';
                $agrupador = 'ingreso';
                break;
            case '5':
            case '6':
            case '7':
                $tipo = 'gasto';
                $naturaleza = 'deudora';
                $agrupador = 'gasto';
                break;
            default:
                $tipo = 'activo';
                $naturaleza = 'deudora';
                $agrupador = 'activo';
        }
        // Intentar insertar, si falla por duplicado, hacer UPDATE
        $stmt = $conn->prepare("INSERT INTO tb_account (code, name, type, naturaleza, agrupador_estado, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param('sssss', $codigo, $denominacion, $tipo, $naturaleza, $agrupador);
        if ($stmt->execute()) {
            echo "  ✓ $codigo - $denominacion ($tipo/$naturaleza/$agrupador)\n";
            $insertadas++;
        } else if ($conn->errno == 1062) { // Duplicate entry
            // Actualizar naturaleza y agrupador_estado
            $stmt2 = $conn->prepare("UPDATE tb_account SET naturaleza = ?, agrupador_estado = ?, type = ? WHERE code = ?");
            $stmt2->bind_param('ssss', $naturaleza, $agrupador, $tipo, $codigo);
            if ($stmt2->execute()) {
                echo "  ~ Actualizada $codigo ($tipo/$naturaleza/$agrupador)\n";
            } else {
                echo "  ✗ Error UPDATE: $codigo - " . $stmt2->error . "\n";
            }
            $omitidas++;
        } else {
            echo "  ✗ Error: $codigo - " . $stmt->error . "\n";
            $omitidas++;
        }
    }
    
    fclose($handle);
    
    echo "\n=================================================\n";
    echo "CARGA COMPLETADA\n";
    echo "=================================================\n";
    echo "  Cuentas insertadas: $insertadas\n";
    echo "  Cuentas omitidas: $omitidas\n\n";
    
    // Verificar resultado
    $result = $conn->query("SELECT COUNT(*) as total FROM tb_account");
    $row = $result->fetch_assoc();
    echo "Total en base de datos CONTA: " . $row['total'] . "\n\n";
    
    // Mostrar resumen por tipo
    echo "Resumen por tipo:\n";
    echo "----------------------------------------\n";
    $result = $conn->query("SELECT type, COUNT(*) as total FROM tb_account GROUP BY type ORDER BY type");
    while ($row = $result->fetch_assoc()) {
        echo "  {$row['type']}: {$row['total']} cuentas\n";
    }
    
    echo "\n";
    echo "Accede a: http://localhost/Conta/contabilidad/catalogo\n";
    
    $conn->close();
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
