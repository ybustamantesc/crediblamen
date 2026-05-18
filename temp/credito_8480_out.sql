SELECT COUNT(*) AS existe
INTO OUTFILE 'C:/xampp/htdocs/Crediblamen/temp/credito_8480_out.txt'
FIELDS TERMINATED BY ','
LINES TERMINATED BY '\n'
FROM tb_prestamos
WHERE idprestamo = 8480;
