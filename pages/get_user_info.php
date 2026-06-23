<?php
// Set session cookie parameters BEFORE starting the session
ini_set('session.cookie_path', '/');
session_set_cookie_params([
    'lifetime' => 0, // Session cookie expires when browser closes
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true
]);

// Now start the session
session_start();

// Set CORS headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$response = array(
    'loggedin' => false,
    'email' => '',
    'fullname' => ''
);

// Check if user is logged in via session
if (isset($_SESSION['user_id'])) {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "chatbot";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn->connect_error) {
        // Get user data from database
        $stmt = $conn->prepare("SELECT firstname, lastname, email FROM userinfo WHERE id = ?");
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $response['loggedin'] = true;
            $response['email'] = $user['email'];
            $response['fullname'] = $user['firstname'] . ' ' . $user['lastname'];
        }

        $stmt->close();
        $conn->close();
    }
} 
// If not logged in via session, check remember me cookies
else if (isset($_COOKIE['remember_user']) && isset($_COOKIE['remember_email'])) {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "chatbot";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn->connect_error) {
        // Get user data from database using remember me cookie
        $stmt = $conn->prepare("SELECT * FROM userinfo WHERE id = ? AND email = ?");
        $stmt->bind_param("is", $_COOKIE['remember_user'], $_COOKIE['remember_email']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // Restore session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            // Set response
            $response['loggedin'] = true;
            $response['email'] = $user['email'];
            $response['fullname'] = $user['firstname'] . ' ' . $user['lastname'];
        }

        $stmt->close();
        $conn->close();
    }
}

http_response_code(200);
echo json_encode($response);
?> 