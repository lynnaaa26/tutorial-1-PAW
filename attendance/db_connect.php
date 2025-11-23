<?php
require_once 'config.php'; // inclut la config

function getConnection() {
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ];

        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        return $pdo;
    } catch (PDOException $e) {
        file_put_contents('db_errors.log', $e->getMessage() . PHP_EOL, FILE_APPEND);
        echo "Connection failed: " . $e->getMessage();
        exit;
    }
}
?>