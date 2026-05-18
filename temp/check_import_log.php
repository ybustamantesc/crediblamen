<?php
$mysqli = new mysqli('localhost', 'root', '', 'crediblamen');
if ($mysqli->connect_error) {
    http_response_code(500);
    echo "ERROR: " . $mysqli->connect_error;
    exit;
}
$res = $mysqli->query("SELECT csv_file, stg_rows, stg_prestamos, imported_prestamos, created_at FROM import_log ORDER BY id DESC LIMIT 1");
if ($res && $row = $res->fetch_assoc()) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "csv=" . $row['csv_file'] . "\n";
    echo "stg_rows=" . $row['stg_rows'] . "\n";
    echo "stg_prestamos=" . $row['stg_prestamos'] . "\n";
    echo "imported_prestamos=" . $row['imported_prestamos'] . "\n";
    echo "created_at=" . $row['created_at'] . "\n";
} else {
    echo "No hay registros en import_log";
}
$mysqli->close();
