<?php
// Health Check Endpoint for DevOps Monitoring and CI/CD Pipeline Verification
header('Content-Type: application/json');

$status = [
    'status' => 'UP',
    'service' => 'To-Do List Web Application',
    'timestamp' => date('c'),
    'php_version' => PHP_VERSION,
    'checks' => [
        'web_server' => 'OK',
        'php_runtime' => 'OK'
    ]
];

// Test database connection if environment variables exist
if (getenv('DB_HOST')) {
    try {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT') ?: '3306';
        $dbname = getenv('DB_NAME') ?: 'todo_app';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname";
        $testPdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_TIMEOUT => 2,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        $status['checks']['database'] = 'OK';
    } catch (Exception $e) {
        $status['checks']['database'] = 'WARNING: ' . $e->getMessage();
    }
}

http_response_code(200);
echo json_encode($status, JSON_PRETTY_PRINT);
exit();
