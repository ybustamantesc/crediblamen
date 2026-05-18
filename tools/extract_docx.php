<?php
if ($argc < 2) {
    echo "Usage: php extract_docx.php <path-to-docx>\n";
    exit(1);
}
$path = $argv[1];
if (!file_exists($path)) {
    echo "File not found: $path\n";
    exit(2);
}
$zip = new ZipArchive;
if ($zip->open($path) === TRUE) {
    $index = $zip->locateName('word/document.xml');
    if ($index === false) {
        echo "document.xml not found in docx\n";
        $zip->close();
        exit(3);
    }
    $xml = $zip->getFromIndex($index);
    $zip->close();
    // load XML and extract all text nodes
    libxml_use_internal_errors(true);
    $doc = new DOMDocument();
    $doc->loadXML($xml);
    $xpath = new DOMXPath($doc);
    // register namespaces commonly used
    $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
    $nodes = $xpath->query('//w:t');
    $out = array();
    foreach ($nodes as $n) {
        $text = trim($n->textContent);
        if ($text !== '') $out[] = $text;
    }
    // collapse consecutive duplicates and print lines
    $prev = null;
    foreach ($out as $line) {
        if ($line === $prev) continue;
        echo $line . "\n";
        $prev = $line;
    }
    exit(0);
} else {
    echo "Unable to open docx as zip\n";
    exit(4);
}
