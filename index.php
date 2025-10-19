<?php
require 'data_layer.php';

// สร้างอินสแตนซ์ของ DataLayer
$dataLayer = new DataLayer($conn);

// ดึงข้อมูลทั้งหมด
$data = $dataLayer->getAllData($_SESSION['user_id']);

// แยกข้อมูลไปใช้งาน
$threads = $data['threads'];
$user_data = $data['user'];
$categories = $data['categories'];
$statistics = $data['statistics'];
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
        if (isset($_GET['action']) && $_GET['action'] === 'login') {
          include 'components/Login.php';
        } elseif (isset($_GET['action']) && $_GET['action'] === 'register') {
          include 'components/Register.php';
        } elseif (isset($_GET['action']) && $_GET['action'] === 'logout') {
          include 'components/Logout.php';
        }
        ?>

        <!-- ThreadList Component -->
        <?php include 'components/ThreadList.php'; ?>
      </div>
    </main>
  </div>
</body>

</html>