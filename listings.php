<?php
session_start(); // Start the session
require 'connect.php'; // Include your database connection

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Set default sorting parameters
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'created_at';
$order = isset($_GET['order']) && $_GET['order'] === 'asc' ? 'asc' : 'desc';
$next_order = $order === 'asc' ? 'desc' : 'asc';

// Validate sorting column to prevent SQL injection
$allowed_sort_columns = ['title', 'username', 'created_at'];
if (!in_array($sort_by, $allowed_sort_columns)) {
    $sort_by = 'created_at';
}

// Fetch listings from the database with sorting
$sql = "
    SELECT listings.listing_id, listings.title, listings.created_at, users.username
    FROM listings
    JOIN users ON listings.user_id = users.user_id
    ORDER BY $sort_by $order
";
$stmt = $db->prepare($sql);
$stmt->execute();
$listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listings</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Listings Page</h1>
    </header>

    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="about.php">About Us</a></li>
            <?php if (isset($_SESSION['username'])): ?>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Log In</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <main>
        <h2>Community Listings</h2>

        <table>
            <thead>
                <tr>
                    <th><a href="?sort_by=title&order=<?= $next_order ?>">Title <?= $sort_by === 'title' ? "($order)" : '' ?></a></th>
                    <th><a href="?sort_by=username&order=<?= $next_order ?>">Posted By <?= $sort_by === 'username' ? "($order)" : '' ?></a></th>
                    <th><a href="?sort_by=created_at&order=<?= $next_order ?>">Created At <?= $sort_by === 'created_at' ? "($order)" : '' ?></a></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listings as $listing): ?>
                    <tr>
                        <td><a href="listing_details.php?id=<?= htmlspecialchars($listing['listing_id']) ?>"><?= htmlspecialchars($listing['title']) ?></a></td>
                        <td><?= htmlspecialchars($listing['username']) ?></td>
                        <td><?= htmlspecialchars($listing['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </main>

    <footer>
        <p>&copy; <?= date("Y"); ?> PatChworks Project. All rights reserved.</p>
    </footer>
</body>
</html>

