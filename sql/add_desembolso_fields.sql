ALTER TABLE tb_prestamos 
ADD COLUMN desembolsado TINYINT(1) NOT NULL DEFAULT 0,
ADD COLUMN obs_desembolso TEXT NULL;
-- Si no existe la columna fecha_desembolso, agregarla:
-- ALTER TABLE tb_prestamos ADD COLUMN fecha_desembolso DATE NULL;
