<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_once '../includes/booking.php';

requireAdmin();

$booking = new Booking();
$bookings = $booking->getAllBookings();

$content = '
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-ticket-alt mr-2"></i>All Bookings
    </h1>
    <p class="text-gray-600 mt-2">View and manage all passenger bookings</p>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <div class="mb-4 flex justify-between items-center">
        <h2 class="text-xl font-bold text-gray-800">Booking List</h2>
        <div class="text-sm text-gray-600">
            Total Bookings: ' . count($bookings) . '
        </div>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left py-3 px-4">Booking ID</th>
                    <th class="text-left py-3 px-4">User</th>
                    <th class="text-left py-3 px-4">Passenger</th>
                    <th class="text-left py-3 px-4">Train & Route</th>
                    <th class="text-left py-3 px-4">Date</th>
                    <th class="text-left py-3 px-4">Seats</th>
                    <th class="text-left py-3 px-4">Price</th>
                    <th class="text-left py-3 px-4">Status</th>
                    <th class="text-left py-3 px-4">Booked On</th>
                </tr>
            </thead>
            <tbody>';

if (!empty($bookings)) {
    foreach ($bookings as $b) {
        $status_color = $b['booking_status'] === 'confirmed' ? 'green' : 'red';
        
        $content .= '
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4 font-semibold">' . htmlspecialchars($b['booking_id']) . '</td>
                    <td class="py-3 px-4">
                        <div>' . htmlspecialchars($b['username']) . '</div>
                        <div class="text-sm text-gray-500">' . htmlspecialchars($b['email']) . '</div>
                    </td>
                    <td class="py-3 px-4">
                        <div>' . htmlspecialchars($b['passenger_name']) . '</div>
                        <div class="text-sm text-gray-500">' . $b['passenger_age'] . ' years, ' . ucfirst($b['passenger_gender']) . '</div>
                    </td>
                    <td class="py-3 px-4">
                        <div class="font-semibold">' . htmlspecialchars($b['train_name']) . '</div>
                        <div class="text-sm text-gray-500">' . htmlspecialchars($b['source_station']) . ' → ' . htmlspecialchars($b['destination_station']) . '</div>
                        <div class="text-sm text-gray-500">' . formatTime($b['departure_time']) . ' - ' . formatTime($b['arrival_time']) . '</div>
                    </td>
                    <td class="py-3 px-4">' . formatDate($b['booking_date']) . '</td>
                    <td class="py-3 px-4">' . $b['seats_booked'] . '</td>
                    <td class="py-3 px-4 font-semibold text-green-600">' . formatCurrency($b['total_price']) . '</td>
                    <td class="py-3 px-4">
                        <span class="bg-' . $status_color . '-100 text-' . $status_color . '-800 font-semibold px-2 py-1 rounded">
                            ' . ucfirst($b['booking_status']) . '
                        </span>
                    </td>
                    <td class="py-3 px-4 text-sm text-gray-500">' . date('M j, Y H:i', strtotime($b['created_at'])) . '</td>
                </tr>';
    }
} else {
    $content .= '
                <tr>
                    <td colspan="9" class="text-center py-8 text-gray-500">No bookings found</td>
                </tr>';
}

$content .= '
            </tbody>
        </table>
    </div>
</div>

<div class="mt-8 bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-bold mb-4 text-gray-800">
        <i class="fas fa-chart-bar mr-2"></i>Booking Statistics
    </h2>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="text-center">
            <div class="text-3xl font-bold text-blue-600">' . count($bookings) . '</div>
            <div class="text-gray-600">Total Bookings</div>
        </div>
        <div class="text-center">
            <div class="text-3xl font-bold text-green-600">' . count(array_filter($bookings, function($b) { return $b['booking_status'] === 'confirmed'; })) . '</div>
            <div class="text-gray-600">Confirmed</div>
        </div>
        <div class="text-center">
            <div class="text-3xl font-bold text-red-600">' . count(array_filter($bookings, function($b) { return $b['booking_status'] === 'cancelled'; })) . '</div>
            <div class="text-gray-600">Cancelled</div>
        </div>
        <div class="text-center">
            <div class="text-3xl font-bold text-purple-600">' . formatCurrency(array_sum(array_column($bookings, 'total_price'))) . '</div>
            <div class="text-gray-600">Total Revenue</div>
        </div>
    </div>
</div>';

require_once '../includes/header.php';
?>