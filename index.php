<?php
// index.php (ส่วนบน)
session_start();
require 'db.php';

// ดึง categories เพื่อแสดงใน sidebar / select
$catStmt = $conn->query("SELECT id, name FROM categories ORDER BY name");
$categories = $catStmt->fetchAll();

// ถ้าต้องการแสดง user profile
$userData = null;
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare("SELECT id, username, email, bio, profile_image, role FROM users WHERE id = :id LIMIT 1");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $userData = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>UniConnect</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-light">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">UniConnect</a>
    <div class="d-flex">
      <?php if ($userData): ?>
        <span class="me-2">สวัสดี, <?=htmlspecialchars($userData['username'])?></span>
        <a class="btn btn-outline-danger" href="logout.php">Logout</a>
      <?php else: ?>
        <button class="btn btn-outline-success me-2" id="login-btn" data-bs-toggle="modal" data-bs-target="#loginModal">Login</button>
        <button class="btn btn-outline-primary" id="register-btn" data-bs-toggle="modal" data-bs-target="#registerModal">Register</button>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- ปรับให้ category list มาจาก DB -->
<div class="container my-3">
  <div class="row">
    <div class="col-md-3">
      <ul class="list-group">
        <?php foreach ($categories as $cat): ?>
          <li class="list-group-item"><?=htmlspecialchars($cat['name'])?></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="col-md-9">
      <!-- Create Thread form -->
      <div class="card mb-3">
        <div class="card-body">
          <form id="create-thread-form">
            <input type="text" class="form-control mb-2" id="threadTitle" placeholder="Title" required>
            <select id="threadCategory" class="form-select mb-2" required>
              <option value="">-- เลือกหมวดหมู่ --</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= $cat['id'] ?>"><?=htmlspecialchars($cat['name'])?></option>
              <?php endforeach; ?>
            </select>
            <textarea id="threadContent" class="form-control mb-2" rows="4" placeholder="เนื้อหากระทู้" required></textarea>
            <button class="btn btn-primary" id="create-thread-btn">Create Thread</button>
            <div id="thread-alert" class="mt-2"></div>
          </form>
        </div>
      </div>

      <!-- Forum threads (จะเติมโดย AJAX) -->
      <div id="forum-threads"></div>
    </div>
  </div>
</div>

<!-- ใส่ modals (login/register) ตามไฟล์เดิม -->
<?php include 'modals.php'; // คุณสามารถแตกไฟล์ modal แยกเพื่อความสะดวก ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>
</body>
</html>
