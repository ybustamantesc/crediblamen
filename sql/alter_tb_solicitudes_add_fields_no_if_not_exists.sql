-- Script explícito: ALTER TABLE ADD COLUMN para `tb_solicitudes`
-- Ejecutar sólo UNA vez y sólo después de hacer backup:
-- mysqldump -u <usuario> -p <nombre_db> tb_solicitudes > tb_solicitudes.bak.sql
--
-- Nota: este archivo contiene ALTERs explícitos (sin checks). Si alguna columna ya existe
-- MySQL devolverá un error. Si eso ocurre, comentar o eliminar la línea correspondiente
-- y continuar con el resto.

-- ALTER TABLE tb_solicitudes ADD COLUMN ventas_dias_buenos INT NULL;
-- ALTER TABLE tb_solicitudes ADD COLUMN ventas_dias_malos INT NULL;
ALTER TABLE tb_solicitudes ADD COLUMN ventas_promedio_diarios DECIMAL(15,2) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN ventas_promedio_mensual DECIMAL(15,2) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN giro_negocio VARCHAR(255) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN monto_solicitado DECIMAL(15,2) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN plazo_meses INT NULL;
ALTER TABLE tb_solicitudes ADD COLUMN frecuencia VARCHAR(100) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN tasa_interes DECIMAL(8,4) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN cuota_estim_estimada DECIMAL(15,2) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN garantia VARCHAR(255) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN otros_ingresos_detalle TEXT NULL;
ALTER TABLE tb_solicitudes ADD COLUMN detalle_inventario TEXT NULL;
ALTER TABLE tb_solicitudes ADD COLUMN cuentas_por_cobrar_amount DECIMAL(15,2) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN caja_amount DECIMAL(15,2) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN banco_amount DECIMAL(15,2) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN pago_alquiler DECIMAL(15,2) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN pago_trabajadores DECIMAL(15,2) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN energia DECIMAL(15,2) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN agua DECIMAL(15,2) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN internet DECIMAL(15,2) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN promotor VARCHAR(255) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN fecha_recepcion DATETIME NULL;
ALTER TABLE tb_solicitudes ADD COLUMN observaciones TEXT NULL;
ALTER TABLE tb_solicitudes ADD COLUMN nombre_conyuge VARCHAR(255) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN ocupacion_conyuge VARCHAR(255) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN telefono_conyuge VARCHAR(100) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN numero_dependientes INT NULL;
ALTER TABLE tb_solicitudes ADD COLUMN fecha_nacimiento DATE NULL;
ALTER TABLE tb_solicitudes ADD COLUMN edad INT NULL;
ALTER TABLE tb_solicitudes ADD COLUMN nombre_empresa VARCHAR(255) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN direccion_empresa VARCHAR(255) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN telefono_empresa VARCHAR(100) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN cargo_puesto VARCHAR(150) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN ingreso_mensual_neto DECIMAL(15,2) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN nombre_negocio VARCHAR(255) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN actividad_economica VARCHAR(255) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN ubicacion_negocio VARCHAR(255) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN telefono_negocio VARCHAR(100) NULL;
ALTER TABLE tb_solicitudes ADD COLUMN numero_empleados INT NULL;
ALTER TABLE tb_solicitudes ADD COLUMN otros_gastos TEXT NULL;

-- Fin del script explícito.

-- Columnas booleanas / flags comunes añadidas ahora
ALTER TABLE tb_solicitudes ADD COLUMN es_nuevo TINYINT(1) NULL DEFAULT 0;
ALTER TABLE tb_solicitudes ADD COLUMN es_renovacion TINYINT(1) NULL DEFAULT 0;
ALTER TABLE tb_solicitudes ADD COLUMN negocio_propio TINYINT(1) NULL DEFAULT 0;
ALTER TABLE tb_solicitudes ADD COLUMN cedula_vigente TINYINT(1) NULL DEFAULT 0;
ALTER TABLE tb_solicitudes ADD COLUMN otros_ingresos TINYINT(1) NULL DEFAULT 0;
ALTER TABLE tb_solicitudes ADD COLUMN ahorros TINYINT(1) NULL DEFAULT 0;
ALTER TABLE tb_solicitudes ADD COLUMN cuentas_por_cobrar TINYINT(1) NULL DEFAULT 0;
ALTER TABLE tb_solicitudes ADD COLUMN inventario_disponible TINYINT(1) NULL DEFAULT 0;
