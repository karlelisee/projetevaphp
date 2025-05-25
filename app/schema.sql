-- Création de la base
CREATE DATABASE IF NOT EXISTS `file_manager`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE `file_manager`;

-- Table users
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table files
CREATE TABLE IF NOT EXISTS `files` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(255) NOT NULL,
  `deleted` TINYINT(1) NOT NULL DEFAULT 0,
  `is_favorite` TINYINT(1) NOT NULL DEFAULT 0,
  `owner_id` INT UNSIGNED NOT NULL,
  `uploaded_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_owner` (`owner_id`),
  CONSTRAINT `fk_files_users`
    FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
