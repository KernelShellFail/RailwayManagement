<?php
require_once 'config/config.php';
require_once 'includes/functions.php';
require_once 'includes/auth.php';

$auth = new Auth();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = sanitizeInput($_POST['full_name'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    
    $errors = [];
    
    if (empty($username)) $errors[] = 'Username is required.';
    if (empty($email)) $errors[] = 'Email is required.';
    if (!validateEmail($email)) $errors[] = 'Invalid email format.';
    if (empty($password)) $errors[] = 'Password is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if ($password !== $confirm_password) $errors[] = 'Passwords do not match.';
    if (empty($full_name)) $errors[] = 'Full name is required.';
    if (!empty($phone) && !validatePhone($phone)) $errors[] = 'Invalid phone number.';
    
    if (empty($errors)) {
        if ($auth->register($username, $email, $password, $full_name, $phone)) {
            setFlashMessage('Registration successful! Please login.', 'success');
            redirect('login.php');
        } else {
            setFlashMessage('Username or email already exists.', 'error');
        }
    } else {
        setFlashMessage(implode(' ', $errors), 'error');
    }
}

$content = '
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">
            <i class="fas fa-user-plus mr-2"></i>Register
        </h2>
        
        <form method="POST" action="">
            <div class="mb-4">
                <label for="username" class="block text-gray-700 text-sm font-bold mb-2">
                    Username *
                </label>
                <input type="text" id="username" name="username" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Choose a username">
            </div>
            
            <div class="mb-4">
                <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                    Email *
                </label>
                <input type="email" id="email" name="email" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Enter your email">
            </div>
            
            <div class="mb-4">
                <label for="full_name" class="block text-gray-700 text-sm font-bold mb-2">
                    Full Name *
                </label>
                <input type="text" id="full_name" name="full_name" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Enter your full name">
            </div>
            
            <div class="mb-4">
                <label for="phone" class="block text-gray-700 text-sm font-bold mb-2">
                    Phone Number
                </label>
                <input type="tel" id="phone" name="phone"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Enter your phone number">
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                    Password *
                </label>
                <input type="password" id="password" name="password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Enter your password (min 6 chars)">
            </div>
            
            <div class="mb-6">
                <label for="confirm_password" class="block text-gray-700 text-sm font-bold mb-2">
                    Confirm Password *
                </label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Confirm your password">
            </div>
            
            <button type="submit" 
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300">
                <i class="fas fa-user-plus mr-2"></i>Register
            </button>
        </form>
        
        <div class="mt-6 text-center">
            <p class="text-gray-600">
                Already have an account? 
                <a href="login.php" class="text-blue-600 hover:text-blue-800 font-semibold">Login here</a>
            </p>
        </div>
    </div>
</div>';

require_once 'includes/header.php';
?>