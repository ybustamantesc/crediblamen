<#
PowerShell script to run necessary migrations for Servicredit.
- Prompts for DB user and database (password will be requested by mysql client interactively).
- Detects common XAMPP mysql.exe or uses system `mysql` if available.
- Executes:
  1) `alter_tb_clientes_add_solicitud_fields.sql` (if present)
  2) Backfill: set `idcliente` on `tb_solicitudes` by matching `numero_doc`
  3) Create index `idx_solicitudes_idcliente` on `tb_solicitudes` if it doesn't exist

Usage: Run from PowerShell:
  PS> cd C:\xampp\htdocs\servicredit\sql
  PS> .\run_migrations.ps1
#>

Set-StrictMode -Version Latest

function Find-MySQLExecutable {
    # Check common XAMPP location
    $candidates = @(
        'C:\xampp\mysql\bin\mysql.exe',
        'C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe',
        'C:\Program Files\MySQL\MySQL Server 5.7\bin\mysql.exe'
    )
    foreach ($c in $candidates) {
        if (Test-Path $c) { return $c }
    }
    # Check in PATH
    $cmd = Get-Command mysql -ErrorAction SilentlyContinue
    if ($cmd) { return $cmd.Source }
    return $null
}

Write-Host "== Servicredit: Migration helper ==" -ForegroundColor Cyan
$mysqlPath = Find-MySQLExecutable
if (-not $mysqlPath) {
    Write-Warning "No se encontró mysql.exe en rutas comunes ni en PATH. Por favor introduce la ruta completa a mysql.exe (ej: C:\\xampp\\mysql\\bin\\mysql.exe), o presiona ENTER para cancelar."
    $manual = Read-Host "Ruta a mysql.exe"
    if ([string]::IsNullOrWhiteSpace($manual)) { Write-Host "Cancelado."; exit 1 }
    if (-not (Test-Path $manual)) { Write-Error "Ruta proporcionada no existe."; exit 1 }
    $mysqlPath = $manual
}
Write-Host "Usando cliente MySQL: $mysqlPath`n"

$dbUser = Read-Host "Usuario MySQL (ej: root)"
if ([string]::IsNullOrWhiteSpace($dbUser)) { Write-Error "Usuario requerido."; exit 1 }
$dbName = Read-Host "Nombre de la base de datos (ej: servicredit)"
if ([string]::IsNullOrWhiteSpace($dbName)) { Write-Error "Nombre de DB requerido."; exit 1 }

# Confirm
Write-Host "Se ejecutarán las siguientes acciones en la BD '$dbName' como usuario '$dbUser':" -ForegroundColor Yellow
Write-Host " 1) Ejecutar archivo: alter_tb_clientes_add_solicitud_fields.sql (si existe)"
Write-Host " 2) Backfill: UPDATE tb_solicitudes JOIN tb_clientes ... SET idcliente"
Write-Host " 3) Crear índice idx_solicitudes_idcliente en tb_solicitudes si no existe" -ForegroundColor Yellow
$ok = Read-Host "Continuar? (s/N)"
if ($ok -ne 's' -and $ok -ne 'S') { Write-Host "Abortado por usuario."; exit 0 }

# Helper to run a single SQL statement via -e
function Run-SqlInline($sql) {
    $escaped = $sql -replace '"','\"'
    & $mysqlPath -u $dbUser -p $dbName -e $escaped
    $rc = $LASTEXITCODE
    if ($rc -ne 0) { Write-Error "mysql retornó código $rc al ejecutar SQL: $sql"; exit $rc }
}

# 1) Run alter file if present
$alterFile = Join-Path $PSScriptRoot 'alter_tb_clientes_add_solicitud_fields.sql'
if (Test-Path $alterFile) {
    Write-Host "Ejecutando: $alterFile" -ForegroundColor Green
    # use source with forward slashes to avoid PowerShell redirection issues
    $sourcePath = $alterFile -replace '\\','/'
    & $mysqlPath -u $dbUser -p $dbName -e "source $sourcePath"
    if ($LASTEXITCODE -ne 0) { Write-Error "Error ejecutando $alterFile (codigo $LASTEXITCODE)"; exit $LASTEXITCODE }
} else {
    Write-Host "Archivo $alterFile no encontrado — se omite." -ForegroundColor DarkYellow
}

# 2) Backfill idcliente
Write-Host "Ejecutando backfill de idcliente en tb_solicitudes..." -ForegroundColor Green
$updateSql = @"
UPDATE tb_solicitudes s
JOIN tb_clientes c
  ON (s.numero_doc IS NOT NULL AND c.numero_doc IS NOT NULL AND TRIM(s.numero_doc) = TRIM(c.numero_doc))
SET s.idcliente = c.idcliente
WHERE s.idcliente IS NULL;
"@
& $mysqlPath -u $dbUser -p $dbName -e $updateSql
if ($LASTEXITCODE -ne 0) { Write-Error "Error ejecutando backfill (codigo $LASTEXITCODE)"; exit $LASTEXITCODE }
Write-Host "Backfill completado." -ForegroundColor Cyan

# 3) Create index if missing
Write-Host "Comprobando existencia del índice idx_solicitudes_idcliente..." -ForegroundColor Green
$showIndexSql = "SHOW INDEX FROM tb_solicitudes WHERE Key_name = 'idx_solicitudes_idcliente' LIMIT 1;"
$idxOutput = & $mysqlPath -u $dbUser -p $dbName -e $showIndexSql 2>&1
if ($LASTEXITCODE -ne 0) { Write-Warning "No se pudo consultar índices (codigo $LASTEXITCODE). Intentando crear índice de todos modos."; $idxExists = $false } else {
    $idxExists = ($idxOutput -match 'idx_solicitudes_idcliente')
}
if (-not $idxExists) {
    Write-Host "Índice no encontrado. Creando idx_solicitudes_idcliente..." -ForegroundColor Green
    $alterIndexSql = "ALTER TABLE tb_solicitudes ADD INDEX idx_solicitudes_idcliente (idcliente);"
    & $mysqlPath -u $dbUser -p $dbName -e $alterIndexSql
    if ($LASTEXITCODE -ne 0) { Write-Warning "Fallo al crear índice (codigo $LASTEXITCODE). Revisa permisos o crea el índice manualmente." } else { Write-Host "Índice creado." -ForegroundColor Cyan }
} else {
    Write-Host "Índice ya existe — no se hace nada." -ForegroundColor Cyan
}

Write-Host "Todas las operaciones finalizadas." -ForegroundColor Green
Write-Host "Si hubo errores revisa la salida y ejecuta manualmente las sentencias en phpMyAdmin si es necesario." -ForegroundColor Yellow
