-- Test script: toma el primer cliente no marcado como rechazado,
-- crea una copia en tb_clientes_rechazados y marca el original como rechazado.
-- Úsalo sólo en entorno de pruebas.

-- 1) Obtener un cliente no rechazado y guardar su id en variable
SET @cid := (SELECT idcliente FROM tb_clientes WHERE (rechazado IS NULL OR rechazado = 0) LIMIT 1);

-- 2) Si no se encontró cliente, abortar (la siguiente inserción no hará nada)
-- 3) Insertar copia en tb_clientes_rechazados
INSERT INTO tb_clientes_rechazados (idcliente_original, apellidos, nombres, direccion, telefono, tipo_doc, numero_doc, comentarios, rechazo_motivo, rechazado_por, rechazado_en)
SELECT idcliente, apellidos, nombres, direccion, telefono, tipo_doc, numero_doc, comentarios, 'Prueba automática', 1, NOW()
FROM tb_clientes WHERE idcliente = @cid;

-- 4) Marcar el cliente original como rechazado y desactivado
UPDATE tb_clientes SET rechazado = 1, estado = 0 WHERE idcliente = @cid;

-- 5) Mostrar el resultado para verificación
SELECT @cid AS cliente_id;
SELECT * FROM tb_clientes_rechazados WHERE rechazado_en >= DATE_SUB(NOW(), INTERVAL 1 MINUTE) ORDER BY rechazado_en DESC LIMIT 5;
