<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

$servername = "localhost";
$username = "root";
$password = "mysql";
$dbname = "christmasdb";

// Create a connection to the database
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Connection failed: " . $e->getMessage()]);
    exit;
}

// POST request - Save recent play data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    $music_id = isset($_POST['music_id']) ? $_POST['music_id'] : null;

    if ($user_id && $music_id) {
        // Insert the recent play into the recent_music table
        $sql = "INSERT INTO recent_music (user_id, music_id) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);

        if ($stmt->execute([$user_id, $music_id])) {
            // Successfully inserted
            echo json_encode(["success" => true, "message" => "Recent play data saved successfully"]);
        } else {
            // If insertion fails
            echo json_encode(["success" => false, "message" => "Error saving data"]);
        }
    } else {
        // If required data is missing
        echo json_encode(["success" => false, "message" => "Missing user_id or music_id"]);
    }
}

// GET request - Get recently played music
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get the user_id from the GET request (e.g., ?user_id=1)
    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : null;

    if ($user_id) {
        // Get recent music data for the user
        $sql = "SELECT rm.id, rm.user_id, rm.music_id, rm.played_at, m.name, m.artist, m.img
                FROM recent_music rm
                JOIN music m ON rm.music_id = m.id
                WHERE rm.user_id = ?
                ORDER BY rm.played_at DESC LIMIT 5"; // Get 5 most recent plays

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(1, $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $recentMusic = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($recentMusic);
    } else {
        echo json_encode(["success" => false, "message" => "Missing user_id"]);
    }
}

// Close the connection
$pdo = null;
?>
