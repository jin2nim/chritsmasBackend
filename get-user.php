<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$host = "localhost";
$user = "root";
$password = "mysql";
$database = "test_db";

$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// SQL to fetch all users あとで他の情報をもってくる
$stmt = $conn->prepare("
    SELECT u.userId, u.name, u.email, u.role, ui.image_path AS profile_image 
    FROM user_tb u 
    LEFT JOIN user_images ui ON u.userId = ui.userId
");
$stmt->execute();
$result = $stmt->get_result();

$users = [];
if ($result->num_rows > 0) {
    while ($user = $result->fetch_assoc()) {
        $users[] = $user;
    }
    echo json_encode($users);
} else {
    echo json_encode(["error" => "No users found"]);
}

$stmt->close();
$conn->close();
?>
