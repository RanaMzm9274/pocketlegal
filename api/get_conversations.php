<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $conversation_id = $_GET['conversation_id'] ?? null;
    
    if ($conversation_id) {
        // Get specific conversation messages
        $query = "SELECT * FROM ai_messages WHERE conversation_id = ? AND user_id = ? ORDER BY created_at ASC";
        $stmt = $db->prepare($query);
        $stmt->execute([$conversation_id, $_SESSION['user_id']]);
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'messages' => $messages
        ]);
    } else {
        // Get all conversations for user
        $query = "SELECT c.*, 
                         (SELECT COUNT(*) FROM ai_messages WHERE conversation_id = c.id) as message_count,
                         (SELECT content FROM ai_messages WHERE conversation_id = c.id AND message_type = 'user' ORDER BY created_at DESC LIMIT 1) as last_message
                  FROM ai_conversations c 
                  WHERE c.user_id = ? 
                  ORDER BY c.updated_at DESC";
        $stmt = $db->prepare($query);
        $stmt->execute([$_SESSION['user_id']]);
        $conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode([
            'success' => true,
            'conversations' => $conversations
        ]);
    }

} catch (Exception $e) {
    error_log("Get Conversations Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>