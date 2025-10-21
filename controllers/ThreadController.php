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
        $data['allData'] = $this->dataLayer->getAllTablesData();
        if (isset($data['allData']['error'])) {
            $data['error'] = $data['allData']['error'];
            $data['filteredThreads'] = [];
            return $data;
        }
        $data['threads'] = $data['allData']['threads'] ?? [];
        $data['filteredThreads'] = $searchQuery ? $this->dataLayer->searchThreads($searchQuery) : $data['threads'];
        if (isset($data['filteredThreads']['error']) || !is_array($data['filteredThreads'])) {
            $data['filteredThreads'] = [];
        }
        return $data;
    }

    public function threadDetail($threadId, $currentUser) {
        $data = [];
        $data['allData'] = $this->dataLayer->getAllTablesData();
        if (isset($data['allData']['error'])) {
            $data['error'] = $data['allData']['error'];
            return $data;
        }
        $data['threadId'] = $threadId;
        $data['thread'] = null;
        foreach ($data['allData']['threads'] ?? [] as $thread) {
            if ($thread['id'] == $threadId) {
                $data['thread'] = $thread;
                break;
            }
        }
        if (!$data['thread']) {
            $data['error'] = 'ไม่พบกระทู้นี้';
        }
        $data['likeCount'] = count(array_filter($data['allData']['likes'] ?? [], fn($l) => $l['thread_id'] == $threadId));
        $data['hasLiked'] = false;
        foreach ($data['allData']['likes'] ?? [] as $like) {
            if ($like['thread_id'] == $threadId && $like['user_id'] == $currentUser['id']) {
                $data['hasLiked'] = true;
                break;
            }
        }
        $data['threadComments'] = array_filter($data['allData']['comments'] ?? [], fn($c) => $c['thread_id'] == $threadId);
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
        $data['allData'] = $this->dataLayer->getAllTablesData();
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
        $data['allData'] = $this->dataLayer->getAllTablesData();
        if (isset($data['allData']['error'])) {
            $data['error'] = $data['allData']['error'];
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