<!-- Pooja History Page -->
<div class="min-h-[calc(100vh-8rem)] py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header with Export Button -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">📜 Pooja History</h1>
                <p class="text-gray-600 font-medium">View all scheduled and completed poojas</p>
            </div>
            <?php if($_SESSION['user']['role'] === 'management'): ?>
            <a href="?url=export-poojas" class="bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-semibold py-3 px-6 rounded-xl shadow-lg transition-all transform hover:scale-105 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Export CSV</span>
            </a>
            <?php endif; ?>
        </div>

        <!-- Analytics Section (Admin Only) -->
        <?php if($_SESSION['user']['role'] === 'management'): ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Popular Poojas -->
            <div class="glass-card p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                    Most Popular Poojas
                </h3>
                <?php if($analytics['popularPoojas'] && $analytics['popularPoojas']->num_rows > 0): ?>
                <div class="space-y-3">
                    <?php while($pooja = $analytics['popularPoojas']->fetch_assoc()): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-800 font-medium"><?= htmlspecialchars($pooja['pooja_name']) ?></span>
                        <span class="px-3 py-1 bg-accent-100 text-accent-700 text-xs font-semibold rounded-full"><?= $pooja['booking_count'] ?> bookings</span>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <p class="text-gray-500 text-sm">No booking data yet</p>
                <?php endif; ?>
            </div>

            <!-- Busiest Time Slots -->
            <div class="glass-card p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Busiest Time Slots
                </h3>
                <?php if($analytics['busyTimeSlots'] && $analytics['busyTimeSlots']->num_rows > 0): ?>
                <div class="space-y-3">
                    <?php while($slot = $analytics['busyTimeSlots']->fetch_assoc()): ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <span class="text-gray-800 font-medium"><?= htmlspecialchars($slot['formatted_time']) ?></span>
                        <span class="px-3 py-1 bg-secondary-100 text-secondary-700 text-xs font-semibold rounded-full"><?= $slot['booking_count'] ?> bookings</span>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <p class="text-gray-500 text-sm">No time slot data yet</p>
                <?php endif; ?>
            </div>

            <!-- Monthly Trends -->
            <div class="glass-card p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    Monthly Trends
                </h3>
                <?php if($analytics['monthlyTrends'] && $analytics['monthlyTrends']->num_rows > 0): ?>
                <div class="space-y-3">
                    <?php while($month = $analytics['monthlyTrends']->fetch_assoc()): ?>
                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-800 font-semibold"><?= htmlspecialchars($month['formatted_month']) ?></span>
                            <span class="text-sm text-gray-600"><?= $month['total_poojas'] ?> poojas</span>
                        </div>
                        <div class="flex items-center space-x-2 text-xs">
                            <span class="px-2 py-1 bg-green-100 text-green-700 rounded">Available: <?= $month['available_count'] ?></span>
                            <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded">Booked: <?= $month['booked_count'] ?></span>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <p class="text-gray-500 text-sm">No monthly data yet</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($message)): ?>
        <div class="mb-6 p-4 rounded-xl shadow-lg <?= $messageType === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Poojas -->
            <div class="glass-card p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold mb-1">Total Poojas</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $stats['total'] ?? 0 ?></h3>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Available -->
            <div class="glass-card p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold mb-1">Available</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $stats['available'] ?? 0 ?></h3>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-700 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Booked -->
            <div class="glass-card p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold mb-1">Booked</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $stats['booked'] ?? 0 ?></h3>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Completed -->
            <div class="glass-card p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold mb-1">Completed</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $stats['completed'] ?? 0 ?></h3>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-accent-500 to-accent-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="glass-card p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <input 
                        type="text" 
                        id="searchInput" 
                        placeholder="🔍 Search by pooja name..." 
                        class="w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl outline-none"
                        onkeyup="filterTable()"
                    >
                </div>
                <div class="flex gap-3">
                    <select 
                        id="statusFilter" 
                        onchange="filterTable()"
                        class="px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl outline-none cursor-pointer"
                    >
                        <option value="">All Status</option>
                        <option value="available">Available</option>
                        <option value="booked">Booked</option>
                        <option value="completed">Completed</option>
                    </select>
                    <select 
                        id="dateFilter" 
                        onchange="filterTable()"
                        class="px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl outline-none cursor-pointer"
                    >
                        <option value="">All Dates</option>
                        <option value="past">Past</option>
                        <option value="today">Today</option>
                        <option value="future">Future</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Pooja History Table -->
        <div class="glass-card p-6 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                All Pooja Schedule
            </h2>

            <?php if($data && $data->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left" id="poojaTable">
                    <thead>
                        <tr class="border-b-2 border-gray-300">
                            <th class="py-3 px-4 text-gray-700 font-semibold">#</th>
                            <th class="py-3 px-4 text-gray-700 font-semibold">Pooja Name</th>
                            <th class="py-3 px-4 text-gray-700 font-semibold">Date</th>
                            <th class="py-3 px-4 text-gray-700 font-semibold">Time Slot</th>
                            <th class="py-3 px-4 text-gray-700 font-semibold">Status</th>
                            <th class="py-3 px-4 text-gray-700 font-semibold">Booked By</th>
                            <th class="py-3 px-4 text-gray-700 font-semibold">Created On</th>
                            <?php if($_SESSION['user']['role'] === 'management'): ?>
                            <th class="py-3 px-4 text-gray-700 font-semibold text-center">Actions</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $counter = 1;
                        while($pooja = $data->fetch_assoc()): 
                            $isPast = strtotime($pooja['pooja_date']) < strtotime('today');
                            $isToday = date('Y-m-d') == $pooja['pooja_date'];
                        ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors" 
                            data-status="<?= $pooja['status'] ?>" 
                            data-date="<?= $isPast ? 'past' : ($isToday ? 'today' : 'future') ?>">
                            <td class="py-3 px-4 text-gray-600 font-medium"><?= $counter++ ?></td>
                            <td class="py-3 px-4">
                                <span class="text-gray-800 font-semibold"><?= htmlspecialchars($pooja['pooja_name']) ?></span>
                                <?php if(!empty($pooja['description'])): ?>
                                <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars(substr($pooja['description'], 0, 50)) ?>...</p>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4">
                                <div>
                                    <p class="text-gray-800 font-medium"><?= date('M d, Y', strtotime($pooja['pooja_date'])) ?></p>
                                    <p class="text-xs text-gray-500"><?= date('l', strtotime($pooja['pooja_date'])) ?></p>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-gray-800 font-medium"><?= date('g:i A', strtotime($pooja['time_slot'])) ?></span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    <?= $pooja['status'] === 'available' ? 'bg-green-100 text-green-700' : 
                                       ($pooja['status'] === 'booked' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700') ?>">
                                    <?= ucfirst($pooja['status']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <?php if($pooja['status'] === 'booked' && !empty($pooja['booked_by_name'])): ?>
                                <div class="flex items-center space-x-2">
                                    <div class="w-6 h-6 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-xs"><?= strtoupper(substr($pooja['booked_by_name'], 0, 1)) ?></span>
                                    </div>
                                    <span class="text-gray-800 text-sm"><?= htmlspecialchars($pooja['booked_by_name']) ?></span>
                                </div>
                                <?php else: ?>
                                <span class="text-gray-400 text-sm">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-gray-800"><?= date('M j, Y g:i A', strtotime($pooja['created_at'])) ?></td>
                            <?php if($_SESSION['user']['role'] === 'management'): ?>
                            <td class="py-3 px-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="?url=schedule&action=edit&id=<?= $pooja['id'] ?>" 
                                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded-lg transition-colors text-sm font-medium">
                                        Edit
                                    </a>
                                    <?php if($pooja['status'] !== 'completed'): ?>
                                    <button onclick="deletePooja(<?= $pooja['id'] ?>)" 
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-lg transition-colors text-sm font-medium">
                                        Delete
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-12">
                <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-gray-600 text-lg font-medium">No poojas scheduled yet</p>
                <?php if($_SESSION['user']['role'] === 'management'): ?>
                <a href="?url=schedule&action=add" class="inline-block mt-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-2 rounded-xl font-semibold hover:from-primary-700 hover:to-primary-800 transition-all">
                    + Add First Pooja
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Pagination Controls -->
        <?php if($totalPages > 1): ?>
        <div class="flex items-center justify-between mt-6">
            <div class="text-sm text-gray-600">
                Showing page <span class="font-semibold"><?= $page ?></span> of <span class="font-semibold"><?= $totalPages ?></span>
                (<?= $totalPoojas ?> total poojas)
            </div>
            <div class="flex items-center space-x-2">
                <!-- Previous Button -->
                <?php if($page > 1): ?>
                <a href="?url=pooja-history&page=<?= $page - 1 ?>" 
                   class="px-4 py-2 bg-white hover:bg-gray-50 border-2 border-gray-300 rounded-xl font-medium transition-all">
                    ← Previous
                </a>
                <?php endif; ?>

                <!-- Page Numbers -->
                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if($i == $page): ?>
                    <span class="px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold rounded-xl">
                        <?= $i ?>
                    </span>
                    <?php elseif($i == 1 || $i == $totalPages || ($i >= $page - 2 && $i <= $page + 2)): ?>
                    <a href="?url=pooja-history&page=<?= $i ?>" 
                       class="px-4 py-2 bg-white hover:bg-gray-50 border-2 border-gray-300 rounded-xl font-medium transition-all">
                        <?= $i ?>
                    </a>
                    <?php else: ?>
                    <span class="px-2 py-2 text-gray-400">...</span>
                    <?php endif; ?>
                <?php endfor; ?>

                <!-- Next Button -->
                <?php if($page < $totalPages): ?>
                <a href="?url=pooja-history&page=<?= $page + 1 ?>" 
                   class="px-4 py-2 bg-white hover:bg-gray-50 border-2 border-gray-300 rounded-xl font-medium transition-all">
                    Next →
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
function filterTable() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;
    
    const rows = document.querySelectorAll('#poojaTable tbody tr');
    
    rows.forEach(row => {
        const name = row.cells[1].textContent.toLowerCase();
        const status = row.getAttribute('data-status');
        const date = row.getAttribute('data-date');
        
        const matchesSearch = name.includes(searchInput);
        const matchesStatus = !statusFilter || status === statusFilter;
        const matchesDate = !dateFilter || date === dateFilter;
        
        if (matchesSearch && matchesStatus && matchesDate) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

<?php if($_SESSION['user']['role'] === 'management'): ?>
function deletePooja(id) {
    if (confirm('Are you sure you want to delete this pooja? This action cannot be undone.')) {
        window.location.href = `?url=schedule&action=delete&id=${id}`;
    }
}
<?php endif; ?>
</script>
