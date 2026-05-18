<?php
$path = __DIR__ . '/../application/controllers/Solicitudes.php';
if (!file_exists($path)) {
    echo "File not found: $path\n";
    exit(1);
}
$code = file_get_contents($path);
$lines = explode("\n", $code);
$tokens = token_get_all($code);

echo "--- Token dump (lines 380-420) ---\n";
foreach ($tokens as $tok) {
    if (is_array($tok)) {
        $ln = $tok[2];
        if ($ln >= 380 && $ln <= 420) {
            printf("%4d: %s => %s\n", $ln, token_name($tok[0]), str_replace("\n","\\n", $tok[1]));
        }
    }
}

echo "\n--- File excerpt (lines 370-430) ---\n";
for ($i = 369; $i <= 430; $i++) {
    if (!isset($lines[$i-1])) break;
    $ln = $i;
    printf("%4d: %s\n", $ln, rtrim($lines[$i-1]));
}

// Brace balance per line
echo "\n--- Brace balance per line (showing cumulative count) ---\n";
$balance = 0;
for ($i = 0; $i < count($lines); $i++) {
    $line = $lines[$i];
    $open = substr_count($line, '{');
    $close = substr_count($line, '}');
    $balance += $open - $close;
    if ($i+1 >= 360 && $i+1 <= 440) {
        printf("%4d: balance=%3d  (+%d/-%d)  %s\n", $i+1, $balance, $open, $close, trim($line));
    }
}

echo "\nFinal brace balance: $balance\n";



