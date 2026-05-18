-- ============================================================
-- Script para limpiar todos los asientos contables
-- Limpia el sistema para una instalación nueva
-- Fecha: 2026-01-13
-- ============================================================

-- Deshabilitar revisión de llaves foráneas temporalmente
SET FOREIGN_KEY_CHECKS = 0;

-- Eliminar todas las líneas de asientos (detalles)
TRUNCATE TABLE tb_journal_entry;

-- Eliminar todas las cabeceras de asientos
TRUNCATE TABLE tb_journal;

-- Reiniciar los autoincrementos
ALTER TABLE tb_journal_entry AUTO_INCREMENT = 1;
ALTER TABLE tb_journal AUTO_INCREMENT = 1;

-- Habilitar revisión de llaves foráneas nuevamente
SET FOREIGN_KEY_CHECKS = 1;

-- Verificar que las tablas estén vacías
SELECT 'Asientos (tb_journal)' as Tabla, COUNT(*) as Total FROM tb_journal
UNION ALL
SELECT 'Líneas de asientos (tb_journal_entry)' as Tabla, COUNT(*) as Total FROM tb_journal_entry;

-- Mensaje de confirmación
SELECT 'Los asientos contables han sido eliminados exitosamente. El sistema está listo para una nueva instalación.' as Resultado;
