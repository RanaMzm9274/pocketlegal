<?php

class Document {
    private $conn;
    private $table_name = "documents";

    public $id;
    public $user_id;
    public $filename;
    public $original_name;
    public $file_path;
    public $file_size;
    public $mime_type;
    public $status;
    public $ai_processed;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create document record
    public function create() {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                      SET user_id=:user_id, filename=:filename, original_name=:original_name, 
                          file_path=:file_path, file_size=:file_size, mime_type=:mime_type, 
                          status=:status, ai_processed=:ai_processed, created_at=NOW()";

            $stmt = $this->conn->prepare($query);

            // Sanitize
            $this->filename = htmlspecialchars(strip_tags($this->filename));
            $this->original_name = htmlspecialchars(strip_tags($this->original_name));
            $this->file_path = htmlspecialchars(strip_tags($this->file_path));
            $this->status = $this->status ?: 'uploaded';
            $this->ai_processed = $this->ai_processed ?: false;

            // Bind values
            $stmt->bindParam(":user_id", $this->user_id);
            $stmt->bindParam(":filename", $this->filename);
            $stmt->bindParam(":original_name", $this->original_name);
            $stmt->bindParam(":file_path", $this->file_path);
            $stmt->bindParam(":file_size", $this->file_size);
            $stmt->bindParam(":mime_type", $this->mime_type);
            $stmt->bindParam(":status", $this->status);
            $stmt->bindParam(":ai_processed", $this->ai_processed, PDO::PARAM_BOOL);

            if($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                error_log("Document created successfully with ID: " . $this->id);
                return true;
            }
            
            error_log("Failed to execute document insert query");
            return false;
        } catch (Exception $e) {
            error_log("Document creation failed: " . $e->getMessage());
            error_log("SQL Error Info: " . print_r($this->conn->errorInfo(), true));
            return false;
        }
    }

    // Get user documents
    public function getUserDocuments($user_id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " 
                      WHERE user_id = :user_id 
                      ORDER BY created_at DESC";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
            
            return [];
        } catch (Exception $e) {
            error_log("Failed to get user documents: " . $e->getMessage());
            return [];
        }
    }
    
    // Get document by ID
    public function getById($id, $user_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE id = :id AND user_id = :user_id 
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":user_id", $user_id);
        
        if ($stmt->execute()) {
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        return false;
    }
    
    // Delete document
    public function delete($id, $user_id) {
        // First get the document to delete the file
        $doc = $this->getById($id, $user_id);
        
        $query = "DELETE FROM " . $this->table_name . " 
                  WHERE id = :id AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":user_id", $user_id);

        if ($stmt->execute()) {
            // Delete physical file if exists
            if ($doc && isset($doc['file_path']) && file_exists($doc['file_path'])) {
                unlink($doc['file_path']);
            }
            return true;
        }
        
        return false;
    }

    // Update AI processing status
    public function updateAIStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " 
                  SET ai_processed = :status 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status, PDO::PARAM_BOOL);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }
    
    // Update document status
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " 
                  SET status = :status 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }
    
    // Get document count for user
    public function getDocumentCount($user_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " 
                  WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user_id);
        
        if ($stmt->execute()) {
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'];
        }
        
        return 0;
    }
}
?>