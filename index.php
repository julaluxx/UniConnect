<?php
session_start();
require 'pdo.php';
$stmt = $conn->query("SELECT t.*, c.name AS category_name, u.username FROM threads t
    JOIN categories c ON t.category_id=c.id
    JOIN users u ON t.author_id=u.id
    ORDER BY t.created_at DESC");
$threads = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

  <title>Document</title>
</head>

<body class="bg-gray-100 min-h-screen">

  <nav class="bg-primary text-primary-content p-4 flex justify-between">
    <a href="index.php" class="font-bold text-lg">UniConnect</a>
    <div>
      <?php if (isset($_SESSION['user_id'])): ?>
        <span>Hi, <?= htmlspecialchars($_SESSION['role']) ?></span>
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
        <a href="create_thread.php" class="btn btn-success">Create Thread</a>
      <?php endif; ?>
    </div>

    <?php foreach ($threads as $t): ?>
      <div class="card bg-base-100 shadow-md mb-4">
        <div class="card-body">
          <h2 class="card-title"><?= htmlspecialchars($t['title']) ?></h2>
          <p class="text-sm text-gray-500">By <?= htmlspecialchars($t['username']) ?> | Category:
            <?= htmlspecialchars($t['category_name']) ?>
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



</body>

</html>