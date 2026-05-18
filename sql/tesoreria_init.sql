-- ALTER script compatible con phpMyAdmin
-- No usa DELIMITER ni PROCEDURES; usa prepared statements condicionales
-- Ejecutar desde la pestaña SQL de phpMyAdmin o mediante cliente mysql

-- Creación segura de tablas (si faltan)
CREATE TABLE IF NOT EXISTS teso_cuentas (
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

CREATE TABLE IF NOT EXISTS teso_cajas (
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

CREATE TABLE IF NOT EXISTS teso_movimientos (
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
  updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS teso_pagos (
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
  updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS teso_cobros (
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
  updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS teso_conciliaciones (
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
  updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS teso_arqueos (
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
  updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS teso_flujo (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  fecha DATE NOT NULL,
  cuenta_id INT NOT NULL,
  concepto VARCHAR(200) NOT NULL,
  tipo ENUM('ingreso','egreso') NOT NULL,
  proyectado DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  realizado DECIMAL(18,2) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS teso_audits (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  entidad ENUM('movimiento','pago','cobro','conciliacion','arqueo','flujo') NOT NULL,
  entidad_id BIGINT NOT NULL,
  accion ENUM('create','update','delete','confirm','approve','cancel') NOT NULL,
  usuario_id INT NULL,
  detalles JSON NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Helper block: add column/index/constraint only if missing (works without stored procedures)

-- teso_cuentas: columna codigo
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cuentas' AND COLUMN_NAME='codigo';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_cuentas ADD COLUMN codigo VARCHAR(20) NOT NULL UNIQUE','SELECT "skip"');
PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;

-- teso_cuentas: columna saldo_actual
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cuentas' AND COLUMN_NAME='saldo_actual';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_cuentas ADD COLUMN saldo_actual DECIMAL(18,2) NOT NULL DEFAULT 0.00','SELECT "skip"');
PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;

-- teso_cuentas: índices
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cuentas' AND INDEX_NAME='idx_teso_cuentas_tipo';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_cuentas ADD INDEX idx_teso_cuentas_tipo (tipo)','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cuentas' AND INDEX_NAME='idx_teso_cuentas_activo';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_cuentas ADD INDEX idx_teso_cuentas_activo (activo)','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;

-- teso_cajas: columna responsable + índice
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cajas' AND COLUMN_NAME='responsable';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_cajas ADD COLUMN responsable INT NULL','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cajas' AND INDEX_NAME='idx_teso_cajas_responsable';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_cajas ADD INDEX idx_teso_cajas_responsable (responsable)','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;

-- teso_movimientos: columna saldo_resultante, índice fecha y FK a teso_cuentas
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_movimientos' AND COLUMN_NAME='saldo_resultante';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_movimientos ADD COLUMN saldo_resultante DECIMAL(18,2) NULL','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_movimientos' AND INDEX_NAME='idx_teso_mov_fecha';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_movimientos ADD INDEX idx_teso_mov_fecha (fecha)','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_movimientos' AND CONSTRAINT_NAME='fk_teso_mov_cuenta';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_movimientos ADD CONSTRAINT fk_teso_mov_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id)','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;

-- teso_pagos: movimiento_id + FKs
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_pagos' AND COLUMN_NAME='movimiento_id';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_pagos ADD COLUMN movimiento_id BIGINT NULL','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_pagos' AND CONSTRAINT_NAME='fk_teso_pagos_cuenta';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_pagos ADD CONSTRAINT fk_teso_pagos_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id)','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_pagos' AND CONSTRAINT_NAME='fk_teso_pagos_mov';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_pagos ADD CONSTRAINT fk_teso_pagos_mov FOREIGN KEY (movimiento_id) REFERENCES teso_movimientos(id)','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;

-- teso_cobros: movimiento_id + FKs
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cobros' AND COLUMN_NAME='movimiento_id';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_cobros ADD COLUMN movimiento_id BIGINT NULL','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cobros' AND CONSTRAINT_NAME='fk_teso_cobros_cuenta';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_cobros ADD CONSTRAINT fk_teso_cobros_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id)','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_cobros' AND CONSTRAINT_NAME='fk_teso_cobros_mov';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_cobros ADD CONSTRAINT fk_teso_cobros_mov FOREIGN KEY (movimiento_id) REFERENCES teso_movimientos(id)','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;

-- teso_conciliaciones: FK + unique
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_conciliaciones' AND CONSTRAINT_NAME='fk_teso_conc_cuenta';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_conciliaciones ADD CONSTRAINT fk_teso_conc_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id)','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_conciliaciones' AND INDEX_NAME='uq_teso_conc_periodo';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_conciliaciones ADD UNIQUE KEY uq_teso_conc_periodo (cuenta_id, periodo)','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;

-- teso_arqueos: FK
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_arqueos' AND CONSTRAINT_NAME='fk_teso_arq_caja';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_arqueos ADD CONSTRAINT fk_teso_arq_caja FOREIGN KEY (caja_id) REFERENCES teso_cajas(id)','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;

-- teso_flujo: FK
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_flujo' AND CONSTRAINT_NAME='fk_teso_flujo_cuenta';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_flujo ADD CONSTRAINT fk_teso_flujo_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id)','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;

-- teso_audits: columna detalles + índice
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_audits' AND COLUMN_NAME='detalles';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_audits ADD COLUMN detalles JSON NULL','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;
SELECT COUNT(*) INTO @cnt FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='teso_audits' AND INDEX_NAME='idx_teso_audit_entidad';
SET @sql = IF(@cnt=0,'ALTER TABLE teso_audits ADD INDEX idx_teso_audit_entidad (entidad, entidad_id)','SELECT "skip"'); PREPARE stm FROM @sql; EXECUTE stm; DEALLOCATE PREPARE stm;

-- End
