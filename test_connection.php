<?php
require_once 'db_connect.php';  // Include the connection function

$connection = getDBConnection();

if ($connection) {
    echo "Connection successful!";
    // Optional: Test a simple query
    $stmt = $connection->query("SELECT 1");
    if ($stmt->fetch()) {
        echo " <br> (Basic query also works.)";
    }
} else {
    echo "Connection failed. Check your config or MySQL server.";
}
?>