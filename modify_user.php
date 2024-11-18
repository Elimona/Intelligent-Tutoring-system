<?php
require_once __DIR__ . '/../../../src/config/db.php';

// Fetch the number of instructors, students, and users
$stmt_instructors = $db->prepare("SELECT COUNT(*) AS total FROM instructors");
$stmt_instructors->execute();
$instructors = $stmt_instructors->fetch(PDO::FETCH_ASSOC);

$stmt_students = $db->prepare("SELECT COUNT(*) AS total FROM students");
$stmt_students->execute();
$students = $stmt_students->fetch(PDO::FETCH_ASSOC);

$stmt_users = $db->prepare("SELECT COUNT(*) AS total FROM users");
$stmt_users->execute();
$users = $stmt_users->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f7f7f7;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            margin: 30px auto;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
        }

        .stats-box {
            text-align: center;
            padding: 20px;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .instructor-box {
            background-color: #007bff;
            color: white;
            width: 200px;
            height: 150px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 8px;
            font-size: 1.5em;
        }

        .student-box {
            background-color: #28a745;
            color: white;
            width: 200px;
            height: 150px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 8px;
            font-size: 1.5em;
        }

        .user-box {
            background-color: #ffc107;
            color: black;
            width: 200px;
            height: 150px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 8px;
            font-size: 1.5em;
        }

        /* Triangle Shape */
        .triangle {
            width: 0;
            height: 0;
            border-left: 100px solid transparent;
            border-right: 100px solid transparent;
            border-bottom: 150px solid #f0ad4e;
            position: relative;
        }

        .triangle .count {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 1.5em;
            color: black;
        }

        /* Navigation Section */
        .nav-bar {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
        }

        .nav-bar a {
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 10px;
        }

        .nav-bar a:hover {
            background-color: #444;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<!-- Navigation Bar -->
<div class="nav-bar">
    <a href="view_users.php">View All Users</a>
    <a href="view_students.php">View All Students</a>
    <a href="view_instructors.php">View All Instructors</a>
</div>

<!-- Stats Section -->
<div class="container">
    <!-- Instructors Box -->
    <div class="stats-box instructor-box">
        <div class="count"><?= $instructors['total'] ?> Instructors</div>
    </div>

    <!-- Students Box (Triangle Shape) -->
    <div class="stats-box student-box">
        <div class="count"><?= $students['total'] ?> Students</div>
    </div>

    <!-- Users Box (Triangle Shape) -->
    <div class="stats-box">
        <div class="triangle">
            <div class="count"><?= $users['total'] ?> Users</div>
        </div>
    </div>
</div>

</body>
</html>
