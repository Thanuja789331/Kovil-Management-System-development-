<!-- Festivals Page - 2 Column Grid -->
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
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Festivals & Events</h1>
        <p class="text-gray-600">Celebrate divine occasions together</p>
    </div>

    <!-- Festivals Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <?php 
        // Convert mysqli_result to array
        $festivals = [];
        if ($data instanceof mysqli_result) {
            $festivals = $data->fetch_all(MYSQLI_ASSOC);
        } elseif (is_array($data)) {
            $festivals = $data;
        }
        ?>
        <?php if(!empty($festivals)): ?>
            <?php foreach($festivals as $festival): ?>
            <div class="glass-card p-6 card-hover border-l-4 border-primary-600">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                    
                    <!-- Badge -->
                    <span class="px-3 py-1 bg-gradient-to-r from-primary-500 to-primary-600 text-white text-xs font-semibold rounded-full uppercase tracking-wide">
                        Festival
                    </span>
                </div>
                
                <h3 class="text-xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($festival['name']) ?></h3>
                
                <div class="flex items-center space-x-2 text-sm text-gray-600 mb-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-semibold"><?= date('F d, Y', strtotime($festival['date'])) ?></span>
                </div>
                
                <?php if(!empty($festival['description'])): ?>
                <p class="text-gray-600 text-sm leading-relaxed"><?= htmlspecialchars($festival['description']) ?></p>
                <?php endif; ?>
                
                <div class="mt-4 pt-4 border-t border-gray-200 flex justify-end">
                    <button class="text-primary-600 hover:text-primary-700 font-semibold text-sm transition-colors">
                        Learn More →
                    </button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-span-full glass-card p-12 text-center">
                <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
                <p class="text-gray-500 text-lg">No upcoming festivals at the moment</p>
            </div>
        <?php endif; ?>
    </div>
</div>
