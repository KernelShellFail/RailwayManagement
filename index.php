<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';
require_once 'includes/train.php';
require_once 'includes/route.php';

$auth = new Auth();
$train = new Train();
$route = new Route();

// Get featured routes for homepage
$featured_routes = $route->getAllRoutes();
$featured_routes = array_slice($featured_routes, 0, 6); // Show first 6 routes

$content = '
<div class="text-center mb-8">
    <h1 class="text-4xl font-bold text-gray-800 mb-4">
        <i class="fas fa-train mr-3"></i>Welcome to Railway Reservation System
    </h1>
    <p class="text-xl text-gray-600">Book your train tickets easily and securely</p>
</div>';

if (!$auth->isLoggedIn()) {
    $content .= '
<div class="grid md:grid-cols-2 gap-8 mb-12">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-4 text-blue-600">
            <i class="fas fa-user mr-2"></i>Passenger Portal
        </h2>
        <p class="text-gray-600 mb-4">
            Search trains, check seat availability, book tickets, and manage your bookings.
        </p>
        <ul class="text-gray-600 mb-6 space-y-2">
            <li><i class="fas fa-check text-green-500 mr-2"></i>Search trains by route and date</li>
            <li><i class="fas fa-check text-green-500 mr-2"></i>Real-time seat availability</li>
            <li><i class="fas fa-check text-green-500 mr-2"></i>Secure online booking</li>
            <li><i class="fas fa-check text-green-500 mr-2"></i>Booking history management</li>
        </ul>
        <a href="login.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition duration-300">
            <i class="fas fa-sign-in-alt mr-2"></i>Login as Passenger
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-4 text-green-600">
            <i class="fas fa-cog mr-2"></i>Admin Portal
        </h2>
        <p class="text-gray-600 mb-4">
            Manage trains, routes, and monitor all passenger bookings and system operations.
        </p>
        <ul class="text-gray-600 mb-6 space-y-2">
            <li><i class="fas fa-check text-green-500 mr-2"></i>Train management (CRUD)</li>
            <li><i class="fas fa-check text-green-500 mr-2"></i>Route configuration</li>
            <li><i class="fas fa-check text-green-500 mr-2"></i>Booking monitoring</li>
            <li><i class="fas fa-check text-green-500 mr-2"></i>Seat availability tracking</li>
        </ul>
        <a href="login.php" class="inline-block bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-6 rounded transition duration-300">
            <i class="fas fa-sign-in-alt mr-2"></i>Login as Admin
        </a>
    </div>
</div>';
} else {
    $user = $auth->getCurrentUser();
    if ($user['user_type'] === 'admin') {
        $content .= '
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">
        <i class="fas fa-tachometer-alt mr-2"></i>Admin Dashboard
    </h2>
    <p class="text-gray-600 mb-4">Manage your railway reservation system from the admin dashboard.</p>
    <a href="admin/dashboard.php" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded transition duration-300">
        <i class="fas fa-arrow-right mr-2"></i>Go to Dashboard
    </a>
</div>';
    } else {
        $content .= '
<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">
        <i class="fas fa-user-circle mr-2"></i>Passenger Dashboard
            <i class="fas fa-arrow-right mr-2"></i>Go to Dashboard
            </a>
        </div>';
    }
}

$content .= '
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold mb-6 text-gray-800">
        <i class="fas fa-route mr-2"></i>Popular Routes
    </h2>
    
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">';

if (!empty($featured_routes)) {
    foreach ($featured_routes as $route) {
        $duration = calculateDuration($route['departure_time'], $route['arrival_time']);
        $content .= '
        <div class="border rounded-lg p-4 hover:shadow-lg transition duration-300">
            <div class="flex items-center mb-3">
                <i class="fas fa-train text-blue-600 mr-2"></i>
                <span class="font-semibold text-gray-800">' . htmlspecialchars($route['train_name']) . '</span>
            </div>
            
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">From:</span>
                    <span class="font-medium">' . htmlspecialchars($route['source_station']) . '</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">To:</span>
                    <span class="font-medium">' . htmlspecialchars($route['destination_station']) . '</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Departure:</span>
                    <span class="font-medium">' . formatTime($route['departure_time']) . '</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Arrival:</span>
                    <span class="font-medium">' . formatTime($route['arrival_time']) . '</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Duration:</span>
                    <span class="font-medium">' . $duration . '</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t">
                    <span class="text-gray-600">Price:</span>
                    <span class="text-xl font-bold text-green-600">' . formatCurrency($route['price_per_seat']) . '</span>
                </div>
            </div>';
        
        if ($auth->isPassenger()) {
            $content .= '
            <div class="mt-4">
                <a href="passenger/search.php" class="w-full block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center transition duration-300">
                    <i class="fas fa-search mr-2"></i>Book Now
                </a>
            </div>';
        }
        
        $content .= '
        </div>';
    }
} else {
    $content .= '
    <div class="col-span-full text-center py-8">
        <p class="text-gray-500">No routes available at the moment.</p>
    </div>';
}

$content .= '
    </div>
</div>';

require_once 'includes/header.php';
?>