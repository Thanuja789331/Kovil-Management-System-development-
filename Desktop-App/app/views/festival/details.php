<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center space-x-4">
        <a href="?url=festival" class="group flex items-center space-x-2 bg-white/80 hover:bg-white backdrop-blur-sm px-4 py-2 rounded-xl shadow-md transition-all duration-200 hover:scale-105 active:scale-95">
            <svg class="w-5 h-5 text-primary-700 group-hover:text-primary-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="font-semibold text-primary-700 group-hover:text-primary-800">Back to Festivals</span>
        </a>
    </div>

    <div class="glass-card p-8 border-l-4 border-primary-600">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2"><?= htmlspecialchars($data['name'] ?? 'Festival Details') ?></h1>
                <p class="text-gray-600"><?= !empty($data['date']) ? date('F d, Y', strtotime($data['date'])) : 'Date unavailable' ?></p>
            </div>
            <span class="px-3 py-1 bg-gradient-to-r from-primary-500 to-primary-600 text-white text-xs font-semibold rounded-full uppercase tracking-wide">
                <?= htmlspecialchars($data['category'] ?? 'Event') ?>
            </span>
        </div>

        <div class="bg-white/60 rounded-xl p-5 border border-gray-200 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-2">About This Event</h2>
            <?php if (!empty($data['description'])): ?>
                <p class="text-gray-700 leading-relaxed"><?= nl2br(htmlspecialchars($data['description'])) ?></p>
            <?php else: ?>
                <p class="text-gray-500">Detailed description is not available for this event.</p>
            <?php endif; ?>
        </div>

        <div class="flex justify-end gap-3">
            <?php if(isset($_SESSION['user']) && $_SESSION['user']['role'] === 'management' && !empty($data['editable']) && !empty($data['id'])): ?>
                <a href="?url=festival&action=edit&id=<?= urlencode((string) $data['id']) ?>" class="bg-gradient-to-r from-yellow-500 to-yellow-600 hover:from-yellow-400 hover:to-yellow-500 text-white px-4 py-2.5 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg inline-block">
                    Edit Festival
                </a>
            <?php endif; ?>
            <a href="?url=festival" class="px-4 py-2.5 rounded-xl font-semibold text-gray-700 border-2 border-gray-300 hover:bg-gray-50">
                Close
            </a>
        </div>
    </div>
</div>
