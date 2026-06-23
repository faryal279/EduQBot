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
$user_id = $_SESSION['user_id'];

if (isset($data['delete_all']) && $data['delete_all'] === true) {
    // Delete all history for this user
    $sql = "DELETE FROM question_history WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
} else if (isset($data['history_id'])) {
    // Delete specific history entry
    $sql = "DELETE FROM question_history WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $data['history_id'], $user_id);
} else {
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Failed to delete history']);
}

$stmt->close();
$conn->close();
?> 