<!-- Reports Page - Stat Cards & Progress Bars -->
<div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center space-x-4 mb-4">
        <a href="javascript:history.back()" class="group flex items-center space-x-2 bg-white/80 hover:bg-white backdrop-blur-sm px-4 py-2 rounded-xl shadow-md transition-all duration-200 hover:scale-105 active:scale-95">
            <svg class="w-5 h-5 text-primary-700 group-hover:text-primary-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="font-semibold text-primary-700 group-hover:text-primary-800">Back</span>
        </a>
    </div>

    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-4xl font-extrabold text-white mb-2 drop-shadow-md">📊 Reports & Analytics</h1>
        <p class="text-emerald-100 font-medium drop-shadow-sm">Comprehensive performance metrics, revenue growth, and booking insights</p>
        
        <?php 
        $startQuery = !empty($_GET['start_date']) ? '&start_date=' . urlencode($_GET['start_date']) : '';
        $endQuery = !empty($_GET['end_date']) ? '&end_date=' . urlencode($_GET['end_date']) : '';
        $exportParams = $startQuery . $endQuery;
        ?>
        <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="?url=export-donations-pdf<?= $exportParams ?>" class="inline-block bg-gradient-to-r from-red-600 to-red-700 hover:from-red-500 hover:to-red-600 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg transition-all duration-200 hover:scale-[1.03] active:scale-[0.97] flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>Download Donations PDF</span>
            </a>
            <a href="?url=export-donations-csv<?= $exportParams ?>" class="inline-block bg-gradient-to-r from-emerald-600 to-emerald-700 hover:from-emerald-500 hover:to-emerald-600 text-white font-bold py-2.5 px-6 rounded-xl shadow-lg transition-all duration-200 hover:scale-[1.03] active:scale-[0.97] flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>Export Donations CSV</span>
            </a>
        </div>
    </div>

    <!-- Date Filtering Form -->
    <div class="glass-card p-6 bg-white/90 shadow-xl border border-white/20 rounded-2xl mb-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center space-x-2">
            <svg class="w-5 h-5 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            <span>Filter Report by Date Range</span>
        </h3>
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <input type="hidden" name="url" value="report">
            <div>
                <label for="start_date" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Start Date</label>
                <input 
                    type="date" 
                    name="start_date" 
                    id="start_date" 
                    class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 focus:border-primary-500 focus:outline-none transition-all duration-200" 
                    value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>"
                >
            </div>
            <div>
                <label for="end_date" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">End Date</label>
                <input 
                    type="date" 
                    name="end_date" 
                    id="end_date" 
                    class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 focus:border-primary-500 focus:outline-none transition-all duration-200" 
                    value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>"
                >
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-primary-700 hover:bg-primary-800 text-white font-bold py-2.5 px-4 rounded-xl shadow-md transition-all duration-200">
                    Apply Filter
                </button>
                <?php if (!empty($_GET['start_date']) || !empty($_GET['end_date'])): ?>
                    <a href="?url=report" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 px-4 rounded-xl transition-all duration-200 text-center flex items-center justify-center border border-gray-200">
                        Clear
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- 3 Colored Stat Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Poojas (Orange) -->
        <div class="glass-card bg-gradient-to-br from-orange-500 to-orange-600 p-6 card-hover shadow-xl border border-white/10 rounded-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/90 text-sm font-semibold uppercase tracking-wide">Pooja Ceremonies</p>
                    <p class="text-4xl font-extrabold text-white mt-2"><?= $poojas ?? 0 ?></p>
                    <p class="text-white/70 text-xs mt-1">Total in selected timeframe</p>
                </div>
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Total Donations (Blue) -->
        <div class="glass-card bg-gradient-to-br from-accent-500 to-accent-600 p-6 card-hover shadow-xl border border-white/10 rounded-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/90 text-sm font-semibold uppercase tracking-wide">Total Donations</p>
                    <p class="text-4xl font-extrabold text-white mt-2">$<?= number_format($donations ?? 0, 2) ?></p>
                    <p class="text-white/70 text-xs mt-1">Total in selected timeframe</p>
                </div>
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Events Held (Yellow) -->
        <div class="glass-card bg-gradient-to-br from-yellow-500 to-yellow-600 p-6 card-hover shadow-xl border border-white/10 rounded-2xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-white/90 text-sm font-semibold uppercase tracking-wide">Pooja Bookings</p>
                    <p class="text-4xl font-extrabold text-white mt-2"><?= $bookings ?? 0 ?></p>
                    <p class="text-white/70 text-xs mt-1">Confirmed booking slots</p>
                </div>
                <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bars Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pooja Bookings Progress -->
        <div class="glass-card p-6 bg-white shadow-xl border border-gray-100 rounded-2xl card-hover">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Pooja Bookings Target</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-700">Monthly Goal</span>
                        <span class="text-sm font-bold text-primary-600">75%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-400 to-orange-600 h-full rounded-full transition-all duration-500" style="width: 75%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-700">Weekly Target</span>
                        <span class="text-sm font-bold text-green-600">92%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-400 to-green-600 h-full rounded-full transition-all duration-500" style="width: 92%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donation Collection Progress -->
        <div class="glass-card p-6 bg-white shadow-xl border border-gray-100 rounded-2xl card-hover">
            <h3 class="text-lg font-bold text-gray-800 mb-4">Donation Collection</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-700">Monthly Target</span>
                        <span class="text-sm font-bold text-accent-600">68%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-accent-400 to-accent-600 h-full rounded-full transition-all duration-500" style="width: 68%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-semibold text-gray-700">Festival Special</span>
                        <span class="text-sm font-bold text-purple-600">85%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-400 to-purple-600 h-full rounded-full transition-all duration-500" style="width: 85%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Poojas List -->
    <div class="glass-card p-6 bg-white shadow-xl border border-gray-100 rounded-2xl card-hover">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Popular Poojas</h3>
        <div class="space-y-3">
            <?php
            $popularPoojas = [
                ['name' => 'Morning Pooja', 'count' => 156, 'percentage' => 92],
                ['name' => 'Evening Aarti', 'count' => 134, 'percentage' => 88],
                ['name' => 'Abhishekam', 'count' => 98, 'percentage' => 75],
                ['name' => 'Archana', 'count' => 87, 'percentage' => 68],
            ];
            foreach($popularPoojas as $index => $pooja):
            ?>
            <div class="flex items-center space-x-4">
                <div class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center text-white font-bold text-sm">
                    <?= $index + 1 ?>
                </div>
                <div class="flex-1">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-semibold text-gray-800"><?= $pooja['name'] ?></span>
                        <span class="text-sm text-gray-600"><?= $pooja['count'] ?> bookings</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                        <div class="bg-gradient-to-r from-secondary-400 to-secondary-600 h-full rounded-full transition-all duration-500" style="width: <?= $pooja['percentage'] ?>%"></div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
