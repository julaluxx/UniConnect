<?php if ($user && in_array($user['role'], ['moderator', 'admin'])): ?>
    <li class="list-group-item profile-menu d-none" data-target="report-manager" id="menu-report-manager">Report Manager
    </li>
    <li class="list-group-item profile-menu d-none" data-target="category-manager" id="menu-category-manager">Category
        Manager</li>
<?php endif; ?>
<?php if ($user && $user['role'] === 'admin'): ?>
    <li class="list-group-item profile-menu d-none" data-target="user-manager" id="menu-user-manager">User Manager</li>
<?php endif; ?>