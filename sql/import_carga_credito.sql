USE conta;

-- Asegurar que todos los campos del CSV existen en stg_carga_credito
ALTER TABLE stg_carga_credito 
  ADD COLUMN IF NOT EXISTS nombre_columna VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS cedula_cliente VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS cedula_promotor VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS id_tipo_zona VARCHAR(255) NULL;
USE conta;
-- Insertar o actualizar préstamos desde staging con lógica de campos clave
-- Ajuste: Importación alineada con columnas del CSV y staging
INSERT INTO tb_prestamos (
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
  created_at
)
SELECT
  TRIM(num_prestamo_raw),
  TRIM(num_exp_raw),
  TRIM(monto_credito_saldo_raw),
  TRIM(monto_credito_saldo_raw),
  TRIM(interes_raw),
  TRIM(comision_desembolso_raw),
  TRIM(cuota_no_raw),
  TRIM(periosidad_pagos),
  STR_TO_DATE(TRIM(fecha_desembolso_raw), '%d/%m/%Y'),
  1,
  NOW()
FROM stg_carga_credito
WHERE TRIM(num_prestamo_raw) IS NOT NULL AND TRIM(num_prestamo_raw) != '';
-- ...existing code...
USE conta;
SET SQL_SAFE_UPDATES = 0;
SET sql_mode = '';
SET FOREIGN_KEY_CHECKS = 0;

-- Removed unnecessary prepare and execute statements
-- Removed unnecessary prepare and execute statements
-- Removed unnecessary prepare and execute statements




-- 2) Carga del CSV (ruta literal)
LOAD DATA LOCAL INFILE 'C:/xampp/htdocs/Conta/temp/CargaCredito.csv'
INTO TABLE stg_carga_credito
FIELDS TERMINATED BY ','
OPTIONALLY ENCLOSED BY '"'
ESCAPED BY '"'
LINES TERMINATED BY '\r\n'
IGNORE 1 LINES
(
  fecha_desembolso_raw,
  num_exp_raw,
  estado_civil,
  codigo_busqueda2,
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

-- Conteos en staging
SELECT COUNT(DISTINCT NULLIF(TRIM(num_prestamo_raw), ''))
  INTO @stg_prestamos
  FROM stg_carga_credito
  WHERE NULLIF(TRIM(num_prestamo_raw), '') IS NOT NULL;

-- 3) Insertar/actualizar solicitudes (para reflejar cliente/datos generales)
  idsolicitud,
  apellidos,
  nombres,
  direccion,
  telefono,
  numero_doc,
  estado_civil,
  promotor,
  tipo_solicitud,
  tipo_credito
)
SELECT
  NULLIF(TRIM(num_exp_raw), '') AS idsolicitud,
  NULLIF(TRIM(CONCAT(COALESCE(primer_apellido,''), ' ', COALESCE(segundo_apellido,''))), '') AS apellidos,
  NULLIF(TRIM(CONCAT(COALESCE(primer_nombre,''), ' ', COALESCE(segundo_nombre,''))), '') AS nombres,
  NULLIF(TRIM(direccion), '') AS direccion,
  NULLIF(TRIM(telefono), '') AS telefono,
  COALESCE(NULLIF(TRIM(num_exp_raw), ''), NULLIF(TRIM(num_prestamo_raw), ''), '0') AS numero_doc,
  NULLIF(TRIM(estado_civil), '') AS estado_civil,
  NULLIF(TRIM(agrupacion_credito), '') AS tipo_solicitud,
  NULLIF(TRIM(id_modalidad_credito), '') AS tipo_credito
FROM stg_carga_credito
WHERE NULLIF(TRIM(num_prestamo_raw), '') IS NOT NULL
GROUP BY TRIM(num_exp_raw)
ON DUPLICATE KEY UPDATE
  apellidos = COALESCE(VALUES(apellidos), apellidos),
  nombres = COALESCE(VALUES(nombres), nombres),
  direccion = COALESCE(VALUES(direccion), direccion),
  telefono = COALESCE(VALUES(telefono), telefono),
  estado_civil = COALESCE(VALUES(estado_civil), estado_civil),
  promotor = COALESCE(VALUES(promotor), promotor),
  tipo_solicitud = COALESCE(VALUES(tipo_solicitud), tipo_solicitud),
  tipo_credito = COALESCE(VALUES(tipo_credito), tipo_credito);

-- Limpieza para reimportar préstamos del CSV
WHERE (
  @only_prestamo IS NOT NULL AND idprestamo = @only_prestamo
) OR (
  @only_prestamo IS NULL AND idprestamo IN (
    SELECT DISTINCT TRIM(num_prestamo_raw)
    FROM stg_carga_credito
    WHERE NULLIF(TRIM(num_prestamo_raw), '') IS NOT NULL
  )
);
DELETE FROM tb_prestamo_cuotas
WHERE (
  @only_prestamo IS NOT NULL AND idprestamo = @only_prestamo
) OR (
  @only_prestamo IS NULL AND idprestamo IN (
    SELECT DISTINCT TRIM(num_prestamo_raw)
    FROM stg_carga_credito
    WHERE NULLIF(TRIM(num_prestamo_raw), '') IS NOT NULL
  )
);
DELETE FROM tb_prestamos
WHERE (
  @only_prestamo IS NOT NULL AND idprestamo = @only_prestamo
) OR (
  @only_prestamo IS NULL AND idprestamo IN (
    SELECT DISTINCT TRIM(num_prestamo_raw)
    FROM stg_carga_credito
    WHERE NULLIF(TRIM(num_prestamo_raw), '') IS NOT NULL
  )
);

SET @has_total_saldo := (
  SELECT COUNT(*)
  FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'tb_prestamos'
    AND COLUMN_NAME = 'total_saldo'
);

INSERT IGNORE INTO tb_prestamos (
  idprestamo, idsolicitud, monto_credito, monto_desembolsado, interes_credito, comision_desembolso, numero_coutas, forma_pago, fecha_credito, estado, interes_corriente_anual, interes_moratorio, promotor, tipo_cuota, fecha_desembolso, primer_dia_pago, saldo_inicial, total_saldo, agrupacion_credito, id_modalidad_credito, id_sector_economico, id_municipio, id_sector_economico2, rango_mora, nivel,
  codigo_busqueda2, sexo, anio_piriosidad, primer_seg_nombre, nombre_cliente2, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, ruta2, piriosidad_mes, dia, periosidad_pagos, cuota_no_raw, dias_raw, monto_credito_saldo_raw, principal_raw, interes_devengado_raw, comision_desembolso_raw, monto_cuota_raw, fecha_raw, recibo_no, monto_usd_raw, principal_usd_raw, interes_usd_raw, saldo_usd_raw, comision_desembolso2_raw, mora_usd_raw, dias_mora_raw, dias_mora2_raw, tipo, serie, consecutivo, suma_principal_interes_mora_raw, resultado, mes_desembolso, rango, rango_mora, mes_pagado, anio_pagado, rango2, c, interes_raw, frecuencia_pago, categoria, cedula_cliente, cedula_promotor, id_tipo_zona
)
SELECT
  TRIM(num_prestamo_raw),
  TRIM(num_exp_raw),
  TRIM(monto_credito_saldo_raw),
  TRIM(monto_credito_saldo_raw),
  TRIM(interes_raw),
  TRIM(comision_desembolso_raw),
  TRIM(cuota_no_raw),
  TRIM(periosidad_pagos),
  STR_TO_DATE(TRIM(fecha_desembolso_raw), '%d/%m/%Y'),
  1,
  TRIM(interes_raw),
  NULL,
  TRIM(frecuencia_pago),
  STR_TO_DATE(TRIM(fecha_desembolso_raw), '%d/%m/%Y'),
  STR_TO_DATE(TRIM(fecha_raw), '%d/%m/%Y'),
  TRIM(monto_credito_saldo_raw),
  TRIM(monto_credito_saldo_raw),
  TRIM(agrupacion_credito),
  TRIM(id_modalidad_credito),
  TRIM(id_sector_economico),
  TRIM(id_municipio),
  TRIM(id_sector_economico2),
  TRIM(rango_mora),
  TRIM(nivel),
  TRIM(codigo_busqueda2),
  TRIM(sexo),
  TRIM(anio_piriosidad),
  TRIM(primer_seg_nombre),
  TRIM(nombre_cliente2),
  TRIM(primer_nombre),
  TRIM(segundo_nombre),
  TRIM(primer_apellido),
  TRIM(segundo_apellido),
  TRIM(ruta2),
  TRIM(piriosidad_mes),
  TRIM(dia),
  TRIM(periosidad_pagos),
  TRIM(cuota_no_raw),
  TRIM(dias_raw),
  TRIM(monto_credito_saldo_raw),
  TRIM(principal_raw),
  TRIM(interes_devengado_raw),
  TRIM(comision_desembolso_raw),
  TRIM(monto_cuota_raw),
  TRIM(fecha_raw),
  TRIM(recibo_no),
  TRIM(monto_usd_raw),
  TRIM(principal_usd_raw),
  TRIM(interes_usd_raw),
  TRIM(saldo_usd_raw),
  TRIM(comision_desembolso2_raw),
  TRIM(mora_usd_raw),
  TRIM(dias_mora_raw),
  TRIM(dias_mora2_raw),
  TRIM(tipo),
  TRIM(serie),
  TRIM(consecutivo),
  TRIM(suma_principal_interes_mora_raw),
  TRIM(resultado),
  TRIM(mes_desembolso),
  TRIM(rango),
  TRIM(rango_mora),
  TRIM(mes_pagado),
  TRIM(anio_pagado),
  TRIM(rango2),
  TRIM(c),
  TRIM(interes_raw),
  TRIM(frecuencia_pago),
  TRIM(categoria),
  TRIM(cedula_cliente),
  TRIM(id_tipo_zona)
FROM stg_carga_credito
WHERE TRIM(num_prestamo_raw) IS NOT NULL AND TRIM(num_prestamo_raw) != '';

 -- PREPARE stmt_insert_prestamos FROM @sql_insert_prestamos;
 -- EXECUTE stmt_insert_prestamos;
 -- DEALLOCATE PREPARE stmt_insert_prestamos;
 -- PREPARE stmt_add_total_saldo FROM @sql_add_total_saldo;
 -- DEALLOCATE PREPARE stmt_add_total_saldo;
// DEALLOCATE PREPARE stmt_add_total_saldo;
-- Conteo de préstamos importados desde este staging
SELECT COUNT(*)
  INTO @imported_prestamos
  FROM tb_prestamos
  WHERE idprestamo IN (
    SELECT DISTINCT TRIM(num_prestamo_raw)
    FROM stg_carga_credito
    WHERE NULLIF(TRIM(num_prestamo_raw), '') IS NOT NULL
  );

INSERT INTO import_log (csv_file, stg_rows, stg_prestamos, imported_prestamos)
VALUES (@csv_file, @stg_rows, @stg_prestamos, @imported_prestamos);
-- 5) Insertar cuotas (solo filas con número de cuota)
INSERT IGNORE INTO tb_prestamo_cuotas (
  idprestamo,
  numero,
  fecha_vencimiento,
  dias,
  principal,
  interes,
  cuota,
  saldo,
  comision
)
SELECT
  TRIM(s.num_prestamo_raw) AS idprestamo,
  TRIM(s.cuota_no_raw) AS numero,
  COALESCE(
    STR_TO_DATE(NULLIF(TRIM(s.fecha_raw), ''), '%d/%m/%Y'),
    STR_TO_DATE(NULLIF(TRIM(s.periosidad_pagos), ''), '%d/%m/%Y')
  ) AS fecha_vencimiento,
  CAST(NULLIF(TRIM(s.dias_raw), '') AS UNSIGNED) AS dias,
  CAST(NULLIF(REPLACE(TRIM(s.principal_raw), ',', ''), '') AS DECIMAL(14,2)) AS principal,
  CAST(NULLIF(REPLACE(TRIM(s.interes_devengado_raw), ',', ''), '') AS DECIMAL(14,2)) AS interes,
  CAST(NULLIF(REPLACE(TRIM(s.monto_cuota_raw), ',', ''), '') AS DECIMAL(14,2)) AS cuota,
  CAST(NULLIF(REPLACE(TRIM(s.monto_credito_saldo_raw), ',', ''), '') AS DECIMAL(14,2)) AS saldo,
  CAST(NULLIF(REPLACE(TRIM(COALESCE(s.comision_desembolso2_raw, s.comision_desembolso_raw)), ',', ''), '') AS DECIMAL(12,4)) AS comision
FROM stg_carga_credito s
WHERE NULLIF(TRIM(s.num_prestamo_raw), '') IS NOT NULL
  referencia,
  idserie,
  dato_adicional,
  dias_mora_raw,
  rango_mora,
  nivel
)
SELECT
  TRIM(s.num_prestamo_raw) AS idprestamo,
  c.idcuota AS idcuota,
  CAST(NULLIF(REPLACE(TRIM(s.monto_usd_raw), ',', ''), '') AS DECIMAL(15,2)) AS monto_pagado,
  COALESCE(
    STR_TO_DATE(NULLIF(TRIM(s.fecha_raw), ''), '%d/%m/%Y'),
    STR_TO_DATE(NULLIF(TRIM(s.periosidad_pagos), ''), '%d/%m/%Y')
  ) AS fecha_pago,
  NULLIF(TRIM(s.tipo), '') AS metodo_pago,
  NULLIF(TRIM(s.recibo_no), '') AS referencia,
  NULLIF(TRIM(s.consecutivo), '') AS dato_adicional,
  CAST(NULLIF(TRIM(s.dias_mora_raw), '') AS UNSIGNED) AS dias_mora_raw,
  NULLIF(TRIM(s.rango_mora), '') AS rango_mora,
  NULLIF(TRIM(s.nivel), '') AS nivel
FROM stg_carga_credito s
LEFT JOIN tb_prestamo_cuotas c
  ON c.idprestamo = TRIM(s.num_prestamo_raw)
 AND c.numero = TRIM(s.cuota_no_raw)
WHERE NULLIF(TRIM(s.num_prestamo_raw), '') IS NOT NULL
  AND NULLIF(TRIM(s.monto_usd_raw), '') IS NOT NULL
  AND CAST(NULLIF(REPLACE(TRIM(s.monto_usd_raw), ',', ''), '') AS DECIMAL(15,2)) > 0
  AND (@only_prestamo IS NULL OR TRIM(s.num_prestamo_raw) = @only_prestamo);


SET FOREIGN_KEY_CHECKS = 1;

-- Resumen de importación
SELECT
  @csv_file AS csv_file,
  @stg_rows AS stg_rows,
  @imported_prestamos AS imported_prestamos;

-- Fin
-- Insertar cuotas desde staging (tb_prestamo_cuotas)
INSERT INTO tb_prestamo_cuotas (
  idcuota,
  idprestamo,
  numero,
  fecha_vencimiento,
  dias,
  principal,
  interes,
  cuota,
  saldo,
  comision,
  dias_mora_raw,
  cuota_no_raw,
  fecha_raw,
  dias_raw,
  principal_raw,
  interes_devengado_raw,
  monto_cuota_raw,
  saldo_usd_raw,
  monto_credito_saldo_raw,
  comision_desembolso_raw
)
SELECT
  cuota_no_raw, -- idcuota desde columna U
  num_prestamo_raw,
  cuota_no_raw,
  fecha_raw,
  dias_raw,
  principal_raw,
  interes_devengado_raw,
  monto_cuota_raw,
  saldo_usd_raw,
  comision_desembolso_raw,
  dias_mora_raw,
  cuota_no_raw,
  fecha_raw,
  dias_raw,
  principal_raw,
  interes_devengado_raw,
  monto_cuota_raw,
  saldo_usd_raw,
  monto_credito_saldo_raw,
  comision_desembolso_raw
FROM stg_carga_credito
WHERE num_prestamo_raw IS NOT NULL AND cuota_no_raw IS NOT NULL;

-- Insertar pagos desde staging (tb_prestamo_pagos)
INSERT INTO tb_prestamo_pagos (
  id,
  idprestamo,
  idcuota,
  monto_pagado,
  fecha_pago,
  referencia,
  idserie,
  idusuario,
  dias_mora_raw,
  rango_mora,
  nivel,
  idcliente,
  metodo_pago,
  dato_adicional
)
SELECT
  cuota_no_raw, -- id desde columna U
  num_prestamo_raw,
  cuota_no_raw,
  monto_usd_raw,
  fecha_raw,
  recibo_no,
  serie, -- idserie desde columna AC
  NULL,
  dias_mora_raw,
  rango_mora,
  nivel,
  num_exp_raw, -- idcliente desde columna B
  tipo,
  resultado
FROM stg_carga_credito
WHERE num_prestamo_raw IS NOT NULL AND cuota_no_raw IS NOT NULL;
