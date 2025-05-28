<?php
header('Content-Type: application/json');
require '../config/db.php';

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['movie_id']) || !isset($data['rating'])) {
        throw new Exception('Missing required fields');
    }

    $movieId = (int)$data['movie_id'];
    $rating = (int)$data['rating'];

    if ($rating < 1 || $rating > 5) {
        throw new Exception('Rating must be between 1 and 5');
    }

    $sql = "INSERT INTO ratings (movie_id, rating) VALUES (?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$movieId, $rating]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred']);
}
?>