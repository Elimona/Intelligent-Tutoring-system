<?php
session_start();
require_once __DIR__ . '/../src/config/db.php';

// Redirect non-admin users
if ($_SESSION['user_role'] != 'admin') {
    header("Location: /unauthorized.php");
    exit;
}

// Path to the log file
$logFile = __DIR__ . '/../src/logs/system_logs.txt'; // Adjust path as necessary

// Read the log file
$logs = [];
if (file_exists($logFile)) {
    $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
} else {
    $logs = ["No logs available."];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Logs - Intelligent Tutoring System</title>
    <link rel="stylesheet" href="css/admin_style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #4CAF50;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        .back-button {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .back-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>System Logs</h1>
    <table>
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>Log Entry</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= htmlspecialchars(explode("] ", $log)[0] . "]") ?></td>
                    <td><?= htmlspecialchars(explode("] ", $log)[1]) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a class="back-button" href="admin_dashboard.php">Back to Dashboard</a>
</body>
</html>
