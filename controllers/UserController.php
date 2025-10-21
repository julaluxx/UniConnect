<?php
require_once 'models/datalayer.php';

class UserController {
    private $conn;
    private $dataLayer;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->dataLayer = new DataLayer($conn);
    }

    public function manageUsers() {
        $data = [];
        $data['users'] = $this->dataLayer->getUsers();
        if (isset($data['users']['error'])) {
            $data['error'] = $data['users']['error'];
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