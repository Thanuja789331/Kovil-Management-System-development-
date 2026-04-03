<!-- Priest Dashboard - Schedules & Festivals -->
<div class="min-h-[calc(100vh-8rem)] py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">📅 Pooja Schedules & Festivals</h1>
            <p class="text-gray-600 font-medium">View all upcoming poojas and temple festivals</p>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex space-x-4 mb-6">
            <button onclick="showSection('schedules')" 
                    id="schedulesTab"
                    class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition-all duration-200 hover:scale-105">
                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Pooja Schedules
            </button>
            <button onclick="showSection('festivals')" 
                    id="festivalsTab"
                    class="flex-1 bg-gradient-to-r from-purple-600 to-pink-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg transition-all duration-200 hover:scale-105">
                <svg class="w-6 h-6 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
                Festivals
            </button>
        </div>

        <!-- Schedules Section -->
        <div id="schedulesSection" class="space-y-4">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <svg class="w-8 h-8 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                All Pooja Schedules
            </h2>

            <?php if($schedules && $schedules->num_rows > 0): ?>
                <?php 
                $schedules->data_seek(0);
                while($schedule = $schedules->fetch_assoc()): 
                    $isAssigned = !empty($schedule['assigned_priest_name']);
                    $isToday = date('Y-m-d') == $schedule['pooja_date'];
                ?>
                <div class="glass-card p-6 card-hover <?= $isToday ? 'ring-4 ring-blue-400' : '' ?>">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                        <!-- Left Side - Pooja Info -->
                        <div class="mb-4 md:mb-0">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($schedule['pooja_name']) ?></h3>
                                <?php if($isToday): ?>
                                <span class="bg-blue-500 text-white px-3 py-1 rounded-full text-xs font-bold animate-pulse">
                                    TODAY
                                </span>
                                <?php endif; ?>
                                <?php if($isAssigned): ?>
                                <span class="bg-green-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    ✓ Assigned
                                </span>
                                <?php else: ?>
                                <span class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                    ⏳ Unassigned
                                </span>
                                <?php endif; ?>
                            </div>
                            
                            <div class="space-y-2">
                                <div class="flex items-center text-gray-700">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="font-medium"><?= date("F j, Y", strtotime($schedule['pooja_date'])) ?></span>
                                </div>
                                
                                <div class="flex items-center text-gray-700">
                                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="font-medium"><?= htmlspecialchars($schedule['time_slot']) ?></span>
                                </div>
                                
                                <?php if(!empty($schedule['description'])): ?>
                                <div class="flex items-start text-gray-700">
                                    <svg class="w-5 h-5 mr-2 mt-0.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="text-sm"><?= htmlspecialchars($schedule['description']) ?></span>
                                </div>
                                <?php endif; ?>

                                <?php if($isAssigned): ?>
                                <div class="flex items-center text-gray-700 mt-2">
                                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="font-medium">Assigned to: <?= htmlspecialchars($schedule['assigned_priest_name']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Right Side - Status -->
                        <div class="flex flex-col items-end space-y-3">
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600 font-semibold">Status:</span>
                                <span class="px-4 py-2 rounded-full text-sm font-bold
                                    <?= $schedule['status'] === 'booked' ? 'bg-red-500 text-white' : 
                                       ($schedule['status'] === 'available' ? 'bg-green-500 text-white' : 'bg-gray-500 text-white') ?>">
                                    <?= ucfirst($schedule['status']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="glass-card p-12 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">No Schedules Found</h3>
                    <p class="text-gray-600">There are no upcoming pooja schedules at the moment.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Festivals Section (Hidden by default) -->
        <div id="festivalsSection" class="hidden space-y-4">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <svg class="w-8 h-8 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
                Upcoming Festivals
            </h2>

            <?php if($festivals && $festivals->num_rows > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php while($festival = $festivals->fetch_assoc()): ?>
                    <div class="glass-card p-6 card-hover">
                        <?php if(!empty($festival['image_url'])): ?>
                        <img src="<?= htmlspecialchars($festival['image_url']) ?>" 
                             alt="<?= htmlspecialchars($festival['name']) ?>"
                             class="w-full h-48 object-cover rounded-lg mb-4">
                        <?php else: ?>
                        <div class="w-full h-48 bg-gradient-to-br from-purple-600 via-pink-600 to-indigo-600 rounded-lg mb-4 flex items-center justify-center">
                            <svg class="w-20 h-20 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                            </svg>
                        </div>
                        <?php endif; ?>
                        
                        <h3 class="text-xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($festival['name']) ?></h3>
                        
                        <div class="flex items-center text-gray-700 mb-3">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <span class="font-semibold"><?= date("F j, Y", strtotime($festival['date'])) ?></span>
                        </div>
                        
                        <?php if(!empty($festival['description'])): ?>
                        <p class="text-gray-600 text-sm"><?= htmlspecialchars($festival['description']) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="glass-card p-12 text-center">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">No Festivals Found</h3>
                    <p class="text-gray-600">There are no upcoming festivals at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function showSection(section) {
    const schedulesSection = document.getElementById('schedulesSection');
    const festivalsSection = document.getElementById('festivalsSection');
    const schedulesTab = document.getElementById('schedulesTab');
    const festivalsTab = document.getElementById('festivalsTab');
    
    if (section === 'schedules') {
        schedulesSection.classList.remove('hidden');
        festivalsSection.classList.add('hidden');
        schedulesTab.classList.add('from-blue-600', 'to-blue-700');
        schedulesTab.classList.remove('from-gray-600', 'to-gray-700');
        festivalsTab.classList.remove('from-purple-600', 'to-purple-700');
        festivalsTab.classList.add('from-gray-600', 'to-gray-700');
    } else {
        festivalsSection.classList.remove('hidden');
        schedulesSection.classList.add('hidden');
        festivalsTab.classList.add('from-purple-600', 'to-purple-700');
        festivalsTab.classList.remove('from-gray-600', 'to-gray-700');
        schedulesTab.classList.remove('from-blue-600', 'to-blue-700');
        schedulesTab.classList.add('from-gray-600', 'to-gray-700');
    }
}
</script>
