<?php  
header('Access-Control-Allow-Origin: *');  
header('Content-Type: application/json');  
  
$config = require 'config.php';  
  
$servername =$config['db_host'];  
$username =$config['db_user'];  
$password =$config['db_password'];  
$dbname =$config['db_name'];  
  
$conn = new mysqli($servername, $username, $password, $dbname);  
  
if ($conn->connect_error) {  
   http_response_code(500);  
   echo json_encode(["error" => $conn->connect_error]);  
   exit();  
}  
  
// POST 데이터 확인  
$jsonData = json_decode(file_get_contents("php://input"), true);  
  
// 디버그: JSON 데이터 확인  
error_log("Received JSON data: " . print_r($jsonData, true));  
  
// Audit Log 기록  
$userID = $jsonData['userID'] ?? null;  
$email = $jsonData['email'] ?? null;  
$ip_address = $jsonData['ip_address'] ?? '';  
$action = $jsonData['action'] ?? '';  
$message = $jsonData['message'] ?? '';  
  
$stmt =$conn->prepare(  
   "INSERT INTO audit_log (userID, email, ip_address, action, message, timestamp)  
    VALUES (?, ?, ?, ?, ?, NOW())"  
);  
  
$stmt->bind_param("issss", $userID,$email, $ip_address, $action, $message);  
  
if ($stmt->execute()) {  
   echo json_encode(["status" => "success"]);  
} else {  
   http_response_code(500);  
   echo json_encode(["status" => "error", "error" => $stmt->error]);  
}  
  
$stmt->close();  
$conn->close();  
?>
