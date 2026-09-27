<?php
/**
 * Automated Test Runner for To-Do List Application
 * Used in Jenkins CI/CD Pipeline (Stage: Test)
 * 
 * Verifies:
 * 1. PHP Syntax Validation (php -l)
 * 2. Required Core Files Existence
 * 3. Database Schema Integrity
 * 4. Critical Application Logic & Security Checks
 * 
 * Returns:
 * Exit code 0 if all tests pass.
 * Exit code 1 if any test fails (signals Jenkins to mark build as failed).
 */

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;
$rootDir = realpath(__DIR__ . '/..');

echo "========================================================\n";
echo "       RUNNING AUTOMATED APPLICATION TEST SUITE         \n";
echo "========================================================\n";
echo "Project Root: $rootDir\n";
echo "PHP Version : " . PHP_VERSION . "\n";
echo "Date        : " . date('Y-m-d H:i:s') . "\n\n";

function runTest($testName, $callback) {
    global $totalTests, $passedTests, $failedTests;
    $totalTests++;
    echo "[TEST $totalTests] $testName... ";
    try {
        $result = $callback();
        if ($result === true) {
            echo "PASSED\n";
            $passedTests++;
            return true;
        } else {
            echo "FAILED: $result\n";
            $failedTests++;
            return false;
        }
    } catch (Exception $e) {
        echo "ERROR: " . $e->getMessage() . "\n";
        $failedTests++;
        return false;
    }
}

// ---------------------------------------------------------
// SUITE 1: Required Files Verification
// ---------------------------------------------------------
echo "--- Suite 1: File Existence & Integrity ---\n";

$requiredFiles = [
    'config.php',
    'index.php',
    'login.php',
    'register.php',
    'dashboard.php',
    'export_pdf.php',
    'logout.php',
    'install.php',
    'healthcheck.php',
    'database.sql',
    '.env.example',
    'assets/css/style.css'
];

foreach ($requiredFiles as $file) {
    runTest("File exists: $file", function() use ($rootDir, $file) {
        $path = $rootDir . '/' . $file;
        if (!file_exists($path)) {
            return "File does not exist: $file";
        }
        if (filesize($path) === 0) {
            return "File is empty: $file";
        }
        return true;
    });
}

// ---------------------------------------------------------
// SUITE 2: PHP Syntax Linting (php -l)
// ---------------------------------------------------------
echo "\n--- Suite 2: PHP Syntax Linting (php -l) ---\n";

$phpFiles = glob($rootDir . '/*.php');
foreach ($phpFiles as $filePath) {
    $fileName = basename($filePath);
    runTest("Syntax check: $fileName", function() use ($filePath) {
        $output = [];
        $returnVar = 0;
        // Use PHP_BINARY for full cross-platform compatibility (Windows, Linux, Docker, Jenkins)
        $phpBin = defined('PHP_BINARY') && PHP_BINARY ? PHP_BINARY : 'php';
        $escapedBin = escapeshellarg($phpBin);
        $escapedPath = escapeshellarg($filePath);
        exec("$escapedBin -l $escapedPath 2>&1", $output, $returnVar);
        if ($returnVar !== 0) {
            return implode(" ", $output);
        }
        return true;
    });
}

// ---------------------------------------------------------
// SUITE 3: Application Structure & Security Checks
// ---------------------------------------------------------
echo "\n--- Suite 3: Application Logic & Security ---\n";

runTest("Database Schema defines required tables", function() use ($rootDir) {
    $schema = file_get_contents($rootDir . '/database.sql');
    if (strpos($schema, 'CREATE TABLE') === false) {
        return "database.sql does not contain CREATE TABLE";
    }
    if (strpos($schema, 'users') === false) {
        return "users table missing from schema";
    }
    if (strpos($schema, 'tasks') === false) {
        return "tasks table missing from schema";
    }
    return true;
});

runTest("config.php supports environment variables", function() use ($rootDir) {
    $configContent = file_get_contents($rootDir . '/config.php');
    if (strpos($configContent, 'getenv') === false) {
        return "config.php does not support getenv() for configuration";
    }
    return true;
});

runTest("dashboard.php enforces authentication", function() use ($rootDir) {
    $dashboardContent = file_get_contents($rootDir . '/dashboard.php');
    if (strpos($dashboardContent, 'requireLogin') === false && strpos($dashboardContent, 'isLoggedIn') === false) {
        return "dashboard.php does not enforce login check";
    }
    return true;
});

runTest("login.php contains email and password inputs", function() use ($rootDir) {
    $loginContent = file_get_contents($rootDir . '/login.php');
    if (strpos($loginContent, 'name="email"') === false || strpos($loginContent, 'name="password"') === false) {
        return "login.php is missing required form inputs";
    }
    return true;
});

runTest("register.php hashes user passwords", function() use ($rootDir) {
    $registerContent = file_get_contents($rootDir . '/register.php');
    if (strpos($registerContent, 'password_hash') === false) {
        return "register.php does not use password_hash()";
    }
    return true;
});

runTest(".env.example contains placeholder definitions", function() use ($rootDir) {
    $envContent = file_get_contents($rootDir . '/.env.example');
    if (strpos($envContent, 'DB_HOST') === false || strpos($envContent, 'DB_NAME') === false) {
        return ".env.example is missing essential DB variables";
    }
    return true;
});

// ---------------------------------------------------------
// TEST SUMMARY & EXIT CODE
// ---------------------------------------------------------
echo "\n========================================================\n";
echo "                     TEST SUMMARY                       \n";
echo "========================================================\n";
echo "Total Tests Run : $totalTests\n";
echo "Passed Tests    : $passedTests\n";
echo "Failed Tests    : $failedTests\n";

if ($failedTests === 0) {
    echo "Result          : ALL TESTS PASSED (EXIT CODE 0)\n";
    echo "========================================================\n";
    exit(0);
} else {
    echo "Result          : $failedTests TEST(S) FAILED (EXIT CODE 1)\n";
    echo "========================================================\n";
    exit(1);
}
