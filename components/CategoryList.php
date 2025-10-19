<div id="category-section" class="card bg-base-100 shadow-md mb-4">
    <ul class="card-body space-y-2">
        <?php foreach ($categories as $category): ?>
            <li>
                <a href="category.php?id=<?= $category['id']; ?>" class="link link-hover text-primary">
                    <?= htmlspecialchars($category['name']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>