<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'models/pdo.php';
require_once 'models/datalayer.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/ThreadController.php';
require_once 'controllers/UserController.php';

// สร้างอ็อบเจ็กต์ Controller
$dataLayer = new DataLayer($conn);
$authController = new AuthController($conn);
$threadController = new ThreadController($conn);
$userController = new UserController($conn);

// GET parameters
$action = $_GET['action'] ?? '';
$threadId = $_GET['thread'] ?? null;
$commentId = $_GET['comment'] ?? null;
$userIdParam = $_GET['user'] ?? null;
$searchQuery = $_GET['q'] ?? '';

// จัดการผู้ใช้ปัจจุบัน
$currentUser = [
    'id' => 0,
    'username' => 'Guest',
    'email' => '',
    'role' => 'guest',
    'bio' => null,
];
if (isset($_SESSION['user_id'])) {
    $users = $dataLayer->getUsers();
    if (!isset($users['error'])) {
        foreach ($users as $user) {
            if ($user['id'] == $_SESSION['user_id']) {
                $currentUser = $user;
                break;
            }
        }
    }
}

// เตรียมข้อมูลสำหรับ views
$data = [
    'currentUser' => $currentUser,
    'searchQuery' => $searchQuery,
    'action' => $action,
    'threadId' => $threadId,
    'commentId' => $commentId,
    'error' => null,
];

// ล็อกอิน
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $data = array_merge($data, $authController->login($_POST['email'] ?? '', $_POST['password'] ?? ''));
}

// สมัครสมาชิก
if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $data = array_merge($data, $authController->register(
        $_POST['username'] ?? '',
        $_POST['email'] ?? '',
        $_POST['password'] ?? '',
        $_POST['confirm_password'] ?? ''
    ));
}

// ล็อกเอาท์
if ($action === 'logout') {
    $authController->logout();
}

// แก้ไขโปรไฟล์
if ($action === 'edit-profile' && $currentUser['role'] !== 'guest' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_profile'])) {
    $data = array_merge($data, $authController->editProfile(
        $currentUser,
        $_POST['username'] ?? '',
        $_POST['email'] ?? '',
        $_POST['bio'] ?? '',
        $_POST['password'] ?? '',
        $_POST['confirm_password'] ?? ''
    ));
}

// สร้างกระทู้
if ($action === 'create-new-thread' && $currentUser['role'] !== 'guest' && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_thread'])) {
    $data = array_merge($data, $threadController->createThread(
        $currentUser,
        $_POST['title'] ?? '',
        $_POST['category_id'] ?? '',
        $_POST['content'] ?? ''
    ));
}

// คอมเมนต์
if ($threadId && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
    $threadController->addComment($threadId, $currentUser, $_POST['content'] ?? '');
}

// ไลค์/ยกเลิกไลค์
if ($action === 'like-toggle' && $threadId && $currentUser['role'] !== 'guest') {
    $threadController->likeToggle($threadId, $currentUser);
}

// รายงานกระทู้
if ($action === 'report' && $threadId && $currentUser['role'] !== 'guest' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $threadController->reportThread($threadId, $currentUser, $_POST['description'] ?? '');
}

// รายงานคอมเมนต์
if ($action === 'report-comment' && $commentId && $currentUser['role'] !== 'guest' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $threadController->reportComment($commentId, $threadId, $currentUser, $_POST['description'] ?? '');
}

// การกระทำของแอดมิน
if ($currentUser['role'] === 'admin') {
    if ($action === 'delete-user' && $userIdParam) {
        $userController->deleteUser($userIdParam);
    }
    if ($action === 'edit-user' && $userIdParam && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $userController->editUser(
            $userIdParam,
            $_POST['username'] ?? '',
            $_POST['email'] ?? '',
            $_POST['role'] ?? 'user'
        );
    }
    if ($action === 'delete-thread' && $threadId) {
        $threadController->deleteThread($threadId, $currentUser);
    }
    if ($action === 'manage-thread') {
        $data = array_merge($data, $threadController->manageThreads($currentUser));
    }
    if ($action === 'manage-user') {
        $data = array_merge($data, $userController->manageUsers());
    }
}

// โหลดข้อมูลสำหรับหน้า ThreadList หรือ ThreadDetail
if ($threadId) {
    $data = array_merge($data, $threadController->threadDetail($threadId, $currentUser));
} else {
    $data = array_merge($data, $threadController->listThreads($searchQuery));
}

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@4.12.10/dist/full.min.css" rel="stylesheet" type="text/css">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>UniConnect</title>
</head>
<body class="bg-gray-100 min-h-screen">
    <?php include 'views/Navbar.php'; ?>
    <div class="container mx-auto mt-6 p-4">
        <?php include 'views/TopBar.php'; ?>
        <main class="grid grid-cols-3 gap-4">
            <div class="side-bar col-span-1">
                <?php include 'views/Profile.php'; ?>
                <?php include 'views/CategoryList.php'; ?>
                <?php include 'views/Statistic.php'; ?>
            </div>
            <div id="dialogue" class="col-span-2">
                <?php
                if ($data['error']) {
                    echo "<div class='alert alert-error'>" . htmlspecialchars($data['error']) . "</div>";
                }
                if ($threadId) {
                    include 'views/ThreadDetail.php';
                } elseif ($action === 'edit-profile' && $currentUser['role'] !== 'guest') {
                    include 'views/EditProfile.php';
                } elseif ($action === 'login' && $currentUser['role'] === 'guest') {
                    include 'views/Login.php';
                } elseif ($action === 'register' && $currentUser['role'] === 'guest') {
                    include 'views/Register.php';
                } elseif ($action === 'create-new-thread' && $currentUser['role'] !== 'guest') {
                    include 'views/NewThread.php';
                } elseif ($action === 'manage-thread' && $currentUser['role'] === 'admin') {
                    include 'views/ThreadManage.php';
                } elseif ($action === 'manage-user' && $currentUser['role'] === 'admin') {
                    include 'views/UserManage.php';
                } elseif ($action === 'report' && $threadId && $currentUser['role'] !== 'guest') {
                    include 'views/Report.php';
                } elseif ($action === 'report-comment' && $commentId && $currentUser['role'] !== 'guest') {
                    include 'views/ReportComment.php';
                } else {
                    include 'views/ThreadList.php';
                }
                ?>
            </div>
        </main>
    </div>
    <?php include 'views/Footer.php'; ?>
</body>
</html>