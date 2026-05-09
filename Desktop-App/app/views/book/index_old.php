<!-- Booking Page - Centered Form Card -->
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-2xl">
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
            <p class="text-gray-600">Fill in the details to schedule your divine ceremony</p>
        </div>

        <!-- Form Card -->
        <div class="glass-card p-8 card-hover">
            <?php if(!empty($error)): ?>
            <div class="mb-6 p-4 bg-red-500 text-white rounded-xl shadow-lg animate-pulse">
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <!-- Pooja Type -->
                <div>
                    <label for="pooja_type" class="block text-sm font-semibold text-gray-700 mb-2">Pooja Type</label>
                    <select 
                        name="pooja_type" 
                        id="pooja_type"
                        class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-secondary-500 focus:bg-white transition-all duration-200 rounded-xl"
                        required
                    >
                        <option value="">Select a pooja</option>
                        <option value="Morning Pooja">Morning Pooja</option>
                        <option value="Evening Aarti">Evening Aarti</option>
                        <option value="Abhishekam">Abhishekam</option>
                        <option value="Archana">Archana</option>
                        <option value="Special Pooja">Special Pooja</option>
                    </select>
                </div>

                <!-- Date Picker -->
                <div>
                    <label for="date" class="block text-sm font-semibold text-gray-700 mb-2">Preferred Date</label>
                    <input 
                        type="date" 
                        name="date" 
                        id="date"
                        min="<?= date('Y-m-d') ?>"
                        class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-secondary-500 focus:bg-white transition-all duration-200 rounded-xl"
                        required
                    >
                </div>

                <!-- Devotee Name -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Your Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name"
                        placeholder="Enter your full name"
                        class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 placeholder-gray-400 focus:border-secondary-500 focus:bg-white transition-all duration-200 rounded-xl"
                        value="<?= htmlspecialchars($_SESSION['user']['name'] ?? '') ?>"
                        required
                    >
                </div>

                <!-- Contact Number -->
                <div>
                    <label for="contact" class="block text-sm font-semibold text-gray-700 mb-2">Contact Number</label>
                    <input 
                        type="tel" 
                        name="contact" 
                        id="contact"
                        placeholder="Enter your phone number"
                        pattern="[0-9]{10}"
                        class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 placeholder-gray-400 focus:border-secondary-500 focus:bg-white transition-all duration-200 rounded-xl"
                        required
                    >
                </div>

                <!-- Special Requests -->
                <div>
                    <label for="requests" class="block text-sm font-semibold text-gray-700 mb-2">Special Requests (Optional)</label>
                    <textarea 
                        name="requests" 
                        id="requests"
                        rows="4"
                        placeholder="Any specific requirements or preferences..."
                        class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 placeholder-gray-400 focus:border-secondary-500 focus:bg-white transition-all duration-200 rounded-xl"
                    ></textarea>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-4 pt-4">
                    <button 
                        type="submit" 
                        class="flex-1 bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-400 hover:to-secondary-500 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] btn-animate"
                    >
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
    </div>
</div>
