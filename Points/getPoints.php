<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "mysql", "christmasdb");
if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

$user_id = intval($_GET["user_id"] ?? 0);
if ($user_id === 0) {
    echo json_encode(["status" => "error", "message" => "Invalid user_id."]);
    exit;
}

$query = "SELECT points FROM points WHERE user_id = $user_id";
$result = $conn->query($query);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(["status" => "success", "points" => $row["points"]]);
} else {
    echo json_encode(["status" => "error", "message" => "User not found or points not available."]);
}

$conn->close();
?>
