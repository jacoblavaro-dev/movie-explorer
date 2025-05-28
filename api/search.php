<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require '../config/db.php';

$query = isset($_GET['query']) ? trim($_GET['query']) : '';
if (empty($query)) {
    http_response_code(400);
    echo json_encode(['error' => 'Query parameter is required']);
    exit;
}

$apiKey = 'bcb16046fef602fb03fb0f90a09967f8';
$searchUrl = "https://api.themoviedb.org/3/search/movie?api_key={$apiKey}&query=" . urlencode($query);

try {
    // Search TMDb
    $response = file_get_contents($searchUrl);
    $data = json_decode($response, true);
    
    if (!$data || !isset($data['results'])) {
        throw new Exception('Invalid API response');
    }

    $movies = array_slice($data['results'], 0, 10); // Limit to first 10 results
    
    // Process each movie
    $processedMovies = array_map(function($movie) use ($pdo) {
        // Handle missing poster path
        $posterUrl = !empty($movie['poster_path']) ? 
            'https://image.tmdb.org/t/p/w500' . $movie['poster_path'] : 
            'https://via.placeholder.com/500x750.png?text=No+Poster';

        // Try to insert or update the movie in our database
        try {
            $sql = "INSERT INTO movies (id, title, overview, poster_url, release_date, search_count) 
                    VALUES (?, ?, ?, ?, ?, 1)
                    ON DUPLICATE KEY UPDATE 
                    search_count = search_count + 1";
                    
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $movie['id'],
                $movie['title'],
                $movie['overview'] ?? '',
                $posterUrl,
                $movie['release_date'] ?? null
            ]);
        } catch (PDOException $e) {
            // Log the error but continue processing
            error_log("Database error for movie {$movie['id']}: " . $e->getMessage());
        }

        // Get average rating
        $avgRating = 0;
        try {
            $ratingStmt = $pdo->prepare("SELECT AVG(rating) as avg_rating FROM ratings WHERE movie_id = ?");
            $ratingStmt->execute([$movie['id']]);
            $avgRating = $ratingStmt->fetch(PDO::FETCH_ASSOC)['avg_rating'] ?? 0;
        } catch (PDOException $e) {
            error_log("Rating error for movie {$movie['id']}: " . $e->getMessage());
        }

        return [
            'id' => $movie['id'],
            'title' => $movie['title'],
            'overview' => $movie['overview'] ?? '',
            'poster' => $posterUrl,
            'release_date' => $movie['release_date'] ?? '',
            'average_rating' => number_format((float)$avgRating, 1)
        ];
    }, $movies);

    echo json_encode($processedMovies);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred while searching movies']);
}
?>