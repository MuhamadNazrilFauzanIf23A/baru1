<?php
class Mobil {
    private $conn;

    public function __construct($dbConn) {
        $this->conn = $dbConn;
    }

    public function getListMobil($filter = 'Semua') {
        $query = "SELECT * FROM list_mobil";

        if ($filter === 'Premium') {
            $query .= " WHERE is_premium = 'ya'";
        } elseif ($filter === 'Biasa') {
            $query .= " WHERE is_premium = 'tidak'";
        }

        $stmt = $this->conn->prepare($query);
        if ($stmt) {
            $stmt->execute();
            return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        }
        return [];
    }
}
?>
