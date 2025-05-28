<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Create database if not exists
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS movie_explorer");
    $pdo->exec("USE movie_explorer");
    
    // Create movies table
    $pdo->exec("DROP TABLE IF EXISTS ratings");
    $pdo->exec("DROP TABLE IF EXISTS movies");
    
    $pdo->exec("CREATE TABLE movies (
        id INT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        overview TEXT,
        poster_url VARCHAR(255),
        release_date DATE,
        search_count INT DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_search_count (search_count DESC),
        INDEX idx_created_at (created_at DESC)
    )");

    // Create ratings table
    $pdo->exec("CREATE TABLE ratings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        movie_id INT NOT NULL,
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
        INDEX idx_movie_id (movie_id)
    )");

    echo "Database and tables created successfully!\n";
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage() . "\n");
}
?> 