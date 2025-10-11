document.addEventListener('DOMContentLoaded', () => {
    // Menu handling
    const menuItems = document.querySelectorAll('.profile-menu');
    const sections = {
        'main-content': document.getElementById('main-content'),
        'my-threads': document.getElementById('my-threads'),
        'notifications': document.getElementById('notifications'),
        'my-comments': document.getElementById('my-comments'),
        'edit-profile': document.getElementById('edit-profile')
    };

    // Show main content by default
    sections['main-content'].classList.remove('d-none');
    document.querySelector('.profile-menu[data-target="main-content"]').classList.add('active');

    // Menu click event
    menuItems.forEach(item => {
        item.addEventListener('click', () => {
            Object.values(sections).forEach(section => section.classList.add('d-none'));
            const target = item.getAttribute('data-target');
            sections[target].classList.remove('d-none');
            menuItems.forEach(menu => menu.classList.remove('active'));
            item.classList.add('active');
        });
    });

    // Back button handling
    document.querySelectorAll('.back-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            Object.values(sections).forEach(section => section.classList.add('d-none'));
            sections['main-content'].classList.remove('d-none');
            menuItems.forEach(menu => menu.classList.remove('active'));
            document.querySelector('.profile-menu[data-target="main-content"]').classList.add('active');
        });
    });

    // Create Thread form
    const threadForm = document.getElementById('create-thread-form');
    threadForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const title = document.getElementById('threadTitle').value;
        const category = document.getElementById('threadCategory').value;
        const content = document.getElementById('threadContent').value;
        if (title && category && content) {
            document.getElementById('thread-success').classList.remove('d-none');
            setTimeout(() => document.getElementById('thread-success').classList.add('d-none'), 3000);
            threadForm.reset();
            // Simulate adding thread to forum
            const forumBody = document.querySelector('#forum .card-body');
            const newThread = document.createElement('div');
            newThread.className = 'thread';
            newThread.innerHTML = `
                <h5 class="thread-title">${title}</h5>
                <p class="thread-meta">
                    <i class="bi bi-person-fill"></i> Posted by Username |
                    <i class="bi bi-chat-fill"></i> 0 comments |
                    <i class="bi bi-heart-fill like-btn" data-liked="false"></i> <span class="like-count">0</span> likes
                </p>`;
            forumBody.prepend(newThread);
        }
    });

    // Edit Profile form
    const profileForm = document.getElementById('edit-profile-form');
    profileForm.addEventListener('submit', (e) => {
        e.preventDefault();
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirmPassword').value;
        if (password && password !== confirmPassword) {
            alert('Passwords do not match!');
            return;
        }
        document.getElementById('profile-success').classList.remove('d-none');
        setTimeout(() => document.getElementById('profile-success').classList.add('d-none'), 3000);
        profileForm.reset();
        document.getElementById('username').value = 'CurrentUsername';
        document.getElementById('bio').value = 'Current bio...';
    });

    // Profile image preview
    document.getElementById('profileImage').addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = document.getElementById('profile-image-preview');
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        }
    });

    // Like button handling
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('like-btn')) {
            const liked = e.target.getAttribute('data-liked') === 'true';
            const likeCountSpan = e.target.nextElementSibling;
            let likeCount = parseInt(likeCountSpan.textContent);
            if (liked) {
                likeCount--;
                e.target.setAttribute('data-liked', 'false');
                e.target.classList.remove('liked');
            } else {
                likeCount++;
                e.target.setAttribute('data-liked', 'true');
                e.target.classList.add('liked');
            }
            likeCountSpan.textContent = likeCount;
        }
    });

    // Thread sorting
    document.getElementById('sort-threads').addEventListener('change', (e) => {
        const sortBy = e.target.value;
        const forumBody = document.querySelector('#forum .card-body');
        const threads = Array.from(forumBody.querySelectorAll('.thread'));
        threads.sort((a, b) => {
            if (sortBy === 'newest') {
                return parseInt(b.getAttribute('data-thread-id')) - parseInt(a.getAttribute('data-thread-id'));
            } else {
                const likesA = parseInt(a.querySelector('.like-count').textContent);
                const likesB = parseInt(b.querySelector('.like-count').textContent);
                return likesB - likesA;
            }
        });
        forumBody.innerHTML = '';
        threads.forEach(thread => forumBody.appendChild(thread));
    });

    // Search functionality
    document.getElementById('search-btn').addEventListener('click', () => {
        const query = document.getElementById('search-input').value.toLowerCase();
        const threads = document.querySelectorAll('.thread');
        threads.forEach(thread => {
            const title = thread.querySelector('.thread-title').textContent.toLowerCase();
            thread.style.display = title.includes(query) ? 'block' : 'none';
        });
    });

    // Login/Logout simulation
    let isLoggedIn = false;
    document.getElementById('login-btn').addEventListener('click', () => {
        isLoggedIn = true;
        document.getElementById('login-btn').classList.add('d-none');
        document.getElementById('register-btn').classList.add('d-none');
        document.getElementById('logout-btn').classList.remove('d-none');
        document.querySelector('.card-title.mt-2').textContent = 'LoggedInUser';
    });
    document.getElementById('logout-btn').addEventListener('click', () => {
        isLoggedIn = false;
        document.getElementById('login-btn').classList.remove('d-none');
        document.getElementById('register-btn').classList.remove('d-none');
        document.getElementById('logout-btn').classList.add('d-none');
        document.querySelector('.card-title.mt-2').textContent = 'Username';
    });
});