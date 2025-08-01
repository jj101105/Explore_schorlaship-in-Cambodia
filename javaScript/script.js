
//------------------profile icon-------------------------------
    const profileIconBtn = document.getElementById('profileIconBtn');
    const profileDropdown = document.getElementById('profileDropdown');

    profileIconBtn.addEventListener('click', (e) => {
        e.stopPropagation(); // Prevent dropdown from closing immediately
        profileDropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
        if (!profileDropdown.contains(e.target)) {
            profileDropdown.classList.add('hidden');
        }
    });

    function handleLogout() {
        isLoggedIn = false;
        alert("Logged out.");
        // Optional: Redirect to login page or homepage
    }