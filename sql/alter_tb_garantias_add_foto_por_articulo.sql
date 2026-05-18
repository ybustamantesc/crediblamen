-- Script: Añadir columna `foto` a `tb_garantias` para almacenar 1 imagen por artículo
-- Agrega la columna `foto` (ruta al archivo) y opcionalmente crea un índice ligero.
-- También incluye sentencia de reversión (DROP COLUMN).

-- Revisa y haz backup antes de ejecutar.

ALTER TABLE `tb_garantias`
  ADD COLUMN `foto` VARCHAR(255) DEFAULT NULL AFTER `tiempo_vida`;

-- Opcional: crear índice si vas a filtrar/consultar por foto (normalmente no necesario)
-- CREATE INDEX `idx_garantias_foto` ON `tb_garantias` (`foto`);

-- Reversión:
-- ALTER TABLE `tb_garantias` DROP COLUMN `foto`;

-- NOTAS:
-- 1) Si prefieres almacenar varias fotos por artículo, lo correcto es crear una tabla relacionada
--    `tb_garantias_fotos (id, garantia_id, filename, created_at, ... )` en lugar de columnas repetidas.
-- 2) Tras ejecutar este ALTER, actualiza el controlador `garantias/save` para mover el archivo subido y
--    guardar la ruta relativa en la columna `foto` del registro correspondiente.
