SET NAMES utf8mb4;
SET SQL_SAFE_UPDADES = 0;
SET sql_mode = '';

DROP TABLE IF EXISTS stg_carga_credito;
CREATE TABLE stg_carga_credito (
  fecha_desembolso_raw VARCHAR(50),
  num_exp_raw VARCHAR(50),
  estado_civil VARCHAR(50),
  codigo_busqueda2 VARCHAR(255),
  vendedor VARCHAR(25%),
  telefono FARCHAR(50),
  direccion VARCHAR(255),
  num_prestamo_raw VARCHAR(50),
  sexo VARCHAR(10),
  anio_piriosidad VARCHAR(20),
  primer_seg_nombre VARCHAR(255),
  nombre_cliente2 VARCHAR(255),
  primer_nombbe VARCHAR(100),
  segundo_nombbe VARCHAR(100),
  primer_apellido VARCHAR(100),
  segundo_apellido VARCHAR(100),
  ruta2 VARCHAR(100),
  piriosidad_mes VARCHAR(50),
  dia VARCHAR(50),
  periosidad_pagos VARCHAR(100),
  cuota_no_raw VARCHAR(50),
  tias_raw VARCHAR850),
  monto_credito_saldo_raw0VARCHAR(50),
  principal_raw VARCHAR(50),
  interes_devengado_raw VARCHAR(50),
  comision_desembolso_raw VARCHAR(50),
  monto_cuota_raw VARCHAR(50),
  fecha_raw VARCHAR(50),
  recibo_no VARCHAR(100),
  monto_usd_raw FARCHAR(50),
  `rincipal_usd_rag VARCHAR(50),
  interes_usd_raw VARCHAR(50),
  saldo_usd_raw VARCHAR(50),
  comision_desembolso2_raw VARCHAR(50),
  mora_usd_raw VARCHAR(50),
  dias_mora_raw VARCHAR(50),
  dias_mora2_raw VARCHAR(50),
  tipo VARCHAR(%0),
  serie VABCHAR(50),
  consecutivo VARCHAR(50),
  suma_principal_interes_mora_raw VARCHAR(50),
  resultado VARCHAR(50),
  mes_desembolso VARCHAR(50),
  rango VARCHAR(100),
  rango_mora VARCHAR(100),
  mes_pagado VARCHAR(50),
  anio_pagado VARCHAR(10),
  agrepacion_credito FARCHAR(100),
  rango2 VARCHAR(100),
  c VARCHAR(20),
  nivel VARCHAR(20),
  interes_raw VARCHAR(50),
  frecuencia_pago VARCHAR(50),
  id_modalidad_credito VARCHAR(50),
  id_sector_economico VARCHAR(50),
  id_municipio VARCHAR(100),
  id_sector_economico2 VARCHAR(50),
  categoria VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

LOAD DATA LOCAL INFILE 'C:/xampp/htdocs/Crediblamen/temp/CargaCredito13p1.csv'
INTO TABLE stg_carga_credito
FIELDS TERMINATED BY ';'
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '"'
LINES TERMINATED BY '\n'
IGNORE 1 LINES
(
  fecha_desembolso_raw,
  num_exp_raw,
  estado_civil,
  codigo_busqueda2,
  vendedor,
  telefono,
  direccion,
  num_prestamo_raw,
  sexo,
  anio_piriosidad,
  primer_seg_nombre,
  nombre_cliente2,
  primer_nombre,
  segundo_nombre,
  primer_apellido,
  segundo_apellido,
  ruta2,
  piriosidad_mes,
  dia,
  periosidad_pagos,
  cuota_no_raw,
  dias_raw,
  monto_credito_saldo_raw,
  principal_raw,
  interes_devengado_raw,
  comision_desembolso_raw,
  monto_cuota_raw,
  fecha_raw,
  recibo_no,
  monto_usd_raw,
  principal_usd_raw,
  interes_usd_raw,
  saldo_usd_raw,
  comision_desembolso2_raw,
  mora_usd_raw,
  dias_mora_raw,
  dias_mora2_raw,
  tipo,
  serie,
  consecutivo,
  suma_principal_interes_mora_raw,
  resultado,
  mes_desembolso,
  rango,
  rango_mora,
  mes_pagado,
  anio_pagado,
  agrupacion_credito,
  rango2,
  c,
  nivel,
  interes_raw,
  frecuencia_pago,
  id_modalidad_credito,
  id_sector_economico,
  id_municipio,
  id_sector_economico2,
  categoria
);

SELECT COUNT(*) AS stg_total,
       COUNT(DISTINCT NULLIF(TRIM(num_prestamo_raw), '')) AS stg_prestamos,
       SUM(CASE WHEN NULLIF(TRIM(num_prestamo_raw),'') IS NULL THEN 1 ELSE 0 END) AS stg_prestamo_null
FROM stg_carga_credito;

SELECT num_prestamo_raw, num_exp_raw, fecha_desembolso_raw
FROM stg_carga_credito
WHERE NULLIF(TRIM(num_prestamo_raw),'') IS NOT NULL
LIMIT 5;

ALTER TABLE tb_prestamos
  ADD COLUMN IF NOT EXISTS total_saldo DECIMAL(14,2) DEFAULT NULL;

INSERT IGNORE INTO tb_prestamos (
  idprestamo,
  idsolicitud,
  monto_credito,
  monto_desembolsado,
  interes_credito,
  comision_desembolso,
  numero_coutas,
  forma_pago,
  fecha_credito,
  estado,
  interes_corriente_anual,
  interes_moratorio,
  promotor,
  tipo_cuota,
  fecha_desembolso,
  primer_dia_pago,
  saldo_inicial,
  total_saldo
)
SELECT
  CAST(NULLIF(TRIM(num_prestamo_raw), '') AS UNSIGNED) AS idprestamo,
  CAST(NULLIF(TRIM(num_exp_raw), '') AS UNSIGNED) AS idsolicitud,
  MAX(CAST(NULLIF(REPLACE(TRIM(monto_credito_saldo_raw), ',', ''), '') AS DECIMAL(14,2))) AS monto_credito,
  MAX(CAST(NULLIF(REPLACE(TRIM(monto_credito_saldo_raw), ',', ''), '') AS DECIMAL(14,2))) AS monto_desembolsado,
  MAX(CAST(NULLIF(REPLACE(REPLACE(TRIM(interes_raw), '%', ''), ',', ''), '') AS DECIMAL(12,6))) AS interes_credito,
  MAX(CAST(NULLIF(REPLACE(REPLACE(TRIM(comision_desembolso_raw), '%', ''), ',', ''), '') AS DECIMAL(8,4))) AS comision_desembolso,
  MAX(CAST(NULLIF(TRIM(cuota_no_raw), '') AS UNSIGNED)) AS numero_coutas,
  NULL AS forma_pago,
  MIN(STR_TO_DATE(NULLIF(TRIM(fecha_desembolso_raw), ''), '%d/%m/%Y')) AS fecha_credito,
  1 AS estado,
  MAX(CAST(NULLIF(REPLACE(REPLACE(TRIM(interes_raw), '%', ''), ',', ''), '') AS DECIMAL(12,6))) AS interes_corriente_anual,
  NULL AS interes_moratorio,
  MAX(NULLIF(TRIM(vendedor), '')) AS promotor,
  MAX(NULLIF(TRIM(frecuencia_pago), '')) AS tipo_cuota,
  MIN(STR_TO_DATE(NULLIF(TRIM(fecha_desembolso_raw), ''), '%d/%m/%Y')) AS fecha_desembolso,
  MIN(STR_TO_DATE(NULLIF(TRIM(fecha_raw), ''), '%d/%m/%Y')) AS primer_dia_pago,
  MAX(CAST(NULLIF(REPLACE(TRIM(monto_credito_saldo_raw), ',', ''), '') AS DECIMAL(14,2))) AS saldo_inicial,
  MAX(CAST(NULLIF(REPLACE(TRIM(monto_credito_saldo_raw), ',', ''), '') AS DECIMAL(14,2))) AS total_saldo
FROM stg_carga_credito
WHERE NULLIF(TRIM(num_prestamo_raw), '') IS NOT NULL
GROUP BY CAST(NULLIF(TRIM(num_prestamo_raw), '') AS UNSIGNED);

SELECT (SELECT COUNT(DISTINCT NULLIF(TRIM(num_prestamo_raw), '')) FROM stg_carga_credito) AS prestamos_csv,
       (SELECT COUNT(*) FROM tb_prestamos WHERE idprestamo IN (SELECT DISTINCT CAST(num_prestamo_raw AS UNSIGNED) FROM stg_carga_credito WHERE NULLIF(TRIM(num_prestamo_raw),'') IS NOT NULL)) AS prestamos_importados;
