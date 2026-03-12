-- Dra. Samla Cristie - Database Initialization Script
-- This script is run automatically when the MySQL container starts

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Admin users
CREATE TABLE IF NOT EXISTS `admin_users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `name` VARCHAR(100) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Site settings
CREATE TABLE IF NOT EXISTS `settings` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `setting_key` VARCHAR(100) NOT NULL UNIQUE,
    `setting_value` TEXT,
    `setting_group` VARCHAR(50) DEFAULT 'geral',
    `setting_label` VARCHAR(200),
    `setting_type` VARCHAR(20) DEFAULT 'text',
    `sort_order` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Services
CREATE TABLE IF NOT EXISTS `services` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL,
    `description` TEXT,
    `icon` VARCHAR(50) DEFAULT 'fas fa-paw',
    `image` VARCHAR(255),
    `whatsapp_text` VARCHAR(500),
    `sort_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Team members
CREATE TABLE IF NOT EXISTS `team` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `role` VARCHAR(200),
    `description` TEXT,
    `image` VARCHAR(255),
    `sort_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Testimonials
CREATE TABLE IF NOT EXISTS `testimonials` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `initials` VARCHAR(5),
    `color` VARCHAR(20) DEFAULT '#4285f4',
    `rating` DECIMAL(2,1) DEFAULT 5.0,
    `text` TEXT NOT NULL,
    `date_label` VARCHAR(50),
    `sort_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Blog articles
CREATE TABLE IF NOT EXISTS `articles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(300) NOT NULL,
    `slug` VARCHAR(300) NOT NULL UNIQUE,
    `excerpt` TEXT,
    `content` LONGTEXT,
    `image` VARCHAR(255),
    `category` VARCHAR(100),
    `author` VARCHAR(200) DEFAULT 'Dra. Samla Cristie',
    `read_time` VARCHAR(20) DEFAULT '5 min',
    `featured` TINYINT(1) DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `views` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- FAQ items
CREATE TABLE IF NOT EXISTS `faq` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category` VARCHAR(100) NOT NULL,
    `category_icon` VARCHAR(50) DEFAULT 'fas fa-paw',
    `question` VARCHAR(500) NOT NULL,
    `answer` TEXT NOT NULL,
    `sort_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Specialties
CREATE TABLE IF NOT EXISTS `specialties` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `icon` VARCHAR(50) DEFAULT 'fas fa-star',
    `sort_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Clients
CREATE TABLE IF NOT EXISTS `clients` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(200) NOT NULL,
    `type` VARCHAR(100),
    `description` TEXT,
    `logo_icon` VARCHAR(50) DEFAULT 'fas fa-hospital',
    `logo_color` VARCHAR(20) DEFAULT '#2d5016',
    `location` VARCHAR(200),
    `sort_order` INT DEFAULT 0,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS = 1;
