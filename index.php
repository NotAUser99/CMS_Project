<?php
session_start(); // Start the session
require 'connect.php';

// Fetch the 10 most recent listings
$sql = "
    SELECT listing_id, title
    FROM listings
    ORDER BY created_at DESC
    LIMIT 10
";
$stmt = $db->prepare($sql);
$stmt->execute();
$recent_listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My CMS Project</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <!-- Header Section -->
    <header>
        <h1>River Park South Buy&Sell</h1>
        <p>River Park South's own local trading hub</p>
    </header>

    <!-- Admin Button (Separate from Main Navigation) -->
    <div class="admin">
        <a href="admin_login.php">Admin</a>
    </div>

    <!-- Navigation Bar -->
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="listings.php">Listings</a></li>
            <li><a href="signup.php">Sign Up</a></li>
            <li><a href="about.php">About Us</a></li>
            <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
                <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
            <?php endif; ?>
            <?php if (isset($_SESSION['username'])): ?>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Log In</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Main Content Section -->
    <main>
        <h2>Welcome to our Community!</h2>
        <p>Join our secure platform for local transactions. Buy, sell, and connect with confidence.</p>
    </main>

    <!-- Footer Section -->
    <footer>
        <p>&copy; <?= date("Y"); ?> PatChworks Project. All rights reserved.</p>
    </footer>
</body>
</html>
