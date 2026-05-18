SELECT DISTINCT num_prestamo_raw
FROM stg_carga_credito
WHERE NULLIF(TRIM(num_prestamo_raw),'') IS NOT NULL
  AND CAST(num_prestamo_raw AS UNSIGNED) = 0
ORDER BY num_prestamo_raw;
