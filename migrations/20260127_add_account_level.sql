-- Migration: add level and is_mayor columns to account tables
-- Run these statements in your MySQL (via phpMyAdmin, CLI or a DB tool).

-- NOTE: This installation uses `tb_account`. `b_account` is not present here
-- so this migration only targets `tb_account` to avoid "table does not exist" errors.

-- STEP 1: Inspect existing columns first. Run these and paste results if unsure:
-- SHOW COLUMNS FROM `tb_account` LIKE 'level';
-- SHOW COLUMNS FROM `tb_account` LIKE 'is_mayor';
-- SHOW CREATE TABLE `tb_account`;

-- STEP 2: If neither column exists, run this single ALTER:
-- ALTER TABLE `tb_account`
--     ADD COLUMN `level` INT NULL DEFAULT NULL,
--     ADD COLUMN `is_mayor` TINYINT(1) NOT NULL DEFAULT 0;

-- STEP 3: If `level` already exists but you want to ensure its type/default, run:
-- ALTER TABLE `tb_account` MODIFY COLUMN `level` INT NULL DEFAULT NULL;

-- STEP 4: If `is_mayor` is missing, add only that column:
-- ALTER TABLE `tb_account` ADD COLUMN `is_mayor` TINYINT(1) NOT NULL DEFAULT 0;

-- Avoid using IF NOT EXISTS in ADD COLUMN (not supported on older MySQL versions)
-- and avoid PREPARE/INFORMATION_SCHEMA checks if your DB user lacks those privileges.

-- Note: MySQL < 8 does not support IF NOT EXISTS for ADD COLUMN. If you get syntax errors,
-- run the ALTER TABLE only when the columns do not exist, or use the following pattern:
-- ALTER TABLE tb_account ADD COLUMN `level` INT NULL;
-- ALTER TABLE tb_account ADD COLUMN `is_mayor` TINYINT(1) NOT NULL DEFAULT 0;

-- After running the migration, optionally backfill `level` for existing accounts using parent_id,
-- or set `is_mayor=1` for top-level accounts. Example backfill:
-- UPDATE tb_account SET level = 1 WHERE parent_id IS NULL OR parent_id = 0;
-- UPDATE tb_account a SET a.level = (SELECT 1 + COALESCE((SELECT level FROM tb_account p WHERE p.id = a.parent_id), 0));

-- For complex trees, a small script to traverse and set levels is recommended.
