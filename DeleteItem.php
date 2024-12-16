<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // JSON 입력 데이터를 읽습니다.
    $input = json_decode(file_get_contents("php://input"), true);
    if (!isset($input['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID is missing']);
        exit();
    }

    $id = intval($input['id']);

    $servername = "localhost";
    $username = "root";
    $password = "mysql";
    $dbname = "christmasdb";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        echo json_encode(['status' => 'error', 'message' => 'DB connection error']);
        exit();
    }

    try {
        $stmt = $conn->prepare("DELETE FROM decorations WHERE id = ?");
        if (!$stmt) {
            throw new Exception('Prepare failed');
        }

        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'delete success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete']);
        }
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
?>