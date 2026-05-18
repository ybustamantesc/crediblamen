-- Limpieza de tablespace residual para stg_carga_credito
USE crediblamen;
DROP TABLE IF EXISTS stg_carga_credito;
-- Si existe tablespace residual, descartar:
-- ALTER TABLE stg_carga_credito DISCARD TABLESPACE;
-- (No es necesario si DROP elimina todo)
