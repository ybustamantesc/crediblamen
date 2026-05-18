<?php
// Test simple de upload
header('Content-Type: application/json');

echo json_encode([
    'php_version' => phpversion(),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'max_execution_time' => ini_get('max_execution_time'),
    'FILES' => $_FILES,
    'POST' => $_POST,
    'tmp_dir' => sys_get_temp_dir(),
    'tmp_writable' => is_writable(sys_get_temp_dir())
], JSON_PRETTY_PRINT);
