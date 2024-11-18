<?php
session_start();
require_once __DIR__ . '/../../src/config/db.php';

// Check if the user is logged in, if not, redirect to login page
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get user details from session
$userId = $_SESSION['user_id']; 
$userName = $_SESSION['user_name'] ?? "User"; // Default to "User" if name is not set
$profilePicture = "/path/to/default/profile/picture.jpg"; 

// Fetch user profile data from the database
$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Update profile picture path if it exists
if ($user && $user['photo']) {
    $profilePicture = htmlspecialchars($user['photo']);
}

// Logout functionality
if (isset($_POST['logout'])) {
    // Destroy session and redirect to login page
    session_destroy();
    header("Location: ../login.php");
    exit();
}

// Fetch all instructors data (excluding photo)
$stmt_instructors = $db->prepare("SELECT id, instructor_id, course_id, full_name, email, created_at, updated_at, status FROM instructors");
$stmt_instructors->execute();
$instructors = $stmt_instructors->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome CDN -->
    <style>
        /* Add additional styling for sidebar and elements */
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            transition: background-color 0.3s, color 0.3s;
            display: flex;
            flex-direction: column;
        }
        /* Sidebar styling */
        .main-sidebar {
            background-color: #343a40;
            min-width: 250px;
            padding: 20px 0;
            position: fixed;
            top: 0;
            bottom: 0;
            overflow-y: auto;
        }
        .main-sidebar a {
            color: #c2c7d0;
            display: block;
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .main-sidebar a:hover, .main-sidebar a.active {
            background-color: #495057;
            color: #fff;
        }
        .main-sidebar .brand-link {
            display: flex;
            align-items: center;
            padding: 15px;
            text-align: center;
        }
        .main-sidebar .brand-link img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .content-wrapper {
            margin-left: 250px;
            padding: 20px;
            width: calc(100% - 250px);
        }
        /* Logout button styling */
        .logout-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        .logout-btn:hover {
            background-color: #c82333;
        }
        /* Table styling */
        .instructor-table {
            width: 100%;
            border-collapse: collapse;
        }
        .instructor-table th, .instructor-table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        .instructor-table th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="dashboard.php" class="brand-link">
            <img src="<?= htmlspecialchars($profilePicture) ?>" alt="User Logo" class="brand-image img-circle">
            <span class="brand-text"><?= htmlspecialchars($userName) ?></span>
        </a>

        <!-- Sidebar Menu -->
        <nav>
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat">
                <li class="nav-item">
                    <a href="admin_dashboard.php" class="nav-link nav-home">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="view_users.php" class="nav-link nav-users">
                        <i class="nav-icon fas fa-user-check"></i>
                        <p>View All Users</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="view_students.php" class="nav-link nav-students">
                        <i class="nav-icon fas fa-users"></i>
                        <p>View All Students</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="view_instructors.php" class="nav-link nav-instructors">
                        <i class="nav-icon fas fa-chalkboard-teacher"></i>
                        <p>View All Instructors</p>
                    </a>
                </li>
                <!-- Add more links as needed -->
            </ul>
        </nav>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <header>
            <h1>Welcome, <?= htmlspecialchars($userName) ?>!</h1>
        </header>
        <div class="container">
            <h2>Dashboard</h2>
            <section>
                <a href="view_users.php" class="btn"><i class="fas fa-user-check"></i> View All Users</a>
                <a href="view_students.php" class="btn"><i class="fas fa-users"></i> View All Students</a>
                <a href="view_instructors.php" class="btn"><i class="fas fa-chalkboard-teacher"></i> View All Instructors</a>
            </section>
            <section>
                <h3>Instructors List</h3>
                <table class="instructor-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Course ID</th>
                            <th>Created At</th>
                            <th>Updated At</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($instructors as $instructor): ?>
                        <tr>
                            <td><?= htmlspecialchars($instructor['instructor_id']) ?></td>
                            <td><?= htmlspecialchars($instructor['full_name']) ?></td>
                            <td><?= htmlspecialchars($instructor['email']) ?></td>
                            <td><?= htmlspecialchars($instructor['course_id']) ?></td>
                            <td><?= htmlspecialchars($instructor['created_at']) ?></td>
                            <td><?= htmlspecialchars($instructor['updated_at']) ?></td>
                            <td><?= htmlspecialchars($instructor['status']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        </div>
    </div>

    <!-- Logout Button -->
    <form method="POST">
        <button type="submit" name="logout" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </form>

    <script>
        // Highlight active link based on current page
        document.addEventListener("DOMContentLoaded", function() {
            let page = new URL(window.location.href).pathname.split("/").pop().replace(".php", "");
            let navLink = document.querySelector(`.nav-${page}`);
            if (navLink) navLink.classList.add("active");
        });
    </script>
</body>
</html>
