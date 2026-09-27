<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database configuration - Reads from Environment Variables with local fallback
$dbHost = getenv('DB_HOST') !== false ? getenv('DB_HOST') : 'localhost';
$dbPort = getenv('DB_PORT') !== false ? getenv('DB_PORT') : '3306';
$dbUser = getenv('DB_USER') !== false ? getenv('DB_USER') : 'root';
$dbPass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$dbName = getenv('DB_NAME') !== false ? getenv('DB_NAME') : 'todo_app';

define('DB_HOST', $dbHost);
define('DB_PORT', $dbPort);
define('DB_USER', $dbUser);
define('DB_PASS', $dbPass);
define('DB_NAME', $dbName);

// Check if running on Vercel or cloud serverless platform
$isVercel = (getenv('VERCEL') !== false && getenv('VERCEL') != '0') 
         || isset($_ENV['VERCEL']) 
         || (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'vercel.app') !== false);

function initializeSqliteDatabase() {
    $dbPath = sys_get_temp_dir() . '/todo_app.sqlite';
    $pdo = new PDO("sqlite:" . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("PRAGMA foreign_keys = ON;");
    
    // Auto-create schema for serverless execution
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    );");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS tasks (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        title TEXT NOT NULL,
        description TEXT,
        due_date TEXT,
        status TEXT DEFAULT 'Pending',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );");
    
    return $pdo;
}

// Create database connection
try {
    // If on Vercel and no external cloud database was configured (still localhost)
    if ($isVercel && (DB_HOST === 'localhost' || DB_HOST === '127.0.0.1')) {
        $pdo = initializeSqliteDatabase();
    } else {
        // Standard MySQL Connection
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT => 3
        ]);
    }
} catch (PDOException $e) {
    // Fallback to SQLite if MySQL fails on cloud/Vercel to prevent breaking the live application
    if ($isVercel || in_array('sqlite', PDO::getAvailableDrivers())) {
        try {
            $pdo = initializeSqliteDatabase();
        } catch (Exception $fallbackErr) {
            die("Database connection failed: " . $e->getMessage());
        }
    } else {
        die("Connection failed: " . $e->getMessage());
    }
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Redirect to login if not authenticated
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}
?>