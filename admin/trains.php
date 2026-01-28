<?php
require_once '../config/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';
require_once '../includes/train.php';

requireAdmin();

$train = new Train();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'create') {
        $train_number = sanitizeInput($_POST['train_number'] ?? '');
        $train_name = sanitizeInput($_POST['train_name'] ?? '');
        $total_seats = (int)($_POST['total_seats'] ?? 0);
        
        if (empty($train_number) || empty($train_name) || $total_seats <= 0) {
            setFlashMessage('Please fill in all fields correctly.', 'error');
        } elseif ($train->createTrain($train_number, $train_name, $total_seats)) {
            setFlashMessage('Train created successfully!', 'success');
        } else {
            setFlashMessage('Failed to create train. Train number may already exist.', 'error');
        }
    } elseif ($action === 'update') {
        $id = (int)($_POST['train_id'] ?? 0);
        $train_number = sanitizeInput($_POST['train_number'] ?? '');
        $train_name = sanitizeInput($_POST['train_name'] ?? '');
        $total_seats = (int)($_POST['total_seats'] ?? 0);
        $status = sanitizeInput($_POST['status'] ?? 'active');
        
        if ($id <= 0 || empty($train_number) || empty($train_name) || $total_seats <= 0) {
            setFlashMessage('Please fill in all fields correctly.', 'error');
        } elseif ($train->updateTrain($id, $train_number, $train_name, $total_seats, $status)) {
            setFlashMessage('Train updated successfully!', 'success');
        } else {
            setFlashMessage('Failed to update train.', 'error');
        }
    } elseif ($action === 'delete') {
        $id = (int)($_POST['train_id'] ?? 0);
        if ($id > 0 && $train->deleteTrain($id)) {
            setFlashMessage('Train deleted successfully!', 'success');
        } else {
            setFlashMessage('Failed to delete train.', 'error');
        }
    }
    
    header('Location: trains.php');
    exit();
}

$trains = $train->getAllTrains();
$editing_train = null;

if (isset($_GET['edit'])) {
    $editing_train = $train->getTrainById((int)$_GET['edit']);
}

$content = '
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-train mr-2"></i>Manage Trains
    </h1>
    <p class="text-gray-600 mt-2">Add, edit, and delete trains</p>
</div>';

if ($editing_train) {
    $content .= '
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-bold mb-4 text-gray-800">
        <i class="fas fa-edit mr-2"></i>Edit Train
    </h2>
    <form method="POST" action="">
        <input type="hidden" name="action" value="update">
        <input type="hidden" name="train_id" value="' . $editing_train['id'] . '">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="train_number" class="block text-gray-700 text-sm font-bold mb-2">Train Number *</label>
                <input type="text" id="train_number" name="train_number" required
                       value="' . htmlspecialchars($editing_train['train_number']) . '"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="train_name" class="block text-gray-700 text-sm font-bold mb-2">Train Name *</label>
                <input type="text" id="train_name" name="train_name" required
                       value="' . htmlspecialchars($editing_train['train_name']) . '"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="total_seats" class="block text-gray-700 text-sm font-bold mb-2">Total Seats *</label>
                <input type="number" id="total_seats" name="total_seats" required min="1"
                       value="' . $editing_train['total_seats'] . '"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status</label>
                <select id="status" name="status" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="active" ' . ($editing_train['status'] === 'active' ? 'selected' : '') . '>Active</option>
                    <option value="inactive" ' . ($editing_train['status'] === 'inactive' ? 'selected' : '') . '>Inactive</option>
                </select>
            </div>
        </div>
        
        <div class="mt-4 flex space-x-4">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                <i class="fas fa-save mr-2"></i>Update Train
            </button>
            <a href="trains.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-300">
                <i class="fas fa-times mr-2"></i>Cancel
            </a>
        </div>
    </form>
</div>';
} else {
    $content .= '
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-bold mb-4 text-gray-800">
        <i class="fas fa-plus mr-2"></i>Add New Train
    </h2>
    <form method="POST" action="">
        <input type="hidden" name="action" value="create">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="train_number" class="block text-gray-700 text-sm font-bold mb-2">Train Number *</label>
                <input type="text" id="train_number" name="train_number" required
                       placeholder="e.g., EXP001"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="train_name" class="block text-gray-700 text-sm font-bold mb-2">Train Name *</label>
                <input type="text" id="train_name" name="train_name" required
                       placeholder="e.g., Express One"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="total_seats" class="block text-gray-700 text-sm font-bold mb-2">Total Seats *</label>
                <input type="number" id="total_seats" name="total_seats" required min="1"
                       placeholder="e.g., 100"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        
        <div class="mt-4">
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded transition duration-300">
                <i class="fas fa-plus mr-2"></i>Add Train
            </button>
        </div>
    </form>
</div>';
}

$content .= '
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-bold mb-4 text-gray-800">
        <i class="fas fa-list mr-2"></i>All Trains
    </h2>
    
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b bg-gray-50">
                    <th class="text-left py-3 px-4">Train Number</th>
                    <th class="text-left py-3 px-4">Train Name</th>
                    <th class="text-left py-3 px-4">Total Seats</th>
                    <th class="text-left py-3 px-4">Available Seats</th>
                    <th class="text-left py-3 px-4">Status</th>
                    <th class="text-left py-3 px-4">Actions</th>
                </tr>
            </thead>
            <tbody>';

if (!empty($trains)) {
    foreach ($trains as $t) {
        $status_color = $t['status'] === 'active' ? 'green' : 'red';
        $seats_percentage = ($t['total_seats'] > 0) ? ($t['available_seats'] / $t['total_seats']) * 100 : 0;
        $seats_color = $seats_percentage > 50 ? 'green' : ($seats_percentage > 20 ? 'yellow' : 'red');
        
        $content .= '
                <tr class="border-b hover:bg-gray-50">
                    <td class="py-3 px-4 font-semibold">' . htmlspecialchars($t['train_number']) . '</td>
                    <td class="py-3 px-4">' . htmlspecialchars($t['train_name']) . '</td>
                    <td class="py-3 px-4">' . $t['total_seats'] . '</td>
                    <td class="py-3 px-4">
                        <span class="bg-' . $seats_color . '-100 text-' . $seats_color . '-800 font-semibold px-2 py-1 rounded">
                            ' . $t['available_seats'] . ' (' . number_format($seats_percentage, 1) . '%)
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="bg-' . $status_color . '-100 text-' . $status_color . '-800 font-semibold px-2 py-1 rounded">
                            ' . ucfirst($t['status']) . '
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex space-x-2">
                            <a href="?edit=' . $t['id'] . '" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm transition duration-300">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form method="POST" action="" class="inline" onsubmit="return confirm(\'Are you sure you want to delete this train?\')">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="train_id" value="' . $t['id'] . '">
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
                    <td colspan="6" class="text-center py-8 text-gray-500">No trains found</td>
                </tr>';
}

$content .= '
            </tbody>
        </table>
    </div>
</div>';

require_once '../includes/header.php';
?>