<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chatbot";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['input_text']) || !isset($data['questions'])) {
    echo json_encode(['error' => 'Missing required data']);
    exit;
}

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Insert into question_history table
$sql = "INSERT INTO question_history (user_id, input_text, generated_questions) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("iss", $user_id, $data['input_text'], $data['questions']);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to save to database']);
    }
    
    $stmt->close();
} else {
    echo json_encode(['error' => 'Failed to prepare statement']);
}

$conn->close();
?> 