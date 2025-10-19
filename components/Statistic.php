<?php
// components/Statistic.php

// คำนวณสถิติ
$totalUsers = count($users);
$totalThreads = count($threads);
$totalComments = count($comments);
$totalLikes = count($likes);
$totalReports = count($reports);
?>

<div class="card bg-white p-4 shadow rounded mt-4">
    <h3 class="card-title mb-2 text-lg font-bold">สถิติ</h3>
    <ul class="space-y-2 text-gray-700">
        <li class="flex justify-between">
            <span>ผู้ใช้ทั้งหมด:</span>
            <span class="font-semibold"><?= $totalUsers; ?></span>
        </li>
        <li class="flex justify-between">
            <span>กระทู้ทั้งหมด:</span>
            <span class="font-semibold"><?= $totalThreads; ?></span>
        </li>
        <li class="flex justify-between">
            <span>คอมเมนต์ทั้งหมด:</span>
            <span class="font-semibold"><?= $totalComments; ?></span>
        </li>
        <li class="flex justify-between">
            <span>ไลค์ทั้งหมด:</span>
            <span class="font-semibold"><?= $totalLikes; ?></span>
        </li>
    </ul>
</div>