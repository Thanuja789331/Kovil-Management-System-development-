<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?></title>
    <!-- Tailwind CSS CDN (Fallback) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Local CSS -->
    <link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
</head>
<body class="min-h-screen bg-overlay relative">
    <!-- Navigation Bar - Dark Green -->
    <nav class="bg-primary-800/95 backdrop-blur-md shadow-xl sticky top-0 z-50 border-b border-white/10">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Left Side: Logo & Title -->
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-xl flex items-center justify-center shadow-lg">
                        <span class="text-2xl">🛕</span>
                    </div>
                    <h1 class="text-xl font-bold text-white tracking-wide"><?= APP_NAME ?></h1>
                </div>
                
                <?php if(isset($_SESSION['user'])): ?>
                <!-- Right Side: User Info, Language Switcher & Logout -->
                <div class="flex items-center space-x-4">
                    <!-- Admin Menu Dropdown -->
                    <?php if($_SESSION['user']['role'] === 'management'): ?>
                    <div class="relative group">
                        <button class="flex items-center space-x-2 bg-white/10 backdrop-blur-sm px-3 py-2 rounded-xl border border-white/20 hover:bg-white/20 transition-all">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="hidden sm:inline text-white font-semibold text-sm">Admin</span>
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Admin Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0 z-50">
                            <div class="py-2">
                                <a href="?url=manage-users" class="block px-4 py-2 hover:bg-gray-100 text-gray-700">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <span class="font-medium text-sm">Manage Users</span>
                                    </div>
                                </a>
                                <a href="?url=pooja-history" class="block px-4 py-2 hover:bg-gray-100 text-gray-700">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                        <span class="font-medium text-sm">Pooja History</span>
                                    </div>
                                </a>
                                <a href="?url=approve-registration" class="block px-4 py-2 hover:bg-gray-100 text-gray-700">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="font-medium text-sm">Approve Registration</span>
                                    </div>
                                </a>
                                <div class="border-t border-gray-200 my-1"></div>
                                <a href="?url=assign" class="block px-4 py-2 hover:bg-gray-100 text-gray-700">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <span class="font-medium text-sm">Assign Priest Duties</span>
                                    </div>
                                </a>
                                <a href="?url=dashboard" class="block px-4 py-2 hover:bg-gray-100 text-gray-700">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-8 0h6"></path>
                                        </svg>
                                        <span class="font-medium text-sm">Dashboard</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Priest Menu Dropdown -->
                    <?php if($_SESSION['user']['role'] === 'priest'): ?>
                    <div class="relative group">
                        <button class="flex items-center space-x-2 bg-white/10 backdrop-blur-sm px-3 py-2 rounded-xl border border-white/20 hover:bg-white/20 transition-all">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            <span class="hidden sm:inline text-white font-semibold text-sm">Priest</span>
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Priest Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0 z-50">
                            <div class="py-2">
                                <a href="?url=dashboard" class="block px-4 py-2 hover:bg-gray-100 text-gray-700">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-8 0h6"></path>
                                        </svg>
                                        <span class="font-medium text-sm">My Dashboard</span>
                                    </div>
                                </a>
                                <a href="?url=priest-schedules" class="block px-4 py-2 hover:bg-gray-100 text-gray-700">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="font-medium text-sm">All Schedules & Festivals</span>
                                    </div>
                                </a>
                                <a href="?url=schedule" class="block px-4 py-2 hover:bg-gray-100 text-gray-700">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                        <span class="font-medium text-sm">Book Pooja (Devotee)</span>
                                    </div>
                                </a>
                                <div class="border-t border-gray-200 my-1"></div>
                                <a href="?url=my-bookings" class="block px-4 py-2 hover:bg-gray-100 text-gray-700">
                                    <div class="flex items-center space-x-2">
                                        <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <span class="font-medium text-sm">My Bookings</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Language Switcher -->
                    <div class="relative group">
                        <button class="flex items-center space-x-2 bg-white/10 backdrop-blur-sm px-3 py-2 rounded-xl border border-white/20 hover:bg-white/20 transition-all">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.148"></path>
                            </svg>
                            <span class="hidden sm:inline text-white font-semibold text-sm"><?= getLanguageName(getCurrentLanguage()) ?></span>
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-200 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0 z-50">
                            <div class="py-2">
                                <a href="?url=language&lang=en" class="block px-4 py-2 hover:bg-gray-100 <?= getCurrentLanguage() === 'en' ? 'bg-green-50 text-green-700' : 'text-gray-700' ?>">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium">English</span>
                                        <?php if(getCurrentLanguage() === 'en'): ?>
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                </a>
                                <a href="?url=language&lang=ta" class="block px-4 py-2 hover:bg-gray-100 <?= getCurrentLanguage() === 'ta' ? 'bg-green-50 text-green-700' : 'text-gray-700' ?>">
                                    <div class="flex items-center justify-between">
                                        <span class="font-medium">தமிழ்</span>
                                        <?php if(getCurrentLanguage() === 'ta'): ?>
                                            <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- User Badge -->
                    <div class="hidden md:flex items-center space-x-3 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-xl border border-white/20">
                        <div class="w-8 h-8 bg-gradient-to-br from-accent-400 to-accent-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-bold text-sm"><?= strtoupper(substr($_SESSION['user']['name'], 0, 1)) ?></span>
                        </div>
                        <div>
                            <p class="text-white font-semibold text-sm"><?= htmlspecialchars($_SESSION['user']['name']) ?></p>
                            <p class="text-xs text-white/70 capitalize"><?= htmlspecialchars($_SESSION['user']['role']) ?></p>
                        </div>
                    </div>
                    
                    <!-- Logout Button -->
                    <a href="?url=logout" class="flex items-center space-x-2 bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-xl transition-all duration-200 hover:scale-105 active:scale-95 shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                        </svg>
                        <span class="hidden sm:inline">Logout</span>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- Main Content Area -->
    <main class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php if(!empty($message)): ?>
        <div class="mb-6 p-4 rounded-xl <?= $messageType === 'success' ? 'bg-green-600' : 'bg-red-600' ?> text-white shadow-lg glass-card">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>
        
        <?php if(!empty($error)): ?>
        <div class="mb-6 p-4 rounded-xl bg-red-600 text-white shadow-lg glass-card">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>
