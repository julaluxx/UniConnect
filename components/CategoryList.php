<div class="category-list card bg-base-100 shadow-md p-4 mb-4">
    <h3>หมวดหมู่</h3>
    <ul>
        <?php foreach ($categories as $category): ?>
            <li><?php echo htmlspecialchars($category['name']); ?></li>
        <?php endforeach; ?>
    </ul>
</div>