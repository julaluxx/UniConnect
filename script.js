document.addEventListener('DOMContentLoaded', () => {
    let currentRole = 'guest'; // Default role

    // Menu handling
    const menuItems = document.querySelectorAll('.profile-menu');
    const sections = {
        'main-content': document.getElementById('main-content'),
        'my-threads': document.getElementById('my-threads'),
        'notifications': document.getElementById('notifications'),
        'my-comments': document.getElementById('my-comments'),
        'edit-profile': document.getElementById('edit-profile'),
        'report-manager': document.getElementById('report-manager'),
        'category-manager': document.getElementById('category-manager'),
        'user-manager': document.getElementById('user-manager')
    };

    // Show main content by default
    sections['main-content'].classList.remove('d-none');
    document.querySelector('.profile-menu[data-target="main-content"]').classList.add('active');
    document.getElementById('current-page').textContent = 'Forum';

    // ดึงเธรด
    async function loadThreads() {
        try {
            const response = await fetch('threads.php'); // ปรับ path ให้ตรง (สมมติอยู่ root)
            if (!response.ok) throw new Error('Failed to load threads');
            const threads = await response.json();
            const forumBody = document.querySelector('#forum .card-body');
            forumBody.innerHTML = '';
            threads.forEach(thread => {
                const div = document.createElement('div');
                div.className = 'thread';
                div.setAttribute('data-thread-id', thread.id);
                div.innerHTML = `
                    <h5 class="thread-title"><a href="thread.php?id=${thread.id}">${thread.title}</a></h5>
                    <p class="thread-meta">
                        <i class="bi bi-person-fill"></i> Posted by ${thread.author} |
                        <i class="bi bi-chat-fill"></i> ${thread.comments || 0} comments |
                        <i class="bi bi-heart-fill like-btn" data-liked="false"></i> <span class="like-count">${thread.likes || 0}</span> likes
                    </p>`;
                forumBody.appendChild(div);
            });
        } catch (err) {
            console.error('Error loading threads:', err);
        }
    }
    loadThreads();

    // Function to switch section and update breadcrumb
    const switchSection = (target) => {
        Object.values(sections).forEach(section => section && section.classList.add('d-none'));
        sections[target].classList.remove('d-none');
        menuItems.forEach(menu => menu.classList.remove('active'));
        const activeMenu = document.querySelector(`.profile-menu[data-target="${target}"]`);
        if (activeMenu) activeMenu.classList.add('active');

        // Update breadcrumb
        let currentPageText = 'Forum';
        if (target === 'my-threads') currentPageText = 'My Threads';
        else if (target === 'notifications') currentPageText = 'Notifications';
        else if (target === 'my-comments') currentPageText = 'My Comments';
        else if (target === 'edit-profile') currentPageText = 'Edit Profile';
        else if (target === 'report-manager') currentPageText = 'Report Manager';
        else if (target === 'category-manager') currentPageText = 'Category Manager';
        else if (target === 'user-manager') currentPageText = 'User Manager';
        document.getElementById('current-page').textContent = currentPageText;
    };

    // Menu click event
    menuItems.forEach(item => {
        item.addEventListener('click', () => {
            const target = item.getAttribute('data-target');
            switchSection(target);
        });
    });

    // Back button handling
    document.querySelectorAll('.back-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            switchSection('main-content');
        });
    });

    // Breadcrumb Home link handling
    const homeLink = document.querySelector('.breadcrumb-item a[href="#main-content"]');
    if (homeLink) {
        homeLink.addEventListener('click', (e) => {
            e.preventDefault();
            switchSection('main-content');
        });
    }

    // Create Thread form
    const threadForm = document.getElementById('create-thread-form');
    if (threadForm) {
        threadForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const title = document.getElementById('threadTitle').value;
            const category = document.getElementById('threadCategory').value;
            const content = document.getElementById('threadContent').value;
            const token = localStorage.getItem('token');
            if (title && category && content && token) {
                try {
                    const user = JSON.parse(localStorage.getItem('user'));
                    const response = await fetch('threads.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${token}` },
                        body: JSON.stringify({ title, content, category_id: category, author_id: user.user_id })
                    });
                    if (!response.ok) throw new Error('Failed to create thread');
                    document.getElementById('thread-success').classList.remove('d-none');
                    setTimeout(() => document.getElementById('thread-success').classList.add('d-none'), 3000);
                    threadForm.reset();
                    loadThreads(); // Reload threads
                } catch (err) {
                    console.error('Error creating thread:', err);
                }
            }
        });
    }

    // Edit Profile form
    const profileForm = document.getElementById('edit-profile-form');
    if (profileForm) {
        profileForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            if (password && password !== confirmPassword) {
                alert('รหัสผ่านไม่ตรงกัน!');
                return;
            }
            document.getElementById('profile-success').classList.remove('d-none');
            setTimeout(() => document.getElementById('profile-success').classList.add('d-none'), 3000);
            // TODO: Add API call to update profile
        });
    }

    // ล็อกอิน (ลบ duplication)
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const username = document.getElementById('login-username').value;
            const password = document.getElementById('login-password').value;
            try {
                const response = await fetch('login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, password })
                });
                const data = await response.json();
                if (response.ok) {
                    localStorage.setItem('token', data.token);
                    localStorage.setItem('user', JSON.stringify(data.user));
                    document.getElementById('login-btn').classList.add('d-none');
                    document.getElementById('register-btn').classList.add('d-none');
                    document.getElementById('logout-btn').classList.remove('d-none');
                    document.querySelector('.card-title.mt-2').textContent = data.user.username;
                    currentRole = data.user.role;
                    // แสดงเมนูตาม role
                    if (currentRole === 'moderator' || currentRole === 'admin') {
                        document.getElementById('menu-report-manager').classList.remove('d-none');
                        document.getElementById('menu-category-manager').classList.remove('d-none');
                    }
                    if (currentRole === 'admin') {
                        document.getElementById('menu-user-manager').classList.remove('d-none');
                    }
                    const loginModal = bootstrap.Modal.getInstance(document.getElementById('loginModal'));
                    loginModal.hide();
                    switchSection('main-content');
                    loadThreads(); // Reload after login
                } else {
                    document.getElementById('login-error').classList.remove('d-none');
                    setTimeout(() => document.getElementById('login-error').classList.add('d-none'), 3000);
                }
            } catch (err) {
                console.error('Login error:', err);
            }
        });
    }

    // Register Form in Modal
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const username = document.getElementById('register-username').value;
            const email = document.getElementById('register-email').value;
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('register-confirm-password').value;
            if (password !== confirmPassword) {
                document.getElementById('register-error').classList.remove('d-none');
                setTimeout(() => document.getElementById('register-error').classList.add('d-none'), 3000);
                return;
            }
            try {
                const response = await fetch('register.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ username, email, password })
                });
                if (response.ok) {
                    document.getElementById('register-success').classList.remove('d-none');
                    setTimeout(() => {
                        document.getElementById('register-success').classList.add('d-none');
                        const registerModal = bootstrap.Modal.getInstance(document.getElementById('registerModal'));
                        registerModal.hide();
                    }, 3000);
                    registerForm.reset();
                } else {
                    alert('Registration failed');
                }
            } catch (err) {
                console.error('Register error:', err);
            }
        });
    }

    // Add Category form (สำหรับ Category Manager)
    const addCategoryForm = document.getElementById('add-category-form');
    if (addCategoryForm) {
        addCategoryForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const newCat = document.getElementById('new-category').value;
            if (newCat) {
                // TODO: Add API call to create category
                // สำหรับตอนนี้จำลอง
                const catList = document.querySelector('.category-container ul.list-group');
                const newLi = document.createElement('li');
                newLi.className = 'list-group-item';
                newLi.innerHTML = `${newCat} <button class="btn btn-sm btn-danger float-end">Delete</button>`;
                catList.appendChild(newLi);

                const select = document.getElementById('threadCategory');
                const option = document.createElement('option');
                option.value = newCat.toLowerCase().replace(/\s/g, '-');
                option.textContent = newCat;
                select.appendChild(option);

                addCategoryForm.reset();
            }
        });
    }
});