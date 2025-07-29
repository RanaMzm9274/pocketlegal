<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once '../config/database.php';
require_once '../classes/Document.php';

if (!isset($_GET['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Document ID required']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM documents WHERE id = :id AND user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_GET['id']);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Document not found']);
        exit();
    }
    
    $document = $stmt->fetch(PDO::FETCH_ASSOC);
    $file_path = $document['file_path'];
    
    if (!file_exists($file_path)) {
        http_response_code(404);
        echo json_encode(['error' => 'File not found on server']);
        exit();
    }
    
    // Set headers for file download
    header('Content-Type: ' . $document['mime_type']);
    header('Content-Disposition: attachment; filename="' . $document['original_name'] . '"');
    header('Content-Length: ' . filesize($file_path));
    
    // Output file
    readfile($file_path);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>