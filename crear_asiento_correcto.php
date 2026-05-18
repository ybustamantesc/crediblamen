<?php
echo "Creando asiento CORRECTO - Cargos=DEBE, Abonos=HABER\n\n";

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

// Leer CSV y usar columnas correctas
$archivo = 'C:/xampp/htdocs/Servicredit/temp/ejemplo_balanza_abril_2025.csv';
$h = fopen($archivo, 'r');
$encabezado = fgetcsv($h);
echo "Columnas: " . implode(" | ", $encabezado) . "\n\n";

$stmt_entry = $conn->prepare("INSERT INTO tb_journal_entry (journal_id, account_id, debit, credit, description) VALUES (?, ?, ?, ?, ?)");

$lineas = 0;
$total_debe = 0;
$total_haber = 0;

while (($r = fgetcsv($h)) !== false) {
    if (count($r) < 6) continue;
    $codigo = trim($r[0]);
    if (empty($codigo)) continue;
    
    $nombre = trim($r[1]);
    // CORRECCIÓN: Columna 4 = Cargos (DEBE), Columna 5 = Abonos (HABER)
    $debe = floatval(str_replace([',', '"', ' '], '', $r[3]));  // Cargos
    $haber = floatval(str_replace([',', '"', ' '], '', $r[4])); // Abonos
    
    // Obtener ID de cuenta
    $stmt_id = $conn->prepare("SELECT id FROM tb_account WHERE code = ?");
    $stmt_id->bind_param('s', $codigo);
    $stmt_id->execute();
    $result = $stmt_id->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $account_id = $row['id'];
        
        // Solo agregar si hay movimiento
        if ($debe > 0 || $haber > 0) {
            $stmt_entry->bind_param('iidds', $journal_id, $account_id, $debe, $haber, $nombre);
            $stmt_entry->execute();
            
            echo "  [$codigo] $nombre\n";
            echo "    Debe (Cargos): " . number_format($debe, 2) . "\n";
            echo "    Haber (Abonos): " . number_format($haber, 2) . "\n\n";
            
            $total_debe += $debe;
            $total_haber += $haber;
            $lineas++;
        }
    }
    $stmt_id->close();
}

fclose($h);
$stmt_entry->close();

// Actualizar totales en el journal
$stmt_update = $conn->prepare("UPDATE tb_journal SET total_debit = ?, total_credit = ? WHERE id = ?");
$stmt_update->bind_param('ddi', $total_debe, $total_haber, $journal_id);
$stmt_update->execute();
$stmt_update->close();

echo "=== ASIENTO CREADO ===\n";
echo "Journal ID: $journal_id\n";
echo "Líneas: $lineas\n";
echo "Total Debe (Cargos): " . number_format($total_debe, 2) . "\n";
echo "Total Haber (Abonos): " . number_format($total_haber, 2) . "\n";
echo "Diferencia: " . number_format(abs($total_debe - $total_haber), 2) . "\n";
echo "Cuadrado: " . (abs($total_debe - $total_haber) < 0.01 ? "SI ✓" : "NO ✗") . "\n";

$conn->close();
