<?php
$festival = $data['festival'] ?? [];
$yearFestivals = $data['yearFestivals'] ?? [];
$yearSpecialDays = $data['yearSpecialDays'] ?? [];
$yearPoojas = $data['yearPoojas'] ?? [];
?>
<div class="space-y-6">
    <div class="flex items-center space-x-4 mb-4">
        <a href="?url=festival" class="group flex items-center space-x-2 bg-white/80 hover:bg-white backdrop-blur-sm px-4 py-2 rounded-xl shadow-md transition-all duration-200">
            <span class="font-semibold text-primary-700">Back</span>
        </a>
    </div>

    <div class="glass-card p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Edit Festival</h1>
        <p class="text-gray-600 mb-6">Update festival details and review this year context.</p>
        <form action="?url=festival&action=update" method="POST" class="space-y-5">
            <input type="hidden" name="id" value="<?= htmlspecialchars($festival['id'] ?? '') ?>">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Festival Name *</label>
                <input type="text" name="name" value="<?= htmlspecialchars($festival['name'] ?? '') ?>" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date *</label>
                <input type="date" name="date" value="<?= htmlspecialchars($festival['date'] ?? '') ?>" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 outline-none"><?= htmlspecialchars($festival['description'] ?? '') ?></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white font-bold py-4 px-6 rounded-xl shadow-lg">
                    Update Festival
                </button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="glass-card p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Year Festivals</h3>
            <ul class="space-y-2 text-sm text-gray-700">
                <?php foreach ($yearFestivals as $item): ?>
                    <li><?= htmlspecialchars($item['name']) ?> - <?= date('M d', strtotime($item['date'])) ?></li>
                <?php endforeach; ?>
                <?php if (empty($yearFestivals)): ?><li class="text-gray-500">No festivals in this year.</li><?php endif; ?>
            </ul>
        </div>

        <div class="glass-card p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Special Days</h3>
            <ul class="space-y-2 text-sm text-gray-700">
                <?php foreach ($yearSpecialDays as $item): ?>
                    <li><?= htmlspecialchars($item['title']) ?> - <?= date('M d', strtotime($item['day_date'])) ?></li>
                <?php endforeach; ?>
                <?php if (empty($yearSpecialDays)): ?><li class="text-gray-500">No special days in this year.</li><?php endif; ?>
            </ul>
        </div>

        <div class="glass-card p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-3">Pooja Schedule</h3>
            <ul class="space-y-2 text-sm text-gray-700 max-h-64 overflow-auto">
                <?php foreach ($yearPoojas as $item): ?>
                    <li><?= htmlspecialchars($item['pooja_name']) ?> - <?= date('M d', strtotime($item['pooja_date'])) ?></li>
                <?php endforeach; ?>
                <?php if (empty($yearPoojas)): ?><li class="text-gray-500">No poojas in this year.</li><?php endif; ?>
            </ul>
        </div>
    </div>

    <div class="glass-card p-6 border-l-4 border-red-500">
        <form action="?url=festival&action=delete" method="POST" onsubmit="return confirm('Delete this festival?');">
            <input type="hidden" name="id" value="<?= htmlspecialchars($festival['id'] ?? '') ?>">
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl transition-all">Delete Festival</button>
        </form>
    </div>
</div>
