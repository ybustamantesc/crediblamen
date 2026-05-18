@echo off
echo ran>"C:\xampp\htdocs\Crediblamen\temp\stg_sample_13p1.txt"
"C:\xampp\mysql\bin\mysql.exe" -u root --batch --skip-column-names crediblamen -e "SELECT num_prestamo_raw FROM stg_carga_credito WHERE NULLIF(TRIM(num_prestamo_raw),'') IS NOT NULL LIMIT 10;" >> "C:\xampp\htdocs\Crediblamen\temp\stg_sample_13p1.txt"
