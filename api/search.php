<?php
require '../config/db.php';

$apiKey = 'YOUR_TMDB_API_KEY';
$query = urlencode($_GET['query']);
$apiUrl = "https://api.themoviedb.org/3/search/movie?api_key=$apiKey&query=$query";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
$movies = [];

foreach ($data['results'] as $movie) {
    $id = $movie['id'];
    $title = $movie['title'];
    $poster = 'https://image.tmdb.org/t/p/w500' . $movie['poster_path'];

    $stmt = $pdo->prepare("SELECT * FROM movies WHERE movie_id = ?");
    $stmt->execute([$id]);
    if ($stmt->rowCount() > 0) {
        $pdo->prepare("UPDATE movies SET search_count = search_count + 1 WHERE movie_id = ?")->execute([$id]);
    } else {
        $pdo->prepare("INSERT INTO movies (movie_id, title, poster_url) VALUES (?, ?, ?)")->execute([$id, $title, $poster]);
    }

    $ratingStmt = $pdo->prepare("SELECT AVG(rating_value) AS avg_rating FROM ratings WHERE movie_id = ?");
    $ratingStmt->execute([$id]);
    $avgRating = round($ratingStmt->fetch()['avg_rating'], 1);

    $movies[] = [
        'id' => $id,
        'title' => $title,
        'poster' => $poster,
        'overview' => $movie['overview'],
        'release_date' => $movie['release_date'],
        'average_rating' => $avgRating ?: 'N/A'
    ];
}

echo json_encode($movies);
?>