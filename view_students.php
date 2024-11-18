<?php
session_start();
require_once __DIR__ . '/../../src/config/db.php';
$query = $db->query("SELECT * FROM students");
$students = $query->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Students</title>
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 5px;
            overflow: hidden;
            background-color: #fff;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 12px;
            transition: background-color 0.3s;
        }
        th {
            background-color: #4CAF50;
            color: white;
            text-transform: uppercase;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .status {
            display: flex;
            align-items: center;
            font-weight: bold;
        }
        .status-icon {
            font-size: 1.2em;
            margin-left: 8px;
            cursor: pointer;
            transition: color 0.3s;
        }
        .status-icon.active {
            color: lightgreen;
        }
        .status-icon.inactive {
            color: red;
        }
        img.student-photo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <h2><i class="fas fa-users"></i> View Students</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Photo</th>
                <th>Name</th>
                <th>Email</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= htmlspecialchars($student['id']) ?></td>
                    <td>
                        <?php if (!empty($student['photo'])): ?>
                            <img src="<?= htmlspecialchars($student['photo']) ?>" alt="Profile Photo" class="student-photo">
                        <?php else: ?>
                            <img src="Test/Public/uploads/default_photo.png" alt="Default Photo" class="student-photo">
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($student['full_name']) ?></td>
                    <td><?= htmlspecialchars($student['email']) ?></td>
                    <td>
                        <div class="status">
                            <?php if ($student['status'] == 'active'): ?>
                                <span>Active</span>
                                <a href="process_admin.php?action=deactivate&student_id=<?= $student['id'] ?>" class="status-icon active"><i class="fas fa-check-circle"></i></a>
                            <?php else: ?>
                                <span>Inactive</span>
                                <a href="process_admin.php?action=activate&student_id=<?= $student['id'] ?>" class="status-icon inactive"><i class="fas fa-times-circle"></i></a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
