SELECT DISTINCT CAST(s.num_prestamo_raw AS UNSIGNED) AS idprestamo
FROM stg_carga_credito s
WHERE NULLIF(TRIM(s.num_prestamo_raw),'') IS NOT NULL
  AND CAST(s.num_prestamo_raw AS UNSIGNED) NOT IN (SELECT idprestamo FROM tb_prestamos)
ORDER BY idprestamo;
