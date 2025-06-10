let container = document.getElementById('container');

// Toggle between sign-in and sign-up forms
const toggle = () => {
    container.classList.toggle('sign-in');
    container.classList.toggle('sign-up');
    // Clear any existing error messages
    document.getElementById('signinError').textContent = '';
    document.getElementById('signupError').textContent = '';
}

// Initialize with sign-in form
setTimeout(() => {
    container.classList.add('sign-in');
}, 200);

// Store user data in session
const storeUserSession = (userData) => {
    sessionStorage.setItem('currentUser', JSON.stringify(userData));
};

// Sign Up Form Submission
document.getElementById('signupForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const username = document.getElementById('signupUsername').value.trim();
    const email = document.getElementById('signupEmail').value.trim();
    const password = document.getElementById('signupPassword').value;
    const confirmPassword = document.getElementById('signupConfirmPassword').value;
    const errorElement = document.getElementById('signupError');
    
    // Validation
    if (!username || !email || !password || !confirmPassword) {
        errorElement.textContent = 'All fields are required';
        return;
    }
    
    if (password !== confirmPassword) {
        errorElement.textContent = 'Passwords do not match';
        return;
    }
    
    if (password.length < 6) {
        errorElement.textContent = 'Password must be at least 6 characters';
        return;
    }
    
    try {
        const response = await fetch('http://localhost/ITA/api/signup.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                username,
                email,
                password
            })
        });
        
        const data = await response.json();
        
        
        if (response.ok) {
            // Store user data in session
            if (data.user) {
                storeUserSession(data.user);
            }
            // Redirect to home page after successful signup
            window.location.href = 'home.html';
        } else {
            errorElement.textContent = data.message || 'Signup failed';
        }
    } catch (error) {
        errorElement.textContent = 'An error occurred. Please try again.';
        console.error('Signup error:', error);
    }
});

// Sign In Form Submission
document.getElementById('signinForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const username = document.getElementById('signinUsername').value.trim();
    const password = document.getElementById('signinPassword').value;
    const errorElement = document.getElementById('signinError');
    
    // Validation
    if (!username || !password) {
        errorElement.textContent = 'All fields are required';
        return;
    }
    
    try {
        const response = await fetch('http://localhost/ITA/api/signin.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                username,
                password
            })
        });
        
        const data = await response.json();
        console.log(data);
        
        if (response.ok && data.authenticated) {
            // Store user data in session
            storeUserSession(data.user);
            
            // Redirect based on user role
            if (data.user.is_admin) {
                window.location.href = 'admin-dashboard.html';
            } else {
                window.location.href = 'home.html';
            }
        } else {
            errorElement.textContent = data.message || 'Invalid credentials';
        }
    } catch (error) {
        errorElement.textContent = 'An error occurred. Please try again.';
        console.error('Signin error:', error);
    }
});

// Add this to clear session when page loads (optional)
window.addEventListener('load', () => {
    // sessionStorage.removeItem('currentUser'); // Uncomment if you want to clear on load
});