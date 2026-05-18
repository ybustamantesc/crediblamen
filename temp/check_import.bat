@echo off
C:\xampp\mysql\bin\mysql.exe -u root -p conta -e "SELECT COUNT(*) AS lineas FROM stg_carga_credito; SELECT COUNT(DISTINCT num_prestamo_raw) AS creditos FROM stg_carga_credito;"
pause
