<?php
session_start();
require 'pdo.php';

class DataLayer {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    // ดึงข้อมูลเธรดทั้งหมด
    public function getThreads() {
        $stmt = $this->conn->query("
            SELECT t.*, c.name AS category_name, u.username 
            FROM threads t
            JOIN categories c ON t.category_id = c.id
            JOIN users u ON t.author_id = u.id
            ORDER BY t.created_at DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ดึงข้อมูลผู้ใช้
    public function getUserData($userId) {
        $stmt = $this->conn->prepare("
            SELECT username, bio, profile_image 
            FROM users 
            WHERE id = ?
        ");
        $stmt->execute([$userId]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_data) {
            return [
                'username' => htmlspecialchars($user_data['username']),
                'bio' => htmlspecialchars($user_data['bio']),
                'profile_image' => htmlspecialchars($user_data['profile_image'])
            ];
        }
        return [
            'username' => 'Guest',
            'bio' => 'No bio available',
            'profile_image' => './assets/square_holder.png'
        ];
    }

    // ดึงข้อมูลหมวดหมู่
    public function getCategories() {
        $stmt = $this->conn->query("
            SELECT id, name 
            FROM categories 
            ORDER BY name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ดึงข้อมูลสถิติ
    public function getStatistics() {
        $users_count = $this->conn->query("SELECT COUNT(id) FROM users")->fetchColumn();
        $thread_count = $this->conn->query("SELECT COUNT(id) FROM threads")->fetchColumn();
        $comment_count = $this->conn->query("SELECT COUNT(id) FROM comments")->fetchColumn();

        return [
            'users_count' => $users_count,
            'thread_count' => $thread_count,
            'comment_count' => $comment_count
        ];
    }

    // ฟังก์ชันรวมข้อมูลทั้งหมด
    public function getAllData($userId) {
        return [
            'threads' => $this->getThreads(),
            'user' => $this->getUserData($userId),
            'categories' => $this->getCategories(),
            'statistics' => $this->getStatistics()
        ];
    }
}
?>