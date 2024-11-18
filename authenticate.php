<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../src/config/db.php';

$email = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo "Please provide both email and password.";
    exit;
}

try {
    // Check in instructors table first
    $queryInstructors = $db->prepare("SELECT * FROM instructors WHERE email = :email LIMIT 1");
    $queryInstructors->execute(['email' => $email]);
    $instructor = $queryInstructors->fetch(PDO::FETCH_ASSOC);

    // If user found in instructors table, check password and redirect to instructor dashboard
    if ($instructor) {
        // Check if the instructor is active
        if ($instructor['status'] !== 'active') {
            echo "Your account is inactive. Please contact the administrator.";
            exit;
        }

        // Check if the password is hashed or plain text
        if (password_verify($password, $instructor['password'])) {
            // Hashed password matches
            $_SESSION['instructor_id'] = $instructor['id']; // Store instructor ID
            $_SESSION['instructor_name'] = $instructor['full_name']; // Store full name
            header("Location: instructor/instructor_dashboard.php");
            exit;
        } elseif ($password === $instructor['password']) {
            // Plain text password matches (for legacy systems)
            $_SESSION['instructor_id'] = $instructor['id']; // Store instructor ID
            $_SESSION['instructor_name'] = $instructor['full_name']; // Store full name
            header("Location: instructor/instructor_dashboard.php");
            exit;
        } else {
            echo "Invalid email or password.";
        }
    } else {
        // Check in users table (admin and department head)
        $queryUsers = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $queryUsers->execute(['email' => $email]);
        $user = $queryUsers->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Check if the user is active
            if ($user['status'] !== 'active') {
                echo "Your account is inactive. Please contact the administrator.";
                exit;
            }

            // Check if the password is hashed or plain text for users table
            if (password_verify($password, $user['password'])) {
                // Hashed password matches
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['name'];

                // Redirect to the appropriate dashboard based on role
                switch ($user['role']) {
                    case 'admin':
                        header("Location: admin/admin_dashboard.php");
                        break;
                    case 'department_head':
                        header("Location: department_head/department_head_dashboard.php");
                        break;
                    default:
                        echo "Invalid role.";
                        exit;
                }
                exit;
            } elseif ($password === $user['password']) {
                // Plain text password matches (for legacy systems)
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['name'];

                // Redirect to the appropriate dashboard based on role
                switch ($user['role']) {
                    case 'admin':
                        header("Location: admin/admin_dashboard.php");
                        break;
                    case 'department_head':
                        header("Location: department_head/department_head_dashboard.php");
                        break;
                    default:
                        echo "Invalid role.";
                        exit;
                }
                exit;
            } else {
                echo "Invalid email or password.";
            }
        } else {
            echo "User not found.";
        }
    }
} catch (PDOException $e) {
    echo "An error occurred: " . $e->getMessage();
    exit;
}
