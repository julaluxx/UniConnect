<?php
require_once 'models/datalayer.php';

class UserController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function manageUsers() {
        $dataLayer = new DataLayer($this->conn);
        $data = ['allData' => $dataLayer->getAllTablesData()];
        if (isset($data['allData']['error'])) {
            $data['error'] = $data['allData']['error'];
        }
        return $data;
    }

    public function editUser($userId, $username, $email, $role) {
        if ($username && $email) {
            try {
                $stmt = $this->conn->prepare("UPDATE users SET username = ?, email = ?, role = ? WHERE id = ?");
                $stmt->execute([$username, $email, $role, $userId]);
                header("Location: ?action=manage-user");
                exit;
            } catch (PDOException $e) {
                echo "<div class='alert alert-error'>เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        }
    }

    public function deleteUser($userId) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            header("Location: ?action=manage-user");
            exit;
        } catch (PDOException $e) {
            echo "<div class='alert alert-error'>เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}