<?php
// process_accounts.php
// Asigna parent_id a cuentas basándose en prefijos de código y marca postable para cuentas hoja.

error_reporting(E_ALL);
ini_set('display_errors', 1);

$conn = new mysqli('localhost','root','','minitas');
if ($conn->connect_error) {
    echo json_encode(['status'=>'error','message'=>'DB connect error: '.$conn->connect_error], JSON_PRETTY_PRINT);
    exit(1);
}
$conn->set_charset('utf8mb4');

// Cargar cuentas
$res = $conn->query("SELECT id, code FROM tb_account ORDER BY CHAR_LENGTH(code) ASC");
$map = [];
$accounts = [];
while ($row = $res->fetch_assoc()) {
    $map[$row['code']] = $row['id'];
    $accounts[] = $row;
}

// Ordenar por longitud descendente para asignar padres más cortos primero
usort($accounts, function($a,$b){ return strlen($b['code']) - strlen($a['code']); });

$parent_updates = 0;
$postable_updates = 0;
$errors = [];

foreach ($accounts as $acc) {
    $code = $acc['code'];
    $id = (int)$acc['id'];
    $parent_id = null;

    $len = strlen($code);
    for ($i = $len - 1; $i >= 1; $i--) {
        $prefix = substr($code, 0, $i);
        if (isset($map[$prefix])) {
            $parent_id = (int)$map[$prefix];
            break;
        }
    }

    // Update parent_id if different
    $q = $conn->query("SELECT parent_id FROM tb_account WHERE id = " . $id);
    if ($q) {
        $cur = $q->fetch_assoc();
        $cur_parent = $cur['parent_id'];
        if (($cur_parent === null && $parent_id !== null) || ($cur_parent !== null && intval($cur_parent) !== $parent_id)) {
            $upd_sql = "UPDATE tb_account SET parent_id = " . ($parent_id !== null ? $parent_id : 'NULL') . " WHERE id = " . $id;
            if ($conn->query($upd_sql)) $parent_updates++;
            else $errors[] = "parent update failed for $id: " . $conn->error;
        }
    } else {
        $errors[] = "select parent failed for $id: " . $conn->error;
    }
}

// Marcar postable: cuenta es postable si no tiene hijos (ninguna otra cuenta cuyo código comience con su código y sea más larga)
$res2 = $conn->query("SELECT id, code FROM tb_account");
while ($r = $res2->fetch_assoc()) {
    $id = (int)$r['id'];
    $code = $conn->real_escape_string($r['code']);
    $sql = "SELECT COUNT(*) AS cnt FROM tb_account WHERE code LIKE '" . $code . "%' AND code != '" . $code . "'";
    $c = $conn->query($sql);
    if (!$c) { $errors[] = "count children failed for $id: " . $conn->error; continue; }
    $cnt = (int)$c->fetch_assoc()['cnt'];
    $should_postable = ($cnt === 0) ? 1 : 0;

    $qcur = $conn->query("SELECT postable FROM tb_account WHERE id = " . $id);
    if ($qcur) {
        $curv = $qcur->fetch_assoc();
        $cur_post = (int)$curv['postable'];
        if ($cur_post !== $should_postable) {
            $u = $conn->query("UPDATE tb_account SET postable = " . $should_postable . " WHERE id = " . $id);
            if ($u) $postable_updates++;
            else $errors[] = "postable update failed for $id: " . $conn->error;
        }
    } else {
        $errors[] = "select postable failed for $id: " . $conn->error;
    }
}

$conn->close();

echo json_encode([
    'status' => 'success',
    'counts' => [
        'total_accounts' => count($map),
        'parent_assigned' => $parent_updates,
        'postable_changed' => $postable_updates,
        'errors' => $errors
    ]
], JSON_PRETTY_PRINT);
