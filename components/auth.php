<?php
// auth.php
require 'pdo.php';

// ฟังก์ชันตรวจสอบและทำความสะอาดข้อมูล
function sanitizeInput($data) {
    return htmlspecialchars(trim($data));
}

// ตรวจสอบว่าเป็นการล็อกอินหรือลงทะเบียน
$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];

    // ตรวจสอบว่าช่องไม่ว่าง
    if (empty($username) || empty($password)) {
        $_SESSION['error'] = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
        header('Location: ?action=login');
        exit();
    }

    // ดึงข้อมูลผู้ใช้จากฐานข้อมูล
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // ตรวจสอบว่าผู้ใช้มีอยู่และรหัสผ่านถูกต้อง
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['error'] = 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง';
        header('Location: ?action=login');
        exit();
    }
} elseif ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $bio = sanitizeInput($_POST['bio'] ?? '');

    // ตรวจสอบว่าช่องที่จำเป็นไม่ว่าง
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
        header('Location: ?action=register');
        exit();
    }

    // ตรวจสอบว่ารหัสผ่านและยืนยันรหัสผ่านตรงกัน
    if ($password !== $confirm_password) {
        $_SESSION['error'] = 'รหัสผ่านและยืนยันรหัสผ่านไม่ตรงกัน';
        header('Location: ?action=register');
        exit();
    }

    // ตรวจสอบความยาวของชื่อผู้ใช้และรหัสผ่าน
    if (strlen($username) < 3 || strlen($password) < 6) {
        $_SESSION['error'] = 'ชื่อผู้ใช้ต้องมีอย่างน้อย 3 ตัวอักษร และรหัสผ่านต้องมีอย่างน้อย 6 ตัวอักษร';
        header('Location: ?action=register');
        exit();
    }

    // ตรวจสอบว่าชื่อผู้ใช้มีอยู่แล้วหรือไม่
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetch()) {
        $_SESSION['error'] = 'ชื่อผู้ใช้นี้มีอยู่แล้ว';
        header('Location: ?action=register');
        exit();
    }

    // เข้ารหัสรหัสผ่าน
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // บันทึกผู้ใช้ใหม่ลงฐานข้อมูล
    $stmt = $conn->prepare("
        INSERT INTO users (username, password, bio, profile_image) 
        VALUES (?, ?, ?, ?)
    ");
    $profile_image = './assets/square_holder.png'; // รูปโปรไฟล์เริ่มต้น
    $success = $stmt->execute([$username, $hashed_password, $bio, $profile_image]);

    if ($success) {
        // ดึง ID ผู้ใช้ที่เพิ่งสร้าง
        $user_id = $conn->lastInsertId();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['error'] = 'เกิดข้อผิดพลาดในการลงทะเบียน กรุณาลองใหม่';
        header('Location: ?action=register');
        exit();
    }
} else {
    // หากไม่มี action ที่ถูกต้อง เปลี่ยนเส้นทางไปยังหน้าแรก
    header('Location: index.php');
    exit();
}
?>