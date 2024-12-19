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

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM music");
    $songs = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($songs);
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];
    $name = $data['name'];
    $artist = $data['artist'];
    $duration = $data['duration'];

    $conn->query("UPDATE music SET name='$name', artist='$artist', duration='$duration' WHERE id=$id");
    echo json_encode(["status" => "update success"]);
}

if ($method === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    $conn->query("DELETE FROM music WHERE id=$id");
    echo json_encode(["status" => "delete success"]);
}

$conn->close();
?>