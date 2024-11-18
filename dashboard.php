<?php
// public/dashboard.php

session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /login.php");  // Redirect to login if not authenticated
    exit;
}

echo "<h1>Welcome to the Dashboard</h1>";
echo "<p>User Role: " . $_SESSION['user_role'] . "</p>";

// Additional dashboard content based on user role could go here
