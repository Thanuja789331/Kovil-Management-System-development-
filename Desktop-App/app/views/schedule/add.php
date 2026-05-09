<!-- Add Pooja Form -->
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
            <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-800"><?= trans('add_pooja') ?></h1>
                <p class="text-gray-600"><?= trans('schedule_new_pooja') ?></p>
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

    <!-- Create Form -->
    <div class="glass-card p-8">
        <form action="?url=schedule&action=store" method="POST" class="space-y-6">
            <!-- Pooja Name -->
            <div>
                <label for="pooja_name" class="block text-sm font-semibold text-gray-700 mb-2">
                    <?= trans('pooja_name') ?> *
                </label>
                <input 
                    type="text" 
                    id="pooja_name" 
                    name="pooja_name" 
                    required
                    placeholder="<?= trans('morning_9_am') ?>"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-4 focus:ring-primary-200 transition-all outline-none"
                >
            </div>

            <!-- Date -->
            <div>
                <label for="pooja_date" class="block text-sm font-semibold text-gray-700 mb-2">
                    <?= trans('date') ?> *
                </label>
                <input 
                    type="date" 
                    id="pooja_date" 
                    name="pooja_date" 
                    required
                    min="<?= date('Y-m-d') ?>"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-4 focus:ring-primary-200 transition-all outline-none"
                >
            </div>

            <!-- Time Slot -->
            <div>
                <label for="time_slot" class="block text-sm font-semibold text-gray-700 mb-2">
                    <?= trans('time_slot') ?> *
                </label>
                <select 
                    id="time_slot" 
                    name="time_slot" 
                    required
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-4 focus:ring-primary-200 transition-all outline-none bg-white"
                >
                    <option value=""><?= trans('select_time_slot') ?></option>
                    <option value="06:00:00"><?= trans('morning_6_am') ?></option>
                    <option value="07:00:00"><?= trans('morning_7_am') ?></option>
                    <option value="08:00:00"><?= trans('morning_8_am') ?></option>
                    <option value="09:00:00"><?= trans('morning_9_am') ?></option>
                    <option value="10:00:00"><?= trans('morning_10_am') ?></option>
                    <option value="11:00:00"><?= trans('morning_11_am') ?></option>
                    <option value="16:00:00"><?= trans('evening_4_pm') ?></option>
                    <option value="17:00:00"><?= trans('evening_5_pm') ?></option>
                    <option value="18:00:00"><?= trans('evening_6_pm') ?></option>
                </select>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                    <?= trans('description') ?>
                </label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="4" 
                    placeholder="<?= trans('brief_description_pooja') ?>"
                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:ring-4 focus:ring-primary-200 transition-all outline-none resize-none"
                ></textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center space-x-4 pt-4">
                <button 
                    type="submit" 
                    class="flex-1 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-bold py-4 px-8 rounded-xl shadow-lg hover:shadow-xl transition-all transform hover:scale-105 active:scale-95"
                >
                    <span class="flex items-center justify-center space-x-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span><?= trans('create_pooja_schedule') ?></span>
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

    <!-- Tips -->
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
        <div class="flex items-start space-x-3">
            <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-1"><?= trans('scheduling_tips') ?></p>
                <ul class="list-disc list-inside space-y-1 text-blue-700">
                    <li><?= trans('morning_poojas_description') ?></li>
                    <li><?= trans('evening_poojas_description') ?></li>
                    <li><?= trans('allow_sufficient_time') ?></li>
                    <li><?= trans('consider_priest_availability') ?></li>
                    <li><?= trans('special_occasions') ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>
