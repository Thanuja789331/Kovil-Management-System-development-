<!-- Edit Pooja Form -->
<?php
// Validate data is an array before proceeding
if (!is_array($data) || empty($data)) {
    // Redirect with error
    $_SESSION['error'] = "Invalid schedule data";
    header("Location: ?url=schedule");
    exit;
}
?>
<div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center space-x-4 mb-4">
        <a href="?url=schedule" class="group flex items-center space-x-2 bg-white/80 hover:bg-white backdrop-blur-sm px-4 py-2 rounded-xl shadow-md transition-all duration-200 hover:scale-105 active:scale-95">
            <svg class="w-5 h-5 text-primary-700 group-hover:text-primary-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="font-semibold text-primary-700 group-hover:text-primary-800">Back</span>
        </a>
    </div>

    <!-- Header -->
    <div class="glass-card p-6">
        <div class="flex items-center space-x-4 mb-4">
            <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Edit Pooja Schedule</h1>
                <p class="text-gray-600">Update pooja details</p>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if(isset($_SESSION['success'])): ?>
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg shadow-md">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <p class="text-green-700 font-medium"><?= htmlspecialchars($_SESSION['success']) ?></p>
            </div>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['error'])): ?>
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow-md">
            <div class="flex items-center">
                <svg class="w-6 h-6 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <p class="text-red-700 font-medium"><?= htmlspecialchars($_SESSION['error']) ?></p>
            </div>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Edit Form -->
    <div class="glass-card p-8">
        <form action="?url=schedule&action=update" method="POST" class="space-y-6">
            <input type="hidden" name="id" value="<?= htmlspecialchars($data['id'] ?? '') ?>">
            
            <!-- Pooja Name -->
            <div>
                <label for="pooja_name" class="block text-sm font-semibold text-gray-700 mb-2">
                    Pooja Name *
                </label>
                <input 
                    type="text" 
                    id="pooja_name" 
                    name="pooja_name" 
                    required
                    value="<?= htmlspecialchars($data['pooja_name'] ?? '') ?>"
                    placeholder="Enter pooja name"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-4 focus:ring-primary-200 transition-all outline-none"
                >
            </div>

            <!-- Date -->
            <div>
                <label for="pooja_date" class="block text-sm font-semibold text-gray-700 mb-2">
                    Date *
                </label>
                <input 
                    type="date" 
                    id="pooja_date" 
                    name="pooja_date" 
                    required
                    min="<?= date('Y-m-d') ?>"
                    value="<?= htmlspecialchars($data['pooja_date'] ?? '') ?>"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-4 focus:ring-primary-200 transition-all outline-none"
                >
            </div>

            <!-- Time Slot -->
            <div>
                <label for="time_slot" class="block text-sm font-semibold text-gray-700 mb-2">
                    Time Slot *
                </label>
                <select 
                    id="time_slot" 
                    name="time_slot" 
                    required
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-4 focus:ring-primary-200 transition-all outline-none bg-white"
                >
                    <option value="">Select time slot</option>
                    <option value="06:00:00" <?= ($data['time_slot'] ?? '') == '06:00:00' ? 'selected' : '' ?>>Morning 6:00 AM</option>
                    <option value="07:00:00" <?= ($data['time_slot'] ?? '') == '07:00:00' ? 'selected' : '' ?>>Morning 7:00 AM</option>
                    <option value="08:00:00" <?= ($data['time_slot'] ?? '') == '08:00:00' ? 'selected' : '' ?>>Morning 8:00 AM</option>
                    <option value="09:00:00" <?= ($data['time_slot'] ?? '') == '09:00:00' ? 'selected' : '' ?>>Morning 9:00 AM</option>
                    <option value="10:00:00" <?= ($data['time_slot'] ?? '') == '10:00:00' ? 'selected' : '' ?>>Morning 10:00 AM</option>
                    <option value="11:00:00" <?= ($data['time_slot'] ?? '') == '11:00:00' ? 'selected' : '' ?>>Morning 11:00 AM</option>
                    <option value="16:00:00" <?= ($data['time_slot'] ?? '') == '16:00:00' ? 'selected' : '' ?>>Evening 4:00 PM</option>
                    <option value="17:00:00" <?= ($data['time_slot'] ?? '') == '17:00:00' ? 'selected' : '' ?>>Evening 5:00 PM</option>
                    <option value="18:00:00" <?= ($data['time_slot'] ?? '') == '18:00:00' ? 'selected' : '' ?>>Evening 6:00 PM</option>
                </select>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                    Description
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4" 
                    placeholder="Brief description of the pooja"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-4 focus:ring-primary-200 transition-all outline-none resize-none"
                ><?= htmlspecialchars($data['description'] ?? '') ?></textarea>
            </div>

            <!-- Submit Buttons -->
            <div class="flex items-center space-x-4 pt-4">
                <button 
                    type="submit" 
                    class="flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-600 hover:to-yellow-700 text-white font-bold py-4 px-8 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105 active:scale-95"
                >
                    <span class="flex items-center justify-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span>Update Pooja</span>
                    </span>
                </button>
                <a 
                    href="?url=schedule" 
                    class="px-8 py-4 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold rounded-xl transition-all"
                >
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <!-- Delete Section -->
    <div class="glass-card p-6 border-l-4 border-red-500">
        <div class="flex items-start space-x-4">
            <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-800 mb-2">Danger Zone</h3>
                <p class="text-sm text-gray-600 mb-4">Deleting this pooja will also cancel all associated bookings and priest assignments. This action cannot be undone.</p>
                
                <form action="?url=schedule&action=delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this pooja? All bookings will be cancelled!');">
                    <input type="hidden" name="id" value="<?= $data['id'] ?>">
                    <button 
                        type="submit" 
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl transition-all transform hover:scale-105"
                    >
                        <span class="flex items-center space-x-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            <span>Delete This Pooja</span>
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
