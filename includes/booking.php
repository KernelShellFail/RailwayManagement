<?php
require_once __DIR__ . '/database.php';

class Booking {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function createBooking($user_id, $route_id, $passenger_name, $passenger_age, $passenger_gender, $seats_booked, $total_price, $booking_date) {
        try {
            // Generate unique booking ID
            $booking_id = 'BK' . date('Y') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            
            $stmt = $this->db->prepare("
                INSERT INTO bookings (booking_id, user_id, route_id, passenger_name, passenger_age, passenger_gender, seats_booked, total_price, booking_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([$booking_id, $user_id, $route_id, $passenger_name, $passenger_age, $passenger_gender, $seats_booked, $total_price, $booking_date]);
            
            if ($result) {
                return $this->db->lastInsertId();
            }
            return false;
        } catch(PDOException $e) {
            error_log("Create booking error: " . $e->getMessage());
            return false;
        }
    }

    public function getUserBookings($user_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT b.*, r.source_station, r.destination_station, r.departure_time, r.arrival_time,
                       t.train_name, t.train_number
                FROM bookings b
                JOIN routes r ON b.route_id = r.id
                JOIN trains t ON r.train_id = t.id
                WHERE b.user_id = ?
                ORDER BY b.created_at DESC
            ");
            $stmt->execute([$user_id]);
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Get user bookings error: " . $e->getMessage());
            return [];
        }
    }

    public function getAllBookings() {
        try {
            $stmt = $this->db->prepare("
                SELECT b.*, u.username, u.email, r.source_station, r.destination_station, r.departure_time, r.arrival_time,
                       t.train_name, t.train_number
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                JOIN routes r ON b.route_id = r.id
                JOIN trains t ON r.train_id = t.id
                ORDER BY b.created_at DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll();
        } catch(PDOException $e) {
            error_log("Get all bookings error: " . $e->getMessage());
            return [];
        }
    }

    public function getBookingById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT b.*, u.username, u.email, r.source_station, r.destination_station, r.departure_time, r.arrival_time, r.price_per_seat,
                       t.train_name, t.train_number
                FROM bookings b
                JOIN users u ON b.user_id = u.id
                JOIN routes r ON b.route_id = r.id
                JOIN trains t ON r.train_id = t.id
                WHERE b.id = ?
            ");
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch(PDOException $e) {
            error_log("Get booking error: " . $e->getMessage());
            return false;
        }
    }

    public function cancelBooking($booking_id) {
        try {
            // Get booking details
            $booking = $this->getBookingById($booking_id);
            if (!$booking || $booking['booking_status'] === 'cancelled') {
                return false;
            }

            // Start transaction
            $this->db->connect()->beginTransaction();

            // Update booking status
            $stmt = $this->db->prepare("UPDATE bookings SET booking_status = 'cancelled' WHERE id = ?");
            $stmt->execute([$booking_id]);

            // Update available seats in train
            $train_stmt = $this->db->prepare("UPDATE trains SET available_seats = available_seats + ? WHERE id = (SELECT train_id FROM routes WHERE id = ?)");
            $train_stmt->execute([$booking['seats_booked'], $booking['route_id']]);

            $this->db->connect()->commit();
            return true;
        } catch(PDOException $e) {
            $this->db->connect()->rollBack();
            error_log("Cancel booking error: " . $e->getMessage());
            return false;
        }
    }

    public function checkSeatAvailability($route_id, $seats_needed, $date) {
        try {
            $route_stmt = $this->db->prepare("SELECT * FROM routes WHERE id = ?");
            $route_stmt->execute([$route_id]);
            $route = $route_stmt->fetch();

            if (!$route) return false;

            $booking_stmt = $this->db->prepare("
                SELECT SUM(seats_booked) as total_booked 
                FROM bookings 
                WHERE route_id = ? AND booking_date = ? AND booking_status = 'confirmed'
            ");
            $booking_stmt->execute([$route_id, $date]);
            $booked = $booking_stmt->fetch();

            $total_booked = $booked['total_booked'] ?? 0;
            $available_seats = $route['total_seats'] - $total_booked;

            return $available_seats >= $seats_needed;
        } catch(PDOException $e) {
            error_log("Check seat availability error: " . $e->getMessage());
            return false;
        }
    }
}
?>