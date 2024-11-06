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

// Check that it's a POST request and that required fields are set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['description'], $_POST['price'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $user_id = $_SESSION['user_id'];

    // Validate and sanitize input (optional, recommended for security)
    $title = htmlspecialchars($title);
    $description = htmlspecialchars($description);
    $price = filter_var($price, FILTER_VALIDATE_FLOAT);

    if ($price === false) {
        echo "Error: Invalid price format.";
        exit;
    }

    // Prepare SQL query to insert the new listing
    $query = "INSERT INTO listings (title, description, price, user_id, created_at) VALUES (:title, :description, :price, :user_id, NOW())";
    $stmt = $db->prepare($query);

    try {
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':price' => $price,
            ':user_id' => $user_id
        ]);

        echo "Listing created successfully!";
        header("Location: admin_dashboard.php");
        exit;
    } catch (PDOException $e) {
        echo "Error creating listing: " . $e->getMessage();
    }
} else {
    echo "Invalid request. Please fill out all required fields.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Listing</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Add New Listing</h1>
    <form method="POST">
        <label>Title:</label>
        <input type="text" name="title" required><br>
        <label>Description:</label>
        <textarea name="description" required></textarea><br>
        <label>Price:</label>
        <input type="number" name="price" required><br>
        <button type="submit">Add Listing</button>
    </form>
</body>
</html>
