<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo defined('APP_NAME') ? APP_NAME : 'Railway Reservation System'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-train text-2xl"></i>
                    <a href="index.php" class="text-xl font-bold">Railway Reservation System</a>
                </div>
                
                <div class="flex items-center space-x-6">
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']): ?>
                        <span class="text-sm">Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
                        
                        <?php if ($_SESSION['user_type'] === 'admin'): ?>
                            <a href="admin/dashboard.php" class="hover:text-blue-200 transition">Admin Dashboard</a>
                        <?php else: ?>
                            <a href="passenger/dashboard.php" class="hover:text-blue-200 transition">My Dashboard</a>
                        <?php endif; ?>
                        
                        <a href="logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded text-sm transition">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="hover:text-blue-200 transition">Login</a>
                        <a href="register.php" class="bg-green-500 hover:bg-green-600 px-4 py-2 rounded text-sm transition">
                            Register
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="container mx-auto px-4 mt-4">
            <div class="bg-<?php echo $_SESSION['flash_type'] ?? 'green'; ?>-100 border border-<?php echo $_SESSION['flash_type'] ?? 'green'; ?>-400 text-<?php echo $_SESSION['flash_type'] ?? 'green'; ?>-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline"><?php echo htmlspecialchars($_SESSION['flash_message']); ?></span>
                <button onclick="this.parentElement.remove()" class="absolute top-0 bottom-0 right-0 px-4 py-3">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <?php unset($_SESSION['flash_message'], $_SESSION['flash_type']); ?>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <?php echo $content ?? ''; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-6 mt-12">
        <div class="container mx-auto px-4 text-center">
            <p>&copy; <?php echo date('Y'); ?> Railway Reservation System. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Auto-hide flash messages after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('[role="alert"]');
            alerts.forEach(alert => alert.remove());
        }, 5000);
    </script>
</body>
</html>