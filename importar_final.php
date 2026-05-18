<?php
echo "=== IMPORTACIÓN BALANZA ABRIL 2025 - CON SALDOS REALES ===\n\n";

$conn = new mysqli('localhost', 'root', '', 'u987557742_testsystem');
if ($conn->connect_error) die("Error de conexión\n");

$archivo = 'C:/xampp/htdocs/Servicredit/temp/ejemplo_balanza_abril_2025.csv';
$mes = '04';
$anio = '2025';
$fecha = '2025-04-30';

// PASO 1: CREAR CUENTAS
echo "PASO 1: Creando cuentas...\n";
$h = fopen($archivo, 'r');
fgetcsv($h);

$cuentas_creadas = 0;
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
    
    $stmt_insert->bind_param('sss', $codigo, $nombre, $tipo);
    if ($stmt_insert->execute()) {
        $cuentas_creadas++;
    }
}
fclose($h);
$stmt_insert->close();

echo "✓ Cuentas creadas: $cuentas_creadas\n\n";

// PASO 2: CREAR ASIENTO
echo "PASO 2: Creando asiento contable...\n";
$descripcion = 'Cierre Mensual - Abril 2025';
$tipo = 'cierre_mensual';

$stmt = $conn->prepare("INSERT INTO tb_journal (date, description, period_month, period_year, entry_type, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param('ssiss', $fecha, $descripcion, $mes, $anio, $tipo);
$stmt->execute();
$journal_id = $conn->insert_id;
$stmt->close();

echo "✓ Asiento creado: ID $journal_id\n\n";

// PASO 3: CREAR MOVIMIENTOS CON SALDO ACTUAL
echo "PASO 3: Creando movimientos (Debe/Haber según saldo)...\n";

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
    $saldo = floatval(str_replace([',', '"', ' '], '', $r[5])); // Saldo Actual
    
    if ($saldo == 0) continue;
    
    $stmt_id->bind_param('s', $codigo);
    $stmt_id->execute();
    $result = $stmt_id->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $account_id = $row['id'];
        
        // Determinar tipo de cuenta
        $d1 = substr($codigo, 0, 1);
        $tipo_cuenta = 'activo';
        if ($d1 === '2') $tipo_cuenta = 'pasivo';
        elseif ($d1 === '3') $tipo_cuenta = 'patrimonio';
        elseif ($d1 === '4') $tipo_cuenta = 'ingreso';
        elseif ($d1 === '5') $tipo_cuenta = 'gasto';
        
        // Lógica CORREGIDA: 
        // En el CSV, los signos están invertidos respecto a la lógica contable normal
        // Negativo en CSV significa que es el saldo NORMAL de esa cuenta
        $debe = 0;
        $haber = 0;
        $monto = abs($saldo); // Trabajamos con valor absoluto
        
        if ($tipo_cuenta === 'activo' || $tipo_cuenta === 'gasto') {
            // Activo/Gasto: su naturaleza es DEUDORA
            if ($saldo >= 0) {
                $debe = $monto;
            } else {
                $haber = $monto;
            }
        } else {
            // Pasivo/Patrimonio/Ingreso: su naturaleza es ACREEDORA
            if ($saldo < 0) {
                $haber = $monto; // Negativo en CSV = HABER (normal)
            } else {
                $debe = $monto;  // Positivo en CSV = DEBE (anormal)
            }
        }
        
        $stmt_entry->bind_param('iidds', $journal_id, $account_id, $debe, $haber, $nombre);
        $stmt_entry->execute();
        
        if ($debe > 0 || $haber > 0) {
            echo "  [$codigo] $nombre ($tipo_cuenta) - D:" . number_format($debe, 2) . " H:" . number_format($haber, 2) . "\n";
            $total_debe += $debe;
            $total_haber += $haber;
            $lineas++;
        }
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
