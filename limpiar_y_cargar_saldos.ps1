$mysqlPath = "C:\xampp\mysql\bin\mysql.exe"
$database = "u987557742_testsystem"
$csvFile = "c:\xampp\htdocs\Conta\temp\saldos_iniciales_balance.csv"

Write-Host ""
Write-Host "===================================================" -ForegroundColor Red
Write-Host "  LIMPIEZA Y CARGA DE CATALOGO DE CUENTAS" -ForegroundColor Red
Write-Host "===================================================" -ForegroundColor Red
Write-Host ""

# Paso 1: Limpiar todas las tablas
Write-Host "PASO 1: Eliminando todos los registros anteriores..." -ForegroundColor Yellow
Write-Host ""

$queries = @(
    "DELETE FROM tb_journal_entry;",
    "DELETE FROM tb_journal;",
    "DELETE FROM tb_account;",
    "ALTER TABLE tb_account AUTO_INCREMENT = 1;",
    "ALTER TABLE tb_journal AUTO_INCREMENT = 1;",
    "ALTER TABLE tb_journal_entry AUTO_INCREMENT = 1;"
)

foreach ($query in $queries) {
    Write-Host "  Ejecutando: $query" -ForegroundColor Gray
    & $mysqlPath -u root -h localhost $database -e $query
}

Write-Host ""
Write-Host "  Limpieza completada" -ForegroundColor Green
Write-Host ""

# Paso 2: Leer el CSV e insertar las cuentas
Write-Host "PASO 2: Cargando cuentas desde CSV..." -ForegroundColor Yellow
Write-Host ""

$csv = Import-Csv -Path $csvFile -Encoding UTF8
$cuentasInsertadas = 0
$cuentasOmitidas = 0

foreach ($row in $csv) {
    $codigo = $row.'Código'.Trim()
    $denominacion = $row.'Denominación'.Trim()
    $saldo = $row.'Saldo Anterior'.Trim()
    
    # Omitir líneas vacías o con código vacío
    if ([string]::IsNullOrWhiteSpace($codigo)) {
        continue
    }
    
    # Determinar el tipo de cuenta según el primer dígito
    $primerDigito = $codigo.Substring(0, 1)
    switch ($primerDigito) {
        '1' { $tipo = 'activo' }
        '2' { $tipo = 'pasivo' }
        '3' { $tipo = 'patrimonio' }
        '4' { $tipo = 'ingreso' }
        '5' { $tipo = 'gasto' }
        '6' { $tipo = 'gasto' }
        '7' { $tipo = 'gasto' }
        default { $tipo = 'activo' }
    }
    
    # Escapar comillas en el nombre
    $denominacionEscapada = $denominacion -replace "'", "''"
    
    # Insertar la cuenta
    $insertQuery = "INSERT INTO tb_account (code, name, type, created_at) VALUES ('$codigo', '$denominacionEscapada', '$tipo', NOW());"
    
    try {
        & $mysqlPath -u root -h localhost $database -e $insertQuery 2>$null
        Write-Host "  Insertada: $codigo - $denominacion" -ForegroundColor Green
        $cuentasInsertadas++
    } catch {
        Write-Host "  Error al insertar: $codigo - $denominacion" -ForegroundColor Red
        $cuentasOmitidas++
    }
}

Write-Host ""
Write-Host "===================================================" -ForegroundColor Green
Write-Host "  PROCESO COMPLETADO" -ForegroundColor Green
Write-Host "===================================================" -ForegroundColor Green
Write-Host ""
Write-Host "  Cuentas insertadas: $cuentasInsertadas" -ForegroundColor White
Write-Host "  Cuentas omitidas: $cuentasOmitidas" -ForegroundColor White
Write-Host ""

# Verificar el resultado
Write-Host "Verificando cuentas cargadas:" -ForegroundColor Cyan
Write-Host ""

$queryVerify = "SELECT COUNT(*) as total FROM tb_account;"
$result = & $mysqlPath -u root -h localhost $database -e $queryVerify
Write-Host $result

Write-Host ""
Write-Host "Primeras 20 cuentas:" -ForegroundColor Cyan
$queryList = "SELECT id, code, name, type FROM tb_account ORDER BY code LIMIT 20;"
$resultList = & $mysqlPath -u root -h localhost $database -t -e $queryList
Write-Host $resultList

Write-Host ""
Write-Host "Ultimas 10 cuentas:" -ForegroundColor Cyan
$queryLast = "SELECT id, code, name, type FROM tb_account ORDER BY code DESC LIMIT 10;"
$resultLast = & $mysqlPath -u root -h localhost $database -t -e $queryLast
Write-Host $resultLast

Write-Host ""
Write-Host "Ahora puedes acceder a: http://localhost/Conta/contabilidad/catalogo" -ForegroundColor Yellow
Write-Host ""
