<?php
// Enable error reporting for debugging (remove/comment out in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db_connect.php';
$pdo = getDBConnection();
if (!$pdo) {
    die('DB connection failed. Check logs/db_errors.log');
}

$tableHtml = '';  // We'll build the table here

try {
    $stmt = $pdo->query("SELECT * FROM students ORDER BY fullname");
    $students = $stmt->fetchAll();
    
    if (empty($students)) {
        $tableHtml = '<p>No students found.</p>';
    } else {
        $tableHtml = '<table border="1" style="border-collapse: collapse; width: 100%;">
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Matricule</th>
                            <th>Group ID</th>
                            <th>Actions</th>
                        </tr>';
        foreach ($students as $student) {
            $tableHtml .= '<tr>
                            <td>' . $student['id'] . '</td>
                            <td>' . htmlspecialchars($student['fullname']) . '</td>
                            <td>' . htmlspecialchars($student['matricule']) . '</td>
                            <td>' . $student['group_id'] . '</td>
                            <td>
                                <a href="update_student.php?id=' . $student['id'] . '">Edit</a> |
                                <a href="delete_student.php?id=' . $student['id'] . '" onclick="return confirm(\'Delete ' . htmlspecialchars($student['fullname']) . '?\')">Delete</a>
                            </td>
                          </tr>';
        }
        $tableHtml .= '</table>';
    }
} catch (PDOException $e) {
    $tableHtml = '<div style="color: red;">Error fetching students: ' . $e->getMessage() . '</div>';
}
?>
<!DOCTYPE html>
<html>
<head><title>List Students</title></head>
<body>
    <h2>Students List</h2>
    <?php echo $tableHtml; ?>
    <br><a href="add_student.php">Add New Student</a>
</body>
</html>