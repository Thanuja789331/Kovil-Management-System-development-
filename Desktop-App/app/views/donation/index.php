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
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Donations</h1>
        <p class="text-gray-600">Contribute to the divine cause</p>
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
    <div class="glass-card bg-gradient-to-r from-accent-500 to-accent-600 p-6 card-hover">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-white/90 text-sm font-semibold uppercase tracking-wide">Total Donations</p>
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
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="glass-card p-4"><p class="text-sm text-gray-600">Weekly</p><p class="text-xl font-bold text-gray-800">$<?= number_format($summary['weekly_total'] ?? 0, 2) ?></p></div>
        <div class="glass-card p-4"><p class="text-sm text-gray-600">Monthly</p><p class="text-xl font-bold text-gray-800">$<?= number_format($summary['monthly_total'] ?? 0, 2) ?></p></div>
        <div class="glass-card p-4"><p class="text-sm text-gray-600">Yearly</p><p class="text-xl font-bold text-gray-800">$<?= number_format($summary['yearly_total'] ?? 0, 2) ?></p></div>
        <div class="glass-card p-4"><p class="text-sm text-gray-600">All Time</p><p class="text-xl font-bold text-gray-800">$<?= number_format($summary['all_time_total'] ?? 0, 2) ?></p></div>
    </div>
    <?php endif; ?>

    <!-- Donation Form -->
    <div class="glass-card p-6 card-hover">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Make a Donation</h2>
        <?php if(!empty($message)): ?>
        <div class="mb-4 p-4 <?= $messageType === 'success' ? 'bg-green-500' : 'bg-red-500' ?> text-white rounded-xl shadow-lg">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>

        <form method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Your Name</label>
                <input 
                    type="text" 
                    name="name" 
                    id="name"
                    placeholder="Enter your name"
                    pattern="[A-Za-z ]{2,}"
                    class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl"
                    value="<?= htmlspecialchars($_SESSION['user']['name'] ?? '') ?>"
                    required
                >
                <p class="text-xs text-gray-500 mt-1">Letters and spaces only, minimum 2 letters</p>
            </div>

            <div>
                <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">Amount ($)</label>
                <input 
                    type="number" 
                    name="amount" 
                    id="amount"
                    placeholder="Enter amount"
                    min="1"
                    step="0.01"
                    class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl"
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
                    class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl"
                >
            </div>

            <div class="md:col-span-2">
                <label for="payment_method" class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
                <select name="payment_method" id="payment_method" class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 rounded-xl" required>
                    <option value="card">Card Payment</option>
                    <option value="online_transfer">Online Transfer</option>
                </select>
                <p class="text-xs text-gray-500 mt-2">Bank transfer details: Kovil Temple Trust, A/C: 1234567890, Bank: Sampath Bank, Branch: Colombo Main, IFSC/SWIFT: BSAMLKLX.</p>
            </div>

            <div class="md:col-span-2">
                <button 
                    type="submit" 
                    class="w-full bg-gradient-to-r from-green-600 to-green-700 hover:from-green-500 hover:to-green-600 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]"
                >
                    Donate Now
                </button>
            </div>
        </form>
    </div>

    <?php if (!empty($receipt)): ?>
    <div class="glass-card p-6 border-l-4 border-green-500">
        <h2 class="text-xl font-bold text-gray-800 mb-3">Donation Receipt</h2>
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
    <div class="glass-card p-6 card-hover">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-gray-800"><?= $isManagement ? 'Recent Donations' : 'Donation Summary' ?></h2>
            <?php if($isManagement): ?>
            <a href="?url=export-donations-pdf" class="bg-gradient-to-r from-primary-700 to-primary-800 hover:from-primary-600 hover:to-primary-700 text-white font-semibold py-2 px-4 rounded-xl shadow-md transition-all duration-200">
                Download Donations Report (PDF)
            </a>
            <?php endif; ?>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
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
                    } elseif (is_array($data)) {
                        $donations = $data['donations'] ?? [];
                    }
                    ?>
                    <?php if(!empty($donations)): ?>
                        <?php foreach($donations as $donation): ?>
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                            <?php if($isManagement): ?><td class="py-3 px-4 text-gray-800"><?= htmlspecialchars($donation['donor_name']) ?></td><?php endif; ?>
                            <td class="py-3 px-4 text-green-600 font-semibold">$<?= number_format($donation['amount'], 2) ?></td>
                            <td class="py-3 px-4 text-gray-600">
                                <?php if(isset($donation['created_at']) && !empty($donation['created_at'])): ?>
                                    <?= date('M d, Y', strtotime($donation['created_at'])) ?>
                                <?php else: ?>
                                    <span class="text-gray-400">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3 px-4 text-gray-600"><?= htmlspecialchars($donation['purpose'] ?? 'General') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $isManagement ? '4' : '3' ?>" class="py-8 text-center text-gray-500">No donations yet</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
