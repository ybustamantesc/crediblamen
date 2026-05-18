<?php
echo "Creando asiento contable de apertura...\n\n";

$conn = new mysqli('localhost', 'root', '', 'u987557742_testsystem');

// Crear asiento
$fecha = '2025-04-30';
$descripcion = 'Asiento de Apertura - Abril 2025';
$mes = '04';
$anio = '2025';
$tipo = 'apertura';

$stmt = $conn->prepare("INSERT INTO tb_journal (date, description, period_month, period_year, entry_type, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
$stmt->bind_param('ssiss', $fecha, $descripcion, $mes, $anio, $tipo);
$stmt->execute();
$journal_id = $conn->insert_id;
$stmt->close();

echo "Asiento creado: ID $journal_id\n";
echo "Fecha: $fecha\n";
echo "Descripción: $descripcion\n\n";

// Leer cuentas del CSV y crear entradas
$archivo = 'C:/xampp/htdocs/Servicredit/temp/ejemplo_balanza_abril_2025.csv';
$h = fopen($archivo, 'r');
fgetcsv($h); // Saltar encabezado

$stmt_entry = $conn->prepare("INSERT INTO tb_journal_entry (journal_id, account_id, debit, credit, description) VALUES (?, ?, ?, ?, ?)");

$lineas = 0;
$total_debe = 0;
$total_haber = 0;

while (($r = fgetcsv($h)) !== false) {
    if (count($r) < 6) continue;
    $codigo = trim($r[0]);
    if (empty($codigo)) continue;
    
    $nombre = trim($r[1]);
    $saldo = floatval(str_replace([',', '"', ' '], '', $r[5]));
    
    // Obtener ID de la cuenta
    $stmt_id = $conn->prepare("SELECT id FROM tb_account WHERE code = ?");
    $stmt_id->bind_param('s', $codigo);
    $stmt_id->execute();
    $result = $stmt_id->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $account_id = $row['id'];
        
        // Determinar tipo y debe/haber
        $d1 = substr($codigo, 0, 1);
        $tipo_cuenta = 'activo';
        if ($d1 === '2') $tipo_cuenta = 'pasivo';
        elseif ($d1 === '3') $tipo_cuenta = 'patrimonio';
        elseif ($d1 === '4') $tipo_cuenta = 'ingreso';
        elseif ($d1 === '5') $tipo_cuenta = 'gasto';
        
        $debe = 0;
        $haber = 0;
        
        if ($tipo_cuenta === 'activo' || $tipo_cuenta === 'gasto') {
            if ($saldo > 0) {
                $debe = $saldo;
            } elseif ($saldo < 0) {
                $haber = abs($saldo);
            }
        } else {
            if ($saldo > 0) {
                $haber = $saldo;
            } elseif ($saldo < 0) {
                $debe = abs($saldo);
            }
        }
        
        if ($debe > 0 || $haber > 0) {
            $stmt_entry->bind_param('iidds', $journal_id, $account_id, $debe, $haber, $nombre);
            $stmt_entry->execute();
            
            echo "  [$codigo] $nombre - Debe: " . number_format($debe, 2) . " / Haber: " . number_format($haber, 2) . "\n";
            
            $total_debe += $debe;
            $total_haber += $haber;
            $lineas++;
        }
    }
    $stmt_id->close();
}

fclose($h);
$stmt_entry->close();

echo "\n=== ASIENTO CREADO ===\n";
echo "Journal ID: $journal_id\n";
echo "Líneas: $lineas\n";
echo "Total Debe: " . number_format($total_debe, 2) . "\n";
echo "Total Haber: " . number_format($total_haber, 2) . "\n";
echo "Cuadrado: " . (abs($total_debe - $total_haber) < 0.01 ? "SI" : "NO") . "\n";

$conn->close();
