<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-lg">
        <div class="glass-card p-8 card-hover">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Forgot Password</h1>
            <p class="text-gray-600 mb-6">Enter your account email to receive a reset link.</p>

            <form method="POST" class="space-y-5">
                <?= csrfField() ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                    <input type="email" name="email" class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl" required>
                </div>
                <button type="submit" class="w-full bg-gradient-to-r from-primary-700 to-primary-800 hover:from-primary-600 hover:to-primary-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all duration-300">
                    Send Reset Link
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="?url=login" class="text-sm font-semibold text-primary-700 hover:text-primary-800 underline">Back to login</a>
            </div>
        </div>
    </div>
</div>
