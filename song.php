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

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "test_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM music");
    $songs = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($songs);
}

if ($method === 'POST') {

    if (isset($_FILES['src']) && isset($_FILES['img'])) {
        $musicFile = $_FILES['src'];
        $imageFile = $_FILES['img'];

        $musicPath = 'Music/song/' . basename($musicFile['name']);
        $imagePath = 'Music/song/album/' . basename($imageFile['name']);

        if (move_uploaded_file($musicFile['tmp_name'], $musicPath) && move_uploaded_file($imageFile['tmp_name'], $imagePath)) {
            $name = $_POST['song_name'];
            $artist = $_POST['artist'];
            $duration = $_POST['duration'];

                        // backend URL
                        $baseUrl = "http://localhost/webdev/test-haru/chritsmasBackend/";

                        $musicUrl = $baseUrl . $musicPath;
                        $imageUrl = $baseUrl . $imagePath;

            $stmt = $conn->prepare("INSERT INTO music (song_name, artist, duration, src, img) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $artist, $duration, $musicPath, $imagePath);
            $stmt->execute();

            echo json_encode(["status" => "insert success", "newItem" => [
                "id" => $stmt->insert_id,
                "name" => $name,
                "artist" => $artist,
                "duration" => $duration,
                "src" => $musicPath,
                "img" => $imagePath
            ]]);
        } else {
            echo json_encode(["status" => "error", "message" => "File upload failed"]);
        }
    } else {

        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'];
        $name = $data['name'];
        $artist = $data['artist'];
        $duration = $data['duration'];

        $stmt = $conn->prepare("UPDATE music SET name=?, artist=?, duration=? WHERE id=?");
        $stmt->bind_param("sssi", $name, $artist, $duration, $id);
        $stmt->execute();

        echo json_encode(["status" => "update success"]);
    }
}

if ($method === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'];

    $stmt = $conn->prepare("DELETE FROM music WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    echo json_encode(["status" => "delete success"]);
}

$conn->close();
?>
