<?php
// Worker script for processing a single report job.
// Usage: php pdf_worker.php <job_id> <print_url> <out_pdf_full_path>
if (php_sapi_name() !== 'cli') {
    echo "This script is intended to be run from CLI.\n";
    exit(1);
}
$argv = $_SERVER['argv'];
if (!isset($argv[1]) || !isset($argv[2]) || !isset($argv[3])) {
    echo "Usage: php pdf_worker.php <job_id> <print_url> <out_pdf_full_path>\n";
    exit(1);
}
$job = $argv[1];
$print_url = $argv[2];
$outPath = $argv[3];
$root = dirname(__DIR__);
$generateScript = $root . DIRECTORY_SEPARATOR . 'scripts' . DIRECTORY_SEPARATOR . 'generate_balanza_pdf.php';
$php = PHP_BINDIR . DIRECTORY_SEPARATOR . 'php';
// report 'processing' to controller
function report_status($job, $status, $file_hash = null, $error_text = null) {
    $reportUrl = 'http://localhost/servicredit/index.php/contabilidad/balanza_pdf_worker_report';
    $data = ['job' => $job, 'status' => $status];
    if ($file_hash) $data['file_hash'] = $file_hash;
    if ($error_text) $data['error_text'] = $error_text;
    $opts = ['http' => ['method' => 'POST', 'header' => "Content-Type: application/x-www-form-urlencoded\r\n", 'content' => http_build_query($data), 'timeout' => 30]];
    @file_get_contents($reportUrl, false, stream_context_create($opts));
}
// mark processing
report_status($job, 'processing');
// run generator
$cmd = '"' . $php . '" "' . $generateScript . '" "' . $print_url . '" "' . $outPath . '"';
exec($cmd . ' 2>&1', $output, $rc);
if ($rc === 0 && file_exists($outPath)) {
    $hash = md5_file($outPath);
    report_status($job, 'done', $hash, null);
    // touch done marker for backward compatibility
    @file_put_contents($outPath . '.done', date('c'));
    exit(0);
} else {
    $err = implode("\n", $output);
    report_status($job, 'error', null, $err);
    @file_put_contents($outPath . '.error.txt', $err);
    exit(2);
}
