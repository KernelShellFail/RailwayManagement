<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_once '../includes/train.php';
require_once '../includes/route.php';
require_once '../includes/booking.php';

requireAdmin();

$train = new Train();
$route = new Route();
$booking = new Booking();

// Get statistics
$stats = [
    'total_trains' => count($train->getAllTrains()),
    'total_routes' => count($route->getAllRoutes()),
    'total_bookings' => count($booking->getAllBookings()),
    'confirmed_bookings' => count(array_filter($booking->getAllBookings(), function($b) { return $b['booking_status'] === 'confirmed'; }))
];

// Get recent bookings
$recent_bookings = array_slice($booking->getAllBookings(), 0, 5);

$content = '
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-tachometer-alt mr-2"></i>Admin Dashboard
    </h1>
    <p class="text-gray-600 mt-2">Manage trains, routes, and monitor bookings</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-train text-blue-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-gray-500 text-sm">Total Trains</h3>
                <p class="text-2xl font-bold text-gray-800">' . $stats['total_trains'] . '</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-route text-green-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-gray-500 text-sm">Total Routes</h3>
                <p class="text-2xl font-bold text-gray-800">' . $stats['total_routes'] . '</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="bg-purple-100 p-3 rounded-full">
                <i class="fas fa-ticket-alt text-purple-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-gray-500 text-sm">Total Bookings</h3>
                <p class="text-2xl font-bold text-gray-800">' . $stats['total_bookings'] . '</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-gray-500 text-sm">Confirmed Bookings</h3>
                <p class="text-2xl font-bold text-gray-800">' . $stats['confirmed_bookings'] . '</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">
            <i class="fas fa-cog mr-2"></i>Quick Actions
        </h2>
        <div class="grid grid-cols-2 gap-4">
            <a href="trains.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded text-center transition duration-300">
                <i class="fas fa-train block mb-2 text-2xl"></i>
                Manage Trains
            </a>
            <a href="routes.php" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded text-center transition duration-300">
                <i class="fas fa-route block mb-2 text-2xl"></i>
                Manage Routes
            </a>
            <a href="bookings.php" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-4 rounded text-center transition duration-300">
                <i class="fas fa-ticket-alt block mb-2 text-2xl"></i>
                View Bookings
            </a>
            <a href="reports.php" class="bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-4 rounded text-center transition duration-300">
                <i class="fas fa-chart-bar block mb-2 text-2xl"></i>
                Reports
            </a>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4 text-gray-800">
            <i class="fas fa-clock mr-2"></i>Recent Bookings
        </h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2">Booking ID</th>
                        <th class="text-left py-2">Passenger</th>
                        <th class="text-left py-2">Route</th>
                        <th class="text-left py-2">Status</th>
                    </tr>
                </thead>
                <tbody>';

if (!empty($recent_bookings)) {
    foreach ($recent_bookings as $booking) {
        $status_color = $booking['booking_status'] === 'confirmed' ? 'green' : 'red';
        $content .= '
                    <tr class="border-b">
                        <td class="py-2">' . htmlspecialchars($booking['booking_id']) . '</td>
                        <td class="py-2">' . htmlspecialchars($booking['passenger_name']) . '</td>
                        <td class="py-2">' . htmlspecialchars($booking['source_station']) . ' → ' . htmlspecialchars($booking['destination_station']) . '</td>
                        <td class="py-2">
                            <span class="bg-' . $status_color . '-100 text-' . $status_color . '-800 text-xs font-semibold px-2 py-1 rounded">
                                ' . ucfirst($booking['booking_status']) . '
                            </span>
                        </td>
                    </tr>';
    }
} else {
    $content .= '
                    <tr>
                        <td colspan="4" class="text-center py-4 text-gray-500">No bookings found</td>
                    </tr>';
}

$content .= '
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            <a href="bookings.php" class="text-blue-600 hover:text-blue-800 font-semibold">
                View all bookings <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
    </div>
</div>';

require_once '../includes/header.php';
?>