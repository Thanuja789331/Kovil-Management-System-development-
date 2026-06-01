<!-- Pooja History Page -->
<?php 
$searchQuery = !empty($_GET['search']) ? '&search=' . urlencode($_GET['search']) : '';
$statusQuery = !empty($_GET['status']) ? '&status=' . urlencode($_GET['status']) : '';
$dateQuery = !empty($_GET['date_filter']) ? '&date_filter=' . urlencode($_GET['date_filter']) : '';
$paginationParams = $searchQuery . $statusQuery . $dateQuery;
?>

<div class="min-h-[calc(100vh-8rem)] py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header with Export Button -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-4xl font-extrabold text-white mb-2 drop-shadow-md">📜 <?= trans('pooja_history') ?? 'Pooja History & Schedule' ?></h1>
                <p class="text-emerald-100 font-medium drop-shadow-sm">Search, filter, and manage devotee bookings across all schedules</p>
            </div>
            <?php if($_SESSION['user']['role'] === 'management'): ?>
            <a href="?url=export-poojas<?= $paginationParams ?>" class="bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition-all transform hover:scale-105 active:scale-95 flex items-center space-x-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                <span>Export CSV</span>
            </a>
            <?php endif; ?>
        </div>

        <!-- Analytics Section (Admin Only) -->
        <?php if($_SESSION['user']['role'] === 'management'): ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Popular Poojas -->
            <div class="glass-card p-6 bg-white/95 shadow-xl border border-white/20 rounded-2xl">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                    Most Popular Poojas
                </h3>
                <?php if($analytics['popularPoojas'] && $analytics['popularPoojas']->num_rows > 0): ?>
                <div class="space-y-3">
                    <?php 
                    $analytics['popularPoojas']->data_seek(0);
                    while($pooja = $analytics['popularPoojas']->fetch_assoc()): 
                    ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="text-gray-800 font-semibold text-sm"><?= htmlspecialchars($pooja['pooja_name']) ?></span>
                        <span class="px-3 py-1 bg-accent-100 text-accent-700 text-[10px] font-black rounded-full uppercase tracking-wider"><?= $pooja['booking_count'] ?> bookings</span>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <p class="text-gray-500 text-sm">No booking data yet</p>
                <?php endif; ?>
            </div>

            <!-- Busiest Time Slots -->
            <div class="glass-card p-6 bg-white/95 shadow-xl border border-white/20 rounded-2xl">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Busiest Time Slots
                </h3>
                <?php if($analytics['busyTimeSlots'] && $analytics['busyTimeSlots']->num_rows > 0): ?>
                <div class="space-y-3">
                    <?php 
                    $analytics['busyTimeSlots']->data_seek(0);
                    while($slot = $analytics['busyTimeSlots']->fetch_assoc()): 
                    ?>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="text-gray-800 font-semibold text-sm"><?= htmlspecialchars($slot['formatted_time']) ?></span>
                        <span class="px-3 py-1 bg-secondary-100 text-secondary-700 text-[10px] font-black rounded-full uppercase tracking-wider"><?= $slot['booking_count'] ?> bookings</span>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <p class="text-gray-500 text-sm">No time slot data yet</p>
                <?php endif; ?>
            </div>

            <!-- Monthly Trends -->
            <div class="glass-card p-6 bg-white/95 shadow-xl border border-white/20 rounded-2xl">
                <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    Monthly Trends
                </h3>
                <?php if($analytics['monthlyTrends'] && $analytics['monthlyTrends']->num_rows > 0): ?>
                <div class="space-y-3">
                    <?php 
                    $analytics['monthlyTrends']->data_seek(0);
                    while($month = $analytics['monthlyTrends']->fetch_assoc()): 
                    ?>
                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-800 font-bold text-sm"><?= htmlspecialchars($month['formatted_month']) ?></span>
                            <span class="text-xs text-gray-600 font-semibold"><?= $month['total_poojas'] ?> ceremonies</span>
                        </div>
                        <div class="flex items-center space-x-2 text-[10px] font-bold">
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full">Available: <?= $month['available_count'] ?></span>
                            <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded-full">Booked: <?= $month['booked_count'] ?></span>
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
        <div class="mb-6 p-4 rounded-xl shadow-lg <?= $messageType === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' ?> font-semibold text-sm">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Poojas -->
            <div class="glass-card p-6 bg-white/90 shadow-xl border border-white/20 rounded-2xl card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Total Scheduled</p>
                        <h3 class="text-3xl font-black text-gray-800"><?= $stats['total'] ?? 0 ?></h3>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Available -->
            <div class="glass-card p-6 bg-white/90 shadow-xl border border-white/20 rounded-2xl card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Slots Available</p>
                        <h3 class="text-3xl font-black text-gray-800"><?= $stats['available'] ?? 0 ?></h3>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-700 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Booked -->
            <div class="glass-card p-6 bg-white/90 shadow-xl border border-white/20 rounded-2xl card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Pooja Booked</p>
                        <h3 class="text-3xl font-black text-gray-800"><?= $stats['booked'] ?? 0 ?></h3>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Completed -->
            <div class="glass-card p-6 bg-white/90 shadow-xl border border-white/20 rounded-2xl card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Rituals Completed</p>
                        <h3 class="text-3xl font-black text-gray-800"><?= $stats['completed'] ?? 0 ?></h3>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-accent-500 to-accent-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search Form (Server-Side Integration) -->
        <div class="glass-card p-6 mb-6 bg-white/95 shadow-xl border border-white/20 rounded-2xl">
            <form method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <input type="hidden" name="url" value="pooja-history">
                <div class="flex-1">
                    <label for="searchInput" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Search Devotees or Bookings</label>
                    <input 
                        type="text" 
                        name="search" 
                        id="searchInput" 
                        placeholder="🔍 Search by devotee name, phone number, reference, or pooja name..." 
                        class="w-full px-4 py-2.5 bg-white border border-gray-300 text-gray-800 focus:border-primary-500 focus:outline-none transition-all duration-200 rounded-xl"
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    >
                </div>
                <div class="flex flex-col sm:flex-row gap-3 items-end">
                    <div>
                        <label for="statusFilter" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Status</label>
                        <select 
                            name="status" 
                            id="statusFilter" 
                            class="px-4 py-2.5 bg-white border border-gray-300 text-gray-800 focus:border-primary-500 focus:outline-none transition-all duration-200 rounded-xl outline-none cursor-pointer"
                        >
                            <?php $statusSel = $_GET['status'] ?? ''; ?>
                            <option value="" <?= $statusSel === '' ? 'selected' : '' ?>>All Status</option>
                            <option value="available" <?= $statusSel === 'available' ? 'selected' : '' ?>>Available</option>
                            <option value="booked" <?= $statusSel === 'booked' ? 'selected' : '' ?>>Booked</option>
                            <option value="completed" <?= $statusSel === 'completed' ? 'selected' : '' ?>>Completed</option>
                        </select>
                    </div>
                    <div>
                        <label for="dateFilter" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Timeline</label>
                        <select 
                            name="date_filter" 
                            id="dateFilter" 
                            class="px-4 py-2.5 bg-white border border-gray-300 text-gray-800 focus:border-primary-500 focus:outline-none transition-all duration-200 rounded-xl outline-none cursor-pointer"
                        >
                            <?php $dateSel = $_GET['date_filter'] ?? ''; ?>
                            <option value="" <?= $dateSel === '' ? 'selected' : '' ?>>All Dates</option>
                            <option value="past" <?= $dateSel === 'past' ? 'selected' : '' ?>>Past</option>
                            <option value="today" <?= $dateSel === 'today' ? 'selected' : '' ?>>Today</option>
                            <option value="future" <?= $dateSel === 'future' ? 'selected' : '' ?>>Future</option>
                        </select>
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="bg-gradient-to-r from-primary-650 to-primary-750 hover:from-primary-550 hover:to-primary-650 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition-all duration-200">
                            Search
                        </button>
                        <?php if(!empty($_GET['search']) || !empty($_GET['status']) || !empty($_GET['date_filter'])): ?>
                        <a href="?url=pooja-history" class="bg-gray-100 hover:bg-gray-200 border border-gray-250 text-gray-700 font-bold px-5 py-2.5 rounded-xl transition-all duration-200 flex items-center justify-center">
                            Clear
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>

        <!-- Pooja History Table -->
        <div class="glass-card p-6 bg-white shadow-xl border border-white/20 rounded-2xl">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <svg class="w-6 h-6 mr-2 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Pooja Audits & Bookings Log
            </h2>

            <?php if($data && $data->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left" id="poojaTable">
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
                        $counter = ($currentPage - 1) * 20 + 1;
                        while($pooja = $data->fetch_assoc()): 
                            $isPast = strtotime($pooja['pooja_date']) < strtotime('today');
                            $isToday = date('Y-m-d') == $pooja['pooja_date'];
                        ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors" 
                            data-status="<?= $pooja['status'] ?>" 
                            data-date="<?= $isPast ? 'past' : ($isToday ? 'today' : 'future') ?>">
                            <td class="py-3 px-4 text-gray-600 font-medium"><?= $counter++ ?></td>
                            <td class="py-3 px-4">
                                <span class="text-gray-800 font-bold"><?= htmlspecialchars($pooja['pooja_name']) ?></span>
                                <?php if(!empty($pooja['description'])): ?>
                                <p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars(substr($pooja['description'], 0, 80)) ?>...</p>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4">
                                <div>
                                    <p class="text-gray-800 font-semibold"><?= date('M d, Y', strtotime($pooja['pooja_date'])) ?></p>
                                    <p class="text-xs text-gray-500 font-medium"><?= date('l', strtotime($pooja['pooja_date'])) ?></p>
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <span class="text-gray-800 font-semibold"><?= date('g:i A', strtotime($pooja['time_slot'])) ?></span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider
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
                                    <div>
                                        <p class="text-gray-850 text-sm font-bold"><?= htmlspecialchars($pooja['booked_by_name']) ?></p>
                                        <?php if(!empty($pooja['booked_by_phone'])): ?>
                                        <p class="text-[10px] text-gray-500 font-mono font-medium"><?= htmlspecialchars($pooja['booked_by_phone']) ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php else: ?>
                                <span class="text-gray-400 text-sm">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-gray-650 font-medium text-sm"><?= date('M j, Y g:i A', strtotime($pooja['created_at'])) ?></td>
                            <?php if($_SESSION['user']['role'] === 'management'): ?>
                            <td class="py-3 px-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="?url=schedule&action=edit&id=<?= $pooja['id'] ?>" 
                                       class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1.5 rounded-xl transition-colors text-sm font-semibold shadow-sm">
                                        Edit
                                    </a>
                                    <?php if($pooja['status'] !== 'completed'): ?>
                                    <button onclick="deletePooja(<?= $pooja['id'] ?>)" 
                                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded-xl transition-colors text-sm font-semibold shadow-sm">
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
                <p class="text-gray-650 text-lg font-bold">No matching poojas found</p>
                <p class="text-gray-500 text-sm mt-1">Try relaxing your search terms or filters.</p>
                <?php if($_SESSION['user']['role'] === 'management' && empty($_GET['search'])): ?>
                <a href="?url=schedule&action=add" class="inline-block mt-4 bg-gradient-to-r from-primary-600 to-primary-700 text-white px-6 py-2.5 rounded-xl font-bold hover:from-primary-700 hover:to-primary-800 transition-all shadow-md">
                    + Add Ceremony
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- Pagination Controls -->
        <?php if($totalPages > 1): ?>
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 mt-6">
            <div class="text-sm text-gray-200 font-medium">
                Showing page <span class="font-bold text-white"><?= $currentPage ?></span> of <span class="font-bold text-white"><?= $totalPages ?></span>
                (<?= $totalPoojas ?> total results matching search)
            </div>
            <div class="flex items-center space-x-2">
                <!-- Previous Button -->
                <?php if($currentPage > 1): ?>
                <a href="?url=pooja-history&page=<?= $currentPage - 1 ?><?= $paginationParams ?>" 
                   class="px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 rounded-xl font-bold text-sm text-gray-705 transition-all shadow-sm">
                    ← Previous
                </a>
                <?php endif; ?>

                <!-- Page Numbers -->
                <?php for($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if($i == $currentPage): ?>
                    <span class="px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-black text-sm rounded-xl shadow-md">
                        <?= $i ?>
                    </span>
                    <?php elseif($i == 1 || $i == $totalPages || ($i >= $currentPage - 2 && $i <= $currentPage + 2)): ?>
                    <a href="?url=pooja-history&page=<?= $i ?><?= $paginationParams ?>" 
                       class="px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 rounded-xl font-bold text-sm text-gray-705 transition-all shadow-sm">
                        <?= $i ?>
                    </a>
                    <?php else: ?>
                    <span class="px-2 py-2 text-gray-400">...</span>
                    <?php endif; ?>
                <?php endfor; ?>

                <!-- Next Button -->
                <?php if($currentPage < $totalPages): ?>
                <a href="?url=pooja-history&page=<?= $currentPage + 1 ?><?= $paginationParams ?>" 
                   class="px-4 py-2 bg-white hover:bg-gray-50 border border-gray-300 rounded-xl font-bold text-sm text-gray-705 transition-all shadow-sm">
                    Next →
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
<?php if($_SESSION['user']['role'] === 'management'): ?>
function deletePooja(id) {
    if (confirm('Are you sure you want to delete this pooja? This action will permanently remove the record and release any booked slots.')) {
        window.location.href = `?url=schedule&action=delete&id=${id}`;
    }
}
<?php endif; ?>
</script>
