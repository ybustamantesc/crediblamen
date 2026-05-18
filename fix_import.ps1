$in = 'c:\xampp\htdocs\lasminitas\u987557742_servicont1.sql'
$out = 'c:\xampp\htdocs\lasminitas\u987557742_servicont1.fixed5.sql'
$text = Get-Content $in -Raw -Encoding UTF8
$text = $text -replace "CHARACTER SET\s+utf8mb3","CHARACTER SET utf8mb4"
$text = $text -replace "COLLATE\s+utf8mb(?:3|4)_[^\s;\)]+","COLLATE utf8mb4_unicode_ci"
$text = $text -replace 'CREATE TABLE `([^`]+)` \(', 'DROP TABLE IF EXISTS `$1`;`r`n`r`nCREATE TABLE `$1` ('
$text = $text -replace 'INSERT INTO `','INSERT IGNORE INTO `'
$text = [regex]::Replace($text, '(?m)^ALTER TABLE[^;]*ADD\s+(CONSTRAINT|PRIMARY KEY|KEY|FOREIGN KEY)[^;]*;','')
$header = "SET FOREIGN_KEY_CHECKS=0;`r`n"
$footer = "`r`nSET FOREIGN_KEY_CHECKS=1;`r`n"
$outText = $header + $text + $footer
$bytes = [System.Text.Encoding]::UTF8.GetBytes($outText)
[System.IO.File]::WriteAllBytes($out, $bytes)
Write-Host "WROTE: $out"