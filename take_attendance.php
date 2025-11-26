<?php
$studentsFile = 'students.json';
$today = date('Y-m-d');  // e.g., '2025-11-25'
$attendanceFile = "attendance_{$today}.json";
$message = '';  // For feedback

// Load students
$students = [];
if (file_exists($studentsFile)) {
    $jsonData = file_get_contents($studentsFile);
    $students = json_decode($jsonData, true) ?: [];
}

if (empty($students)) {
    $message = '<div style="color: orange;">No students found. <a href="add_student.php">Add some first?</a></div>';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($message)) {
    // Check if today's attendance exists
    if (file_exists($attendanceFile)) {
        $message = '<div style="color: orange;">Attendance for today has already been taken.</div>';
    } else {
        $attendance = [];
        foreach ($students as $student) {
            $status = $_POST["status_{$student['student_id']}"] ?? 'absent';
            $attendance[] = [
                'student_id' => $student['student_id'],
                'status' => $status
            ];
        }
        
        // Save to JSON
        if (file_put_contents($attendanceFile, json_encode($attendance, JSON_PRETTY_PRINT)) !== false) {
            $message = '<div style="color: green;">Attendance saved for ' . $today . '!</div>';
        } else {
            $message = '<div style="color: red;">Error saving attendance.</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Take Attendance</title></head>
<body>
    <h2>Take Attendance - <?php echo $today; ?></h2>
    <?php echo $message; ?>
    <?php if (empty($message) || strpos($message, 'already been taken') === false): ?>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <?php foreach ($students as $student): ?>
        <p>
            <?php echo htmlspecialchars($student['name']); ?> (ID: <?php echo $student['student_id']; ?>, Group: <?php echo htmlspecialchars($student['group']); ?>)
            <br>
            <label><input type="radio" name="status_<?php echo $student['student_id']; ?>" value="present" <?php echo (($_POST["status_{$student['student_id']}"] ?? '') === 'present' ? 'checked' : ''); ?>> Present</label>
            <label><input type="radio" name="status_<?php echo $student['student_id']; ?>" value="absent" <?php echo (($_POST["status_{$student['student_id']}"] ?? '') === 'absent' ? 'checked' : ''); ?>> Absent</label>
        </p>
        <?php endforeach; ?>
        <button type="submit" style="padding: 10px; background: #2196F3; color: white;">Submit Attendance</button>
    </form>
    <?php endif; ?>
    <br><a href="add_student.php">Add Student</a>
</body>
</html>