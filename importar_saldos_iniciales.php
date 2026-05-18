<?php
/**
 * Importar Saldos Iniciales
 * Este script permite cargar los saldos iniciales de las cuentas contables
 * desde un archivo CSV y generar un asiento de apertura.
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
while (@ob_get_level()) @ob_end_clean();
header('Content-Type: application/json');

try {
    // Conexión a la base de datos (usar la base de datos del proyecto)
    $conn = new mysqli('localhost', 'root', '', 'minitas');
    if ($conn->connect_error) {
        throw new Exception('Error de conexión a la base de datos: ' . $conn->connect_error);
    }
    
    $conn->set_charset('utf8mb4');
    
    // Validar que se haya subido un archivo
    if (!isset($_FILES['saldosFile']) || $_FILES['saldosFile']['error'] !== 0) {
        throw new Exception('No se recibió el archivo o hubo un error en la carga');
    }
    
    $file = $_FILES['saldosFile'];
    
    // Obtener parámetros del período
    $fecha_apertura = $_POST['fechaApertura'] ?? null;
    $descripcion = $_POST['descripcion'] ?? 'Asiento de Apertura - Saldos Iniciales';
    
    if (!$fecha_apertura) {
        throw new Exception('Debe especificar la fecha de apertura');
    }
    
    // Validar formato de fecha
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_apertura)) {
        throw new Exception('Formato de fecha inválido. Use YYYY-MM-DD');
    }
    
    // Abrir y leer el archivo CSV
    $handle = fopen($file['tmp_name'], 'r');
    if (!$handle) {
        throw new Exception('No se pudo abrir el archivo CSV');
    }
    
    // Leer la primera línea (encabezados)
    $headers = fgetcsv($handle);
    if (!$headers) {
        throw new Exception('El archivo CSV está vacío o mal formateado');
    }
    
    // Detectar las columnas necesarias
    $col_codigo = null;
    $col_nombre = null;
    $col_saldo_anterior = null;
    
    // Limpiar BOM si existe
    if (isset($headers[0])) {
        $headers[0] = str_replace("\xEF\xBB\xBF", '', $headers[0]);
    }
    
    foreach ($headers as $idx => $header) {
        // Limpiar el header de caracteres especiales y espacios
        $h = mb_strtolower(trim($header), 'UTF-8');
        
        // Normalizar caracteres especiales
        $h = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['a', 'e', 'i', 'o', 'u', 'n'], $h);
        
        if ($idx === 0 && (strpos($h, 'codigo') !== false || $h === 'code')) {
            $col_codigo = $idx;
        } elseif ($idx === 1 && (strpos($h, 'denominacion') !== false || strpos($h, 'nombre') !== false || $h === 'name' || strpos($h, 'descripcion') !== false)) {
            $col_nombre = $idx;
        } elseif ($idx === 2 && strpos($h, 'saldo') !== false) {
            $col_saldo_anterior = $idx;
        }
    }
    
    // Si no se encontraron por nombre, asumir por posición si hay 3 columnas
    if ($col_codigo === null && $col_nombre === null && $col_saldo_anterior === null && count($headers) >= 3) {
        $col_codigo = 0;
        $col_nombre = 1;
        $col_saldo_anterior = 2;
    }
    
    if ($col_codigo === null || $col_nombre === null || $col_saldo_anterior === null) {
        $debug_info = "Columnas detectadas: " . implode(', ', $headers);
        throw new Exception('El archivo CSV no tiene las columnas requeridas. ' . $debug_info);
    }
    
    // Arrays para almacenar los datos
    $cuentas = [];
    $cuentas_creadas = 0;
    $cuentas_existentes = 0;
    $errores = [];
    
    // Procesar cada línea del CSV
    $linea = 1;
    while (($row = fgetcsv($handle)) !== false) {
        $linea++;
        
        if (count($row) <= max($col_codigo, $col_nombre, $col_saldo_anterior)) {
            continue; // Línea incompleta
        }
        
        $codigo = trim($row[$col_codigo]);
        $nombre = trim($row[$col_nombre]);
        $saldo_str = trim($row[$col_saldo_anterior]);
        
        // Saltar líneas vacías
        if (empty($codigo)) {
            continue;
        }
        
        // Limpiar el saldo (quitar comas, espacios, comillas)
        $saldo_str = str_replace([',', '"', ' ', '$'], '', $saldo_str);
        
        // Manejar guiones o valores vacíos como 0
        if ($saldo_str === '-' || $saldo_str === '' || $saldo_str === 'nan') {
            $saldo = 0.0;
        } else {
            $saldo = floatval($saldo_str);
        }
        
        // Determinar el tipo de cuenta según el primer dígito
        $primer_digito = substr($codigo, 0, 1);
        switch ($primer_digito) {
            case '1':
                $tipo_cuenta = 'activo';
                break;
            case '2':
                $tipo_cuenta = 'pasivo';
                break;
            case '3':
                $tipo_cuenta = 'patrimonio';
                break;
            case '4':
                $tipo_cuenta = 'ingreso';
                break;
            case '5':
            case '6':
            case '7':
                $tipo_cuenta = 'gasto';
                break;
            default:
                $tipo_cuenta = 'activo';
        }
        
        // Verificar si la cuenta ya existe
        $stmt = $conn->prepare("SELECT id FROM tb_account WHERE code = ?");
        $stmt->bind_param('s', $codigo);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // La cuenta ya existe
            $cuenta = $result->fetch_assoc();
            $cuenta_id = $cuenta['id'];
            $cuentas_existentes++;
        } else {
            // Crear la cuenta
            $stmt2 = $conn->prepare("INSERT INTO tb_account (code, name, type, created_at) VALUES (?, ?, ?, NOW())");
            $stmt2->bind_param('sss', $codigo, $nombre, $tipo_cuenta);
            
            if (!$stmt2->execute()) {
                $errores[] = "Línea $linea: Error al crear cuenta $codigo - " . $stmt2->error;
                continue;
            }
            
            $cuenta_id = $conn->insert_id;
            $cuentas_creadas++;
        }
        
        // Guardar los datos de la cuenta para el asiento
        $cuentas[] = [
            'id' => $cuenta_id,
            'code' => $codigo,
            'name' => $nombre,
            'type' => $tipo_cuenta,
            'saldo' => $saldo
        ];
    }
    
    fclose($handle);
    
    // Ahora vamos a crear el asiento de apertura
    if (empty($cuentas)) {
        throw new Exception('No se encontraron cuentas válidas en el archivo CSV');
    }
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    try {
        // Crear el asiento (journal) y marcarlo automáticamente como mayorizado (posted=1)
        $stmt = $conn->prepare("INSERT INTO tb_journal (date, description, created_at, posted, posted_at) VALUES (?, ?, NOW(), 1, NOW())");
        $stmt->bind_param('ss', $fecha_apertura, $descripcion);
        
        if (!$stmt->execute()) {
            throw new Exception('Error al crear el asiento: ' . $stmt->error);
        }
        
        $journal_id = $conn->insert_id;
        
        // Preparar el statement para las líneas del asiento
        $stmt_entry = $conn->prepare("INSERT INTO tb_journal_entry (journal_id, account_id, debit, credit, description) VALUES (?, ?, ?, ?, ?)");
        
        $total_debe = 0.0;
        $total_haber = 0.0;
        $entries_count = 0;
        
        foreach ($cuentas as $cuenta) {
            $cuenta_id = $cuenta['id'];
            $saldo = $cuenta['saldo'];
            $tipo = $cuenta['type'];
            $desc_linea = "Saldo Inicial - " . $cuenta['name'];
            
            // Para saldos iniciales:
            // - Cuentas de ACTIVO y GASTO: Si el saldo es positivo -> DEBE, si es negativo -> HABER
            // - Cuentas de PASIVO, PATRIMONIO e INGRESO: Si el saldo es positivo -> HABER, si es negativo -> DEBE
            
            $debe = 0.0;
            $haber = 0.0;
            
            if (in_array($tipo, ['activo', 'gasto'])) {
                // Naturaleza deudora
                if ($saldo > 0) {
                    $debe = $saldo;
                } elseif ($saldo < 0) {
                    $haber = abs($saldo);
                }
            } else {
                // Naturaleza acreedora (pasivo, patrimonio, ingreso)
                if ($saldo > 0) {
                    $haber = $saldo;
                } elseif ($saldo < 0) {
                    $debe = abs($saldo);
                }
            }
            
            // Solo insertar si hay movimiento
            if ($debe != 0.0 || $haber != 0.0) {
                $stmt_entry->bind_param('iidds', $journal_id, $cuenta_id, $debe, $haber, $desc_linea);
                
                if (!$stmt_entry->execute()) {
                    throw new Exception('Error al insertar línea del asiento: ' . $stmt_entry->error);
                }
                
                $total_debe += $debe;
                $total_haber += $haber;
                $entries_count++;
            }
        }
        
        // Actualizar los totales del asiento
        $stmt_update = $conn->prepare("UPDATE tb_journal SET total_debit = ?, total_credit = ? WHERE id = ?");
        $stmt_update->bind_param('ddi', $total_debe, $total_haber, $journal_id);
        $stmt_update->execute();
        
        // Verificar que el asiento cuadre
        $diferencia = abs($total_debe - $total_haber);
        
        // Preparar información detallada para debug
        $debug_info = [
            'total_debe' => $total_debe,
            'total_haber' => $total_haber,
            'diferencia' => $diferencia,
            'resumen_por_tipo' => []
        ];
        
        // Calcular resumen por tipo de cuenta
        $resumen = [];
        foreach ($cuentas as $cuenta) {
            $tipo = $cuenta['type'];
            if (!isset($resumen[$tipo])) {
                $resumen[$tipo] = ['count' => 0, 'saldo_total' => 0];
            }
            $resumen[$tipo]['count']++;
            $resumen[$tipo]['saldo_total'] += $cuenta['saldo'];
        }
        $debug_info['resumen_por_tipo'] = $resumen;
        
        // Si no cuadra con una diferencia significativa, crear cuenta de ajuste
        $cuenta_ajuste_creada = false;
        if ($diferencia > 0.01) {
            // Crear/buscar cuenta de ajuste
            $codigo_ajuste = '39010199999';
            $nombre_ajuste = 'Ajuste por Apertura - Diferencia';
            
            $stmt_check = $conn->prepare("SELECT id FROM tb_account WHERE code = ?");
            $stmt_check->bind_param('s', $codigo_ajuste);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();
            
            if ($result_check->num_rows > 0) {
                $ajuste_id = $result_check->fetch_assoc()['id'];
            } else {
                $stmt_create = $conn->prepare("INSERT INTO tb_account (code, name, type, created_at) VALUES (?, ?, 'patrimonio', NOW())");
                $stmt_create->bind_param('ss', $codigo_ajuste, $nombre_ajuste);
                $stmt_create->execute();
                $ajuste_id = $conn->insert_id;
            }
            
            // Determinar si la diferencia va al debe o haber
            $ajuste_debe = 0.0;
            $ajuste_haber = 0.0;
            
            if ($total_debe > $total_haber) {
                $ajuste_haber = $diferencia;
            } else {
                $ajuste_debe = $diferencia;
            }
            
            $desc_ajuste = "Ajuste automático por apertura - Diferencia: " . number_format($diferencia, 2);
            $stmt_entry->bind_param('iidds', $journal_id, $ajuste_id, $ajuste_debe, $ajuste_haber, $desc_ajuste);
            $stmt_entry->execute();
            
            $total_debe += $ajuste_debe;
            $total_haber += $ajuste_haber;
            $entries_count++;
            $cuenta_ajuste_creada = true;
        }
        
        // Confirmar la transacción
        $conn->commit();
        
        // Preparar respuesta exitosa
        $response = [
            'status' => 'success',
            'message' => 'Saldos iniciales importados correctamente',
            'data' => [
                'journal_id' => $journal_id,
                'fecha_apertura' => $fecha_apertura,
                'total_cuentas_procesadas' => count($cuentas),
                'cuentas_creadas' => $cuentas_creadas,
                'cuentas_existentes' => $cuentas_existentes,
                'entries_creadas' => $entries_count,
                'total_debe' => round($total_debe, 2),
                'total_haber' => round($total_haber, 2),
                'cuadra' => $diferencia < 0.01,
                'cuenta_ajuste_creada' => $cuenta_ajuste_creada,
                'errores' => $errores
            ]
        ];
        
        echo json_encode($response, JSON_PRETTY_PRINT);
        
    } catch (Exception $e) {
        // Revertir la transacción en caso de error
        $conn->rollback();
        throw $e;
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}
