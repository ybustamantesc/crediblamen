-- Crear tabla de centros de costo
CREATE TABLE IF NOT EXISTS `tb_centro_costo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(10) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `codigo` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insertar los 5 centros de costo iniciales
INSERT INTO `tb_centro_costo` (`codigo`, `nombre`, `descripcion`, `activo`) VALUES
('001', 'Gerencia', 'Centro de costo de Gerencia', 1),
('002', 'Administración', 'Centro de costo de Administración', 1),
('003', 'Finanzas', 'Centro de costo de Finanzas', 1),
('004', 'Crédito', 'Centro de costo de Crédito', 1),
('005', 'Cobranza', 'Centro de costo de Cobranza', 1);

-- Agregar campo centro_costo_id a tb_journal
ALTER TABLE `tb_journal` 
ADD COLUMN `centro_costo_id` int(11) NULL AFTER `entry_type`,
ADD KEY `idx_centro_costo` (`centro_costo_id`);
