<div class="thread-list">
    <?php foreach ($threads as $thread): ?>
        <div class="thread card bg-base-100 shadow-md mb-4 p-4">
            <h4 class="card-title"><?php echo htmlspecialchars($thread['title']); ?></h4>
            <p>หมวดหมู่: <?php echo htmlspecialchars($thread['category_name']); ?></p>
            <p>โดย: <?php echo htmlspecialchars($thread['username']); ?></p>
        </div>
    <?php endforeach; ?>
</div>