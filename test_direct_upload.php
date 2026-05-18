<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
while (ob_get_level()) ob_end_clean();
header('Content-Type: application/json');
$response = ['status' => 'success'];
if (isset($_FILES['balanzaFile'])) {
    $f = $_FILES['balanzaFile'];
    $response['file'] = ['name' => $f['name'], 'size' => $f['size'], 'error' => $f['error']];
    if ($f['error'] === 0 && file_exists($f['tmp_name'])) {
        $lines = file($f['tmp_name'], FILE_IGNORE_NEW_LINES);
        $response['file']['lines'] = count($lines);
        $response['file']['first_3'] = array_slice($lines, 0, 3);
    }
}
echo json_encode($response);