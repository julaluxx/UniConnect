<nav class="bg-primary text-primary-content p-4 flex justify-between">
    <a href="index.php" class="font-bold text-lg">UniConnect</a>
    <div>
        <?php if (isset($currentUser)): ?>
            <span>Hi, <?= htmlspecialchars($currentUser['role'] ?? 'user') ?>, <?= htmlspecialchars($currentUser['username'] ?? 'ไม่ระบุ') ?>.</span>
            <a href="?action=logout" class="btn btn-sm btn-secondary ml-2">Logout</a>
        <?php else: ?>
            <a href="?action=login" class="btn btn-sm btn-secondary">Login</a>
            <a href="?action=register" class="btn btn-sm btn-accent ml-2">Register</a>
        <?php endif; ?>
    </div>
</nav>
