<?php
$path = __DIR__ . '/../application/controllers/Solicitudes.php';
$code = file_get_contents($path);
$tokens = token_get_all($code);
$line = 400;
foreach ($tokens as $tok) {
    if (is_array($tok)) {
        $name = token_name($tok[0]);
        $text = str_replace("\n", "\\n", $tok[1]);
        $ln = $tok[2];
        if ($ln >= $line-2 && $ln <= $line+2) {
            printf("%04d: %s => %s\n", $ln, $name, $text);
        }
    } else {
        // single-char token
        // approximate line number by counting newlines in previous tokens
    }
}
?>