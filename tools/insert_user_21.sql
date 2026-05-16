    -- tools/insert_user_21.sql
    -- Insert or update user id=21 into `users` table.
    -- Adjust database name if different. The app's database configured in CI is `crediblamen.db`.

    USE `crediblamen.db`;

    INSERT INTO `users` (
    `id`, `ip_address`, `username`, `password`, `email`,
    `activation_selector`, `activation_code`,
    `forgotten_password_selector`, `forgotten_password_code`, `forgotten_password_time`,
    `remember_selector`, `remember_code`,
    `created_on`, `last_login`, `active`,
    `first_name`, `last_name`, `company`, `phone`, `perfil`
    )
    VALUES (
    21,
    '::1',
    'Roman Lainez',
    '$2y$10$v0eXtI1/KOlr5d2g1.F9kepj7UpnmY7BcZvkRXzRwM....',
    'Rlainez@crediblamen.group',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    'e113f9dbfef0beaf3a6c4515356442c928122fb0',
    '$2y$10$LvYcwoO3HbeFnuSBDKQKdeA5EzwOK2QZGPARYsVkGbr...',
    1767043488,
    1767384581,
    1,
    'Roman Lainez',
    'Rlainez',
    NULL,
    NULL,
    4
    )
    ON DUPLICATE KEY UPDATE
    `ip_address` = VALUES(`ip_address`),
    `username` = VALUES(`username`),
    `password` = VALUES(`password`),
    `email` = VALUES(`email`),
    `activation_selector` = VALUES(`activation_selector`),
    `activation_code` = VALUES(`activation_code`),
    `forgotten_password_selector` = VALUES(`forgotten_password_selector`),
    `forgotten_password_code` = VALUES(`forgotten_password_code`),
    `forgotten_password_time` = VALUES(`forgotten_password_time`),
    `remember_selector` = VALUES(`remember_selector`),
    `remember_code` = VALUES(`remember_code`),
    `created_on` = VALUES(`created_on`),
    `last_login` = VALUES(`last_login`),
    `active` = VALUES(`active`),
    `first_name` = VALUES(`first_name`),
    `last_name` = VALUES(`last_name`),
    `company` = VALUES(`company`),
    `phone` = VALUES(`phone`),
    `perfil` = VALUES(`perfil`);

    -- Notes:
    -- 1) This script uses the `id` primary key to decide insert vs update.
    -- 2) If your database name is different, change the `USE` line.
    -- 3) If you prefer to match/replace by `email` rather than `id`, run a check and
    --    convert into an UPDATE + INSERT logic or temporarily add a UNIQUE index on `email`.
    -- 4) Because `id` is not AUTO_INCREMENT in this DB, be sure the numeric id chosen
    --    doesn't conflict with existing rows you don't intend to replace.

    -- To run from PowerShell (XAMPP):
    -- & 'C:\\xampp\\mysql\\bin\\mysql.exe' -u root -p your_database < tools\\insert_user_21.sql
    -- or open phpMyAdmin and run the SQL.
