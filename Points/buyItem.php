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
if (!isset($data["user_id"], $data["item_id"])) {
    echo json_encode(["status" => "error", "message" => "Missing user_id or item_id."]);
    exit;
}

$user_id = intval($data["user_id"]);
$item_id = intval($data["item_id"]);

// get deco_name and img From decoration table
$getDecorationQuery = "SELECT deco_name, img FROM decoration WHERE id = ?";
$stmt = $conn->prepare($getDecorationQuery);
$stmt->bind_param("i", $item_id);
$stmt->execute();
$decorationResult = $stmt->get_result();

if ($decorationResult->num_rows > 0) {
    $decorationData = $decorationResult->fetch_assoc();
    $deco_name = $decorationData["deco_name"];
    $img = $decorationData["img"];
} else {
    echo json_encode(["status" => "error", "message" => "Item not found in decoration table."]);
    exit;
}

// Get Tree_items From deco_items table
$sql = "SELECT tree_items FROM deco_items WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $tree_items = json_decode($row['tree_items'], true); // 기존 아이템 가져오기

    // insert Item into Tree
    $emptySpot = array_search(null, $tree_items);
    if ($emptySpot !== false) {
        $tree_items[$emptySpot] = [
            "id" => $item_id,
            "deco_name" => $deco_name,
            "img" => $img
        ];

        // tree_items Update
        $updatedTreeItems = json_encode($tree_items);
        $updateQuery = "UPDATE deco_items SET tree_items = ? WHERE user_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $updatedTreeItems, $user_id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Item added to tree successfully."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to update tree items."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Tree is full."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "No deco items found for user."]);
}

$conn->close();
?>
