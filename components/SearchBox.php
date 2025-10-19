<div class="flex justify-between mb-4">
    <form method="get" class="flex">
        <input type="text" name="q" placeholder="Search threads" class="input input-bordered"
            value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        <button type="submit" class="btn btn-primary ml-2">Search</button>
    </form>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="user/create_thread.php" class="btn btn-success">Create Thread</a>
    <?php endif; ?>
</div>