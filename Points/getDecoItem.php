<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid user_id.']);
    exit();
}

$user_id = $_GET['user_id'];

$conn = new mysqli("localhost", "root", "mysql", "christmasdb");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

// user_id가 데이터베이스에 존재하는지 확인하는 쿼리
$sql = "SELECT * FROM register WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // user_id가 유효하지 않으면 오류 메시지 반환
    echo json_encode(['status' => 'error', 'message' => 'Invalid user_id.']);
    exit();
}

// deco_items 테이블에서 해당 user_id에 대한 tree_items 가져오기
$sql = "SELECT tree_items FROM deco_items WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $tree_items = json_decode($row['tree_items'], true); // JSON을 배열로 디코딩

    echo json_encode(['status' => 'success', 'items' => $tree_items]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No deco items found.']);
}

$conn->close();
?>
