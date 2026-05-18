-- Agregar columna tasa_venta a la tabla tb_tasa_cambio
-- El campo tasa_cambio actual se mantiene como "tasa_compra" conceptualmente
-- Agregamos tasa_venta para tener ambos tipos de cambio

ALTER TABLE tb_tasa_cambio 
ADD COLUMN tasa_venta DECIMAL(10,4) NULL AFTER tasa_cambio;

-- Comentario explicativo:
-- tasa_cambio = Tipo de Cambio COMPRA (cuánto paga el banco/casa de cambio por dólares)
-- tasa_venta = Tipo de Cambio VENTA (cuánto cobra el banco/casa de cambio por dólares)
-- Ejemplo: Compra C$36.50 / Venta C$37.00
