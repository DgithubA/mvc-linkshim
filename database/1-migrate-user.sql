CREATE TABLE `users`
(
    `id`                BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name`              VARCHAR(255) NOT NULL,
    `email`             VARCHAR(255) NOT NULL UNIQUE,
    `email_verified_at` TIMESTAMP    NULL,
    `password`          VARCHAR(255) NOT NULL,
    `remember_token`    VARCHAR(100) NULL,
    `role`              VARCHAR(50)  NOT NULL DEFAULT 'student',
    `created_at`        TIMESTAMP    NULL,
    `updated_at`        TIMESTAMP    NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `password_reset_tokens` (
     `email` VARCHAR(255) NOT NULL PRIMARY KEY,
     `token` VARCHAR(255) NOT NULL,
     `created_at` TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `sessions` (
        `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `user_id` BIGINT UNSIGNED NULL,
        `ip_address` VARCHAR(45) NULL,
        `user_agent` TEXT NULL,
        `payload` LONGTEXT NOT NULL,
        `last_activity` INT NOT NULL,
        INDEX (`user_id`),
        INDEX (`last_activity`),
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
