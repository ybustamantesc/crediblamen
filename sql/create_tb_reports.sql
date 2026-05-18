-- Create table to store report generation jobs
CREATE TABLE IF NOT EXISTS `tb_reports` (
  `job_id` VARCHAR(64) NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `print_url` TEXT,
  `file_path` TEXT,
  `status` ENUM('pending','processing','done','error') NOT NULL DEFAULT 'pending',
  `created_by` VARCHAR(100) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `started_at` DATETIME DEFAULT NULL,
  `finished_at` DATETIME DEFAULT NULL,
  `error_text` TEXT DEFAULT NULL,
  `file_hash` VARCHAR(128) DEFAULT NULL,
  PRIMARY KEY (`job_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
