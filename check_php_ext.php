<?php
header('Content-Type: application/json');
$data = [
    'php_sapi' => PHP_SAPI,
    'gd' => extension_loaded('gd'),
    'gd_version' => function_exists('gd_info') ? gd_info() : null,
    'imagick' => extension_loaded('imagick'),
    'allow_url_fopen' => ini_get('allow_url_fopen'),
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time')
];
echo json_encode($data, JSON_PRETTY_PRINT);
?>