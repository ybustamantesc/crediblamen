<?php
// apply_muc_grouping.php
// Populate muc_group/muc_subgroup from tb_account.code and set statement by muc_class

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');

$mysqli = new mysqli('localhost','root','','minitas');
if ($mysqli->connect_error) { echo json_encode(['status'=>'error','message'=>$mysqli->connect_error]); exit(1); }
$mysqli->set_charset('utf8mb4');

$updated = 0;
$errors = [];
$samples = [];

$res = $mysqli->query("SELECT id, code, muc_class, muc_group, muc_subgroup, statement FROM tb_account ORDER BY id");
if (!$res) { echo json_encode(['status'=>'error','message'=>$mysqli->error], JSON_PRETTY_PRINT); exit(1); }

$mysqli->begin_transaction();
try {
    while ($r = $res->fetch_assoc()) {
        $id = (int)$r['id'];
        $code = trim($r['code']);
        $muc_class = $r['muc_class'];
        $cur_group = $r['muc_group'];
        $cur_sub = $r['muc_subgroup'];
        $cur_stmt = $r['statement'];

        // sanitize code -> keep digits only for prefix extraction
        $digits = preg_replace('/\D+/', '', $code);
        if ($digits === '') {
            // fallback: use code as-is
            $g = substr($code,0,2);
            $s = substr($code,0,3);
        } else {
            $g = strlen($digits) >= 2 ? substr($digits,0,2) : $digits;
            $s = strlen($digits) >= 3 ? substr($digits,0,3) : $digits;
        }

        // determine statement from muc_class if available
        $new_stmt = $cur_stmt;
        if ($muc_class !== null && $muc_class !== '') {
            if (in_array($muc_class, ['1','2','3','01','02','03'])) $new_stmt = 'BS';
            elseif (in_array($muc_class, ['4','5','6','7','04','05','06','07'])) $new_stmt = 'IS';
        }

        $need_update = false;
        $updates = [];
        if (($cur_group === null || $cur_group === '') && $g !== '') { $updates['muc_group'] = $g; $need_update = true; }
        if (($cur_sub === null || $cur_sub === '') && $s !== '') { $updates['muc_subgroup'] = $s; $need_update = true; }
        if ($new_stmt !== $cur_stmt && $new_stmt !== null) { $updates['statement'] = $new_stmt; $need_update = true; }

        if ($need_update) {
            $sets = [];
            foreach ($updates as $col => $val) {
                $sets[] = "`".$col."` = '".$mysqli->real_escape_string($val)."'";
            }
            $sql = "UPDATE tb_account SET " . implode(',', $sets) . " WHERE id = " . $id;
            if ($mysqli->query($sql)) {
                $updated++;
                if (count($samples) < 25) $samples[] = ['id'=>$id,'code'=>$code,'muc_group'=>$g,'muc_subgroup'=>$s,'old_statement'=>$cur_stmt,'new_statement'=>$new_stmt];
            } else {
                $errors[] = "id $id update failed: " . $mysqli->error;
            }
        }
    }
    $mysqli->commit();
} catch (Exception $e) {
    $mysqli->rollback();
    echo json_encode(['status'=>'error','message'=>$e->getMessage()], JSON_PRETTY_PRINT);
    exit(1);
}

$mysqli->close();

echo json_encode(['status'=>'success','updated'=>$updated,'errors'=>$errors,'samples'=>$samples], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
