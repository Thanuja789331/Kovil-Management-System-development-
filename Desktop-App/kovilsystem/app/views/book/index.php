<!-- Booking Page - Centered Form Card -->
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-3xl">
        <!-- Back Button -->
        <div class="flex items-center space-x-4 mb-6">
            <a href="javascript:history.back()" class="group flex items-center space-x-2 bg-white/80 hover:bg-white backdrop-blur-sm px-4 py-2 rounded-xl shadow-md transition-all duration-200 hover:scale-105 active:scale-95">
                <svg class="w-5 h-5 text-secondary-600 group-hover:text-secondary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span class="font-semibold text-secondary-600 group-hover:text-secondary-700">Back</span>
            </a>
        </div>

        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Book Your Pooja</h1>
            <p class="text-gray-600">Complete your booking details</p>
        </div>

        <?php if(!empty($error)): ?>
        <div class="mb-6 p-4 bg-red-500 text-white rounded-xl shadow-lg animate-pulse">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- Form Card -->
        <div class="glass-card p-8 card-hover">
            <!-- Pooja Details Summary -->
            <?php if($data): ?>
            <div class="bg-gradient-to-r from-secondary-50 to-accent-50 p-6 rounded-xl mb-6 border-2 border-secondary-200">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    Pooja Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Pooja Name</p>
                        <p class="font-bold text-gray-800"><?= htmlspecialchars($data['pooja_name']) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Date</p>
                        <p class="font-bold text-gray-800"><?= date("F j, Y", strtotime($data['pooja_date'])) ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Time Slot</p>
                        <p class="font-bold text-gray-800"><?= htmlspecialchars($data['time_slot']) ?></p>
                    </div>
                    <?php if(!empty($data['description'])): ?>
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-600 mb-1">Description</p>
                        <p class="text-gray-700"><?= htmlspecialchars($data['description']) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <form method="POST" id="bookingForm" class="space-y-6">
                <!-- Devotee Name (Read-only from session) -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Your Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name"
                        class="input-field w-full px-4 py-3 bg-gray-100 border-2 border-gray-300 text-gray-800 rounded-xl cursor-not-allowed"
                        value="<?= htmlspecialchars($_SESSION['user']['name'] ?? '') ?>"
                        readonly
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">Name from your profile</p>
                </div>

                <!-- Contact Number -->
                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Contact Number <span class="text-red-500">*</span></label>
                    <input 
                        type="tel" 
                        name="phone" 
                        id="phone"
                        placeholder="Enter 10-digit mobile number"
                        pattern="[0-9]{10}"
                        class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-secondary-500 focus:bg-white transition-all duration-200 rounded-xl"
                        required
                    >
                    <p class="text-xs text-gray-500 mt-1">Booking confirmation will be sent to this number</p>
                </div>

                <!-- Special Requests -->
                <div>
                    <label for="special_requests" class="block text-sm font-semibold text-gray-700 mb-2">Special Requests (Optional)</label>
                    <textarea 
                        name="special_requests" 
                        id="special_requests"
                        rows="4"
                        placeholder="Any specific requirements or preferences..."
                        class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 placeholder-gray-400 focus:border-secondary-500 focus:bg-white transition-all duration-200 rounded-xl resize-none"
                    ></textarea>
                </div>

                <!-- Confirmation Checkbox -->
                <div class="flex items-start space-x-3">
                    <input 
                        type="checkbox" 
                        name="confirm" 
                        id="confirm"
                        class="mt-1 w-4 h-4 text-secondary-600 border-2 border-gray-300 rounded focus:ring-secondary-500"
                        required
                    >
                    <label for="confirm" class="text-sm text-gray-700">
                        I confirm that I will arrive 15 minutes before the scheduled time. I agree to receive SMS notifications about my booking.
                    </label>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4 pt-4">
                    <button 
                        type="submit" 
                        id="submitBtn"
                        class="flex-1 bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-400 hover:to-secondary-500 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] btn-animate"
                    >
                        <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Confirm Booking
                    </button>
                    <a 
                        href="?url=schedule"
                        class="flex items-center justify-center px-6 py-4 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-200"
                    >
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- On-Spot Success Message Modal -->
        <div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50 p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-8 transform transition-all duration-300 scale-100 relative">
                <!-- Close Button -->
                <button onclick="closeSuccessModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                
                <!-- Success Icon -->
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4 animate-bounce">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 mb-2">🎉 Pooja Booked Successfully!</h2>
                    <p class="text-gray-600">Your booking has been confirmed</p>
                </div>

                <!-- Booking Details -->
                <div class="bg-gradient-to-r from-green-50 to-blue-50 p-6 rounded-xl mb-6 border-2 border-green-200">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Booking Confirmation
                    </h3>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Pooja:</span>
                            <span id="modalPoojaName" class="font-bold text-gray-800"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Date:</span>
                            <span id="modalDate" class="font-bold text-gray-800"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Time:</span>
                            <span id="modalTime" class="font-bold text-gray-800"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Reference:</span>
                            <span id="modalReference" class="font-bold text-green-600"></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Status:</span>
                            <span class="bg-green-600 text-white px-3 py-1 rounded-full text-sm font-bold">✓ Confirmed</span>
                        </div>
                    </div>
                </div>

                <!-- Success Message -->
                <div class="bg-blue-50 p-4 rounded-lg mb-6 border-l-4 border-blue-500">
                    <p class="text-sm text-blue-800">
                        <strong>✅ On-Spot Confirmation:</strong> Your pooja has been successfully booked! 
                        A confirmation SMS will also be sent to your mobile number shortly.
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a href="?url=my-bookings" class="block w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white font-bold py-3 rounded-lg transition transform hover:scale-105 text-center">
                        📋 View My Bookings
                    </a>
                    <button onclick="closeSuccessModal()" class="block w-full bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold py-3 rounded-lg transition text-center">
                        Stay Here
                    </button>
                </div>
            </div>
        </div>

        <!-- Important Information -->
        <div class="mt-6 bg-blue-50 p-6 rounded-xl border-2 border-blue-200">
            <h4 class="font-bold text-blue-800 mb-3 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Important Information
            </h4>
            <ul class="space-y-2 text-sm text-blue-700">
                <li class="flex items-start">
                    <span class="mr-2">✓</span>
                    <span>Please arrive 15 minutes before the scheduled time</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2">✓</span>
                    <span>A confirmation SMS will be sent to your mobile number</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2">✓</span>
                    <span>Bring the booking reference for verification</span>
                </li>
                <li class="flex items-start">
                    <span class="mr-2">✓</span>
                    <span>Contact temple office for cancellations or rescheduling</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
// Simple loading indicator for submit button
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bookingForm');
    const submitBtn = document.getElementById('submitBtn');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function() {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin inline w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
            submitBtn.classList.add('opacity-75');
            // Form will submit normally
        });
    }
});
</script>
