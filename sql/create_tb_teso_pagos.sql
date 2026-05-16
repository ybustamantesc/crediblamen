-- Create only the teso_pagos table (for environments where full tesoreria SQL wasn't executed)
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
