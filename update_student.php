<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_connect.php';
$pdo = getDBConnection();
if (!$pdo) {
    die('DB connection failed. Check logs/db_errors.log');
}

$message = '';  // For feedback

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Uncomment to see POST data
    // var_dump($_POST); exit;

    $fullname = trim($_POST['fullname'] ?? '');
    $matricule = trim($_POST['matricule'] ?? '');
    $group_id = (int)($_POST['group_id'] ?? 0);

    if (empty($fullname) || empty($matricule) || $group_id <= 0) {
        $message = '<div style="color: red;">All fields are required!</div>';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO students (fullname, matricule, group_id) VALUES (?, ?, ?)");
            $stmt->execute([$fullname, $matricule, $group_id]);
            $message = '<div style="color: green;">Student added successfully! ID: ' . $pdo->lastInsertId() . '</div>';
        } catch (PDOException $e) {
            $message = '<div style="color: red;">Error adding student: ' . $e->getMessage() . '</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Add Student</title></head>
<body>
    <h2>Add a New Student</h2>
    <?php echo $message; ?>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        Full Name: <input type="text" name="fullname" required><br><br>
        Matricule: <input type="text" name="matricule" required><br><br>
        Group ID: <input type="number" name="group_id" min="1" required><br><br>
        <button type="submit" style="padding: 10px; background: #4CAF50; color: white;">Add Student</button>
    </form>
    <br><a href="list_students.php">View All Students</a>
</body>
</html>