<?php
session_start();
require_once __DIR__ . '/../../../src/config/db.php';

// Ensure that both 'id' and 'status' are set
if (!isset($_GET['id'], $_GET['status'])) {
    echo "Invalid request.";
    exit;
}

$id = (int)$_GET['id']; // Get user ID
$currentStatus = $_GET['status']; // Get current status (active or deactivated)

// Toggle status logic
$newStatus = $currentStatus === 'active' ? 'deactivated' : 'active'; // Switch status

try {
    // Prepare the SQL statement to update the user status
    $stmt = $db->prepare("UPDATE users SET status = :status WHERE id = :id");
    $stmt->execute(['status' => $newStatus, 'id' => $id]); // Execute the update

    // Redirect back to the view users page
    header("Location: ../view_users.php");
    exit;
} catch (PDOException $e) {
    echo "An error occurred: " . $e->getMessage(); // Handle potential errors
}
