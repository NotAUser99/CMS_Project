<?php
session_start();
require 'connect.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get the listing ID from the URL
$listing_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch the listing details from the database
$sql = "
    SELECT listings.*, users.username 
    FROM listings 
    JOIN users ON listings.user_id = users.user_id 
    WHERE listings.listing_id = ?
";
$stmt = $db->prepare($sql);
$stmt->execute([$listing_id]);
$listing = $stmt->fetch(PDO::FETCH_ASSOC);

// If the listing is not found, redirect or show an error
if (!$listing) {
    echo "<p>Listing not found.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listing Details</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Listing Details</h1>
    </header>

    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="listings.php">Listings</a></li>
            <?php if (isset($_SESSION['username'])): ?>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Log In</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <h2><?= htmlspecialchars($listing['title']) ?></h2>
        <p><strong>Posted by:</strong> <?= htmlspecialchars($listing['username']) ?></p>
        <p><strong>Created at:</strong> <?= htmlspecialchars($listing['created_at']) ?></p>
        <p><strong>Description:</strong> <?= nl2br(htmlspecialchars($listing['description'])) ?></p>
        <p><strong>Price:</strong> <?= htmlspecialchars($listing['price']) ?></p>
        <!-- Add any other fields you have in the listings table here -->
    </main>

    <footer>
        <p>&copy; <?= date("Y"); ?> PatChworks Project. All rights reserved.</p>
    </footer>
</body>
</html>
