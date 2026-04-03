<!-- Priest Dashboard - My Duties -->
<div class="min-h-[calc(100vh-8rem)] py-12 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Welcome Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">🙏 My Assigned Duties</h1>
            <p class="text-gray-600 font-medium">Welcome, <?= htmlspecialchars($_SESSION['user']['name']) ?></p>
        </div>

        <?php if($data && $data->num_rows > 0): ?>
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="glass-card p-6 card-hover bg-gradient-to-br from-blue-500 to-blue-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-semibold mb-1">Total Assignments</p>
                        <p class="text-white text-4xl font-bold"><?= $data->num_rows ?></p>
                    </div>
                    <svg class="w-16 h-16 text-blue-300 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>

            <div class="glass-card p-6 card-hover bg-gradient-to-br from-green-500 to-green-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-semibold mb-1">Completed</p>
                        <p class="text-white text-4xl font-bold">
                            <?php
                            $data->data_seek(0);
                            $completed = 0;
                            while($duty = $data->fetch_assoc()) {
                                if($duty['status'] === 'completed') $completed++;
                            }
                            echo $completed;
                            ?>
                        </p>
                    </div>
                    <svg class="w-16 h-16 text-green-300 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>

            <div class="glass-card p-6 card-hover bg-gradient-to-br from-yellow-500 to-yellow-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm font-semibold mb-1">Upcoming</p>
                        <p class="text-white text-4xl font-bold">
                            <?php
                            $data->data_seek(0);
                            $upcoming = 0;
                            while($duty = $data->fetch_assoc()) {
                                if(in_array($duty['status'], ['assigned'])) $upcoming++;
                            }
                            echo $upcoming;
                            ?>
                        </p>
                    </div>
                    <svg class="w-16 h-16 text-yellow-300 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Duties List -->
        <div class="space-y-4">
            <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                <svg class="w-8 h-8 mr-2 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                All Assigned Poojas
            </h2>

            <?php 
            $data->data_seek(0);
            while($duty = $data->fetch_assoc()): 
                $isToday = date('Y-m-d') == $duty['pooja_date'];
                $isPast = date('Y-m-d') > $duty['pooja_date'];
            ?>
            <div class="glass-card p-6 card-hover <?= $isToday ? 'ring-4 ring-accent-400' : '' ?>">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <!-- Left Side - Pooja Info -->
                    <div class="mb-4 md:mb-0">
                        <div class="flex items-center space-x-3 mb-2">
                            <h3 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($duty['pooja_name']) ?></h3>
                            <?php if($isToday): ?>
                            <span class="bg-accent-500 text-white px-3 py-1 rounded-full text-xs font-bold animate-pulse">
                                TODAY
                            </span>
                            <?php endif; ?>
                            <?php if($isPast): ?>
                            <span class="bg-gray-500 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                COMPLETED
                            </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="space-y-2">
                            <div class="flex items-center text-gray-700">
                                <svg class="w-5 h-5 mr-2 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="font-medium"><?= date("F j, Y", strtotime($duty['pooja_date'])) ?></span>
                            </div>
                            
                            <div class="flex items-center text-gray-700">
                                <svg class="w-5 h-5 mr-2 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-medium"><?= htmlspecialchars($duty['time_slot']) ?></span>
                            </div>
                            
                            <?php if(!empty($duty['description'])): ?>
                            <div class="flex items-start text-gray-700">
                                <svg class="w-5 h-5 mr-2 mt-0.5 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="text-sm"><?= htmlspecialchars($duty['description']) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Right Side - Status & Actions -->
                    <div class="flex flex-col items-end space-y-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600 font-semibold">Status:</span>
                            <span class="px-4 py-2 rounded-full text-sm font-bold
                                <?= $duty['status'] === 'completed' ? 'bg-green-500 text-white' : 
                                   ($duty['status'] === 'cancelled' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white') ?>">
                                <?= ucfirst($duty['status']) ?>
                            </span>
                        </div>

                        <div class="flex items-center space-x-2">
                            <span class="text-xs text-gray-500">Assigned on:</span>
                            <span class="text-xs text-gray-700 font-medium"><?= date("M j, Y", strtotime($duty['assigned_date'])) ?></span>
                        </div>

                        <?php if(!$isPast && $duty['status'] === 'assigned'): ?>
                        <button onclick="markAsComplete(<?= $duty['id'] ?>)" 
                                class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-semibold transition-all duration-200 flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span>Mark as Complete</span>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <?php else: ?>
        <!-- Empty State -->
        <div class="glass-card p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            <h3 class="text-2xl font-bold text-gray-800 mb-3">No Duties Assigned Yet</h3>
            <p class="text-gray-600 max-w-md mx-auto">You haven't been assigned to any poojas yet. The admin will assign duties soon.</p>
        </div>
        <?php endif; ?>

        <!-- Quick Links -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <a href="?url=priest-schedules" 
               class="glass-card p-6 card-hover bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 text-white block transform transition-all duration-200 hover:scale-105 shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold mb-2">📅 View All Schedules & Festivals</h3>
                        <p class="text-indigo-100 text-sm">Browse upcoming poojas and temple events</p>
                    </div>
                    <svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                </div>
            </a>

            <a href="?url=schedule" 
               class="glass-card p-6 card-hover bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-500 text-white block transform transition-all duration-200 hover:scale-105 shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold mb-2">🛕 Book Pooja (Devotee)</h3>
                        <p class="text-emerald-100 text-sm">Book a pooja as a devotee</p>
                    </div>
                    <svg class="w-12 h-12 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
function markAsComplete(dutyId) {
    if (confirm('Are you sure you want to mark this duty as completed?')) {
        // In a real implementation, this would make an AJAX call or redirect to a confirmation page
        alert('Duty marked as complete! (This is a demo - actual implementation would update the database)');
        // window.location.href = '?url=priest&action=complete&id=' + dutyId;
    }
}
</script>
