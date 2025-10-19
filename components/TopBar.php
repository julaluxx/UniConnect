<div id="top-bar" class="grid grid-cols-3 gap-4 mb-4">
    <!-- Search box -->
    <label class="input w-full">
        <svg class="h-[1em] opacity-50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
            <g stroke-linejoin="round" stroke-linecap="round" stroke-width="2.5" fill="none" stroke="currentColor">
                <circle cx="11" cy="11" r="8"></circle>
                <path d="m21 21-4.3-4.3"></path>
            </g>
        </svg>
        <input type="search" required placeholder="Search" />
    </label>

    <!-- Breadcrumb -->
    <div class="breadcrumbs w-full">
        <ul>
            <li><a href="./index.php">Home</a></li>
            <li>Forum</li>
            <?php if (isset($_GET['category'])): ?>
                <?php
                $categoryName = 'ไม่ระบุหมวดหมู่';
                foreach ($categories as $category) {
                    if ((int) $_GET['category'] === (int) $category['id']) {
                        $categoryName = htmlspecialchars($category['name']);
                        break;
                    }
                }
                ?>
                <li><?= $categoryName; ?></li>
            <?php endif; ?>
        </ul>
    </div>


    <!-- Create New Thread -->
    <div class="button flex justify-end w-full">
        <?php if ($currentUser): ?>
            <button class="btn btn-dash btn-primary">
                <a href="?action=create-new-thread">สร้างกระทู้ใหม่</a>
            </button>
        <?php endif; ?>
    </div>
</div>