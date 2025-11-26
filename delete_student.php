<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_connect.php';
$pdo = getDBConnection();
if (!$pdo) {
    die('DB connection failed.');
}

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($id <= 0) {
    die('<div style="color: red;">Invalid student ID.</div>');
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = ?");
        $stmt->execute([$id]);
        $message = '<div style="color: green;">Student deleted successfully!</div>';
        echo $message . '<br><a href="list_student.php">Back to List</a>';  // Note: Matches your file name
        exit;
    } catch (PDOException $e) {
        $message = '<div style="color: red;">Error deleting: ' . $e->getMessage() . '</div>';
    }
}

// Fetch student name for confirmation
try {
    $stmt = $pdo->prepare("SELECT fullname FROM students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch();
    if (!$student) {
        die('<div style="color: red;">Student not found.</div>');
    }
} catch (PDOException $e) {
    die('<div style="color: red;">Error: ' . $e->getMessage() . '</div>');
}
?>
<!DOCTYPE html>
<html>
<head><title>Delete Student</title></head>
<body>
    <h2>Confirm Delete</h2>
    <p>Are you sure you want to delete "<?php echo htmlspecialchars($student['fullname']); ?>" (ID: <?php echo $id; ?>)? This cannot be undone!</p>
    <?php if ($message) echo $message; ?>
    <form method="POST" style="margin: 20px 0;">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <button type="submit" name="confirm" style="padding: 10px; background: #f44336; color: white; border: none; cursor: pointer;">Yes, Delete</button>
        <button type="button" onclick="window.location.href='list_student.php'" style="padding: 10px; background: #ddd; border: none; cursor: pointer; margin-left: 10px;">Cancel</button>
    </form>
</body>
</html>