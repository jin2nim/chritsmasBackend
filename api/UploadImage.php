<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

$upload_dir = __DIR__ . "/svg/decoration/";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $fileName = $data['fileName'];

    if ($fileName) {
        $path = "svg/decoration/" . basename($fileName);

        echo json_encode(["status" => "success", "imgPath" => $path]);
    } else {
        echo json_encode(["status" => "error"]);
    }
}
?>
