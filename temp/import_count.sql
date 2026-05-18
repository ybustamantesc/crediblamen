SELECT COUNT(DISTINCT CAST(num_prestamo_raw AS UNSIGNED)) AS creditos_csv
FROM stg_carga_credito
WHERE NULLIF(TRIM(num_prestamo_raw),'') IS NOT NULL;
