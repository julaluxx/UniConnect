<?php
// index.php
require 'data_layer.php';

// สร้างอินสแตนซ์ของ DataLayer
$dataLayer = new DataLayer($conn);

// ดึงข้อมูลทั้งหมดจากฐานข้อมูล
$allData = $dataLayer->getAllTablesData();

// แยกเก็บข้อมูลแต่ละตารางไว้ในตัวแปรเฉพาะ
$users = $allData['users'] ?? [];
$categories = $allData['categories'] ?? [];
$threads = $allData['threads'] ?? [];
$comments = $allData['comments'] ?? [];
$likes = $allData['likes'] ?? [];
$reports = $allData['reports'] ?? [];

// เก็บ user ที่ login อยู่ (ถ้ามี)
$userId = $_SESSION['user_id'] ?? null;
$currentUser = null;
if ($userId) {
  // ค้นหาข้อมูล user จาก $users
  foreach ($users as $user) {
    if ($user['id'] == $userId) {
      $currentUser = $user;
      break;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <title>UniConnect</title>
</head>

<body class="bg-gray-100 min-h-screen">
  <!-- Navbar Component -->
  <?php include 'components/Navbar.php'; ?>

  <div class="container mx-auto mt-6 p-4">
    <div id="top-bar" class="flex justify-between mb-4">
      <!-- Search box -->
      <?php include 'components/SearchBox.php'; ?>

      <!-- Breadcrumb -->
      <?php include 'components/Breadcrumb.php'; ?>
    </div>

    <main class="grid grid-cols-3 gap-4">
      <!-- Sidebar -->
      <div class="side-bar col-span-1">
        <!-- Profile Component -->
        <?php include 'components/Profile.php'; ?>

        <!-- CategoryList Component -->
        <?php include 'components/CategoryList.php'; ?>

        <!-- Statistic Section -->
        <?php include 'components/Statistic.php'; ?>
      </div>

      <!-- Forum -->
      <div id="forum" class="col-span-2">
        <?php
        // จัดการ action login/register/logout
        $action = $_GET['action'] ?? '';
        if ($action === 'login') {
          include 'components/Login.php';
        } elseif ($action === 'register') {
          include 'components/Register.php';
        } elseif ($action === 'logout') {
          include 'components/Logout.php';
        }
        ?>

        <!-- New Thread -->
        <?php if ($currentUser): ?>
          <?php include 'components/NewThread.php'; ?>
        <?php endif; ?>

        <!-- ThreadList Component -->
        <?php include 'components/ThreadList.php'; ?>
      </div>
    </main>
  </div>

  <!-- Footer -->
   <?php include 'components/Footer.php'; ?>
</body>

</html>