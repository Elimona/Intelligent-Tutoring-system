<?php
session_start();
require_once __DIR__ . '/../../src/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$stmt_instructors = $db->prepare("SELECT id, instructor_id, course_id, full_name, email, status FROM instructors");
$stmt_instructors->execute();
$instructors = $stmt_instructors->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['action']) && isset($_GET['id'])) {
    $instructorId = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'activate') {
        $updateStmt = $db->prepare("UPDATE instructors SET status = 'active' WHERE id = ?");
        $updateStmt->execute([$instructorId]);
    } elseif ($action == 'deactivate') {
        $updateStmt = $db->prepare("UPDATE instructors SET status = 'inactive' WHERE id = ?");
        $updateStmt->execute([$instructorId]);
    }

    header("Location: view_instructors.php");
    exit();
}

if (isset($_POST['activate_all'])) {
    $updateStmt = $db->prepare("UPDATE instructors SET status = 'active'");
    $updateStmt->execute();
    header("Location: view_instructors.php");
    exit();
}

if (isset($_POST['deactivate_all'])) {
    $updateStmt = $db->prepare("UPDATE instructors SET status = 'inactive'");
    $updateStmt->execute();
    header("Location: view_instructors.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Instructors</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        /* Add styles as needed */
    </style>
</head>
<body>
<div class="content-wrapper">
    <header class="text-center">
        <h1>Instructors List <i class="fas fa-chalkboard-teacher"></i></h1>
    </header>

    <div class="mb-4 text-center">
        <!-- Bulk Activation/Deactivation Buttons -->
        <form method="POST" class="d-inline">
            <button type="submit" name="activate_all" class="btn btn-success">
                <i class="fas fa-check-circle"></i> Activate All
            </button>
            <button type="submit" name="deactivate_all" class="btn btn-danger">
                <i class="fas fa-ban"></i> Deactivate All
            </button>
        </form>
        <!-- Link to Add Instructor Page -->
        <a href="operations/add_instructor.php" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Add Instructor
        </a>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Course ID</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($instructors as $instructor): ?>
                <tr>
                    <td><?= htmlspecialchars($instructor['instructor_id']) ?></td>
                    <td><?= htmlspecialchars($instructor['full_name']) ?></td>
                    <td><?= htmlspecialchars($instructor['email']) ?></td>
                    <td><?= htmlspecialchars($instructor['course_id']) ?></td>
                    <td>
                        <?php if ($instructor['status'] == 'active'): ?>
                            <span class="text-success"><i class="fas fa-user-check"></i> Active</span>
                        <?php else: ?>
                            <span class="text-danger"><i class="fas fa-user-times"></i> Inactive</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="operations/modify_instructors.php?id=<?= $instructor['id'] ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
