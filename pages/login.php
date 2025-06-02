<?php
session_start();
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    // Get form data
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    // First check if email exists
    $sql = "SELECT * FROM userinfo WHERE email = ?";
    
    // Create a prepared statement
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        // Bind parameters
        $stmt->bind_param("s", $email);
        
        // Execute the statement
        $stmt->execute();
        
        // Get the result
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            // Email exists, now check password
            $user = $result->fetch_assoc();
            if ($user['password'] === $password) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['firstname'] = $user['firstname'];
                $_SESSION['lastname'] = $user['lastname'];
                
                // Redirect to index.php
                header("Location: ../index.php");
                exit();
            } else {
                // Wrong password
                $error_message = "Invalid password! Please try again.";
            }
        } else {
            // Email not found
            $error_message = "Email not found! Please check your email or sign up.";
        }
        
        // Close statement
        $stmt->close();
    } else {
        $error_message = "Error in preparing statement: " . $conn->error;
    }
    
    // Close connection
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login | Question Generator</title>
    <link rel="stylesheet" href="signup.css" />
    <style>
        .error-message {
            color: #ff0000;
            background-color: rgba(255, 0, 0, 0.1);
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <section class="parallax-section">
        <div class="parallax-bg"></div>
        <div class="form-wrapper">
            <form class="form-box" method="POST">
                <h2><img src="../icons/owl.webp" alt="Logo" /></h2>
                <h2>Login</h2>

                <?php if ($error_message): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
                <?php endif; ?>

                <div class="input-box">
                    <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" />
                    <label for="email">Email Address</label>
                </div>

                <div class="input-box">
                    <input type="password" id="password" name="password" required />
                    <label for="password">Password</label>
                </div>

                <button type="submit">Login</button>

                <a href="signup.html" class="login-link">Don't have account? <span style="color: burlywood;">Signup</span></a>
            </form>
        </div>
    </section>

    <script>
        const section = document.querySelector('.parallax-section');
        const bg = section.querySelector('.parallax-bg');

        section.addEventListener('mousemove', (e) => {
            const rect = section.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width - 0.5) * -30;
            const y = ((e.clientY - rect.top) / rect.height - 0.5) * -30;
            bg.style.transform = `translate(${x}px, ${y}px)`;
        });
    </script>
</body>
</html> 