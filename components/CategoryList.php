<?php
// components/CategoryList.php

if (empty($categories)) {
    echo '<div class="card bg-white p-4 shadow rounded">';
    echo '<p class="text-gray-500 text-center">ยังไม่มีหมวดหมู่</p>';
    echo '</div>';
    return;
}
?>

<div class="card bg-white p-4 shadow rounded mb-4">
    <h3 class="card-title mb-2 text-lg font-bold">หมวดหมู่</h3>
    <ul class="menu menu-vertical w-full">
        <?php foreach ($categories as $category): ?>
            <?php
            $catName = htmlspecialchars($category['name'] ?? 'ไม่ระบุ');
            $catId = $category['id'] ?? 0;
            ?>
            <li>
                <a href="?category=<?php echo $catId; ?>" class="hover:bg-gray-100">
                    <?php echo $catName; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>