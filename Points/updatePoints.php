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

$data = json_decode(file_get_contents("php://input"), true);
if (!isset($data["user_id"], $data["points_change"])) {
    echo json_encode(["status" => "error", "message" => "Missing user_id or points_change."]);
    exit;
}

$user_id = intval($data["user_id"]);
$points_change = intval($data["points_change"]);

$updateQuery = "UPDATE points SET points = points + $points_change WHERE user_id = $user_id";

if ($conn->query($updateQuery) === TRUE) {
    echo json_encode(["status" => "success", "message" => "Points updated successfully."]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to update points: " . $conn->error]);
}

$conn->close();
?>
