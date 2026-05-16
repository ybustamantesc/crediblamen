<?php
/**
 * Cargar saldos iniciales y crear asiento de apertura en base CONTA
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
    echo "CARGANDO SALDOS INICIALES EN BASE CONTA\n";
    echo "=================================================\n\n";
    
    $csvFile = 'c:\xampp\htdocs\Conta\temp\saldos_iniciales_importar.csv';
    $handle = fopen($csvFile, 'r');
    
    if (!$handle) {
        throw new Exception('No se pudo abrir el archivo CSV');
    }
    
    // Leer encabezados
    $headers = fgetcsv($handle);
    
    // Array para almacenar las cuentas con saldo
    $cuentas_con_saldo = [];
    
    // Procesar cada línea y obtener los saldos
    while (($row = fgetcsv($handle)) !== false) {
        if (count($row) < 3) {
            continue;
        }
        $codigo = trim($row[0]);
        $denominacion = trim($row[1]);
        $saldo_str = trim($row[2]);
        if (empty($codigo)) {
            continue;
        }
        $saldo_str = str_replace([',', ' ', '$'], '', $saldo_str);
        if ($saldo_str === '-' || $saldo_str === '' || strtolower($saldo_str) === 'nan') {
            $saldo = 0.0;
        } else {
            $saldo = floatval(str_replace('"', '', $saldo_str));
        }
        if ($saldo != 0) {
            // Buscar el ID, tipo y naturaleza de la cuenta en la base de datos
            $stmt = $conn->prepare("SELECT id, type, naturaleza FROM tb_account WHERE code = ?");
            $stmt->bind_param('s', $codigo);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $cuenta = $result->fetch_assoc();
                $cuentas_con_saldo[] = [
                    'id' => $cuenta['id'],
                    'code' => $codigo,
                    'name' => $denominacion,
                    'type' => $cuenta['type'],
                    'naturaleza' => isset($cuenta['naturaleza']) ? $cuenta['naturaleza'] : null,
                    'saldo' => $saldo
                ];
            }
        }
    }
    
    fclose($handle);
    
    echo "Cuentas con saldo encontradas: " . count($cuentas_con_saldo) . "\n\n";
    
    if (empty($cuentas_con_saldo)) {
        throw new Exception('No se encontraron cuentas con saldo');
    }
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    try {
        // Crear el asiento de apertura
        $fecha_apertura = '2025-11-30';
        $descripcion = 'Asiento de Apertura - Saldos Iniciales';
        $centro_costo_id = '001';

        $stmt = $conn->prepare("INSERT INTO tb_journal (date, description, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param('ss', $fecha_apertura, $descripcion);
        if (!$stmt->execute()) {
            throw new Exception('Error al crear el asiento: ' . $stmt->error);
        }
        $journal_id = $conn->insert_id;
        echo "Asiento creado con ID: $journal_id\n";
        echo "Fecha: $fecha_apertura\n\n";
        // Preparar statement para las líneas del asiento (con centro_costo_id)
        $stmt_entry = $conn->prepare("INSERT INTO tb_journal_entry (journal_id, account_id, debit, credit, description, centro_costo_id) VALUES (?, ?, ?, ?, ?, ?)");
        
        $total_debe = 0.0;
        $total_haber = 0.0;
        $entries_count = 0;
        
        echo "Creando movimientos contables...\n\n";
        
        foreach ($cuentas_con_saldo as $cuenta) {
            $cuenta_id = $cuenta['id'];
            $saldo = $cuenta['saldo'];
            $tipo = $cuenta['type'];
            $naturaleza = isset($cuenta['naturaleza']) ? strtolower($cuenta['naturaleza']) : null;
            $desc_linea = "Saldo Inicial - " . $cuenta['name'];
            $debe = 0.0;
            $haber = 0.0;
            $saldo_abs = abs($saldo);
            // Asignar según naturaleza
            if ($naturaleza === 'deudora') {
                $debe = $saldo_abs;
            } elseif ($naturaleza === 'acreedora') {
                $haber = $saldo_abs;
            } else {
                // Fallback: si no hay naturaleza, usar el signo
                if ($saldo > 0) {
                    $debe = $saldo_abs;
                } elseif ($saldo < 0) {
                    $haber = $saldo_abs;
                }
            }
            if ($debe != 0.0 || $haber != 0.0) {
                $stmt_entry->bind_param('iiddss', $journal_id, $cuenta_id, $debe, $haber, $desc_linea, $centro_costo_id);
                if (!$stmt_entry->execute()) {
                    throw new Exception('Error al insertar línea: ' . $stmt_entry->error);
                }
                $total_debe += $debe;
                $total_haber += $haber;
                $entries_count++;
                echo "  ✓ {$cuenta['code']} - Debe: " . number_format($debe, 2) . " | Haber: " . number_format($haber, 2) . "\n";
            }
        }
        
        // Actualizar totales del asiento
        $stmt_update = $conn->prepare("UPDATE tb_journal SET total_debit = ?, total_credit = ? WHERE id = ?");
        $stmt_update->bind_param('ddi', $total_debe, $total_haber, $journal_id);
        $stmt_update->execute();
        
        // Verificar que cuadre
        $diferencia = abs($total_debe - $total_haber);
        
        echo "\n=================================================\n";
        echo "RESUMEN DEL ASIENTO DE APERTURA\n";
        echo "=================================================\n";
        echo "Total Debe:   " . number_format($total_debe, 2) . "\n";
        echo "Total Haber:  " . number_format($total_haber, 2) . "\n";
        echo "Diferencia:   " . number_format($diferencia, 2) . "\n";
        echo "Movimientos:  $entries_count\n";
        
        if ($diferencia > 0.01) {
            echo "\n⚠ ADVERTENCIA: El asiento no cuadra perfectamente\n";
            echo "Se necesita ajuste de: " . number_format($diferencia, 2) . "\n";
        } else {
            echo "\n✓ El asiento está cuadrado\n";
        }
        
        // Confirmar transacción
        $conn->commit();
        
        echo "\n=================================================\n";
        echo "SALDOS INICIALES CARGADOS EXITOSAMENTE\n";
        echo "=================================================\n";
        
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "\nERROR: " . $e->getMessage() . "\n";
}
?>
