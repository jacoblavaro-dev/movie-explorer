<?php
require '../config/db.php';

$sql = "SELECT * FROM movies ORDER BY search_count DESC LIMIT 5";
$stmt = $pdo->query($sql);
$topMovies = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($topMovies);
?>