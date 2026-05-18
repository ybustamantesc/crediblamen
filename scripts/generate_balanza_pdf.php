<?php
// CLI helper to generate a Balanza PDF in background.
// Usage: php generate_balanza_pdf.php "http://localhost/servicredit/contabilidad/balanza_print?start_date=...&end_date=..." /full/path/to/project/uploads/reports/output.pdf

if (php_sapi_name() !== 'cli') {
    echo "This script is intended to be run from CLI.\n";
    exit(1);
}

$argc = $_SERVER['argc'];
$argv = $_SERVER['argv'];
if ($argc < 3) {
    echo "Usage: php generate_balanza_pdf.php <print_url> <output_pdf_path>\n";
    exit(1);
}

$url = $argv[1];
$outPath = $argv[2];

// fetch the printable HTML
$opts = array('http' => array('method' => 'GET', 'timeout' => 30));
$context = stream_context_create($opts);
$html = @file_get_contents($url, false, $context);
if ($html === false) {
    file_put_contents($outPath . '.error.txt', "Failed to fetch URL: $url\n");
    exit(2);
}

// Double-pass flow:
// 1) Render HTML -> PDF bytes
// 2) Compute MD5 hash of bytes
// 3) Replace placeholder {hash} in HTML with computed hash
// 4) Re-render HTML -> final PDF with embedded hash

// try to include dompdf from project root dompdf/autoload.inc.php or vendor/autoload.php
$root = dirname(__DIR__);
if (file_exists($root . DIRECTORY_SEPARATOR . 'dompdf' . DIRECTORY_SEPARATOR . 'autoload.inc.php')) {
    require_once $root . DIRECTORY_SEPARATOR . 'dompdf' . DIRECTORY_SEPARATOR . 'autoload.inc.php';
} elseif (file_exists($root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) {
    require_once $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
} else {
    file_put_contents($outPath . '.error.txt', "Dompdf not found in project root (dompdf/ or vendor/).\n");
    exit(3);
}

try {
    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->setPaper('A4', 'landscape');

    // first pass
    $dompdf->loadHtml($html);
    $dompdf->render();
    $firstPdf = $dompdf->output();
    $hash = md5($firstPdf);

    // replace placeholder {hash} in HTML and re-render
    $htmlWithHash = str_replace('{hash}', $hash, $html);
    // re-instantiate to avoid state carry
    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->loadHtml($htmlWithHash);
    $dompdf->render();
    $finalPdf = $dompdf->output();
    file_put_contents($outPath, $finalPdf);
    // touch a .done marker and a .hash file
    file_put_contents($outPath . '.done', date('c'));
    file_put_contents($outPath . '.hash', $hash);
    exit(0);
} catch (Exception $e) {
    file_put_contents($outPath . '.error.txt', "Exception: " . $e->getMessage() . "\n");
    exit(4);
}
