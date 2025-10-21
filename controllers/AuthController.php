<?php
require_once 'models/datalayer.php';

class AuthController {
    private $dataLayer;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->dataLayer = new DataLayer($conn);
    }

    public function login($email, $password) {
        $data = ['loginError' => ''];
        $allData = $this->dataLayer->getAllTablesData();
        if (isset($allData['error'])) {
            $data['loginError'] = $allData['error'];
            return $data;
        }
        $users = $allData['users'] ?? [];
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
            $data['loginError'] = 'อีเมลหรือรหัสผ่านไม่ถูกต้อง';
        }
        return $data;
    }

    public function register($username, $email, $password, $confirmPassword) {
        $data = ['registerError' => ''];
        if (!$username || !$email || !$password || !$confirmPassword) {
            $data['registerError'] = 'กรุณากรอกข้อมูลให้ครบทุกช่อง';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $data['registerError'] = 'รูปแบบอีเมลไม่ถูกต้อง';
        } elseif ($password !== $confirmPassword) {
            $data['registerError'] = 'รหัสผ่านไม่ตรงกัน';
        } else {
            try {
                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetchColumn()) {
                    $data['registerError'] = 'อีเมลนี้ถูกใช้แล้ว';
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $this->conn->prepare("INSERT INTO users (username, email, password, role, created_at) VALUES (?, ?, ?, 'user', NOW())");
                    $stmt->execute([$username, $email, $hashedPassword]);
                    $newUserId = $this->conn->lastInsertId();
                    $_SESSION['user_id'] = $newUserId;
                    $_SESSION['username'] = $username;
                    $_SESSION['role'] = 'user';
                    header("Location: index.php");
                    exit;
                }
            } catch (PDOException $e) {
                $data['registerError'] = 'เกิดข้อผิดพลาดระหว่างการสมัครสมาชิก: ' . $e->getMessage();
            }
        }
        return $data;
    }

    public function logout() {
        session_destroy();
        header("Location: index.php");
        exit;
    }

    public function editProfile($currentUser, $username, $email, $bio, $password, $confirmPassword) {
        $data = ['editError' => ''];
        if (!$username || !$email) {
            $data['editError'] = 'กรุณากรอกชื่อและอีเมลให้ครบ';
        } elseif ($password && $password !== $confirmPassword) {
            $data['editError'] = 'รหัสผ่านไม่ตรงกัน';
        } else {
            try {
                $params = [$username, $email, $bio, $currentUser['id']];
                $sql = "UPDATE users SET username = ?, email = ?, bio = ? WHERE id = ?";
                if ($password) {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "UPDATE users SET username = ?, email = ?, bio = ?, password = ? WHERE id = ?";
                    $params = [$username, $email, $bio, $hashed, $currentUser['id']];
                }
                $stmt = $this->conn->prepare($sql);
                $stmt->execute($params);
                $_SESSION['username'] = $username;
                $data['currentUser'] = [
                    'id' => $currentUser['id'],
                    'username' => $username,
                    'email' => $email,
                    'bio' => $bio,
                    'role' => $currentUser['role']
                ];
                header("Location: index.php");
                exit;
            } catch (PDOException $e) {
                $data['editError'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
            }
        }
        return $data;
    }
}