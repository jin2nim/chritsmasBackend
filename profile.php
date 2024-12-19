<?php
// Allow CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Start the session (if not already started in other files)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database credentials
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "test_db";

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection error
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Get the login_id from the session
$userId = $_SESSION['login_id'];

// Prepare SQL to fetch the user's username
$sql = "SELECT username FROM register WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(["error" => "Failed to prepare SQL statement."]);
    exit;
}

// Bind the userId to the SQL statement
$stmt->bind_param("i", $userId);







// Prepare SQL to update the user's data
$sql = "UPDATE user_images SET image_path = ? WHERE userId = ?";
$stmt = $conn->prepare($sql);

// Check if prepare failed
if ($stmt === false) {
    die(json_encode(["error" => "Failed to prepare SQL statement"]));
}

// Bind parameters
$stmt->bind_param("si", $newProfileImage, $userId);

// Execute the statement
if ($stmt->execute()) {
    // Return success response
    echo json_encode(["success" => true]);
} else {
    // Return error response
    echo json_encode(["error" => "Failed to update profile image"]);
}

// Close the statement and connection
$stmt->close();
$conn->close();
?>
