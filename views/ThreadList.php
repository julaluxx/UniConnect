<?php
$filteredThreads = $filteredThreads ?? $allData['threads'] ?? [];
$threadId = $threadId ?? null;
?>
<div class="card bg-base-100 shadow-xl p-4">
    <h2 class="card-title">กระทู้ทั้งหมด</h2>
    <?php if (empty($filteredThreads)): ?>
        <p>ไม่พบกระทู้</p>
    <?php else: ?>
        <?php foreach ($filteredThreads as $thread): ?>
            <div class="card bg-base-200 p-4 mb-2">
                <h3 class="font-bold"><a href="?thread=<?php echo $thread['id']; ?>" class="link link-primary"><?php echo htmlspecialchars($thread['title']); ?></a></h3>
                <p><?php echo htmlspecialchars(substr($thread['content'], 0, 100)) . (strlen($thread['content']) > 100 ? '...' : ''); ?></p>
                <p class="text-sm">โดย: <?php echo htmlspecialchars($allData['users'][$thread['author_id'] - 1]['username'] ?? 'ไม่ระบุ'); ?> | หมวดหมู่: <?php echo htmlspecialchars($allData['categories'][$thread['category_id'] - 1]['name'] ?? 'ไม่ระบุ'); ?> | วันที่: <?php echo htmlspecialchars($thread['created_at']); ?></p>
                <?php if ($threadId == $thread['id']): ?>
                    <div class="mt-2">
                        <h4 class="font-bold">ความคิดเห็น</h4>
                        <?php
                        $threadComments = array_filter($allData['comments'] ?? [], fn($c) => $c['thread_id'] == $threadId);
                        if (empty($threadComments)): ?>
                            <p>ยังไม่มีความคิดเห็น</p>
                        <?php else: ?>
                            <?php foreach ($threadComments as $comment): ?>
                                <div class="border-t pt-2 mt-2">
                                    <p><?php echo htmlspecialchars($comment['content']); ?></p>
                                    <p class="text-sm">โดย: <?php echo htmlspecialchars($allData['users'][$comment['author_id'] - 1]['username'] ?? 'ไม่ระบุ'); ?> | วันที่: <?php echo htmlspecialchars($comment['created_at']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if ($currentUser['role'] !== 'guest'): ?>
                            <form method="POST" action="?thread=<?php echo $threadId; ?>" class="mt-2">
                                <input type="hidden" name="comment" value="1">
                                <textarea name="content" class="textarea textarea-bordered w-full" placeholder="เพิ่มความคิดเห็น" required></textarea>
                                <button type="submit" class="btn btn-primary mt-2">ส่งความคิดเห็น</button>
                            </form>
                            <div class="mt-2">
                                <a href="?action=like-toggle&thread=<?php echo $threadId; ?>" class="btn btn-sm btn-outline"><?php echo in_array($threadId, array_column($allData['likes'] ?? [], 'thread_id')) ? 'ยกเลิกถูกใจ' : 'ถูกใจ'; ?></a>
                                <a href="?action=report&thread=<?php echo $threadId; ?>" class="btn btn-sm btn-warning">รายงาน</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>