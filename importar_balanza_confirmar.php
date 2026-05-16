<?php
error_reporting(0);
ini_set('display_errors', 0);
while (@ob_get_level()) @ob_end_clean();
header('Content-Type: application/json');
try {
    $conn = new mysqli('localhost', 'root', '', 'u987557742_testsystem');
    if ($conn->connect_error) throw new Exception('DB Error');
    $conn->set_charset('utf8');
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    if (!$data || !isset($data['cuentas']) || !isset($data['asiento'])) throw new Exception('Bad data');
    $conn->begin_transaction();
    try {
        $ids = [];
        $stc = $conn->prepare("SELECT id FROM tb_account WHERE code = ?");
        $sti = $conn->prepare("INSERT INTO tb_account (code, name, type, created_at) VALUES (?, ?, ?, NOW())");
        foreach ($data['cuentas'] as $c) {
            $stc->bind_param('s', $c['code']);
            $stc->execute();
            $res = $stc->get_result();
            if ($row = $res->fetch_assoc()) {
                $ids[$c['code']] = $row['id'];
            } else {
                $sti->bind_param('sss', $c['code'], $c['name'], $c['type']);
                $sti->execute();
                $ids[$c['code']] = $conn->insert_id;
            }
        }
        $stc->close();
        $sti->close();
        $a = $data['asiento'];
        $stj = $conn->prepare("INSERT INTO tb_journal (date, description, period_month, period_year, entry_type, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stj->bind_param('ssiss', $a['fecha'], $a['descripcion'], $a['periodo_mes'], $a['periodo_anio'], $a['tipo']);
        $stj->execute();
        $jid = $conn->insert_id;
        $stj->close();
        $ste = $conn->prepare("INSERT INTO tb_journal_entry (journal_id, account_id, debit, credit, description) VALUES (?, ?, ?, ?, ?)");
        foreach ($data['cuentas'] as $c) {
            if ($c['debe'] > 0 || $c['haber'] > 0) {
                $aid = $ids[$c['code']];
                $ste->bind_param('iidds', $jid, $aid, $c['debe'], $c['haber'], $c['name']);
                $ste->execute();
            }
        }
        $ste->close();
        $conn->commit();
        echo json_encode(['status'=>'success','message'=>'Balanza importada','cuentas_creadas'=>count($ids),'journal_id'=>$jid]);
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
