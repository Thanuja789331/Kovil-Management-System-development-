<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-br from-primary-600 to-primary-800 rounded-2xl flex items-center justify-center shadow-xl mx-auto mb-4">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Complete Your Profile</h1>
            <p class="text-gray-500 text-sm mt-1">A few more details to finish setting up your account</p>
        </div>

        <div class="glass-card p-8">

            <!-- Google account summary -->
            <div class="flex items-center gap-4 p-4 bg-blue-50 border border-blue-100 rounded-xl mb-6">
                <?php if (!empty($ob['google_avatar'])): ?>
                    <img src="<?= htmlspecialchars($ob['google_avatar']) ?>" alt="Google photo"
                         class="w-12 h-12 rounded-full object-cover ring-2 ring-white shadow flex-shrink-0">
                <?php else: ?>
                    <div class="w-12 h-12 bg-gradient-to-br from-accent-400 to-accent-600 rounded-full flex items-center justify-center flex-shrink-0 shadow">
                        <span class="text-white font-bold text-xl"><?= strtoupper(substr($ob['name'], 0, 1)) ?></span>
                    </div>
                <?php endif; ?>
                <div class="min-w-0">
                    <p class="font-semibold text-gray-800 text-sm truncate"><?= htmlspecialchars($ob['name']) ?></p>
                    <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($ob['email']) ?></p>
                    <span class="inline-flex items-center gap-1 text-xs text-blue-700 font-medium mt-0.5">
                        <svg class="w-3 h-3" viewBox="0 0 24 24">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Signed in with Google
                    </span>
                </div>
            </div>

            <!-- Error -->
            <?php if (!empty($error)): ?>
            <div class="mb-5 p-4 bg-red-500 text-white rounded-xl text-sm font-medium">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <form method="POST" action="?url=google-complete-profile" class="space-y-5">
                <?= csrfField() ?>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Full Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name"
                           value="<?= htmlspecialchars($_POST['name'] ?? $ob['name'], ENT_QUOTES, 'UTF-8') ?>"
                           class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl"
                           required maxlength="100">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Email Address
                    </label>
                    <input type="email"
                           value="<?= htmlspecialchars($ob['email'], ENT_QUOTES, 'UTF-8') ?>"
                           class="w-full px-4 py-3 bg-gray-100 border-2 border-gray-200 text-gray-500 rounded-xl cursor-not-allowed"
                           disabled>
                    <p class="text-xs text-gray-400 mt-1">Email comes from your Google account and cannot be changed.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">
                        Phone Number <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" name="phone"
                           value="<?= htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="10-digit mobile number"
                           class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl"
                           required maxlength="15" inputmode="numeric">
                    <p class="text-xs text-gray-400 mt-1">Used for booking confirmations and notifications.</p>
                </div>

                <!-- Approval notice -->
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800 flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                    </svg>
                    <span>After submitting, your account will be reviewed by an admin before you can sign in. You will be notified once approved.</span>
                </div>

                <button type="submit"
                        class="w-full bg-gradient-to-r from-primary-700 to-primary-800 hover:from-primary-600 hover:to-primary-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                    Submit &amp; Request Approval
                </button>
            </form>

            <div class="mt-5 text-center">
                <a href="?url=login" class="text-sm text-gray-500 hover:text-primary-700 underline transition-colors">
                    Back to Login
                </a>
            </div>
        </div>

    </div>
</div>
