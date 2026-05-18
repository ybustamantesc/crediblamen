<?php
// clean_account_names.php
// Limpia sufijos numéricos de los nombres de cuentas en tb_account

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$mysqli = new mysqli('localhost','root','','minitas');
if ($mysqli->connect_error) {
    echo json_encode(['status'=>'error','message'=>$mysqli->connect_error], JSON_PRETTY_PRINT);
    exit(1);
}
$mysqli->set_charset('utf8mb4');

$res = $mysqli->query("SELECT id, name FROM tb_account");
if (!$res) {
    echo json_encode(['status'=>'error','message'=>$mysqli->error], JSON_PRETTY_PRINT);
    exit(1);
}

$updated = 0;
$changes = [];

while ($row = $res->fetch_assoc()) {
    $id = (int)$row['id'];
    $name = $row['name'];
    if ($name === null) continue;

    // Normalizar espacios y control chars
    $clean = preg_replace('/\s+/u', ' ', trim($name));

    // Eliminar sufijos de dígitos individuales (ej. " 1 0 0 0" o " 0 0 0 0")
    $clean = preg_replace('/(?:\s+[01])+$/u', '', $clean);

    // También eliminar sufijos de varios ceros separados por espacios (por si acaso)
    $clean = preg_replace('/(?:\s+0{1,})+$/u', '', $clean);

    // Eliminar sufijos de un dígito final aislado (ej. " TOTAL 0")
    $clean = preg_replace('/\s+\d{1}$/u', '', $clean);

    $clean = trim($clean);

    if ($clean !== $name) {
        $stmt = $mysqli->prepare('UPDATE tb_account SET name = ? WHERE id = ?');
        $stmt->bind_param('si', $clean, $id);
        if ($stmt->execute()) {
            $updated++;
            $changes[] = ['id'=>$id, 'old'=>$name, 'new'=>$clean];
        }
        $stmt->close();
    }
}

$mysqli->close();

echo json_encode(['status'=>'success','updated'=>$updated,'changes_count'=>count($changes),'sample_changes'=>array_slice($changes,0,20)], JSON_PRETTY_PRINT);
