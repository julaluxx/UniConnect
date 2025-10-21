<?php
require_once 'models/datalayer.php';

class ThreadController {
    private $dataLayer;
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->dataLayer = new DataLayer($conn);
    }

    public function listThreads($searchQuery) {
        $data = [];
        $data['threads'] = $this->dataLayer->getThreads();
        if (isset($data['threads']['error'])) {
            $data['error'] = $data['threads']['error'];
            $data['filteredThreads'] = [];
            return $data;
        }
        $data['users'] = $this->dataLayer->getUsers();
        if (isset($data['users']['error'])) {
            $data['error'] = $data['users']['error'];
            $data['filteredThreads'] = [];
            return $data;
        }
        $data['categories'] = $this->dataLayer->getCategories();
        if (isset($data['categories']['error'])) {
            $data['error'] = $data['categories']['error'];
            $data['filteredThreads'] = [];
            return $data;
        }
        $data['filteredThreads'] = $searchQuery ? $this->dataLayer->searchThreads($searchQuery) : $data['threads'];
        if (isset($data['filteredThreads']['error']) || !is_array($data['filteredThreads'])) {
            $data['filteredThreads'] = [];
        }
        return $data;
    }

    public function threadDetail($threadId, $currentUser) {
        $data = [];
        $data['thread'] = $this->dataLayer->getThreadById($threadId);
        if (isset($data['thread']['error']) || !$data['thread']) {
            $data['error'] = isset($data['thread']['error']) ? $data['thread']['error'] : 'ไม่พบกระทู้นี้';
            return $data;
        }
        $data['threadId'] = $threadId;
        $data['users'] = $this->dataLayer->getUsers();
        if (isset($data['users']['error'])) {
            $data['error'] = $data['users']['error'];
            return $data;
        }
        $data['categories'] = $this->dataLayer->getCategories();
        if (isset($data['categories']['error'])) {
            $data['error'] = $data['categories']['error'];
            return $data;
        }
        $data['threadComments'] = $this->dataLayer->getCommentsByThreadId($threadId);
        if (isset($data['threadComments']['error'])) {
            $data['error'] = $data['threadComments']['error'];
            return $data;
        }
        $data['likes'] = $this->dataLayer->getLikesByThreadId($threadId);
        if (isset($data['likes']['error'])) {
            $data['error'] = $data['likes']['error'];
            return $data;
        }
        $data['likeCount'] = count($data['likes']);
        $data['hasLiked'] = false;
        foreach ($data['likes'] as $like) {
            if ($like['thread_id'] == $threadId && $like['user_id'] == $currentUser['id']) {
                $data['hasLiked'] = true;
                break;
            }
        }
        return $data;
    }

    public function createThread($currentUser, $title, $categoryId, $content) {
        $data = ['threadError' => ''];
        if (!$title || !$categoryId || !$content) {
            $data['threadError'] = 'กรุณากรอกข้อมูลให้ครบทุกช่อง';
        } else {
            try {
                $stmt = $this->conn->prepare("INSERT INTO threads (title, category_id, author_id, content, created_at) VALUES (?, ?, ?, ?, NOW())");
                $stmt->execute([$title, $categoryId, $currentUser['id'], $content]);
                header("Location: index.php");
                exit;
            } catch (PDOException $e) {
                $data['threadError'] = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
            }
        }
        $data['categories'] = $this->dataLayer->getCategories();
        return $data;
    }

    public function addComment($threadId, $currentUser, $content) {
        try {
            if ($content) {
                $stmt = $this->conn->prepare("INSERT INTO comments (thread_id, author_id, content, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$threadId, $currentUser['id'], $content]);
                header("Location: ?thread=$threadId");
                exit;
            }
        } catch (PDOException $e) {
            echo "<div class='alert alert-error'>เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    public function likeToggle($threadId, $currentUser) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM likes WHERE thread_id = ? AND user_id = ?");
            $stmt->execute([$threadId, $currentUser['id']]);
            $hasLiked = $stmt->fetchColumn() > 0;
            if ($hasLiked) {
                $stmt = $this->conn->prepare("DELETE FROM likes WHERE thread_id = ? AND user_id = ?");
                $stmt->execute([$threadId, $currentUser['id']]);
            } else {
                $stmt = $this->conn->prepare("INSERT INTO likes (thread_id, user_id, created_at) VALUES (?, ?, NOW())");
                $stmt->execute([$threadId, $currentUser['id']]);
            }
            header("Location: ?thread=$threadId");
            exit;
        } catch (PDOException $e) {
            echo "<div class='alert alert-error'>เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    public function reportThread($threadId, $currentUser, $description) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM reports WHERE thread_id = ? AND reported_by = ?");
            $stmt->execute([$threadId, $currentUser['id']]);
            if ($stmt->fetchColumn() == 0) {
                $stmt = $this->conn->prepare("INSERT INTO reports (thread_id, reported_by, description, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$threadId, $currentUser['id'], $description]);
            }
            header("Location: ?thread=$threadId");
            exit;
        } catch (PDOException $e) {
            echo "<div class='alert alert-error'>เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    public function reportComment($commentId, $threadId, $currentUser, $description) {
        try {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM reports WHERE comment_id = ? AND reported_by = ?");
            $stmt->execute([$commentId, $currentUser['id']]);
            if ($stmt->fetchColumn() == 0) {
                $stmt = $this->conn->prepare("INSERT INTO reports (comment_id, reported_by, description, created_at) VALUES (?, ?, ?, NOW())");
                $stmt->execute([$commentId, $currentUser['id'], $description]);
            }
            header("Location: ?thread=$threadId");
            exit;
        } catch (PDOException $e) {
            echo "<div class='alert alert-error'>เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }

    public function manageThreads($currentUser) {
        $data = [];
        $data['threads'] = $this->dataLayer->getThreads();
        if (isset($data['threads']['error'])) {
            $data['error'] = $data['threads']['error'];
        }
        $data['users'] = $this->dataLayer->getUsers();
        if (isset($data['users']['error'])) {
            $data['error'] = $data['users']['error'];
        }
        $data['categories'] = $this->dataLayer->getCategories();
        if (isset($data['categories']['error'])) {
            $data['error'] = $data['categories']['error'];
        }
        $data['reports'] = $this->dataLayer->getReports();
        if (isset($data['reports']['error'])) {
            $data['error'] = $data['reports']['error'];
        }
        return $data;
    }

    public function deleteThread($threadId, $currentUser) {
        try {
            $stmt = $this->conn->prepare("DELETE FROM threads WHERE id = ?");
            $stmt->execute([$threadId]);
            header("Location: ?action=manage-thread");
            exit;
        } catch (PDOException $e) {
            echo "<div class='alert alert-error'>เกิดข้อผิดพลาด: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    }
}