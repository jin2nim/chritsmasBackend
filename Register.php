<?php
// DB connection - 이 부분은 나중에 config.php 파일로 따로 뺴도 좋을 것 같아.
$conn = new mysqli("localhost", "root", "mysql", "christmasdb");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Allow Cross-Origin Requests (CORS) for development purposes
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// 프리플라이트 요청 처리
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    http_response_code(200);
    exit;
}

// Only handle POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    // Stop execution for non-POST requests
    http_response_code(405); // 405 Method Not Allowed
    exit;
}

// Decode the JSON input
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data["email"], $data["name"], $data["password"])) {
    $email = $conn->real_escape_string(trim($data["email"]));
    $name = $conn->real_escape_string(trim($data["name"]));
    $password = $conn->real_escape_string(trim($data["password"]));

    // Check if the email is already registered
    $checkEmailQuery = "SELECT * FROM register WHERE email = '$email'";
    $result = $conn->query($checkEmailQuery);

    if ($result->num_rows > 0) {
        echo json_encode([
            "status" => "error",
            "message" => "This email is already registered."
        ]);
    } else {
        // Hash the password for security
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user into the database
        $insertQuery = "INSERT INTO register (username, email, password, created_at) 
                        VALUES ('$name', '$email', '$hashedPassword', NOW())";
        if ($conn->query($insertQuery) === TRUE) {
            echo json_encode([
                "status" => "success",
                "message" => "Registration successful."
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Error: " . $conn->error
            ]);
        }
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required fields."
    ]);
}

ob_end_clean();

// Close the database connection
$conn->close();
?>