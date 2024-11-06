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

    // Fetch the listing details to display in the form
    $query = "SELECT title, description, price FROM listings WHERE listing_id = :listing_id AND (user_id = :user_id OR :user_is_admin)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        ':listing_id' => $listing_id,
        ':user_id' => $user_id,
        ':user_is_admin' => $_SESSION['role'] === 'Manager' || $_SESSION['role'] === 'Moderator'
    ]);

    $listing = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$listing) {
        echo "Error: Listing not found or you do not have permission to edit this listing.";
        exit;
    }
    ?>

    <!-- Display the edit form with current listing information -->
    <form action="edit_listing.php" method="POST">
        <input type="hidden" name="listing_id" value="<?php echo htmlspecialchars($listing_id); ?>">
        
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($listing['title']); ?>" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" required><?php echo htmlspecialchars($listing['description']); ?></textarea>

        <label for="price">Price:</label>
        <input type="number" name="price" id="price" value="<?php echo htmlspecialchars($listing['price']); ?>" step="0.01" required>

        <button type="submit">Update Listing</button>
        <button type="button" onclick="window.location.href='admin_dashboard.php'">Cancel</button>
    </form>

    <?php
    exit;
}

// Process form submission on POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['listing_id'], $_POST['title'], $_POST['description'], $_POST['price'])) {
    $listing_id = $_POST['listing_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $user_id = $_SESSION['user_id'];

    // Update listing if it belongs to the logged-in user or the user is an admin
    $query = "UPDATE listings SET title = :title, description = :description, price = :price WHERE listing_id = :listing_id AND (user_id = :user_id OR :user_is_admin)";
    $stmt = $db->prepare($query);

    try {
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':price' => $price,
            ':listing_id' => $listing_id,
            ':user_id' => $user_id,
            ':user_is_admin' => $_SESSION['role'] === 'Manager' || $_SESSION['role'] === 'Moderator'
        ]);

        // Check if the listing exists even if no rows were modified
        if ($stmt->rowCount() > 0 || $stmt->errorCode() === '00000') {
            echo "Listing updated successfully!";
            header("Location: admin_dashboard.php");
            exit;
        } else {
            echo "Error: You do not have permission to edit this listing or it does not exist.";
        }
    } catch (PDOException $e) {
        echo "Error updating listing: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
