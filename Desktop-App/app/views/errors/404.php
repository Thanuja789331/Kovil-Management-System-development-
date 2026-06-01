<?php
/**
 * 404 Page Not Found
 * Displayed when a route doesn't exist
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found | Kovil Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="flex items-center justify-center">
    <div class="text-center text-white px-4">
        <div class="mb-8">
            <h1 class="text-8xl font-bold mb-4 animate-bounce">404</h1>
            <h2 class="text-4xl font-bold mb-4">Page Not Found</h2>
            <p class="text-xl mb-8 text-gray-100">
                The page you're looking for doesn't exist or has been moved.
            </p>
        </div>
        
        <div class="space-y-4">
            <a href="?url=home" class="inline-block bg-white text-purple-600 font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition">
                ← Go to Home
            </a>
            <?php if (isset($_SESSION['user']) && !empty($_SESSION['user']['id'])): ?>
                <br>
                <a href="?url=dashboard" class="inline-block bg-purple-500 text-white font-bold py-3 px-8 rounded-lg hover:bg-purple-600 transition">
                    Go to Dashboard
                </a>
            <?php endif; ?>
        </div>
        
        <hr class="my-12 border-purple-400">
        
        <p class="text-sm text-gray-200">
            If you believe this is an error, please contact support.
        </p>
    </div>
</body>
</html>
