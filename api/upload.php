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
        if (!mkdir($upload_dir, 0755, true)) {
            throw new Exception('Failed to create upload directory');
        }
    }
    
    // Ensure directory is writable
    if (!is_writable($upload_dir)) {
        throw new Exception('Upload directory is not writable');
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

    // Log successful uploads
    if (!empty($uploaded_files)) {
        error_log("Successfully uploaded " . count($uploaded_files) . " files for user " . $_SESSION['user_id']);
    }

    echo json_encode([
        'success' => true,
        'uploaded_files' => $uploaded_files,
        'errors' => $errors,
        'message' => count($uploaded_files) . ' file(s) uploaded successfully',
        'total_uploaded' => count($uploaded_files),
        'total_errors' => count($errors)
    ]);

} catch (Exception $e) {
    error_log("Upload error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}

function processFile($original_name, $tmp_name, $file_size, $mime_type, $upload_dir, $document, $user_id) {
    // Sanitize filename
    $original_name = basename($original_name);
    $original_name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $original_name);
    
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
    
    // Additional MIME type validation
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $detected_mime = finfo_file($finfo, $tmp_name);
    finfo_close($finfo);
    
    if (!in_array($mime_type, $allowed_types)) {
        return [
            'success' => false,
            'error' => "File type not allowed for {$original_name}. Allowed types: PDF, DOC, DOCX, TXT, JPG, PNG, GIF, WEBP"
        ];
    }
    
    if (!in_array($detected_mime, $allowed_types)) {
        return [
            'success' => false,
            'error' => "Detected file type not allowed for {$original_name}"
        ];
    }
    
    // Validate file size (max 10MB)
    if ($file_size > 10 * 1024 * 1024) {
        return [
            'success' => false,
            'error' => "File too large: {$original_name}. Maximum size is 10MB."
        ];
    }
    
    // Validate file size is not zero
    if ($file_size <= 0) {
        return [
            'success' => false,
            'error' => "Invalid file size for {$original_name}"
        ];
    }
    
    // Generate unique filename
    $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
    $safe_name = pathinfo($original_name, PATHINFO_FILENAME);
    $safe_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $safe_name);
    $filename = uniqid() . '_' . time() . '_' . $safe_name . '.' . $file_extension;
    $file_path = $upload_dir . $filename;
    
    if (move_uploaded_file($tmp_name, $file_path)) {
        // Set proper file permissions
        chmod($file_path, 0644);
        
        // Save to database
        $document->user_id = $user_id;
        $document->filename = $filename;
        $document->original_name = $original_name;
        $document->file_path = $file_path;
        $document->file_size = $file_size;
        $document->mime_type = $detected_mime; // Use detected MIME type
        $document->status = 'uploaded';
        $document->ai_processed = false;
        
        if ($document->create()) {
            error_log("Document saved to database: " . $original_name . " (ID: " . $document->id . ")");
            return [
                'success' => true,
                'data' => [
                    'id' => $document->id,
                    'filename' => $filename,
                    'original_name' => $original_name,
                    'size' => $file_size,
                    'mime_type' => $detected_mime,
                    'status' => 'uploaded',
                    'created_at' => date('Y-m-d H:i:s')
                ]
            ];
        } else {
            // Clean up file if database save failed
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            error_log("Failed to save document to database: " . $original_name);
            return [
                'success' => false,
                'error' => "Failed to save {$original_name} to database"
            ];
        }
    } else {
        error_log("Failed to move uploaded file: " . $original_name . " to " . $file_path);
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