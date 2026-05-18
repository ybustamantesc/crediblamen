<?php
$path = __DIR__ . '/../application/controllers/Solicitudes.php';
$code = file_get_contents($path);
$open = substr_count($code, '{');
$close = substr_count($code, '}');
echo "{ count: $open\n";
echo "} count: $close\n";
echo "difference (open-close): " . ($open - $close) . "\n";
