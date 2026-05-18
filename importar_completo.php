<?php
echo "=== IMPORTACIÓN COMPLETA DE BALANZA - MARZO 2025 ===\n\n";

$conn = new mysqli('localhost', 'root', '', 'u987557742_testsystem');
if ($conn->connect_error) die("Error de conexión\n");

$archivo = 'C:/xampp/htdocs/Servicredit/temp/ejemplo_balanza_marzo_2025.csv';
$mes = '03';
$anio = '2025';
$fecha = '2025-03-31';

// 1. CREAR CUENTAS
echo "PASO 1: Creando cuentas...\n";
$h = fopen($archivo, 'r');
fgetcsv($h); // Saltar encabezado

$cuentas_creadas = 0;
$stmt_check = $conn->prepare("SELECT id FROM tb_account WHERE code = ?");
$stmt_insert = $conn->prepare("INSERT INTO tb_account (code, name, type, created_at) VALUES (?, ?, ?, NOW())");

while (($r = fgetcsv($h)) !== false) {
    if (count($r) < 6) continue;
    $codigo = trim($r[0]);
    if (empty($codigo)) continue;
    
    $nombre = trim($r[1]);
    $d1 = substr($codigo, 0, 1);
    if ($d1 === '1') $tipo = 'activo';
    elseif ($d1 === '2') $tipo = 'pasivo';
    elseif ($d1 === '3') $tipo = 'patrimonio';
    elseif ($d1 === '4') $tipo = 'ingreso';
    elseif ($d1 === '5') $tipo = 'gasto';
    else $tipo = 'activo';
    
    $stmt_check->bind_param('s', $codigo);
    $stmt_check->execute();
    $result = $stmt_check->get_result();
    
    if (!$result->fetch_assoc()) {
        $stmt_insert->bind_param('sss', $codigo, $nombre, $tipo);
        if ($stmt_insert->execute()) {
            $cuentas_creadas++;
            echo "  [$codigo] $nombre ($tipo)\n";
        }
    }
}
fclose($h);
$stmt_check->close();
$stmt_insert->close();

echo "\n✓ Cuentas creadas: $cuentas_creadas\n\n";

// 2. CREAR ASIENTO
echo "PASO 2: Creando asiento contable...\n";
$descripcion = 'Asiento de Apertura - Marzo 2025';
$tipo = 'apertura';

$stmt = $conn->prepare("INSERT INTO tb_journal (date, description, period_month, period_year, entry_type, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param('ssiss', $fecha, $descripcion, $mes, $anio, $tipo);
$stmt->execute();
$journal_id = $conn->insert_id;
$stmt->close();

echo "✓ Asiento creado: ID $journal_id\n";
echo "  Fecha: $fecha\n";
echo "  Descripción: $descripcion\n\n";

// 3. CREAR MOVIMIENTOS
echo "PASO 3: Creando movimientos contables...\n";

$h = fopen($archivo, 'r');
fgetcsv($h);

$stmt_id = $conn->prepare("SELECT id FROM tb_account WHERE code = ?");
$stmt_entry = $conn->prepare("INSERT INTO tb_journal_entry (journal_id, account_id, debit, credit, description) VALUES (?, ?, ?, ?, ?)");

$lineas = 0;
$total_debe = 0;
$total_haber = 0;

while (($r = fgetcsv($h)) !== false) {
    if (count($r) < 6) continue;
    $codigo = trim($r[0]);
    if (empty($codigo)) continue;
    
    $nombre = trim($r[1]);
    $debe = floatval(str_replace([',', '"', ' '], '', $r[3]));
    $haber = floatval(str_replace([',', '"', ' '], '', $r[4]));
    
    if ($debe <= 0 && $haber <= 0) continue;
    
    $stmt_id->bind_param('s', $codigo);
    $stmt_id->execute();
    $result = $stmt_id->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $account_id = $row['id'];
        $stmt_entry->bind_param('iidds', $journal_id, $account_id, $debe, $haber, $nombre);
        $stmt_entry->execute();
        
        echo "  [$codigo] D:" . number_format($debe, 2) . " H:" . number_format($haber, 2) . "\n";
        
        $total_debe += $debe;
        $total_haber += $haber;
        $lineas++;
    }
}

fclose($h);
$stmt_id->close();
$stmt_entry->close();

// Actualizar totales
$stmt_update = $conn->prepare("UPDATE tb_journal SET total_debit = ?, total_credit = ? WHERE id = ?");
$stmt_update->bind_param('ddi', $total_debe, $total_haber, $journal_id);
$stmt_update->execute();
$stmt_update->close();

$conn->close();

echo "\n=== IMPORTACIÓN COMPLETADA ===\n";
echo "✓ Journal ID: $journal_id\n";
echo "✓ Cuentas: $cuentas_creadas\n";
echo "✓ Movimientos: $lineas\n";
echo "✓ Total Debe: " . number_format($total_debe, 2) . "\n";
echo "✓ Total Haber: " . number_format($total_haber, 2) . "\n";
echo "✓ Diferencia: " . number_format(abs($total_debe - $total_haber), 2) . "\n";
echo "✓ Cuadra: " . (abs($total_debe - $total_haber) < 0.01 ? "SÍ ✓" : "NO ✗") . "\n";
