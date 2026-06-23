/*
 * History Table Creation Script
 * This table stores the history of questions generated for each user
 * 
 * Fields:
 * - id: Unique identifier for each history entry
 * - user_id: Foreign key linking to userinfo table
 * - paragraph: The original text input by the user
 * - questions: The generated questions stored as text
 * - created_at: Timestamp of when the entry was created
 */

CREATE TABLE IF NOT EXISTS history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    paragraph TEXT NOT NULL,
    questions TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES userinfo(id)
); 