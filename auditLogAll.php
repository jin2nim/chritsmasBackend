<?php
header("Access-Control-Allow-Origin: http://localhost:3000"); // Allow requests from React server
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow HTTP methods
header("Access-Control-Allow-Headers: Content-Type"); // Allow headers
header("Content-Type: application/json");

$config = require 'config.php';

try {
    $pdo = new PDO(
        "mysql:host={$config['db_host']};dbname={$config['db_name']};charset={$config['charset']}",
        $config['db_user'],
        $config['db_password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $method = $_SERVER['REQUEST_METHOD'];

    if ($method === 'GET') {
        // Retrieve search, filter, and date parameters
        $search = isset($_GET['search']) ? $_GET['search'] : null;
        $filter = isset($_GET['filter']) ? $_GET['filter'] : null;
        $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
        $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;
        $sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'DESC'; // Default DESC
    
        // Construct base query
        $query = "SELECT * FROM audit_log WHERE 1=1";
    
        // Add search condition
        if ($search) {
            $query .= " AND (email LIKE :search OR ip_address LIKE :search)";
        }
    
        // Add filter condition
        if ($filter) {
            $query .= " AND login_state = :filter";
        }
    
        // Add date range condition
        if ($start_date && $end_date) {
            $query .= " AND timestamp BETWEEN :start_date AND :end_date";
        }
    
        // Add sorting
        $query .= " ORDER BY timestamp $sort_order";
    
        // Prepare statement
        $stmt = $pdo->prepare($query);
    
        // Bind parameters
        if ($search) {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }
        if ($filter) {
            $stmt->bindValue(':filter', $filter, PDO::PARAM_STR);
        }
        if ($start_date && $end_date) {
            $stmt->bindValue(':start_date', $start_date, PDO::PARAM_STR);
            $stmt->bindValue(':end_date', $end_date, PDO::PARAM_STR);
        }
    
        // Execute and fetch results
        $stmt->execute();
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        echo json_encode($logs);
    }
    
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
