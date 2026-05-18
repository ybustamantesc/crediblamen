-- SQL: create table to store prestamo payments (separate from legacy tb_pagos)
CREATE TABLE IF NOT EXISTS `tb_prestamo_pagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idprestamo` int(11) NOT NULL,
  `idcuota` int(11) DEFAULT NULL,
  `idcliente` int(11) DEFAULT NULL,
  `monto_pagado` decimal(15,2) NOT NULL DEFAULT '0.00',
  `fecha_pago` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `metodo_pago` varchar(50) DEFAULT NULL,
  `referencia` varchar(100) DEFAULT NULL,
  `idserie` int(11) DEFAULT NULL,
  `dato_adicional` varchar(255) DEFAULT NULL,
  `idusuario` int(11) DEFAULT NULL,
  `anulado` tinyint(1) NOT NULL DEFAULT '0',
  `anulado_por` int(11) DEFAULT NULL,
  `anulado_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_idprestamo` (`idprestamo`),
  KEY `idx_idcliente` (`idcliente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
