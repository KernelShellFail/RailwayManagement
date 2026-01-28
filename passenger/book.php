<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_once '../includes/route.php';
require_once '../includes/booking.php';

requireAuth();

$route = new Route();
$booking = new Booking();

$route_id = (int)($_GET['route_id'] ?? 0);
$date = sanitizeInput($_GET['date'] ?? '');

if ($route_id <= 0 || empty($date)) {
    setFlashMessage('Invalid booking request.', 'error');
    redirect('search.php');
}

$route_details = $route->getRouteById($route_id);
if (!$route_details) {
    setFlashMessage('Route not found.', 'error');
    redirect('search.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passenger_name = sanitizeInput($_POST['passenger_name'] ?? '');
    $passenger_age = (int)($_POST['passenger_age'] ?? 0);
    $passenger_gender = sanitizeInput($_POST['passenger_gender'] ?? '');
    $seats_booked = (int)($_POST['seats_booked'] ?? 1);
    $total_price = $route_details['price_per_seat'] * $seats_booked;
    
    $errors = [];
    
    if (empty($passenger_name)) $errors[] = 'Passenger name is required.';
    if ($passenger_age <= 0 || $passenger_age > 120) $errors[] = 'Please enter a valid age.';
    if (!in_array($passenger_gender, ['male', 'female', 'other'])) $errors[] = 'Please select a valid gender.';
    if ($seats_booked < 1 || $seats_booked > 10) $errors[] = 'Number of seats must be between 1 and 10.';
    
    if (empty($errors)) {
        // Check seat availability
        if ($booking->checkSeatAvailability($route_id, $seats_booked, $date)) {
            $user_id = getCurrentUserId();
            
            if ($booking_id = $booking->createBooking($user_id, $route_id, $passenger_name, $passenger_age, $passenger_gender, $seats_booked, $total_price, $date)) {
                setFlashMessage('Booking successful! Your booking ID is ' . $booking_id . '.', 'success');
                redirect('dashboard.php');
            } else {
                setFlashMessage('Booking failed. Please try again.', 'error');
            }
        } else {
            setFlashMessage('Not enough seats available for the requested number.', 'error');
        }
    } else {
        setFlashMessage(implode(' ', $errors), 'error');
    }
}

$duration = calculateDuration($route_details['departure_time'], $route_details['arrival_time']);
$available_seats = $route->getAvailableSeats($route_id, $date);

$content = '
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-ticket-alt mr-2"></i>Book Ticket
    </h1>
    <p class="text-gray-600 mt-2">Complete your booking details</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div>
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">
                <i class="fas fa-route mr-2"></i>Journey Details
            </h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Train:</span>
                    <span class="font-semibold">' . htmlspecialchars($route_details['train_name']) . ' (' . htmlspecialchars($route_details['train_number']) . ')</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Route:</span>
                    <span class="font-semibold">' . htmlspecialchars($route_details['source_station']) . ' → ' . htmlspecialchars($route_details['destination_station']) . '</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Date:</span>
                    <span class="font-semibold">' . formatDate($date) . '</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Time:</span>
                    <span class="font-semibold">' . formatTime($route_details['departure_time']) . ' - ' . formatTime($route_details['arrival_time']) . '</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Duration:</span>
                    <span class="font-semibold">' . $duration . '</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Distance:</span>
                    <span class="font-semibold">' . $route_details['distance_km'] . ' km</span>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">
                <i class="fas fa-chair mr-2"></i>Seat Information
            </h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Price per seat:</span>
                    <span class="font-semibold">' . formatCurrency($route_details['price_per_seat']) . '</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Available seats:</span>
                    <span class="font-semibold text-green-600">' . $available_seats . '</span>
                </div>
            </div>
        </div>
    </div>
    
    <div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">
                <i class="fas fa-user mr-2"></i>Passenger Details
            </h2>
            
            <form method="POST" action="">
                <div class="mb-4">
                    <label for="passenger_name" class="block text-gray-700 text-sm font-bold mb-2">
                        Full Name *
                    </label>
                    <input type="text" id="passenger_name" name="passenger_name" required
                           value="' . htmlspecialchars($_POST['passenger_name'] ?? '') . '"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Enter passenger full name">
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="passenger_age" class="block text-gray-700 text-sm font-bold mb-2">
                            Age *
                        </label>
                        <input type="number" id="passenger_age" name="passenger_age" required min="1" max="120"
                               value="' . htmlspecialchars($_POST['passenger_age'] ?? '') . '"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="Age">
                    </div>
                    
                    <div>
                        <label for="passenger_gender" class="block text-gray-700 text-sm font-bold mb-2">
                            Gender *
                        </label>
                        <select id="passenger_gender" name="passenger_gender" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Gender</option>
                            <option value="male" ' . (($_POST['passenger_gender'] ?? '') === 'male' ? 'selected' : '') . '>Male</option>
                            <option value="female" ' . (($_POST['passenger_gender'] ?? '') === 'female' ? 'selected' : '') . '>Female</option>
                            <option value="other" ' . (($_POST['passenger_gender'] ?? '') === 'other' ? 'selected' : '') . '>Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-6">
                    <label for="seats_booked" class="block text-gray-700 text-sm font-bold mb-2">
                        Number of Seats *
                    </label>
                    <input type="number" id="seats_booked" name="seats_booked" required min="1" max="' . min(10, $available_seats) . '"
                           value="' . htmlspecialchars($_POST['seats_booked'] ?? 1) . '"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Number of seats">
                    <p class="text-sm text-gray-500 mt-1">Maximum ' . min(10, $available_seats) . ' seats available</p>
                </div>
                
                <div class="mb-6 p-4 bg-gray-50 rounded">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold">Total Price:</span>
                        <span class="text-2xl font-bold text-green-600" id="total-price">' . formatCurrency($route_details['price_per_seat'] * (int)($_POST['seats_booked'] ?? 1)) . '</span>
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                    <i class="fas fa-check mr-2"></i>Confirm Booking
                </button>
                
                <a href="search.php" class="block w-full text-center mt-4 bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Search
                </a>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById("seats_booked").addEventListener("input", function() {
    const seats = parseInt(this.value) || 0;
    const pricePerSeat = ' . $route_details['price_per_seat'] . ';
    const totalPrice = seats * pricePerSeat;
    document.getElementById("total-price").textContent = "$" + totalPrice.toFixed(2);
});
</script>';

require_once '../includes/header.php';
?>