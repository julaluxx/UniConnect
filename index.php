<?php
// index.php
require 'data_layer.php';

// สร้างอินสแตนซ์ของ DataLayer
$dataLayer = new DataLayer($conn);

// ดึงข้อมูลทั้งหมดจากฐานข้อมูล
$allData = $dataLayer->getAllTablesData();
$users = $allData['users'] ?? [];
$categories = $allData['categories'] ?? [];
$threads = $allData['threads'] ?? [];
$comments = $allData['comments'] ?? [];
$likes = $allData['likes'] ?? [];
$reports = $allData['reports'] ?? [];

// ตรวจสอบ action ก่อน HTML
$action = $_GET['action'] ?? '';

// Logout
if ($action === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Login form submit
$loginError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    $foundUser = null;
    foreach ($users as $user) {
        if ($user['email'] === $email) {
            $foundUser = $user;
            break;
        }
    }

    if ($foundUser && password_verify($password, $foundUser['password'])) {
        $_SESSION['user_id'] = $foundUser['id'];
        $_SESSION['username'] = $foundUser['username'] ?? '';
        $_SESSION['role'] = $foundUser['role'] ?? '';
        header("Location: index.php");
        exit;
    } else {
        $loginError = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
    }
}

// เก็บ user ที่ login อยู่ (ถ้ามี)
$userId = $_SESSION['user_id'] ?? null;
$currentUser = null;
if ($userId) {
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
  <?php include 'components/Navbar.php'; ?>

  <div class="container mx-auto mt-6 p-4">
    <?php include 'components/TopBar.php'; ?>

    <main class="grid grid-cols-3 gap-4">
      <div class="side-bar col-span-1">
        <?php include 'components/Profile.php'; ?>
        <?php include 'components/CategoryList.php'; ?>
        <?php include 'components/Statistic.php'; ?>
      </div>

      <div id="forum" class="col-span-2">
        <?php
        // แสดง Login / Register form
        if ($action === 'login' && !$currentUser) {
            include 'components/Login.php';
        } elseif ($action === 'register' && !$currentUser) {
            include 'components/Register.php';
        }

        // สร้างกระทู้ใหม่
        if ($action === 'create-new-thread' && $currentUser) {
            include 'components/NewThread.php';
        }

        // แสดง Thread list
        include 'components/ThreadList.php';
        ?>
      </div>
    </main>
  </div>

  <?php include 'components/Footer.php'; ?>
</body>
</html>
