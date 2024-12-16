<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// 데이터베이스 연결
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "christmasdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 데이터 가져오기
$sql = "SELECT id, name, artist, duration, src, img FROM music";
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
