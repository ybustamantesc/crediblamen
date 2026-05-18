<?php
/**
 * Script de importación automática para Balanza de Septiembre 2025
 */

// Conexión directa a la base de datos
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'u987557742_testsystem';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("ERROR: Conexión fallida: " . $conn->connect_error . "\n");
}

$conn->set_charset('utf8');

echo "=== IMPORTACIÓN BALANZA OCTUBRE 2025 ===\n\n";

// Leer CSV
$archivo = 'temp/ejemplo_balanza_Octubre_2025.csv';
if (!file_exists($archivo)) {
    die("ERROR: No se encuentra el archivo $archivo\n");
}

echo "Leyendo archivo CSV...\n";

function limpiar_numero($str) {
    $str = trim($str);
    $str = str_replace([' ', ',', '"'], '', $str);
    return floatval($str);
}

$cuentas = [];
$handle = fopen($archivo, 'r');

// Saltar encabezados
fgetcsv($handle, 10000, ',');

$linea = 0;
while (($row = fgetcsv($handle, 10000, ',')) !== FALSE) {
    $linea++;
    
    $codigo = trim($row[0]);
    $nombre = trim($row[1]);
    $saldo_anterior = limpiar_numero($row[2]);
    $cargos = limpiar_numero($row[3]);
    $abonos = limpiar_numero($row[4]);
    $saldo_actual = limpiar_numero($row[5]);
    
    if (empty($codigo) || empty($nombre)) {
        continue;
    }
    
    // Clasificar tipo de cuenta según primer dígito
    $primer_digito = substr($codigo, 0, 1);
    $tipo = 'activo';
    
    switch ($primer_digito) {
        case '1': $tipo = 'activo'; break;
        case '2': $tipo = 'pasivo'; break;
        case '3': $tipo = 'patrimonio'; break;
        case '4': $tipo = 'ingreso'; break;
        case '5':
        case '6': $tipo = 'gasto'; break;
    }
    
    $cuentas[] = [
        'codigo' => $codigo,
        'nombre' => $nombre,
        'tipo' => $tipo,
        'saldo_anterior' => $saldo_anterior,
        'cargos' => $cargos,
        'abonos' => $abonos,
        'saldo_actual' => $saldo_actual
    ];
}

fclose($handle);

echo "Cuentas leídas: " . count($cuentas) . "\n\n";

// Iniciar transacción
$conn->begin_transaction();

$creadas = 0;
$actualizadas = 0;
$lineas_asiento = [];

echo "Procesando cuentas...\n";

foreach ($cuentas as $cuenta) {
    // Verificar si existe
    $stmt = $conn->prepare("SELECT id FROM tb_account WHERE code = ?");
    $stmt->bind_param("s", $cuenta['codigo']);
    $stmt->execute();
    $result = $stmt->get_result();
    $existe = $result->fetch_assoc();
    $stmt->close();
    
    if ($existe) {
        // Actualizar
        $stmt = $conn->prepare("UPDATE tb_account SET name = ?, type = ? WHERE id = ?");
        $stmt->bind_param("ssi", $cuenta['nombre'], $cuenta['tipo'], $existe['id']);
        $stmt->execute();
        $stmt->close();
        
        $account_id = $existe['id'];
        $actualizadas++;
        echo "  ✓ Actualizada: {$cuenta['codigo']} - {$cuenta['nombre']}\n";
    } else {
        // Crear
        $stmt = $conn->prepare("INSERT INTO tb_account (code, name, type, parent_id) VALUES (?, ?, ?, NULL)");
        $stmt->bind_param("sss", $cuenta['codigo'], $cuenta['nombre'], $cuenta['tipo']);
        $stmt->execute();
        $account_id = $conn->insert_id;
        $stmt->close();
        
        $creadas++;
        echo "  + Creada: {$cuenta['codigo']} - {$cuenta['nombre']}\n";
    }
    
    // Preparar línea del asiento si tiene saldo
    $saldo = $cuenta['saldo_actual'];
    
    if (abs($saldo) > 0.01) {
        $debe = 0;
        $haber = 0;
        
        // Si el saldo es positivo va al DEBE, si es negativo va al HABER
        // (independientemente del tipo de cuenta, ya viene el saldo neto)
        if ($saldo > 0) {
            $debe = abs($saldo);
        } else {
            $haber = abs($saldo);
        }
        
        $lineas_asiento[] = [
            'account_id' => $account_id,
            'debit' => $debe,
            'credit' => $haber,
            'description' => 'Saldo Octubre 2025 - ' . $cuenta['nombre']
        ];
    }
}

echo "\n";
echo "Cuentas creadas: $creadas\n";
echo "Cuentas actualizadas: $actualizadas\n";
echo "Líneas de asiento: " . count($lineas_asiento) . "\n\n";

// Crear asiento
if (!empty($lineas_asiento)) {
    echo "Creando asiento contable...\n";
    
    // Insertar journal header
    $stmt = $conn->prepare("INSERT INTO tb_journal (date, description, entry_type, created_at) VALUES (?, ?, ?, ?)");
    $fecha = '2025-10-31';
    $descripcion = 'Importación Balanza - Octubre 2025';
    $tipo = 'CD';
    $created = date('Y-m-d H:i:s');
    $stmt->bind_param("ssss", $fecha, $descripcion, $tipo, $created);
    $stmt->execute();
    $journal_id = $conn->insert_id;
    $stmt->close();
    
    if ($journal_id) {
        echo "  Asiento #$journal_id creado\n";
        
        // Insertar líneas
        $stmt = $conn->prepare("INSERT INTO tb_journal_entry (journal_id, account_id, debit, credit, description) VALUES (?, ?, ?, ?, ?)");
        foreach ($lineas_asiento as $linea) {
            $stmt->bind_param("iidds", $journal_id, $linea['account_id'], $linea['debit'], $linea['credit'], $linea['description']);
            $stmt->execute();
        }
        $stmt->close();
        
        // Calcular totales
        $total_debe = array_sum(array_column($lineas_asiento, 'debit'));
        $total_haber = array_sum(array_column($lineas_asiento, 'credit'));
        
        // Actualizar totales en journal
        $stmt = $conn->prepare("UPDATE tb_journal SET total_debit = ?, total_credit = ? WHERE id = ?");
        $stmt->bind_param("ddi", $total_debe, $total_haber, $journal_id);
        $stmt->execute();
        $stmt->close();
        
        echo "  Total Debe: " . number_format($total_debe, 2) . "\n";
        echo "  Total Haber: " . number_format($total_haber, 2) . "\n";
        echo "  Diferencia: " . number_format($total_debe - $total_haber, 2) . "\n";
    }
}

// Completar transacción
$conn->commit();

echo "\n✅ IMPORTACIÓN COMPLETADA EXITOSAMENTE\n";

$conn->close();
