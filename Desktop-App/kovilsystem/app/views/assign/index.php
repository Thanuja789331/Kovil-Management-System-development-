<div class="max-w-6xl mx-auto space-y-6">
    <!-- Header -->
    <div class="glass-card p-6">
        <h2 class="text-3xl font-bold text-white mb-2 flex items-center">
            <svg class="w-8 h-8 mr-3 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Assign Priest Duties
        </h2>
        <p class="text-gray-300">Assign poojas to priests and send SMS notifications</p>
    </div>

    <?php if(!empty($message)): ?>
    <div class="p-4 rounded-lg <?= $messageType === 'success' ? 'bg-green-600' : 'bg-red-600' ?> text-white text-center shadow-lg">
        <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <!-- Priest Availability Section -->
    <div class="glass-card p-6">
        <h3 class="text-xl font-bold text-white mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Priest Availability
        </h3>
        
        <!-- Date Filter -->
        <div class="mb-4">
            <form method="GET" class="flex items-center space-x-4">
                <input type="hidden" name="url" value="assign">
                <label class="text-white font-semibold">Check availability for date:</label>
                <input 
                    type="date" 
                    name="date" 
                    value="<?= htmlspecialchars($selectedDate) ?>"
                    min="<?= date('Y-m-d') ?>"
                    class="px-4 py-2 bg-white/10 border-2 border-white/20 text-white rounded-lg focus:border-accent-400 focus:outline-none"
                    onchange="this.form.submit()"
                >
            </form>
        </div>

        <!-- Priests Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-white">
                <thead>
                    <tr class="border-b-2 border-white/30">
                        <th class="text-left py-3 px-4 font-semibold">Priest Name</th>
                        <th class="text-left py-3 px-4 font-semibold">Email</th>
                        <th class="text-left py-3 px-4 font-semibold">Phone</th>
                        <th class="text-center py-3 px-4 font-semibold">Assigned Duties</th>
                        <th class="text-center py-3 px-4 font-semibold">Availability</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if($priests && $priests->num_rows > 0):
                        while($priest = $priests->fetch_assoc()): 
                            $isAvailable = $priest['assigned_count'] < 3;
                    ?>
                    <tr class="border-b border-white/10 hover:bg-white/5 transition-colors">
                        <td class="py-3 px-4"><?= htmlspecialchars($priest['name']) ?></td>
                        <td class="py-3 px-4"><?= htmlspecialchars($priest['email']) ?></td>
                        <td class="py-3 px-4"><?= htmlspecialchars($priest['phone'] ?? 'N/A') ?></td>
                        <td class="text-center py-3 px-4">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                <?= $priest['assigned_count'] == 0 ? 'bg-green-600 text-white' : 
                                   ($priest['assigned_count'] < 3 ? 'bg-yellow-600 text-white' : 'bg-red-600 text-white') ?>">
                                <?= $priest['assigned_count'] ?> / 3
                            </span>
                        </td>
                        <td class="text-center py-3 px-4">
                            <?php if($isAvailable): ?>
                                <span class="text-green-400 font-semibold flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Available
                                </span>
                            <?php else: ?>
                                <span class="text-red-400 font-semibold">Not Available</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="5" class="text-center py-8 text-gray-400">No priests found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Assignment Form -->
    <div class="glass-card p-8">
        <h3 class="text-xl font-bold text-white mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create New Assignment
        </h3>
        
        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-white text-sm font-semibold mb-3">Select Priest</label>
                <select name="priest" class="input-field bg-white/10 border-white/20 text-white w-full" required>
                    <option value="" class="text-gray-800">-- Choose a Priest --</option>
                    <?php 
                    // Reset pointer and show only available priests
                    $priests->data_seek(0);
                    while($p = $priests->fetch_assoc()): 
                        if($p['assigned_count'] < 3):
                    ?>
                    <option value="<?= $p['id'] ?>" class="text-gray-800">
                        <?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['email']) ?>) - <?= $p['assigned_count'] ?>/3 duties
                    </option>
                    <?php 
                        endif;
                    endwhile; 
                    ?>
                </select>
                <p class="text-gray-400 text-xs mt-2">Only priests with less than 3 assignments are shown</p>
            </div>

            <div>
                <label class="block text-white text-sm font-semibold mb-3">Select Pooja Schedule</label>
                <select name="schedule" class="input-field bg-white/10 border-white/20 text-white w-full" required>
                    <option value="" class="text-gray-800">-- Choose a Schedule --</option>
                    <?php while($s = $schedules->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>" class="text-gray-800">
                        <?= htmlspecialchars($s['pooja_name']) ?> - 
                        <?= date("M j, Y", strtotime($s['pooja_date'])) ?> (<?= htmlspecialchars($s['time_slot']) ?>)
                    </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" 
                    class="w-full bg-gradient-to-r from-accent-500 to-accent-600 hover:from-accent-400 hover:to-accent-500 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center space-x-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Assign Duty & Send SMS</span>
            </button>
        </form>
    </div>

    <!-- Recent Assignments -->
    <div class="glass-card p-6 mt-6">
        <h3 class="text-xl font-bold text-white mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Recent Assignments History
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full text-white">
                <thead>
                    <tr class="border-b-2 border-white/30">
                        <th class="text-left py-3 px-4">Priest</th>
                        <th class="text-left py-3 px-4">Pooja</th>
                        <th class="text-left py-3 px-4">Date</th>
                        <th class="text-left py-3 px-4">Time</th>
                        <th class="text-center py-3 px-4">SMS Sent</th>
                        <th class="text-left py-3 px-4">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $duties = $dutyModel->getAllDetailed();
                    while($duty = $duties->fetch_assoc()):
                    ?>
                    <tr class="border-b border-white/10 hover:bg-white/5 transition-colors">
                        <td class="py-3 px-4"><?= htmlspecialchars($duty['priest_name']) ?></td>
                        <td class="py-3 px-4"><?= htmlspecialchars($duty['pooja_name']) ?></td>
                        <td class="py-3 px-4"><?= date("M j, Y", strtotime($duty['pooja_date'])) ?></td>
                        <td class="py-3 px-4"><?= htmlspecialchars($duty['time_slot']) ?></td>
                        <td class="text-center py-3 px-4">
                            <?php if($duty['notification_sent']): ?>
                                <span class="text-green-400" title="SMS sent">✓</span>
                            <?php else: ?>
                                <span class="text-yellow-400" title="SMS pending">⏳</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4">
                            <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-semibold">
                                <?= ucfirst($duty['status']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
