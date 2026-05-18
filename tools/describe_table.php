<?php
// tools/describe_table.php
// Usage: php tools\describe_table.php [table_name]
// If no table_name given, the script will prompt and default to 'catalogo_cuentas'.

$configPath = __DIR__ . '/../application/config/database.php';
if (!file_exists($configPath)) {
    fwrite(STDERR, "Cannot find database config at $configPath\n");
    exit(2);
}

// Allow requiring the CodeIgniter config file even when run standalone.
if (!defined('BASEPATH')) {
    define('BASEPATH', true);
}
/** @noinspection PhpIncludeInspection */
require $configPath;

if (!isset($db) || !isset($db['default'])) {
    fwrite(STDERR, "Database config structure not found in $configPath\n");
    exit(3);
}

$conf = $db['default'];
$host = $conf['hostname'] ?? '127.0.0.1';
$user = $conf['username'] ?? 'root';
$pass = $conf['password'] ?? '';
$database = $conf['database'] ?? '';
$charset = $conf['char_set'] ?? 'utf8mb4';

$table = $argv[1] ?? null;

echo "Using DB: $database @ $host as $user\n";
try {
    $pdo = new PDO("mysql:host={$host};dbname={$database};charset={$charset}", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
} catch (PDOException $e) {
    fwrite(STDERR, "Connection failed: " . $e->getMessage() . "\n");
    exit(4);
}

if ($table === null || in_array(strtolower($table), ['list','tables','ls'])) {
    echo "Listing tables in database...\n\n";
    $stmt = $pdo->query('SHOW TABLES');
    $rows = $stmt->fetchAll(PDO::FETCH_NUM);
    if (empty($rows)) {
        echo "No tables found.\n";
    } else {
        foreach ($rows as $r) {
            echo $r[0] . "\n";
        }
    }
    exit(0);
}

echo "Describing table: $table\n\n";

// SHOW FULL COLUMNS
$stmt = $pdo->prepare("SHOW FULL COLUMNS FROM `" . str_replace('`','``',$table) . "`;");
try {
    $stmt->execute();
    $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if (empty($cols)) {
        echo "No columns returned (table may not exist).\n";
    } else {
        $fmt = "%-30s %-20s %-10s %-8s %-8s %-20s %s\n";
        printf($fmt, 'Field', 'Type', 'Null', 'Key', 'Default', 'Collation', 'Extra');
        printf($fmt, str_repeat('-',30), str_repeat('-',20), str_repeat('-',10), str_repeat('-',8), str_repeat('-',8), str_repeat('-',20), '-----');
        foreach ($cols as $c) {
            printf($fmt,
                $c['Field'],
                $c['Type'],
                $c['Null'],
                $c['Key'],
                $c['Default'] === null ? 'NULL' : $c['Default'],
                $c['Collation'] ?? '',
                $c['Extra'] ?? ''
            );
        }
    }
} catch (Exception $e) {
    fwrite(STDERR, "SHOW FULL COLUMNS failed: " . $e->getMessage() . "\n");
}

echo "\nSHOW CREATE TABLE $table:\n\n";
try {
    $stmt2 = $pdo->query("SHOW CREATE TABLE `" . str_replace('`','``',$table) . "`;");
    $row = $stmt2->fetch(PDO::FETCH_ASSOC);
    if ($row && isset($row['Create Table'])) {
        echo $row['Create Table'] . "\n";
    } else {
        // some MySQL versions return with key 2
        $vals = array_values($row);
        if (isset($vals[1])) echo $vals[1] . "\n";
    }
} catch (Exception $e) {
    fwrite(STDERR, "SHOW CREATE TABLE failed: " . $e->getMessage() . "\n");
}

echo "\nDone.\n";
