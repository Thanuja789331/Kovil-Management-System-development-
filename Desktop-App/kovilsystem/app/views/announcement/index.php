<!-- Announcements Page - Card List -->
<div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center space-x-4 mb-4">
        <a href="javascript:history.back()" class="group flex items-center space-x-2 bg-white/80 hover:bg-white backdrop-blur-sm px-4 py-2 rounded-xl shadow-md transition-all duration-200 hover:scale-105 active:scale-95">
            <svg class="w-5 h-5 text-orange-600 group-hover:text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="font-semibold text-orange-600 group-hover:text-orange-700">Back</span>
        </a>
    </div>

    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Announcements</h1>
        <p class="text-gray-600">Latest updates and notifications from the temple</p>
    </div>

    <!-- Announcements List -->
    <div class="space-y-4">
        <?php 
        // Convert mysqli_result to array
        $announcements = [];
        if ($data instanceof mysqli_result) {
            $announcements = $data->fetch_all(MYSQLI_ASSOC);
        } elseif (is_array($data)) {
            $announcements = $data;
        }
        ?>
        <?php if(!empty($announcements)): ?>
            <?php foreach($announcements as $announcement): ?>
            <div class="glass-card p-6 card-hover border-l-4 border-orange-500">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4 flex-1">
                        <!-- Icon -->
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-400 to-orange-600 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                            </svg>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($announcement['title']) ?></h3>
                            <p class="text-gray-600 mb-3"><?= htmlspecialchars($announcement['message']) ?></p>
                            <div class="flex items-center space-x-2 text-sm text-gray-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span><?= date('F d, Y', strtotime($announcement['date'])) ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Date Badge (Right Side) -->
                    <div class="hidden md:flex flex-col items-center justify-center bg-gray-100 rounded-xl px-4 py-2 ml-4">
                        <span class="text-xs font-semibold text-gray-600 uppercase"><?= date('M', strtotime($announcement['date'])) ?></span>
                        <span class="text-2xl font-bold text-gray-800"><?= date('d', strtotime($announcement['date'])) ?></span>
                        <span class="text-xs text-gray-500"><?= date('Y', strtotime($announcement['date'])) ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="glass-card p-12 text-center">
                <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                </svg>
                <p class="text-gray-500 text-lg">No announcements at the moment</p>
            </div>
        <?php endif; ?>
    </div>
</div>
