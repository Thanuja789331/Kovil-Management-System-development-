<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-xl shadow-lg">
        <!-- Success Icon and Title -->
        <div class="text-center mb-8">
            <!-- Back Button -->
            <a href="javascript:history.back()" class="inline-flex items-center text-gray-600 hover:text-gray-800 mb-4">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Booking
            </a>
            
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Booking Confirmed!</h1>
            <p class="text-gray-600">Your pooja has been successfully booked</p>
        </div>

        <?php if($data): ?>
        <div class="bg-gray-50 p-6 rounded-lg mb-6">
            <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-200 pb-2">Booking Details</h3>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="text-gray-600 text-sm">Booking ID:</span>
                    <p class="text-gray-800 font-semibold">#<?= htmlspecialchars($data['id'] ?? 'N/A') ?></p>
                </div>
                
                <div>
                    <span class="text-gray-600 text-sm">Reference:</span>
                    <p class="text-gray-800 font-semibold"><?= htmlspecialchars($data['booking_reference'] ?? 'N/A') ?></p>
                </div>
                
                <div class="col-span-2">
                    <span class="text-gray-600 text-sm">Pooja Name:</span>
                    <p class="text-gray-800 font-semibold"><?= htmlspecialchars($data['pooja_name'] ?? 'N/A') ?></p>
                </div>
                
                <div>
                    <span class="text-gray-600 text-sm">Date:</span>
                    <p class="text-gray-800 font-semibold"><?= date("F j, Y", strtotime($data['pooja_date'] ?? 'now')) ?></p>
                </div>
                
                <div>
                    <span class="text-gray-600 text-sm">Time:</span>
                    <p class="text-gray-800 font-semibold"><?= htmlspecialchars($data['time_slot'] ?? 'N/A') ?></p>
                </div>
                
                <div>
                    <span class="text-gray-600 text-sm">Booked By:</span>
                    <p class="text-gray-800 font-semibold"><?= htmlspecialchars($data['user_name'] ?? 'N/A') ?></p>
                </div>
                
                <div>
                    <span class="text-gray-600 text-sm">Phone:</span>
                    <p class="text-gray-800 font-semibold"><?= htmlspecialchars($data['phone'] ?? 'N/A') ?></p>
                </div>
                
                <?php if(!empty($data['special_requests'])): ?>
                <div class="col-span-2 pt-3 border-t border-gray-200">
                    <span class="text-gray-600 text-sm block mb-2">Special Requests:</span>
                    <p class="text-gray-800 text-sm"><?= htmlspecialchars($data['special_requests']) ?></p>
                </div>
                <?php endif; ?>
                
                <div class="col-span-2 pt-3 border-t border-gray-200">
                    <span class="text-gray-600 text-sm">Status:</span>
                    <span class="bg-green-500 text-white px-4 py-1 rounded-full text-sm font-semibold inline-block ml-2">
                        <?= ucfirst($data['status'] ?? 'confirmed') ?> ✓
                    </span>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <div class="space-y-3">
            <a href="?url=schedule" 
               class="block w-full bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-bold py-3 px-4 rounded-lg transition transform hover:scale-105 text-center">
                View All Schedules
            </a>
            
            <a href="?url=my-bookings" 
               class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 px-4 rounded-lg transition text-center">
                My Bookings
            </a>
            
            <?php if(isset($data['phone'])): ?>
            <button onclick="sendConfirmationSMS('<?= htmlspecialchars($data['id']) ?>', '<?= htmlspecialchars($data['phone']) ?>')"
                    class="block w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-3 px-4 rounded-lg transition transform hover:scale-105 shadow-lg">
                📱 Send Confirmation to Mobile
            </button>
            <?php endif; ?>
        </div>

        <div id="sms-status" class="hidden mt-4 p-4 rounded-lg"></div>

        <div class="mt-8 p-4 bg-blue-50 rounded-lg border-l-4 border-blue-500">
            <p class="text-sm text-blue-800">
                <strong>📍</strong> Please arrive 15 minutes before the scheduled time<br>
                <strong>🙏</strong> Bring this confirmation for verification
            </p>
        </div>
    </div>
</div>

<script>
function sendConfirmationSMS(bookingId, phone) {
    const statusDiv = document.getElementById('sms-status');
    
    // Show loading state
    statusDiv.classList.remove('hidden');
    statusDiv.className = 'mt-4 p-4 rounded-lg bg-yellow-600/20 text-yellow-200';
    statusDiv.innerHTML = '⏳ Sending confirmation to ' + phone + '...';
    
    fetch('?url=confirmation&action=send-sms', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `booking_id=${bookingId}&phone=${encodeURIComponent(phone)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            statusDiv.className = 'mt-4 p-4 rounded-lg bg-green-600/20 text-green-200';
            statusDiv.innerHTML = '✅ ' + data.message;
        } else {
            statusDiv.className = 'mt-4 p-4 rounded-lg bg-red-600/20 text-red-200';
            statusDiv.innerHTML = '❌ ' + data.message;
        }
        
        // Hide message after 5 seconds
        setTimeout(() => {
            statusDiv.classList.add('hidden');
        }, 5000);
    })
    .catch(error => {
        statusDiv.className = 'mt-4 p-4 rounded-lg bg-red-600/20 text-red-200';
        statusDiv.innerHTML = '❌ Failed to send SMS. Please try again.';
        console.error('Error:', error);
        
        setTimeout(() => {
            statusDiv.classList.add('hidden');
        }, 5000);
    });
}
</script>
