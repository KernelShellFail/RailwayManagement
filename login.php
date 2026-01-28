<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$auth = new Auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        setFlashMessage('Please fill in all fields.', 'error');
    } elseif ($auth->login($username, $password)) {
        $user = $auth->getCurrentUser();
        if ($user['user_type'] === 'admin') {
            redirect('admin/dashboard.php');
        } else {
            redirect('passenger/dashboard.php');
        }
    } else {
        setFlashMessage('Invalid username or password.', 'error');
    }
}

$content = '
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">
            <i class="fas fa-sign-in-alt mr-2"></i>Login
        </h2>
        
        <form method="POST" action="">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">
                    Username or Email
                </label>
                <input type="text" id="username" name="username" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Enter your username or email">
            </div>
            
            <div class="mb-6">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                    Password
                </label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Enter your password">
            </div>
            
            <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                <i class="fas fa-sign-in-alt mr-2"></i>Login
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-gray-600">
                Don\'t have an account? 
                <a href="register.php" class="text-blue-600 hover:text-blue-800 font-semibold">Register here</a>
            </p>
        </div>
        
        <div class="mt-4 p-4 bg-gray-100 rounded">
            <p class="text-sm text-gray-600 text-center">
                <strong>Demo Credentials:</strong><br>
                Admin: admin / admin123<br>
                Passenger: Register a new account
            </p>
        </div>
    </div>
</div>';

require_once 'includes/header.php';
?>