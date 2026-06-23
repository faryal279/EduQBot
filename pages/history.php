<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

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

// Get user ID from session
$user_id = $_SESSION['user_id'];

// Fetch history entries for the current user only using prepared statement
$sql = "SELECT * FROM question_history WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question History | Chatbot</title>
    <link rel="stylesheet" href="chatbot.css">
    <style>
        /* Header styles */
        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .username {
            color: #a855f7;
            font-weight: 600;
            font-size: 18px;
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
            background-color: #cc3333;
        }
        .auth-buttons {
            display: none;
        }
        .auth-buttons.show {
            display: flex;
            gap: 20px;
        }
        .login-btn {
            background-color: #a855f7;
        }
        .login-btn:hover {
            background-color: #9333ea;
        }

        /* History section styles */
        .history-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        .history-item {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            backdrop-filter: blur(10px);
            position: relative;
        }

        .history-item:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .history-meta {
            color: #a855f7;
            font-size: 0.9em;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .input-text {
            color: #fff;
            font-size: 1.1em;
            margin-bottom: 15px;
            padding: 10px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 5px;
        }

        .questions-container {
            color: #fff;
        }

        .no-history {
            text-align: center;
            color: #fff;
            padding: 40px;
            font-style: italic;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            margin-top: 20px;
        }

        .back-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #a855f7;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .back-btn:hover {
            background: #9333ea;
        }

        .delete-btn {
            background: #ff4444;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
        }

        .delete-btn:hover {
            background: #cc3333;
        }

        .delete-all-btn {
            background: #ff4444;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 20px;
        }

        .delete-all-btn:hover {
            background: #cc3333;
        }

        .header-actions {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            justify-content: space-between;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loading-spinner {
            color: #a855f7;
            font-size: 1.2em;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .download-btn {
            background: #1e052e;
            color: #d5acfe;
            border: 1px solid #a855f7;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            transition: all 0.3s ease;
        }

        .download-btn:hover {
            background: #a855f7;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-spinner">Deleting...</div>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="nav-container">
            <div class="logo">
                <img src="../icons/owl.webp" alt="Chatbot Logo">
            </div>
            <nav class="navbar">
                <a href="../index.php">Home</a>
                <a href="../pages/aboutus.html">About Us</a>
                <a href="../pages/faq.html">FAQ</a>
                <a href="../pages/contact.html">Contact Us</a>
            </nav>
            <div class="user-info" style="display: none;">
                <span id="userFirstName" class="username">Welcome, </span>
                <a href="logout.php" class="demo-btn logout-btn">Logout</a>
            </div>
            <div class="auth-buttons">
                <a href="login.php" class="demo-btn login-btn">Login</a>
                <a href="signup.html" class="demo-btn">Sign Up</a>
            </div>
        </div>
    </header>

    <!-- Parallax Background Section -->
    <section class="parallax-section">
        <div class="parallax-bg"></div>
        <div class="parallax-overlay-text">
            <h1>Question History</h1>
        </div>
    </section>

    <!-- History Section -->
    <section class="history-container">
        <div class="header-actions">
            <a href="chatbot.html" class="back-btn">← Back to Generator</a>
            <?php if ($result && $result->num_rows > 0): ?>
                <button onclick="deleteAllHistory()" class="delete-all-btn">Delete All History</button>
            <?php endif; ?>
        </div>
        <h1>Your Question History</h1>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="history-item" id="history-<?php echo $row['id']; ?>">
                    <div class="history-meta">
                        <span>Generated on: <?php echo date('F j, Y, g:i a', strtotime($row['created_at'])); ?></span>
                        <div class="action-buttons">
                            <button class="download-btn" onclick="downloadHistory(<?php echo $row['id']; ?>, <?php echo htmlspecialchars(json_encode($row['input_text'])); ?>, <?php echo htmlspecialchars(json_encode($row['generated_questions'])); ?>)">Download</button>
                            <button class="delete-btn" onclick="deleteHistory(<?php echo $row['id']; ?>)">Delete</button>
                        </div>
                    </div>
                    <div class="input-text">
                        <strong>Input Text:</strong><br>
                        <?php echo htmlspecialchars($row['input_text']); ?>
                    </div>
                    <div class="questions-container">
                        <strong>Generated Questions:</strong><br>
                        <?php 
                        $questions = json_decode($row['generated_questions'], true);
                        if ($questions && isset($questions['Questions'])) {
                            echo '<ol>';
                            foreach ($questions['Questions'] as $question) {
                                if (is_array($question) && isset($question['question'])) {
                                    echo '<li>' . htmlspecialchars($question['question']) . '</li>';
                                } else if (is_string($question)) {
                                    echo '<li>' . htmlspecialchars($question) . '</li>';
                                }
                            }
                            echo '</ol>';
                        } else {
                            echo htmlspecialchars($row['generated_questions']);
                        }
                        ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-history">
                <p>You haven't generated any questions yet. Start generating some questions!</p>
            </div>
        <?php endif; ?>
    </section>

    <script>
        // Parallax Effect
        const section = document.querySelector('.parallax-section');
        const bg = section.querySelector('.parallax-bg');

        section.addEventListener('mousemove', (e) => {
            const rect = section.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width - 0.5) * -30;
            const y = ((e.clientY - rect.top) / rect.height - 0.5) * -30;
            bg.style.transform = `translate(${x}px, ${y}px)`;
        });

        // Loading overlay functions
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }

        async function deleteHistory(id) {
            if (!confirm('Are you sure you want to delete this history item?')) {
                return;
            }

            showLoading();
            try {
                const response = await fetch('delete_history.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        history_id: id
                    })
                });

                const data = await response.json();
                if (data.success) {
                    const element = document.getElementById(`history-${id}`);
                    element.remove();

                    // Check if there are any remaining history items
                    const remainingItems = document.querySelectorAll('.history-item');
                    if (remainingItems.length === 0) {
                        location.reload(); // Reload to show the "no history" message
                    }
                } else {
                    alert('Failed to delete history item');
                }
            } catch (error) {
                alert('Error deleting history item');
            }
            hideLoading();
        }

        async function deleteAllHistory() {
            if (!confirm('Are you sure you want to delete all history items? This cannot be undone.')) {
                return;
            }

            showLoading();
            try {
                const response = await fetch('delete_history.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        delete_all: true
                    })
                });

                const data = await response.json();
                if (data.success) {
                    location.reload(); // Reload the page to show the "no history" message
                } else {
                    alert('Failed to delete history');
                }
            } catch (error) {
                alert('Error deleting history');
            }
            hideLoading();
        }

        // Header Authentication
        window.onload = function() {
            const userInfo = document.querySelector('.user-info');
            const authButtons = document.querySelector('.auth-buttons');

            // Get the PHP session ID
            function getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
            }

            const sessionId = getCookie('PHPSESSID');
            
            if (!sessionId) {
                userInfo.style.display = 'none';
                authButtons.classList.add('show');
            } else {
                fetch('../pages/get_user_info.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.loggedin) {
                            userInfo.style.display = 'flex';
                            authButtons.classList.remove('show');
                            document.getElementById('userFirstName').textContent = 'Welcome, ' + data.fullname;
                        } else {
                            userInfo.style.display = 'none';
                            authButtons.classList.add('show');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        userInfo.style.display = 'none';
                        authButtons.classList.add('show');
                    });
            }
        };

        // Add this new function for downloading history
        function downloadHistory(id, inputText, questions) {
            try {
                // Parse the questions JSON if it's a string
                const parsedQuestions = typeof questions === 'string' ? JSON.parse(questions) : questions;
                
                // Create the content for the text file
                let content = "Question History\n";
                content += "=================\n\n";
                content += "Input Text:\n";
                content += "----------\n";
                content += inputText + "\n\n";
                content += "Generated Questions:\n";
                content += "------------------\n";

                if (parsedQuestions && parsedQuestions.Questions) {
                    parsedQuestions.Questions.forEach((question, index) => {
                        if (typeof question === 'object' && question.question) {
                            content += `${index + 1}. ${question.question}\n`;
                        } else if (typeof question === 'string') {
                            content += `${index + 1}. ${question}\n`;
                        }
                    });
                } else {
                    content += questions + "\n";
                }

                // Create a blob with the content
                const blob = new Blob([content], { type: 'text/plain' });
                const url = window.URL.createObjectURL(blob);
                
                // Create a temporary link and trigger the download
                const a = document.createElement('a');
                a.href = url;
                a.download = `question_history_${id}.txt`;
                document.body.appendChild(a);
                a.click();
                
                // Cleanup
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            } catch (error) {
                console.error('Error downloading history:', error);
                alert('Error downloading history. Please try again.');
            }
        }
    </script>

    <script src="../js/header.js"></script>
</body>
</html>
<?php 
$stmt->close();
$conn->close(); 
?> 