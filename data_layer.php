<?php
session_start();
require 'pdo.php'; // ไฟล์เชื่อมต่อฐานข้อมูล

class DataLayer
{
    private $conn;
    private $dbName = 'uniconnect_db';

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // ------------------------------
    // ดึงข้อมูลจากทุกตารางในฐานข้อมูล + CLEANUP
    // ------------------------------
    public function getAllTablesData()
    {
        $stmt = $this->conn->prepare("SHOW TABLES FROM `$this->dbName`");
        $stmt->execute();
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $allData = [];

        foreach ($tables as $table) {
            $query = "SELECT * FROM `$this->dbName`.`$table`";
            $stmt2 = $this->conn->query($query);
            $rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            $allData[$table] = $rows;
        }

        // ✅ CLEANUP / SANITIZE DATA
        $allData['users'] = $this->sanitizeUsers($allData['users'] ?? []);
        $allData['categories'] = $this->sanitizeCategories($allData['categories'] ?? []);
        $allData['threads'] = $this->sanitizeThreads($allData['threads'] ?? []);
        $allData['comments'] = $this->sanitizeComments($allData['comments'] ?? []);
        $allData['likes'] = $this->sanitizeLikes($allData['likes'] ?? []);
        $allData['reports'] = $this->sanitizeReports($allData['reports'] ?? []);

        return $allData;
    }

    // --------------------------------------
    // ✅ ฟังก์ชัน sanitize แต่ละตาราง
    // --------------------------------------

    private function sanitizeUsers($data)
    {
        return array_map(fn($u) => [
            'id' => $u['id'] ?? 0,
            'username' => $u['username'] ?? 'ไม่ระบุ',
            'email' => $u['email'] ?? '',
            'password' => $u['password'] ?? '',
            'bio' => $u['bio'] ?? '',
            'role' => $u['role'] ?? 'user',
            'created_at' => $u['created_at'] ?? null,
        ], $data);
    }

    private function sanitizeCategories($data)
    {
        return array_map(fn($c) => [
            'id' => $c['id'] ?? 0,
            'name' => $c['name'] ?? 'ไม่ระบุ',
            'created_at' => $c['created_at'] ?? null,
        ], $data);
    }

    private function sanitizeThreads($data)
    {
        return array_map(fn($t) => [
            'id' => $t['id'] ?? 0,
            'title' => $t['title'] ?? 'ไม่ระบุ',
            'content' => $t['content'] ?? '',
            'category_id' => $t['category_id'] ?? 0,
            'author_id' => $t['author_id'] ?? 0,
            'created_at' => $t['created_at'] ?? null,
            'updated_at' => $t['updated_at'] ?? null,
        ], $data);
    }

    private function sanitizeComments($data)
    {
        return array_map(fn($c) => [
            'id' => $c['id'] ?? 0,
            'content' => $c['content'] ?? '',
            'thread_id' => $c['thread_id'] ?? 0,
            'author_id' => $c['author_id'] ?? 0,
            'created_at' => $c['created_at'] ?? null,
            'updated_at' => $c['updated_at'] ?? null,
        ], $data);
    }

    private function sanitizeLikes($data)
    {
        return array_map(fn($l) => [
            'id' => $l['id'] ?? 0,
            'thread_id' => $l['thread_id'] ?? 0,
            'user_id' => $l['user_id'] ?? 0,
            'created_at' => $l['created_at'] ?? null,
        ], $data);
    }

    private function sanitizeReports($data)
    {
        return array_map(fn($r) => [
            'id' => $r['id'] ?? 0,
            'description' => $r['description'] ?? '',
            'reported_by' => $r['reported_by'] ?? 0,
            'thread_id' => $r['thread_id'] ?? null,
            'status' => $r['status'] ?? 'pending',
            'created_at' => $r['created_at'] ?? null,
            'updated_at' => $r['updated_at'] ?? null,
        ], $data);
    }

    public function searchThreads($keyword)
    {
        $keyword = "%$keyword%"; // เตรียม LIKE pattern
        $stmt = $this->conn->prepare("
        SELECT t.*, u.username AS author_name, c.name AS category_name
        FROM threads t
        LEFT JOIN users u ON t.author_id = u.id
        LEFT JOIN categories c ON t.category_id = c.id
        WHERE t.title LIKE ? OR t.content LIKE ?
        ORDER BY t.created_at DESC
        ");
        $stmt->execute([$keyword, $keyword]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>