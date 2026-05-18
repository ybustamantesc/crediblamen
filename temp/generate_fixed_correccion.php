<?php
// Generates a corrected SQL script to adjust accounts to desired Saldo Correcto
$csv = __DIR__ . DIRECTORY_SEPARATOR . 'correccion_asientos_cierre2025.csv';
$out = __DIR__ . DIRECTORY_SEPARATOR . 'correccion_asiento_cierre2025_fixed.sql';
$fecha = '2025-12-31';
$description = "Ajuste cierre importacion 2025 - fix";
$lines = [];
if (!file_exists($csv)) { echo "CSV not found: $csv\n"; exit(1); }
$h = fopen($csv,'r');
$hdr = fgetcsv($h);
while (($r = fgetcsv($h)) !== false) {
    if (count($r) < 4) continue;
    $code = trim($r[0]);
    $name = trim($r[1]);
    $saldo_correcto = trim($r[3]);
    // normalize number: remove apostrophes and spaces, replace comma with dot
    $s = str_replace("'", '', $saldo_correcto);
    $s = str_replace(' ', '', $s);
    $s = str_replace(',', '.', $s);
    if ($s === '') $s = '0';
    // ensure numeric format
    if (!is_numeric($s)) {
        // try removing non-numeric
        $s = preg_replace('/[^0-9.\-]/','',$s);
        if ($s === '') $s = '0';
    }
    $lines[] = ['code'=>$code,'desired'=>$s,'name'=>$name];
}
fclose($h);
// build SQL
$sql = "-- Fixed adjustment journal to set accounts to Saldo Correcto\n";
$sql .= "START TRANSACTION;\n";
$sql .= "-- Ensure adjust account 9999 exists\n";
$sql .= "INSERT INTO tb_account (`code`,`name`,`postable`,`type`,`naturaleza`,`created_at`) SELECT '9999','AJUSTE IMPORTACION',1,'MISC','acreedora',NOW() FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM tb_account WHERE code = '9999');\n\n";
$sql .= "INSERT INTO tb_journal (`date`,`description`,`posted`,`posted_at`,`created_at`,`period_month`,`period_year`) VALUES ('{$fecha}','{$description}',1,NOW(),NOW(),12,2025);\nSET @journal_id = LAST_INSERT_ID();\n\n";
foreach ($lines as $i => $ln) {
    $code = $ln['code'];
    $desired = $ln['desired'];
    $sql .= "-- Account {$code}\n";
    $sql .= "SELECT id INTO @a_id_{$i} FROM tb_account WHERE code = '{$code}' LIMIT 1;\n";
    $sql .= "SELECT IFNULL(SUM(e.debit - e.credit),0) INTO @curr_raw_{$i} FROM tb_journal_entry e JOIN tb_journal j ON j.id = e.journal_id WHERE e.account_id = @a_id_{$i} AND j.posted = 1 AND (j.voided IS NULL OR j.voided = 0) AND j.date <= '{$fecha}';\n";
    $sql .= "SELECT CASE WHEN (SELECT type FROM tb_account WHERE id = @a_id_{$i}) IN ('pasivo','patrimonio','ingreso') THEN -1 ELSE 1 END INTO @factor_{$i};\n";
    $sql .= "SET @curr_display_{$i} = ROUND(@curr_raw_{$i} * @factor_{$i},2);\n";
    $sql .= "SET @desired_display_{$i} = ROUND({$desired},2);\n";
    $sql .= "SET @diff_display_{$i} = ROUND(@desired_display_{$i} - @curr_display_{$i},2);\n";
    $sql .= "SET @raw_diff_{$i} = ROUND((@diff_display_{$i} / @factor_{$i}),2);\n";
    $sql .= "-- Insert line if needed\n";
    $sql .= "INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id)\n";
    $sql .= "SELECT @journal_id, @a_id_{$i}, CASE WHEN @raw_diff_{$i} > 0 THEN @raw_diff_{$i} ELSE 0 END, CASE WHEN @raw_diff_{$i} < 0 THEN ABS(@raw_diff_{$i}) ELSE 0 END, 'Ajuste cierre importacion 2025 - fixed', 1 FROM DUAL WHERE @a_id_{$i} IS NOT NULL AND @raw_diff_{$i} != 0;\n\n";
}
$sql .= "-- Compute totals and insert balancing entry to 9999\n";
$sql .= "SET @totdeb = (SELECT COALESCE(SUM(debit),0) FROM tb_journal_entry WHERE journal_id = @journal_id);\n";
$sql .= "SET @totcre = (SELECT COALESCE(SUM(credit),0) FROM tb_journal_entry WHERE journal_id = @journal_id);\n";
$sql .= "SET @diff = ROUND(@totdeb - @totcre,2);\n";
$sql .= "SELECT @totdeb AS total_debit, @totcre AS total_credit, @diff AS diff;\n";
$sql .= "SELECT id INTO @adjust_id FROM tb_account WHERE code = '9999' LIMIT 1;\n";
$sql .= "INSERT INTO tb_journal_entry (journal_id,account_id,debit,credit,description,centro_costo_id) VALUES (@journal_id, @adjust_id, CASE WHEN @diff < 0 THEN -@diff ELSE 0 END, CASE WHEN @diff > 0 THEN @diff ELSE 0 END, 'Ajuste cierre importacion 2025 - fixed',1);\n\n";
$sql .= "UPDATE tb_journal SET total_debit = (SELECT COALESCE(SUM(debit),0) FROM tb_journal_entry WHERE journal_id = @journal_id), total_credit = (SELECT COALESCE(SUM(credit),0) FROM tb_journal_entry WHERE journal_id = @journal_id) WHERE id = @journal_id;\n\n";
$sql .= "COMMIT;\n";
file_put_contents($out, $sql);
echo "Generated $out with " . count($lines) . " accounts\n";
