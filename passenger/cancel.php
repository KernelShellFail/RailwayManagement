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

if (!$booking_details || $booking_details['user_id'] != $user_id || $booking_details['booking_status'] !== 'confirmed') {
    setFlashMessage('Invalid booking request.', 'error');
    redirect('history.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_cancel'])) {
    if ($booking->cancelBooking($booking_id)) {
        setFlashMessage('Booking cancelled successfully. Your seats have been restored.', 'success');
        redirect('history.php');
    } else {
        setFlashMessage('Failed to cancel booking. Please try again.', 'error');
    }
}

$content = '
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">
        <i class="fas fa-times-circle mr-2"></i>Cancel Booking
    </h1>
    <p class="text-gray-600 mt-2">Are you sure you want to cancel this booking?</p>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="border-b pb-4 mb-4">
        <h2 class="text-xl font-bold text-gray-800">Booking Details</h2>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h3 class="text-lg font-semibold mb-3 text-gray-800">
                <i class="fas fa-info-circle mr-2"></i>Basic Information
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Booking ID:</span>
                    <span class="font-semibold">' . htmlspecialchars($booking_details['booking_id']) . '</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status:</span>
                    <span class="bg-green-100 text-green-800 font-semibold px-2 py-1 rounded">Confirmed</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Booked On:</span>
                    <span class="font-semibold">' . date('M j, Y H:i', strtotime($booking_details['created_at'])) . '</span>
                </div>
            </div>
        </div>
        
        <div>
            <h3 class="text-lg font-semibold mb-3 text-gray-800">
                <i class="fas fa-train mr-2"></i>Journey Information
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Train:</span>
                    <span class="font-semibold">' . htmlspecialchars($booking_details['train_name']) . '</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Route:</span>
                    <span class="font-semibold">' . htmlspecialchars($booking_details['source_station']) . ' → ' . htmlspecialchars($booking_details['destination_station']) . '</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Date:</span>
                    <span class="font-semibold">' . formatDate($booking_details['booking_date']) . '</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Time:</span>
                    <span class="font-semibold">' . formatTime($booking_details['departure_time']) . ' - ' . formatTime($booking_details['arrival_time']) . '</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
        <div>
            <h3 class="text-lg font-semibold mb-3 text-gray-800">
                <i class="fas fa-user mr-2"></i>Passenger Details
            </h3>
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
                    <span class="text-gray-600">Seats:</span>
                    <span class="font-semibold">' . $booking_details['seats_booked'] . ' seat(s)</span>
                </div>
            </div>
        </div>
        
        <div>
            <h3 class="text-lg font-semibold mb-3 text-gray-800">
                <i class="fas fa-dollar-sign mr-2"></i>Payment Details
            </h3>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Price per Seat:</span>
                    <span class="font-semibold">' . formatCurrency($booking_details['price_per_seat']) . '</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Price:</span>
                    <span class="font-semibold text-lg text-green-600">' . formatCurrency($booking_details['total_price']) . '</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-6">
    <h3 class="text-lg font-semibold mb-3 text-red-800">
        <i class="fas fa-exclamation-triangle mr-2"></i>Cancellation Notice
    </h3>
    <ul class="text-red-700 space-y-2">
        <li>• This action cannot be undone</li>
        <li>• Your seats will be restored and made available for other passengers</li>
        <li>• Refund policy: Please check our terms and conditions for refund information</li>
        <li>• You will receive a confirmation email after cancellation</li>
    </ul>
</div>

<div class="flex justify-between">
    <a href="ticket.php?id=' . $booking_id . '" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded transition duration-300">
        <i class="fas fa-arrow-left mr-2"></i>Back to Ticket
    </a>
    
    <form method="POST" action="" onsubmit="return confirm(\'Are you absolutely sure you want to cancel this booking?\')">
        <input type="hidden" name="confirm_cancel" value="1">
        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-300">
            <i class="fas fa-times mr-2"></i>Confirm Cancellation
        </button>
    </form>
</div>';

require_once '../includes/header.php';
?>