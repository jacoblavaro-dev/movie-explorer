<?php
require '../config/db.php';

$sql = "SELECT * FROM movies ORDER BY search_count DESC, title LIMIT 10";
$stmt = $pdo->query($sql);
$movies = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($movies);
?>
