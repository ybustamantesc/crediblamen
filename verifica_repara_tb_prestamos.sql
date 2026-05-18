-- Verifica si la columna idusuario existe en la tabla tb_prestamos
SHOW COLUMNS FROM tb_prestamos LIKE 'idusuario';

-- Si la columna NO existe, agrégala:
ALTER TABLE tb_prestamos ADD COLUMN idusuario INT(11) NULL AFTER idasesor;

-- Repara la tabla por si hay corrupción de metadatos
REPAIR TABLE tb_prestamos;

-- Opcional: Forzar que todos los préstamos tengan un usuario por defecto (por ejemplo, admin con id=1)
UPDATE tb_prestamos SET idusuario = 1 WHERE idusuario IS NULL;