<?php
header('Content-Type: application/json');
require '../config/db.php';

try {
    $sql = "SELECT m.*, AVG(r.rating) as avg_rating, COUNT(r.id) as rating_count
            FROM movies m
            INNER JOIN ratings r ON m.id = r.movie_id
            GROUP BY m.id
            HAVING COUNT(r.id) > 0
            ORDER BY avg_rating DESC
            LIMIT 10";

    $stmt = $pdo->query($sql);
    $movies = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formattedMovies = array_map(function($movie) {
        return [
            'id' => $movie['id'],
            'title' => $movie['title'],
            'poster_url' => $movie['poster_url'],
            'avg_rating' => number_format($movie['avg_rating'], 1),
            'rating_count' => (int)$movie['rating_count']
        ];
    }, $movies);

    echo json_encode($formattedMovies);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred']);
}
?>
