<?php
session_start();
require_once __DIR__ . '/../../../src/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$fullName = $email = $courseId = "";
$errors = [];
$generatedPassword = "";

// Fetch courses that are not assigned to any instructor
$courses = [];
$stmt = $db->prepare("SELECT c.id, c.course_name FROM courses c LEFT JOIN instructors i ON c.id = i.course_id WHERE i.course_id IS NULL");
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to generate a random password
function generatePassword($length = 10) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
    return substr(str_shuffle($characters), 0, $length);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST['full_name'];
    $courseId = $_POST['course_id'];

    // Generate email from full name
    if (!empty($fullName)) {
        $nameParts = explode(" ", strtolower($fullName));
        $email = count($nameParts) > 1
            ? $nameParts[0] . "." . $nameParts[1] . "@wsumail.com"
            : $nameParts[0] . "@wsumail.com";
    } else {
        $errors[] = "Full name is required to generate an email.";
    }

    // Generate password
    if (empty($generatedPassword)) {
        $generatedPassword = generatePassword();
    }

    if (empty($fullName)) $errors[] = "Full name is required.";
    if (empty($courseId)) $errors[] = "Course selection is required.";

    if (empty($errors)) {
        // Hash the generated password before saving
        $hashedPassword = password_hash($generatedPassword, PASSWORD_BCRYPT);

        // Insert into users table first
        $stmt = $db->prepare("INSERT INTO users (email, password, role, status) VALUES (?, ?, 'instructor', 'active')");
        $stmt->execute([$email, $hashedPassword]);

        // Get the last inserted user ID
        $userId = $db->lastInsertId();

        // Insert into instructors table with the user_id as the foreign key
        $stmt = $db->prepare("INSERT INTO instructors (instructor_id, course_id, full_name, email, password, status) VALUES (?, ?, ?, ?, ?, 'active')");
        $stmt->execute([$userId, $courseId, $fullName, $email, $hashedPassword]);

        $_SESSION['success_message'] = "Instructor added successfully! Generated Password: $generatedPassword";
        header("Location: add_instructor.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Instructor</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            max-width: 600px;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        h2 {
            color: #007bff;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            transition: transform 0.2s;
        }
        .btn-primary:hover {
            transform: scale(1.05);
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .form-label {
            font-weight: 500;
        }
        .alert {
            margin-top: 20px;
        }
        .btn-back {
            position: absolute;
            top: 10px;
            left: 10px;
            color: #007bff;
            text-decoration: none;
        }
        .btn-back:hover {
            text-decoration: underline;
        }
        .text-muted {
            font-size: 0.9em;
        }
    </style>
</head>
<body>
<a href="view_instructors.php" class="btn-back">&larr; Back to Instructors</a>
<div class="container">
    <h2>Add New Instructor</h2>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?= $_SESSION['success_message'] ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="add_instructor.php">
        <div class="mb-3">
            <label for="full_name" class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" id="full_name" value="<?= htmlspecialchars($fullName) ?>" required aria-describedby="nameHelp">
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email (Auto-generated)</label>
            <input type="text" name="email" class="form-control" id="email" value="<?= htmlspecialchars($email) ?>" readonly>
            <button type="button" class="btn btn-secondary mt-2" onclick="generateEmail()">Generate Email</button>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password (Auto-generated)</label>
            <input type="text" name="password" class="form-control" id="password" value="<?= htmlspecialchars($generatedPassword) ?>" readonly>
            <button type="button" class="btn btn-secondary mt-2" onclick="generatePassword()">Generate Password</button>
        </div>
        <div class="mb-3">
            <label for="course_id" class="form-label">Course</label>
            <select name="course_id" class="form-select form-control" id="course_id" required>
                <option value="">Select a course</option>
                <?php if (empty($courses)): ?>
                    <option value="" disabled>No available courses</option>
                <?php else: ?>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?= htmlspecialchars($course['id']) ?>" <?= $courseId == $course['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($course['course_name']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
            <?php if (empty($courses)): ?>
                <small class="text-muted">All courses are already assigned to instructors.</small>
            <?php endif; ?>
        </div>
        <button type="submit" class="btn btn-primary w-100">Add Instructor</button>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script>
function generateEmail() {
    const fullName = document.getElementById("full_name").value.trim().toLowerCase();
    if (fullName) {
        const nameParts = fullName.split(" ");
        const email = nameParts.length > 1
            ? `${nameParts[0]}.${nameParts[1]}@wsumail.com`
            : `${nameParts[0]}@wsumail.com`;
        document.getElementById("email").value = email;
    } else {
        alert("Please enter a full name first.");
    }
}

function generatePassword() {
    const password = Math.random().toString(36).slice(-10); // Generate a 10-character password
    document.getElementById("password").value = password;
}
</script>
</body>
</html>
