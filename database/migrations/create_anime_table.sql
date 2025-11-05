-- Migration: Create anime table

CREATE DATABASE IF NOT EXISTS anime_laravel;

USE anime_laravel;

CREATE TABLE IF NOT EXISTS anime (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    genre VARCHAR(255),
    episodes INT,
    status ENUM('Ongoing', 'Completed', 'Upcoming') DEFAULT 'Ongoing',
    rating DECIMAL(3,1),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Sample data
INSERT INTO anime (title, genre, episodes, status, rating) VALUES
('Demon Slayer', 'Action, Supernatural', 26, 'Completed', 8.7),
('My Hero Academia', 'Action, Superhero', 113, 'Ongoing', 8.4),
('Steins;Gate', 'Sci-Fi, Thriller', 24, 'Completed', 9.1);
