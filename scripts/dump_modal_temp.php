<?php
$tmp = getenv('TEMP') . DIRECTORY_SEPARATOR . 'modal7.html';
if (!file_exists($tmp)) { echo "Temp file not found: $tmp\n"; exit(1); }
$s = file_get_contents($tmp);
$u = @mb_convert_encoding($s, 'UTF-8', 'UTF-16LE');
if ($u === false) { echo "Encoding conversion failed\n"; exit(1); }
$needle = 'name="report_bs"';
$pos = strpos($u, $needle);
if ($pos === false) {
	echo "report_bs select not found in modal HTML\n";
	exit(0);
}
echo "--- report_bs select context ---\n";
echo substr($u, max(0,$pos-200), 1200);
echo "\n--- End ---\n";
?>
