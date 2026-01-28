<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_once '../includes/booking.php';

requireAuth();

$booking = new Booking();
$booking_id = (int)($_GET['id'] ?? 0);
$user_id = getCurrentUserId();

$booking_details = $booking->getBookingById($booking_id);

if (!$booking_details || $booking_details['user_id'] != $user_id) {
    setFlashMessage('Booking not found.', 'error');
    redirect('history.php');
}

$duration = calculateDuration($booking_details['departure_time'], $booking_details['arrival_time']);
$status_color = $booking_details['booking_status'] === 'confirmed' ? 'green' : 'red';

$content = '
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-ticket-alt mr-2"></i>Ticket Details
    </h1>
    <p class="text-gray-600 mt-2">View your booking information</p>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="border-b pb-4 mb-4">
        <div class="flex justify-between items-start">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Booking Confirmation</h2>
                <div class="text-lg text-gray-600 mt-1">Booking ID: <span class="font-semibold">' . htmlspecialchars($booking_details['booking_id']) . '</span></div>
            </div>
            <span class="bg-' . $status_color . '-100 text-' . $status_color . '-800 font-semibold px-3 py-1 rounded text-lg">
                ' . ucfirst($booking_details['booking_status']) . '
            </span>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-lg font-semibold mb-3 text-gray-800">
                <i class="fas fa-user mr-2"></i>Passenger Information
            </h3>
            <div class="bg-gray-50 p-4 rounded">
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Name:</span>
                        <span class="font-semibold">' . htmlspecialchars($booking_details['passenger_name']) . '</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Age:</span>
                        <span class="font-semibold">' . $booking_details['passenger_age'] . ' years</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Gender:</span>
                        <span class="font-semibold">' . ucfirst($booking_details['passenger_gender']) . '</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Seats Booked:</span>
                        <span class="font-semibold">' . $booking_details['seats_booked'] . '</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div>
            <h3 class="text-lg font-semibold mb-3 text-gray-800">
                <i class="fas fa-train mr-2"></i>Train Information
            </h3>
            <div class="bg-gray-50 p-4 rounded">
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Train Name:</span>
                        <span class="font-semibold">' . htmlspecialchars($booking_details['train_name']) . '</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Train Number:</span>
                        <span class="font-semibold">' . htmlspecialchars($booking_details['train_number']) . '</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Route:</span>
                        <span class="font-semibold">' . htmlspecialchars($booking_details['source_station']) . ' → ' . htmlspecialchars($booking_details['destination_station']) . '</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Distance:</span>
                        <span class="font-semibold">' . $booking_details['distance_km'] . ' km</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div>
            <h3 class="text-lg font-semibold mb-3 text-gray-800">
                <i class="fas fa-calendar mr-2"></i>Journey Details
            </h3>
            <div class="bg-gray-50 p-4 rounded">
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Travel Date:</span>
                        <span class="font-semibold">' . formatDate($booking_details['booking_date']) . '</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Departure:</span>
                        <span class="font-semibold">' . formatTime($booking_details['departure_time']) . '</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Arrival:</span>
                        <span class="font-semibold">' . formatTime($booking_details['arrival_time']) . '</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Duration:</span>
                        <span class="font-semibold">' . $duration . '</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div>
            <h3 class="text-lg font-semibold mb-3 text-gray-800">
                <i class="fas fa-receipt mr-2"></i>Payment Information
            </h3>
            <div class="bg-gray-50 p-4 rounded">
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Price per Seat:</span>
                        <span class="font-semibold">' . formatCurrency($booking_details['price_per_seat']) . '</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Number of Seats:</span>
                        <span class="font-semibold">' . $booking_details['seats_booked'] . '</span>
                    </div>
                    <div class="border-t pt-2 mt-2">
                        <div class="flex justify-between">
                            <span class="text-lg font-semibold">Total Price:</span>
                            <span class="text-2xl font-bold text-green-600">' . formatCurrency($booking_details['total_price']) . '</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-6 p-4 bg-blue-50 rounded">
        <h4 class="font-semibold mb-2 text-blue-800">
            <i class="fas fa-info-circle mr-2"></i>Important Information
        </h4>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• Please arrive at the station 30 minutes before departure</li>
            <li>• Carry a valid ID proof for verification</li>
            <li>• This ticket is non-transferable</li>
            <li>• For cancellations, please refer to our cancellation policy</li>
        </ul>
    </div>
    
    <div class="mt-6 flex justify-between">
        <div>
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                <i class="fas fa-print mr-2"></i>Print Ticket
            </button>
        </div>
        <div class="flex space-x-3">
            <a href="history.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                <i class="fas fa-arrow-left mr-2"></i>Back to History
            </a>';
            
            if ($booking_details['booking_status'] === 'confirmed') {
                $content .= '
                <a href="cancel.php?id=' . $booking_details['id'] . '" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-300" onclick="return confirm(\'Are you sure you want to cancel this booking?\')">
                    <i class="fas fa-times mr-2"></i>Cancel Booking
                </a>';
            }
            
$content .= '
        </div>
    </div>
</div>';

require_once '../includes/header.php';
?>