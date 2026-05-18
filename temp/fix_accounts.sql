-- Fix accounts: create new table and copy from b_account mapping corrupted column names
CREATE TABLE IF NOT EXISTS `new_tb_account` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `parent_id` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO new_tb_account (`code`,`name`,`type`,`parent_id`,`created_at`)
SELECT code, ame, ype, parent_id, created_at FROM b_account;

-- If successful, rename into tb_account (drop existing tb_account if any)
DROP TABLE IF EXISTS tb_account;
RENAME TABLE new_tb_account TO tb_account;

SELECT 'done' as status;
