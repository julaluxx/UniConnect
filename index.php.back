<?php
require 'data_layer.php';
$dataLayer = new DataLayer($conn);
$allData = $dataLayer->getAllTablesData();

$users = $allData['users'];
$categories = $allData['categories'];
$threads = $allData['threads'];
$comments = $allData['comments'];
$likes = $allData['likes'];
$reports = $allData['reports'];

// GET parameters
$action = $_GET['action'] ?? '';
$threadId = $_GET['thread'] ?? null;
$userIdParam = $_GET['user'] ?? null;

// ===== HANDLE CURRENT USER =====
$currentUser = [
    'id' => 0,
    'username' => 'Guest',
    'email' => '',
    'role' => 'guest',
    'profile_image' => null,
];
if (isset($_SESSION['user_id'])) {
    foreach ($users as $user) {
        if ($user['id'] == $_SESSION['user_id']) {
            $currentUser = $user;
            break;
        }
    }
}

// ===== LOGOUT =====
if ($action === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}

// ===== LOGIN =====
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

// ===== REGISTER =====
$registerError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // ตรวจสอบข้อมูลว่าง
    if (!$username || !$email || !$password || !$confirmPassword) {
        $registerError = 'กรุณากรอกข้อมูลให้ครบทุกช่อง';
    }
    // ตรวจสอบรูปแบบอีเมล
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registerError = 'รูปแบบอีเมลไม่ถูกต้อง';
    }
    // ตรวจสอบรหัสผ่านตรงกัน
    elseif ($password !== $confirmPassword) {
        $registerError = 'รหัสผ่านไม่ตรงกัน';
    } else {
        try {
            // ตรวจสอบว่าอีเมลนี้มีอยู่แล้วหรือยัง
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $exists = $stmt->fetchColumn();

            if ($exists) {
                $registerError = 'อีเมลนี้ถูกใช้แล้ว';
            } else {
                // แฮ็ชรหัสผ่าน
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                // เพิ่มข้อมูลลงฐานข้อมูล (พร้อมเวลาและรูปโปรไฟล์เริ่มต้น)
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, 'user', NOW())");
                $stmt->execute([$username, $email, $hashedPassword]);

                // ล็อกอินอัตโนมัติหลังสมัคร
                $newUserId = $conn->lastInsertId();
                $_SESSION['user_id'] = $newUserId;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'user';

                // กลับไปหน้าแรก
                header("Location: index.php");
                exit;
            }
        } catch (PDOException $e) {
            $registerError = 'เกิดข้อผิดพลาดระหว่างการสมัครสมาชิก กรุณาลองใหม่อีกครั้ง';
        }
    }
}

// GET parameters
$action = $_GET['action'] ?? '';
$threadId = $_GET['thread'] ?? null;
$userIdParam = $_GET['user'] ?? null;
$searchQuery = $_GET['q'] ?? ''; // เพิ่มตัวแปรสำหรับคำค้นหา

// กรองกระทู้ตามคำค้นหา
$filteredThreads = $threads; // เริ่มต้นด้วยกระทู้ทั้งหมด
if ($searchQuery) {
    $filteredThreads = array_filter($threads, function ($thread) use ($searchQuery) {
        // ค้นหาคำใน title หรือ content ของกระทู้ (case-insensitive)
        return stripos($thread['title'], $searchQuery) !== false ||
            stripos($thread['content'], $searchQuery) !== false;
    });
}

// ===== THREAD ACTIONS =====
if ($threadId && $currentUser['role'] !== 'guest') {

    // Like toggle
    if ($action === 'like-toggle') {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM likes WHERE thread_id=? AND user_id=?");
        $stmt->execute([$threadId, $currentUser['id']]);
        $hasLiked = $stmt->fetchColumn() > 0;
        if ($hasLiked) {
            $stmt = $conn->prepare("DELETE FROM likes WHERE thread_id=? AND user_id=?");
            $stmt->execute([$threadId, $currentUser['id']]);
        } else {
            $stmt = $conn->prepare("INSERT INTO likes (thread_id, user_id, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$threadId, $currentUser['id']]);
        }
        header("Location: ?thread=$threadId");
        exit;
    }

    // Report
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

    // Comment
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
        $content = trim($_POST['content'] ?? '');
        if ($content) {
            $stmt = $conn->prepare("INSERT INTO comments (thread_id, author_id, content, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$threadId, $currentUser['id'], $content]);
        }
        header("Location: ?thread=$threadId");
        exit;
    }

    // Delete thread (Admin)
    if ($action === 'delete-thread' && $currentUser['role'] === 'admin') {
        $stmt = $conn->prepare("DELETE FROM threads WHERE id=?");
        $stmt->execute([$threadId]);
        header("Location: ?action=manage-thread");
        exit;
    }
}

// ===== CREATE NEW THREAD =====
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

// ===== EDIT PROFILE =====
if ($action === 'edit-profile' && $currentUser['role'] !== 'guest') {
    $editError = '';
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
            $_SESSION['username'] = $username;
            $currentUser['username'] = $username;
            $currentUser['email'] = $email;
            $currentUser['bio'] = $bio;
            header("Location: index.php");
            exit;
        }
    }
}

// ===== ADMIN USER ACTIONS =====
if ($currentUser['role'] === 'admin') {

    // Delete user
    if ($action === 'delete-user' && $userIdParam) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->execute([$userIdParam]);
        header("Location: ?action=manage-user");
        exit;
    }

    // Edit user
    if ($action === 'edit-user' && $userIdParam && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = trim($_POST['role'] ?? 'user');
        if ($username && $email) {
            $stmt = $conn->prepare("UPDATE users SET username=?, email=?, role=? WHERE id=?");
            $stmt->execute([$username, $email, $role, $userIdParam]);
            header("Location: ?action=manage-user");
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
            <div id="dialogue" class="col-span-2">
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

                if ($action === 'manage-thread' && $currentUser['role'] === 'admin') {
                    include 'components/ThreadManage.php';
                }
                if ($action === 'manage-user' && $currentUser['role'] === 'admin') {
                    include 'components/UserManage.php';
                }

                // แสดง ThreadList เสมอ
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