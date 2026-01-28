<?php
require_once __DIR__ . '/database.php';

class Route {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllRoutes() {
        try {
            $stmt = $this->db->prepare("
                SELECT r.*, t.train_name, t.train_number 
                FROM routes r 
                JOIN trains t ON r.train_id = t.id 
                ORDER BY r.source_station, r.destination_station
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Get routes error: " . $e->getMessage());
            return [];
        }
    }

    public function getRouteById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT r.*, t.train_name, t.train_number 
                FROM routes r 
                JOIN trains t ON r.train_id = t.id 
                WHERE r.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Get route error: " . $e->getMessage());
            return false;
        }
    }

    public function searchRoutes($source, $destination, $date = null) {
        try {
            $sql = "
                SELECT r.*, t.train_name, t.train_number, t.available_seats 
                FROM routes r 
                JOIN trains t ON r.train_id = t.id 
                WHERE r.source_station = ? AND r.destination_station = ? 
                AND r.status = 'active' AND t.status = 'active'
            ";
            
            $params = [$source, $destination];
            
            if ($date) {
                // Check if there are confirmed bookings for this date
                $sql .= " AND (SELECT COUNT(*) FROM bookings b WHERE b.route_id = r.id AND b.booking_date = ? AND b.booking_status = 'confirmed') < t.total_seats";
                $params[] = $date;
            }
            
            $sql .= " ORDER BY r.departure_time";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Search routes error: " . $e->getMessage());
            return [];
        }
    }

    public function createRoute($train_id, $source_station, $destination_station, $distance_km, $departure_time, $arrival_time, $price_per_seat) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO routes (train_id, source_station, destination_station, distance_km, departure_time, arrival_time, price_per_seat) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            return $stmt->execute([$train_id, $source_station, $destination_station, $distance_km, $departure_time, $arrival_time, $price_per_seat]);
        } catch(PDOException $e) {
            error_log("Create route error: " . $e->getMessage());
            return false;
        }
    }

    public function updateRoute($id, $train_id, $source_station, $destination_station, $distance_km, $departure_time, $arrival_time, $price_per_seat, $status) {
        try {
            $stmt = $this->db->prepare("
                UPDATE routes SET 
                train_id = ?, source_station = ?, destination_station = ?, 
                distance_km = ?, departure_time = ?, arrival_time = ?, 
                price_per_seat = ?, status = ? 
                WHERE id = ?
            ");
            return $stmt->execute([$train_id, $source_station, $destination_station, $distance_km, $departure_time, $arrival_time, $price_per_seat, $status, $id]);
        } catch(PDOException $e) {
            error_log("Update route error: " . $e->getMessage());
            return false;
        }
    }

    public function deleteRoute($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM routes WHERE id = ?");
            return $stmt->execute([$id]);
        } catch(PDOException $e) {
            error_log("Delete route error: " . $e->getMessage());
            return false;
        }
    }

    public function getAvailableSeats($route_id, $date) {
        try {
            $route = $this->getRouteById($route_id);
            if (!$route) return 0;

            $stmt = $this->db->prepare("
                SELECT SUM(seats_booked) as booked_seats 
                FROM bookings 
                WHERE route_id = ? AND booking_date = ? AND booking_status = 'confirmed'
            ");
            $stmt->execute([$route_id, $date]);
            $result = $stmt->fetch();
            
            $booked_seats = $result['booked_seats'] ?? 0;
            return $route['total_seats'] - $booked_seats;
        } catch(PDOException $e) {
            error_log("Get available seats error: " . $e->getMessage());
            return 0;
        }
    }
}
?>