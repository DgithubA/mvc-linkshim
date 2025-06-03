-- جدول دوره‌های آموزشی
CREATE TABLE courses
(
    id               INT AUTO_INCREMENT PRIMARY KEY,
    instructor_id    BIGINT UNSIGNED NOT NULL,
    title            VARCHAR(255) NOT NULL,
    description      TEXT,
    category         VARCHAR(100),
    level            ENUM('Beginner', 'Intermediate', 'Advanced') DEFAULT 'Beginner',
    language         VARCHAR(2)    DEFAULT 'Fa',
    price            DECIMAL(10, 2) DEFAULT 0.00,
    discount_percent INT            DEFAULT 0,
    duration_hours   DECIMAL(5, 2),
    start_date       DATE,
    end_date         DATE,
    status           ENUM('Draft', 'Published', 'Archived') DEFAULT 'Draft',
    thumbnail_url    VARCHAR(512),

    FOREIGN KEY (`instructor_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
);
