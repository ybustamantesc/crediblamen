-- Idempotent migration: add bitmask columns for selected business days
-- This migration will add `ventas_dias_buenos` and `ventas_dias_malos` as INT columns
-- Use a bitmask with 7 bits: bit0=Lunes, bit1=Martes, bit2=Miercoles, bit3=Jueves,
-- bit4=Viernes, bit5=Sabado, bit6=Domingo
-- Run with: mysql -u <user> -p <database> < migrate_add_ventas_dias_bitmask.sql

-- Use explicit ALTER TABLE with IF NOT EXISTS (works on MySQL 8+).
-- This avoids querying INFORMATION_SCHEMA and PREPARE/EXECUTE which may fail
-- if the MySQL user has limited privileges.

ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS ventas_dias_buenos_mask INT NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS ventas_dias_malos_mask INT NULL;

-- Optional: add simple indexes for mask columns
ALTER TABLE tb_solicitudes ADD INDEX IF NOT EXISTS idx_ventas_dias_buenos_mask (ventas_dias_buenos_mask);
ALTER TABLE tb_solicitudes ADD INDEX IF NOT EXISTS idx_ventas_dias_malos_mask (ventas_dias_malos_mask);

-- End of migration (IF NOT EXISTS style)
