-- Corregir la tabla login_attempts para que el campo id sea AUTO_INCREMENT
-- Este error ocurre porque el campo 'id' no tiene valor por defecto

-- Agregar AUTO_INCREMENT al campo id
ALTER TABLE `login_attempts` 
MODIFY COLUMN `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;

-- Asegurar que el campo id sea la clave primaria
ALTER TABLE `login_attempts` 
ADD PRIMARY KEY IF NOT EXISTS (`id`);
