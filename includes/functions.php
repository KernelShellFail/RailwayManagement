<?php
// Utility functions for the Railway Reservation System

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePhone($phone) {
    return preg_match('/^[0-9+\-\s()]+$/', $phone);
}

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function generateBookingId() {
    return 'BK' . date('Y') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}

function formatCurrency($amount) {
    return '$' . number_format($amount, 2);
}

function formatTime($time) {
    return date('h:i A', strtotime($time));
}

function formatDate($date) {
    return date('M d, Y', strtotime($date));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function setFlashMessage($message, $type = 'success') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type;
}

function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function isAdmin() {
    return isLoggedIn() && $_SESSION['user_type'] === 'admin';
}

function isPassenger() {
    return isLoggedIn() && $_SESSION['user_type'] === 'passenger';
}

function requireAuth() {
    if (!isLoggedIn()) {
        setFlashMessage('Please login to access this page.', 'error');
        redirect('login.php');
    }
}

function requireAdmin() {
    requireAuth();
    if (!isAdmin()) {
        setFlashMessage('Access denied. Admin privileges required.', 'error');
        redirect('index.php');
    }
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function timeToMinutes($time) {
    $parts = explode(':', $time);
    return ($parts[0] * 60) + $parts[1];
}

function calculateDuration($departure, $arrival) {
    $dep = timeToMinutes($departure);
    $arr = timeToMinutes($arrival);
    
    if ($arr < $dep) {
        $arr += 24 * 60; // Next day arrival
    }
    
    $duration = $arr - $dep;
    $hours = floor($duration / 60);
    $minutes = $duration % 60;
    
    return sprintf("%dh %dm", $hours, $minutes);
}

// CSRF Protection
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>