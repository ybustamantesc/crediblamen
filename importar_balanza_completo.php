<?php
/**
 * Importar Balanza de Comprobación - Proceso Completo
 * Lee el CSV y crea el asiento contable en una sola operación
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);
while (@ob_get_level()) @ob_end_clean();
header('Content-Type: application/json');

try {
    // Conexión a la base de datos: usar configuración de CodeIgniter si existe
    $dbConfigPath = __DIR__ . '/application/config/database.php';
    if (file_exists($dbConfigPath)) {
        if (!defined('BASEPATH')) define('BASEPATH', true);
        @include $dbConfigPath; // define $db
        $conf = $db['default'] ?? [];
        $dbHost = $conf['hostname'] ?? 'localhost';
        $dbUser = $conf['username'] ?? 'root';
        $dbPass = $conf['password'] ?? '';
        $dbName = $conf['database'] ?? 'minitas';
    } else {
        $dbHost = 'localhost';
        $dbUser = 'root';
        $dbPass = '';
        $dbName = 'u987557742_testsystem';
    }

    $conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
    if ($conn->connect_error) {
        throw new Exception('Error de conexión a la base de datos: ' . $conn->connect_error);
    }

    $conn->set_charset('utf8mb4');
    
    // Validar que se haya subido un archivo
    if (!isset($_FILES['balanzaFile']) || $_FILES['balanzaFile']['error'] !== 0) {
        throw new Exception('No se recibió el archivo o hubo un error en la carga');
    }
    
    $file = $_FILES['balanzaFile'];
    
    // Obtener parámetros del período
    $mes = $_POST['periodoMes'] ?? null;
    $anio = $_POST['periodoAnio'] ?? null;
    $tipo = $_POST['tipoImportacion'] ?? 'mensual';
    $descripcion = $_POST['descripcion'] ?? "Balanza de Comprobación - $mes/$anio";
    
    if (!$mes || !$anio) {
        throw new Exception('Debe especificar el mes y año del período');
    }
    
    // Validar mes y año
    if (!is_numeric($mes) || $mes < 1 || $mes > 12) {
        throw new Exception('Mes inválido');
    }
    if (!is_numeric($anio) || $anio < 2000 || $anio > 2100) {
        throw new Exception('Año inválido');
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
    
    // Arrays para almacenar los datos
    $cuentas = [];
    $total_debe = 0;
    $total_haber = 0;
    
    // Procesar cada línea del CSV
    $linea = 1;
    while (($row = fgetcsv($handle)) !== false) {
        $linea++;
        
        if (count($row) < 5) {
            continue; // Línea incompleta
        }
        
        $codigo = trim($row[0]);
        $nombre = trim($row[1]);
        if (count($row) >= 6) {
            $saldo_anterior_str = trim($row[2]);
            $cargos_str = trim($row[3]);
            $abonos_str = trim($row[4]);
            $saldo_actual_str = trim($row[5]);
        } else {
            // Formato con 5 columnas: Codigo,Denominacion,Cargos,Abonos,Saldo Actual
            $saldo_anterior_str = '0';
            $cargos_str = trim($row[2]);
            $abonos_str = trim($row[3]);
            $saldo_actual_str = trim($row[4]);
        }
        
        // Saltar líneas vacías
        if (empty($codigo)) {
            continue;
        }
        
        // Limpiar los valores numéricos
        $limpiar = function($str) {
            $str = str_replace([",", "'", '"', ' ', '$'], '', $str);
            if ($str === '-' || $str === '' || $str === 'nan') {
                return 0.0;
            }
            return floatval($str);
        };
        
        $debe = $limpiar($cargos_str);
        $haber = $limpiar($abonos_str);
        $saldo = $limpiar($saldo_actual_str);
        
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
        
        // Guardar los datos de la cuenta
        $cuentas[] = [
            'code' => $codigo,
            'name' => $nombre,
            'type' => $tipo_cuenta,
            'debe' => $debe,
            'haber' => $haber,
            'saldo' => $saldo
        ];
        
        $total_debe += $debe;
        $total_haber += $haber;
    }
    
    fclose($handle);
    
    // Validar que haya cuentas
    if (empty($cuentas)) {
        throw new Exception('No se encontraron cuentas válidas en el archivo CSV');
    }
    
    // Iniciar transacción
    $conn->begin_transaction();
    
    try {
        // Crear/actualizar cuentas y obtener sus IDs
        $cuenta_ids = [];
        $cuentas_creadas = 0;
        $cuentas_actualizadas = 0;
        
        foreach ($cuentas as $cuenta) {
            // Verificar si la cuenta existe
            $stmt = $conn->prepare("SELECT id FROM tb_account WHERE code = ?");
            $stmt->bind_param('s', $cuenta['code']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                // La cuenta existe
                $row = $result->fetch_assoc();
                $cuenta_ids[$cuenta['code']] = $row['id'];
                $cuentas_actualizadas++;
            } else {
                // Crear la cuenta
                $stmt2 = $conn->prepare("INSERT INTO tb_account (code, name, type, created_at) VALUES (?, ?, ?, NOW())");
                $stmt2->bind_param('sss', $cuenta['code'], $cuenta['name'], $cuenta['type']);
                
                if (!$stmt2->execute()) {
                    throw new Exception('Error al crear cuenta ' . $cuenta['code'] . ': ' . $stmt2->error);
                }
                
                $cuenta_ids[$cuenta['code']] = $conn->insert_id;
                $cuentas_creadas++;
            }
        }
        
        // Calcular la fecha del asiento (último día del mes)
        $fecha = date('Y-m-t', strtotime("$anio-$mes-01"));
        
        // Crear el asiento (journal)
        $stmt = $conn->prepare("INSERT INTO tb_journal (date, description, period_month, period_year, entry_type, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $periodo_mes = str_pad($mes, 2, '0', STR_PAD_LEFT);
        $stmt->bind_param('ssiss', $fecha, $descripcion, $periodo_mes, $anio, $tipo);
        
        if (!$stmt->execute()) {
            throw new Exception('Error al crear el asiento: ' . $stmt->error);
        }
        
        $journal_id = $conn->insert_id;
        
        // Preparar el statement para las líneas del asiento
        $stmt_entry = $conn->prepare("INSERT INTO tb_journal_entry (journal_id, account_id, debit, credit, description) VALUES (?, ?, ?, ?, ?)");
        
        $entries_count = 0;
        
        foreach ($cuentas as $cuenta) {
            // Solo insertar si hay movimiento
            if ($cuenta['debe'] != 0.0 || $cuenta['haber'] != 0.0) {
                $cuenta_id = $cuenta_ids[$cuenta['code']];
                $desc_linea = $cuenta['name'];
                
                $stmt_entry->bind_param('iidds', $journal_id, $cuenta_id, $cuenta['debe'], $cuenta['haber'], $desc_linea);
                
                if (!$stmt_entry->execute()) {
                    throw new Exception('Error al insertar línea del asiento: ' . $stmt_entry->error);
                }
                
                $entries_count++;
            }
        }
        
        // Actualizar los totales del asiento
        $stmt_update = $conn->prepare("UPDATE tb_journal SET total_debit = ?, total_credit = ? WHERE id = ?");
        $stmt_update->bind_param('ddi', $total_debe, $total_haber, $journal_id);
        $stmt_update->execute();
        
        // Verificar que el asiento cuadre
        $diferencia = abs($total_debe - $total_haber);
        $cuadra = $diferencia < 0.01;
        
        // Confirmar la transacción
        $conn->commit();
        
        // Preparar respuesta exitosa
        $meses = [
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        ];
        
        $response = [
            'status' => 'success',
            'message' => 'Balanza importada correctamente',
            'data' => [
                'journal_id' => $journal_id,
                'fecha_asiento' => $fecha,
                'periodo' => [
                    'mes' => $mes,
                    'anio' => $anio,
                    'mes_nombre' => $meses[$periodo_mes],
                    'tipo' => $tipo
                ],
                'total_cuentas_procesadas' => count($cuentas),
                'cuentas_creadas' => $cuentas_creadas,
                'cuentas_existentes' => $cuentas_actualizadas,
                'entries_creadas' => $entries_count,
                'total_debe' => round($total_debe, 2),
                'total_haber' => round($total_haber, 2),
                'diferencia' => round($diferencia, 2),
                'cuadra' => $cuadra
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
