/**
 * Header Management Script
 * This script handles the dynamic header behavior based on user authentication status
 * It updates the UI elements and navigation links depending on whether a user is logged in
 */

// Execute when the DOM is fully loaded
window.onload = function() {
    // Get references to key UI elements
    const userInfo = document.querySelector('.user-info');
    const authButtons = document.querySelector('.auth-buttons');
    const tryNowBtn = document.getElementById('tryNowBtn');

    /**
     * Helper function to extract cookie value by name
     * @param {string} name - The name of the cookie to retrieve
     * @returns {string|undefined} The cookie value if found
     */
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }

    // Check for PHP session cookie to determine login status
    const sessionId = getCookie('PHPSESSID');
    
    if (!sessionId) {
        // User is not logged in - Show authentication buttons
        userInfo.style.display = 'none';
        authButtons.classList.add('show');
        // Update Try It Now button if it exists
        if (tryNowBtn) {
            tryNowBtn.href = '../pages/login.php';
        }
    } else {
        // User might be logged in - Verify with server
        // Determine correct path for API call based on current page location
        const rootPath = window.location.pathname.includes('/pages/') ? '..' : '.';
        
        // Fetch current user's information from the server
        fetch(`${rootPath}/pages/get_user_info.php`)
            .then(response => response.json())
            .then(data => {
                if (data.loggedin) {
                    // User is logged in - Show personalized welcome message
                    userInfo.style.display = 'flex';
                    authButtons.classList.remove('show');
                    document.getElementById('userFirstName').textContent = 'Welcome, ' + data.fullname;
                    // Update Try It Now button if it exists
                    if (tryNowBtn) {
                        tryNowBtn.href = '../pages/chatbot.html';
                    }
                } else {
                    // Session expired or invalid - Show login options
                    userInfo.style.display = 'none';
                    authButtons.classList.add('show');
                    if (tryNowBtn) {
                        tryNowBtn.href = '../pages/login.php';
                    }
                }
            })
            .catch(error => {
                // Handle API errors gracefully
                console.error('Error:', error);
                // Fallback to non-authenticated state
                userInfo.style.display = 'none';
                authButtons.classList.add('show');
                if (tryNowBtn) {
                    tryNowBtn.href = '../pages/login.php';
                }
            });
    }
}; 