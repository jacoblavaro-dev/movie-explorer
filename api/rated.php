<?php
require '../config/db.php';

$sql = "SELECT m.title, m.poster_url, AVG(r.rating_value) AS avg_rating
        FROM ratings r
        JOIN movies m ON r.movie_id = m.movie_id
        GROUP BY m.movie_id
        HAVING COUNT(r.rating_value) > 0
        ORDER BY avg_rating DESC
        LIMIT 10";

$stmt = $pdo->query($sql);
$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($movies);
?>
