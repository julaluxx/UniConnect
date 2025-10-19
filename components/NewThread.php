<?php
// เมื่อผู้ใช้ส่งฟอร์มตั้งกระทู้ใหม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_thread'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $category_id = intval($_POST['category_id']);
    $author_id = $_SESSION['user_id'];

    if ($title !== '' && $content !== '' && $category_id > 0) {
        $stmt = $conn->prepare("
            INSERT INTO threads (title, content, category_id, author_id, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([$title, $content, $category_id, $author_id]);
        header("Location: index.php");
        exit;
    } else {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
    }
}
?>

<div class="bg-white rounded shadow p-6 mb-6">
    <h2 class="text-xl font-semibold mb-4">ตั้งกระทู้ใหม่</h2>

    <?php if (!empty($error)): ?>
        <div class="text-red-500 mb-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="block mb-1">หัวข้อกระทู้</label>
            <input type="text" name="title" required class="input input-bordered w-full"
                placeholder="เช่น แชร์ประสบการณ์ฝึกงาน" />
        </div>

        <div class="mb-3">
            <label class="block mb-1">หมวดหมู่</label>
            <select name="category_id" required class="select select-bordered w-full">
                <option value="">-- เลือกหมวดหมู่ --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="block mb-1">เนื้อหากระทู้</label>
            <textarea name="content" rows="5" required class="textarea textarea-bordered w-full"
                placeholder="พิมพ์เนื้อหาที่คุณต้องการพูดคุย..."></textarea>
        </div>

        <button type="submit" name="create_thread" class="btn btn-primary w-full">
            โพสต์กระทู้
        </button>
    </form>
</div>