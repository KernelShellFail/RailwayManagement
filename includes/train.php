<?php
require_once __DIR__ . '/database.php';

class Train {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllTrains() {
        try {
            $stmt = $this->db->prepare("SELECT * FROM trains ORDER BY train_number");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Get trains error: " . $e->getMessage());
            return [];
        }
    }

    public function getTrainById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM trains WHERE id = ?");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Get train error: " . $e->getMessage());
            return false;
        }
    }

    public function createTrain($train_number, $train_name, $total_seats) {
        try {
            $stmt = $this->db->prepare("INSERT INTO trains (train_number, train_name, total_seats, available_seats) VALUES (?, ?, ?, ?)");
            return $stmt->execute([$train_number, $train_name, $total_seats, $total_seats]);
        } catch(PDOException $e) {
            error_log("Create train error: " . $e->getMessage());
            return false;
        }
    }

    public function updateTrain($id, $train_number, $train_name, $total_seats, $status) {
        try {
            $stmt = $this->db->prepare("UPDATE trains SET train_number = ?, train_name = ?, total_seats = ?, status = ?, available_seats = ? WHERE id = ?");
            $train = $this->getTrainById($id);
            $booked_seats = $train ? ($train['total_seats'] - $train['available_seats']) : 0;
            $new_available_seats = $total_seats - $booked_seats;
            
            return $stmt->execute([$train_number, $train_name, $total_seats, $status, $new_available_seats, $id]);
        } catch(PDOException $e) {
            error_log("Update train error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteTrain($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM trains WHERE id = ?");
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            error_log("Delete train error: " . $e->getMessage());
            return false;
        }
    }

    public function updateAvailableSeats($train_id, $seats_change) {
        try {
            $stmt = $this->db->prepare("UPDATE trains SET available_seats = available_seats + ? WHERE id = ?");
            return $stmt->execute([$seats_change, $train_id]);
        } catch(PDOException $e) {
            error_log("Update seats error: " . $e->getMessage());
            return false;
        }
    }
}
?>