<?php
// index.php
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
  // ถ้าข้อมูลผู้ใช้ไม่พบ
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

  <nav class="bg-primary text-primary-content p-4 flex justify-between">
    <a href="index.php" class="font-bold text-lg">UniConnect</a>
    <div>
      <?php if (isset($_SESSION['user_id'])): ?>
        <span>Hi, <?= htmlspecialchars($_SESSION['role']) ?>, <?= htmlspecialchars($_SESSION['username']) ?>.</span>
        <a href="logout.php" class="btn btn-sm btn-secondary ml-2">Logout</a>
      <?php else: ?>
        <a href="login.php" class="btn btn-sm btn-secondary">Login</a>
        <a href="register.php" class="btn btn-sm btn-accent ml-2">Register</a>
      <?php endif; ?>
    </div>
  </nav>

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
    <div class="text-sm breadcrumbs mb-6">
      <ul>
        <li><a href="index.php">Home</a></li>
        <li>Forum</li>
      </ul>
    </div>

    <main class="grid grid-cols-3 gap-4">

      <!-- Sidebar -->
      <div class="side-bar col-span-1">
        <div id="profile-section" class="card bg-base-100 shadow-md mb-4">
          <div class="card-body">
            <h2 class="card-title"><?= $username ?></h2>
            <img src="<?=$profile_image ?>" alt="Profile Image">
            <p><?= $bio ?></p>
          </div>
        </div>

        <div id="category-section" class="card bg-base-100 shadow-md mb-4">
          <ul class="card-body space-y-2">
            <?php foreach ($categories as $category): ?>
              <li>
                <a href="category.php?id=<?= $category['id']; ?>" class="link link-hover text-primary">
                  <?= htmlspecialchars($category['name']); ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div id="static-section" class="card bg-base-100 shadow-md mb-4">
          <ul class="card-body">
            <li>online: <?= htmlspecialchars($users_count) ?></li>
            <li>thread: <?= htmlspecialchars($thread_count) ?> </li>
            <li>comment: <?= htmlspecialchars($comment_count) ?> </li>
          </ul>
        </div>
      </div>

      <!-- Forum -->
      <div id="forum" class="col-span-2">
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
      </div>

    </main>

</body>

</html>