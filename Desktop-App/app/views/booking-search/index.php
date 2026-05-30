<!-- Management: Search Bookings -->
<div class="space-y-6">
    <!-- Header -->
    <div class="glass-card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-1">Search Bookings</h1>
                <p class="text-gray-500 text-sm">Find a devotee's booking by name, phone number, or booking reference</p>
            </div>
            <a href="?url=dashboard" class="inline-flex items-center space-x-2 bg-white/80 hover:bg-white px-4 py-2 rounded-xl shadow-md transition-all text-primary-700 font-semibold text-sm self-start">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>Back to Dashboard</span>
            </a>
        </div>
    </div>

    <!-- Search Form -->
    <div class="glass-card p-6">
        <form method="GET" action="" class="flex flex-col sm:flex-row gap-3">
            <input type="hidden" name="url" value="booking-search">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input
                    type="text"
                    name="q"
                    value="<?= htmlspecialchars($searchQuery ?? '') ?>"
                    placeholder="Devotee name, phone number, or booking reference…"
                    autofocus
                    class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 focus:outline-none transition-all text-gray-800"
                >
            </div>
            <button type="submit" class="bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-500 hover:to-primary-600 text-white px-6 py-3 rounded-xl font-bold shadow-md transition-all whitespace-nowrap">
                Search
            </button>
            <?php if (!empty($searchQuery)): ?>
            <a href="?url=booking-search" class="px-4 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold rounded-xl transition-all text-center">
                Clear
            </a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (!empty($searchQuery)): ?>
    <!-- Results -->
    <div class="glass-card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-800">
                Results for "<span class="text-primary-700"><?= htmlspecialchars($searchQuery) ?></span>"
            </h2>
            <span class="bg-primary-100 text-primary-700 text-sm font-bold px-3 py-1 rounded-full">
                <?= count($data) ?> booking<?= count($data) !== 1 ? 's' : '' ?> found
            </span>
        </div>

        <?php if (!empty($data)): ?>
        <!-- Desktop table -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Devotee</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Phone</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Pooja</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Date & Time</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($data as $booking):
                        $statusClass = match($booking['status']) {
                            'confirmed' => 'bg-green-100 text-green-700',
                            'cancelled' => 'bg-red-100 text-red-700',
                            default     => 'bg-gray-100 text-gray-600',
                        };
                        $isPast = $booking['pooja_date'] < date('Y-m-d');
                    ?>
                    <tr class="hover:bg-gray-50 transition-colors <?= $isPast ? 'opacity-70' : '' ?>">
                        <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-700">
                            <?= htmlspecialchars($booking['booking_reference']) ?>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-800"><?= htmlspecialchars($booking['user_name']) ?></p>
                            <p class="text-gray-500 text-xs"><?= htmlspecialchars($booking['user_email']) ?></p>
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            <?= htmlspecialchars($booking['devotee_phone'] ?: ($booking['user_phone'] ?: '—')) ?>
                        </td>
                        <td class="px-6 py-4 font-semibold text-gray-800">
                            <?= htmlspecialchars($booking['pooja_name']) ?>
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            <p><?= date('d M Y', strtotime($booking['pooja_date'])) ?></p>
                            <p class="text-xs text-gray-500"><?= htmlspecialchars($booking['time_slot']) ?></p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-xs font-bold uppercase <?= $statusClass ?>">
                                <?= ucfirst($booking['status']) ?>
                            </span>
                            <?php if ($booking['status'] === 'cancelled' && !empty($booking['cancelled_at'])): ?>
                            <p class="text-xs text-gray-400 mt-1">on <?= date('d M Y', strtotime($booking['cancelled_at'])) ?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Mobile cards -->
        <div class="md:hidden divide-y divide-gray-100">
            <?php foreach ($data as $booking):
                $statusClass = match($booking['status']) {
                    'confirmed' => 'bg-green-100 text-green-700',
                    'cancelled' => 'bg-red-100 text-red-700',
                    default     => 'bg-gray-100 text-gray-600',
                };
            ?>
            <div class="p-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="font-mono text-xs text-gray-500"><?= htmlspecialchars($booking['booking_reference']) ?></span>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold uppercase <?= $statusClass ?>"><?= ucfirst($booking['status']) ?></span>
                </div>
                <p class="font-bold text-gray-800"><?= htmlspecialchars($booking['user_name']) ?></p>
                <p class="text-sm text-gray-600"><?= htmlspecialchars($booking['devotee_phone'] ?: ($booking['user_phone'] ?: '—')) ?></p>
                <p class="text-sm font-semibold text-gray-700"><?= htmlspecialchars($booking['pooja_name']) ?></p>
                <p class="text-sm text-gray-500"><?= date('d M Y', strtotime($booking['pooja_date'])) ?> &middot; <?= htmlspecialchars($booking['time_slot']) ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <?php else: ?>
        <div class="py-16 text-center">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <p class="text-gray-500 font-semibold text-lg">No bookings found</p>
            <p class="text-gray-400 text-sm mt-1">Try a different name, phone number, or reference</p>
        </div>
        <?php endif; ?>
    </div>
    <?php else: ?>
    <!-- Empty state before first search -->
    <div class="glass-card py-16 text-center">
        <svg class="w-20 h-20 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        <p class="text-gray-400 font-semibold">Enter a name, phone number, or booking reference above to search</p>
    </div>
    <?php endif; ?>
</div>
