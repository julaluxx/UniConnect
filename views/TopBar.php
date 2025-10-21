<div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">UniConnect Community</h1>
    <?php if ($currentUser['role'] !== 'guest'): ?>
        <a href="?action=create-new-thread" class="btn btn-primary">สร้างกระทู้ใหม่</a>
    <?php endif; ?>
</div>