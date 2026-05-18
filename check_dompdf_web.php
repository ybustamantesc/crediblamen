<?php
// Simple web check for Dompdf autoload availability
echo "PHP SAPI: " . PHP_SAPI . "\n";
$vendor = __DIR__ . '/vendor/autoload.php';
$bundled = __DIR__ . '/dompdf/autoload.inc.php';
$third = __DIR__ . '/application/third_party/dompdf/autoload.inc.php';
echo "VENDOR_EXISTS:" . (file_exists($vendor) ? 'YES' : 'NO') . "\n";
echo "BUNDLED_EXISTS:" . (file_exists($bundled) ? 'YES' : 'NO') . "\n";
echo "THIRD_EXISTS:" . (file_exists($third) ? 'YES' : 'NO') . "\n";
if (file_exists($vendor)) {
    require_once $vendor;
    echo "REQUIRE_VENDOR: done\n";
}
if (! class_exists('\\Dompdf\\Dompdf')) {
    if (file_exists($bundled)) {
        require_once $bundled;
        echo "REQUIRE_BUNDLED: done\n";
    } elseif (file_exists($third)) {
        require_once $third;
        echo "REQUIRE_THIRD: done\n";
    }
}
echo "CLASS_EXISTS_AFTER_REQUIRE:" . (class_exists('\\Dompdf\\Dompdf') ? 'YES' : 'NO') . "\n";
?>