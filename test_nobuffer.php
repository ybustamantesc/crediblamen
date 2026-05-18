<?php
// Deshabilitar CUALQUIER buffering
while (ob_get_level()) {
    ob_end_clean();
}

header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Intentar escribir inmediatamente
echo json_encode(['status' => 'success', 'message' => 'Test inmediato sin buffering', 'time' => date('H:i:s')]);
flush();
exit();
