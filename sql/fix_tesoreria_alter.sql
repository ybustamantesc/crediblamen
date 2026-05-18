-- Idempotent ALTER script for Tesorería
-- Adds missing columns, indexes and foreign keys when needed
-- Safe to run multiple times. Tested on MySQL 5.7+ / 8.0

DELIMITER $$

-- teso_cuentas: create if missing, add columns/indexes if missing
DROP PROCEDURE IF EXISTS upd_teso_cuentas$$
CREATE PROCEDURE upd_teso_cuentas()
BEGIN
  IF NOT EXISTS (
    SELECT 1 FROM INFORMATION_SCHEMA.TABLES
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'teso_cuentas'
  ) THEN
    CREATE TABLE teso_cuentas (
      id INT AUTO_INCREMENT PRIMARY KEY,
      codigo VARCHAR(20) NOT NULL UNIQUE,
      nombre VARCHAR(100) NOT NULL,
      tipo ENUM('banco','caja','otros') NOT NULL DEFAULT 'banco',
      moneda VARCHAR(10) NOT NULL DEFAULT 'PEN',
      saldo_inicial DECIMAL(18,2) NOT NULL DEFAULT 0.00,
      saldo_actual DECIMAL(18,2) NOT NULL DEFAULT 0.00,
      activo TINYINT(1) NOT NULL DEFAULT 1,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NULL
    ) ENGINE=InnoDB;
  ELSE
    -- ensure columns
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cuentas' AND COLUMN_NAME='codigo') THEN
      SET @s = 'ALTER TABLE teso_cuentas ADD COLUMN codigo VARCHAR(20) NOT NULL UNIQUE';
      PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cuentas' AND COLUMN_NAME='saldo_actual') THEN
      SET @s = 'ALTER TABLE teso_cuentas ADD COLUMN saldo_actual DECIMAL(18,2) NOT NULL DEFAULT 0.00';
      PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
    -- indexes
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cuentas' AND INDEX_NAME='idx_teso_cuentas_tipo') THEN
      SET @s = 'ALTER TABLE teso_cuentas ADD INDEX idx_teso_cuentas_tipo (tipo)';
      PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cuentas' AND INDEX_NAME='idx_teso_cuentas_activo') THEN
      SET @s = 'ALTER TABLE teso_cuentas ADD INDEX idx_teso_cuentas_activo (activo)';
      PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
  END IF;
END$$
CALL upd_teso_cuentas();
DROP PROCEDURE IF EXISTS upd_teso_cuentas$$

-- teso_cajas
DROP PROCEDURE IF EXISTS upd_teso_cajas$$
CREATE PROCEDURE upd_teso_cajas()
BEGIN
  IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cajas') THEN
    CREATE TABLE teso_cajas (
      id INT AUTO_INCREMENT PRIMARY KEY,
      codigo VARCHAR(20) NOT NULL UNIQUE,
      nombre VARCHAR(100) NOT NULL,
      responsable INT NULL,
      moneda VARCHAR(10) NOT NULL DEFAULT 'PEN',
      saldo_inicial DECIMAL(18,2) NOT NULL DEFAULT 0.00,
      saldo_actual DECIMAL(18,2) NOT NULL DEFAULT 0.00,
      activo TINYINT(1) NOT NULL DEFAULT 1,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NULL
    ) ENGINE=InnoDB;
  ELSE
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cajas' AND COLUMN_NAME='responsable') THEN
      SET @s='ALTER TABLE teso_cajas ADD COLUMN responsable INT NULL'; PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cajas' AND INDEX_NAME='idx_teso_cajas_responsable') THEN
      SET @s='ALTER TABLE teso_cajas ADD INDEX idx_teso_cajas_responsable (responsable)'; PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
  END IF;
END$$
CALL upd_teso_cajas();
DROP PROCEDURE IF EXISTS upd_teso_cajas$$

-- teso_movimientos
DROP PROCEDURE IF EXISTS upd_teso_movimientos$$
CREATE PROCEDURE upd_teso_movimientos()
BEGIN
  IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_movimientos') THEN
    CREATE TABLE teso_movimientos (
      id BIGINT AUTO_INCREMENT PRIMARY KEY,
      fecha DATE NOT NULL,
      cuenta_id INT NOT NULL,
      tipo ENUM('ingreso','egreso','transferencia') NOT NULL,
      origen ENUM('caja','banco','otros') NOT NULL,
      referencia VARCHAR(50) NULL,
      descripcion VARCHAR(255) NULL,
      moneda VARCHAR(10) NOT NULL DEFAULT 'PEN',
      monto DECIMAL(18,2) NOT NULL,
      saldo_resultante DECIMAL(18,2) NULL,
      documento_tipo VARCHAR(20) NULL,
      documento_numero VARCHAR(50) NULL,
      usuario_id INT NULL,
      estado ENUM('registrado','confirmado','anulado') NOT NULL DEFAULT 'registrado',
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NULL,
      CONSTRAINT fk_teso_mov_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id)
    ) ENGINE=InnoDB;
  ELSE
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_movimientos' AND COLUMN_NAME='saldo_resultante') THEN
      SET @s='ALTER TABLE teso_movimientos ADD COLUMN saldo_resultante DECIMAL(18,2) NULL'; PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_movimientos' AND INDEX_NAME='idx_teso_mov_fecha') THEN
      SET @s='ALTER TABLE teso_movimientos ADD INDEX idx_teso_mov_fecha (fecha)'; PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
    -- add FK if missing
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_movimientos' AND CONSTRAINT_NAME='fk_teso_mov_cuenta') THEN
      SET @s='ALTER TABLE teso_movimientos ADD CONSTRAINT fk_teso_mov_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id)'; PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
  END IF;
END$$
CALL upd_teso_movimientos();
DROP PROCEDURE IF EXISTS upd_teso_movimientos$$

-- teso_pagos
DROP PROCEDURE IF EXISTS upd_teso_pagos$$
CREATE PROCEDURE upd_teso_pagos()
BEGIN
  IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_pagos') THEN
    CREATE TABLE teso_pagos (
      id BIGINT AUTO_INCREMENT PRIMARY KEY,
      fecha DATE NOT NULL,
      cuenta_id INT NOT NULL,
      beneficiario VARCHAR(150) NOT NULL,
      concepto VARCHAR(200) NULL,
      moneda VARCHAR(10) NOT NULL DEFAULT 'PEN',
      monto DECIMAL(18,2) NOT NULL,
      medio_pago ENUM('transferencia','cheque','efectivo','tarjeta') NOT NULL DEFAULT 'transferencia',
      documento_tipo VARCHAR(20) NULL,
      documento_numero VARCHAR(50) NULL,
      movimiento_id BIGINT NULL,
      usuario_id INT NULL,
      estado ENUM('registrado','confirmado','anulado') NOT NULL DEFAULT 'registrado',
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NULL,
      CONSTRAINT fk_teso_pagos_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id)
    ) ENGINE=InnoDB;
  ELSE
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_pagos' AND COLUMN_NAME='movimiento_id') THEN
      SET @s='ALTER TABLE teso_pagos ADD COLUMN movimiento_id BIGINT NULL'; PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_pagos' AND CONSTRAINT_NAME='fk_teso_pagos_cuenta') THEN
      SET @s='ALTER TABLE teso_pagos ADD CONSTRAINT fk_teso_pagos_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id)'; PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_pagos' AND CONSTRAINT_NAME='fk_teso_pagos_mov') THEN
      SET @s='ALTER TABLE teso_pagos ADD CONSTRAINT fk_teso_pagos_mov FOREIGN KEY (movimiento_id) REFERENCES teso_movimientos(id)'; PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
  END IF;
END$$
CALL upd_teso_pagos();
DROP PROCEDURE IF EXISTS upd_teso_pagos$$

-- teso_cobros
DROP PROCEDURE IF EXISTS upd_teso_cobros$$
CREATE PROCEDURE upd_teso_cobros()
BEGIN
  IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cobros') THEN
    CREATE TABLE teso_cobros (
      id BIGINT AUTO_INCREMENT PRIMARY KEY,
      fecha DATE NOT NULL,
      cuenta_id INT NOT NULL,
      pagador VARCHAR(150) NOT NULL,
      concepto VARCHAR(200) NULL,
      moneda VARCHAR(10) NOT NULL DEFAULT 'PEN',
      monto DECIMAL(18,2) NOT NULL,
      medio_cobro ENUM('transferencia','deposito','efectivo','tarjeta') NOT NULL DEFAULT 'transferencia',
      documento_tipo VARCHAR(20) NULL,
      documento_numero VARCHAR(50) NULL,
      movimiento_id BIGINT NULL,
      usuario_id INT NULL,
      estado ENUM('registrado','confirmado','anulado') NOT NULL DEFAULT 'registrado',
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NULL,
      CONSTRAINT fk_teso_cobros_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id)
    ) ENGINE=InnoDB;
  ELSE
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cobros' AND COLUMN_NAME='movimiento_id') THEN
      SET @s='ALTER TABLE teso_cobros ADD COLUMN movimiento_id BIGINT NULL'; PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cobros' AND CONSTRAINT_NAME='fk_teso_cobros_cuenta') THEN
      SET @s='ALTER TABLE teso_cobros ADD CONSTRAINT fk_teso_cobros_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id)'; PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cobros' AND CONSTRAINT_NAME='fk_teso_cobros_mov') THEN
      SET @s='ALTER TABLE teso_cobros ADD CONSTRAINT fk_teso_cobros_mov FOREIGN KEY (movimiento_id) REFERENCES teso_movimientos(id)'; PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
  END IF;
END$$
CALL upd_teso_cobros();
DROP PROCEDURE IF EXISTS upd_teso_cobros$$

-- teso_conciliaciones
DROP PROCEDURE IF EXISTS upd_teso_conciliaciones$$
CREATE PROCEDURE upd_teso_conciliaciones()
BEGIN
  IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_conciliaciones') THEN
    CREATE TABLE teso_conciliaciones (
      id BIGINT AUTO_INCREMENT PRIMARY KEY,
      cuenta_id INT NOT NULL,
      periodo VARCHAR(7) NOT NULL,
      saldo_extracto DECIMAL(18,2) NOT NULL,
      saldo_libros DECIMAL(18,2) NOT NULL,
      diferencia DECIMAL(18,2) NOT NULL,
      observaciones VARCHAR(255) NULL,
      usuario_id INT NULL,
      estado ENUM('borrador','finalizado','aprobado') NOT NULL DEFAULT 'borrador',
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NULL,
      CONSTRAINT fk_teso_conc_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id),
      UNIQUE KEY uq_teso_conc_periodo (cuenta_id, periodo)
    ) ENGINE=InnoDB;
  ELSE
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_conciliaciones' AND CONSTRAINT_NAME='fk_teso_conc_cuenta') THEN
      SET @s='ALTER TABLE teso_conciliaciones ADD CONSTRAINT fk_teso_conc_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id)'; PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_conciliaciones' AND INDEX_NAME='uq_teso_conc_periodo') THEN
      SET @s='ALTER TABLE teso_conciliaciones ADD UNIQUE KEY uq_teso_conc_periodo (cuenta_id, periodo)'; PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
  END IF;
END$$
CALL upd_teso_conciliaciones();
DROP PROCEDURE IF EXISTS upd_teso_conciliaciones$$

-- teso_arqueos
DROP PROCEDURE IF EXISTS upd_teso_arqueos$$
CREATE PROCEDURE upd_teso_arqueos()
BEGIN
  IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_arqueos') THEN
    CREATE TABLE teso_arqueos (
      id BIGINT AUTO_INCREMENT PRIMARY KEY,
      caja_id INT NOT NULL,
      fecha DATE NOT NULL,
      responsable INT NULL,
      moneda VARCHAR(10) NOT NULL DEFAULT 'PEN',
      saldo_sistema DECIMAL(18,2) NOT NULL,
      saldo_fisico DECIMAL(18,2) NOT NULL,
      diferencia DECIMAL(18,2) NOT NULL,
      observaciones VARCHAR(255) NULL,
      usuario_id INT NULL,
      estado ENUM('borrador','finalizado','aprobado') NOT NULL DEFAULT 'borrador',
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NULL,
      CONSTRAINT fk_teso_arq_caja FOREIGN KEY (caja_id) REFERENCES teso_cajas(id),
      UNIQUE KEY uq_teso_arq_fecha (caja_id, fecha)
    ) ENGINE=InnoDB;
  ELSE
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_arqueos' AND CONSTRAINT_NAME='fk_teso_arq_caja') THEN
      SET @s='ALTER TABLE teso_arqueos ADD CONSTRAINT fk_teso_arq_caja FOREIGN KEY (caja_id) REFERENCES teso_cajas(id)'; PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
  END IF;
END$$
CALL upd_teso_arqueos();
DROP PROCEDURE IF EXISTS upd_teso_arqueos$$

-- teso_flujo
DROP PROCEDURE IF EXISTS upd_teso_flujo$$
CREATE PROCEDURE upd_teso_flujo()
BEGIN
  IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_flujo') THEN
    CREATE TABLE teso_flujo (
      id BIGINT AUTO_INCREMENT PRIMARY KEY,
      fecha DATE NOT NULL,
      cuenta_id INT NOT NULL,
      concepto VARCHAR(200) NOT NULL,
      tipo ENUM('ingreso','egreso') NOT NULL,
      proyectado DECIMAL(18,2) NOT NULL DEFAULT 0.00,
      realizado DECIMAL(18,2) NULL,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      updated_at DATETIME NULL,
      CONSTRAINT fk_teso_flujo_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id)
    ) ENGINE=InnoDB;
  ELSE
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_flujo' AND CONSTRAINT_NAME='fk_teso_flujo_cuenta') THEN
      SET @s='ALTER TABLE teso_flujo ADD CONSTRAINT fk_teso_flujo_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id)'; PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
  END IF;
END$$
CALL upd_teso_flujo();
DROP PROCEDURE IF EXISTS upd_teso_flujo$$

-- teso_audits
DROP PROCEDURE IF EXISTS upd_teso_audits$$
CREATE PROCEDURE upd_teso_audits()
BEGIN
  IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_audits') THEN
    CREATE TABLE teso_audits (
      id BIGINT AUTO_INCREMENT PRIMARY KEY,
      entidad ENUM('movimiento','pago','cobro','conciliacion','arqueo','flujo') NOT NULL,
      entidad_id BIGINT NOT NULL,
      accion ENUM('create','update','delete','confirm','approve','cancel') NOT NULL,
      usuario_id INT NULL,
      detalles JSON NULL,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB;
  ELSE
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_audits' AND COLUMN_NAME='detalles') THEN
      SET @s='ALTER TABLE teso_audits ADD COLUMN detalles JSON NULL'; PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
    IF NOT EXISTS (SELECT 1 FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_audits' AND INDEX_NAME='idx_teso_audit_entidad') THEN
      SET @s='ALTER TABLE teso_audits ADD INDEX idx_teso_audit_entidad (entidad, entidad_id)'; PREPARE stm FROM @s; EXECUTE stm; DEALLOCATE PREPARE stm;
    END IF;
  END IF;
END$$
CALL upd_teso_audits();
DROP PROCEDURE IF EXISTS upd_teso_audits$$

DELIMITER ;

-- Notes:
-- 1) The script will create missing tables (using the structure you provided) and add missing
--    columns, indexes and foreign keys if they are not present. It is defensive but not
--    transaction-wrapped because some ALTER operations (like adding FKs) cannot be
--    easily combined across all MySQL versions.
-- 2) If there is inconsistent data that prevents adding a foreign key (e.g., orphaned rows),
--    the ALTER CONSTRAINT will fail; in that case inspect the failing statement and fix the data.
