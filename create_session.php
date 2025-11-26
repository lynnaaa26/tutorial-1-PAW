<?php
require_once 'db_connect.php';  // Assuming this returns PDO via db_connect()

$message = '';  // For feedback

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = (int)($_POST['course_id'] ?? 0);
    $group_id = (int)($_POST['group_id'] ?? 0);
    $professor_id = (int)($_POST['professor_id'] ?? 0);

    // Validation
    if ($course_id <= 0 || $group_id <= 0 || $professor_id <= 0) {
        $message = '<div style="color: red;">All fields must be positive integers.</div>';
    } else {
        try {
            $conn = db_connect();  // Your function

            $stmt = $conn->prepare("
                INSERT INTO attendance_sessions (course_id, group_id, date, opened_by, status)
                VALUES (?, ?, CURDATE(), ?, 'open')
            ");
            $stmt->execute([$course_id, $group_id, $professor_id]);
            $session_id = $conn->lastInsertId();
            $message = '<div style="color: green;">Session created successfully! Session ID: ' . $session_id . '</div>';
            $message .= '<br><a href="close_session.php?id=' . $session_id . '">Close This Session</a>';
        } catch (PDOException $e) {
            $message = '<div style="color: red;">Error creating session: ' . $e->getMessage() . '</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Create Session</title></head>
<body>
    <h2>Create New Attendance Session</h2>
    <?php echo $message; ?>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        Course ID: <input type="number" name="course_id" min="1" required><br><br>
        Group ID: <input type="number" name="group_id" min="1" required><br><br>
        Professor ID: <input type="number" name="professor_id" min="1" required><br><br>
        <button type="submit" style="padding: 10px; background: #4CAF50; color: white;">Create Session</button>
    </form>
    <br><a href="list_sessions.php">View All Sessions</a>  <!-- Optional: Create this file if needed -->
</body>
</html>