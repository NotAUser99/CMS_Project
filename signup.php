<?php
// Connect to the database
require 'connect.php';

// Initialize variables for error messages
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validate the input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert the new user into the database
        $stmt = $db->prepare("INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, 'Member')");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        
        if ($stmt->execute()) {
            $success = "Registration successful! You can now log in.";
        } else {
            $error = "An error occurred. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>River Park South Buy&Sell</h1>
        <p>River Park South's own local trading hub</p>
    </header>

    <!-- Admin Button (Separate from Main Navigation) -->
    <div class="admin">
        <a href="admin_login.php">Admin</a>
    </div>

    <nav>
        <li><a href="index.php">Home</a></li>
        <li><a href="listings.php">Listings</a></li>
        <li><a href="about.php">About Us</a></li>
        <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
            <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
        <?php endif; ?>
        <?php if (isset($_SESSION['username'])): ?>
            <li><a href="logout.php">Logout</a></li>
        <?php else: ?>
            <li><a href="login.php">Log In</a></li>
        <?php endif; ?>
    </nav>

    <main>

        <h3>Registration</h3>
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success"><?= htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form action="signup.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" required>

            <label for="email">Email:</label>
            <input type="email" name="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" required>

            <button type="submit">Register</button>
        </form>
    </main>

    <footer>
        <p>&copy; <?= date("Y"); ?> River Park South Buy&Sell. All rights reserved.</p>
    </footer>
</body>
</html>
