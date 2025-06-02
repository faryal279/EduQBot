<?php
session_start();

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
$fullName = $isLoggedIn ? $_SESSION['firstname'] . ' ' . $_SESSION['lastname'] : '';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Question Generator</title>
  <link rel="stylesheet" href="styles.css" />
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
  <!-- Header -->
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

  <!-- Hero Section -->
  <section class="hero">
    <div class="tag">/AI Text Generator Hub</div>
    <h1 class="hero-title">
      Generate Varied Content<br />
      Employing The <span class="highlight">Power</span> Of AI
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