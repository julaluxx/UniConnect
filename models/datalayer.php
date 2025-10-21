<?php

require 'models/pdo.php'; // ไฟล์เชื่อมต่อฐานข้อมูล

class DataLayer
{
    private $conn;
    private $dbName = 'ucdb';

    public function __construct($conn)
    {
        if (!$conn instanceof PDO) {
            throw new Exception("ต้องส่งอ็อบเจ็กต์ PDO ที่ถูกต้อง");
        }
        $this->conn = $conn;
    }

    // ------------------------------
    // ดึงข้อมูลจากทุกตารางในฐานข้อมูล + CLEANUP
    // ------------------------------
    public function getAllTablesData()
    {
        try {
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
        } catch (Exception $e) {
            return ['error' => "เกิดข้อผิดพลาด: " . $e->getMessage()];
        }
    }

    // --------------------------------------
    // ✅ ฟังก์ชัน sanitize แต่ละตาราง
    // --------------------------------------

    private function sanitizeUsers($data)
    {
        return array_map(fn($u) => [
            'id' => (int)($u['id'] ?? 0),
            'username' => htmlspecialchars($u['username'] ?? 'ไม่ระบุ'),
            'email' => htmlspecialchars($u['email'] ?? ''),
            'password' => $u['password'] ?? '', // ไม่ควรแสดง password ในผลลัพธ์จริง
            'bio' => htmlspecialchars($u['bio'] ?? ''),
            'role' => $u['role'] ?? 'user',
            'created_at' => $u['created_at'] ?? null,
        ], $data);
    }

    private function sanitizeCategories($data)
    {
        return array_map(fn($c) => [
            'id' => (int)($c['id'] ?? 0),
            'name' => htmlspecialchars($c['name'] ?? 'ไม่ระบุ'),
            'created_at' => $c['created_at'] ?? null,
        ], $data);
    }

    private function sanitizeThreads($data)
    {
        return array_map(fn($t) => [
            'id' => (int)($t['id'] ?? 0),
            'title' => htmlspecialchars($t['title'] ?? 'ไม่ระบุ'),
            'content' => htmlspecialchars($t['content'] ?? ''),
            'category_id' => (int)($t['category_id'] ?? 0),
            'author_id' => (int)($t['author_id'] ?? 0),
            'created_at' => $t['created_at'] ?? null,
            'updated_at' => $t['updated_at'] ?? null,
        ], $data);
    }

    private function sanitizeComments($data)
    {
        return array_map(fn($c) => [
            'id' => (int)($c['id'] ?? 0),
            'content' => htmlspecialchars($c['content'] ?? ''),
            'thread_id' => (int)($c['thread_id'] ?? 0),
            'author_id' => (int)($c['author_id'] ?? 0),
            'created_at' => $c['created_at'] ?? null,
            'updated_at' => $c['updated_at'] ?? null,
        ], $data);
    }

    private function sanitizeLikes($data)
    {
        return array_map(fn($l) => [
            'id' => (int)($l['id'] ?? 0),
            'thread_id' => (int)($l['thread_id'] ?? 0),
            'user_id' => (int)($l['user_id'] ?? 0),
            'created_at' => $l['created_at'] ?? null,
        ], $data);
    }

    private function sanitizeReports($data)
    {
        return array_map(fn($r) => [
            'id' => (int)($r['id'] ?? 0),
            'description' => htmlspecialchars($r['description'] ?? ''),
            'reported_by' => (int)($r['reported_by'] ?? 0),
            'thread_id' => (int)($r['thread_id'] ?? null),
            'status' => $r['status'] ?? 'pending',
            'created_at' => $r['created_at'] ?? null,
            'updated_at' => $r['updated_at'] ?? null,
        ], $data);
    }

    public function searchThreads($keyword)
    {
        try {
            $keyword = "%$keyword%";
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
        } catch (Exception $e) {
            return ['error' => "เกิดข้อผิดพลาดในการค้นหา: " . $e->getMessage()];
        }
    }
}
?>