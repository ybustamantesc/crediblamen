# PowerShell script to backup DB, run migration and test notes endpoints
# Edit the variables below before running

$mysqlExe = "C:\\xampp\\mysql\\bin\\mysql.exe"
$mysqldumpExe = "C:\\xampp\\mysql\\bin\\mysqldump.exe"
$dbUser = Read-Host "DB user"
$dbPass = Read-Host "DB password" -AsSecureString
$dbPassPlain = [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($dbPass))
$dbName = Read-Host "DB name"

# Backup tb_solicitudes and notes tables
$backupFile = "${dbName}_backup_$(Get-Date -Format yyyyMMdd_HHmmss).sql"
Write-Host "Creating backup to $backupFile ..."
& $mysqldumpExe -u $dbUser -p$dbPassPlain $dbName tb_solicitudes tb_solicitudes_notes tb_solicitud_aprobaciones > $backupFile
if ($LASTEXITCODE -ne 0) { Write-Error "Backup failed"; exit 1 }
Write-Host "Backup completed."

# Run migration (the script is in sql/migrate_add_solicitudes_fields_and_run_report.sql)
$migrationFile = "c:\\xampp\\htdocs\\servicredit\\sql\\migrate_add_solicitudes_fields_and_run_report.sql"
if (-not (Test-Path $migrationFile)) { Write-Error "Migration file not found: $migrationFile"; exit 1 }
Write-Host "Applying migration $migrationFile ..."
& $mysqlExe -u $dbUser -p$dbPassPlain $dbName < $migrationFile
if ($LASTEXITCODE -ne 0) { Write-Error "Migration failed"; exit 1 }
Write-Host "Migration applied."

# Test endpoints: replace ID with an existingSolicitud id
$baseUrl = Read-Host "Base URL (e.g. http://localhost/servicredit)"
$testId = Read-Host "Test solicitud id (e.g. 1)"

# Test GET notes
$notesUrl = "$baseUrl/solicitudes/get_notes_ajax/$testId"
Write-Host "Testing GET $notesUrl ..."
try {
    $resp = Invoke-RestMethod -Method Get -Uri $notesUrl -ErrorAction Stop
    Write-Host "GET response:`n" ($resp | ConvertTo-Json -Depth 4)
} catch {
    Write-Error "GET request failed: $_"
}

# Test POST add note
$addUrl = "$baseUrl/solicitudes/add_note_ajax"
$body = @{ idsolicitud = $testId; comment = "Prueba de nota desde PowerShell " + (Get-Date) }
Write-Host "Posting sample note to $addUrl ..."
try {
    $resp2 = Invoke-RestMethod -Method Post -Uri $addUrl -Body $body -ErrorAction Stop
    Write-Host "POST response:`n" ($resp2 | ConvertTo-Json -Depth 4)
} catch {
    Write-Error "POST request failed: $_"
}

Write-Host "Done."
