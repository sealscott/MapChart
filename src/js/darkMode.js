document.addEventListener('DOMContentLoaded', function() {
    const darkModeButton = document.getElementById('darkModeButton');
    
    // Check if dark mode preference exists (from localStorage or cookie)
    const darkModeLocalStorage = localStorage.getItem('darkMode');
    const darkModeCookie = getCookie('darkMode');
    
    // Apply dark mode if it was previously enabled (prioritize localStorage)
    if (darkModeLocalStorage === 'enabled' || darkModeCookie === 'enabled') {
        document.body.classList.add('dark-mode');
    }
    
    // Toggle dark mode on button click
    darkModeButton.addEventListener('click', function() {
        console.log('Dark mode button clicked'); // Debug output
        
        if (document.body.classList.contains('dark-mode')) {
            // Switch to light mode
            document.body.classList.remove('dark-mode');
            localStorage.setItem('darkMode', 'disabled');
            setDarkModeCookie(false);
            console.log('Switching to light mode'); // Debug output
        } else {
            // Switch to dark mode
            document.body.classList.add('dark-mode');
            localStorage.setItem('darkMode', 'enabled');
            setDarkModeCookie(true);
            console.log('Switching to dark mode'); // Debug output
        }
    });
});

// Helper function to get cookie value
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

// Use cookies for dark mode preference (for PHP integration)
function setDarkModeCookie(enabled) {
    const value = enabled ? 'enabled' : 'disabled';
    const date = new Date();
    date.setTime(date.getTime() + (365 * 24 * 60 * 60 * 1000)); // Cookie expires in 1 year
    document.cookie = `darkMode=${value}; expires=${date.toUTCString()}; path=/`;
}