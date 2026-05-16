<?php
// show_accounts_imported.php
header('Content-Type: application/json');
$mysqli = new mysqli('localhost','root','','minitas');
if ($mysqli->connect_error) {
    echo json_encode(['status'=>'error','message'=>$mysqli->connect_error]);
    exit(1);
}
$mysqli->set_charset('utf8mb4');

$countRes = $mysqli->query("SELECT COUNT(*) AS c FROM tb_account");
$count = $countRes->fetch_assoc()['c'];

$samples = [];
$res = $mysqli->query("SELECT id, code, name, type, `level`, parent_id, postable FROM tb_account ORDER BY code LIMIT 20");
while ($r = $res->fetch_assoc()) {
    $samples[] = $r;
}
$mysqli->close();

echo json_encode(['status'=>'success','count'=>(int)$count,'samples'=>$samples], JSON_PRETTY_PRINT);
