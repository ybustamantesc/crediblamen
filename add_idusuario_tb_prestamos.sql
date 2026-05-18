-- Agrega el campo idusuario a la tabla tb_prestamos para registrar el usuario creador
ALTER TABLE tb_prestamos ADD COLUMN idusuario INT(11) NULL AFTER idasesor;

-- Opcional: Si quieres que todos los préstamos existentes tengan un usuario por defecto (por ejemplo, admin con id=1):
-- UPDATE tb_prestamos SET idusuario = 1 WHERE idusuario IS NULL;