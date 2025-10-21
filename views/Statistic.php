<?php
$threadCount = count($allData['threads'] ?? []);
$commentCount = count($allData['comments'] ?? []);
$userCount = count($allData['users'] ?? []);
?>
<div class="card bg-base-100 shadow-xl p-4 mb-4">
    <h2 class="card-title">สถิติ</h2>
    <p><strong>จำนวนกระทู้:</strong> <?php echo $threadCount; ?></p>
    <p><strong>จำนวนความคิดเห็น:</strong> <?php echo $commentCount; ?></p>
    <p><strong>จำนวนผู้ใช้:</strong> <?php echo $userCount; ?></p>
</div>