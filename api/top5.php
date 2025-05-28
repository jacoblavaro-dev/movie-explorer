<?php
header('Content-Type: application/json');
require '../config/db.php';

try {
    // Get top 5 most searched movies with their average ratings
    $sql = "SELECT m.*, 
            COALESCE(AVG(r.rating), 0) as avg_rating,
            COUNT(DISTINCT r.id) as rating_count
            FROM movies m
            LEFT JOIN ratings r ON m.id = r.movie_id
            GROUP BY m.id
            ORDER BY m.search_count DESC
            LIMIT 5";
            
    $stmt = $pdo->query($sql);
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formattedMovies = array_map(function($movie) {
        return [
            'id' => $movie['id'],
            'title' => $movie['title'],
            'poster_url' => $movie['poster_url'],
            'search_count' => (int)$movie['search_count'],
            'avg_rating' => number_format($movie['avg_rating'], 1)
        ];
    }, $movies);

    echo json_encode($formattedMovies);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred']);
}
?>