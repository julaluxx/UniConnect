<?php
// components/NewThread.php

if (!$currentUser) return;

$newThreadError = '';
$newThreadSuccess = '';

// ตรวจสอบการ submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_thread'])) {
    $title = trim($_POST['title'] ?? '');
    $categoryId = $_POST['category_id'] ?? '';
    $content = trim($_POST['content'] ?? '');

    if (!$title || !$categoryId || !$content) {
        $newThreadError = 'กรุณากรอกทุกช่อง';
    } else {
        // บันทึกลงฐานข้อมูล
        $stmt = $conn->prepare("INSERT INTO threads (user_id, category_id, title, content, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$currentUser['id'], $categoryId, $title, $content]);

        $newThreadSuccess = 'สร้างกระทู้สำเร็จ!';
        // เคลียร์ฟอร์ม
        $title = $content = '';
        $categoryId = '';
    }
}
?>

<div class="card bg-white p-6 shadow rounded mb-6">
    <h3 class="text-lg font-bold mb-4">สร้างกระทู้ใหม่</h3>

    <?php if ($newThreadError): ?>
        <p class="text-red-500 mb-2"><?php echo htmlspecialchars($newThreadError); ?></p>
    <?php elseif ($newThreadSuccess): ?>
        <p class="text-green-500 mb-2"><?php echo htmlspecialchars($newThreadSuccess); ?></p>
        <script>
            // หลังสร้างสำเร็จ ให้รีเฟรชหน้าเพจเพื่อกลับไปหน้า forum
            setTimeout(() => window.location.href = 'index.php', 1000);
        </script>
    <?php endif; ?>

    <form method="POST">
        <input type="hidden" name="user_id" value="<?php echo $currentUser['id']; ?>">

        <div class="mb-4">
            <label class="block mb-1 font-semibold">หัวข้อกระทู้</label>
            <input type="text" name="title" class="input w-full border" value="<?php echo htmlspecialchars($title ?? ''); ?>" required>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">หมวดหมู่</label>
            <select name="category_id" class="select w-full border" required>
                <option value="">-- เลือกหมวดหมู่ --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo (isset($categoryId) && $categoryId == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-4">
            <label class="block mb-1 font-semibold">เนื้อหากระทู้</label>
            <textarea name="content" class="textarea w-full border" rows="5" required><?php echo htmlspecialchars($content ?? ''); ?></textarea>
        </div>

        <button type="submit" name="new_thread" class="btn btn-primary">สร้างกระทู้</button>
    </form>
</div>
