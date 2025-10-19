<?php
// components/CategoryList.php

$selectedCategory = $_GET['category'] ?? null;
?>

<div class="card bg-white p-4 shadow rounded mb-4">
    <div class="flex justify-between items-center mb-2">
        <h3 class="card-title text-lg font-bold">หมวดหมู่</h3>
        <a href="./index.php" class="text-sm text-blue-500 hover:underline">ล้าง</a>
    </div>

    <?php if (empty($categories)): ?>
        <p class="text-gray-500 text-center">ยังไม่มีหมวดหมู่</p>
    <?php else: ?>
        <ul class="menu menu-vertical w-full">
            <?php foreach ($categories as $category): ?>
                <?php
                $catName = htmlspecialchars($category['name'] ?? 'ไม่ระบุ');
                $catId = $category['id'] ?? 0;
                $activeClass = ($selectedCategory == $catId) ? 'bg-blue-100 font-semibold' : '';
                ?>
                <li>
                    <a href="?category=<?= $catId; ?>" class="hover:bg-gray-100 <?= $activeClass; ?>">
                        <?= $catName; ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
