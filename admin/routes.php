<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_once '../includes/route.php';
require_once '../includes/train.php';

requireAdmin();

$route = new Route();
$train = new Train();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $train_id = (int)($_POST['train_id'] ?? 0);
        $source_station = sanitizeInput($_POST['source_station'] ?? '');
        $destination_station = sanitizeInput($_POST['destination_station'] ?? '');
        $distance_km = (float)($_POST['distance_km'] ?? 0);
        $departure_time = sanitizeInput($_POST['departure_time'] ?? '');
        $arrival_time = sanitizeInput($_POST['arrival_time'] ?? '');
        $price_per_seat = (float)($_POST['price_per_seat'] ?? 0);
        
        if ($train_id <= 0 || empty($source_station) || empty($destination_station) || 
            $distance_km <= 0 || empty($departure_time) || empty($arrival_time) || $price_per_seat <= 0) {
            setFlashMessage('Please fill in all fields correctly.', 'error');
        } elseif ($route->createRoute($train_id, $source_station, $destination_station, $distance_km, $departure_time, $arrival_time, $price_per_seat)) {
            setFlashMessage('Route created successfully!', 'success');
        } else {
            setFlashMessage('Failed to create route.', 'error');
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['route_id'] ?? 0);
        $train_id = (int)($_POST['train_id'] ?? 0);
        $source_station = sanitizeInput($_POST['source_station'] ?? '');
        $destination_station = sanitizeInput($_POST['destination_station'] ?? '');
        $distance_km = (float)($_POST['distance_km'] ?? 0);
        $departure_time = sanitizeInput($_POST['departure_time'] ?? '');
        $arrival_time = sanitizeInput($_POST['arrival_time'] ?? '');
        $price_per_seat = (float)($_POST['price_per_seat'] ?? 0);
        $status = sanitizeInput($_POST['status'] ?? 'active');
        
        if ($id <= 0 || $train_id <= 0 || empty($source_station) || empty($destination_station) || 
            $distance_km <= 0 || empty($departure_time) || empty($arrival_time) || $price_per_seat <= 0) {
            setFlashMessage('Please fill in all fields correctly.', 'error');
        } elseif ($route->updateRoute($id, $train_id, $source_station, $destination_station, $distance_km, $departure_time, $arrival_time, $price_per_seat, $status)) {
            setFlashMessage('Route updated successfully!', 'success');
        } else {
            setFlashMessage('Failed to update route.', 'error');
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['route_id'] ?? 0);
        if ($id > 0 && $route->deleteRoute($id)) {
            setFlashMessage('Route deleted successfully!', 'success');
        } else {
            setFlashMessage('Failed to delete route.', 'error');
        }
    }
    
    header('Location: routes.php');
    exit();
}

$routes = $route->getAllRoutes();
$trains = $train->getAllTrains();
$editing_route = null;

if (isset($_GET['edit'])) {
    $editing_route = $route->getRouteById((int)$_GET['edit']);
}

$content = '
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-route mr-2"></i>Manage Routes
    </h1>
    <p class="text-gray-600 mt-2">Add, edit, and delete train routes</p>
</div>';

if ($editing_route) {
    $content .= '
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-bold mb-4 text-gray-800">
        <i class="fas fa-edit mr-2"></i>Edit Route
    </h2>
    <form method="POST" action="">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="route_id" value="' . $editing_route['id'] . '">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="train_id" class="block text-gray-700 text-sm font-bold mb-2">Train *</label>
                <select id="train_id" name="train_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Train</option>';
                    foreach ($trains as $t) {
                        $selected = $t['id'] == $editing_route['train_id'] ? 'selected' : '';
                        $content .= '<option value="' . $t['id'] . '" ' . $selected . '>' . htmlspecialchars($t['train_name']) . ' (' . htmlspecialchars($t['train_number']) . ')</option>';
                    }
    $content .= '
                </select>
            </div>
            
            <div>
                <label for="source_station" class="block text-gray-700 text-sm font-bold mb-2">Source Station *</label>
                <input type="text" id="source_station" name="source_station" required
                       value="' . htmlspecialchars($editing_route['source_station']) . '"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="destination_station" class="block text-gray-700 text-sm font-bold mb-2">Destination Station *</label>
                <input type="text" id="destination_station" name="destination_station" required
                       value="' . htmlspecialchars($editing_route['destination_station']) . '"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="distance_km" class="block text-gray-700 text-sm font-bold mb-2">Distance (km) *</label>
                <input type="number" id="distance_km" name="distance_km" required step="0.01" min="0.01"
                       value="' . $editing_route['distance_km'] . '"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="departure_time" class="block text-gray-700 text-sm font-bold mb-2">Departure Time *</label>
                <input type="time" id="departure_time" name="departure_time" required
                       value="' . $editing_route['departure_time'] . '"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="arrival_time" class="block text-gray-700 text-sm font-bold mb-2">Arrival Time *</label>
                <input type="time" id="arrival_time" name="arrival_time" required
                       value="' . $editing_route['arrival_time'] . '"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="price_per_seat" class="block text-gray-700 text-sm font-bold mb-2">Price per Seat ($) *</label>
                <input type="number" id="price_per_seat" name="price_per_seat" required step="0.01" min="0.01"
                       value="' . $editing_route['price_per_seat'] . '"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                <select id="status" name="status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="active" ' . ($editing_route['status'] === 'active' ? 'selected' : '') . '>Active</option>
                    <option value="inactive" ' . ($editing_route['status'] === 'inactive' ? 'selected' : '') . '>Inactive</option>
                </select>
            </div>
        </div>
        
        <div class="mt-4 flex space-x-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                <i class="fas fa-save mr-2"></i>Update Route
            </button>
            <a href="routes.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
        </div>
    </form>
</div>';
} else {
    $content .= '
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-bold mb-4 text-gray-800">
        <i class="fas fa-plus mr-2"></i>Add New Route
    </h2>
    <form method="POST" action="">
        <input type="hidden" name="action" value="create">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="train_id" class="block text-gray-700 text-sm font-bold mb-2">Train *</label>
                <select id="train_id" name="train_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Select Train</option>';
                    foreach ($trains as $t) {
                        $content .= '<option value="' . $t['id'] . '">' . htmlspecialchars($t['train_name']) . ' (' . htmlspecialchars($t['train_number']) . ')</option>';
                    }
    $content .= '
                </select>
            </div>
            
            <div>
                <label for="source_station" class="block text-gray-700 text-sm font-bold mb-2">Source Station *</label>
                <input type="text" id="source_station" name="source_station" required
                       placeholder="e.g., New York"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="destination_station" class="block text-gray-700 text-sm font-bold mb-2">Destination Station *</label>
                <input type="text" id="destination_station" name="destination_station" required
                       placeholder="e.g., Boston"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="distance_km" class="block text-gray-700 text-sm font-bold mb-2">Distance (km) *</label>
                <input type="number" id="distance_km" name="distance_km" required step="0.01" min="0.01"
                       placeholder="e.g., 300.50"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="departure_time" class="block text-gray-700 text-sm font-bold mb-2">Departure Time *</label>
                <input type="time" id="departure_time" name="departure_time" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="arrival_time" class="block text-gray-700 text-sm font-bold mb-2">Arrival Time *</label>
                <input type="time" id="arrival_time" name="arrival_time" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="price_per_seat" class="block text-gray-700 text-sm font-bold mb-2">Price per Seat ($) *</label>
                <input type="number" id="price_per_seat" name="price_per_seat" required step="0.01" min="0.01"
                       placeholder="e.g., 45.00"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        
        <div class="mt-4">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                <i class="fas fa-plus mr-2"></i>Add Route
            </button>
        </div>
    </form>
</div>';
}

$content .= '
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-bold mb-4 text-gray-800">
        <i class="fas fa-list mr-2"></i>All Routes
    </h2>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left py-3 px-4">Train</th>
                    <th class="text-left py-3 px-4">Route</th>
                    <th class="text-left py-3 px-4">Distance</th>
                    <th class="text-left py-3 px-4">Time</th>
                    <th class="text-left py-3 px-4">Price</th>
                    <th class="text-left py-3 px-4">Status</th>
                    <th class="text-left py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>';

if (!empty($routes)) {
    foreach ($routes as $r) {
        $duration = calculateDuration($r['departure_time'], $r['arrival_time']);
        $status_color = $r['status'] === 'active' ? 'green' : 'red';
        
        $content .= '
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4">
                        <div class="font-semibold">' . htmlspecialchars($r['train_name']) . '</div>
                        <div class="text-sm text-gray-500">' . htmlspecialchars($r['train_number']) . '</div>
                    </td>
                    <td class="py-3 px-4">
                        <div>' . htmlspecialchars($r['source_station']) . '</div>
                        <div class="text-sm text-gray-500">→ ' . htmlspecialchars($r['destination_station']) . '</div>
                    </td>
                    <td class="py-3 px-4">' . $r['distance_km'] . ' km</td>
                    <td class="py-3 px-4">
                        <div>' . formatTime($r['departure_time']) . ' - ' . formatTime($r['arrival_time']) . '</div>
                        <div class="text-sm text-gray-500">' . $duration . '</div>
                    </td>
                    <td class="py-3 px-4 font-semibold text-green-600">' . formatCurrency($r['price_per_seat']) . '</td>
                    <td class="py-3 px-4">
                        <span class="bg-' . $status_color . '-100 text-' . $status_color . '-800 font-semibold px-2 py-1 rounded">
                            ' . ucfirst($r['status']) . '
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex space-x-2">
                            <a href="?edit=' . $r['id'] . '" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition duration-300">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="" class="inline" onsubmit="return confirm(\'Are you sure you want to delete this route?\')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="route_id" value="' . $r['id'] . '">
                                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm transition duration-300">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>';
    }
} else {
    $content .= '
                <tr>
                    <td colspan="7" class="text-center py-8 text-gray-500">No routes found</td>
                </tr>';
}

$content .= '
            </tbody>
        </table>
    </div>
</div>';

require_once '../includes/header.php';
?>