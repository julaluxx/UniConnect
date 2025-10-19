<?php
// data_layer.php
session_start();
require 'pdo.php'; // ไฟล์เชื่อมต่อฐานข้อมูล

class DataLayer
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    // ------------------------------
    // ดึงข้อมูลจากทุกตารางในฐานข้อมูล
    // ------------------------------
    public function getAllTablesData()
    {
        $dbName = 'uniconnect_db';

        // ดึงชื่อทุกตารางในฐานข้อมูล
        $stmt = $this->conn->prepare("SHOW TABLES FROM `$dbName`");
        $stmt->execute();
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $allData = [];

        foreach ($tables as $table) {
            $query = "SELECT * FROM `$dbName`.`$table`";
            $stmt2 = $this->conn->query($query);
            $rows = $stmt2->fetchAll(PDO::FETCH_ASSOC);
            $allData[$table] = $rows;
        }

        return $allData;
    }
}

?>
