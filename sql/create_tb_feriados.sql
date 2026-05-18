    -- Create table for holidays (feriados)
    CREATE TABLE IF NOT EXISTS `tb_feriados` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `fecha` DATE NOT NULL,
    `motivo` VARCHAR(255) DEFAULT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_fecha` (`fecha`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

    -- Seed common fixed holidays (year-specific dates can be added/edited via UI)
    -- Example entries for 2026 (user can change year when adding):
    INSERT IGNORE INTO `tb_feriados` (`fecha`, `motivo`, `activo`) VALUES
    ('2026-01-01', 'Año Nuevo', 1),
    ('2026-04-17', 'Jueves Santo (Semana Santa)', 1),
    ('2026-04-18', 'Viernes Santo (Semana Santa)', 1),
    ('2026-04-20', 'Domingo de Resurrección (Semana Santa)', 1),
    ('2026-05-01', 'Día del Trabajo', 1),
    ('2026-07-19', 'Día de la Revolución', 1),
    ('2026-08-01', 'Término festividad (ejemplo)', 1),
    ('2026-09-14', 'Batalla de San Jacinto', 1),
    ('2026-09-15', 'Día de la Independencia', 1),
    ('2026-12-08', 'Día de la Inmaculada Concepción', 1),
    ('2026-12-25', 'Navidad', 1)
    ON DUPLICATE KEY UPDATE motivo=VALUES(motivo), activo=VALUES(activo);
