<?php
$path = __DIR__ . '/../application/controllers/Solicitudes.php';
if (!file_exists($path)) {
    echo "File not found: $path\n";
    exit(1);
}
$code = file_get_contents($path);
$lines = explode("\n", $code);
$balance = 0;
$firstNegative = null;
for ($i = 0; $i < count($lines); $i++) {
    $line = $lines[$i];
    $open = substr_count($line, '{');
    $close = substr_count($line, '}');
    $balance += $open - $close;
    if ($firstNegative === null && $balance < 0) {
        $firstNegative = $i + 1;
        break;
    }
}
if ($firstNegative) {
    echo "First line where brace balance < 0: $firstNegative\n\n";
    $start = max(1, $firstNegative - 10);
    $end = min(count($lines), $firstNegative + 20);
    for ($ln = $start; $ln <= $end; $ln++) {
        printf("%4d: %s\n", $ln, rtrim($lines[$ln-1]));
    }
} else {
    echo "No negative balance found. Final balance: $balance\n";
}

