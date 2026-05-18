<?php
// Test de POST sin archivo
error_reporting(E_ALL);
ini_set('display_errors', 1);

while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json');

try {
    $response = [
        'status' => 'success',
        'method' => $_SERVER['REQUEST_METHOD'],
        'post_data' => $_POST,
        'files_count' => count($_FILES),
        'has_balanza_file' => isset($_FILES['balanzaFile']),
        'time' => date('H:i:s')
    ];
    
    if (isset($_FILES['balanzaFile'])) {
        $response['file_info'] = [
            'name' => $_FILES['balanzaFile']['name'],
            'size' => $_FILES['balanzaFile']['size'],
            'error' => $_FILES['balanzaFile']['error'],
            'tmp_name' => $_FILES['balanzaFile']['tmp_name']
        ];
    }
    
    echo json_encode($response, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
