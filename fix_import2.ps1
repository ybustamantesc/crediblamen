$in = 'c:\xampp\htdocs\lasminitas\u987557742_servicont1.sql'
$out = 'c:\xampp\htdocs\lasminitas\u987557742_servicont1.fixed6.sql'
$reader = [System.IO.File]::OpenText($in)
$writer = New-Object System.IO.StreamWriter($out, $false, [System.Text.Encoding]::UTF8)
# write header
$writer.WriteLine('SET FOREIGN_KEY_CHECKS=0;')
while (-not $reader.EndOfStream) {
    $line = $reader.ReadLine()
    $orig = $line
    $line = $line -replace 'CHARACTER SET\s+utf8mb3','CHARACTER SET utf8mb4'
    $line = $line -replace 'COLLATE\s+utf8mb(?:3|4)_[^\s;\)]*','COLLATE utf8mb4_unicode_ci'
    if ($line -match '^CREATE TABLE `([^`]+)`') {
        $tbl = $matches[1]
        $writer.WriteLine('DROP TABLE IF EXISTS `' + $tbl + '`;')
        $writer.WriteLine($line)
        continue
    }
    if ($line -match '^INSERT INTO `') {
        $line = $line -replace '^INSERT INTO `','INSERT IGNORE INTO `'
    }
    if ($line -match '^ALTER TABLE' -and $line -match 'ADD\s+(CONSTRAINT|PRIMARY KEY|KEY|FOREIGN KEY)') {
        # skip problematic ALTER TABLE add constraints / primary keys
        continue
    }
    $writer.WriteLine($line)
}
# footer
$writer.WriteLine('SET FOREIGN_KEY_CHECKS=1;')
$reader.Close()
$writer.Close()
Write-Host "WROTE: $out"