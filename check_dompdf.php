<?php
// Simple check for Dompdf availability
echo "CHECK_VENDOR: ";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
    echo class_exists('\\Dompdf\\Dompdf') ? "YES\n" : "NO\n";
} else {
    echo "NO_VENDOR\n";
}

echo "CHECK_BUNDLED: ";
if (file_exists(__DIR__ . '/dompdf/autoload.inc.php')) {
    require __DIR__ . '/dompdf/autoload.inc.php';
    echo class_exists('\\Dompdf\\Dompdf') ? "YES\n" : "NO\n";
} else {
    echo "NO_BUNDLED\n";
}

// Print PHP version and loaded files for debugging
echo "PHP: " . PHP_BINARY . " (" . PHP_VERSION . ")\n";
echo "Loaded files count: " . count(get_included_files()) . "\n";
?>