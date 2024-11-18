<?php
session_start();
require_once __DIR__ . '/../../src/config/db.php';

if (isset($_GET['action']) && isset($_GET['student_id'])) {
    $action = $_GET['action'];
    $student_id = $_GET['student_id'];

    if ($action === 'activate') {
        // Activate student
        $query = $db->prepare("UPDATE students SET status = 'active' WHERE id = :student_id");
    } elseif ($action === 'deactivate') {
        // Deactivate student
        $query = $db->prepare("UPDATE students SET status = 'inactive' WHERE id = :student_id");
    } else {
        header('Location: view_students.php');
        exit;
    }

    $query->bindParam(':student_id', $student_id);
    if ($query->execute()) {
        // Redirect back to view students
        header('Location: view_students.php');
        exit;
    } else {
        echo "Error updating student status.";
    }
} else {
    header('Location: view_students.php');
    exit;
}
