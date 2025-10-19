<?php
session_start();
require 'pdo.php';

// ดึงข้อมูลเธรด
$stmt = $conn->query("SELECT t.*, c.name AS category_name, u.username FROM threads t
    JOIN categories c ON t.category_id = c.id
    JOIN users u ON t.author_id = u.id
    ORDER BY t.created_at DESC");
$threads = $stmt->fetchAll();

// ดึงข้อมูลผู้ใช้
$stmt = $conn->prepare("SELECT username, bio, profile_image FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user_data = $stmt->fetch();

// ตรวจสอบว่ามีข้อมูลผู้ใช้หรือไม่
if ($user_data) {
  $username = htmlspecialchars($user_data['username']);
  $bio = htmlspecialchars($user_data['bio']);
  $profile_image = htmlspecialchars($user_data['profile_image']);
} else {
  $username = 'Guest';
  $bio = 'No bio available';
  $profile_image = './assets/square_holder.png';
}

// ดึงข้อมูลหมวดหมู่
$stmt = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll();

// ดึงข้อมูลสถิติของเว็บบอร์ด
$stmt = $conn->query("SELECT COUNT(id) FROM users");
$users_count = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(id) FROM threads");
$thread_count = $stmt->fetchColumn();

$stmt = $conn->query("SELECT COUNT(id) FROM comments");
$comment_count = $stmt->fetchColumn();
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

  <div class="container mx-auto mt-6">
    <div class="flex justify-between mb-4">
      <form method="get" class="flex">
        <input type="text" name="q" placeholder="Search threads" class="input input-bordered"
          value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        <button type="submit" class="btn btn-primary ml-2">Search</button>
      </form>
      <?php if (isset($_SESSION['user_id'])): ?>
        <a href="user/create_thread.php" class="btn btn-success">Create Thread</a>
      <?php endif; ?>
    </div>

    <!-- Breadcrumb -->
    <?php include 'components/Breadcrumb.php'; ?>

    <main class="grid grid-cols-3 gap-4">

      <!-- Sidebar -->
      <div class="side-bar col-span-1">
        <!-- Profile Component -->
        <?php include 'components/Profile.php'; ?>

        <!-- CategoryList Component -->
        <?php include 'components/CategoryList.php'; ?>

        <!-- Static Section (Users Count, Threads Count, etc.) -->
        <?php include 'components/Statistic.php'; ?>

      </div>

      <!-- Forum -->
      <div id="forum" class="col-span-2">

        <?php
        if (isset($_GET['action']) && $_GET['action'] === 'login'): ?>
          <!-- ฟอร์ม Login -->
          <?php include 'components/Login.php'; ?>
        <?php endif; ?>

        <!-- ThreadList Component -->
        <?php include 'components/ThreadList.php'; ?>
      </div>

    </main>

  </div>

</body>

</html>