<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Intelligent Tutoring System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #e0f7e4; /* Light green background */
            color: #2c6e49; /* Dark green for text */
        }
        .container {
            max-width: 400px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 100px;
        }
        h2 {
            color: #2c6e49;
        }
        .form-control {
            border-color: #a4d4b4; /* Light green border */
        }
        .form-control:focus {
            border-color: #2c6e49;
            box-shadow: 0 0 5px rgba(44, 110, 73, 0.4);
        }
        .btn-primary {
            background-color: #2c6e49; /* Dark green button */
            border: none;
        }
        .btn-primary:hover {
            background-color: #3f8d5b;
        }
        label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center mt-3"><i class="fas fa-user-lock"></i> Login</h2>
        <form action="authenticate.php" method="POST" class="mt-4">
            <div class="form-group">
                <label for="username"><i class="fas fa-envelope"></i> Email:</label>
                <input type="email" name="username" id="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> Password:</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt"></i> Login</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
