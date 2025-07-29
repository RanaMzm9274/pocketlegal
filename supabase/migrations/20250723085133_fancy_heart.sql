/*
  # AI Conversations and Messages Tables

  1. New Tables
    - `ai_conversations`
      - `id` (int, primary key, auto increment)
      - `user_id` (int, foreign key to users)
      - `title` (varchar 255)
      - `created_at` (timestamp)
      - `updated_at` (timestamp)
    - `ai_messages`
      - `id` (int, primary key, auto increment)
      - `conversation_id` (int, foreign key to ai_conversations)
      - `user_id` (int, foreign key to users)
      - `message_type` (enum: 'user', 'assistant')
      - `content` (text)
      - `document_name` (varchar 255, nullable)
      - `created_at` (timestamp)

  2. Security
    - Foreign key constraints for data integrity
    - Indexes for better query performance

  3. Features
    - Conversation history tracking
    - Message threading
    - Document attachment tracking
*/

-- AI Conversations table
CREATE TABLE IF NOT EXISTS ai_conversations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_updated (user_id, updated_at)
);

-- AI Messages table
CREATE TABLE IF NOT EXISTS ai_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    conversation_id INT NOT NULL,
    user_id INT NOT NULL,
    message_type ENUM('user', 'assistant') NOT NULL,
    content TEXT NOT NULL,
    document_name VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (conversation_id) REFERENCES ai_conversations(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_conversation_created (conversation_id, created_at)
);