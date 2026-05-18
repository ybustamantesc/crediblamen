<?php
// show_muc_structure.php
header('Content-Type: application/json');
$mysqli = new mysqli('localhost','root','','minitas');
if ($mysqli->connect_error) { echo json_encode(['status'=>'error','message'=>$mysqli->connect_error]); exit(1); }
$mysqli->set_charset('utf8mb4');

// Distinct classes
$classes = [];
$res = $mysqli->query("SELECT muc_class, COUNT(*) AS cnt FROM tb_account GROUP BY muc_class ORDER BY muc_class");
while ($r = $res->fetch_assoc()) $classes[] = $r;

// For each class, list groups and sample accounts
$structure = [];
$res2 = $mysqli->query("SELECT DISTINCT muc_class FROM tb_account ORDER BY muc_class");
while ($r2 = $res2->fetch_assoc()) {
    $mc = $r2['muc_class'];
    $groups = [];
    $qr = $mysqli->query($mysqli->real_escape_string("SELECT muc_group, COUNT(*) AS cnt FROM tb_account WHERE muc_class = '".$mc."' GROUP BY muc_group ORDER BY muc_group"));
    if ($qr) {
        while ($g = $qr->fetch_assoc()) {
            $mg = $g['muc_group'];
            $sample = [];
            $s = $mysqli->query("SELECT code, name FROM tb_account WHERE muc_class = '".$mc."' AND muc_group = '".$mg."' ORDER BY code LIMIT 5");
            if ($s) while ($row = $s->fetch_assoc()) $sample[] = $row;
            $groups[] = ['muc_group'=>$mg,'count'=>$g['cnt'],'sample'=>$sample];
        }
    }
    $structure[] = ['muc_class'=>$mc,'groups'=>$groups];
}

// Also list counts for statement mapping
$stm = [];
$res3 = $mysqli->query("SELECT statement, COUNT(*) AS cnt FROM tb_account GROUP BY statement");
while ($r3 = $res3->fetch_assoc()) $stm[] = $r3;

$mysqli->close();

echo json_encode(['status'=>'success','classes'=>$classes,'structure'=>$structure,'statement_counts'=>$stm], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
