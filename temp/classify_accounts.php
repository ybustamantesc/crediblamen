<?php
// classify_accounts.php
// Infers missing `type` from account code and sets `naturaleza` = 'deudora'|'acreedora'

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$mysqli = new mysqli('localhost','root','','minitas');
if ($mysqli->connect_error) {
    echo json_encode(['status'=>'error','message'=>'DB connect error: '.$mysqli->connect_error], JSON_PRETTY_PRINT);
    exit(1);
}
$mysqli->set_charset('utf8mb4');

// Ensure column `naturaleza` exists
$schema = 'minitas';
$check = $mysqli->query("SELECT COUNT(*) AS c FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='".$schema."' AND TABLE_NAME='tb_account' AND COLUMN_NAME='naturaleza'");
$hasNaturaleza = ($check && $check->fetch_assoc()['c']>0);
if (!$hasNaturaleza) {
    $ok = $mysqli->query("ALTER TABLE tb_account ADD COLUMN naturaleza VARCHAR(16) NULL AFTER type");
    if (!$ok) {
        echo json_encode(['status'=>'error','message'=>'Failed to add naturaleza column: '.$mysqli->error], JSON_PRETTY_PRINT);
        exit(1);
    }
}

// Select accounts needing attention (missing naturaleza or missing type)
$sql = "SELECT id, code, name, type, naturaleza FROM tb_account WHERE naturaleza IS NULL OR naturaleza = '' OR type IS NULL OR type = ''";
$res = $mysqli->query($sql);
if (!$res) {
    echo json_encode(['status'=>'error','message'=>$mysqli->error], JSON_PRETTY_PRINT);
    exit(1);
}

$updated = 0;
$changes = [];

$mysqli->begin_transaction();
try {
    while ($row = $res->fetch_assoc()) {
        $id = (int)$row['id'];
        $code = trim($row['code']);
        $oldType = $row['type'];
        $oldNaturaleza = $row['naturaleza'];

        // Infer type if missing
        $type = $oldType;
        if (!$type || trim($type) === '') {
            $first = substr($code, 0, 1);
            switch ($first) {
                case '1': $type = 'activo'; break;
                case '2': $type = 'pasivo'; break;
                case '3': $type = 'patrimonio'; break;
                case '4': $type = 'ingreso'; break;
                case '5': case '6': case '7': $type = 'gasto'; break;
                default: $type = 'activo';
            }
        }

        // Determine naturaleza
        if (in_array($type, ['activo','gasto'])) {
            $naturaleza = 'deudora';
        } else {
            $naturaleza = 'acreedora';
        }

        // If no change, continue
        if ($type === $oldType && $naturaleza === $oldNaturaleza) continue;

        // Apply update
        $stmt = $mysqli->prepare('UPDATE tb_account SET type = ?, naturaleza = ? WHERE id = ?');
        $stmt->bind_param('ssi', $type, $naturaleza, $id);
        if ($stmt->execute()) {
            $updated++;
            $changes[] = ['id'=>$id,'code'=>$code,'old_type'=>$oldType,'old_naturaleza'=>$oldNaturaleza,'new_type'=>$type,'new_naturaleza'=>$naturaleza];
        } else {
            throw new Exception('Update failed for id '.$id.': '.$mysqli->error);
        }
        $stmt->close();
    }
    $mysqli->commit();
} catch (Exception $e) {
    $mysqli->rollback();
    echo json_encode(['status'=>'error','message'=>$e->getMessage()], JSON_PRETTY_PRINT);
    exit(1);
}

$mysqli->close();

echo json_encode(['status'=>'success','updated'=>$updated,'changes_count'=>count($changes),'sample_changes'=>array_slice($changes,0,30)], JSON_PRETTY_PRINT);
