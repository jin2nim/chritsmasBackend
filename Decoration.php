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

// 데이터베이스 연결
$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "christmasdb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

// 데이터 조회
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT id, deco_name, img, points FROM decoration";
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

// 데이터 추가 또는 수정
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['name'], $data['points'])) {
        $name = $conn->real_escape_string(trim($data['name']));
        $points = intval($data['points']);
        $imgPath = isset($data['img']) ? $conn->real_escape_string(trim($data['img'])) : "default.svg";

        if (isset($data['id']) && $data['id']) {
            // 수정 로직
            $id = intval($data['id']);
            $updateQuery = "UPDATE decoration SET deco_name = '$name', points = '$points', img = '$imgPath' WHERE id = '$id'";

            if ($conn->query($updateQuery) === TRUE) {
                echo json_encode(["status" => "update success"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => $conn->error]);
            }
        } else {
            // 신규 추가 로직
            $insertQuery = "INSERT INTO decoration (deco_name, points, img) VALUES ('$name', '$points', '$imgPath')";
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
