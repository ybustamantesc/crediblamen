<!DOCTYPE html>
<html>
<head>
    <title>Ver Log de Garantías en Tiempo Real</title>
    <meta http-equiv="refresh" content="2">
    <style>
        body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
        h2 { color: #4ec9b0; }
        pre { background: #2d2d2d; padding: 15px; border-radius: 5px; overflow: auto; max-height: 80vh; }
        .checkpoint { color: #4ec9b0; font-weight: bold; }
        .exception { color: #f48771; font-weight: bold; }
        .debug { color: #569cd6; }
    </style>
</head>
<body>
    <h2>Log de Garantías - Últimas 200 líneas (Auto-refresh cada 2 segundos)</h2>
    <p style="color: #808080;">Archivo: application/logs/garantias_save_debug.log</p>
    <pre><?php
    $log_file = __DIR__ . '/application/logs/garantias_save_debug.log';
    if (file_exists($log_file)) {
        $lines = file($log_file);
        $last_lines = array_slice($lines, -200);
        $output = implode('', $last_lines);
        // Colorear líneas importantes
        $output = preg_replace('/(CHECKPOINT \d+:.*)/m', '<span class="checkpoint">$1</span>', $output);
        $output = preg_replace('/(EXCEPTION.*)/m', '<span class="exception">$1</span>', $output);
        $output = preg_replace('/(===.*===)/m', '<span class="debug">$1</span>', $output);
        echo htmlspecialchars_decode($output);
    } else {
        echo "El archivo de log no existe.";
    }
    ?></pre>
    <p style="color: #808080; font-size: 12px;">Última actualización: <?php echo date('Y-m-d H:i:s'); ?></p>
</body>
</html>
