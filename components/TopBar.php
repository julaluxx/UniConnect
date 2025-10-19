<div id="top-bar" class="grid grid-cols-3 gap-4 mb-4 items-center">

    <!-- Search box -->
    <form method="GET" action="" class="w-full">
        <div class="relative">
            <input type="search" name="q" placeholder="Search" class="input w-full pr-10" />
            <button type="submit" class="absolute right-0 top-0 h-full px-3 text-gray-500">
                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor" stroke-width="2">
                    <circle cx="11" cy="11" r="8"></circle>
                    <path d="M21 21l-4.3-4.3"></path>
                </svg>
            </button>
        </div>
    </form>

    <!-- Breadcrumb -->
    <div class="breadcrumbs w-full">
        <ul class="flex space-x-2 text-gray-600">
            <li><a href="./index.php" class="hover:underline">Home</a></li>

            <?php
            $action = $_GET['action'] ?? '';
            $threadId = $_GET['thread'] ?? null;
            $categoryId = $_GET['category'] ?? null; // สำหรับหน้า category list
            
            // ฟังก์ชันหาชื่อ category
            function getCategoryName($categories, $id)
            {
                foreach ($categories as $cat) {
                    if ($cat['id'] == $id)
                        return $cat['name'];
                }
                return 'Unknown Category';
            }

            if ($action === 'login') {
                echo '<li>Login</li>';
            } elseif ($action === 'register') {
                echo '<li>Register</li>';
            } elseif ($action === 'create-new-thread') {
                echo '<li><a href="./index.php?action=forum" class="hover:underline">Forum</a></li>';
                echo '<li>สร้างกระทู้ใหม่</li>';
            } elseif ($threadId) {
                // หา thread
                $threadTitle = 'Unknown Thread';
                $threadCategoryId = null;
                foreach ($threads as $thread) {
                    if ($thread['id'] == $threadId) {
                        $threadTitle = $thread['title'];
                        $threadCategoryId = $thread['category_id'] ?? null;
                        break;
                    }
                }

                // แสดง category
                if ($threadCategoryId) {
                    $catName = getCategoryName($categories, $threadCategoryId);
                    echo '<li><a href="?category=' . $threadCategoryId . '" class="hover:underline">' . htmlspecialchars($catName) . '</a></li>';
                }

                echo '<li>' . htmlspecialchars($threadTitle) . '</li>';
            } elseif ($categoryId) {
                // หน้า category list
                $catName = getCategoryName($categories, $categoryId);
                echo '<li>' . htmlspecialchars($catName) . '</li>';
            } else {
                // default
                echo '<li>Forum</li>';
            }
            ?>
        </ul>
    </div>


    <!-- Create New Thread -->
    <div class="flex justify-end w-full">
        <?php if ($currentUser['role'] !== 'guest'): ?>
            <a href="?action=create-new-thread" class="btn btn-dash btn-primary">
                สร้างกระทู้ใหม่
            </a>
        <?php endif; ?>
    </div>
</div>