<?php
// TEST REAL
echo "Iniciando...\n";

$conn = new mysqli('localhost', 'root', '', 'u987557742_testsystem');
echo "Conexión: " . ($conn->connect_error ? "ERROR" : "OK") . "\n";

$archivo = 'C:/xampp/htdocs/Servicredit/temp/ejemplo_balanza_abril_2025.csv';
echo "Archivo existe: " . (file_exists($archivo) ? "SI" : "NO") . "\n";

$h = fopen($archivo, 'r');
$encabezado = fgetcsv($h);
echo "Encabezado: " . implode(", ", $encabezado) . "\n\n";

$cuentas = 0;
$debe_total = 0;
$haber_total = 0;

while (($r = fgetcsv($h)) !== false) {
    if (count($r) < 6) continue;
    $codigo = trim($r[0]);
    if (empty($codigo)) continue;
    
    $nombre = trim($r[1]);
    $saldo = floatval(str_replace([',', '"', ' '], '', $r[5]));
    
    // Tipo por primer dígito
    $d1 = substr($codigo, 0, 1);
    $tipo = 'activo';
    if ($d1 === '2') $tipo = 'pasivo';
    elseif ($d1 === '3') $tipo = 'patrimonio';
    elseif ($d1 === '4') $tipo = 'ingreso';
    elseif ($d1 === '5') $tipo = 'gasto';
    
    // Debe/Haber
    $debe = 0;
    $haber = 0;
    
    if ($tipo === 'activo' || $tipo === 'gasto') {
        if ($saldo > 0) {
            $debe = $saldo;
            $debe_total += $saldo;
        }
    } else {
        if ($saldo > 0) {
            $haber = $saldo;
            $haber_total += $saldo;
        }
    }
    
    // Verificar si existe
    $stmt = $conn->prepare("SELECT id FROM tb_account WHERE code = ?");
    $stmt->bind_param('s', $codigo);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $id = $row['id'];
        echo "[$codigo] $nombre - YA EXISTE (ID: $id)\n";
    } else {
        // Crear cuenta
        $stmt2 = $conn->prepare("INSERT INTO tb_account (code, name, type, created_at) VALUES (?, ?, ?, NOW())");
        $stmt2->bind_param('sss', $codigo, $nombre, $tipo);
        if ($stmt2->execute()) {
            $id = $conn->insert_id;
            echo "[$codigo] $nombre - CREADA (ID: $id, Tipo: $tipo)\n";
            $cuentas++;
        } else {
            echo "[$codigo] ERROR: " . $stmt2->error . "\n";
        }
        $stmt2->close();
    }
    $stmt->close();
}

fclose($h);

echo "\n=== RESUMEN ===\n";
echo "Cuentas nuevas creadas: $cuentas\n";
echo "Total Debe: " . number_format($debe_total, 2) . "\n";
echo "Total Haber: " . number_format($haber_total, 2) . "\n";
echo "Diferencia: " . number_format(abs($debe_total - $haber_total), 2) . "\n";

$conn->close();
