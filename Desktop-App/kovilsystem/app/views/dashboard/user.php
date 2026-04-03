<!-- User Dashboard - Devotee View -->
<div class="space-y-8">
    <!-- Back Button & Header -->
    <div class="flex items-center space-x-4 mb-4">
        <a href="javascript:history.back()" class="group flex items-center space-x-2 bg-white/80 hover:bg-white backdrop-blur-sm px-4 py-2 rounded-xl shadow-md transition-all duration-200 hover:scale-105 active:scale-95">
            <svg class="w-5 h-5 text-secondary-600 group-hover:text-secondary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="font-semibold text-secondary-600 group-hover:text-secondary-700">Back</span>
        </a>
    </div>

    <!-- Welcome Section -->
    <div class="glass-card p-6 card-hover">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?>!</h1>
        <p class="text-gray-600">Continue your spiritual journey</p>
        
        <!-- Quick Actions -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="?url=schedule" class="group bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-400 hover:to-primary-500 text-white p-6 rounded-xl shadow-lg transition-all duration-200 transform hover:scale-105">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">View Pooja Schedules</h3>
                        <p class="text-sm text-white/80">Browse and book available poojas</p>
                    </div>
                </div>
            </a>
            
            <a href="?url=my-bookings" class="group bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-400 hover:to-secondary-500 text-white p-6 rounded-xl shadow-lg transition-all duration-200 transform hover:scale-105">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-white/20 rounded-xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold">My Bookings</h3>
                        <p class="text-sm text-white/80">View your booking history</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Bookings Section -->
    <?php
    // Convert bookings data if needed
    $bookingsList = [];
    if (isset($data) && $data instanceof mysqli_result && $data->num_rows > 0) {
        $bookingsList = $data->fetch_all(MYSQLI_ASSOC);
    }
    ?>
    
    <?php if(!empty($bookingsList)): ?>
    <div class="glass-card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">Recent Bookings</h3>
            <a href="?url=my-bookings" class="text-primary-600 hover:text-primary-700 font-semibold text-sm">View All →</a>
        </div>
        
        <div class="space-y-3">
            <?php 
            $count = 0;
            foreach($bookingsList as $booking): 
                if ($count >= 5) break;
                $count++;
            ?>
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-secondary-400 to-secondary-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($booking['pooja_name'] ?? 'Pooja Booking') ?></p>
                        <p class="text-sm text-gray-600"><?= date('M d, Y', strtotime($booking['pooja_date'] ?? 'now')) ?></p>
                    </div>
                </div>
                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                    <?= ucfirst($booking['status'] ?? 'confirmed') ?>
                </span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <!-- No bookings - show welcome message -->
    <div class="glass-card p-12 text-center">
        <div class="w-20 h-20 bg-gradient-to-br from-secondary-400 to-secondary-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
        </div>
        <h3 class="text-2xl font-bold text-gray-800 mb-2">Start Your Spiritual Journey</h3>
        <p class="text-gray-600 mb-6">Book your first pooja or explore upcoming events</p>
        <div class="flex justify-center space-x-4">
            <a href="?url=schedule" class="bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-400 hover:to-primary-500 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition-all duration-200 transform hover:scale-105">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                View Pooja Schedules
            </a>
            <a href="?url=festival" class="bg-gradient-to-r from-accent-500 to-accent-600 hover:from-accent-400 hover:to-accent-500 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition-all duration-200 transform hover:scale-105">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
                Festivals
            </a>
        </div>
    </div>
    <?php endif; ?>
</div>
