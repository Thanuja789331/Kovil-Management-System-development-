<!-- Priest Dashboard - My Duties -->
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
        <h1 class="text-3xl font-bold text-gray-800 mb-2">My Duties</h1>
        <p class="text-gray-600">Your assigned pooja responsibilities</p>
    </div>

    <!-- Duties List -->
    <div class="space-y-4">
        <?php 
        // Convert mysqli_result to array
        $duties = [];
        if ($data instanceof mysqli_result) {
            $duties = $data->fetch_all(MYSQLI_ASSOC);
        } elseif (is_array($data)) {
            $duties = $data;
        }
        ?>
        <?php if(!empty($duties)): ?>
            <?php foreach($duties as $duty): ?>
            <div class="glass-card p-6 card-hover border-l-4 border-primary-600">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4 flex-1">
                        <!-- Icon -->
                        <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($duty['pooja_name'] ?? 'Assigned Pooja') ?></h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div class="flex items-center space-x-2 text-sm text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="font-medium">
                                        <?php if(isset($duty['pooja_date']) && !empty($duty['pooja_date'])): ?>
                                            <?= date('F d, Y', strtotime($duty['pooja_date'])) ?>
                                        <?php else: ?>
                                            <span class="text-gray-400">TBD</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                                
                                <div class="flex items-center space-x-2 text-sm text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium">
                                        <?php if(isset($duty['time_slot']) && !empty($duty['time_slot'])): ?>
                                            <?= date('g:i A', strtotime($duty['time_slot'])) ?>
                                        <?php else: ?>
                                            <span class="text-gray-400">TBD</span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Status -->
                            <div class="mt-3">
                                <span class="inline-block px-4 py-1.5 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full uppercase tracking-wide">
                                    Assigned
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="ml-4 flex space-x-2">
                        <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg">
                            Accept
                        </button>
                        <button class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-xl font-semibold transition-all duration-200">
                            Decline
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="glass-card p-12 text-center">
                <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-500 text-lg">No duties assigned at the moment</p>
                <p class="text-gray-400 mt-2 text-sm">You'll be notified when new poojas are assigned</p>
            </div>
        <?php endif; ?>
    </div>
</div>
