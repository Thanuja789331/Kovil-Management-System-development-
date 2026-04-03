<!-- Schedule Page - Card List Layout -->
<div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center space-x-4 mb-4">
        <a href="javascript:history.back()" class="group flex items-center space-x-2 bg-white/80 hover:bg-white backdrop-blur-sm px-4 py-2 rounded-xl shadow-md transition-all duration-200 hover:scale-105 active:scale-95">
            <svg class="w-5 h-5 text-secondary-600 group-hover:text-secondary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="font-semibold text-secondary-600 group-hover:text-secondary-700">Back</span>
        </a>
    </div>

    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Pooja Schedule</h1>
        <p class="text-gray-600">View all upcoming pooja ceremonies</p>
    </div>

    <!-- Schedule List -->
    <div class="space-y-4">
        <?php 
        // Convert mysqli_result to array
        $schedules = [];
        if ($data instanceof mysqli_result) {
            $schedules = $data->fetch_all(MYSQLI_ASSOC);
        } elseif (is_array($data)) {
            $schedules = $data;
        }
        ?>
        <?php if(!empty($schedules)): ?>
            <?php foreach($schedules as $schedule): 
                $isBooked = ($schedule['status'] === 'booked');
                $hasBookingInfo = isset($schedule['booked_by_name']);
            ?>
            <div class="glass-card p-6 card-hover <?= $isBooked ? 'opacity-75 bg-gray-50' : '' ?>">
                <div class="flex items-start justify-between">
                    <div class="flex items-start space-x-4 flex-1">
                        <!-- Icon Container -->
                        <div class="w-14 h-14 bg-gradient-to-br from-secondary-400 to-secondary-600 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        
                        <!-- Content -->
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($schedule['pooja_name']) ?></h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                <div class="flex items-center space-x-2 text-sm text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="font-medium"><?= date('F d, Y', strtotime($schedule['pooja_date'])) ?></span>
                                </div>
                                
                                <div class="flex items-center space-x-2 text-sm text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium"><?= date('g:i A', strtotime($schedule['time_slot'])) ?></span>
                                </div>
                                
                                <?php if(isset($schedule['priest_name'])): ?>
                                <div class="flex items-center space-x-2 text-sm text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span>Priest: <?= htmlspecialchars($schedule['priest_name']) ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if($isBooked && $hasBookingInfo): ?>
                                <div class="flex items-center space-x-2 text-sm text-green-700 bg-green-50 px-3 py-2 rounded-lg">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="font-semibold">Booked by <?= htmlspecialchars($schedule['booked_by_name']) ?></span>
                                </div>
                                <?php elseif($isBooked): ?>
                                <div class="flex items-center space-x-2 text-sm text-orange-700 bg-orange-50 px-3 py-2 rounded-lg">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="font-semibold">Already Booked</span>
                                </div>
                                <?php else: ?>
                                <?php if(isset($schedule['attendees'])): ?>
                                <div class="flex items-center space-x-2 text-sm text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span><?= $schedule['attendees'] ?> attendees</span>
                                </div>
                                <?php endif; ?>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Status Badge -->
                            <?php if($isBooked): ?>
                            <span class="inline-block px-4 py-1.5 bg-red-100 text-red-700 text-xs font-semibold rounded-full uppercase tracking-wide border-2 border-red-200">
                                🔒 Booked - <?= date('M d, Y', strtotime($schedule['created_at'])) ?>
                            </span>
                            <?php else: ?>
                            <span class="inline-block px-4 py-1.5 bg-green-100 text-green-700 text-xs font-semibold rounded-full uppercase tracking-wide">
                                <?= ucfirst($schedule['status']) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    <div class="ml-4 flex items-center space-x-2">
                        <?php if($isBooked): ?>
                        <button disabled class="bg-gray-300 text-gray-500 px-5 py-2.5 rounded-xl font-semibold cursor-not-allowed inline-block">
                            🔒 Unavailable
                        </button>
                        <?php else: ?>
                        <a href="?url=book&id=<?= $schedule['id'] ?>" class="bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-400 hover:to-secondary-500 text-white px-5 py-2.5 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg inline-block">
                            Book Now
                        </a>
                        <?php endif; ?>
                        
                        <?php if(isset($_SESSION['user']) && $_SESSION['user']['role'] === 'management'): ?>
                        <a href="?url=schedule&action=edit&id=<?= $schedule['id'] ?>" class="bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-400 hover:to-yellow-500 text-white px-4 py-2.5 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg inline-block">
                            Edit
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="glass-card p-12 text-center">
                <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-gray-500 text-lg">No pooja schedules available at the moment</p>
                <p class="text-gray-400 mt-2 text-sm">Check back later for upcoming ceremonies</p>
            </div>
        <?php endif; ?>
    </div>
</div>
