<?php
header('Content-Type: text/plain');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== Testing Database Connection ===\n";

try {
    require '../config/db.php';
    echo "✅ Database connection successful\n";
    
    // Test movies table
    $stmt = $pdo->query("SHOW TABLES LIKE 'movies'");
    if ($stmt->rowCount() > 0) {
        echo "✅ Movies table exists\n";
        
        // Check movies table structure
        $stmt = $pdo->query("DESCRIBE movies");
        echo "\nMovies table structure:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- {$row['Field']}: {$row['Type']}\n";
        }
    } else {
        echo "❌ Movies table does not exist\n";
    }
    
    // Test ratings table
    $stmt = $pdo->query("SHOW TABLES LIKE 'ratings'");
    if ($stmt->rowCount() > 0) {
        echo "\n✅ Ratings table exists\n";
        
        // Check ratings table structure
        $stmt = $pdo->query("DESCRIBE ratings");
        echo "\nRatings table structure:\n";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "- {$row['Field']}: {$row['Type']}\n";
        }
    } else {
        echo "❌ Ratings table does not exist\n";
    }

    // Test TMDb API connection
    echo "\n=== Testing TMDb API Connection ===\n";
    $apiKey = 'bcb16046fef602fb03fb0f90a09967f8';
    $testUrl = "https://api.themoviedb.org/3/movie/popular?api_key={$apiKey}&language=en-US&page=1";
    
    $response = @file_get_contents($testUrl);
    if ($response !== false) {
        echo "✅ TMDb API connection successful\n";
        $data = json_decode($response, true);
        if (isset($data['results'])) {
            echo "✅ TMDb API returned valid data\n";
        } else {
            echo "❌ TMDb API response format unexpected\n";
        }
    } else {
        echo "❌ Failed to connect to TMDb API\n";
        echo "Error: " . error_get_last()['message'] . "\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?> 