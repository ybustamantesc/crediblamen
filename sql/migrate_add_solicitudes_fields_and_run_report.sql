-- Script combinado: agrega columnas faltantes en `tb_solicitudes` si no existen y luego ejecuta el reporte de completitud.
-- Haz un backup antes de ejecutar: mysqldump -u user -p nombre_db tb_solicitudes > tb_solicitudes.bak.sql

-- Para cada columna: construyo y ejecuto un ALTER TABLE sólo si la columna no existe.

-- 1) ventas_dias_buenos (ejemplo que causó #1054)
SET @col := 'ventas_dias_buenos';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' INT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Tabla para almacenar notas colaborativas separadas de los comentarios históricos
SET @tbl := 'tb_solicitudes_notes';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@tbl)=0,
               CONCAT('CREATE TABLE ', @tbl, " (idnote INT NOT NULL AUTO_INCREMENT PRIMARY KEY, idsolicitud INT NOT NULL, user_id INT NULL, username VARCHAR(150) NULL, note TEXT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX (idsolicitud));"),
               'SELECT "table_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Repetir para todas las columnas referenciadas por el reporte
SET @col := 'ventas_dias_malos';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' INT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'ventas_promedio_diarios';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(15,2) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'ventas_promedio_mensual';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(15,2) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'giro_negocio';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(255) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'monto_solicitado';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(15,2) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'plazo_meses';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' INT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'frecuencia';
-- Asegurar que `frecuencia` es una lista restringida (Diaria, Semanal, Quincenal, Mensual).
SET @desired := "enum('Diaria','Semanal','Quincenal','Mensual')";
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
         CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' ', @desired, ' NULL;'),
         IF((SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col) <> @desired,
          CONCAT('ALTER TABLE tb_solicitudes MODIFY COLUMN ', @col, ' ', @desired, ' NULL;'),
          'SELECT "col_ok";')
        );
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'tasa_interes';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(8,4) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'cuota_estim_estimada';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(15,2) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'garantia';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(255) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'otros_ingresos_detalle';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' TEXT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'detalle_inventario';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' TEXT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Montos de activos/caja/banco/cxc
SET @cols := 'cuentas_por_cobrar_amount,caja_amount,banco_amount';
-- cuentas_por_cobrar_amount
SET @col := 'cuentas_por_cobrar_amount';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(15,2) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
-- caja_amount
SET @col := 'caja_amount';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(15,2) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
-- banco_amount
SET @col := 'banco_amount';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(15,2) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Gastos habituales
SET @cols2 := 'pago_alquiler,pago_trabajadores,energia,agua,internet';
SET @col := 'pago_alquiler';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(15,2) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'pago_trabajadores';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(15,2) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'energia';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(15,2) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'agua';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(15,2) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'internet';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(15,2) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Datos personales y conyuge
SET @col := 'promotor';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(255) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Tipo de documento: Lista controlada (Cedula, Pasaporte)
SET @col := 'tipo_documento';
SET @desired := "enum('Cedula','Pasaporte')";
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
         CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' ', @desired, ' NULL;'),
         IF((SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col) <> @desired,
          CONCAT('ALTER TABLE tb_solicitudes MODIFY COLUMN ', @col, ' ', @desired, ' NULL;'),
          'SELECT "col_ok";')
        );
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'fecha_recepcion';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DATETIME NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'observaciones';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' TEXT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'nombre_conyuge';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(255) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'ocupacion_conyuge';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(255) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'telefono_conyuge';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(100) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'numero_dependientes';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' INT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'fecha_nacimiento';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DATE NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'edad';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' INT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Datos laborales / negocio
SET @col := 'nombre_empresa';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(255) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'direccion_empresa';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(255) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'telefono_empresa';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(100) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'cargo_puesto';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(150) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'ingreso_mensual_neto';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(15,2) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'nombre_negocio';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(255) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'actividad_economica';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(255) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'ubicacion_negocio';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(255) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'telefono_negocio';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(100) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'numero_empleados';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' INT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'otros_gastos';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' TEXT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Columnas booleanas / flags y otros campos detectados en el formulario
SET @col := 'es_nuevo';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' TINYINT(1) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'es_renovacion';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' TINYINT(1) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'negocio_propio';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' TINYINT(1) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'negocio_antiguedad';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' INT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'matricula_permiso';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' TINYINT(1) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'cedula_vigente';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' TINYINT(1) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'ingreso_promedio_alto';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(15,2) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'ingreso_promedio_bajo';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(15,2) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'otros_ingresos';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' TINYINT(1) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'ahorros';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' TINYINT(1) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'margen_comercial';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(12,4) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'gastos_fijos';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(15,2) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'gastos_operativos';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' TEXT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'datos_personales';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' TEXT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'datos_conyuge';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' TEXT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'dni_conyuge';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(100) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'salario_conyuge';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(15,2) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'recibo_servicios';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' TINYINT(1) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'investigacion_vecinos';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' TINYINT(1) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'referencias_personales';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' TEXT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'barrio';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(255) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'municipio';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(255) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'tiempo_residir_anios';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' INT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'tiempo_residir_meses';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' INT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'condicion_vivienda';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(100) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'tipo_credito';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(100) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'tipo_solicitud';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(100) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Estado civil: lista controlada (Soltero, Casado, Viudo)
SET @col := 'estado_civil';
SET @desired := "enum('Soltero','Casado','Viudo')";
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
         CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' ', @desired, ' NULL;'),
         IF((SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col) <> @desired,
          CONCAT('ALTER TABLE tb_solicitudes MODIFY COLUMN ', @col, ' ', @desired, ' NULL;'),
          'SELECT "col_ok";')
        );
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'uso_credito';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(255) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'tiempo_empleo_anios';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' INT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'tiempo_empleo_meses';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' INT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'tipo_contrato';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' VARCHAR(100) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'deducciones';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' DECIMAL(15,2) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'tiempo_operacion_anios';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' INT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'tiempo_operacion_meses';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' INT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @col := 'propiedad_negocio';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' TINYINT(1) NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
 
-- Columna 'estado' (estado del registro) — evitar error 1366 cuando se envía cadena vacía
SET @col := 'estado';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='tb_solicitudes' AND COLUMN_NAME=@col)=0,
               CONCAT('ALTER TABLE tb_solicitudes ADD COLUMN ', @col, ' INT NULL;'),
               'SELECT "col_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
-- FIN de ALTERs idempotentes. Ahora ejecutar el SELECT del reporte.

-- Reporte de completitud: muestra cuántos registros tienen cada campo llenado
SELECT
  COUNT(*) AS total_registros,
  SUM(CASE WHEN TRIM(IFNULL(giro_negocio,'')) <> '' THEN 1 ELSE 0 END) AS giro_negocio_filled,
  SUM(CASE WHEN monto_solicitado IS NOT NULL THEN 1 ELSE 0 END) AS monto_solicitado_filled,
  SUM(CASE WHEN plazo_meses IS NOT NULL THEN 1 ELSE 0 END) AS plazo_meses_filled,
  SUM(CASE WHEN TRIM(IFNULL(frecuencia,'')) <> '' THEN 1 ELSE 0 END) AS frecuencia_filled,
  SUM(CASE WHEN tasa_interes IS NOT NULL THEN 1 ELSE 0 END) AS tasa_interes_filled,
  SUM(CASE WHEN cuota_estim_estimada IS NOT NULL THEN 1 ELSE 0 END) AS cuota_estim_estimada_filled,
  SUM(CASE WHEN TRIM(IFNULL(garantia,'')) <> '' THEN 1 ELSE 0 END) AS garantia_filled,
  SUM(CASE WHEN TRIM(IFNULL(otros_ingresos_detalle,'')) <> '' THEN 1 ELSE 0 END) AS otros_ingresos_detalle_filled,
  SUM(CASE WHEN ventas_promedio_diarios IS NOT NULL THEN 1 ELSE 0 END) AS ventas_promedio_diarios_filled,
  SUM(CASE WHEN ventas_promedio_mensual IS NOT NULL THEN 1 ELSE 0 END) AS ventas_promedio_mensual_filled,
  SUM(CASE WHEN ventas_dias_buenos IS NOT NULL THEN 1 ELSE 0 END) AS ventas_dias_buenos_filled,
  SUM(CASE WHEN ventas_dias_malos IS NOT NULL THEN 1 ELSE 0 END) AS ventas_dias_malos_filled,
  SUM(CASE WHEN TRIM(IFNULL(detalle_inventario,'')) <> '' THEN 1 ELSE 0 END) AS detalle_inventario_filled,
  SUM(CASE WHEN cuentas_por_cobrar_amount IS NOT NULL THEN 1 ELSE 0 END) AS cuentas_por_cobrar_amount_filled,
  SUM(CASE WHEN caja_amount IS NOT NULL THEN 1 ELSE 0 END) AS caja_amount_filled,
  SUM(CASE WHEN banco_amount IS NOT NULL THEN 1 ELSE 0 END) AS banco_amount_filled,
  SUM(CASE WHEN pago_alquiler IS NOT NULL THEN 1 ELSE 0 END) AS pago_alquiler_filled,
  SUM(CASE WHEN pago_trabajadores IS NOT NULL THEN 1 ELSE 0 END) AS pago_trabajadores_filled,
  SUM(CASE WHEN energia IS NOT NULL THEN 1 ELSE 0 END) AS energia_filled,
  SUM(CASE WHEN agua IS NOT NULL THEN 1 ELSE 0 END) AS agua_filled,
  SUM(CASE WHEN internet IS NOT NULL THEN 1 ELSE 0 END) AS internet_filled,
  SUM(CASE WHEN TRIM(IFNULL(promotor,'')) <> '' THEN 1 ELSE 0 END) AS promotor_filled,
  SUM(CASE WHEN fecha_recepcion IS NOT NULL THEN 1 ELSE 0 END) AS fecha_recepcion_filled,
  SUM(CASE WHEN TRIM(IFNULL(observaciones,'')) <> '' THEN 1 ELSE 0 END) AS observaciones_filled,
  SUM(CASE WHEN TRIM(IFNULL(nombre_conyuge,'')) <> '' THEN 1 ELSE 0 END) AS nombre_conyuge_filled,
  SUM(CASE WHEN TRIM(IFNULL(ocupacion_conyuge,'')) <> '' THEN 1 ELSE 0 END) AS ocupacion_conyuge_filled,
  SUM(CASE WHEN TRIM(IFNULL(telefono_conyuge,'')) <> '' THEN 1 ELSE 0 END) AS telefono_conyuge_filled,
  SUM(CASE WHEN numero_dependientes IS NOT NULL THEN 1 ELSE 0 END) AS numero_dependientes_filled,
  SUM(CASE WHEN fecha_nacimiento IS NOT NULL THEN 1 ELSE 0 END) AS fecha_nacimiento_filled,
  SUM(CASE WHEN edad IS NOT NULL THEN 1 ELSE 0 END) AS edad_filled,
  SUM(CASE WHEN TRIM(IFNULL(nombre_empresa,'')) <> '' THEN 1 ELSE 0 END) AS nombre_empresa_filled,
  SUM(CASE WHEN TRIM(IFNULL(direccion_empresa,'')) <> '' THEN 1 ELSE 0 END) AS direccion_empresa_filled,
  SUM(CASE WHEN TRIM(IFNULL(telefono_empresa,'')) <> '' THEN 1 ELSE 0 END) AS telefono_empresa_filled,
  SUM(CASE WHEN TRIM(IFNULL(cargo_puesto,'')) <> '' THEN 1 ELSE 0 END) AS cargo_puesto_filled,
  SUM(CASE WHEN ingreso_mensual_neto IS NOT NULL THEN 1 ELSE 0 END) AS ingreso_mensual_neto_filled,
  SUM(CASE WHEN TRIM(IFNULL(nombre_negocio,'')) <> '' THEN 1 ELSE 0 END) AS nombre_negocio_filled,
  SUM(CASE WHEN TRIM(IFNULL(actividad_economica,'')) <> '' THEN 1 ELSE 0 END) AS actividad_economica_filled,
  SUM(CASE WHEN TRIM(IFNULL(ubicacion_negocio,'')) <> '' THEN 1 ELSE 0 END) AS ubicacion_negocio_filled,
  SUM(CASE WHEN TRIM(IFNULL(telefono_negocio,'')) <> '' THEN 1 ELSE 0 END) AS telefono_negocio_filled,
  SUM(CASE WHEN numero_empleados IS NOT NULL THEN 1 ELSE 0 END) AS numero_empleados_filled,
  SUM(CASE WHEN TRIM(IFNULL(otros_gastos,'')) <> '' THEN 1 ELSE 0 END) AS otros_gastos_filled
FROM tb_solicitudes;

-- Tabla para almacenar el histórico de comentarios sobre cambios/acciones en las solicitudes
-- Se crea idempotentemente
SET @tbl := 'tb_solicitudes_comments';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@tbl)=0,
               CONCAT('CREATE TABLE ', @tbl, " (idcomment INT NOT NULL AUTO_INCREMENT PRIMARY KEY, idsolicitud INT NOT NULL, user_id INT NULL, username VARCHAR(150) NULL, action VARCHAR(50) NULL, comment TEXT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX (idsolicitud));"),
               'SELECT "table_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Tabla para almacenar aprobaciones por solicitud (Comité Interno/Externo, Gerencia)
SET @tbl := 'tb_solicitud_aprobaciones';
SET @sql := IF((SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=@tbl)=0,
               CONCAT('CREATE TABLE ', @tbl, " (idaprobacion INT NOT NULL AUTO_INCREMENT PRIMARY KEY, idsolicitud INT NOT NULL, role VARCHAR(80) NOT NULL, user_id INT NULL, username VARCHAR(120) NULL, comment TEXT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX (idsolicitud));"),
               'SELECT "table_exists";');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;
