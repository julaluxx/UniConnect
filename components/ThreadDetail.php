<?php
// ThreadDetail.php

if (!$currentThread) {
    return;
}

// --- ตัวแปรสำหรับข้อผิดพลาดและข้อความแจ้งเตือน ---
$commentError = '';
$commentSuccess = '';
$reportError = '';
$reportSuccess = '';

// --- ตรวจสอบว่าผู้ใช้กดไลค์แล้วหรือยัง ---
$hasLiked = false;
if ($currentUser['role'] !== 'guest') {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM likes WHERE thread_id = ? AND user_id = ?");
    $stmt->execute([$currentThread['id'], $currentUser['id']]);
    $hasLiked = $stmt->fetchColumn() > 0;
}

// --- Thread Info ---
$title = htmlspecialchars($currentThread['title'] ?? 'ไม่ระบุ');
$authorId = $currentThread['author_id'] ?? null;
$author = ['username' => 'ไม่ระบุ'];
foreach ($users as $u) {
    if ($u['id'] == $authorId) {
        $author = $u;
        break;
    }
}

$categoryId = $currentThread['category_id'] ?? null;
$categoryName = 'ไม่ระบุ';
foreach ($categories as $c) {
    if ($c['id'] == $categoryId) {
        $categoryName = $c['name'];
        break;
    }
}

$createdAt = isset($currentThread['created_at'])
    ? date('d M Y H:i', strtotime($currentThread['created_at']))
    : 'ไม่ระบุ';

// --- Fetch Comments ---
$threadComments = array_filter($comments, fn($c) => ($c['thread_id'] ?? 0) == $currentThread['id']);

// --- Count Likes ---
$likeCount = count(array_filter($likes, fn($l) => ($l['thread_id'] ?? 0) == $currentThread['id']));

// --- Handle Comment Submission ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment']) && $currentUser['role'] !== 'guest') {
    $content = trim($_POST['content'] ?? '');
    if (empty($content)) {
        $commentError = 'กรุณากรอกข้อความคอมเมนต์';
    } else {
        try {
            $stmt = $conn->prepare("INSERT INTO comments (thread_id, author_id, content, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$currentThread['id'], $currentUser['id'], $content]);
            $commentSuccess = 'โพสต์คอมเมนต์สำเร็จ';
            // รีเฟรชหน้าเพื่อแสดงคอมเมนต์ใหม่
            header("Location: ?thread=" . $currentThread['id']);
            exit;
        } catch (PDOException $e) {
            $commentError = 'เกิดข้อผิดพลาดในการโพสต์คอมเมนต์ กรุณาลองใหม่';
        }
    }
}

// --- Handle Report Submission ---
if ($action === 'report' && $_SERVER['REQUEST_METHOD'] === 'POST' && $currentUser['role'] !== 'guest') {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM reports WHERE thread_id = ? AND reported_by = ?");
    $stmt->execute([$currentThread['id'], $currentUser['id']]);
    $alreadyReported = $stmt->fetchColumn() > 0;

    if ($alreadyReported) {
        $reportError = 'คุณได้รายงานกระทู้นี้แล้ว';
    } else {
        $description = trim($_POST['description'] ?? '');
        if (empty($description)) {
            $reportError = 'กรุณากรอกเหตุผลในการรายงาน';
        } else {
            try {
                $stmt = $conn->prepare("INSERT INTO reports (thread_id, reported_by, description, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$currentThread['id'], $currentUser['id'], $description]);
                $reportSuccess = 'รายงานกระทู้สำเร็จ';
                header("Location: ?thread=" . $currentThread['id']);
                exit;
            } catch (PDOException $e) {
                $reportError = 'เกิดข้อผิดพลาดในการรายงาน กรุณาลองใหม่';
            }
        }
    }
}
?>

<div class="mx-auto bg-white rounded shadow-lg p-6 mb-8">
    <a href="./index.php" class="inline-block mb-4 text-gray-500 hover:text-blue-700">&larr; กลับหน้าหลัก</a>

    <!-- Title -->
    <h2 class="text-3xl font-extrabold mb-2 text-gray-800"><?= $title; ?></h2>

    <!-- Info Bar -->
    <div class="flex flex-wrap justify-between items-center text-sm text-gray-500 mb-6 gap-2">
        <span>โดย: <span class="font-medium"><?= htmlspecialchars($author['username']); ?></span></span>
        <span>หมวด: <span class="font-medium"><?= htmlspecialchars($categoryName); ?></span></span>
        <span>สร้าง: <?= $createdAt; ?></span>
    </div>

    <!-- Content -->
    <div class="prose max-w-full mb-6">
        <?= nl2br(htmlspecialchars($currentThread['content'] ?? '')); ?>
    </div>

    <!-- Actions -->
    <div class="flex flex-wrap gap-3 items-center mb-6">
        <span class="text-gray-600 font-medium">❤️ <?= $likeCount; ?> ไลค์</span>

        <?php if ($currentUser['role'] !== 'guest'): ?>
            <a href="?thread=<?= $currentThread['id']; ?>&action=like-toggle"
                class="btn btn-sm <?= $hasLiked ? 'btn-outline' : 'btn-primary' ?>">
                <?= $hasLiked ? 'ยกเลิกไลค์' : 'กดไลค์' ?>
            </a>
            <button onclick="document.getElementById('report-modal').showModal()" class="btn btn-sm btn-error">รายงาน</button>
        <?php endif; ?>
    </div>

    <!-- Comments Section -->
    <div>
        <h3 class="text-xl font-semibold mb-4">คอมเมนต์ (<?= count($threadComments); ?>)</h3>

        <?php if ($currentUser['role'] !== 'guest'): ?>
            <?php if (!empty($commentError)): ?>
                <div class="alert alert-error mb-4">
                    <span><?= htmlspecialchars($commentError); ?></span>
                </div>
            <?php endif; ?>
            <?php if (!empty($commentSuccess)): ?>
                <div class="alert alert-success mb-4">
                    <span><?= htmlspecialchars($commentSuccess); ?></span>
                </div>
            <?php endif; ?>
            <form method="POST" class="mb-6">
                <textarea name="content" placeholder="เขียนคอมเมนต์..." class="textarea w-full mb-2 textarea-bordered"
                    required></textarea>
                <button type="submit" name="comment" class="btn btn-primary">โพสต์คอมเมนต์</button>
            </form>
        <?php else: ?>
            <p class="text-gray-500 mb-4">
                กรุณา <a href="?action=login" class="text-blue-500 underline">เข้าสู่ระบบ</a> เพื่อคอมเมนต์
            </p>
        <?php endif; ?>

        <?php if (empty($threadComments)): ?>
            <p class="text-gray-500">ยังไม่มีคอมเมนต์</p>
        <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($threadComments as $comment): ?>
                    <?php
                    $commentUserId = $comment['author_id'] ?? null;
                    $commentUser = ['username' => 'ไม่ระบุ'];
                    foreach ($users as $u) {
                        if ($u['id'] == $commentUserId) {
                            $commentUser = $u;
                            break;
                        }
                    }
                    $commentAt = isset($comment['created_at']) ? date('d M Y H:i', strtotime($comment['created_at'])) : 'ไม่ระบุ';
                    $content = htmlspecialchars($comment['content'] ?? '');
                    ?>
                    <div class="border rounded-lg p-4 bg-gray-50 hover:bg-gray-100 transition">
                        <div class="flex justify-between items-center text-gray-500 text-sm mb-2">
                            <span class="font-medium"><?= htmlspecialchars($commentUser['username']); ?></span>
                            <span><?= $commentAt; ?></span>
                        </div>
                        <p class="text-gray-700"><?= nl2br($content); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Report Modal -->
    <?php if ($currentUser['role'] !== 'guest'): ?>
        <dialog id="report-modal" class="modal">
            <div class="modal-box">
                <h3 class="font-bold text-lg">รายงานกระทู้</h3>
                <?php if (!empty($reportError)): ?>
                    <div class="alert alert-error mb-4">
                        <span><?= htmlspecialchars($reportError); ?></span>
                    </div>
                <?php endif; ?>
                <?php if (!empty($reportSuccess)): ?>
                    <div class="alert alert-success mb-4">
                        <span><?= htmlspecialchars($reportSuccess); ?></span>
                    </div>
                <?php endif; ?>
                <form method="POST" action="?thread=<?= $currentThread['id']; ?>&action=report">
                    <div class="form-control">
                        <textarea name="description" class="textarea textarea-bordered" placeholder="กรุณาระบุเหตุผล" required></textarea>
                    </div>
                    <div class="modal-action">
                        <button type="submit" class="btn btn-error">ส่งรายงาน</button>
                        <button type="button" class="btn" onclick="document.getElementById('report-modal').close()">ยกเลิก</button>
                    </div>
                </form>
            </div>
        </dialog>
    <?php endif; ?>
</div>