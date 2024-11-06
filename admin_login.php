<?php
session_start();
require 'connect.php'; // Include the database connection file

// Redirect if already logged in as admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_dashboard.php");
    exit;
}

$error = ""; // To store any error messages

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch user from the database
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify user exists and role is either 'Manager' or 'Moderator'
    if ($user && ($user['role'] === 'Manager' || $user['role'] === 'Moderator')) {
        // Verify the password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role']; // Store the role for role-based access
            
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Incorrect password.";
        }
    } else {
        $error = "Access denied. Only administrators can log in.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
</head>
<body>
    <h1>Admin Login</h1>
    <form method="POST" action="">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <?php if ($error) echo "<p style='color:red;'>$error</p>"; ?>
</body>
</html>
