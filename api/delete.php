<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

require_once '../config/database.php';

if (!isset($_POST['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Document ID required']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get document info first
    $query = "SELECT * FROM documents WHERE id = :id AND user_id = :user_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $_POST['id']);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo json_encode(['error' => 'Document not found']);
        exit();
    }
    
    $document = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Delete from database
    $delete_query = "DELETE FROM documents WHERE id = :id AND user_id = :user_id";
    $delete_stmt = $db->prepare($delete_query);
    $delete_stmt->bindParam(':id', $_POST['id']);
    $delete_stmt->bindParam(':user_id', $_SESSION['user_id']);
    
    if ($delete_stmt->execute()) {
        // Delete physical file
        if (file_exists($document['file_path'])) {
            unlink($document['file_path']);
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Document deleted successfully'
        ]);
    } else {
        throw new Exception('Failed to delete document from database');
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>