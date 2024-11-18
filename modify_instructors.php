<?php
session_start();
require_once __DIR__ . '/../../../src/config/db.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check if the instructor ID is passed in the URL
if (!isset($_GET['id'])) {
    die("Instructor ID is missing.");
}

// Fetch the instructor data based on the ID
$instructorId = $_GET['id'];
$stmt_instructor = $db->prepare("SELECT id, full_name, email, password, photo, status FROM instructors WHERE id = ?");
$stmt_instructor->execute([$instructorId]);
$instructor = $stmt_instructor->fetch(PDO::FETCH_ASSOC);

// Check if the instructor exists
if (!$instructor) {
    die("Instructor not found.");
}

// Handle form submission to update the instructor's name or email
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_instructor'])) {
        $full_name = $_POST['full_name'];
        $email = $_POST['email'];

        // Update the instructor's details in the database
        $updateStmt = $db->prepare("UPDATE instructors SET full_name = ?, email = ? WHERE id = ?");
        $updateStmt->execute([$full_name, $email, $instructorId]);

        // Display success message
        $success_message = "Instructor details updated successfully.";
    }

    // Handle password reset
    if (isset($_POST['reset_password'])) {
        // Generate a random password
        $new_password = bin2hex(random_bytes(8)); // 16-character password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password in the database
        $updatePasswordStmt = $db->prepare("UPDATE instructors SET password = ? WHERE id = ?");
        $updatePasswordStmt->execute([$hashed_password, $instructorId]);

        // Display the new password
        $new_password_message = "New password: " . $new_password;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modify Instructor</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
        }
        .content-wrapper {
            margin: 20px;
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-action {
            padding: 10px 20px;
        }
    </style>
</head>
<body>

<!-- Sidebar and Header (if any) -->
<!-- You can include your sidebar here if needed -->

<div class="content-wrapper">
    <header>
        <h1 class="text-center mb-4">Modify Instructor Details</h1>
    </header>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?= $success_message ?></div>
    <?php endif; ?>
    
    <?php if (isset($new_password_message)): ?>
        <div class="alert alert-info"><?= $new_password_message ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($instructor['full_name']) ?>" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($instructor['email']) ?>" required>
        </div>

        <div class="form-group">
            <label for="status">Status</label>
            <select class="form-control" id="status" name="status">
                <option value="active" <?= ($instructor['status'] == 'active') ? 'selected' : '' ?>>Active</option>
                <option value="inactive" <?= ($instructor['status'] == 'inactive') ? 'selected' : '' ?>>Inactive</option>
            </select>
        </div>

        <!-- Update Instructor Details Button -->
        <button type="submit" name="update_instructor" class="btn btn-primary btn-action">Update Details</button>
    </form>

    <br>

    <form method="POST">
        <!-- Reset Password Button -->
        <button type="submit" name="reset_password" class="btn btn-danger btn-action">Reset Password</button>
    </form>

    <br>

    <a href="view_instructors.php" class="btn btn-secondary btn-action">Back to Instructor List</a>
</div>

<!-- Bootstrap JS & jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
