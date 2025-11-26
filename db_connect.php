<?php
require_once 'config.php';  // Include your config file

function getDBConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        return $pdo;  // Return the connection object
    } catch (PDOException $e) {
        // Clean error handling: Log to file (optional) and return false
        $errorMsg = "[" . date('Y-m-d H:i:s') . "] Database connection failed: " . $e->getMessage() . PHP_EOL;
        error_log($errorMsg, 3, __DIR__ . '/logs/db_errors.log');  // Logs to file
        
        // For development: echo $errorMsg; (comment out in production)
        // echo "Connection failed: " . $e->getMessage();
        return false;
    }
}
function db_connect() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8";
        $conn = new PDO($dsn, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;

    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
        return null;
    }
}
?>