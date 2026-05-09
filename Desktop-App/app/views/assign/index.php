<div class="max-w-7xl mx-auto space-y-6">
    <!-- Header -->
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
        <h2 class="text-3xl font-bold text-gray-900 mb-2 flex items-center">
            <svg class="w-8 h-8 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Assign Priest Duties
        </h2>
        <p class="text-gray-600 mt-1">Assign poojas to priests and send SMS notifications</p>
    </div>

    <?php if(!empty($message)): ?>
    <div class="p-4 rounded-lg <?= $messageType === 'success' ? 'bg-green-500' : 'bg-red-500' ?> text-white text-center shadow-lg font-semibold">
        <?= htmlspecialchars($message) ?>
    </div>
    <?php endif; ?>

    <!-- Priest Availability Section -->
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Priest Availability
        </h3>
        
        <!-- Date Filter -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <form method="GET" class="flex items-center space-x-4">
                <input type="hidden" name="url" value="assign">
                <label class="text-gray-800 font-semibold whitespace-nowrap">Check availability for date:</label>
                <input 
                    type="date" 
                    name="date" 
                    value="<?= htmlspecialchars($selectedDate) ?>"
                    min="<?= date('Y-m-d') ?>"
                    class="px-4 py-2 bg-white border-2 border-gray-300 text-gray-900 rounded-lg focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-200 transition-all"
                    onchange="this.form.submit()"
                >
            </form>
        </div>

        <!-- Priests Table -->
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b-2 border-gray-300 bg-gray-50">
                        <th class="text-left py-3 px-4 font-bold text-gray-800">Priest Name</th>
                        <th class="text-left py-3 px-4 font-bold text-gray-800">Email</th>
                        <th class="text-left py-3 px-4 font-bold text-gray-800">Phone</th>
                        <th class="text-center py-3 px-4 font-bold text-gray-800">Assigned Duties</th>
                        <th class="text-center py-3 px-4 font-bold text-gray-800">Availability</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if($priests && $priests->num_rows > 0):
                        while($priest = $priests->fetch_assoc()): 
                            $isAvailable = $priest['assigned_count'] < 3;
                            $isPending = $priest['approval_status'] === 'pending';
                    ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors <?= $isPending ? 'bg-yellow-50' : '' ?>">
                        <td class="py-3 px-4 text-gray-900 font-medium">
                            <?= htmlspecialchars($priest['name']) ?>
                            <?php if($isPending): ?>
                                <span class="ml-2 px-2 py-1 bg-yellow-200 text-yellow-800 text-xs font-bold rounded">PENDING APPROVAL</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4 text-gray-700"><?= htmlspecialchars($priest['email']) ?></td>
                        <td class="py-3 px-4 text-gray-700"><?= htmlspecialchars($priest['phone'] ?? 'N/A') ?></td>
                        <td class="text-center py-3 px-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold
                                <?= $priest['assigned_count'] == 0 ? 'bg-green-500 text-white' : 
                                   ($priest['assigned_count'] < 3 ? 'bg-yellow-500 text-white' : 'bg-red-500 text-white') ?>">
                                <?= $priest['assigned_count'] ?> / 3
                            </span>
                        </td>
                        <td class="text-center py-3 px-4">
                            <?php if($isPending): ?>
                                <span class="text-yellow-700 font-bold flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Pending Approval
                                </span>
                            <?php elseif($isAvailable): ?>
                                <span class="text-green-700 font-bold flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Available
                                </span>
                            <?php else: ?>
                                <span class="text-red-700 font-bold">Not Available</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <tr>
                        <td colspan="5" class="text-center py-8 text-gray-500 font-medium">No priests found</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Assignment Form -->
    <div class="bg-white p-8 rounded-xl shadow-md border border-gray-200">
        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create New Assignment
        </h3>
        
        <form method="POST" class="space-y-6">
            <div>
                <label class="block text-gray-800 text-sm font-bold mb-3">Select Priest</label>
                <select name="priest" class="w-full px-4 py-3 bg-white border-2 border-gray-300 text-gray-900 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all" required>
                    <option value="" class="text-gray-900">-- Choose a Priest --</option>
                    <?php 
                    // Reset pointer and show all priests (including pending)
                    $priests->data_seek(0);
                    while($p = $priests->fetch_assoc()): 
                        if($p['assigned_count'] < 3):
                            $isPending = $p['approval_status'] === 'pending';
                    ?>
                    <option value="<?= $p['id'] ?>" class="text-gray-900 <?= $isPending ? 'text-yellow-700 font-bold' : '' ?>">
                        <?= htmlspecialchars($p['name']) ?> 
                        (<?= htmlspecialchars($p['email']) ?>) - 
                        <?= $p['assigned_count'] ?>/3 duties
                        <?= $isPending ? ' [PENDING APPROVAL]' : '' ?>
                    </option>
                    <?php 
                        endif;
                    endwhile; 
                    ?>
                </select>
                <p class="text-gray-600 text-xs mt-2 font-medium">
                    ⚠️ Priests with "Pending Approval" status need admin approval first
                </p>
            </div>

            <div>
                <label class="block text-gray-800 text-sm font-bold mb-3">Select Pooja Schedule</label>
                <select name="schedule" class="w-full px-4 py-3 bg-white border-2 border-gray-300 text-gray-900 rounded-lg focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 transition-all" required>
                    <option value="" class="text-gray-900">-- Choose a Schedule --</option>
                    <?php 
                    // Check if schedules exist
                    if($schedules && $schedules->num_rows > 0):
                        while($s = $schedules->fetch_assoc()): 
                    ?>
                    <option value="<?= $s['id'] ?>" class="text-gray-900">
                        <?= htmlspecialchars($s['pooja_name']) ?> - 
                        <?= date("M j, Y", strtotime($s['pooja_date'])) ?> (<?= htmlspecialchars($s['time_slot']) ?>)
                    </option>
                    <?php 
                        endwhile;
                    else:
                    ?>
                    <option disabled class="text-gray-500 italic">No upcoming pooja schedules found</option>
                    <?php endif; ?>
                </select>
                <p class="text-gray-600 text-xs mt-2 font-medium">Shows all upcoming pooja schedules</p>
            </div>

            <button type="submit" 
                    class="w-full bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] flex items-center justify-center space-x-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Assign Duty & Send SMS</span>
            </button>
        </form>
    </div>

    <!-- Recent Assignments -->
    <div class="bg-white p-6 rounded-xl shadow-md border border-gray-200">
        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
            <svg class="w-6 h-6 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Recent Assignments History
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b-2 border-gray-300 bg-gray-50">
                        <th class="text-left py-3 px-4 font-bold text-gray-800">Priest</th>
                        <th class="text-left py-3 px-4 font-bold text-gray-800">Pooja</th>
                        <th class="text-left py-3 px-4 font-bold text-gray-800">Date</th>
                        <th class="text-left py-3 px-4 font-bold text-gray-800">Time</th>
                        <th class="text-center py-3 px-4 font-bold text-gray-800">SMS Sent</th>
                        <th class="text-left py-3 px-4 font-bold text-gray-800">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $duties = $dutyModel->getAllDetailed();
                    while($duty = $duties->fetch_assoc()):
                    ?>
                    <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                        <td class="py-3 px-4 text-gray-900 font-medium"><?= htmlspecialchars($duty['priest_name']) ?></td>
                        <td class="py-3 px-4 text-gray-900 font-medium"><?= htmlspecialchars($duty['pooja_name']) ?></td>
                        <td class="py-3 px-4 text-gray-700"><?= date("M j, Y", strtotime($duty['pooja_date'])) ?></td>
                        <td class="py-3 px-4 text-gray-700"><?= htmlspecialchars($duty['time_slot']) ?></td>
                        <td class="text-center py-3 px-4">
                            <?php if($duty['notification_sent']): ?>
                                <span class="text-green-600 font-bold" title="SMS sent">✓</span>
                            <?php else: ?>
                                <span class="text-yellow-600 font-bold" title="SMS pending">⏳</span>
                            <?php endif; ?>
                        </td>
                        <td class="py-3 px-4">
                            <span class="bg-indigo-500 text-white px-3 py-1 rounded-full text-xs font-bold">
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
