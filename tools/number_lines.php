<?php
$path = __DIR__ . '/../application/controllers/Solicitudes.php';
$lines = file($path);
foreach ($lines as $i => $line) {
    printf("%04d: %s", $i+1, $line);
}
?>