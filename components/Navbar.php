<nav class="bg-primary text-primary-content p-4 flex justify-between items-center">
    <!-- Logo / Brand -->
    <a href="index.php" class="font-bold text-lg hover:text-secondary transition">UniConnect</a>

    <!-- User Actions -->
    <div class="flex items-center space-x-2">
        <?php if ($currentUser['role'] !== 'guest'): ?>
            <span class="text-sm">
                Hi, <span class="font-semibold"><?= htmlspecialchars($currentUser['username']) ?></span>
                (<?= htmlspecialchars($currentUser['role']) ?>)
            </span>
            <a href="?action=logout" class="btn btn-sm btn-secondary">Logout</a>
        <?php else: ?>
            <a href="?action=login" class="btn btn-sm btn-secondary">Login</a>
            <a href="?action=register" class="btn btn-sm btn-accent">Register</a>
        <?php endif; ?>
    </div>
</nav>