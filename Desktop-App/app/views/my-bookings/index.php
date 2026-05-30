<!-- My Bookings Page -->
<?php
$today = date('Y-m-d');
$upcomingBookings = [];
$pastBookings = [];

if ($data) {
    if ($data instanceof mysqli_result) {
        $data->data_seek(0);
        while ($row = $data->fetch_assoc()) {
            if ($row['status'] === 'confirmed' && $row['pooja_date'] >= $today) {
                $upcomingBookings[] = $row;
            } else {
                $pastBookings[] = $row;
            }
        }
    } elseif (is_array($data)) {
        foreach ($data as $row) {
            if ($row['status'] === 'confirmed' && $row['pooja_date'] >= $today) {
                $upcomingBookings[] = $row;
            } else {
                $pastBookings[] = $row;
            }
        }
    }
}
?>

<div class="min-h-[calc(100vh-8rem)] py-12 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-black text-white mb-2 drop-shadow-md">📅 <?= trans('my_bookings') ?? 'My Bookings' ?></h1>
            <p class="text-emerald-100 font-medium drop-shadow-sm">View and manage your personal pooja history and upcoming bookings</p>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="mb-6 p-4 bg-emerald-500/20 backdrop-blur-md border border-emerald-500/30 rounded-xl text-emerald-200 flex items-center space-x-3 shadow-lg">
                <svg class="w-6 h-6 flex-shrink-0 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-semibold text-sm"><?= $_SESSION['success']; unset($_SESSION['success']); ?></span>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="mb-6 p-4 bg-rose-500/20 backdrop-blur-md border border-rose-500/30 rounded-xl text-rose-200 flex items-center space-x-3 shadow-lg">
                <svg class="w-6 h-6 flex-shrink-0 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="font-semibold text-sm"><?= $_SESSION['error']; unset($_SESSION['error']); ?></span>
            </div>
        <?php endif; ?>

        <!-- Modern Tab Controller -->
        <div class="flex justify-center space-x-4 mb-8">
            <button id="tab-upcoming" onclick="switchTab('upcoming')" 
                    class="px-6 py-3 rounded-xl font-bold text-sm shadow-md transition-all duration-300 transform active:scale-95 flex items-center space-x-2 bg-secondary-500 text-white hover:bg-secondary-600">
                <span>Upcoming Poojas</span>
                <span class="bg-white/20 text-white px-2 py-0.5 rounded-full text-xs font-black"><?= count($upcomingBookings) ?></span>
            </button>
            <button id="tab-past" onclick="switchTab('past')" 
                    class="px-6 py-3 rounded-xl font-bold text-sm shadow-md transition-all duration-300 transform active:scale-95 flex items-center space-x-2 bg-white/60 text-gray-700 hover:bg-white">
                <span>History & Cancelled</span>
                <span class="bg-gray-200 text-gray-700 px-2 py-0.5 rounded-full text-xs font-black"><?= count($pastBookings) ?></span>
            </button>
        </div>

        <!-- Tab 1: Upcoming Bookings Grid -->
        <div id="grid-upcoming" class="transition-all duration-300">
            <?php if(count($upcomingBookings) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($upcomingBookings as $booking): ?>
                <div class="glass-card p-6 card-hover bg-gradient-to-br from-white to-secondary-50 shadow-xl border border-white/20 rounded-2xl relative overflow-hidden flex flex-col justify-between">
                    <div>
                        <!-- Header -->
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-secondary-600 bg-secondary-100 px-3 py-1 rounded-full">
                                    REF: <?= htmlspecialchars($booking['booking_reference'] ?? ('BKG' . $booking['id'])) ?>
                                </span>
                                <span class="text-xs font-bold text-green-700 bg-green-100 px-3 py-1 rounded-full uppercase tracking-wider">
                                    <?= htmlspecialchars($booking['status']) ?>
                                </span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-800 mt-3 line-clamp-1"><?= htmlspecialchars($booking['pooja_name']) ?></h3>
                        </div>

                        <!-- Info details -->
                        <div class="space-y-3 mb-6">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-secondary-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Date</p>
                                    <p class="text-gray-800 font-semibold"><?= date("l, F j, Y", strtotime($booking['pooja_date'])) ?></p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-secondary-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Time</p>
                                    <p class="text-gray-800 font-semibold"><?= htmlspecialchars($booking['time_slot']) ?></p>
                                </div>
                            </div>
                        </div>

                        <?php if(!empty($booking['special_requests'])): ?>
                        <div class="bg-gray-50/80 p-3 rounded-xl mb-4 border border-gray-100">
                            <p class="text-[10px] text-gray-500 font-bold uppercase mb-1">Special Requests</p>
                            <p class="text-sm text-gray-700 italic">"<?= htmlspecialchars($booking['special_requests']) ?>"</p>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-2 mt-4">
                        <button onclick="viewBookingDetails(<?= htmlspecialchars(json_encode($booking)) ?>)" 
                                class="w-full bg-secondary-500 hover:bg-secondary-600 text-white font-bold py-2.5 px-4 rounded-xl transition-all duration-200 flex items-center justify-center space-x-2 shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            <span>Receipt & Checklist</span>
                        </button>
                        
                        <form action="?url=cancel-booking" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking? This will free up the slot for other devotees and cannot be undone.');" class="w-full">
                            <input type="hidden" name="id" value="<?= htmlspecialchars($booking['id']) ?>">
                            <button type="submit" class="w-full bg-rose-50 hover:bg-rose-100 border border-rose-200 text-rose-600 font-semibold py-2 px-4 rounded-xl transition-all duration-200 flex items-center justify-center space-x-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <span>Cancel Booking</span>
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="glass-card p-12 text-center max-w-xl mx-auto shadow-2xl border border-white/20">
                <div class="w-20 h-20 bg-secondary-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-4xl">🛕</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">No Upcoming Poojas</h3>
                <p class="text-gray-600 mb-6">You don't have any upcoming scheduled bookings. Make a booking to get started!</p>
                <a href="?url=schedule" class="bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-400 hover:to-secondary-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-transform duration-200 transform hover:scale-105 active:scale-95 inline-flex items-center space-x-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>Browse & Book Pooja</span>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tab 2: Past & Cancelled Grid (Hidden by Default) -->
        <div id="grid-past" class="hidden transition-all duration-300">
            <?php if(count($pastBookings) > 0): ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach($pastBookings as $booking): ?>
                <?php 
                $isCancelled = $booking['status'] === 'cancelled'; 
                $statusColor = $isCancelled ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700';
                ?>
                <div class="glass-card p-6 bg-white/75 shadow-lg border border-gray-200/50 rounded-2xl flex flex-col justify-between opacity-85">
                    <div>
                        <!-- Header -->
                        <div class="mb-4 pb-4 border-b border-gray-200/60">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-xs font-semibold text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                    REF: <?= htmlspecialchars($booking['booking_reference'] ?? ('BKG' . $booking['id'])) ?>
                                </span>
                                <span class="text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider <?= $statusColor ?>">
                                    <?= htmlspecialchars($booking['status']) ?>
                                </span>
                            </div>
                            <h3 class="text-xl font-bold text-gray-700 mt-3 line-clamp-1"><?= htmlspecialchars($booking['pooja_name']) ?></h3>
                        </div>

                        <!-- Info details -->
                        <div class="space-y-3 mb-6">
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Date</p>
                                    <p class="text-gray-700 font-semibold"><?= date("l, F j, Y", strtotime($booking['pooja_date'])) ?></p>
                                </div>
                            </div>

                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-gray-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Time</p>
                                    <p class="text-gray-700 font-semibold"><?= htmlspecialchars($booking['time_slot']) ?></p>
                                </div>
                            </div>

                            <?php if($isCancelled && !empty($booking['cancelled_at'])): ?>
                            <div class="flex items-start space-x-3">
                                <svg class="w-5 h-5 text-red-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                <div>
                                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Cancelled On</p>
                                    <p class="text-gray-600 font-semibold text-sm"><?= date("M j, Y g:i A", strtotime($booking['cancelled_at'])) ?></p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <button onclick="viewBookingDetails(<?= htmlspecialchars(json_encode($booking)) ?>)"
                            class="w-full bg-gray-100 hover:bg-gray-250 border border-gray-200 text-gray-700 font-bold py-2.5 px-4 rounded-xl transition-all duration-200 flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span>View Details</span>
                    </button>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="glass-card p-12 text-center max-w-xl mx-auto shadow-2xl border border-white/20">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="text-4xl text-gray-400">📅</span>
                </div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">No Past Bookings</h3>
                <p class="text-gray-600">You don't have any past pooja bookings or cancellation records.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Summary Stats -->
        <div class="mt-12 glass-card p-8 bg-white/80 shadow-xl border border-white/20 rounded-2xl">
            <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center space-x-2">
                <svg class="w-6 h-6 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                <span>My Devotional Statistics</span>
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 p-6 rounded-2xl border border-emerald-100 shadow-sm text-center">
                    <p class="text-4xl font-extrabold text-emerald-600 mb-1"><?= count($upcomingBookings) ?></p>
                    <p class="text-sm text-gray-600 font-bold">Upcoming Poojas</p>
                </div>
                <div class="bg-gradient-to-br from-blue-50 to-blue-100/50 p-6 rounded-2xl border border-blue-100 shadow-sm text-center">
                    <p class="text-4xl font-extrabold text-blue-600 mb-1"><?= count($pastBookings) ?></p>
                    <p class="text-sm text-gray-600 font-bold">Completed & Cancelled</p>
                </div>
                <div class="bg-gradient-to-br from-purple-50 to-purple-100/50 p-6 rounded-2xl border border-purple-100 shadow-sm text-center">
                    <p class="text-4xl font-extrabold text-purple-600 mb-1">
                        <?php 
                        $total = count($upcomingBookings) + count($pastBookings);
                        echo $total > 0 ? round((count($upcomingBookings) / $total) * 100) . '%' : '100%';
                        ?>
                    </p>
                    <p class="text-sm text-gray-600 font-bold">Active Booking Rate</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Booking Details Modal -->
<div id="bookingModal" class="hidden fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto border border-gray-100">
        <div class="p-8">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                <h3 class="text-2xl font-bold text-gray-800 flex items-center space-x-2">
                    <span>🛕</span>
                    <span>Pooja Confirmation Receipt</span>
                </h3>
                <button onclick="closeBookingModal()" class="text-gray-400 hover:text-gray-600 transition-colors p-1.5 rounded-full hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div id="modalContent" class="space-y-6">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    const upcomingTab = document.getElementById('tab-upcoming');
    const pastTab = document.getElementById('tab-past');
    const upcomingGrid = document.getElementById('grid-upcoming');
    const pastGrid = document.getElementById('grid-past');
    
    if (tab === 'upcoming') {
        upcomingTab.className = "px-6 py-3 rounded-xl font-bold text-sm shadow-md transition-all duration-300 transform active:scale-95 flex items-center space-x-2 bg-secondary-500 text-white hover:bg-secondary-600";
        pastTab.className = "px-6 py-3 rounded-xl font-bold text-sm shadow-md transition-all duration-300 transform active:scale-95 flex items-center space-x-2 bg-white/60 text-gray-700 hover:bg-white";
        upcomingGrid.classList.remove('hidden');
        pastGrid.classList.add('hidden');
    } else {
        pastTab.className = "px-6 py-3 rounded-xl font-bold text-sm shadow-md transition-all duration-300 transform active:scale-95 flex items-center space-x-2 bg-secondary-500 text-white hover:bg-secondary-600";
        upcomingTab.className = "px-6 py-3 rounded-xl font-bold text-sm shadow-md transition-all duration-300 transform active:scale-95 flex items-center space-x-2 bg-white/60 text-gray-700 hover:bg-white";
        pastGrid.classList.remove('hidden');
        upcomingGrid.classList.add('hidden');
    }
}

function viewBookingDetails(booking) {
    const content = document.getElementById('modalContent');
    const formattedDate = new Date(booking.pooja_date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
    const formattedCreated = new Date(booking.created_at).toLocaleString('en-US', { dateStyle: 'medium', timeStyle: 'short' });
    
    // Determine bring checklist based on pooja name keyword mapping
    const poojaNameLower = (booking.pooja_name || '').toLowerCase();
    const defaultChecklist = [
        "Coconut (1 or 2)",
        "Bananas or seasonal fruits",
        "Flowers and garland",
        "Betel leaves and arecanut",
        "Camphor and incense sticks",
        "Ghee or oil for lamp"
    ];
    let checklist = [...defaultChecklist];
    
    if (poojaNameLower.includes('shiva') || poojaNameLower.includes('rudra')) {
        checklist.unshift("Vilva leaves", "Milk, curd, honey for Abhishekam");
    } else if (poojaNameLower.includes('ganesh') || poojaNameLower.includes('vinayagar')) {
        checklist.unshift("Modakam or Kozhukattai", "Arugampul garland");
    } else if (poojaNameLower.includes('lakshmi') || poojaNameLower.includes('kubera')) {
        checklist.unshift("Lotus or red flowers", "Turmeric and Kumkum");
    } else if (poojaNameLower.includes('murugan')) {
        checklist.unshift("Panneer flowers", "Milk for Abhishekam");
    } else if (poojaNameLower.includes('amman')) {
        checklist.unshift("Lemon garland", "Turmeric and Kumkum");
    } else if (poojaNameLower.includes('satyanarayana')) {
        checklist.unshift("Panchamirtham ingredients", "Aval, banana, jaggery");
    } else if (poojaNameLower.includes('vishnu')) {
        checklist.unshift("Tulasi leaves", "Butter or sweet Pongal");
    }

    content.innerHTML = `
        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Pooja Name</p>
                <p class="text-lg font-black text-gray-800">${booking.pooja_name}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Booking Status</p>
                <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider">
                    ${booking.status}
                </span>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Date & Day</p>
                <p class="text-sm font-semibold text-gray-700">${formattedDate}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Time Slot</p>
                <p class="text-sm font-semibold text-gray-700">${booking.time_slot}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Reference ID</p>
                <p class="text-sm font-mono font-bold text-gray-700">${booking.booking_reference || ('BKG' + booking.id)}</p>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider mb-0.5">Booked On</p>
                <p class="text-sm font-semibold text-gray-700">${formattedCreated}</p>
            </div>
        </div>
        
        ${booking.special_requests ? `
        <div class="bg-blue-50 border border-blue-100 p-5 rounded-2xl">
            <p class="text-xs text-blue-600 font-bold uppercase tracking-wider mb-1">Special Devotee Request</p>
            <p class="text-gray-700 italic text-sm">"${booking.special_requests}"</p>
        </div>
        ` : ''}
        
        <!-- Checklist of Bring Items -->
        <div class="border border-amber-100 bg-amber-50/50 p-6 rounded-2xl">
            <h4 class="text-md font-bold text-amber-950 mb-3 flex items-center space-x-2">
                <span>📋</span>
                <span>Pooja Material Checklist</span>
            </h4>
            <p class="text-xs text-amber-800 mb-4 font-medium">Please bring the following items to the temple for your pooja rituals:</p>
            <ul class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-700 font-medium">
                ${checklist.map(item => `
                    <li class="flex items-center space-x-2">
                        <span class="text-emerald-500 flex-shrink-0 font-bold">✔</span>
                        <span>${item}</span>
                    </li>
                `).join('')}
            </ul>
        </div>
        
        <div class="bg-gray-50 border border-gray-200/60 p-5 rounded-2xl text-center">
            <p class="text-xs text-gray-600 font-bold leading-relaxed">
                📢 <strong>Important Instructions:</strong> Please reach the temple at least 15 minutes before your booked time slot. Contact the main desk/priest upon arrival and show this receipt.
            </p>
        </div>
    `;
    
    document.getElementById('bookingModal').classList.remove('hidden');
}

function closeBookingModal() {
    document.getElementById('bookingModal').classList.add('hidden');
}

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeBookingModal();
    }
});

// Close modal on outside click
document.getElementById('bookingModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBookingModal();
    }
});
</script>
