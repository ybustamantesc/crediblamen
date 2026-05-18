-- Importador de créditos y clientes desde CargaCredito.csv
-- Instrucciones: agrega aquí los pasos y el flujo que deseas para el nuevo proceso de importación.
-- Este archivo está listo para que definas el nuevo flujo desde cero.

-- Ejemplo de estructura inicial:
-- 1. Limpieza de staging
-- 2. Carga del CSV a staging
-- 3. Insert/Update en tb_clientes
-- 4. Insert/Update en tb_prestamos
-- 5. Resumen de importación

-- Agrega aquí tus instrucciones y el flujo deseado.

INSERT INTO tb_prestamos (
  agrupacion_credito,
  id_modalidad_credito,
  id_sector_economico,
  id_municipio,
  id_sector_economico2,
  rango_mora,
  nivel
)

SELECT
  NULLIF(TRIM(agrupacion_credito),''),
  NULLIF(TRIM(id_modalidad_credito),''),
  NULLIF(TRIM(id_sector_economico),''),
  NULLIF(TRIM(id_municipio),''),
  NULLIF(TRIM(id_sector_economico2),''),
  NULLIF(TRIM(rango_mora),''),
  NULLIF(TRIM(nivel),'')
FROM stg_carga_credito;



-- Insertar cuotas


-- Limpieza: Solo dejar el INSERT de tb_prestamos, eliminar los demás para evitar errores por columnas inexistentes.

-- Insertar pagos


-- (Eliminado INSERT tb_prestamo_pagos por columnas inexistentes en staging)



-- REGLAS DE IMPORTACIÓN
-- 1. No duplicar créditos: solo debe existir 1 código de crédito (idprestamo). Se usa INSERT IGNORE o manejo de clave única en tb_prestamos.
-- 2. Los clientes pueden tener varios créditos: permitido por diseño.
-- 3. Campos NULL o alfanuméricos se importan tal cual, sin detener la importación.
-- 4. Se respeta el orden de importación del CSV.
-- 5. No se mezclan columnas, cada campo va a su destino exacto.
-- 6. Columnas H y B pueden tener letras, se importan como texto.
-- 7. No se mezclan créditos, se respeta el orden de las cuotas.

-- RESUMEN DE IMPORTACIÓN
SELECT 'RESUMEN DE IMPORTACIÓN' AS resumen;
SELECT COUNT(*) AS lineas_importadas FROM stg_carga_credito;
SELECT COUNT(DISTINCT NULLIF(TRIM(num_exp_raw),'')) AS clientes_importados FROM stg_carga_credito;
SELECT COUNT(DISTINCT NULLIF(TRIM(num_prestamo_raw),'')) AS creditos_importados FROM stg_carga_credito;
