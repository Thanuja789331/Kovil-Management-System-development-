<!-- Manage Users Page -->
<div class="min-h-[calc(100vh-8rem)] py-12 px-4">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">👥 User Management</h1>
            <p class="text-gray-600 font-medium">View and manage all registered users in the system</p>
        </div>

        <?php if(!empty($message)): ?>
        <div class="mb-6 p-4 rounded-xl shadow-lg <?= $messageType === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white' ?>">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="glass-card p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold mb-1">Total Users</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $stats['total'] ?? 0 ?></h3>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Devotees -->
            <div class="glass-card p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold mb-1">Devotees</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $stats['devotee'] ?? 0 ?></h3>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Priests -->
            <div class="glass-card p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold mb-1">Priests</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $stats['priest'] ?? 0 ?></h3>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-accent-500 to-accent-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Management -->
            <div class="glass-card p-6 card-hover">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 font-semibold mb-1">Management</p>
                        <h3 class="text-3xl font-bold text-gray-800"><?= $stats['management'] ?? 0 ?></h3>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-primary-500 to-primary-700 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter and Search -->
        <div class="glass-card p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex-1">
                    <input 
                        type="text" 
                        id="searchInput" 
                        placeholder="🔍 Search by name or email..." 
                        class="w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl outline-none"
                        onkeyup="filterTable()"
                    >
                </div>
                <div class="flex gap-3">
                    <select 
                        id="roleFilter" 
                        onchange="filterTable()"
                        class="px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl outline-none cursor-pointer"
                    >
                        <option value="">All Roles</option>
                        <option value="devotee">Devotees</option>
                        <option value="priest">Priests</option>
                        <option value="management">Management</option>
                    </select>
                    <select 
                        id="statusFilter" 
                        onchange="filterTable()"
                        class="px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl outline-none cursor-pointer"
                    >
                        <option value="">All Status</option>
                        <option value="approved">Approved</option>
                        <option value="pending">Pending</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="glass-card p-6 card-hover">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                <svg class="w-6 h-6 mr-2 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                All Registered Users
            </h2>

            <?php if($data && $data->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left" id="usersTable">
                    <thead>
                        <tr class="border-b-2 border-gray-300">
                            <th class="py-3 px-4 text-gray-700 font-semibold">#</th>
                            <th class="py-3 px-4 text-gray-700 font-semibold">Name</th>
                            <th class="py-3 px-4 text-gray-700 font-semibold">Email</th>
                            <th class="py-3 px-4 text-gray-700 font-semibold">Phone</th>
                            <th class="py-3 px-4 text-gray-700 font-semibold">Role</th>
                            <th class="py-3 px-4 text-gray-700 font-semibold">Status</th>
                            <th class="py-3 px-4 text-gray-700 font-semibold">Registered On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $counter = 1;
                        while($user = $data->fetch_assoc()): 
                        ?>
                        <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors" data-role="<?= $user['role'] ?>" data-status="<?= $user['approval_status'] ?>">
                            <td class="py-3 px-4 text-gray-600 font-medium"><?= $counter++ ?></td>
                            <td class="py-3 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-<?= $user['role'] === 'priest' ? 'accent' : ($user['role'] === 'management' ? 'primary' : 'secondary') ?>-500 to-<?= $user['role'] === 'priest' ? 'accent' : ($user['role'] === 'management' ? 'primary' : 'secondary') ?>-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold text-sm"><?= strtoupper(substr($user['name'], 0, 1)) ?></span>
                                    </div>
                                    <span class="text-gray-800 font-medium"><?= htmlspecialchars($user['name']) ?></span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-gray-800"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="py-3 px-4 text-gray-800"><?= htmlspecialchars($user['phone'] ?? 'N/A') ?></td>
                            <td class="py-3 px-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold 
                                    <?= $user['role'] === 'priest' ? 'bg-accent-100 text-accent-700' : 
                                       ($user['role'] === 'management' ? 'bg-primary-100 text-primary-700' : 'bg-secondary-100 text-secondary-700') ?>">
                                    <?= ucfirst($user['role']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    <?= $user['approval_status'] === 'approved' ? 'bg-green-100 text-green-700' : 
                                       ($user['approval_status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') ?>">
                                    <?= ucfirst($user['approval_status']) ?>
                                </span>
                            </td>
                            <td class="py-3 px-4 text-gray-800"><?= date("M j, Y g:i A", strtotime($user['created_at'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
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

<script>
function filterTable() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const roleFilter = document.getElementById('roleFilter').value;
    const statusFilter = document.getElementById('statusFilter').value;
    
    const rows = document.querySelectorAll('#usersTable tbody tr');
    
    rows.forEach(row => {
        const name = row.cells[1].textContent.toLowerCase();
        const email = row.cells[2].textContent.toLowerCase();
        const role = row.getAttribute('data-role');
        const status = row.getAttribute('data-status');
        
        const matchesSearch = name.includes(searchInput) || email.includes(searchInput);
        const matchesRole = !roleFilter || role === roleFilter;
        const matchesStatus = !statusFilter || status === statusFilter;
        
        if (matchesSearch && matchesRole && matchesStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
