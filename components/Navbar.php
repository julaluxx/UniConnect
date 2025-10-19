<nav class="bg-primary text-primary-content p-4 flex justify-between">
    <a href="index.php" class="font-bold text-lg">UniConnect</a>
    <div>
        <?php if (isset($_SESSION['user_id'])): ?>
            <span>Hi, <?= htmlspecialchars($_SESSION['role']) ?>, <?= htmlspecialchars($_SESSION['username']) ?>.</span>
            <a href="index.php?action=logout" class="btn btn-sm btn-secondary ml-2">Logout</a>
        <?php else: ?>
            <a href="index.php?action=login" class="btn btn-sm btn-secondary">Login</a>
            <a href="index.php?action=register" class="btn btn-sm btn-accent ml-2">Register</a>
        <?php endif; ?>
    </div>
</nav>