<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Error: User not logged in.";
    exit;
}

// Check if `listing_id` is provided in the URL for GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['listing_id'])) {
    $listing_id = $_GET['listing_id'];
    $user_id = $_SESSION['user_id'];

    // Check if the user is an admin (Manager or Moderator)
    $is_admin = $_SESSION['role'] === 'Manager' || $_SESSION['role'] === 'Moderator';

    try {
        // Delete the listing if it belongs to the logged-in user or if the user is an admin
        $query = "DELETE FROM listings WHERE listing_id = :listing_id AND (user_id = :user_id OR :is_admin)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':listing_id' => $listing_id,
            ':user_id' => $user_id,
            ':is_admin' => $is_admin ? 1 : 0
        ]);

        // Check if the deletion was successful
        if ($stmt->rowCount() > 0) {
            // Set a success message in session
            $_SESSION['message'] = "Listing deleted successfully!";
        } else {
            $_SESSION['message'] = "Error: You do not have permission to delete this listing or it does not exist.";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error deleting listing: " . $e->getMessage();
    }

    // Redirect back to the admin dashboard
    header("Location: admin_dashboard.php");
    exit;
} else {
    $_SESSION['message'] = "Invalid request.";
    header("Location: admin_dashboard.php");
    exit;
}
?>
