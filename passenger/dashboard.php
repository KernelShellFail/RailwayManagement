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
        <i class="fas fa-user-circle mr-2"></i>Passenger Dashboard
    </h1>
    <p class="text-gray-600 mt-2">Manage your bookings and profile</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="bg-blue-100 p-3 rounded-full">
                <i class="fas fa-ticket-alt text-blue-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-gray-500 text-sm">Total Bookings</h3>
                <p class="text-2xl font-bold text-gray-800">' . count($user_bookings) . '</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="bg-green-100 p-3 rounded-full">
                <i class="fas fa-check-circle text-green-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-gray-500 text-sm">Confirmed</h3>
                <p class="text-2xl font-bold text-gray-800">' . count(array_filter($user_bookings, function($b) { return $b['booking_status'] === 'confirmed'; })) . '</p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex items-center">
            <div class="bg-purple-100 p-3 rounded-full">
                <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
            </div>
            <div class="ml-4">
                <h3 class="text-gray-500 text-sm">Total Spent</h3>
                <p class="text-2xl font-bold text-gray-800">' . formatCurrency(array_sum(array_column($user_bookings, 'total_price'))) . '</p>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">
                <i class="fas fa-history mr-2"></i>Recent Bookings
            </h2>
            
            <div class="space-y-4">';

if (!empty($user_bookings)) {
    foreach (array_slice($user_bookings, 0, 5) as $b) {
        $status_color = $b['booking_status'] === 'confirmed' ? 'green' : 'red';
        $duration = calculateDuration($b['departure_time'], $b['arrival_time']);
        
        $content .= '
                <div class="border rounded-lg p-4 hover:shadow-md transition duration-300">
                    <div class="flex justify-between items-start mb-3">
                        <div>
                            <div class="font-semibold text-lg">' . htmlspecialchars($b['booking_id']) . '</div>
                            <div class="text-sm text-gray-500">Booked on ' . date('M j, Y', strtotime($b['created_at'])) . '</div>
                        </div>
                        <span class="bg-' . $status_color . '-100 text-' . $status_color . '-800 font-semibold px-2 py-1 rounded text-sm">
                            ' . ucfirst($b['booking_status']) . '
                        </span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div>
                            <div class="text-gray-500">Train:</div>
                            <div class="font-semibold">' . htmlspecialchars($b['train_name']) . '</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Route:</div>
                            <div class="font-semibold">' . htmlspecialchars($b['source_station']) . ' → ' . htmlspecialchars($b['destination_station']) . '</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Date:</div>
                            <div class="font-semibold">' . formatDate($b['booking_date']) . '</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Time:</div>
                            <div class="font-semibold">' . formatTime($b['departure_time']) . ' - ' . formatTime($b['arrival_time']) . '</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Passenger:</div>
                            <div class="font-semibold">' . htmlspecialchars($b['passenger_name']) . '</div>
                        </div>
                        <div>
                            <div class="text-gray-500">Seats:</div>
                            <div class="font-semibold">' . $b['seats_booked'] . ' seats</div>
                        </div>
                    </div>
                    
                    <div class="mt-3 pt-3 border-t flex justify-between items-center">
                        <div class="text-lg font-bold text-green-600">' . formatCurrency($b['total_price']) . '</div>
                        <div class="flex space-x-2">
                            <a href="ticket.php?id=' . $b['id'] . '" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition duration-300">
                                <i class="fas fa-eye mr-1"></i>View
                            </a>';
                            if ($b['booking_status'] === 'confirmed') {
                                $content .= '
                                <a href="cancel.php?id=' . $b['id'] . '" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition duration-300" onclick="return confirm(\'Are you sure you want to cancel this booking?\')">
                                    <i class="fas fa-times mr-1"></i>Cancel
                                </a>';
                            }
        $content .= '
                        </div>
                    </div>
                </div>';
    }
} else {
    $content .= '
                <div class="text-center py-8">
                    <i class="fas fa-ticket-alt text-gray-300 text-5xl mb-4"></i>
                    <p class="text-gray-500">You haven\'t made any bookings yet.</p>
                    <a href="search.php" class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                        <i class="fas fa-search mr-2"></i>Search Trains
                    </a>
                </div>';
}

$content .= '
            </div>
            
            ' . (count($user_bookings) > 5 ? '<div class="mt-4 text-center"><a href="history.php" class="text-blue-600 hover:text-blue-800 font-semibold">View all bookings <i class="fas fa-arrow-right ml-1"></i></a></div>' : '') . '
        </div>
    </div>
    
    <div>
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">
                <i class="fas fa-user mr-2"></i>Profile Information
            </h2>
            
            <div class="space-y-3">
                <div>
                    <span class="text-gray-500">Name:</span>
                    <div class="font-semibold">' . htmlspecialchars($_SESSION['full_name']) . '</div>
                </div>
                <div>
                    <span class="text-gray-500">Username:</span>
                    <div class="font-semibold">' . htmlspecialchars($_SESSION['username']) . '</div>
                </div>
                <div>
                    <span class="text-gray-500">Account Type:</span>
                    <div class="font-semibold">Passenger</div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold mb-4 text-gray-800">
                <i class="fas fa-rocket mr-2"></i>Quick Actions
            </h2>
            
            <div class="space-y-3">
                <a href="search.php" class="block w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded text-center transition duration-300">
                    <i class="fas fa-search mr-2"></i>Search Trains
                </a>
                <a href="history.php" class="block w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded text-center transition duration-300">
                    <i class="fas fa-history mr-2"></i>Booking History
                </a>
                <a href="../logout.php" class="block w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-4 rounded text-center transition duration-300">
                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                </a>
            </div>
        </div>
    </div>
</div>';

require_once '../includes/header.php';
?>