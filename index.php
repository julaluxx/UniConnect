<?php
// index.php

require 'data_layer.php';
$dataLayer = new DataLayer($conn);
$allData = $dataLayer->getAllTablesData();

$searchQuery = trim($_GET['q'] ?? '');
$searchResults = [];

if ($searchQuery) {
    $searchResults = $dataLayer->searchThreads($searchQuery);
}

$users = $allData['users'];
$categories = $allData['categories'];
$threads = $allData['threads'];
$comments = $allData['comments'];
$likes = $allData['likes'];
$reports = $allData['reports'];

// ตรวจสอบ action ก่อน HTML
$action = $_GET['action'] ?? '';

// ===== HANDLE CURRENT USER =====
$userId = $_SESSION['user_id'] ?? null;

// กำหนดค่า default user สำหรับ guest
$currentUser = [
    'id' => 0,
    'username' => 'Guest',
    'email' => '',
    'role' => 'guest',
    'profile_image' => null,
];

// ถ้ามี user_id จริง → ดึงข้อมูลจาก $users
if ($userId) {
    foreach ($users as $user) {
        if ($user['id'] == $userId) {
            $currentUser = $user;
            break;
        }
    }
}

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

// ===== HANDLE THREAD ACTIONS =====
$threadId = $_GET['thread'] ?? null;

if ($threadId && $currentUser['role'] !== 'guest') {

    // LIKE
    if ($action === 'like') {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM likes WHERE thread_id=? AND user_id=?");
        $stmt->execute([$threadId, $currentUser['id']]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $conn->prepare("INSERT INTO likes (thread_id, user_id, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$threadId, $currentUser['id']]);
        }
        header("Location: ?thread=$threadId");
        exit;
    }

    // UNLIKE
    if ($action === 'unlike') {
        $stmt = $conn->prepare("DELETE FROM likes WHERE thread_id=? AND user_id=?");
        $stmt->execute([$threadId, $currentUser['id']]);
        header("Location: ?thread=$threadId");
        exit;
    }

    // REPORT (เหมือนเดิม)
    if ($action === 'report') {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM reports WHERE thread_id=? AND reported_by=?");
        $stmt->execute([$threadId, $currentUser['id']]);
        $alreadyReported = $stmt->fetchColumn() > 0;
        include 'components/Report.php';
        exit;
    }

    if ($action === 'confirm-report' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM reports WHERE thread_id=? AND reported_by=?");
        $stmt->execute([$threadId, $currentUser['id']]);
        if ($stmt->fetchColumn() == 0) {
            $description = trim($_POST['description'] ?? '');
            $stmt = $conn->prepare("INSERT INTO reports (thread_id, reported_by, description, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$threadId, $currentUser['id'], $description]);
        }
        header("Location: ?thread=$threadId");
        exit;
    }

    // COMMENTS (เหมือนเดิม)
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
        $content = trim($_POST['content'] ?? '');
        if ($content) {
            $stmt = $conn->prepare("INSERT INTO comments (thread_id, author_id, content, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$threadId, $currentUser['id'], $content]);
        }
        header("Location: ?thread=$threadId");
        exit;
    }
}

// ===== HANDLE CREATE NEW THREAD =====
if ($action === 'create-new-thread' && $currentUser['role'] !== 'guest') {
    $threadError = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_thread'])) {
        $title = trim($_POST['title'] ?? '');
        $categoryId = $_POST['category_id'] ?? '';
        $content = trim($_POST['content'] ?? '');

        if (!$title || !$categoryId || !$content) {
            $threadError = 'กรุณากรอกข้อมูลให้ครบทุกช่อง';
        } else {
            $stmt = $conn->prepare("INSERT INTO threads (title, category_id, author_id, content, created_at) VALUES (?, ?, ?, ?, NOW())");
            $stmt->execute([$title, $categoryId, $currentUser['id'], $content]);
            header("Location: index.php");
            exit;
        }
    }
}

// ===== HANDLE EDIT PROFILE =====
if ($action === 'edit-profile' && $currentUser['role'] !== 'guest') {
    $editError = '';
    $editSuccess = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_profile'])) {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $confirmPassword = trim($_POST['confirm_password'] ?? '');

        if (!$username || !$email) {
            $editError = 'กรุณากรอกชื่อและอีเมลให้ครบ';
        } elseif ($password && $password !== $confirmPassword) {
            $editError = 'รหัสผ่านไม่ตรงกัน';
        } else {
            $params = [$username, $email, $bio, $currentUser['id']];
            $sql = "UPDATE users SET username=?, email=?, bio=? WHERE id=?";

            if ($password) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET username=?, email=?, bio=?, password=? WHERE id=?";
                $params = [$username, $email, $bio, $hashed, $currentUser['id']];
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            // อัปเดต session + currentUser
            $_SESSION['username'] = $username;
            $currentUser['username'] = $username;
            $currentUser['email'] = $email;
            $currentUser['bio'] = $bio;

            // redirect ไปหน้าหลักทันที
            header("Location: index.php");
            exit;
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
                if ($action === 'edit-profile' && $currentUser['role'] !== 'guest') {
                    include 'components/EditProfile.php';
                } elseif ($action === 'login' && $currentUser['role'] === 'guest') {
                    include 'components/Login.php';
                } elseif ($action === 'register' && $currentUser['role'] === 'guest') {
                    include 'components/Register.php';
                } elseif ($action === 'create-new-thread' && $currentUser['role'] !== 'guest') {
                    include 'components/NewThread.php';
                }

                // แสดง ThreadList เสมอ (ยกเว้น edit-profile)
                if ($action !== 'edit-profile') {
                    include 'components/ThreadList.php';
                }
                ?>
            </div>
        </main>
    </div>

    <?php include 'components/Footer.php'; ?>
</body>

</html>