<?php
// user_page.php
session_start();
require 'pdo.php';

$username = $_GET['username'] ?? '';

if (!$username) {
  http_response_code(400);
  echo "Username is required.";
  exit;
}

// ดึงข้อมูลผู้ใช้
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
  http_response_code(404);
  echo "User not found.";
  exit;
}

// ดึง threads ที่ผู้ใช้นี้เขียน
$stmtThreads = $conn->prepare("
    SELECT t.*, c.name AS category_name 
    FROM threads t 
    JOIN categories c ON t.category_id = c.id 
    WHERE t.author_id = ? 
    ORDER BY t.created_at DESC
");
$stmtThreads->execute([$user['id']]);
$threads = $stmtThreads->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <title><?= htmlspecialchars($user['username']) ?>'s Profile</title>
</head>

<body class="bg-gray-100 min-h-screen">

  <!-- Navbar -->
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
    <!-- โปรไฟล์ผู้ใช้ -->
    <div class="card bg-white shadow-md p-6 mb-6">
      <h2 class="text-2xl font-bold mb-2"><?= htmlspecialchars($user['username']) ?></h2>
      <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
      <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
      <?php if (!empty($user['bio'])): ?>
        <p class="mt-2"><strong>Bio:</strong> <?= nl2br(htmlspecialchars($user['bio'])) ?></p>
      <?php endif; ?>
    </div>

    <!-- กระทู้ของผู้ใช้ -->
    <h3 class="text-xl font-semibold mb-4">Threads by <?= htmlspecialchars($user['username']) ?>:</h3>

    <?php if (count($threads) > 0): ?>
      <?php foreach ($threads as $t): ?>
        <div class="card bg-base-100 shadow-md mb-4">
          <div class="card-body">
            <h2 class="card-title"><?= htmlspecialchars($t['title']) ?></h2>
            <p class="text-sm text-gray-500">Category: <?= htmlspecialchars($t['category_name']) ?> |
              Created at: <?= htmlspecialchars($t['created_at']) ?>
            </p>
            <p><?= nl2br(htmlspecialchars($t['content'])) ?></p>
            <a href="thread.php?id=<?= $t['id'] ?>" class="btn btn-sm btn-outline mt-2">View Thread</a>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>This user hasn't posted any threads yet.</p>
    <?php endif; ?>
  </div>

</body>

</html>