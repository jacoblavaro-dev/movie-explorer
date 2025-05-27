<?php
require '../config/db.php';

$data = json_decode(file_get_contents("php://input"), true);
$movie_id = $data['movie_id'];
$rating = $data['rating'];

$stmt = $pdo->prepare("INSERT INTO ratings (movie_id, rating_value) VALUES (?, ?)");
$stmt->execute([$movie_id, $rating]);

echo json_encode(['message' => 'Rating submitted']);
?>