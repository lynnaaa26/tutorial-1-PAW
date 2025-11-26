<?php
$jsonFile = 'students.json';
$message = '';  // For feedback

// Load existing students
$students = [];
if (file_exists($jsonFile)) {
    $jsonData = file_get_contents($jsonFile);
    $students = json_decode($jsonData, true) ?: [];
} else {
    // Create empty file if not exists
    file_put_contents($jsonFile, json_encode([]));
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = trim($_POST['student_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $group = trim($_POST['group'] ?? '');

    // Validation
    $errors = [];
    if (empty($student_id)) $errors[] = "Student ID is required.";
    if (empty($name)) $errors[] = "Name is required.";
    if (empty($group)) $errors[] = "Group is required.";
    
    // Check uniqueness
    foreach ($students as $student) {
        if ($student['student_id'] === $student_id) {
            $errors[] = "Student ID '$student_id' already exists.";
            break;
        }
    }

    if (empty($errors)) {
        // Add new student
        $newStudent = [
            'student_id' => $student_id,
            'name' => $name,
            'group' => $group
        ];
        $students[] = $newStudent;
        
        // Save back to JSON
        if (file_put_contents($jsonFile, json_encode($students, JSON_PRETTY_PRINT)) !== false) {
            $message = '<div style="color: green;">Student added successfully!</div>';
        } else {
            $message = '<div style="color: red;">Error saving data.</div>';
        }
    } else {
        $message = '<div style="color: red;">Errors: ' . implode(' ', $errors) . '</div>';
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Add Student</title></head>
<body>
    <h2>Add New Student</h2>
    <?php echo $message; ?>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        Student ID: <input type="text" name="student_id" value="<?php echo htmlspecialchars($_POST['student_id'] ?? ''); ?>" required><br><br>
        Name: <input type="text" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required><br><br>
        Group: <input type="text" name="group" value="<?php echo htmlspecialchars($_POST['group'] ?? ''); ?>" required><br><br>
        <button type="submit" style="padding: 10px; background: #4CAF50; color: white;">Add Student</button>
    </form>
    <br><a href="take_attendance.php">Take Attendance</a>
</body>
</html>