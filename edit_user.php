<?php
session_start();
require 'connect.php';

// Check if the user is logged in as an admin
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: admin_login.php");
    exit;
}

// Ensure the user has Manager or Moderator role
$role = $_SESSION['role'];
if ($role !== 'Manager' && $role !== 'Moderator') {
    header("Location: index.php");
    exit;
}

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    echo "No user specified!";
    exit;
}

// Fetch the current user details
try {
    $query = "SELECT * FROM users WHERE user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found!";
        exit;
    }
} catch (PDOException $e) {
    echo "Error fetching user: " . $e->getMessage();
    exit;
}

// Handle form submission for updating user details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];

    try {
        $updateQuery = "UPDATE users SET username = :username, email = :email, phone = :phone, role = :role WHERE user_id = :user_id";
        $stmt = $db->prepare($updateQuery);
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'role' => $role,
            'user_id' => $user_id
        ]);

        // Redirect back to the admin dashboard after a successful update
        header("Location: admin_dashboard.php");
        exit;
    } catch (PDOException $e) {
        echo "Error updating user: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Edit User</h1>
        <p>Admin: <?= htmlspecialchars($_SESSION['username']); ?> (<?= htmlspecialchars($role); ?>)</p>
    </header>

    <main>
        <form action="" method="post" class="user-form">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user['username']); ?>" required>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']); ?>" required>

            <label for="role">Role:</label>
            <select id="role" name="role">
                <option value="Member" <?= $user['role'] === 'Member' ? 'selected' : ''; ?>>Member</option>
                <option value="Moderator" <?= $user['role'] === 'Moderator' ? 'selected' : ''; ?>>Moderator</option>
                <option value="Manager" <?= $user['role'] === 'Manager' ? 'selected' : ''; ?>>Manager</option>
            </select>

            <button type="submit" class="button">Update User</button>
            <a href="admin_dashboard.php" class="button">Cancel</a>
        </form>
    </main>
</body>
</html>
