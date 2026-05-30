<?php 
// Convert mysqli_result to array
$requests = [];
if ($data instanceof mysqli_result) {
    $requests = $data->fetch_all(MYSQLI_ASSOC);
} elseif (is_array($data)) {
    $requests = $data;
}

// Calculate status counts for filter tabs
$countAll = count($requests);
$countPending = 0;
$countApprovedScheduled = 0;
$countRejected = 0;

foreach($requests as $request) {
    $status = $request['status'] ?? 'pending';
    if ($status === 'pending') {
        $countPending++;
    } elseif ($status === 'approved' || $status === 'scheduled') {
        $countApprovedScheduled++;
    } elseif ($status === 'rejected') {
        $countRejected++;
    }
}
?>

<div class="min-h-[calc(100vh-8rem)] py-12 px-4 transition-all duration-300">
    <div class="max-w-6xl mx-auto space-y-8">
        
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white/10 backdrop-blur-md p-6 rounded-2xl border border-white/20 shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="w-16 h-16 bg-gradient-to-br from-secondary-400 to-secondary-600 rounded-2xl flex items-center justify-center shadow-lg transform hover:rotate-6 transition-transform duration-300">
                    <span class="text-3xl">🙏</span>
                </div>
                <div>
                    <h1 class="text-3xl font-extrabold text-white tracking-wide mb-1"><?= trans('my_custom_requests') ?></h1>
                    <p class="text-emerald-100/80 text-sm font-medium">Track and manage your custom requested ceremonies</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-3">
                <a href="?url=schedule" class="group flex items-center space-x-2 bg-white/10 hover:bg-white/20 border border-white/20 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 active:scale-95">
                    <svg class="w-4 h-4 text-white group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span><?= trans('back') ?></span>
                </a>
                
                <a href="?url=schedule" class="flex items-center space-x-2 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-lg transition-all duration-200 transform hover:scale-105 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span><?= trans('book_pooja_devotee_label') ?></span>
                </a>
            </div>
        </div>

        <?php if(!empty($requests)): ?>
        
        <!-- Filter Tabs -->
        <div class="flex flex-wrap gap-2 p-1.5 bg-white/5 backdrop-blur-sm rounded-xl border border-white/10 max-w-2xl">
            <button data-filter="all" class="tab-btn flex items-center space-x-2 px-4 py-2 rounded-lg font-bold text-sm transition-all duration-200 bg-gradient-to-r from-primary-600 to-primary-700 text-white shadow-md">
                <span><?= trans('all_tabs') ?></span>
                <span class="bg-white/20 text-white text-xs px-2 py-0.5 rounded-full font-extrabold"><?= $countAll ?></span>
            </button>
            <button data-filter="pending" class="tab-btn flex items-center space-x-2 px-4 py-2 rounded-lg font-bold text-sm transition-all duration-200 bg-white/10 text-gray-200 hover:bg-white/20">
                <span><?= trans('pending_requests') ?></span>
                <span class="bg-amber-500/20 text-amber-300 text-xs px-2 py-0.5 rounded-full font-extrabold"><?= $countPending ?></span>
            </button>
            <button data-filter="approved_scheduled" class="tab-btn flex items-center space-x-2 px-4 py-2 rounded-lg font-bold text-sm transition-all duration-200 bg-white/10 text-gray-200 hover:bg-white/20">
                <span><?= trans('approved_scheduled') ?></span>
                <span class="bg-emerald-500/20 text-emerald-300 text-xs px-2 py-0.5 rounded-full font-extrabold"><?= $countApprovedScheduled ?></span>
            </button>
            <button data-filter="rejected" class="tab-btn flex items-center space-x-2 px-4 py-2 rounded-lg font-bold text-sm transition-all duration-200 bg-white/10 text-gray-200 hover:bg-white/20">
                <span><?= trans('declined') ?></span>
                <span class="bg-rose-500/20 text-rose-300 text-xs px-2 py-0.5 rounded-full font-extrabold"><?= $countRejected ?></span>
            </button>
        </div>

        <!-- Requests Grid -->
        <div id="requests-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 transition-all duration-300">
            <?php foreach($requests as $request): ?>
            <?php 
            $status = $request['status'] ?? 'pending';
            
            // Premium layout options based on status
            switch($status) {
                case 'pending':
                    $cardBorder = 'border-amber-400/30 hover:border-amber-400/60 bg-amber-950/5';
                    $badgeStyle = 'bg-amber-500/10 text-amber-400 border-amber-500/20';
                    $statusIcon = '🕒';
                    $statusText = trans('pending');
                    break;
                case 'approved':
                    $cardBorder = 'border-indigo-400/30 hover:border-indigo-400/60 bg-indigo-950/5';
                    $badgeStyle = 'bg-indigo-500/10 text-indigo-400 border-indigo-500/20';
                    $statusIcon = '✅';
                    $statusText = trans('confirmed');
                    break;
                case 'scheduled':
                    $cardBorder = 'border-emerald-400/30 hover:border-emerald-400/60 bg-emerald-950/5';
                    $badgeStyle = 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20';
                    $statusIcon = '📅';
                    $statusText = 'Scheduled';
                    break;
                case 'rejected':
                    $cardBorder = 'border-rose-400/30 hover:border-rose-400/60 bg-rose-950/5';
                    $badgeStyle = 'bg-rose-500/10 text-rose-400 border-rose-500/20';
                    $statusIcon = '❌';
                    $statusText = trans('declined');
                    break;
                default:
                    $cardBorder = 'border-gray-400/30 hover:border-gray-400/60 bg-gray-950/5';
                    $badgeStyle = 'bg-gray-500/10 text-gray-400 border-gray-500/20';
                    $statusIcon = '❔';
                    $statusText = $status;
            }
            ?>
            <div data-status="<?= htmlspecialchars($status) ?>" class="request-card glass-card p-6 flex flex-col justify-between border <?= $cardBorder ?> rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 bg-white/95">
                <div>
                    <!-- Card Header -->
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-gray-100">
                        <span class="text-xs font-bold text-gray-500 bg-gray-100 px-3 py-1 rounded-full border border-gray-200/50">
                            REQ #<?= htmlspecialchars($request['id']) ?>
                        </span>
                        
                        <span class="flex items-center space-x-1 text-xs font-black uppercase tracking-wider px-2.5 py-1 rounded-full border <?= $badgeStyle ?>">
                            <span><?= $statusIcon ?></span>
                            <span><?= htmlspecialchars($statusText) ?></span>
                        </span>
                    </div>

                    <!-- Pooja Name -->
                    <h3 class="text-xl font-bold text-gray-800 tracking-tight line-clamp-1 mb-4">
                        <?= htmlspecialchars($request['pooja_name']) ?>
                    </h3>

                    <!-- Date & Time details -->
                    <div class="space-y-3 mb-5">
                        <div class="flex items-center space-x-3 bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                            <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Preferred Date</p>
                                <p class="text-gray-800 text-sm font-semibold"><?= date("l, F j, Y", strtotime($request['preferred_date'])) ?></p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-3 bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                            <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Preferred Time Slot</p>
                                <p class="text-gray-800 text-sm font-semibold">
                                    <?= !empty($request['preferred_time_slot']) ? date('g:i A', strtotime($request['preferred_time_slot'])) : 'Any time' ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- User Notes -->
                    <?php if(!empty($request['special_requests'])): ?>
                    <div class="bg-orange-50/50 p-3 rounded-xl mb-3 border border-orange-100/50">
                        <p class="text-[10px] text-orange-600 font-black uppercase mb-1 tracking-wider">Devotee Remarks</p>
                        <p class="text-xs text-gray-700 italic">"<?= htmlspecialchars($request['special_requests']) ?>"</p>
                    </div>
                    <?php endif; ?>

                    <!-- Admin Remarks -->
                    <?php if(!empty($request['admin_remarks'])): ?>
                    <div class="bg-blue-50/80 p-3 rounded-xl mb-3 border border-blue-100">
                        <p class="text-[10px] text-blue-600 font-black uppercase mb-1 tracking-wider">Temple Office Remarks</p>
                        <p class="text-xs text-blue-900 font-medium">"<?= htmlspecialchars($request['admin_remarks']) ?>"</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Footer Timestamp -->
                <div class="text-[10px] text-gray-400 font-bold tracking-wider text-right pt-3 border-t border-gray-100 mt-2">
                    Requested on: <?= date("M j, Y g:i A", strtotime($request['created_at'])) ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Empty state for filtered views -->
        <div id="tab-empty-state" class="glass-card p-12 text-center max-w-xl mx-auto shadow-2xl border border-white/20 bg-white/90 rounded-2xl" style="display: none;">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="text-4xl text-gray-400">✨</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">No Requests Found</h3>
            <p class="text-gray-600">There are no requests matching this status filter currently.</p>
        </div>

        <?php else: ?>
        
        <!-- Main Empty State -->
        <div class="glass-card p-12 text-center max-w-xl mx-auto shadow-2xl border border-white/20 bg-white/90 rounded-2xl">
            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6">
                <span class="text-4xl text-gray-400">✨</span>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">No Pooja Requests</h3>
            <p class="text-gray-600 mb-6">Submit a custom request to have specialized rituals and ceremonies performed on auspicios days!</p>
            <a href="?url=schedule" class="bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-400 hover:to-orange-500 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-all duration-200">
                Go to Schedule to Request
            </a>
        </div>
        
        <?php endif; ?>
    </div>
</div>

<!-- Tabs Filtering Logic -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.tab-btn');
    const cards = document.querySelectorAll('.request-card');
    const emptyState = document.getElementById('tab-empty-state');
    
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove active classes from all tabs
            tabs.forEach(t => {
                t.classList.remove('bg-gradient-to-r', 'from-primary-600', 'to-primary-700', 'text-white', 'shadow-md');
                t.classList.add('bg-white/10', 'text-gray-200', 'hover:bg-white/20');
            });
            
            // Add active class to clicked tab
            tab.classList.add('bg-gradient-to-r', 'from-primary-600', 'to-primary-700', 'text-white', 'shadow-md');
            tab.classList.remove('bg-white/10', 'text-gray-200', 'hover:bg-white/20');
            
            const filter = tab.getAttribute('data-filter');
            let visibleCount = 0;
            
            cards.forEach(card => {
                const status = card.getAttribute('data-status');
                if (filter === 'all') {
                    card.style.display = 'flex';
                    visibleCount++;
                } else if (filter === 'pending' && status === 'pending') {
                    card.style.display = 'flex';
                    visibleCount++;
                } else if (filter === 'approved_scheduled' && (status === 'approved' || status === 'scheduled')) {
                    card.style.display = 'flex';
                    visibleCount++;
                } else if (filter === 'rejected' && status === 'rejected') {
                    card.style.display = 'flex';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Toggle visibility of filtered empty state
            if (visibleCount === 0) {
                emptyState.style.display = 'block';
            } else {
                emptyState.style.display = 'none';
            }
        });
    });
});
</script>
