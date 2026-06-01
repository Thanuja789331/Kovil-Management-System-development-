<!-- Manage Users Page -->
<div class="min-h-[calc(100vh-8rem)] py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">👥 User Management</h1>
            <p class="text-gray-600 font-medium">View and manage all registered users in the system</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
            <div class="glass-card p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold mb-1">Total Users</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $stats['total'] ?? 0 ?></h3>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="glass-card p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold mb-1">Devotees</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $stats['devotee'] ?? 0 ?></h3>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="glass-card p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold mb-1">Priests</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $stats['priest'] ?? 0 ?></h3>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-accent-500 to-accent-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="glass-card p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold mb-1">Management</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $stats['management'] ?? 0 ?></h3>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="glass-card p-4 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex-1">
                    <input
                        type="text"
                        id="searchInput"
                        placeholder="Search by name or email..."
                        class="w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl outline-none"
                        onkeyup="filterTable()"
                    >
                </div>
                <div class="flex gap-3">
                    <select id="roleFilter" onchange="filterTable()"
                        class="px-3 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 rounded-xl outline-none cursor-pointer text-sm">
                        <option value="">All Roles</option>
                        <option value="devotee">Devotees</option>
                        <option value="priest">Priests</option>
                        <option value="management">Management</option>
                    </select>
                    <select id="statusFilter" onchange="filterTable()"
                        class="px-3 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 rounded-xl outline-none cursor-pointer text-sm">
                        <option value="">All Status</option>
                        <option value="approved">Approved</option>
                        <option value="pending">Pending</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="glass-card p-4 sm:p-6 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                All Registered Users
            </h2>

            <?php if(!empty($data)): ?>
            <div class="overflow-x-auto -mx-2 sm:mx-0">
                <table class="w-full min-w-[800px] text-left" id="usersTable">
                    <thead>
                        <tr class="border-b-2 border-gray-300">
                            <th class="py-3 px-3 text-gray-700 font-semibold">#</th>
                            <th class="py-3 px-3 text-gray-700 font-semibold">Name</th>
                            <th class="py-3 px-3 text-gray-700 font-semibold">Email</th>
                            <th class="py-3 px-3 text-gray-700 font-semibold">Phone</th>
                            <th class="py-3 px-3 text-gray-700 font-semibold">Role</th>
                            <th class="py-3 px-3 text-gray-700 font-semibold">Status</th>
                            <th class="py-3 px-3 text-gray-700 font-semibold">Registered On</th>
                            <th class="py-3 px-3 text-gray-700 font-semibold text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $counter = 1; foreach($data as $user): ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors"
                            data-role="<?= htmlspecialchars($user['role']) ?>"
                            data-status="<?= htmlspecialchars($user['approval_status']) ?>">
                            <td class="py-3 px-3 text-gray-600 font-medium"><?= $counter++ ?></td>
                            <td class="py-3 px-3">
                                <div class="flex items-center space-x-2">
                                    <?php
                                    $avatarColor = $user['role'] === 'priest' ? 'accent' : ($user['role'] === 'management' ? 'primary' : 'secondary');
                                    ?>
                                    <div class="w-9 h-9 bg-gradient-to-br from-<?= $avatarColor ?>-500 to-<?= $avatarColor ?>-600 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-white font-bold text-sm"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
                                    </div>
                                    <span class="text-gray-800 font-medium"><?= htmlspecialchars($user['name']) ?></span>
                                </div>
                            </td>
                            <td class="py-3 px-3 text-gray-800 text-sm"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="py-3 px-3 text-gray-600 text-sm"><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    <?= $user['role'] === 'priest' ? 'bg-accent-100 text-accent-700' :
                                       ($user['role'] === 'management' ? 'bg-primary-100 text-primary-700' : 'bg-secondary-100 text-secondary-700') ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-1 rounded-full text-xs font-semibold
                                    <?= $user['approval_status'] === 'approved' ? 'bg-green-100 text-green-700' :
                                       ($user['approval_status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') ?>">
                                    <?= ucfirst($user['approval_status']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-3 text-gray-600 text-sm"><?= date("M j, Y", strtotime($user['created_at'])) ?></td>
                            <td class="py-3 px-3">
                                <div class="flex items-center justify-center gap-2">
                                    <button
                                        onclick='openEditModal(<?= htmlspecialchars(json_encode($user), ENT_QUOTES) ?>)'
                                        class="bg-blue-100 hover:bg-blue-200 text-blue-700 px-3 py-1.5 rounded-lg font-semibold transition-all text-xs">
                                        Edit
                                    </button>
                                    <?php if($user['id'] != $_SESSION['user']['id']): ?>
                                    <button
                                        onclick="openDeleteModal(<?= intval($user['id']) ?>, '<?= htmlspecialchars($user['name'], ENT_QUOTES) ?>')"
                                        class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded-lg font-semibold transition-all text-xs">
                                        Delete
                                    </button>
                                    <?php else: ?>
                                    <span class="text-xs text-gray-400 italic px-1">You</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-12">
                <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-gray-600 text-lg font-medium">No users found in the system</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-bold text-gray-800">Edit User</h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form method="POST" action="?url=manage-users&action=update" class="space-y-4">
            <?= csrfField() ?>
            <input type="hidden" name="user_id" id="edit_user_id">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="edit_name" required
                    class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-colors">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" id="edit_email" required
                    class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-colors">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Phone</label>
                <input type="text" name="phone" id="edit_phone"
                    class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-colors"
                    placeholder="e.g. 9876543210">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Role</label>
                    <select name="role" id="edit_role"
                        class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-colors">
                        <option value="devotee">Devotee</option>
                        <option value="priest">Priest</option>
                        <option value="management">Management</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
                    <select name="approval_status" id="edit_status"
                        class="w-full px-4 py-2.5 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:outline-none transition-colors">
                        <option value="approved">Approved</option>
                        <option value="pending">Pending</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
            <div class="flex space-x-3 pt-2">
                <button type="submit"
                    class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-400 hover:to-blue-500 text-white font-bold py-3 rounded-xl shadow-lg transition-all duration-200">
                    Save Changes
                </button>
                <button type="button" onclick="closeEditModal()"
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 rounded-xl transition-all duration-200">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full p-6">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">Delete User?</h3>
            <p class="text-gray-600">You are about to delete <span id="deleteUserName" class="font-semibold text-red-600"></span>.</p>
            <p class="text-sm text-gray-500 mt-2">This action cannot be undone. Associated bookings and data may be affected.</p>
        </div>
        <form method="POST" action="?url=manage-users&action=delete">
            <?= csrfField() ?>
            <input type="hidden" name="user_id" id="deleteUserId">
            <div class="flex space-x-3">
                <button type="submit"
                    class="flex-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-400 hover:to-red-500 text-white font-bold py-3 rounded-xl shadow-lg transition-all duration-200">
                    Delete User
                </button>
                <button type="button" onclick="closeDeleteModal()"
                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 font-bold py-3 rounded-xl transition-all duration-200">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function filterTable() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const role   = document.getElementById('roleFilter').value;
    const status = document.getElementById('statusFilter').value;
    document.querySelectorAll('#usersTable tbody tr').forEach(function(row) {
        const name  = row.cells[1].textContent.toLowerCase();
        const email = row.cells[2].textContent.toLowerCase();
        const matchSearch = name.includes(search) || email.includes(search);
        const matchRole   = !role   || row.dataset.role   === role;
        const matchStatus = !status || row.dataset.status === status;
        row.style.display = (matchSearch && matchRole && matchStatus) ? '' : 'none';
    });
}

function openEditModal(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_name').value    = user.name;
    document.getElementById('edit_email').value   = user.email;
    document.getElementById('edit_phone').value   = user.phone || '';
    document.getElementById('edit_role').value    = user.role;
    document.getElementById('edit_status').value  = user.approval_status;
    document.getElementById('editModal').classList.remove('hidden');
}

function closeEditModal() {
    document.getElementById('editModal').classList.add('hidden');
}

function openDeleteModal(userId, userName) {
    document.getElementById('deleteUserId').value       = userId;
    document.getElementById('deleteUserName').textContent = userName;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') { closeEditModal(); closeDeleteModal(); }
});

document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>
