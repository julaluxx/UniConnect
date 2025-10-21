<?php
require_once 'pdo.php'; // ใช้ require_once และสมมติ path ถูกต้อง

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

    // ดึงข้อมูลผู้ใช้ทั้งหมด
    public function getUsers()
    {
        try {
            $stmt = $this->conn->query("SELECT * FROM `$this->dbName`.`users`");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->sanitizeUsers($rows);
        } catch (Exception $e) {
            return ['error' => "เกิดข้อผิดพลาดในการดึงข้อมูลผู้ใช้: " . $e->getMessage()];
        }
    }

    // ดึงข้อมูลหมวดหมู่ทั้งหมด
    public function getCategories()
    {
        try {
            $stmt = $this->conn->query("SELECT * FROM `$this->dbName`.`categories`");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->sanitizeCategories($rows);
        } catch (Exception $e) {
            return ['error' => "เกิดข้อผิดพลาดในการดึงข้อมูลหมวดหมู่: " . $e->getMessage()];
        }
    }

    // ดึงข้อมูลกระทู้ทั้งหมด
    public function getThreads()
    {
        try {
            $stmt = $this->conn->query("SELECT * FROM `$this->dbName`.`threads`");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->sanitizeThreads($rows);
        } catch (Exception $e) {
            return ['error' => "เกิดข้อผิดพลาดในการดึงข้อมูลกระทู้: " . $e->getMessage()];
        }
    }

    // ดึงข้อมูลกระทู้ตาม ID
    public function getThreadById($threadId)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM `$this->dbName`.`threads` WHERE id = ?");
            $stmt->execute([$threadId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $this->sanitizeThreads([$row])[0] : null;
        } catch (Exception $e) {
            return ['error' => "เกิดข้อผิดพลาดในการดึงข้อมูลกระทู้: " . $e->getMessage()];
        }
    }

    // ดึงความคิดเห็นตาม thread_id
    public function getCommentsByThreadId($threadId)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM `$this->dbName`.`comments` WHERE thread_id = ?");
            $stmt->execute([$threadId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->sanitizeComments($rows);
        } catch (Exception $e) {
            return ['error' => "เกิดข้อผิดพลาดในการดึงความคิดเห็น: " . $e->getMessage()];
        }
    }

    // ดึงข้อมูลไลค์ตาม thread_id
    public function getLikesByThreadId($threadId)
    {
        try {
            $stmt = $this->conn->prepare("SELECT * FROM `$this->dbName`.`likes` WHERE thread_id = ?");
            $stmt->execute([$threadId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->sanitizeLikes($rows);
        } catch (Exception $e) {
            return ['error' => "เกิดข้อผิดพลาดในการดึงข้อมูลไลค์: " . $e->getMessage()];
        }
    }

    // ดึงข้อมูลรายงานทั้งหมด (สำหรับแอดมิน)
    public function getReports()
    {
        try {
            $stmt = $this->conn->query("SELECT * FROM `$this->dbName`.`reports`");
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->sanitizeReports($rows);
        } catch (Exception $e) {
            return ['error' => "เกิดข้อผิดพลาดในการดึงข้อมูลรายงาน: " . $e->getMessage()];
        }
    }

    // ค้นหากระทู้
    public function searchThreads($keyword)
    {
        try {
            $keyword = "%$keyword%";
            $stmt = $this->conn->prepare("
                SELECT t.*, u.username AS author_name, c.name AS category_name
                FROM `$this->dbName`.`threads` t
                LEFT JOIN `$this->dbName`.`users` u ON t.author_id = u.id
                LEFT JOIN `$this->dbName`.`categories` c ON t.category_id = c.id
                WHERE t.title LIKE ? OR t.content LIKE ?
                ORDER BY t.created_at DESC
            ");
            $stmt->execute([$keyword, $keyword]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->sanitizeThreads($rows);
        } catch (Exception $e) {
            return ['error' => "เกิดข้อผิดพลาดในการค้นหา: " . $e->getMessage()];
        }
    }

    // --------------------------------------
    // ฟังก์ชัน sanitize แต่ละตาราง
    // --------------------------------------
    private function sanitizeUsers($data)
    {
        return array_map(fn($u) => [
            'id' => (int)($u['id'] ?? 0),
            'username' => htmlspecialchars($u['username'] ?? 'ไม่ระบุ'),
            'email' => htmlspecialchars($u['email'] ?? ''),
            'password' => $u['password'] ?? '',
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
            'author_name' => isset($t['author_name']) ? htmlspecialchars($t['author_name']) : null,
            'category_name' => isset($t['category_name']) ? htmlspecialchars($t['category_name']) : null,
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
            'thread_id' => isset($r['thread_id']) ? (int)$r['thread_id'] : null,
            'comment_id' => isset($r['comment_id']) ? (int)$r['comment_id'] : null,
            'status' => $r['status'] ?? 'pending',
            'created_at' => $r['created_at'] ?? null,
            'updated_at' => $r['updated_at'] ?? null,
        ], $data);
    }
}
?>