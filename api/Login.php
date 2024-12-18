<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// DB connection
$conn = new mysqli("localhost", "root", "mysql", "christmasdb");
if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection failed: " . $conn->connect_error
    ]);
    exit;
}

// JSON
$data = json_decode(file_get_contents("php://input"), true);
if (!$data || !isset($data["email"], $data["password"])) {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid input or missing fields."
    ]);
    exit;
}

$email = $conn->real_escape_string(trim($data["email"]));
$password = trim($data["password"]);

// Searching the user
$query = "SELECT id, username, email, password, role, points FROM register WHERE email = '$email'";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user["password"])) {
        // When the password matches
        unset($user["password"]);

        // Log the login attempt in the Audit log
        recordAuditLog($user['username'], $user['email']);

        echo json_encode([
            "status" => "success",
            "message" => "Login successful.",
            "user" => $user
        ]);
    } else {
        // When the password doesn't match
        echo json_encode([
            "status" => "error",
            "message" => "Incorrect password."
        ]);
    }
} else {
    // no email
    echo json_encode([
        "status" => "error",
        "message" => "User not found. Register first."
    ]);
}

$conn->close();

// Audit log function to record login information
function recordAuditLog($username, $email) {
    $auditFolder = 'Audit'; // Audit folder route
    $logFile = $auditFolder . '/login_log.txt'; // log file route

    // content
    $logData = sprintf(
        "[%s] - User: %s, Email: %s\n", 
        date('Y-m-d H:i:s'),
        $username,
        $email
    );

    // If there's no audit folder
    if (!is_dir($auditFolder)) {
        mkdir($auditFolder, 0777, true);
    }

    // add log
    file_put_contents($logFile, $logData, FILE_APPEND);
}
?>

