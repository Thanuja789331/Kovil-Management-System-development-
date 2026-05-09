<!-- My Bookings Page -->
<div class="min-h-[calc(100vh-8rem)] py-12 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">📅 My Bookings</h1>
            <p class="text-gray-600 font-medium">View all your confirmed pooja bookings</p>
        </div>

        <?php if($data && $data->num_rows > 0): ?>
        <!-- Bookings Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while($booking = $data->fetch_assoc()): ?>
            <div class="glass-card p-6 card-hover bg-gradient-to-br from-white to-secondary-50">
                <!-- Booking Header -->
                <div class="mb-4 pb-4 border-b-2 border-secondary-200">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-semibold text-secondary-600 bg-secondary-100 px-3 py-1 rounded-full">
                            BOOKING #<?= htmlspecialchars($booking['id']) ?>
                        </span>
                        <span class="text-xs font-bold text-green-700 bg-green-100 px-3 py-1 rounded-full">
                            <?= ucfirst($booking['status']) ?>
                        </span>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mt-3"><?= htmlspecialchars($booking['pooja_name']) ?></h3>
                </div>

                <!-- Booking Details -->
                <div class="space-y-3 mb-6">
                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-secondary-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase">Date</p>
                            <p class="text-gray-800 font-medium"><?= date("F j, Y", strtotime($booking['pooja_date'])) ?></p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-secondary-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase">Time</p>
                            <p class="text-gray-800 font-medium"><?= htmlspecialchars($booking['time_slot']) ?></p>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3">
                        <svg class="w-5 h-5 text-secondary-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase">Booked On</p>
                            <p class="text-gray-800 font-medium"><?= date("M j, Y g:i A", strtotime($booking['created_at'])) ?></p>
                        </div>
                    </div>
                </div>

                <!-- Additional Info -->
                <?php if(!empty($booking['special_requests'])): ?>
                <div class="bg-white/60 p-3 rounded-lg mb-4">
                    <p class="text-xs text-gray-600 font-semibold mb-1">Special Requests:</p>
                    <p class="text-sm text-gray-700 italic">"<?= htmlspecialchars($booking['special_requests']) ?>"</p>
                </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="space-y-2">
                    <button onclick="viewBookingDetails(<?= htmlspecialchars(json_encode($booking)) ?>)" 
                            class="w-full bg-secondary-500 hover:bg-secondary-600 text-white font-semibold py-2 px-4 rounded-lg transition-all duration-200 flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <span>View Details</span>
                    </button>
                    
                    <a href="?url=schedule" 
                       class="w-full block text-center bg-white/60 hover:bg-white text-gray-700 font-semibold py-2 px-4 rounded-lg transition-all duration-200">
                        Book More Poojas
                    </a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Summary Stats -->
        <div class="mt-8 glass-card p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                Booking Statistics
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-green-50 p-4 rounded-lg text-center">
                    <p class="text-3xl font-bold text-green-600 mb-1"><?= $data->num_rows ?></p>
                    <p class="text-sm text-gray-600 font-semibold">Total Bookings</p>
                </div>
                <div class="bg-blue-50 p-4 rounded-lg text-center">
                    <p class="text-3xl font-bold text-blue-600 mb-1"><?= $data->num_rows ?></p>
                    <p class="text-sm text-gray-600 font-semibold">Confirmed</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg text-center">
                    <p class="text-3xl font-bold text-purple-600 mb-1">100%</p>
                    <p class="text-sm text-gray-600 font-semibold">Attendance Rate</p>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- Empty State -->
        <div class="glass-card p-12 text-center">
            <svg class="w-24 h-24 mx-auto text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-2xl font-bold text-gray-800 mb-3">No Bookings Yet</h3>
            <p class="text-gray-600 mb-6 max-w-md mx-auto">You haven't booked any poojas yet. Browse available poojas and make your first booking!</p>
            <a href="?url=schedule" 
               class="inline-flex items-center bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-400 hover:to-secondary-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-105">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Browse Available Poojas
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Booking Details Modal -->
<div id="bookingModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <!-- Modal Header -->
            <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-gray-200">
                <h3 class="text-2xl font-bold text-gray-800">Booking Details</h3>
                <button onclick="closeBookingModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Modal Content -->
            <div id="modalContent" class="space-y-4">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>
    </div>
</div>

<script>
function viewBookingDetails(booking) {
    const content = document.getElementById('modalContent');
    content.innerHTML = `
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-xs text-gray-600 font-semibold uppercase">Pooja Name</p>
                <p class="text-gray-800 font-bold">${booking.pooja_name}</p>
            </div>
            <div>
                <p class="text-xs text-gray-600 font-semibold uppercase">Status</p>
                <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold">
                    ${booking.status}
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-600 font-semibold uppercase">Date</p>
                <p class="text-gray-800 font-medium">${new Date(booking.pooja_date).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' })}</p>
            </div>
            <div>
                <p class="text-xs text-gray-600 font-semibold uppercase">Time</p>
                <p class="text-gray-800 font-medium">${booking.time_slot}</p>
            </div>
            <div>
                <p class="text-xs text-gray-600 font-semibold uppercase">Booked On</p>
                <p class="text-gray-800 font-medium">${new Date(booking.created_at).toLocaleString('en-US', { dateStyle: 'medium', timeStyle: 'short' })}</p>
            </div>
        </div>
        
        ${booking.special_requests ? `
        <div class="bg-blue-50 p-4 rounded-lg">
            <p class="text-xs text-blue-600 font-semibold mb-1">Special Requests:</p>
            <p class="text-gray-700 italic">"${booking.special_requests}"</p>
        </div>
        ` : ''}
        
        <div class="bg-yellow-50 p-4 rounded-lg">
            <p class="text-sm text-yellow-800">
                <strong>Important:</strong> Please arrive 15 minutes before the scheduled time. 
                Bring this booking confirmation for verification.
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
