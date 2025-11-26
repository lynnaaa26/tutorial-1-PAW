<?php
require_once 'db_connect.php';
$pdo = getDBConnection();
if (!$pdo) {
    die('DB connection failed');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $matricule = trim($_POST['matricule'] ?? '');
    $group_id = (int)($_POST['group_id'] ?? 0);

    if (empty($fullname) || empty($matricule) || $group_id <= 0) {
        echo "All fields are required.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO students (fullname, matricule, group_id) VALUES (?, ?, ?)");
            $stmt->execute([$fullname, $matricule, $group_id]);
            echo "Student added successfully! ID: " . $pdo->lastInsertId();
        } catch (PDOException $e) {
            echo "Error adding student: " . $e->getMessage();
        }
    }
}
?>
<!-- Simple form (this for testing) -->
<form method="POST">
    Full Name: <input type="text" name="fullname" required><br>
    Matricule: <input type="text" name="matricule" required><br>
    Group ID: <input type="number" name="group_id" required><br>
    <button type="submit">Add Student</button>
</form>