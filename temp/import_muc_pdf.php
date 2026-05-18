<?php
// import_muc_pdf.php
// Extrae cuentas del PDF del catálogo MUC y las inserta/actualiza en tb_account

require __DIR__ . '/../vendor/autoload.php';

use Smalot\PdfParser\Parser;

header('Content-Type: application/json');

$pdfPath = __DIR__ . '/Cuentas del catalogo MUC.pdf';
if (!file_exists($pdfPath)) {
    echo json_encode(['status' => 'error', 'message' => "Archivo no encontrado: $pdfPath"], JSON_PRETTY_PRINT);
    exit(1);
}

$parser = new Parser();
try {
    $pdf = $parser->parseFile($pdfPath);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Error al parsear PDF: ' . $e->getMessage()], JSON_PRETTY_PRINT);
    exit(1);
}

$text = $pdf->getText();
$lines = preg_split('/\r?\n/', $text);

$items = [];
$current = null;

foreach ($lines as $raw) {
    $line = trim($raw);
    if ($line === '') continue;

    // Detectar línea que empieza con código (ej: 1, 10, 1001, 1.01, 1 01, etc.)
    if (preg_match('/^(\d[\d\.\s\-]*\d?)\s+(.+)$/u', $line, $m)) {
        // Nueva entrada
        if ($current !== null) {
            $items[] = $current;
        }
        $code = preg_replace('/\s+/', '', $m[1]);
        $code = rtrim($code, '.-');
        $name = trim($m[2]);
        $current = ['code' => $code, 'name' => $name];
    } else {
        // Continuación del nombre
        if ($current !== null) {
            $current['name'] .= ' ' . $line;
        }
    }
}
if ($current !== null) $items[] = $current;

// Conectar a la base de datos (usar credenciales del proyecto)
$mysqli = new mysqli('localhost', 'root', '', 'minitas');
if ($mysqli->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'DB connection error: ' . $mysqli->connect_error], JSON_PRETTY_PRINT);
    exit(1);
}
$mysqli->set_charset('utf8mb4');

$inserted = 0;
$updated = 0;
$skipped = 0;
$errors = [];

$now = date('Y-m-d H:i:s');
foreach ($items as $it) {
    $code = $mysqli->real_escape_string($it['code']);
    $name = $mysqli->real_escape_string($it['name']);
    if ($code === '' || $name === '') {
        $skipped++;
        continue;
    }

    // Inferir tipo por primer dígito
    $first = substr($it['code'], 0, 1);
    switch ($first) {
        case '1': $type = 'activo'; break;
        case '2': $type = 'pasivo'; break;
        case '3': $type = 'patrimonio'; break;
        case '4': $type = 'ingreso'; break;
        case '5': case '6': case '7': $type = 'gasto'; break;
        default: $type = 'activo';
    }

    // Inferir niveles y MUC
    $digits = preg_replace('/\D+/', '', $it['code']);
    $len = strlen($digits);
    if ($len <= 1) $level = 1;
    elseif ($len == 2) $level = 2;
    elseif ($len == 3) $level = 3;
    else $level = 4;

    $muc_class = $mysqli->real_escape_string(substr($digits, 0, 1));
    $muc_group = $mysqli->real_escape_string(substr($digits, 0, min(2, strlen($digits))));
    $muc_subgroup = $mysqli->real_escape_string(substr($digits, 0, min(3, strlen($digits))));

    $statement = 'BS';
    $postable = 0; // por defecto
    $must_report = 0;

    // Verificar existencia
    $q = "SELECT id FROM tb_account WHERE code = '" . $code . "' LIMIT 1";
    $res = $mysqli->query($q);
    if ($res && $res->num_rows > 0) {
        // actualizar (no asumimos columna updated_at en la tabla)
        $sql = "UPDATE tb_account SET name='" . $name . "', `level`=" . intval($level) . ", muc_class='" . $muc_class . "', muc_group='" . $muc_group . "', muc_subgroup='" . $muc_subgroup . "', statement='" . $statement . "', postable=" . intval($postable) . ", must_report=" . intval($must_report) . " WHERE code='" . $code . "'";
        if ($mysqli->query($sql)) {
            $updated++;
        } else {
            $errors[] = "Error updating $code: " . $mysqli->error;
        }
    } else {
        // insertar
        $sql = "INSERT INTO tb_account (code, name, type, `level`, muc_class, muc_group, muc_subgroup, statement, postable, must_report, created_at) VALUES ('" . $code . "', '" . $name . "', '" . $mysqli->real_escape_string($type) . "', " . intval($level) . ", '" . $muc_class . "', '" . $muc_group . "', '" . $muc_subgroup . "', '" . $statement . "', " . intval($postable) . ", " . intval($must_report) . ", '" . $now . "')";
        if ($mysqli->query($sql)) {
            $inserted++;
        } else {
            $errors[] = "Error inserting $code: " . $mysqli->error;
        }
    }
}

// Cerrar conexión
$mysqli->close();

echo json_encode([
    'status' => 'success',
    'message' => 'Import finished',
    'counts' => [
        'total_parsed' => count($items),
        'inserted' => $inserted,
        'updated' => $updated,
        'skipped' => $skipped,
        'errors' => $errors
    ]
], JSON_PRETTY_PRINT);
