<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit;
}

// Get the POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['paragraph']) || !isset($data['questions'])) {
    echo json_encode(['error' => 'Missing required data']);
    exit;
}

// Database connection parameters
$servername = "localhost";
$username = "root";  // default XAMPP username
$password = "";      // default XAMPP password
$dbname = "chatbot";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Prepare SQL statement
$sql = "INSERT INTO history (user_id, paragraph, questions) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    // Bind parameters
    $stmt->bind_param("iss", $_SESSION['user_id'], $data['paragraph'], $data['questions']);
    
    // Execute the statement
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'History saved successfully']);
    } else {
        echo json_encode(['error' => 'Failed to save history']);
    }
    
    // Close statement
    $stmt->close();
} else {
    echo json_encode(['error' => 'Error in preparing statement']);
}

// Close connection
$conn->close();
?> 