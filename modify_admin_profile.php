<?php
session_start();
require_once __DIR__ . '/../../src/config/db.php';

$adminId = $_SESSION['user_id'] ?? null;
if (!$adminId) {
    header('Location: admin_dashboard.php');
    exit();
}

// Fetch current admin data
$stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND role = 'admin'");
$stmt->execute([$adminId]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    
    // Handle file upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
        $photoDir = 'uploads/';
        $photoFile = $photoDir . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $photoFile);
    } else {
        // Keep the old photo if no new photo is uploaded
        $photoFile = $admin['photo'];
    }

    // Update admin information in the database
    $query = $db->prepare("UPDATE users SET name = ?, email = ?, photo = ? WHERE id = ?");
    $query->execute([$name, $email, $photoFile, $adminId]);

    header('Location: admin_dashboard.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Modify Admin Profile</title>
    <link rel="stylesheet" href="css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> <!-- Font Awesome CDN -->
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f9f9f9;
            color: #333;
            margin: 0;
            padding: 20px;
            transition: background-color 0.3s, color 0.3s;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        form {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input[type="text"],
        input[type="email"],
        input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus,
        input[type="email"]:focus {
            border-color: #4CAF50;
        }
        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        img {
            border-radius: 50%;
            margin-top: 10px;
        }
        .dark-mode {
            background-color: #333;
            color: #f9f9f9;
        }
        .dark-mode form {
            background-color: #444;
        }
        .dark-mode input[type="text"],
        .dark-mode input[type="email"] {
            background-color: #555;
            color: #f9f9f9;
            border-color: #777;
        }
        .dark-mode input[type="submit"] {
            background-color: #2196F3;
        }
        .dark-mode input[type="submit"]:hover {
            background-color: #1976D2;
        }
    </style>
</head>
<body>
    <h2><i class="fas fa-user-edit"></i> Modify Profile</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="name">Name:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($admin['name']) ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($admin['email']) ?>" required>

        <label for="photo">Photo:</label>
        <input type="file" name="photo" accept="image/*"><br>
        <img src="<?= htmlspecialchars($admin['photo']) ?>" alt="Current Profile Picture" style="width: 100px; height: 100px;">

        <input type="submit" value="Update Profile">
    </form>
</body>
</html>
