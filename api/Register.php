<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
// ob_start();

// Allow Cross-Origin Requests (CORS) for development purposes
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle preflight requests
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

// DB connection
$conn = new mysqli("localhost", "root", "mysql", "christmasdb");

// Check connection
if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $conn->connect_error
    ]);
    http_response_code(500); // Internal Server Error
    exit;
}

// Decode the JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Input validation
if (!isset($data["email"], $data["name"], $data["password"])) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required fields: email, name, or password."
    ]);
    http_response_code(400); // Bad Request
    exit;
}

$email = $conn->real_escape_string(trim($data["email"]));
$name = $conn->real_escape_string(trim($data["name"]));
$password = $conn->real_escape_string(trim($data["password"]));

// Check if the email is already registered
$checkEmailQuery = "SELECT * FROM register WHERE email = '$email'";
$result = $conn->query($checkEmailQuery);

if ($result === false) {
    echo json_encode([
        "status" => "error",
        "message" => "Database error: " . $conn->error
    ]);
    http_response_code(500); // Internal Server Error
    exit;
}

if ($result->num_rows > 0) {
    echo json_encode([
        "status" => "error",
        "message" => "This email is already registered."
    ]);
}

// New Users
// Hash the password for security
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert new user into the database
$insertQuery = "INSERT INTO register (username, email, password, created_at) 
                VALUES ('$name', '$email', '$hashedPassword', NOW())";

if ($conn->query($insertQuery) === TRUE) {
    echo json_encode([
        "status" => "success",
        "message" => "Registration successful."
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Error inserting data: " . $conn->error
    ]);
    http_response_code(500); // Internal Server Error
}

// Close the database connection
$conn->close();
// ob_end_clean();
?>
