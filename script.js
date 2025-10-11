document.addEventListener('DOMContentLoaded', () => {
    // Get all menu items
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

    // Add click event to menu items
    menuItems.forEach(item => {
        item.addEventListener('click', () => {
            // Hide all sections
            Object.values(sections).forEach(section => {
                section.classList.add('d-none');
            });

            // Show the selected section
            const target = item.getAttribute('data-target');
            sections[target].classList.remove('d-none');

            // Update active menu item
            menuItems.forEach(menu => menu.classList.remove('active'));
            item.classList.add('active');
        });
    });
});