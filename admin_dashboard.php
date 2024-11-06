<?php
session_start();
require 'connect.php';

// Check if the user is logged in as an admin
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: admin_login.php");
    exit;
}

// Check if user is a Manager or Moderator
$role = $_SESSION['role'];
if ($role !== 'Manager' && $role !== 'Moderator') {
    header("Location: index.php"); // Redirect if not a Manager or Moderator
    exit;
}

// Fetch users if Manager role
$users = [];
if ($role === 'Manager') {
    try {
        $query = "SELECT * FROM users";
        $stmt = $db->query($query);
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching users: " . $e->getMessage();
    }
}

// Fetch listings for both Managers and Moderators
$listings = [];
try {
    $query = "SELECT * FROM listings";
    $stmt = $db->query($query);
    $listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching listings: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Admin Dashboard</h1>
        <p>Welcome, <?= htmlspecialchars($_SESSION['username']); ?> (<?= htmlspecialchars($role); ?>)</p>
    </header>

    <nav>
        <a href="index.php" class="button">Home</a>
        <a href="create_user.php" class="button">Add New User</a>
        <a href="admin_dashboard.php" class="button">Manage Users</a>
        <a href="logout.php" class="button">Logout</a>
    </nav>

    <main>
        <?php if ($role === 'Manager'): ?>
            <h2>User Management</h2>
            <table class="user-table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['user_id']); ?></td>
                            <td><?= htmlspecialchars($user['username']); ?></td>
                            <td><?= htmlspecialchars($user['email']); ?></td>
                            <td><?= htmlspecialchars($user['phone']); ?></td>
                            <td><?= htmlspecialchars($user['role']); ?></td>
                            <td>
                                <a href="edit_user.php?user_id=<?= $user['user_id']; ?>" class="button">Edit</a>
                                <a href="delete_user.php?user_id=<?= $user['user_id']; ?>" class="button" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Listing Management Section -->
        <h2>Listing Management</h2>
        <a href="create_listing.php" class="button">Add New Listing</a>
        <table class="listing-table">
            <thead>
                <tr>
                    <th>Listing ID</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listings as $listing): ?>
                    <tr>
                        <td><?= htmlspecialchars($listing['listing_id']); ?></td>
                        <td><?= htmlspecialchars($listing['title']); ?></td>
                        <td><?= htmlspecialchars($listing['description']); ?></td>
                        <td>$<?= htmlspecialchars($listing['price']); ?></td>
                        <td>
                            <a href="edit_listing.php?listing_id=<?= $listing['listing_id']; ?>" class="button">Edit</a>
                            <a href="delete_listing.php?listing_id=<?= $listing['listing_id']; ?>" class="button" onclick="return confirm('Are you sure you want to delete this listing?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</body>
</html>
