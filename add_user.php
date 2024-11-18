<?php
session_start();
require_once __DIR__ . '/../src/config/db.php';

// Check if the user is an admin
if ($_SESSION['user_role'] != 'admin') {
    header("Location: /unauthorized.php");
    exit;
}

// Initialize variables
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role = $_POST['role'] ?? '';
$courses = $_POST['courses'] ?? [];  // Array of selected courses for instructors

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // 1. Check if a department head already exists
        if ($role === 'department_head') {
            $query = $db->query("SELECT COUNT(*) FROM users WHERE role = 'department_head'");
            $existingDeptHead = $query->fetchColumn();
            if ($existingDeptHead > 0) {
                echo "A department head already exists. Only one department head is allowed.";
                exit;
            }
        }

        // 2. Insert new user into the users table
        $query = $db->prepare("INSERT INTO users (name, email, password, role, status) VALUES (:name, :email, :password, :role, 'active')");
        $query->execute([
            'name' => $name,
            'email' => $email,
            'password' => sha1($password),  // Using SHA-1 hashing for password
            'role' => $role,
        ]);

        $userId = $db->lastInsertId();  // Get the new user's ID

        // 3. Link instructor to courses if applicable
        if ($role === 'instructor' && !empty($courses)) {
            foreach ($courses as $courseId) {
                $courseQuery = $db->prepare("INSERT INTO instructor_courses (instructor_id, course_id) VALUES (:instructor_id, :course_id)");
                $courseQuery->execute(['instructor_id' => $userId, 'course_id' => $courseId]);
            }
        }

        echo "User added successfully!";
    } catch (PDOException $e) {
        echo "An error occurred: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add User</title>
    <link rel="stylesheet" href="path/to/bootstrap.css">
</head>
<body>
    <div class="container">
        <h2>Add New User</h2>
        <form action="add_user.php" method="POST">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required class="form-control">
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" required class="form-control">
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required class="form-control">
            </div>
            <div class="form-group">
                <label for="role">Role:</label>
                <select name="role" id="role" required class="form-control">
                    <option value="instructor">Instructor</option>
                    <option value="department_head">Department Head</option>
                </select>
            </div>

            <!-- Show courses selection only if the user is an instructor -->
            <div class="form-group">
                <label for="courses">Assign Courses (For Instructors):</label>
                <select name="courses[]" id="courses" class="form-control" multiple>
                    <?php
                    // Fetch courses from the database
                    $courseQuery = $db->query("SELECT id, course_name FROM courses");
                    $courses = $courseQuery->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($courses as $course) {
                        echo "<option value='{$course['id']}'>{$course['course_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Add User</button>
        </form>
    </div>
</body>
</html>
