<?php
error_reporting(0);
ini_set('display_errors', 0);
while (@ob_get_level()) @ob_end_clean();
header('Content-Type: application/json');
try {
    $conn = new mysqli('localhost', 'root', '', 'u987557742_testsystem');
    if ($conn->connect_error) throw new Exception('DB Error');
    if (!isset($_FILES['balanzaFile'])) throw new Exception('No file');
    $f = $_FILES['balanzaFile'];
    if ($f['error'] !== 0) throw new Exception('Upload error: ' . $f['error']);
    $mes = $_POST['periodoMes'] ?? null;
    $anio = $_POST['periodoAnio'] ?? null;
    $tipo = $_POST['tipoImportacion'] ?? 'apertura';
    if (!$mes || !$anio) throw new Exception('Missing period');
    $h = fopen($f['tmp_name'], 'r');
    fgetcsv($h);
    $cuentas = [];
    $tdebe = 0;
    $thaber = 0;
    while (($r = fgetcsv($h)) !== false) {
        if (count($r) < 6) continue;
        $cod = trim($r[0]);
        $nom = trim($r[1]);
        if (empty($cod)) continue;
        $debe = floatval(str_replace([',', '"', ' '], '', $r[3]));
        $haber = floatval(str_replace([',', '"', ' '], '', $r[4]));
        $saldo = floatval(str_replace([',', '"', ' '], '', $r[5]));
        $d1 = substr($cod, 0, 1);
        if ($d1 === '1') $tipo_cuenta = 'activo';
        elseif ($d1 === '2') $tipo_cuenta = 'pasivo';
        elseif ($d1 === '3') $tipo_cuenta = 'patrimonio';
        elseif ($d1 === '4') $tipo_cuenta = 'ingreso';
        elseif ($d1 === '5') $tipo_cuenta = 'gasto';
        else $tipo_cuenta = 'activo';
        $cuentas[] = ['code'=>$cod,'name'=>$nom,'type'=>$tipo_cuenta,'saldo'=>$saldo,'debe'=>$debe,'haber'=>$haber];
        $tdebe += $debe;
        $thaber += $haber;
    }
    fclose($h);
    $fecha = date('Y-m-t', strtotime("$anio-$mes-01"));
    $meses = ['01'=>'Enero','02'=>'Febrero','03'=>'Marzo','04'=>'Abril','05'=>'Mayo','06'=>'Junio','07'=>'Julio','08'=>'Agosto','09'=>'Septiembre','10'=>'Octubre','11'=>'Noviembre','12'=>'Diciembre'];
    echo json_encode(['status'=>'success','data'=>['cuentas'=>$cuentas,'total_cuentas'=>count($cuentas),'total_debe'=>round($tdebe,2),'total_haber'=>round($thaber,2),'cuadra'=>abs($tdebe-$thaber)<0.01,'periodo'=>['mes'=>$mes,'anio'=>$anio,'mes_nombre'=>$meses[$mes],'tipo'=>$tipo,'fecha_asiento'=>$fecha]]]);
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
