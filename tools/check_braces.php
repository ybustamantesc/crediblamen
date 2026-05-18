<?php
$path = __DIR__ . '/../application/controllers/Solicitudes.php';
$lines = file($path);
$bal = 0;
for ($i=0; $i<count($lines); $i++) {
    $line = $lines[$i];
    $bal += substr_count($line, '{') - substr_count($line, '}');
    if ($i > 66 && $bal == 1) {
        echo "FUNC_CORE_END_LINE=" . ($i+1) . "\n";
        break;
    }
}
if ($bal > 0) echo "All ok, final BAL=$bal\n";
?>