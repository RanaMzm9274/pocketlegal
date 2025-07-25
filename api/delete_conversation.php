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

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $conversation_id = $input['conversation_id'] ?? null;
    
    if (!$conversation_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Conversation ID is required']);
        exit();
    }

    $database = new Database();
    $db = $database->getConnection();
    
    // Delete messages first (foreign key constraint)
    $delete_messages = "DELETE FROM ai_messages WHERE conversation_id = ? AND user_id = ?";
    $stmt = $db->prepare($delete_messages);
    $stmt->execute([$conversation_id, $_SESSION['user_id']]);
    
    // Delete conversation
    $delete_conv = "DELETE FROM ai_conversations WHERE id = ? AND user_id = ?";
    $stmt = $db->prepare($delete_conv);
    $stmt->execute([$conversation_id, $_SESSION['user_id']]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Conversation deleted successfully'
        ]);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Conversation not found']);
    }

} catch (Exception $e) {
    error_log("Delete Conversation Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>