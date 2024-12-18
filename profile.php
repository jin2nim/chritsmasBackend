<?php
// Allow CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

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

// Get data from POST request
$userId = $_POST['userId'];
$newUserName = $_POST['newUserName'];
$newProfileImage = $_POST['newProfileImage'];

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
