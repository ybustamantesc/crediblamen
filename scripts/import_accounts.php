<?php
// simple CLI importer for Chart of Accounts CSV
// Usage:
// php import_accounts.php --file="C:/xampp/htdocs/servicredit/uploads/cuentas contables.csv" --dbhost=127.0.0.1 --dbuser=root --dbpass= --dbname=servicredit [--derive-parent]

set_time_limit(0);
ini_set('memory_limit','512M');

$options = getopt('', ['file:','dbhost:','dbuser:','dbpass:','dbname:','derive-parent','dry-run','limit:']);

$file = isset($options['file']) ? $options['file'] : __DIR__ . '/../uploads/cuentas contables.csv';
$dbhost = isset($options['dbhost']) ? $options['dbhost'] : '127.0.0.1';
$dbuser = isset($options['dbuser']) ? $options['dbuser'] : 'root';
$dbpass = isset($options['dbpass']) ? $options['dbpass'] : '';
$dbname = isset($options['dbname']) ? $options['dbname'] : 'servicredit';
$derive_parent = isset($options['derive-parent']);
$dry_run = isset($options['dry-run']);
$limit = isset($options['limit']) ? intval($options['limit']) : 50;

if (!file_exists($file)) {
    fwrite(STDERR, "File not found: $file\n");
    exit(1);
}

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if ($mysqli->connect_errno) {
    fwrite(STDERR, "DB connect error: " . $mysqli->connect_error . "\n");
    exit(1);
}
$mysqli->set_charset('utf8mb4');

$fh = fopen($file, 'r');
if (!$fh) {
    fwrite(STDERR, "Could not open file: $file\n");
    exit(1);
}

// read header
$header = fgetcsv($fh, 0, ';');
if ($header === false) {
    fwrite(STDERR, "Empty file or invalid CSV\n");
    exit(1);
}
// Normalize header keys
$cols = array_map(function($c){ return strtolower(trim($c)); }, $header);
$map = [];
foreach ($cols as $i => $col) {
    $map[$col] = $i;
}

// helper: get value by header name (case-insensitive)
function val($row, $map, $names) {
    foreach ((array)$names as $n) {
        $key = strtolower(trim($n));
        if (isset($map[$key]) && isset($row[$map[$key]])) return trim($row[$map[$key]]);
    }
    return null;
}

$inserted = 0; $updated = 0; $skipped = 0; $processed = 0;
$codes_imported = [];

// If dry-run requested, scan and print classification for a sample of rows and exit
if (isset($dry_run) && $dry_run) {
    echo "DRY RUN: showing first $limit rows classification (no DB changes)\n";
    $count = 0;
    while (($row = fgetcsv($fh, 0, ';')) !== false) {
        $processed++;
        // Prefer CUENTAMUC as the account code; fallback to CUENTACREDIBLAMEN if missing
        $code = val($row, $map, ['cuentamuc','cuentacrediblamen','code','cuenta','cuenta_id','cuenta']);
        // Prefer the MUC name; fallback to the original nombrecuenta
        $name = val($row, $map, ['nombredecuentamuc','nombrecuenta','name','nombre','descripcion']);
        if (!$code) { $skipped++; continue; }
        $code = preg_replace('/\s+/', '', $code);
        if ($code === '') { $skipped++; continue; }
        if (!$name) $name = $code;
        $first = substr($code,0,1);
        $type = 'otro';
        if ($first === '1') $type = 'activo';
        elseif ($first === '2') $type = 'pasivo';
        elseif ($first === '3') $type = 'patrimonio';
        elseif ($first === '4') $type = 'ingreso';
        elseif ($first === '5') $type = 'gasto';

        echo sprintf("%s | %-10s | %s\n", $code, $type, $name);
        $count++;
        if ($count >= $limit) break;
    }
    fclose($fh);
    echo "DRY RUN complete. Processed: $processed, Skipped: $skipped\n";
    exit(0);
}

$mysqli->begin_transaction();
try {
    // Determine target table and column names (handle corrupted/alternate schemas)
    $target_table = 'tb_account';
    $name_col = 'name';
    $type_col = 'type';
    $res = $mysqli->query("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = '" . $mysqli->real_escape_string($dbname) . "' AND TABLE_NAME IN ('tb_account','b_account','teso_accounts')");
    if ($res) {
        $found = [];
        while ($r = $res->fetch_assoc()) $found[] = $r['TABLE_NAME'];
        if (!in_array('tb_account', $found)) {
            // fallback to b_account if present
            if (in_array('b_account', $found)) {
                $target_table = 'b_account';
                // detect columns in b_account
                $cols = [];
                $cres = $mysqli->query("SHOW COLUMNS FROM b_account");
                if ($cres) {
                    while ($c = $cres->fetch_assoc()) $cols[] = $c['Field'];
                }
                if (in_array('ame', $cols)) $name_col = 'ame';
                // support several possible type column names
                if (in_array('type', $cols)) {
                    $type_col = 'type';
                } elseif (in_array('account_type', $cols)) {
                    $type_col = 'account_type';
                } elseif (in_array('ype', $cols)) {
                    $type_col = 'ype';
                }
            }
        }
    }

    // prepare statement using INSERT ... ON DUPLICATE KEY UPDATE for chosen target
    $sql = "INSERT INTO `".$target_table."` (`code`,`".$name_col."`,`".$type_col."`,`created_at`) VALUES (?,?,?,NOW()) ON DUPLICATE KEY UPDATE `".$name_col."`=VALUES(`".$name_col."`), `".$type_col."`=VALUES(`".$type_col."`)";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) throw new Exception('Prepare failed: ' . $mysqli->error . " -- SQL: " . $sql);

    while (($row = fgetcsv($fh, 0, ';')) !== false) {
        $processed++;
        // Prefer CUENTAMUC as the account code; fallback to CUENTACREDIBLAMEN if missing
        $code = val($row, $map, ['cuentamuc','cuentacrediblamen','code','cuenta','cuenta_id','cuenta']);
        // Prefer the MUC name; fallback to the original nombrecuenta
        $name = val($row, $map, ['nombredecuentamuc','nombrecuenta','name','nombre','descripcion']);
        if (!$code) { $skipped++; continue; }
        $code = preg_replace('/\s+/', '', $code);
        if ($code === '') { $skipped++; continue; }
        if (!$name) $name = $code;

        // determine type by first digit (fallback 'otro')
        $first = substr($code,0,1);
        $type = 'otro';
        if ($first === '1') $type = 'activo';
        elseif ($first === '2') $type = 'pasivo';
        elseif ($first === '3') $type = 'patrimonio';
        elseif ($first === '4') $type = 'ingreso';
        elseif ($first === '5') $type = 'gasto';

        // execute
        $stmt->bind_param('sss', $code, $name, $type);
        if (!$stmt->execute()) {
            // if duplicate key error or other, report and continue
            fwrite(STDERR, "Row $processed: DB error: " . $stmt->error . "\n");
            $skipped++;
            continue;
        }
        // affected_rows: 1 = inserted, 2 = updated (for ON DUPLICATE UPDATE MySQL returns 2 when update)
        $aff = $stmt->affected_rows;
        if ($aff == 1) $inserted++; elseif ($aff == 2) $updated++; else { /* 0 */ }
        $codes_imported[$code] = true;
    }

    $stmt->close();

    // derive parent_id if requested
    if ($derive_parent && !empty($codes_imported)) {
        echo "Deriving parent_id by longest-prefix algorithm...\n";
        // fetch code->id map after insert
        $res = $mysqli->query("SELECT id, code FROM `".$target_table."`");
        $code_map = [];
        while ($r = $res->fetch_assoc()) { $code_map[$r['code']] = (int)$r['id']; }
        // build sorted list of codes by length desc to ensure children processed after parents
        $all_codes = array_keys($code_map);
        usort($all_codes, function($a,$b){ return strlen($b) - strlen($a); });

        // for each imported code, find the longest existing code that is a strict prefix
        foreach (array_keys($codes_imported) as $c) {
            $parent_id = null;
            $best_len = 0;
            foreach ($all_codes as $candidate) {
                if ($candidate === $c) continue;
                if (strpos($c, $candidate) === 0) {
                    $len = strlen($candidate);
                    if ($len > $best_len) { $best_len = $len; $parent_id = $code_map[$candidate]; }
                }
            }
            if ($parent_id) {
                $upd = $mysqli->prepare("UPDATE `".$target_table."` SET parent_id = ? WHERE code = ?");
                $upd->bind_param('is', $parent_id, $c);
                $upd->execute();
                $upd->close();
            }
        }
    }

    $mysqli->commit();
} catch (Exception $e) {
    $mysqli->rollback();
    fwrite(STDERR, "Import failed: " . $e->getMessage() . "\n");
    fclose($fh);
    $mysqli->close();
    exit(1);
}

fclose($fh);
$mysqli->close();

echo "Import finished. Processed: $processed, Inserted: $inserted, Updated: $updated, Skipped: $skipped\n";
if ($derive_parent) echo "Parent derivation completed.\n";

exit(0);
