<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_once '../includes/booking.php';

requireAuth();

$booking = new Booking();
$user_id = getCurrentUserId();
$user_bookings = $booking->getUserBookings($user_id);

$content = '
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-history mr-2"></i>Booking History
    </h1>
    <p class="text-gray-600 mt-2">View all your past and current bookings</p>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">All Bookings</h2>
        <div class="text-sm text-gray-600">
            Total Bookings: ' . count($user_bookings) . '
        </div>
    </div>';

if (!empty($user_bookings)) {
    $content .= '
    <div class="space-y-4">';
    
    foreach ($user_bookings as $b) {
        $status_color = $b['booking_status'] === 'confirmed' ? 'green' : 'red';
        $duration = calculateDuration($b['departure_time'], $b['arrival_time']);
        
        $content .= '
        <div class="border rounded-lg p-4 hover:shadow-lg transition duration-300">
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <div>
                    <div class="text-sm text-gray-500">Booking ID</div>
                    <div class="font-semibold text-lg">' . htmlspecialchars($b['booking_id']) . '</div>
                    <div class="text-sm text-gray-500">Booked on ' . date('M j, Y', strtotime($b['created_at'])) . '</div>
                </div>
                
                <div>
                    <div class="text-sm text-gray-500">Train & Route</div>
                    <div class="font-semibold">' . htmlspecialchars($b['train_name']) . '</div>
                    <div class="text-sm">' . htmlspecialchars($b['source_station']) . ' → ' . htmlspecialchars($b['destination_station']) . '</div>
                    <div class="text-sm text-gray-500">' . $b['distance_km'] . ' km</div>
                </div>
                
                <div>
                    <div class="text-sm text-gray-500">Schedule</div>
                    <div class="font-semibold">' . formatDate($b['booking_date']) . '</div>
                    <div class="text-sm">' . formatTime($b['departure_time']) . ' - ' . formatTime($b['arrival_time']) . '</div>
                    <div class="text-sm text-gray-500">' . $duration . '</div>
                </div>
                
                <div>
                    <div class="text-sm text-gray-500">Details</div>
                    <div class="font-semibold">' . htmlspecialchars($b['passenger_name']) . '</div>
                    <div class="text-sm">' . $b['passenger_age'] . ' years, ' . ucfirst($b['passenger_gender']) . '</div>
                    <div class="text-sm">' . $b['seats_booked'] . ' seat(s)</div>
                    <div class="text-lg font-bold text-green-600">' . formatCurrency($b['total_price']) . '</div>
                    <span class="bg-' . $status_color . '-100 text-' . $status_color . '-800 font-semibold px-2 py-1 rounded text-sm">
                        ' . ucfirst($b['booking_status']) . '
                    </span>
                </div>
            </div>
            
            <div class="mt-4 pt-4 border-t flex justify-between items-center">
                <div class="flex space-x-4">
                    <a href="ticket.php?id=' . $b['id'] . '" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm transition duration-300">
                        <i class="fas fa-eye mr-2"></i>View Ticket
                    </a>';
                    
                    if ($b['booking_status'] === 'confirmed') {
                        $content .= '
                        <a href="cancel.php?id=' . $b['id'] . '" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition duration-300" onclick="return confirm(\'Are you sure you want to cancel this booking?\')">
                            <i class="fas fa-times mr-2"></i>Cancel Booking
                        </a>';
                    }
        
        $content .= '
                </div>
            </div>
        </div>';
    }
    
    $content .= '
    </div>';
} else {
    $content .= '
    <div class="text-center py-12">
        <i class="fas fa-ticket-alt text-gray-300 text-6xl mb-4"></i>
        <h3 class="text-xl font-semibold text-gray-600 mb-2">No Bookings Found</h3>
        <p class="text-gray-500 mb-6">You haven\'t made any bookings yet.</p>
        <a href="search.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded transition duration-300">
            <i class="fas fa-search mr-2"></i>Search Trains
        </a>
    </div>';
}

$content .= '
</div>';

require_once '../includes/header.php';
?>