<?php
// Database installation script
$host = getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'localhost';
$port = getenv('DB_PORT') !== false ? getenv('DB_PORT') : '3306';
$user = getenv('DB_USER') !== false ? getenv('DB_USER') : 'root';
$pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

try {
    // Connect to MySQL server
    $pdo = new PDO("mysql:host=$host;port=$port", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read and execute SQL file
    $sqlFile = __DIR__ . '/database.sql';
    if (!file_exists($sqlFile)) {
        throw new Exception("database.sql file not found in " . __DIR__);
    }
    $sql = file_get_contents($sqlFile);
    $pdo->exec($sql);
    
    echo "<h2>Installation Successful!</h2>";
    echo "<p>Database and tables have been created successfully.</p>";
    echo "<p><a href='register.php'>Go to Registration</a></p>";
    
} catch(PDOException $e) {
    echo "<h2>Installation Failed!</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please make sure MySQL is running and check your database credentials in config.php</p>";
}
?>