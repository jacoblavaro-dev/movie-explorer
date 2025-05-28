-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS movie_explorer;
USE movie_explorer;

-- Create movies table if not exists
CREATE TABLE IF NOT EXISTS movies (
    id INT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    overview TEXT,
    poster_url VARCHAR(255),
    release_date DATE,
    search_count INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Create ratings table if not exists
CREATE TABLE IF NOT EXISTS ratings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE
);

-- Add indexes
CREATE INDEX IF NOT EXISTS idx_movie_search ON movies(search_count DESC);
CREATE INDEX IF NOT EXISTS idx_movie_created ON movies(created_at DESC);
CREATE INDEX IF NOT EXISTS idx_rating_movie ON ratings(movie_id); 