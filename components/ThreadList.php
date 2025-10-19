<?php foreach ($threads as $t): ?>
    <div class="card bg-base-100 shadow-md mb-4">
        <div class="card-body">
            <h2 class="card-title"><?= htmlspecialchars($t['title']) ?></h2>
            <p class="text-sm text-gray-500">
                By <?= htmlspecialchars($t['username']) ?> | Category: <?= htmlspecialchars($t['category_name']) ?>
            </p>
            <p><?= nl2br(htmlspecialchars($t['content'])) ?></p>
            <div class="mt-2 flex gap-2">
                <a href="thread.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline">View</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="like_action.php?thread_id=<?= $t['id'] ?>" class="btn btn-sm btn-primary">Like</a>
                    <!-- Button modal -->
                    <label for="report-modal-<?= $t['id'] ?>" class="btn btn-sm btn-warning cursor-pointer">Report</label>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal for report -->
    <input type="checkbox" id="report-modal-<?= $t['id'] ?>" class="modal-toggle">
    <div class="modal">
        <div class="modal-box">
            <h3 class="font-bold text-lg">Report Thread: <?= htmlspecialchars($t['title']) ?></h3>
            <form method="post" action="report_action.php" class="mt-4">
                <input type="hidden" name="thread_id" value="<?= $t['id'] ?>">
                <textarea name="description" class="textarea textarea-bordered w-full" placeholder="Describe the issue"
                    required></textarea>
                <div class="modal-action">
                    <button type="submit" class="btn btn-error">Submit Report</button>
                    <label for="report-modal-<?= $t['id'] ?>" class="btn">Cancel</label>
                </div>
            </form>
        </div>
    </div>
<?php endforeach; ?>