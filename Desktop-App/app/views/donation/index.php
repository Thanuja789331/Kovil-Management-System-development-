<!-- Donations Page - White Card with Blue Summary -->
<div class="space-y-6">
    <!-- Back Button -->
    <div class="flex items-center space-x-4 mb-4">
        <a href="javascript:history.back()" class="group flex items-center space-x-2 bg-white/80 hover:bg-white backdrop-blur-sm px-4 py-2 rounded-xl shadow-md transition-all duration-200 hover:scale-105 active:scale-95">
            <svg class="w-5 h-5 text-accent-600 group-hover:text-accent-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="font-semibold text-accent-600 group-hover:text-accent-700">Back</span>
        </a>
    </div>

    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-4xl font-extrabold text-white mb-2 drop-shadow-md">🙏 <?= trans('donation') ?? 'Donations' ?></h1>
        <p class="text-emerald-100 font-medium drop-shadow-sm">Contribute to the divine cause and support temple activities</p>
    </div>

    <?php $isManagement = isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'management'; ?>
    <?php
    $donationsResult = null;
    $receipt = null;
    $summary = ['weekly_total' => 0, 'monthly_total' => 0, 'yearly_total' => 0, 'all_time_total' => 0];
    if (is_array($data)) {
        $donationsResult = $data['donations'] ?? null;
        $receipt = $data['receipt'] ?? null;
        $summary = $data['summary'] ?? $summary;
    } else {
        $donationsResult = $data;
    }
    ?>
    <!-- Top Blue Summary Card -->
    <div class="glass-card bg-gradient-to-r from-accent-500 to-accent-600 p-6 card-hover shadow-xl border border-white/20">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/90 text-sm font-semibold uppercase tracking-wide">
                    <?= !empty($_GET['start_date']) || !empty($_GET['end_date']) ? 'Filtered Donations Total' : 'Total Donations All-Time' ?>
                </p>
                <p class="text-4xl font-bold text-white mt-2">$<?= number_format($donations ?? 0, 2) ?></p>
            </div>
            <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <?php if ($isManagement): ?>
    <!-- Date Filtering Form for Admins -->
    <div class="glass-card p-6 bg-white/90 shadow-xl border border-white/20 rounded-2xl">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center space-x-2">
            <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
            </svg>
            <span>Filter Report by Date Range</span>
        </h3>
        <form method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <input type="hidden" name="url" value="donation">
            <div>
                <label for="start_date" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">Start Date</label>
                <input 
                    type="date" 
                    name="start_date" 
                    id="start_date" 
                    class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 focus:border-accent-500 focus:outline-none transition-all duration-200" 
                    value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>"
                >
            </div>
            <div>
                <label for="end_date" class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-2">End Date</label>
                <input 
                    type="date" 
                    name="end_date" 
                    id="end_date" 
                    class="w-full px-4 py-2.5 bg-white border border-gray-300 rounded-xl text-gray-800 focus:border-accent-500 focus:outline-none transition-all duration-200" 
                    value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>"
                >
            </div>
            <div class="flex space-x-2">
                <button type="submit" class="flex-1 bg-accent-600 hover:bg-accent-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-md transition-all duration-200">
                    Apply Filter
                </button>
                <?php if (!empty($_GET['start_date']) || !empty($_GET['end_date'])): ?>
                    <a href="?url=donation" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold py-2.5 px-4 rounded-xl transition-all duration-200 text-center flex items-center justify-center border border-gray-200">
                        Clear
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Management Dashboard stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="glass-card p-5 bg-white shadow-md border border-gray-100 rounded-2xl">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Weekly</p>
            <p class="text-2xl font-black text-gray-850">$<?= number_format($summary['weekly_total'] ?? 0, 2) ?></p>
        </div>
        <div class="glass-card p-5 bg-white shadow-md border border-gray-100 rounded-2xl">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Monthly</p>
            <p class="text-2xl font-black text-gray-850">$<?= number_format($summary['monthly_total'] ?? 0, 2) ?></p>
        </div>
        <div class="glass-card p-5 bg-white shadow-md border border-gray-100 rounded-2xl">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">Yearly</p>
            <p class="text-2xl font-black text-gray-850">$<?= number_format($summary['yearly_total'] ?? 0, 2) ?></p>
        </div>
        <div class="glass-card p-5 bg-white shadow-md border border-gray-100 rounded-2xl">
            <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mb-1">All Time</p>
            <p class="text-2xl font-black text-gray-850">$<?= number_format($summary['all_time_total'] ?? 0, 2) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Donation Form -->
    <div class="glass-card p-6 bg-white/95 shadow-xl border border-white/20 rounded-2xl">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Make a Devotional Contribution</h2>
        <?php if(!empty($message)): ?>
        <div class="mb-4 p-4 <?= $messageType === 'success' ? 'bg-green-500' : 'bg-red-500' ?> text-white rounded-xl shadow-lg font-semibold text-sm">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <?= csrfField() ?>
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Your Name</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name"
                    placeholder="Enter your name"
                    minlength="2"
                    maxlength="100"
                    class="input-field w-full px-4 py-3 bg-white border border-gray-300 text-gray-800 focus:border-accent-500 focus:bg-white transition-all duration-200 rounded-xl"
                    value="<?= htmlspecialchars($_SESSION['user']['name'] ?? '') ?>"
                    required
                >
                <p class="text-xs text-gray-500 mt-1">Letters and spaces only, minimum 2 letters (Tamil/English supported)</p>
            </div>

            <div>
                <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">Amount ($)</label>
                <input 
                    type="number" 
                    name="amount" 
                    id="amount"
                    placeholder="Enter amount"
                    min="1"
                    max="999999.99"
                    step="0.01"
                    class="input-field w-full px-4 py-3 bg-white border border-gray-300 text-gray-800 focus:border-accent-500 focus:bg-white transition-all duration-200 rounded-xl"
                    required
                >
            </div>

            <div class="md:col-span-2">
                <label for="purpose" class="block text-sm font-semibold text-gray-700 mb-2">Purpose</label>
                <input 
                    type="text" 
                    name="purpose" 
                    id="purpose"
                    placeholder="E.g., Temple maintenance, Festival sponsorship"
                    minlength="3"
                    maxlength="200"
                    class="input-field w-full px-4 py-3 bg-white border border-gray-300 text-gray-800 focus:border-accent-500 focus:bg-white transition-all duration-200 rounded-xl"
                >
                <p class="text-xs text-gray-500 mt-1">Optional. Leave blank for a general donation.</p>
            </div>

            <div class="md:col-span-2">
                <label for="payment_method" class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
                <select name="payment_method" id="payment_method" class="input-field w-full px-4 py-3 bg-white border border-gray-300 text-gray-800 rounded-xl focus:border-accent-500" required>
                    <option value="card">Card Payment</option>
                    <option value="online_transfer">Online Transfer</option>
                </select>
                <p class="text-xs text-gray-500 mt-2">Bank transfer details: Kovil Temple Trust, A/C: 1234567890, Bank: Sampath Bank, Branch: Colombo Main, IFSC/SWIFT: BSAMLKLX.</p>
            </div>

            <div class="md:col-span-2">
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-500 hover:to-green-600 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-[1.01] active:scale-[0.99]"
                >
                    Donate Now
                </button>
            </div>
        </form>
    </div>

    <?php if (!empty($receipt)): ?>
    <div class="glass-card p-6 border-l-4 border-green-500 bg-white/95 rounded-2xl shadow-xl">
        <h2 class="text-xl font-bold text-gray-800 mb-3 flex items-center space-x-2">
            <span class="text-green-500">✔</span>
            <span>Donation Receipt Confirmed</span>
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-700">
            <p><strong>Reference:</strong> <?= htmlspecialchars($receipt['reference']) ?></p>
            <p><strong>Name:</strong> <?= htmlspecialchars($receipt['name']) ?></p>
            <p><strong>Amount:</strong> $<?= number_format((float)$receipt['amount'], 2) ?></p>
            <p><strong>Method:</strong> <?= htmlspecialchars($receipt['payment_method'] === 'online_transfer' ? 'Online Transfer' : 'Card') ?></p>
            <p><strong>Purpose:</strong> <?= htmlspecialchars($receipt['purpose'] ?: 'General') ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($receipt['created_at']) ?></p>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Donations Table -->
    <div class="glass-card p-6 bg-white shadow-xl border border-white/20 rounded-2xl">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-800"><?= $isManagement ? 'Recent Completed Donations' : 'Donation Summary' ?></h2>
                <p class="text-xs text-gray-500 mt-1">Audit log of verified temple funding</p>
            </div>
            <?php if($isManagement): ?>
            <?php 
            $startQuery = !empty($_GET['start_date']) ? '&start_date=' . urlencode($_GET['start_date']) : '';
            $endQuery = !empty($_GET['end_date']) ? '&end_date=' . urlencode($_GET['end_date']) : '';
            $exportParams = $startQuery . $endQuery;
            ?>
            <div class="flex flex-wrap gap-2">
                <a href="?url=export-donations-csv<?= $exportParams ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-4 rounded-xl shadow-md transition-all duration-200 flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Export CSV</span>
                </a>
                <a href="?url=export-donations-pdf<?= $exportParams ?>" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-xl shadow-md transition-all duration-200 flex items-center space-x-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span>Export PDF</span>
                </a>
            </div>
            <?php endif; ?>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[520px]">
                <thead>
                    <tr class="border-b-2 border-gray-200">
                        <?php if($isManagement): ?><th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 uppercase">Name</th><?php endif; ?>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 uppercase">Amount</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 uppercase">Date</th>
                        <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700 uppercase">Purpose</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Convert mysqli_result to array
                    $donations = [];
                    if ($donationsResult instanceof mysqli_result) {
                        $donations = $donationsResult->fetch_all(MYSQLI_ASSOC);
                    } elseif (is_array($donationsResult)) {
                        $donations = $donationsResult;
                    }
                    ?>
                    <?php if(!empty($donations)): ?>
                        <?php foreach($donations as $donation): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <?php if($isManagement): ?><td class="py-3 px-4 text-gray-800 font-semibold"><?= htmlspecialchars($donation['donor_name']) ?></td><?php endif; ?>
                            <td class="py-3 px-4 text-green-600 font-bold">$<?= number_format($donation['amount'], 2) ?></td>
                            <td class="py-3 px-4 text-gray-650 font-medium">
                                <?php if(isset($donation['created_at']) && !empty($donation['created_at'])): ?>
                                    <?= date('M d, Y g:i A', strtotime($donation['created_at'])) ?>
                                <?php else: ?>
                                    <span class="text-gray-400">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-gray-600 font-medium"><?= htmlspecialchars($donation['purpose'] ?? 'General') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $isManagement ? '4' : '3' ?>" class="py-8 text-center text-gray-500">No donations found in this date range.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
