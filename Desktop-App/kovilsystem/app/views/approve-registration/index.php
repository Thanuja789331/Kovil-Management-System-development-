<!-- Approve Registration Page -->
<div class="min-h-[calc(100vh-8rem)] py-12 px-4">
    <div class="max-w-6xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">📋 Registration Approvals</h1>
            <p class="text-gray-600 font-medium">Review and approve pending priest and management registrations</p>
        </div>

        <?php if(!empty($message)): ?>
        <div class="mb-6 p-4 rounded-xl shadow-lg <?= $messageType === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>

        <!-- Pending Registrations Table -->
        <div class="glass-card p-6 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Pending Registrations
            </h2>

            <?php if($data && $data->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b-2 border-gray-300">
                            <th class="py-3 px-4 text-gray-700 font-semibold">Name</th>
                            <th class="py-3 px-4 text-gray-700 font-semibold">Email</th>
                            <th class="py-3 px-4 text-gray-700 font-semibold">Phone</th>
                            <th class="py-3 px-4 text-gray-700 font-semibold">Role</th>
                            <th class="py-3 px-4 text-gray-700 font-semibold">Registered On</th>
                            <th class="py-3 px-4 text-gray-700 font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = $data->fetch_assoc()): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                            <td class="py-3 px-4 text-gray-800"><?= htmlspecialchars($user['name']) ?></td>
                            <td class="py-3 px-4 text-gray-800"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="py-3 px-4 text-gray-800"><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></td>
                            <td class="py-3 px-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                    <?= $user['role'] === 'priest' ? 'bg-accent-100 text-accent-700' : 'bg-primary-100 text-primary-700' ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-800"><?= date("M j, Y g:i A", strtotime($user['created_at'])) ?></td>
                            <td class="py-3 px-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Approve Button -->
                                    <button onclick="openApproveModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['name']) ?>')" 
                                            class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        <span>Approve</span>
                                    </button>
                                    
                                    <!-- Reject Button -->
                                    <button onclick="openRejectModal(<?= $user['id'] ?>, '<?= htmlspecialchars($user['name']) ?>')" 
                                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        <span>Reject</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-12">
                <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-600 text-lg font-medium">No pending registrations awaiting approval</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Approve Registration?</h3>
            <p class="text-gray-600">User: <span id="approveUserName" class="font-semibold"></span></p>
        </div>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="user_id" id="approveUserId">
            <input type="hidden" name="action" value="approve">
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Remarks (Optional)</label>
                <textarea name="remarks" rows="3" placeholder="Add any notes..." class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 text-gray-800 focus:border-green-500 focus:bg-white transition-all duration-200 rounded-xl resize-none"></textarea>
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-400 hover:to-green-500 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                    Confirm Approval
                </button>
                <button type="button" onclick="closeApproveModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-xl transition-all duration-200">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Reject Registration?</h3>
            <p class="text-gray-600">User: <span id="rejectUserName" class="font-semibold"></span></p>
        </div>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="user_id" id="rejectUserId">
            <input type="hidden" name="action" value="reject">
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Reason for Rejection <span class="text-red-500">*</span></label>
                <textarea name="remarks" rows="3" placeholder="Please provide a reason..." class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 text-gray-800 focus:border-red-500 focus:bg-white transition-all duration-200 rounded-xl resize-none" required></textarea>
            </div>

            <div class="flex space-x-3">
                <button type="submit" class="flex-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-400 hover:to-red-500 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                    Confirm Rejection
                </button>
                <button type="button" onclick="closeRejectModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 px-6 rounded-xl transition-all duration-200">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openApproveModal(userId, userName) {
    document.getElementById('approveUserId').value = userId;
    document.getElementById('approveUserName').textContent = userName;
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

function openRejectModal(userId, userName) {
    document.getElementById('rejectUserId').value = userId;
    document.getElementById('rejectUserName').textContent = userName;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

// Close modals on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeApproveModal();
        closeRejectModal();
    }
});

// Close modals on outside click
document.getElementById('approveModal').addEventListener('click', function(e) {
    if (e.target === this) closeApproveModal();
});

document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) closeRejectModal();
});
</script>
