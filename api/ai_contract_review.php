<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

session_start();

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
    $instructions = $_POST['instructions'] ?? '';
    $conversation_id = $_POST['conversation_id'] ?? null;
    $document_file = $_FILES['document'] ?? null;
    
    if (empty(trim($instructions))) {
        http_response_code(400);
        echo json_encode(['error' => 'Instructions are required']);
        exit();
    }

    if (!$document_file || $document_file['error'] !== UPLOAD_ERR_OK) {
        http_response_code(400);
        echo json_encode(['error' => 'Document file is required']);
        exit();
    }

    $database = new Database();
    $db = $database->getConnection();
    
    if (!$conversation_id) {
        $conv_query = "INSERT INTO ai_conversations (user_id, title, created_at) VALUES (:user_id, :title, NOW())";
        $conv_stmt = $db->prepare($conv_query);
        $title = 'Contract Review: ' . substr($document_file['name'], 0, 30) . (strlen($document_file['name']) > 30 ? '...' : '');
        $conv_stmt->bindParam(':user_id', $_SESSION['user_id']);
        $conv_stmt->bindParam(':title', $title);
        $conv_stmt->execute();
        $conversation_id = $db->lastInsertId();
    }

    $document_name = $document_file['name'];
    $tmp_name = $document_file['tmp_name'];
    $file_size = $document_file['size'];
    $mime_type = $document_file['type'];

    $allowed_types = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain'
    ];

    if (!in_array($mime_type, $allowed_types)) {
        http_response_code(400);
        echo json_encode(['error' => 'File type not allowed. Please upload PDF, DOC, DOCX, or TXT files.']);
        exit();
    }

    if ($file_size > 10 * 1024 * 1024) {
        http_response_code(400);
        echo json_encode(['error' => 'File too large. Maximum size is 10MB.']);
        exit();
    }

    $upload_dir = '../Uploads/contracts/';
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to create upload directory']);
            exit();
        }
    }

    if (!is_writable($upload_dir)) {
        http_response_code(500);
        echo json_encode(['error' => 'Upload directory is not writable']);
        exit();
    }

    $temp_filename = uniqid() . '_' . time() . '_' . $document_name;
    $temp_path = $upload_dir . $temp_filename;

    if (!move_uploaded_file($tmp_name, $temp_path)) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save uploaded file']);
        exit();
    }

    // Step 1: Upload file to doc_upload webhook
    $doc_upload_url = 'https://n8n.srv909751.hstgr.cloud/webhook/doc_upload';
    $form_data = [
        'file' => new CURLFile($temp_path, $mime_type, $document_name)
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $doc_upload_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $form_data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: PocketLegal/1.0',
        'Accept: application/json'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if (file_exists($temp_path)) {
        unlink($temp_path);
    }

    if ($curl_error || $http_code < 200 || $http_code >= 300 || !$response) {
        http_response_code(503);
        echo json_encode([
            'error' => 'Unable to upload contract file to AI service',
            'details' => $curl_error ?: 'Bad HTTP code: ' . $http_code,
            'raw_response' => $response
        ]);
        exit();
    }

    $file_upload_response = json_decode($response, true);
    if (!$file_upload_response || !isset($file_upload_response['file_id'])) {
        http_response_code(500);
        echo json_encode([
            'error' => 'AI doc_upload did not return file_id',
            'details' => $file_upload_response
        ]);
        exit();
    }
    $file_id = $file_upload_response['file_id'];

    // Step 2: Send instructions to query webhook
    $query_url = 'https://n8n.srv909751.hstgr.cloud/webhook/query';
    $webhook_data = [
        'instructions' => trim($instructions),
        'user_id' => $_SESSION['user_id'],
        'conversation_id' => $conversation_id,
        'document_name' => $document_name,
        'file_id' => $file_id,
        'file_size' => $file_size,
        'mime_type' => $mime_type,
        'timestamp' => date('c')
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $query_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhook_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'User-Agent: PocketLegal/1.0',
        'Accept: application/json',
        'Cache-Control: no-cache'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3);

    $query_response = curl_exec($ch);
    $query_http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $query_curl_error = curl_error($ch);
    curl_close($ch);

    if ($query_curl_error || $query_http_code < 200 || $query_http_code >= 300 || !$query_response) {
        http_response_code(503);
        echo json_encode([
            'error' => 'Unable to process instructions with AI service',
            'details' => $query_curl_error ?: 'Bad HTTP code: ' . $query_http_code,
            'raw_response' => $query_response
        ]);
        exit();
    }

    $query_result = json_decode($query_response, true);

    // Accept either 'output' or 'response'
    $ai_response = '';
    if (isset($query_result['output'])) {
        $ai_response = $query_result['output'];
    } elseif (isset($query_result['response'])) {
        $ai_response = $query_result['response'];
    } elseif (isset($query_result['message'])) {
        $ai_response = $query_result['message'];
    } else {
        $ai_response = json_encode($query_result, JSON_PRETTY_PRINT);
    }

    // Save messages to database
    $msg_query = "INSERT INTO ai_messages (conversation_id, user_id, message_type, content, document_name, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
    $msg_stmt = $db->prepare($msg_query);

    $user_message = "Please review this contract: " . $document_name . "\n\nInstructions: " . $instructions;
    $msg_stmt->execute([$conversation_id, $_SESSION['user_id'], 'user', $user_message, $document_name]);
    $msg_stmt->execute([$conversation_id, $_SESSION['user_id'], 'assistant', $ai_response, null]);

    $update_conv = "UPDATE ai_conversations SET updated_at = NOW() WHERE id = ?";
    $update_stmt = $db->prepare($update_conv);
    $update_stmt->execute([$conversation_id]);

    echo json_encode([
        'success' => true,
        'response' => $ai_response,
        'instructions' => $instructions,
        'conversation_id' => $conversation_id,
        'document_name' => $document_name
    ]);

} catch (Exception $e) {
    error_log("AI Contract Review Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error occurred while processing your request',
        'details' => $e->getMessage()
    ]);
}
?>