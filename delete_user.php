<?php
session_start();

// Correct the path to db.php
require_once __DIR__ . '/../../../src/config/db.php'; 

// Check if the user ID is set in the POST request
if (isset($_POST['id'])) {
    $userId = $_POST['id'];

    // Prepare and execute the delete query
    $stmt = $db->prepare("DELETE FROM users WHERE id = :id");
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    if ($stmt->execute()) {
        // Set success message and redirect back to the users page
        $_SESSION['message'] = "User deleted successfully.";
        $_SESSION['message_type'] = 'success';
    } else {
        // Set error message and redirect back
        $_SESSION['message'] = "Failed to delete user.";
        $_SESSION['message_type'] = 'danger';
    }
} else {
    // Invalid request if ID is not set
    $_SESSION['message'] = "Invalid request.";
    $_SESSION['message_type'] = 'danger';
}

// Redirect back to the users page
header('Location: ../view_users.php');
exit;
