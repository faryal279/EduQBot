<?php
// Set session cookie parameters BEFORE starting the session
ini_set('session.cookie_path', '/');
session_set_cookie_params([
    'lifetime' => 0, // Session cookie expires when browser closes
    'path' => '/',
    'domain' => '',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax'
]);

// Now start the session
session_start();

// Check for remember me cookies and auto-login if they exist
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_user']) && isset($_COOKIE['remember_email'])) {
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

    // Get user data from database
    $sql = "SELECT * FROM userinfo WHERE id = ? AND email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $_COOKIE['remember_user'], $_COOKIE['remember_email']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['firstname'] = $user['firstname'];
        $_SESSION['lastname'] = $user['lastname'];
    }

    $stmt->close();
    $conn->close();
}

// Session timeout management - only for very long inactivity
$session_timeout = 86400; // 24 hours timeout
if (isset($_SESSION['last_activity'])) {
    $inactive_time = time() - $_SESSION['last_activity'];
    
    // Only timeout after 24 hours of inactivity, regardless of remember me
    if ($inactive_time > $session_timeout) {
        session_destroy();
        // Also clear remember me cookies if they exist
        if (isset($_COOKIE['remember_user'])) {
            setcookie('remember_user', '', time() - 3600, '/');
            setcookie('remember_email', '', time() - 3600, '/');
        }
        header("Location: index.php");
        exit();
    }
}

// Update last activity time stamp
$_SESSION['last_activity'] = time();

/**
 * User Authentication Status
 * Check if user is logged in and get their full name
 */
// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$fullName = $isLoggedIn ? $_SESSION['firstname'] . ' ' . $_SESSION['lastname'] : '';

/**
 * Logout Handler
 * Clears session, cookies, and redirects to home page
 */
// Handle logout
if (isset($_GET['logout'])) {
    // Clear remember me cookies if they exist
    if (isset($_COOKIE['remember_user'])) {
        setcookie('remember_user', '', time() - 3600, '/');
        setcookie('remember_email', '', time() - 3600, '/');
    }
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <!-- Meta tags for proper mobile rendering -->
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Question Generator</title>
  
  <!-- External and internal stylesheets -->
  <link rel="stylesheet" href="styles.css" />
  <!-- Inline styles for user-related UI components -->
  <style>
    .user-info {
      display: flex;
      align-items: center;
      gap: 20px;
    }
    .welcome-text {
      color: #333;
      font-size: 16px;
      font-weight: 500;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 200px;
    }
    .logout-btn {
      background-color: #ff6b6b;
      color: white;
      padding: 8px 20px;
      border-radius: 5px;
      text-decoration: none;
      transition: background-color 0.3s;
      white-space: nowrap;
    }
    .logout-btn:hover {
      background-color: #ff5252;
    }
  </style>
</head>
<body>
  <!-- Header Section: Contains navigation and user authentication status -->
  <header class="header">
    <div class="nav-container">
      <div class="logo">
        <img src="icons/owl.webp" alt="">
      </div>
      <nav class="navbar">
        <a href="index.php" class="active">Home</a>
        <a href="./pages/aboutus.html">About Us</a>
        <a href="./pages/faq.html">FAQ</a>
        <a href="./pages/contact.html">Contact Us</a>
      </nav>
      <div class="user-info">
        <?php if ($isLoggedIn): ?>
          <span class="welcome-text">Welcome, <?php echo htmlspecialchars($fullName); ?>!</span>
          <a href="index.php?logout=1" class="logout-btn">Logout</a>
        <?php else: ?>
          <a href="./pages/login.php" class="demo-btn">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- Hero Section: Main landing page content and call-to-action -->
  <section class="hero">
    <div class="tag">/AI Text Generator Hub</div>
    <h1 class="hero-title">
      Generate Varied Content<br />
      Employing The <span class="h  ighlight">Power</span> Of AI
    </h1>
    <p class="hero-subtext">
      Unlock extraordinary writing capabilities with an AI writing companion. With bra simple click, our AI assists in continued writing, idea expression, and more.
    </p>
    <form class="hero-form">
      <?php if ($isLoggedIn): ?>
        <a href="./pages/chatbot.html" class="demo-btn">Try It Now!</a>
      <?php else: ?>
        <a href="./pages/login.php" class="demo-btn">Try It Now!</a>
      <?php endif; ?>
    </form>
  </section>
</body>
</html> 