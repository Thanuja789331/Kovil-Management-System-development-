<!-- Pooja Requests Management Page -->
<div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center space-x-4 mb-4">
        <a href="?url=dashboard" class="group flex items-center space-x-2 bg-white/80 hover:bg-white backdrop-blur-sm px-4 py-2 rounded-xl shadow-md transition-all duration-200 hover:scale-105 active:scale-95">
            <svg class="w-5 h-5 text-orange-600 group-hover:text-orange-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="font-semibold text-orange-600 group-hover:text-orange-700">Back to Dashboard</span>
        </a>
    </div>

    <!-- Header -->
    <div class="glass-card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Pooja Requests</h1>
                <p class="text-gray-600">Manage devotee pooja requests</p>
            </div>
            <div class="flex items-center space-x-2 self-start sm:self-auto">
                <span class="bg-orange-100 text-orange-700 px-4 py-2 rounded-lg font-semibold">
                    <?= ($data instanceof mysqli_result) ? $data->num_rows : 0 ?> Total Requests
                </span>
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

    <!-- Requests List -->
    <div class="space-y-4">
        <?php 
        $requests = [];
        if ($data instanceof mysqli_result) {
            $requests = $data->fetch_all(MYSQLI_ASSOC);
        } elseif (is_array($data)) {
            $requests = $data;
        }
        ?>
        <?php if(!empty($requests)): ?>
            <?php foreach($requests as $request): ?>
            <div class="glass-card p-6 card-hover border-l-4 <?= 
                $request['status'] === 'pending' ? 'border-yellow-500' : 
                ($request['status'] === 'approved' ? 'border-green-500' : 
                ($request['status'] === 'rejected' ? 'border-red-500' : 'border-blue-500')) 
            ?>">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <!-- Request Details -->
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-3">
                            <h3 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($request['pooja_name']) ?></h3>
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase
                                <?= $request['status'] === 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                   ($request['status'] === 'approved' ? 'bg-green-100 text-green-700' : 
                                   ($request['status'] === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700')) ?>">
                                <?= ucfirst($request['status']) ?>
                            </span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <span><?= htmlspecialchars($request['user_name']) ?></span>
                            </div>

                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                                <span><?= htmlspecialchars($request['user_email']) ?></span>
                            </div>

                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span><?= date('F d, Y', strtotime($request['preferred_date'])) ?></span>
                            </div>

                            <?php if(!empty($request['preferred_time_slot'])): ?>
                            <div class="flex items-center space-x-2 text-sm text-gray-600">
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span><?= date('g:i A', strtotime($request['preferred_time_slot'])) ?></span>
                            </div>
                            <?php endif; ?>
                        </div>

                        <?php if(!empty($request['special_requests'])): ?>
                        <div class="bg-gray-50 p-3 rounded-lg mb-3">
                            <p class="text-sm text-gray-700"><span class="font-semibold">Special Requests:</span> <?= htmlspecialchars($request['special_requests']) ?></p>
                        </div>
                        <?php endif; ?>

                        <?php if(!empty($request['admin_remarks'])): ?>
                        <div class="bg-blue-50 p-3 rounded-lg mb-3">
                            <p class="text-sm text-blue-700"><span class="font-semibold">Admin Remarks:</span> <?= htmlspecialchars($request['admin_remarks']) ?></p>
                        </div>
                        <?php endif; ?>

                        <div class="text-xs text-gray-500">
                            Requested on: <?= date('F d, Y g:i A', strtotime($request['created_at'])) ?>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-col space-y-2">
                        <?php if($request['status'] === 'pending'): ?>
                        <form action="?url=pooja-request&action=update-status" method="POST" class="space-y-2">
                            <?= csrfField() ?>
                            <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                            <select name="status" class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none text-sm">
                                <option value="approved">Approve</option>
                                <option value="rejected">Reject</option>
                                <option value="scheduled">Mark as Scheduled</option>
                            </select>
                            <textarea name="admin_remarks" rows="2" placeholder="Add remarks (optional)" class="w-full px-3 py-2 border-2 border-gray-200 rounded-lg focus:border-orange-500 focus:outline-none text-sm resize-none"></textarea>
                            <button type="submit" class="w-full bg-gradient-to-r from-green-500 to-green-600 hover:from-green-400 hover:to-green-500 text-white px-4 py-2 rounded-lg font-semibold transition-all text-sm">
                                Update Status
                            </button>
                        </form>
                        <?php endif; ?>

                        <form action="?url=pooja-request&action=delete" method="POST" onsubmit="return confirm('Are you sure you want to delete this request?');">
                            <?= csrfField() ?>
                            <input type="hidden" name="request_id" value="<?= $request['id'] ?>">
                            <button type="submit" class="w-full bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg font-semibold transition-all text-sm">
                                Delete Request
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="glass-card p-12 text-center">
                <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-gray-500 text-lg">No pooja requests at the moment</p>
            </div>
        <?php endif; ?>
    </div>
</div>
