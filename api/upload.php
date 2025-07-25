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

require_once '../config/database.php';
require_once '../classes/Document.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit();
}

try {
    $database = new Database();
    $db = $database->getConnection();
    $document = new Document($db);

    $upload_dir = '../uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Create contracts subdirectory
    $contracts_dir = '../uploads/contracts/';
    if (!file_exists($contracts_dir)) {
        mkdir($contracts_dir, 0777, true);
    }

    $uploaded_files = [];
    $errors = [];

    if (isset($_FILES['documents'])) {
        $files = $_FILES['documents'];
        
        // Handle multiple files
        if (is_array($files['name'])) {
            $file_count = count($files['name']);
            
            for ($i = 0; $i < $file_count; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $result = processFile(
                        $files['name'][$i],
                        $files['tmp_name'][$i],
                        $files['size'][$i],
                        $files['type'][$i],
                        $upload_dir,
                        $document,
                        $_SESSION['user_id']
                    );
                    
                    if ($result['success']) {
                        $uploaded_files[] = $result['data'];
                    } else {
                        $errors[] = $result['error'];
                    }
                } else {
                    $errors[] = "Upload error for {$files['name'][$i]}: " . getUploadErrorMessage($files['error'][$i]);
                }
            }
        } else {
            // Single file
            if ($files['error'] === UPLOAD_ERR_OK) {
                $result = processFile(
                    $files['name'],
                    $files['tmp_name'],
                    $files['size'],
                    $files['type'],
                    $upload_dir,
                    $document,
                    $_SESSION['user_id']
                );
                
                if ($result['success']) {
                    $uploaded_files[] = $result['data'];
                } else {
                    $errors[] = $result['error'];
                }
            } else {
                $errors[] = "Upload error: " . getUploadErrorMessage($files['error']);
            }
        }
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'No files uploaded']);
        exit();
    }

    echo json_encode([
        'success' => true,
        'uploaded_files' => $uploaded_files,
        'errors' => $errors,
        'message' => count($uploaded_files) . ' file(s) uploaded successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}

function processFile($original_name, $tmp_name, $file_size, $mime_type, $upload_dir, $document, $user_id) {
    // Validate file type
    $allowed_types = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain',
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp'
    ];
    
    if (!in_array($mime_type, $allowed_types)) {
        return [
            'success' => false,
            'error' => "File type not allowed for {$original_name}. Allowed types: PDF, DOC, DOCX, TXT, JPG, PNG, GIF, WEBP"
        ];
    }
    
    // Validate file size (max 10MB)
    if ($file_size > 10 * 1024 * 1024) {
        return [
            'success' => false,
            'error' => "File too large: {$original_name}. Maximum size is 10MB."
        ];
    }
    
    // Generate unique filename
    $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $file_extension;
    $file_path = $upload_dir . $filename;
    
    if (move_uploaded_file($tmp_name, $file_path)) {
        // Save to database
        $document->user_id = $user_id;
        $document->filename = $filename;
        $document->original_name = $original_name;
        $document->file_path = $file_path;
        $document->file_size = $file_size;
        $document->mime_type = $mime_type;
        $document->status = 'uploaded';
        $document->ai_processed = false;
        
        if ($document->create()) {
            return [
                'success' => true,
                'data' => [
                    'id' => $document->id,
                    'filename' => $filename,
                    'original_name' => $original_name,
                    'size' => $file_size,
                    'mime_type' => $mime_type,
                    'status' => 'uploaded',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
        } else {
            // Clean up file if database save failed
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            return [
                'success' => false,
                'error' => "Failed to save {$original_name} to database"
            ];
        }
    } else {
        return [
            'success' => false,
            'error' => "Failed to upload {$original_name}"
        ];
    }
}

function getUploadErrorMessage($error_code) {
    switch ($error_code) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            return 'File too large';
        case UPLOAD_ERR_PARTIAL:
            return 'File partially uploaded';
        case UPLOAD_ERR_NO_FILE:
            return 'No file uploaded';
        case UPLOAD_ERR_NO_TMP_DIR:
            return 'No temporary directory';
        case UPLOAD_ERR_CANT_WRITE:
            return 'Cannot write to disk';
        case UPLOAD_ERR_EXTENSION:
            return 'Upload stopped by extension';
        default:
            return 'Unknown upload error';
    }
}
?>