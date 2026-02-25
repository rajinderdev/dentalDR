// Authentication Check for localStorage
document.addEventListener('DOMContentLoaded', function() {
    checkAuthentication();
});

function checkAuthentication() {
    // Check if user data exists in localStorage
    const userData = localStorage.getItem('userData');
    
    if (!userData) {
        // No user data found, redirect to external login
        window.location.href = 'https://dental.stgserver.co.in/auth/login';
        return;
    }
    
    try {
        const user = JSON.parse(userData);
        
        // Validate user data structure
        if (!user || !user.UserID) {
            console.error('Invalid user data in localStorage');
            clearUserDataAndRedirect();
            return;
        }
        
        // Optional: Make an AJAX call to validate the user session
        validateUserSession(user.UserID);
        
    } catch (error) {
        console.error('Error parsing user data:', error);
        clearUserDataAndRedirect();
    }
}

function validateUserSession(userID) {
    // Make AJAX call to validate user session with the backend
    fetch('/api/validate-session', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify({ userID: userID })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Session validation failed');
        }
        return response.json();
    })
    .then(data => {
        if (!data.valid) {
            clearUserDataAndRedirect();
        }
    })
    .catch(error => {
        console.error('Session validation error:', error);
        // On validation error, redirect to login
        clearUserDataAndRedirect();
    });
}

function clearUserDataAndRedirect() {
    localStorage.removeItem('userData');
    window.location.href = 'https://dental.stgserver.co.in/auth/login';
}

// Function to store user data after successful login
function storeUserData(userData) {
    localStorage.setItem('userData', JSON.stringify(userData));
}

// Function to get current user data
function getUserData() {
    const userData = localStorage.getItem('userData');
    return userData ? JSON.parse(userData) : null;
}

// Function to logout and clear data
function logout() {
    localStorage.removeItem('userData');
    window.location.href = 'https://dental.stgserver.co.in/auth/login';
}
