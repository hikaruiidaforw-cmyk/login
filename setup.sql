-- 保存ニューステーブル
-- ※さくらサーバーでは既存のデータベース(gs-iidahikaru_php03)を使用するため、
--   CREATE DATABASE と USE 文は不要です
CREATE TABLE IF NOT EXISTS saved_news (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(500) NOT NULL,
    description TEXT,
    url VARCHAR(1000),
    image_url VARCHAR(1000),
    source_name VARCHAR(200),
    published_at DATETIME,
    memo TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
