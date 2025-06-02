// Check if user is logged in and display name
window.onload = function() {
    const userInfo = document.querySelector('.user-info');
    const authButtons = document.querySelector('.auth-buttons');
    const tryNowBtn = document.getElementById('tryNowBtn');

    // Create a function to get cookie value by name
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    // Get the PHP session ID
    const sessionId = getCookie('PHPSESSID');
    
    if (!sessionId) {
        // Show login/signup buttons for non-logged in users
        userInfo.style.display = 'none';
        authButtons.classList.add('show');
        // Update Try It Now button if it exists
        if (tryNowBtn) {
            tryNowBtn.href = '../pages/login.php';
        }
    } else {
        // Fetch user data from get_user_info.php
        const rootPath = window.location.pathname.includes('/pages/') ? '..' : '.';
        fetch(`${rootPath}/pages/get_user_info.php`)
            .then(response => response.json())
            .then(data => {
                if (data.loggedin) {
                    userInfo.style.display = 'flex';
                    authButtons.classList.remove('show');
                    document.getElementById('userFirstName').textContent = 'Welcome, ' + data.fullname;
                    // Update Try It Now button if it exists
                    if (tryNowBtn) {
                        tryNowBtn.href = '../pages/chatbot.html';
                    }
                } else {
                    userInfo.style.display = 'none';
                    authButtons.classList.add('show');
                    if (tryNowBtn) {
                        tryNowBtn.href = '../pages/login.php';
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                userInfo.style.display = 'none';
                authButtons.classList.add('show');
                if (tryNowBtn) {
                    tryNowBtn.href = '../pages/login.php';
                }
            });
    }
}; 