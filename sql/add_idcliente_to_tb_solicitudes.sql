-- Añade columna idcliente a tb_solicitudes y backfill desde tb_clientes
ALTER TABLE `tb_solicitudes`
  ADD COLUMN `idcliente` INT NULL;

-- Crear índice para facilitar joins
CREATE INDEX `idx_solicitudes_idcliente` ON `tb_solicitudes` (`idcliente`);

-- Backfill: si existe numero_doc en solicitudes y clientes, asociar idcliente
UPDATE `tb_solicitudes` s
  JOIN `tb_clientes` c ON (s.numero_doc IS NOT NULL AND c.numero_doc IS NOT NULL AND s.numero_doc = c.numero_doc)
  SET s.idcliente = c.idcliente
  WHERE s.idcliente IS NULL;

-- Nota: ejecutar este script en phpMyAdmin o desde el cliente mysql.
-- Si tu versión de MySQL no permite ejecutar varias sentencias en un único comando, ejecútalas por separado en orden.
