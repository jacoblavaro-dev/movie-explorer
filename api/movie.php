<?php
header('Content-Type: application/json');
require '../config/db.php';

try {
    if (!isset($_GET['id'])) {
        throw new Exception('Movie ID is required');
    }

    $movieId = (int)$_GET['id'];
    
    $sql = "SELECT m.*, 
            COALESCE(AVG(r.rating), 0) as avg_rating,
            COUNT(r.id) as rating_count
            FROM movies m
            LEFT JOIN ratings r ON m.id = r.movie_id
            WHERE m.id = ?
            GROUP BY m.id";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$movieId]);
    $movie = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$movie) {
        throw new Exception('Movie not found');
    }

    echo json_encode([
        'id' => $movie['id'],
        'title' => $movie['title'],
        'overview' => $movie['overview'],
        'poster_url' => $movie['poster_url'],
        'release_date' => $movie['release_date'],
        'average_rating' => number_format($movie['avg_rating'], 1),
        'rating_count' => (int)$movie['rating_count']
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred']);
}
?> 