<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_once '../includes/route.php';
require_once '../includes/booking.php';

requireAuth();

$route = new Route();
$booking = new Booking();

$search_results = [];
$search_performed = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $source = sanitizeInput($_POST['source'] ?? '');
    $destination = sanitizeInput($_POST['destination'] ?? '');
    $date = sanitizeInput($_POST['date'] ?? '');
    
    if (!empty($source) && !empty($destination) && !empty($date)) {
        $search_results = $route->searchRoutes($source, $destination, $date);
        $search_performed = true;
    } else {
        setFlashMessage('Please fill in all search fields.', 'error');
    }
}

$content = '
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-search mr-2"></i>Search Trains
    </h1>
    <p class="text-gray-600 mt-2">Find available trains for your journey</p>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-bold mb-4 text-gray-800">
        <i class="fas fa-route mr-2"></i>Search Routes
    </h2>
    
    <form method="POST" action="">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="source" class="block text-gray-700 text-sm font-bold mb-2">From *</label>
                <input type="text" id="source" name="source" required
                       value="' . htmlspecialchars($_POST['source'] ?? '') . '"
                       placeholder="e.g., New York"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="destination" class="block text-gray-700 text-sm font-bold mb-2">To *</label>
                <input type="text" id="destination" name="destination" required
                       value="' . htmlspecialchars($_POST['destination'] ?? '') . '"
                       placeholder="e.g., Boston"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="date" class="block text-gray-700 text-sm font-bold mb-2">Travel Date *</label>
                <input type="date" id="date" name="date" required
                       value="' . htmlspecialchars($_POST['date'] ?? '') . '"
                       min="' . date('Y-m-d') . '"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                    <i class="fas fa-search mr-2"></i>Search
                </button>
            </div>
        </div>
    </form>
</div>';

if ($search_performed) {
    $content .= '
<div class="bg-white rounded-lg shadow-md p-6">
        <h3 class="text-lg font-semibold mb-4">Search Results</h3>';
    
    if (!empty($search_results)) {
        $content .= '
        <div class="space-y-4">';
        
        foreach ($search_results as $route) {
            $duration = calculateDuration($route['departure_time'], $route['arrival_time']);
            $available_seats = $booking->checkSeatAvailability($route['id'], 1, $_POST['date']) ? 'Available' : 'Sold Out';
            $seats_color = $available_seats === 'Available' ? 'green' : 'red';
            
            $content .= '
            <div class="border rounded-lg p-4 hover:shadow-lg transition duration-300">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <div class="text-sm text-gray-500">Train</div>
                        <div class="font-semibold text-lg">' . htmlspecialchars($route['train_name']) . '</div>
                        <div class="text-sm text-gray-600">' . htmlspecialchars($route['train_number']) . '</div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-500">Route</div>
                        <div class="font-semibold">' . htmlspecialchars($route['source_station']) . ' → ' . htmlspecialchars($route['destination_station']) . '</div>
                        <div class="text-sm text-gray-600">' . $route['distance_km'] . ' km</div>
                    </div>
                    
                    <div>
                        <div class="text-sm text-gray-500">Schedule</div>
                        <div class="font-semibold">' . formatTime($route['departure_time']) . ' - ' . formatTime($route['arrival_time']) . '</div>
                        <div class="text-sm text-gray-600">' . $duration . '</div>
                    </div>
                    
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Price</div>
                        <div class="text-2xl font-bold text-green-600">' . formatCurrency($route['price_per_seat']) . '</div>
                        <div class="text-sm text-gray-600">per seat</div>
                    </div>
                </div>
                
                <div class="mt-4 pt-4 border-t flex justify-between items-center">
                    <div>
                        <span class="text-sm text-gray-500">Seats: </span>
                        <span class="bg-' . $seats_color . '-100 text-' . $seats_color . '-800 text-sm font-semibold px-2 py-1 rounded">
                            ' . $available_seats . '
                        </span>
                    </div>
                    
                    <div class="flex space-x-2">
                        <a href="book.php?route_id=' . $route['id'] . '&date=' . urlencode($_POST['date']) . '" 
                           class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                            <i class="fas fa-ticket-alt mr-2"></i>Book Now
                        </a>
                    </div>
                </div>
            </div>';
        }
        
        $content .= '
        </div>';
    } else {
        $content .= '
        <div class="text-center py-8">
            <i class="fas fa-train text-gray-300 text-5xl mb-4"></i>
            <p class="text-gray-500">No trains found for this route on the selected date.</p>
            <p class="text-gray-400 text-sm mt-2">Try different dates or routes.</p>
        </div>';
    }
    
    $content .= '
</div>';
}

require_once '../includes/header.php';
?>