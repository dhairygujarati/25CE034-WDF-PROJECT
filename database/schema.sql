-- =====================================================================
-- Database Schema for Campus Management & Event Portal
-- Items 8 & 9: Relational Schema, Indexes, Foreign Keys, Integrity
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `campus_portal_db` 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `campus_portal_db`;

-- ---------------------------------------------------------------------
-- Table: users (Authentication, RBAC, Remember-Me Tokens)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `role` ENUM('admin', 'student') NOT NULL DEFAULT 'student',
    `remember_token` VARCHAR(64) DEFAULT NULL,
    `remember_expiry` DATETIME DEFAULT NULL,
    `last_login` DATETIME DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_users_email` (`email`),
    INDEX `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: students (Student Management CRUD)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `students` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `roll_number` VARCHAR(30) NOT NULL UNIQUE,
    `first_name` VARCHAR(60) NOT NULL,
    `last_name` VARCHAR(60) NOT NULL,
    `email` VARCHAR(150) NOT NULL UNIQUE,
    `phone` VARCHAR(20) NOT NULL,
    `department` VARCHAR(80) NOT NULL,
    `semester` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `gpa` DECIMAL(3, 2) NOT NULL DEFAULT 0.00,
    `status` ENUM('Active', 'Inactive', 'Graduated', 'Suspended') NOT NULL DEFAULT 'Active',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_students_roll` (`roll_number`),
    INDEX `idx_students_department` (`department`),
    INDEX `idx_students_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: events (Event Management CRUD with Posters)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `events` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(180) NOT NULL,
    `category` VARCHAR(50) NOT NULL,
    `description` TEXT NOT NULL,
    `event_date` DATE NOT NULL,
    `event_time` TIME NOT NULL,
    `venue` VARCHAR(150) NOT NULL,
    `organizer` VARCHAR(100) NOT NULL,
    `max_participants` INT UNSIGNED NOT NULL DEFAULT 100,
    `poster_url` VARCHAR(255) DEFAULT NULL,
    `created_by` INT UNSIGNED DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_events_date` (`event_date`),
    INDEX `idx_events_category` (`category`),
    CONSTRAINT `fk_events_created_by` 
        FOREIGN KEY (`created_by`) 
        REFERENCES `users` (`id`) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: event_registrations (Many-to-Many Bridge Table)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `event_registrations` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `event_id` INT UNSIGNED NOT NULL,
    `student_id` INT UNSIGNED NOT NULL,
    `status` ENUM('Registered', 'Attended', 'Cancelled') NOT NULL DEFAULT 'Registered',
    `registered_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_event_student` (`event_id`, `student_id`),
    INDEX `idx_reg_event` (`event_id`),
    INDEX `idx_reg_student` (`student_id`),
    CONSTRAINT `fk_reg_event` 
        FOREIGN KEY (`event_id`) 
        REFERENCES `events` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT `fk_reg_student` 
        FOREIGN KEY (`student_id`) 
        REFERENCES `students` (`id`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Table: activity_logs (Audit Trails and Security Monitoring)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `activity_logs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED DEFAULT NULL,
    `action` VARCHAR(100) NOT NULL,
    `details` VARCHAR(255) DEFAULT NULL,
    `ip_address` VARCHAR(45) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_logs_user` (`user_id`),
    CONSTRAINT `fk_logs_user` 
        FOREIGN KEY (`user_id`) 
        REFERENCES `users` (`id`) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
