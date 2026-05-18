SELECT num_prestamo_raw, num_exp_raw, fecha_desembolso_raw
INTO OUTFILE 'C:/xampp/htdocs/Crediblamen/temp/stg_debug_13p1.txt'
FIELDS TERMINATED BY ';'
LINES TERMINATED BY '\n'
FROM stg_carga_credito
LIMIT 5;
