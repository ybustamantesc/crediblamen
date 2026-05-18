<?php
$patterns = array('ADMINISTRADOR','Fecha Vencimiento','ESTADO DE CUENTA','2026-02-02');
$files = glob(__DIR__ . '/estado_cuenta*.pdf');
foreach ($files as $f) {
    $size = filesize($f);
    $data = file_get_contents($f);
    echo "File: " . basename($f) . " (" . $size . " bytes)\n";
    foreach ($patterns as $p) {
        $found = strpos($data, $p) !== false ? 'YES' : 'NO';
        echo "  contains '$p'? " . $found . "\n";
    }
    echo "\n";
}

?>
