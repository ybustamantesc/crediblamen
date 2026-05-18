SELECT
  COUNT(*) AS filas_csv,
  COUNT(DISTINCT NULLIF(TRIM(num_prestamo_raw), '')) AS prestamos_distintos,
  SUM(CASE WHEN NULLIF(TRIM(num_prestamo_raw), '') IS NULL THEN 1 ELSE 0 END) AS filas_sin_prestamo,
  SUM(CASE WHEN NULLIF(TRIM(num_prestamo_raw), '') = '0' THEN 1 ELSE 0 END) AS filas_prestamo_cero
FROM stg_carga_credito;
