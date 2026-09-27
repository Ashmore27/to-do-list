<?php
/**
 * Vercel Serverless Function Gateway / Router
 * Bridges Vercel's serverless 'api/' function architecture with the existing PHP project root.
 * Preserves 100% of the existing application without modifying root files.
 */

// Base directory pointing to the project root
$rootDir = dirname(__DIR__);
chdir($rootDir);

// Parse the requested URL path
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$path = trim($requestUri, '/');

// Route mapping
if (empty($path) || $path === 'index.php') {
    require $rootDir . '/index.php';
    exit();
}

// Check if direct PHP file was requested (e.g. login.php, dashboard.php)
$targetFile = $rootDir . '/' . $path;
if (file_exists($targetFile) && pathinfo($targetFile, PATHINFO_EXTENSION) === 'php') {
    require $targetFile;
    exit();
}

// Fallback to index.php
require $rootDir . '/index.php';
exit();
