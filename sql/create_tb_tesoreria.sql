-- Tesorería schema
-- Fecha: 2025-11-27

-- Cuentas bancarias / cajas generales
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
  updated_at DATETIME NULL,
  INDEX idx_teso_cuentas_tipo (tipo),
  INDEX idx_teso_cuentas_activo (activo)
) ENGINE=InnoDB;

-- Cajas físicas (opcional si se separa de cuentas)
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
  updated_at DATETIME NULL,
  INDEX idx_teso_cajas_responsable (responsable),
  INDEX idx_teso_cajas_activo (activo)
) ENGINE=InnoDB;

-- Movimientos (ingresos/egresos) de tesorería
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
  updated_at DATETIME NULL,
  CONSTRAINT fk_teso_mov_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id),
  INDEX idx_teso_mov_fecha (fecha),
  INDEX idx_teso_mov_cuenta (cuenta_id),
  INDEX idx_teso_mov_tipo (tipo),
  INDEX idx_teso_mov_estado (estado)
) ENGINE=InnoDB;

-- Pagos realizados (a proveedores, cuotas, etc.)
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
  updated_at DATETIME NULL,
  CONSTRAINT fk_teso_pagos_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id),
  CONSTRAINT fk_teso_pagos_mov FOREIGN KEY (movimiento_id) REFERENCES teso_movimientos(id),
  INDEX idx_teso_pagos_fecha (fecha),
  INDEX idx_teso_pagos_cuenta (cuenta_id),
  INDEX idx_teso_pagos_estado (estado)
) ENGINE=InnoDB;

-- Cobros recibidos (de clientes u otros)
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
  updated_at DATETIME NULL,
  CONSTRAINT fk_teso_cobros_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id),
  CONSTRAINT fk_teso_cobros_mov FOREIGN KEY (movimiento_id) REFERENCES teso_movimientos(id),
  INDEX idx_teso_cobros_fecha (fecha),
  INDEX idx_teso_cobros_cuenta (cuenta_id),
  INDEX idx_teso_cobros_estado (estado)
) ENGINE=InnoDB;

-- Conciliaciones bancarias
CREATE TABLE IF NOT EXISTS teso_conciliaciones (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  cuenta_id INT NOT NULL,
  periodo VARCHAR(7) NOT NULL, -- YYYY-MM
  saldo_extracto DECIMAL(18,2) NOT NULL,
  saldo_libros DECIMAL(18,2) NOT NULL,
  diferencia DECIMAL(18,2) NOT NULL,
  observaciones VARCHAR(255) NULL,
  usuario_id INT NULL,
  estado ENUM('borrador','finalizado','aprobado') NOT NULL DEFAULT 'borrador',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  CONSTRAINT fk_teso_conc_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id),
  UNIQUE KEY uq_teso_conc_periodo (cuenta_id, periodo),
  INDEX idx_teso_conc_estado (estado)
) ENGINE=InnoDB;

-- Arqueos de caja
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
  updated_at DATETIME NULL,
  CONSTRAINT fk_teso_arq_caja FOREIGN KEY (caja_id) REFERENCES teso_cajas(id),
  UNIQUE KEY uq_teso_arq_fecha (caja_id, fecha),
  INDEX idx_teso_arq_estado (estado)
) ENGINE=InnoDB;

-- Flujo proyectado / real de tesorería (opcional)
CREATE TABLE IF NOT EXISTS teso_flujo (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  fecha DATE NOT NULL,
  cuenta_id INT NOT NULL,
  concepto VARCHAR(200) NOT NULL,
  tipo ENUM('ingreso','egreso') NOT NULL,
  proyectado DECIMAL(18,2) NOT NULL DEFAULT 0.00,
  realizado DECIMAL(18,2) NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NULL,
  CONSTRAINT fk_teso_flujo_cuenta FOREIGN KEY (cuenta_id) REFERENCES teso_cuentas(id),
  INDEX idx_teso_flujo_fecha (fecha),
  INDEX idx_teso_flujo_cuenta (cuenta_id),
  INDEX idx_teso_flujo_tipo (tipo)
) ENGINE=InnoDB;

-- Auditoría de operaciones de tesorería
CREATE TABLE IF NOT EXISTS teso_audits (
  id BIGINT AUTO_INCREMENT PRIMARY KEY,
  entidad ENUM('movimiento','pago','cobro','conciliacion','arqueo','flujo') NOT NULL,
  entidad_id BIGINT NOT NULL,
  accion ENUM('create','update','delete','confirm','approve','cancel') NOT NULL,
  usuario_id INT NULL,
  detalles JSON NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_teso_audit_entidad (entidad, entidad_id),
  INDEX idx_teso_audit_accion (accion)
) ENGINE=InnoDB;
