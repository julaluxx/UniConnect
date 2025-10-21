<?php
$categories = $allData['categories'] ?? [];
?>
<div class="card bg-base-100 shadow-xl p-4 mb-4">
    <h2 class="card-title">หมวดหมู่</h2>
    <ul class="menu">
        <?php foreach ($categories as $category): ?>
            <li><a href="?q=<?php echo urlencode($category['name']); ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>