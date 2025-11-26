<?php
require_once 'db_connect.php';  // Assuming db_connect() returns PDO

$session_id = (int)($_GET['id'] ?? 0);
$message = '';

if ($session_id <= 0) {
    die('<div style="color: red;">Invalid session ID. <a href="create_session.php">Create one?</a></div>');
}

$conn = null;
try {
    $conn = db_connect();
} catch (PDOException $e) {
    die('DB connection failed: ' . $e->getMessage());
}

// Handle confirmation (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm'])) {
    try {
        $stmt = $conn->prepare("UPDATE attendance_sessions SET status = 'closed' WHERE id = ? AND status = 'open'");
        $stmt->execute([$session_id]);
        
        if ($stmt->rowCount() > 0) {
            $message = '<div style="color: green;">Session ' . $session_id . ' closed successfully!</div>';
        } else {
            $message = '<div style="color: orange;">Session not found or already closed.</div>';
        }
        $message .= '<br><a href="list_sessions.php">View All Sessions</a>';
    } catch (PDOException $e) {
        $message = '<div style="color: red;">Error closing session: ' . $e->getMessage() . '</div>';
    }
} else {
    // Fetch for confirmation (GET request)
    try {
        $stmt = $conn->prepare("SELECT course_id, group_id, date, status FROM attendance_sessions WHERE id = ?");
        $stmt->execute([$session_id]);
        $session = $stmt->fetch();
        
        if (!$session) {
            die('<div style="color: red;">Session not found. <a href="create_session.php">Create one?</a></div>');
        }
        if ($session['status'] !== 'open') {
            die('<div style="color: orange;">Session already closed. <a href="list_sessions.php">View All</a></div>');
        }
    } catch (PDOException $e) {
        die('Error fetching session: ' . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Close Session</title></head>
<body>
    <h2>Close Session ID: <?php echo $session_id; ?></h2>
    <?php if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['confirm'])): ?>
        <p>Details: Course <?php echo $session['course_id']; ?>, Group <?php echo $session['group_id']; ?>, Date: <?php echo $session['date']; ?></p>
        <p>Are you sure? This will set status to 'closed'.</p>
        <form method="POST">
            <button type="submit" name="confirm" style="padding: 10px; background: #f44336; color: white;">Yes, Close</button>
            <button type="button" onclick="window.location.href='list_sessions.php'" style="padding: 10px; background: #ddd;">Cancel</button>
        </form>
    <?php else: ?>
        <?php echo $message; ?>
    <?php endif; ?>
</body>
</html>