<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$config = require 'config.php';

$servername = $config['db_host'];
$username = $config['db_user'];
$password = $config['db_password'];
$dbname = $config['db_name'];

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 데이터 가져오기
$sql = "SELECT id, name, artist, duration, src, img FROM musics";
$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

$conn->close();

// JSON으로 응답
echo json_encode($data);
