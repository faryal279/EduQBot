<?php
// Database connection parameters
$servername = "localhost";
$username = "root";  // default XAMPP username
$password = "";      // default XAMPP password
$dbname = "chatbot";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $firstname = $conn->real_escape_string($_POST['firstName']);
    $lastname = $conn->real_escape_string($_POST['lastName']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']); // Remove hashing

    // Prepare SQL statement
    $sql = "INSERT INTO userinfo (firstname, lastname, email, password) 
            VALUES (?, ?, ?, ?)";
    
    // Create a prepared statement
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        // Bind parameters
        $stmt->bind_param("ssss", $firstname, $lastname, $email, $password);
        
        // Execute the statement
        if ($stmt->execute()) {
            // Redirect to login page with success message
            header("Location: login.html?signup=success");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
        
        // Close statement
        $stmt->close();
    } else {
        echo "Error in preparing statement: " . $conn->error;
    }
    
    // Close connection
    $conn->close();
}
?> 