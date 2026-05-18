-- Idempotent migration: add columns for "1. DATOS GENERALES DEL CLIENTE"
-- Adds columns to `tb_solicitudes` if they don't already exist.
-- Review types before running on production. Designed for MySQL 8+ (ADD COLUMN IF NOT EXISTS).
-- Run with: mysql -u <user> -p <database> < migrate_add_section1_clientes.sql

ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS nombre_completo VARCHAR(255) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS apellidos VARCHAR(255) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS nombres VARCHAR(255) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS fecha_solicitud DATETIME NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS numero_doc VARCHAR(100) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS tipo_documento VARCHAR(50) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS fecha_nacimiento DATE NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS edad INT NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS estado_civil VARCHAR(60) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS nombre_conyuge VARCHAR(255) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS dni_conyuge VARCHAR(100) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS ocupacion_conyuge VARCHAR(255) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS telefono_conyuge VARCHAR(100) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS numero_dependientes INT NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS telefono VARCHAR(100) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS direccion TEXT NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS tiempo_residir_anios INT NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS tiempo_residir_meses INT NULL;
ALTER TABLE tb_solicitudes ADD COLUMN IF NOT EXISTS condicion_vivienda VARCHAR(60) NULL;

-- Useful index on identity number for quick lookups (no unique constraint applied)
ALTER TABLE tb_solicitudes ADD INDEX IF NOT EXISTS idx_numero_doc (numero_doc);

-- End of migration
