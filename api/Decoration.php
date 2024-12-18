<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle Preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit();
}

$config = require 'config.php';

$servername = $config['db_host'];
$username = $config['db_user'];
$password = $config['db_password'];
$dbname = $config['db_name'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

// Bring the Data
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT id, deco_name, img, points FROM ornaments";
    $result = $conn->query($sql);

    $data = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    $conn->close();
    echo json_encode($data);
    exit();
}

// Data Add and Modify(Update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['deco_name'], $data['points'])) {
        $name = $conn->real_escape_string(trim($data['deco_name']));
        $points = intval($data['points']);
        $imgPath = isset($data['img']) ? $conn->real_escape_string(trim($data['img'])) : "default.svg";

        if (isset($data['id']) && $data['id']) {
            // About Modify
            $id = intval($data['id']);
            $updateQuery = "UPDATE ornaments SET deco_name = '$name', points = '$points', img = '$imgPath' WHERE id = '$id'";

            if ($conn->query($updateQuery) === TRUE) {
                echo json_encode(["status" => "update success"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => $conn->error]);
            }
        } else {
            // About Add
            $insertQuery = "INSERT INTO ornaments (deco_name, points, img) VALUES ('$name', '$points', '$imgPath')";
            if ($conn->query($insertQuery) === TRUE) {
                echo json_encode(["status" => "insert success"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => $conn->error]);
            }
        }
    } else {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid or missing fields"]);
    }

    $conn->close();
    exit();
} else {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
}
?>
