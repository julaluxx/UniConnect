<?php foreach ($threads as $thread): ?>

    <div class="thread-item card bg-base-100 shadow-md p-4 mb-4">

        <h3 class="card-title"><?= $thread['title'] ?></h3>
        <p>โดย: <?= $thread['username'] ?></p>
        <p>เมื่อ: <?= $thread['created_at'] ?></p>
        <p><?= $thread['content'] ?></p>

        <?php foreach ($thread['comments'] as $comment): ?>
            <div class="comment-item border-t mt-4 pt-4">
                <p><strong><?= $comment['username'] ?></strong> แสดงความคิดเห็นเมื่อ <?= $comment['created_at'] ?></p>
                <p><?= $comment['content'] ?></p>
            </div>
        <?php endforeach; ?>
        
    </div>

<?php endforeach; ?>