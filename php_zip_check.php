<?php
// Quick web-check for Zip extension and basic phpinfo
header('Content-Type: text/plain');
echo "zip enabled: ";
var_export(extension_loaded('zip'));
echo "\n\nPHP Version: " . PHP_VERSION . "\n";
echo "Loaded php.ini: " . php_ini_loaded_file() . "\n\n";
// Also include minimal phpinfo if requested
if (isset($_GET['full'])) {
    echo "\n--- phpinfo ---\n";
    ob_start();
    phpinfo();
    $p = ob_get_clean();
    // strip tags for plaintext
    echo strip_tags($p);
}
