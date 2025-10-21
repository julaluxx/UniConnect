<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'models/pdo.php';
require_once 'models/datalayer.php';

// สร้างอ็อบเจ็กต์ DataLayer
try {
    $dataLayer = new DataLayer($conn);
} catch (Exception $e) {
    die("เกิดข้อผิดพลาดในการสร้าง DataLayer: " . $e->getMessage());
}

// ดึงข้อมูลทุกตาราง
$allData = $dataLayer->getAllTablesData();
if (isset($allData['error'])) {
    die("เกิดข้อผิดพลาด: " . htmlspecialchars($allData['error']));
}

$users = $allData['users'] ?? [];
$categories = $allData['categories'] ?? [];
$threads = $allData['threads'] ?? [];
$comments = $allData['comments'] ?? [];
$likes = $allData['likes'] ?? [];
$reports = $allData['reports'] ?? [];

// GET parameters
$action = $_GET['action'] ?? '';
$threadId = $_GET['thread'] ?? null;
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
    foreach ($users as $user) {
        if ($user['id'] == $_SESSION['user_id']) {
            $currentUser = $user;
            break;
        }
    }
}

// ล็อกเอาท์
if ($action === 'logout') {
    session_destroy();
    header("Location: index.php");
    exit;
}

// ล็อกอิน
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
        $_SESSION['username'] = $foundUser['username'];
        $_SESSION['role'] = $foundUser['role'];
        header("Location: index.php");
        exit;
    } else {
        $loginError = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
    }
}

// สมัครสมาชิก
$registerError = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (!$username || !$email || !$password || !$confirmPassword) {
        $registerError = 'กรุณากรอกข้อมูลให้ครบทุกช่อง';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $registerError = 'รูปแบบอีเมลไม่ถูกต้อง';
    } elseif ($password !== $confirmPassword) {
        $registerError = 'รหัสผ่านไม่ตรงกัน';
    } else {
        try {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn()) {
                $registerError = 'อีเมลนี้ถูกใช้แล้ว';
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, 'user', NOW())");
                $stmt->execute([$username, $email, $hashedPassword]);
                $newUserId = $conn->lastInsertId();
                $_SESSION['user_id'] = $newUserId;
                $_SESSION['username'] = $username;
                $_SESSION['role'] = 'user';
                header("Location: index.php");
                exit;
            }
        } catch (PDOException $e) {
            $registerError = 'เกิดข้อผิดพลาดระหว่างการสมัครสมาชิก: ' . $e->getMessage();
        }
    }
}

// กรองกระทู้ตามคำค้นหา
$filteredThreads = $threads; // ค่าเริ่มต้นเป็น $threads ซึ่งเป็น array
if ($searchQuery) {
    $searchResults = $dataLayer->searchThreads($searchQuery);
    if (isset($searchResults['error']) || !is_array($searchResults)) {
        $filteredThreads = []; // ตั้งเป็น array ว่างถ้ามีข้อผิดพลาดหรือผลลัพธ์ไม่ใช่ array
    } else {
        $filteredThreads = $searchResults;
    }
}

// การกระทำเกี่ยวกับกระทู้
if ($threadId && $currentUser['role'] !== 'guest') {
    // Like toggle
    if ($action === 'like-toggle') {
        try {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM likes WHERE thread_id = ? AND user_id = ?");
            $stmt->execute([$threadId, $currentUser['id']]);
            $hasLiked = $stmt->fetchColumn() > 0;
            if ($hasLiked) {
                $stmt = $conn->prepare("DELETE FROM likes WHERE thread_id = ? AND user_id = ?");
                $stmt->execute([$threadId, $currentUser['id']]);
            } else {
                $stmt = $conn->prepare("INSERT INTO likes (thread_id, user_id, created_at) VALUES (?, ?, NOW())");
                $stmt->execute([$threadId, $currentUser['id']]);
            }
            header("Location: ?thread=$threadId");
            exit;
        } catch (PDOException $e) {
            echo "<div class='alert alert-error'>เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    // Report
    if ($action === 'confirm-report' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM reports WHERE thread_id = ? AND reported_by = ?");
            $stmt->execute([$threadId, $currentUser['id']]);
            if ($stmt->fetchColumn() == 0) {
                $description = trim($_POST['description'] ?? '');
                $stmt = $conn->prepare("INSERT INTO reports (thread_id, reported_by, description, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$threadId, $currentUser['id'], $description]);
            }
            header("Location: ?thread=$threadId");
            exit;
        } catch (PDOException $e) {
            echo "<div class='alert alert-error'>เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    // Comment
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
        try {
            $content = trim($_POST['content'] ?? '');
            if ($content) {
                $stmt = $conn->prepare("INSERT INTO comments (thread_id, author_id, content, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$threadId, $currentUser['id'], $content]);
            }
            header("Location: ?thread=$threadId");
            exit;
        } catch (PDOException $e) {
            echo "<div class='alert alert-error'>เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    // การรายงานคอมเมนต์
    if ($action === 'report-comment' && $commentId && $currentUser['role'] !== 'guest') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $stmt = $conn->prepare("SELECT COUNT(*) FROM reports WHERE comment_id = ? AND reported_by = ?");
                $stmt->execute([$commentId, $currentUser['id']]);
                if ($stmt->fetchColumn() == 0) {
                    $description = trim($_POST['description'] ?? '');
                    $stmt = $conn->prepare("INSERT INTO reports (comment_id, reported_by, description, created_at) VALUES (?, ?, ?, NOW())");
                    $stmt->execute([$commentId, $currentUser['id'], $description]);
                }
                header("Location: ?thread=$threadId");
                exit;
            } catch (PDOException $e) {
                echo "<div class='alert alert-error'>เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }

    // Delete thread (Admin)
    if ($action === 'delete-thread' && $currentUser['role'] === 'admin') {
        try {
            $stmt = $conn->prepare("DELETE FROM threads WHERE id = ?");
            $stmt->execute([$threadId]);
            header("Location: ?action=manage-thread");
            exit;
        } catch (PDOException $e) {
            echo "<div class='alert alert-error'>เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}

// สร้างกระทู้ใหม่
$threadError = '';
if ($action === 'create-new-thread' && $currentUser['role'] !== 'guest') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_thread'])) {
        $title = trim($_POST['title'] ?? '');
        $categoryId = $_POST['category_id'] ?? '';
        $content = trim($_POST['content'] ?? '');
        if (!$title || !$categoryId || !$content) {
            $threadError = 'กรุณากรอกข้อมูลให้ครบทุกช่อง';
        } else {
            try {
                $stmt = $conn->prepare("INSERT INTO threads (title, category_id, author_id, content, created_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$title, $categoryId, $currentUser['id'], $content]);
                header("Location: index.php");
                exit;
            } catch (PDOException $e) {
                $threadError = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
            }
        }
    }
}

// แก้ไขโปรไฟล์
$editError = '';
if ($action === 'edit-profile' && $currentUser['role'] !== 'guest') {
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
            try {
                $params = [$username, $email, $bio, $currentUser['id']];
                $sql = "UPDATE users SET username = ?, email = ?, bio = ? WHERE id = ?";
                if ($password) {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "UPDATE users SET username = ?, email = ?, bio = ?, password = ? WHERE id = ?";
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
            } catch (PDOException $e) {
                $editError = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
            }
        }
    }
}

// การกระทำของแอดมิน
if ($currentUser['role'] === 'admin') {
    // ลบผู้ใช้
    if ($action === 'delete-user' && $userIdParam) {
        try {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userIdParam]);
            header("Location: ?action=manage-user");
            exit;
        } catch (PDOException $e) {
            echo "<div class='alert alert-error'>เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    // แก้ไขผู้ใช้
    if ($action === 'edit-user' && $userIdParam && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $role = trim($_POST['role'] ?? 'user');
        if ($username && $email) {
            try {
                $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
                $stmt->execute([$username, $email, $role, $userIdParam]);
                header("Location: ?action=manage-user");
                exit;
            } catch (PDOException $e) {
                echo "<div class='alert alert-error'>เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }
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