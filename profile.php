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
$dbname = "christmasdb";

// Create a new MySQLi connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection error
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    $userId = $input['id'] ?? null;
    $newUsername = $input['username'] ?? null;

    if ($userId && $newUsername) {
        // Update username
        $stmt = $conn->prepare("UPDATE register SET username = ? WHERE id = ?");
        $stmt->bind_param("si", $newUsername, $userId);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Username updated successfully"]);
        } else {
            echo json_encode(["success" => false, "error" => "Failed to update username"]);
        }

        $stmt->close();
    } elseif ($userId) {
        // Retrieve username
        $stmt = $conn->prepare("SELECT username FROM register WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            echo json_encode(["username" => $user['username']]);
        } else {
            echo json_encode(["error" => "User not found"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => "Invalid input data"]);
    }
} else {
    echo json_encode(["error" => "Invalid request method"]);
}

$conn->close();
?>
