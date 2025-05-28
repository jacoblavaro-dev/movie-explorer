<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Create connection without database
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute the SQL setup file
    $sql = file_get_contents(__DIR__ . '/setup.sql');
    $pdo->exec($sql);
    
    echo "Database setup completed successfully!\n";
    echo "✅ Created database 'movie_explorer'\n";
    echo "✅ Created table 'movies'\n";
    echo "✅ Created table 'ratings'\n";
    echo "✅ Created necessary indexes\n";
    
} catch (PDOException $e) {
    die("DB Setup Error: " . $e->getMessage() . "\n");
}
?> 