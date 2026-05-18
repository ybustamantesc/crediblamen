SELECT COUNT(*) AS sin_solicitud
FROM tb_prestamos p
LEFT JOIN tb_solicitudes s ON s.idsolicitud = p.idsolicitud
WHERE p.idprestamo IN (SELECT DISTINCT CAST(num_prestamo_raw AS UNSIGNED) FROM stg_carga_credito WHERE NULLIF(TRIM(num_prestamo_raw),'') IS NOT NULL)
  AND s.idsolicitud IS NULL;
