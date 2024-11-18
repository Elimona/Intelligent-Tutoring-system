<?php
session_start();
require_once __DIR__ . '/../../src/config/db.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if not logged in
    header("Location: ../login.php");
    exit;
}

// Fetch all users from the database in ascending order by ID
$stmt = $db->prepare("SELECT id, name, email, role, status FROM users ORDER BY id ASC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <link rel="stylesheet" href="css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .content-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .user-table th, .user-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .user-table th {
            background-color: #343a40;
            color: white;
        }

        .status-active {
            color: green;
        }

        .status-inactive {
            color: red;
        }

        .action-icons {
            display: flex;
            gap: 10px;
        }

        .action-icons .fas {
            cursor: pointer;
            font-size: 1.2rem;
            transition: transform 0.3s;
        }

        .action-icons .modify:hover {
            transform: scale(1.1);
        }

        .action-icons .delete:hover {
            transform: scale(1.1);
        }

        .success-message {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .deactivate, .activate {
            font-size: 1.5rem;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .deactivate:hover, .activate:hover {
            transform: scale(1.1);
        }

        .status-active-icon {
            color: #28a745; /* Light Green for Active */
        }

        .status-inactive-icon {
            color: #dc3545; /* Red for Inactive */
        }

    </style>
</head>
<body>
    <div class="content-wrapper">
        <h2>View All Users</h2>

        <!-- Display session message if set -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="success-message">
                <?= $_SESSION['message']; ?>
                <?php unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <!-- Users table -->
        <table class="user-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['id']) ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td class="<?= $user['status'] === 'active' ? 'status-active' : 'status-inactive' ?>">
                            <?= $user['status'] === 'active' ? 'Active' : 'Inactive' ?>
                            <!-- Toggle Active/Deactivate Link -->
                            <a href="operations/toggle_user_status.php?id=<?= $user['id'] ?>&status=<?= $user['status'] ?>" title="<?= $user['status'] === 'active' ? 'Deactivate' : 'Activate' ?>">
                                <i class="fas <?= $user['status'] === 'active' ? 'fa-user-check status-active-icon' : 'fa-user-slash status-inactive-icon' ?>"></i>
                            </a>
                        </td>
                        <td class="action-icons">
                            <!-- Modify Link -->
                            <a href="operations/modify_user.php?id=<?= $user['id'] ?>" title="Modify">
                                <i class="fas fa-edit modify"></i>
                            </a>

                            <!-- Delete Link (with POST) -->
                            <form action="operations/delete_user.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                <button type="submit" title="Delete" onclick="return confirm('Are you sure you want to delete this user?');" style="background: none; border: none;">
                                    <i class="fas fa-trash delete"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
