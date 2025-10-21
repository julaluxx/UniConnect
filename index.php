<?php
session_start();

require_once 'models/pdo.php';
require_once 'models/datalayer.php';

// สร้างอ็อบเจ็กต์ DataLayer
$dataLayer = new DataLayer($conn);

// ทดสอบดึงข้อมูลทุกตาราง
$allData = $dataLayer->getAllTablesData();

// แสดงผลข้อมูล (ตัวอย่างง่ายๆ)
echo "<h1>ข้อมูลจากฐานข้อมูล</h1>";
foreach ($allData as $table => $rows) {
    echo "<h2>ตาราง: $table</h2>";
    if (empty($rows)) {
        echo "<p>ไม่มีข้อมูลในตารางนี้</p>";
    } else {
        echo "<ul>";
        foreach ($rows as $row) {
            echo "<li>";
            foreach ($row as $key => $value) {
                echo "$key: " . htmlspecialchars($value) . " | ";
            }
            echo "</li>";
        }
        echo "</ul>";
    }
}

// ทดสอบค้นหา threads
$keyword = "test"; // ตัวอย่างคีย์เวิร์ด
$searchResults = $dataLayer->searchThreads($keyword);
echo "<h2>ผลการค้นหา Threads (คีย์เวิร์ด: $keyword)</h2>";
if (empty($searchResults)) {
    echo "<p>ไม่พบผลลัพธ์</p>";
} else {
    echo "<ul>";
    foreach ($searchResults as $thread) {
        echo "<li>หัวข้อ: " . htmlspecialchars($thread['title']) . " | ผู้เขียน: " . htmlspecialchars($thread['author_name']) . " | หมวดหมู่: " . htmlspecialchars($thread['category_name']) . "</li>";
    }
    echo "</ul>";
}
?>