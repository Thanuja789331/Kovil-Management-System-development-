<?php
/**
 * 500 Server Error
 * Displayed when an unexpected error occurs
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error | Kovil Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            min-height: 100vh;
        }
    </style>
</head>
<body class="flex items-center justify-center">
    <div class="text-center text-white px-4 max-w-md">
        <div class="mb-8">
            <h1 class="text-8xl font-bold mb-4">⚠️</h1>
            <h2 class="text-4xl font-bold mb-4">500 - Server Error</h2>
            <p class="text-xl mb-8 text-gray-100">
                An unexpected error occurred while processing your request.
            </p>
        </div>
        
        <div class="space-y-4 mb-8">
            <p class="text-sm text-gray-200">
                Our technical team has been notified and is working on a fix.
            </p>
            <p class="text-sm text-gray-200">
                Please try again in a few moments.
            </p>
        </div>
        
        <div class="space-y-4">
            <a href="?url=home" class="inline-block bg-white text-red-600 font-bold py-3 px-8 rounded-lg hover:bg-gray-100 transition">
                ← Go to Home
            </a>
            <?php if (isset($_SESSION['user']) && !empty($_SESSION['user']['id'])): ?>
                <br>
                <a href="?url=dashboard" class="inline-block bg-red-500 text-white font-bold py-3 px-8 rounded-lg hover:bg-red-600 transition">
                    Go to Dashboard
                </a>
            <?php endif; ?>
        </div>
        
        <hr class="my-12 border-red-400">
        
        <p class="text-xs text-gray-200">
            Error ID: <?php echo uniqid(); ?> (included in server logs)
        </p>
    </div>
</body>
</html>
