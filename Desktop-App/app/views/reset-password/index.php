<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-lg">
        <div class="glass-card p-8 card-hover">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Reset Password</h1>
            <p class="text-gray-600 mb-6">Set a new password for <?= htmlspecialchars($data['user_email'] ?? 'your account') ?>.</p>

            <form method="POST" class="space-y-5">
                <input type="hidden" name="token" value="<?= htmlspecialchars($data['token'] ?? '') ?>">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">New Password</label>
                    <div class="relative">
                        <input type="password" id="reset-password" name="password" minlength="6" class="input-field w-full px-4 py-3 pr-16 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl" required>
                        <button type="button" onclick="toggleResetPassword('reset-password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-600 hover:text-gray-900 font-semibold">Show</button>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Confirm Password</label>
                    <div class="relative">
                        <input type="password" id="reset-confirm-password" name="confirm_password" minlength="6" class="input-field w-full px-4 py-3 pr-16 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl" required>
                        <button type="button" onclick="toggleResetPassword('reset-confirm-password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-600 hover:text-gray-900 font-semibold">Show</button>
                    </div>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-primary-700 to-primary-800 hover:from-primary-600 hover:to-primary-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all duration-300">
                    Update Password
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function toggleResetPassword(id, btn) {
    const input = document.getElementById(id);
    if (!input) return;
    const hidden = input.type === 'password';
    input.type = hidden ? 'text' : 'password';
    btn.textContent = hidden ? 'Hide' : 'Show';
}
</script>
