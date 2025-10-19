<?php
// components/CategoryList.php

$selectedCategory = $_GET['category'] ?? null; // หมวดหมู่ปัจจุบัน

if (empty($categories)) {
    echo '<div class="card bg-white p-4 shadow rounded">';
    echo '<p class="text-gray-500 text-center">ยังไม่มีหมวดหมู่</p>';
    echo '</div>';
    return;
}
?>

<div class="card bg-white p-4 shadow rounded mb-4">
    <div class="grid-cols-2 flex justify-between">
        <h3 class="card-title mb-2 text-lg font-bold">หมวดหมู่</h3>
        <a href="./index.php">ล้าง</a>
    </div>
    <ul class="menu menu-vertical w-full">
        <?php foreach ($categories as $category): ?>
            <?php
            $catName = htmlspecialchars($category['name'] ?? 'ไม่ระบุ');
            $catId = $category['id'] ?? 0;
            $activeClass = ($selectedCategory && $selectedCategory == $catId) ? 'bg-blue-100 font-semibold' : '';
            ?>
            <li>
                <!-- กดแล้วส่ง category พร้อม reset thread -->
                <a href="?category=<?php echo $catId; ?>" class="hover:bg-gray-100 <?php echo $activeClass; ?>">
                    <?php echo $catName; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>