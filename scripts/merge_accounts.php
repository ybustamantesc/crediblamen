<?php
// Merge accounts helper
// Usage:
// php merge_accounts.php --file="uploads/cuentas contables.csv" --dbhost=127.0.0.1 --dbuser=root --dbpass= --dbname=crediblamen_db [--apply] [--derive-parent] [--limit=N]

set_time_limit(0);
ini_set('memory_limit','512M');

$options = getopt('', ['file:','dbhost:','dbuser:','dbpass:','dbname:','apply','derive-parent','limit:','sep:','help']);
if (isset($options['help'])) {
    echo "Usage: php merge_accounts.php --file=PATH --dbname=DBNAME [--apply] [--derive-parent]\n";
    exit(0);
}

$file = isset($options['file']) ? $options['file'] : __DIR__ . '/../uploads/cuentas contables.csv';
$dbhost = isset($options['dbhost']) ? $options['dbhost'] : '127.0.0.1';
$dbuser = isset($options['dbuser']) ? $options['dbuser'] : 'root';
$dbpass = isset($options['dbpass']) ? $options['dbpass'] : '';
$dbname = isset($options['dbname']) ? $options['dbname'] : 'crediblamen_db';
$apply = isset($options['apply']);
$derive_parent = isset($options['derive-parent']);
$limit = isset($options['limit']) ? intval($options['limit']) : 200;
$sep = isset($options['sep']) ? $options['sep'] : ';';

if (!file_exists($file)) {
    fwrite(STDERR, "File not found: $file\n");
    exit(1);
}

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($mysqli->connect_errno) { fwrite(STDERR, "DB connect error: " . $mysqli->connect_error . "\n"); exit(1); }
$mysqli->set_charset('utf8mb4');

$fh = fopen($file, 'r');
if (!$fh) { fwrite(STDERR, "Could not open file: $file\n"); exit(1); }

$header = fgetcsv($fh, 0, $sep);
if ($header === false) { fwrite(STDERR, "Empty or invalid CSV\n"); exit(1); }
$cols = array_map(function($c){ return strtolower(trim($c)); }, $header);
$map = [];
foreach ($cols as $i => $col) $map[$col] = $i;

function val($row, $map, $names) {
    foreach ((array)$names as $n) {
        $key = strtolower(trim($n));
        if (isset($map[$key]) && isset($row[$map[$key]])) return trim($row[$map[$key]]);
    }
    return null;
}

// Define header aliases for flexible mapping
$code_keys = ['cuentamuc','cuentacrediblamen','code','cuenta','cuenta_id','cuenta','cuentas'];
$name_keys = ['nombredecuentamuc','nombrecuenta','name','nombre','descripcion','description'];
$type_keys = ['type','account_type','tipo','grupo','group'];
$parent_code_keys = ['parent_code','cuentapadre','parent','padre','parentcode'];


// detect target table and columns (reuse logic from import_accounts)
$target_table = 'tb_account';
$name_col = 'name';
$type_col = 'type';
$res = $mysqli->query("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = '" . $mysqli->real_escape_string($dbname) . "' AND TABLE_NAME IN ('tb_account','b_account','teso_accounts')");
if ($res) {
    $found = [];
    while ($r = $res->fetch_assoc()) $found[] = $r['TABLE_NAME'];
    if (!in_array('tb_account', $found) && in_array('b_account', $found)) {
        $target_table = 'b_account';
        $cols = []; $cres = $mysqli->query("SHOW COLUMNS FROM b_account");
        if ($cres) { while ($c = $cres->fetch_assoc()) $cols[] = $c['Field']; }
        if (in_array('ame', $cols)) $name_col = 'ame';
        if (in_array('type', $cols)) $type_col = 'type'; elseif (in_array('account_type', $cols)) $type_col = 'account_type'; elseif (in_array('ype', $cols)) $type_col = 'ype';
    }
}

echo "Target table: $target_table (name_col=$name_col, type_col=$type_col)\n";

$to_insert = [];
$to_update = [];
$skipped = [];
$processed = 0;

// prepare select stmt
$sel = $mysqli->prepare("SELECT id, `".$name_col."` as name, `".$type_col."` as type FROM `".$target_table."` WHERE code = ? LIMIT 1");
if (!$sel) { fwrite(STDERR, "Prepare select failed: " . $mysqli->error . "\n"); exit(1); }

while (($row = fgetcsv($fh, 0, $sep)) !== false) {
    $processed++;
    $code = val($row, $map, $code_keys);
    $name = val($row, $map, $name_keys);
    $provided_type = val($row, $map, $type_keys);
    $parent_code = val($row, $map, $parent_code_keys);
    if (!$code) { $skipped[] = ['row'=>$processed,'reason'=>'no_code']; continue; }
    $code = preg_replace('/\s+/', '', $code);
    if ($code === '') { $skipped[] = ['row'=>$processed,'code'=>$code,'reason'=>'empty_code']; continue; }
    if (!$name) $name = $code;
    $first = substr($code,0,1);
    // Prefer provided type column when present; otherwise derive from code prefix
    if ($provided_type) {
        $type = strtolower(trim($provided_type));
    } else {
        $type = 'otro';
        if ($first === '1') $type = 'activo'; elseif ($first === '2') $type = 'pasivo'; elseif ($first === '3') $type = 'patrimonio'; elseif ($first === '4') $type = 'ingreso'; elseif ($first === '5') $type = 'gasto';
    }

    // check existing
    $sel->bind_param('s', $code);
    $sel->execute();
    $res = $sel->get_result();
    $existing = $res->fetch_assoc();
    if (!$existing) {
        $to_insert[$code] = ['code'=>$code,'name'=>$name,'type'=>$type,'row'=>$processed,'parent_code'=>$parent_code];
    } else {
        // compare
        $needs = [];
        if (trim((string)$existing['name']) !== trim((string)$name)) $needs['name'] = ['old'=>$existing['name'],'new'=>$name];
        if (trim((string)$existing['type']) !== trim((string)$type)) $needs['type'] = ['old'=>$existing['type'],'new'=>$type];
        if (!empty($needs)) $to_update[$code] = ['id'=>$existing['id'],'code'=>$code,'name'=>$name,'type'=>$type,'diff'=>$needs,'row'=>$processed,'parent_code'=>$parent_code];
        else $skipped[] = ['row'=>$processed,'code'=>$code,'reason'=>'no_change'];
    }
}

fclose($fh);

echo "Processed: $processed\n";
echo "To insert: " . count($to_insert) . "\n";
echo "To update: " . count($to_update) . "\n";
echo "Skipped (no action): " . count($skipped) . "\n";

// show samples
if (count($to_insert)) {
    echo "\nSample inserts:\n";
    $i=0; foreach ($to_insert as $c=>$row) { echo "  {$row['code']} | {$row['type']} | {$row['name']}\n"; if (++$i >= $limit) break; }
}
if (count($to_update)) {
    echo "\nSample updates:\n";
    $i=0; foreach ($to_update as $c=>$row) { echo "  {$row['code']} | changes: "; foreach($row['diff'] as $k=>$v) echo "$k: '{$v['old']}' => '{$v['new']}' "; echo "\n"; if (++$i >= $limit) break; }
}

if (!$apply) {
    echo "\nNo changes applied. Re-run with --apply to perform inserts/updates.\n";
    exit(0);
}

// Apply changes
$mysqli->begin_transaction();
try {
    $ins_sql = "INSERT INTO `{$target_table}` (`code`,`{$name_col}` , `{$type_col}`, `created_at`) VALUES (?,?,?,NOW())";
    $upd_sql = "UPDATE `{$target_table}` SET `{$name_col}` = ?, `{$type_col}` = ? WHERE code = ?";
    $ins = $mysqli->prepare($ins_sql);
    $upd = $mysqli->prepare($upd_sql);
    if (!$ins || !$upd) throw new Exception('Prepare failed: ' . $mysqli->error);

    $inserted = 0; $updated = 0;
    foreach ($to_insert as $code => $r) {
        $ins->bind_param('sss', $r['code'], $r['name'], $r['type']);
        if (!$ins->execute()) { fwrite(STDERR, "Insert error for {$r['code']}: " . $ins->error . "\n"); continue; }
        $inserted++;
    }
    foreach ($to_update as $code => $r) {
        $upd->bind_param('sss', $r['name'], $r['type'], $r['code']);
        if (!$upd->execute()) { fwrite(STDERR, "Update error for {$r['code']}: " . $upd->error . "\n"); continue; }
        $updated++;
    }

    echo "\nApplied: Inserted={$inserted}, Updated={$updated}\n";

    // derive parent if requested
    if ($derive_parent) {
        echo "Deriving parent_id...\n";
        // ensure parent_id column exists
        $has_parent_col = false;
        $cres = $mysqli->query("SHOW COLUMNS FROM `{$target_table}` LIKE 'parent_id'");
        if ($cres && $cres->num_rows) $has_parent_col = true;
        if (!$has_parent_col) {
            echo "Target table has no parent_id column; skipping parent derivation.\n";
        } else {
            $res = $mysqli->query("SELECT id, code FROM `{$target_table}`");
            $code_map = [];
            while ($r = $res->fetch_assoc()) $code_map[$r['code']] = (int)$r['id'];
            // First, apply explicit parent_code mappings from imported rows
            $resolved = [];
            foreach (array_merge($to_insert, $to_update) as $code => $info) {
                if (!empty($info['parent_code'])) {
                    $pc = preg_replace('/\s+/', '', $info['parent_code']);
                    if ($pc !== '' && isset($code_map[$pc])) {
                        $pid = $code_map[$pc];
                        $u = $mysqli->prepare("UPDATE `{$target_table}` SET parent_id = ? WHERE code = ?");
                        $u->bind_param('is', $pid, $code);
                        $u->execute();
                        $u->close();
                        $resolved[$code] = true;
                    }
                }
            }

            // Next, for remaining inserted codes, use longest-prefix algorithm
            $all_codes = array_keys($code_map);
            usort($all_codes, function($a,$b){ return strlen($b) - strlen($a); });
            foreach (array_keys($to_insert) as $c) {
                if (isset($resolved[$c])) continue;
                $parent_id = null; $best_len = 0;
                foreach ($all_codes as $candidate) {
                    if ($candidate === $c) continue;
                    if (strpos($c, $candidate) === 0) {
                        $len = strlen($candidate);
                        if ($len > $best_len) { $best_len = $len; $parent_id = $code_map[$candidate]; }
                    }
                }
                if ($parent_id) {
                    $upd_p = $mysqli->prepare("UPDATE `{$target_table}` SET parent_id = ? WHERE code = ?");
                    $upd_p->bind_param('is', $parent_id, $c);
                    $upd_p->execute();
                    $upd_p->close();
                }
            }
            echo "Parent derivation done.\n";
        }
    }

    $mysqli->commit();
} catch (Exception $e) {
    $mysqli->rollback();
    fwrite(STDERR, "Merge failed: " . $e->getMessage() . "\n");
    exit(1);
}

echo "Done.\n";
exit(0);
