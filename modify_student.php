<?php
session_start();
require_once __DIR__ . '/../../src/config/db.php';

// Check if a student ID is provided
if (!isset($_GET['student_id'])) {
    header('Location: view_students.php');
    exit;
}

$student_id = $_GET['student_id'];

// Fetch student details
$query = $db->prepare("SELECT * FROM students WHERE id = :student_id");
$query->bindParam(':student_id', $student_id);
$query->execute();
$student = $query->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    // Redirect if student not found
    header('Location: view_students.php');
    exit;
}

// Initialize variables
$new_password = null; // For storing new password

// Handle form submission for modifying student information
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];

    // Handle password reset
    if (isset($_POST['reset_password'])) {
        $new_password = bin2hex(random_bytes(4)); // Generate a random 8-character password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT); // Hash the new password

        // Update only the full name for now
        $updateQuery = $db->prepare("UPDATE students SET full_name = :full_name WHERE id = :student_id");
        $updateQuery->bindParam(':full_name', $full_name);
        $updateQuery->bindParam(':student_id', $student_id);
        
        if ($updateQuery->execute()) {
            // If full name is updated, show the new password
            $success_message = "Password reset successful. New Password: $new_password";
        } else {
            $error_message = "Failed to update student information.";
        }
    } else {
        // Update only the full name and password
        $updateQuery = $db->prepare("UPDATE students SET full_name = :full_name, password = :new_password WHERE id = :student_id");
        $updateQuery->bindParam(':new_password', $hashed_password);
        $updateQuery->bindParam(':full_name', $full_name);
        $updateQuery->bindParam(':student_id', $student_id);
        
        if ($updateQuery->execute()) {
            $success_message = "Student information updated successfully.";
            // Redirect to the view students page after saving changes
            header('Location: view_students.php');
            exit; // Ensure no further code is executed after redirection
        } else {
            $error_message = "Failed to update student information.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Student</title>
    <link rel="stylesheet" href="css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        form {
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="email"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
        .success {
            color: green;
            margin-top: 10px;
        }
        .reset-password {
            background-color: #2196F3; /* Blue */
        }
        .reset-password:hover {
            background-color: #1976D2; /* Darker blue */
        }
    </style>
</head>
<body>
    <h2><i class="fas fa-user-edit"></i> Modify Student</h2>
    <?php if (isset($error_message)): ?>
        <div class="error"><?= htmlspecialchars($error_message) ?></div>
    <?php endif; ?>
    <?php if (isset($success_message)): ?>
        <div class="success"><?= htmlspecialchars($success_message) ?></div>
    <?php endif; ?>
    
    <form action="" method="POST">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" required>
        </div>

        <?php if ($new_password): ?>
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="text" id="new_password" name="new_password" value="<?= htmlspecialchars($new_password) ?>" readonly>
            </div>
        <?php endif; ?>

        <button type="submit" class="btn"><i class="fas fa-save"></i> Save Changes</button>
        <button type="submit" name="reset_password" class="btn reset-password"><i class="fas fa-redo"></i> Reset Password</button>
    </form>
</body>
</html>
