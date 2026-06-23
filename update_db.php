<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chatbot";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL to update the table structure
$sql = "
DROP TABLE IF EXISTS question_history;

CREATE TABLE question_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    input_text TEXT NOT NULL,
    generated_questions TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES userinfo(id)
)";

if ($conn->multi_query($sql)) {
    echo "Database structure updated successfully";
} else {
    echo "Error updating database structure: " . $conn->error;
}

$conn->close();
?> 