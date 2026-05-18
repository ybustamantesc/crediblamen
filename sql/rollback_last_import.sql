SET NAMES utf8mb4;
SET SQL_SAFE_UPDATES = 0;

DROP TABLE IF EXISTS stg_carga_credito;
CREATE TABLE stg_carga_credito (
  num_exp_raw VARCHAR(50),
  num_prestamo_raw VARCHAR(50)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOAD DATA LOCAL INFILE 'C:/xampp/htdocs/Crediblamen/temp/CargaCredito.csv'
INTO TABLE stg_carga_credito
FIELDS TERMINATED BY ';'
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '"'
LINES TERMINATED BY '\r\n'
IGNORE 1 LINES
(
  @fecha_desembolso_raw,
  @num_exp_raw,
  @estado_civil,
  @codigo_busqueda2,
  @vendedor,
  @telefono,
  @direccion,
  @num_prestamo_raw,
  @sexo,
  @anio_piriosidad,
  @primer_seg_nombre,
  @nombre_cliente2,
  @primer_nombre,
  @segundo_nombre,
  @primer_apellido,
  @segundo_apellido,
  @ruta2,
  @piriosidad_mes,
  @dia,
  @periosidad_pagos,
  @cuota_no_raw,
  @dias_raw,
  @monto_credito_saldo_raw,
  @principal_raw,
  @interes_devengado_raw,
  @comision_desembolso_raw,
  @monto_cuota_raw,
  @fecha_raw,
  @recibo_no,
  @monto_usd_raw,
  @principal_usd_raw,
  @interes_usd_raw,
  @saldo_usd_raw,
  @comision_desembolso2_raw,
  @mora_usd_raw,
  @dias_mora_raw,
  @dias_mora2_raw,
  @tipo,
  @serie,
  @consecutivo,
  @suma_principal_interes_mora_raw,
  @resultado,
  @mes_desembolso,
  @rango,
  @rango_mora,
  @mes_pagado,
  @anio_pagado,
  @agrupacion_credito,
  @rango2,
  @c,
  @nivel,
  @interes_raw,
  @frecuencia_pago,
  @id_modalidad_credito,
  @id_sector_economico,
  @id_municipio,
  @id_sector_economico2,
  @categoria
)
SET
  num_exp_raw = NULLIF(TRIM(@num_exp_raw), ''),
  num_prestamo_raw = NULLIF(TRIM(@num_prestamo_raw), '');

DELETE FROM tb_prestamo_pagos
WHERE idprestamo IN (
  SELECT DISTINCT CAST(num_prestamo_raw AS UNSIGNED)
  FROM stg_carga_credito
  WHERE num_prestamo_raw IS NOT NULL
);

DELETE FROM tb_prestamo_cuotas
WHERE idprestamo IN (
  SELECT DISTINCT CAST(num_prestamo_raw AS UNSIGNED)
  FROM stg_carga_credito
  WHERE num_prestamo_raw IS NOT NULL
);

DELETE FROM tb_prestamos
WHERE idprestamo IN (
  SELECT DISTINCT CAST(num_prestamo_raw AS UNSIGNED)
  FROM stg_carga_credito
  WHERE num_prestamo_raw IS NOT NULL
);

DELETE s FROM tb_solicitudes s
WHERE s.idsolicitud IN (
  SELECT DISTINCT CAST(num_exp_raw AS UNSIGNED)
  FROM stg_carga_credito
  WHERE num_exp_raw IS NOT NULL
)
AND s.idsolicitud NOT IN (SELECT DISTINCT idsolicitud FROM tb_prestamos);
