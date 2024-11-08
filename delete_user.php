<?php
session_start();
require 'connect.php';

// Check if the user is logged in as an admin
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header("Location: admin_login.php");
    exit;
}

// Check if user is a Manager
$role = $_SESSION['role'];
if ($role !== 'Manager') {
    header("Location: index.php"); // Redirect if not a Manager
    exit;
}

// Check if a user ID is provided
if (isset($_GET['user_id'])) {
    $userId = $_GET['user_id'];

    try {
        // Delete the user from the database
        $stmt = $db->prepare("DELETE FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        // Check if the user was successfully deleted
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "User deleted successfully.";
        } else {
            $_SESSION['error'] = "Failed to delete user.";
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error deleting user: " . $e->getMessage();
    }
} else {
    $_SESSION['error'] = "Invalid user ID.";
}

// Redirect back to the admin dashboard
header("Location: admin_dashboard.php");
exit;
?>
