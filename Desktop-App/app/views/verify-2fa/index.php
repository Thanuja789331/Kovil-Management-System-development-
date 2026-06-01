<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-md">

        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-20 h-20 bg-gradient-to-r from-secondary-500 to-primary-700 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Two-Step Verification</h1>
            <p class="text-gray-600">
                We sent a 6-digit code to<br>
                <strong class="text-gray-800"><?= htmlspecialchars($maskedEmail ?? '', ENT_QUOTES, 'UTF-8') ?></strong>
            </p>
        </div>

        <?php if (!empty($error)): ?>
        <div class="mb-6 p-4 bg-red-500 text-white rounded-xl shadow-lg text-center font-medium">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($info)): ?>
        <div class="mb-6 p-4 bg-green-500 text-white rounded-xl shadow-lg text-center font-medium">
            <?= htmlspecialchars($info, ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php endif; ?>

        <!-- OTP Form -->
        <div class="glass-card p-8 card-hover">
            <form method="POST" id="otp-form">
                <?= csrfField() ?>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 text-center">
                            Enter Verification Code
                        </label>
                        <input
                            type="text"
                            name="otp_code"
                            id="otp_code"
                            placeholder="000000"
                            class="input-field w-full px-4 py-4 text-center text-3xl tracking-[0.4em] bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl font-mono"
                            maxlength="6"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            required
                            autofocus
                        >
                        <p class="text-xs text-gray-500 mt-2 text-center">Code expires in 10 minutes</p>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-primary-700 to-primary-800 hover:from-primary-600 hover:to-primary-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                        Verify &amp; Sign In
                    </button>
                </div>
            </form>

            <div class="text-center mt-6 pt-4 border-t border-gray-200 space-y-3">
                <p class="text-gray-600 text-sm">
                    Didn't receive the code?
                    <a href="?url=resend-2fa" class="text-primary-700 font-semibold hover:underline ml-1">
                        Resend Code
                    </a>
                </p>
                <p>
                    <a href="?url=login" class="text-sm text-gray-500 hover:text-gray-700 hover:underline">
                        &#8592; Back to Login
                    </a>
                </p>
            </div>
        </div>

        <!-- Security notice -->
        <p class="text-center text-xs text-gray-500 mt-6">
            This extra step keeps your account secure. The code is valid for one login only.
        </p>
    </div>
</div>

<script>
(function () {
    const input = document.getElementById('otp_code');
    if (!input) return;

    // Allow digits only
    input.addEventListener('input', function () {
        this.value = this.value.replace(/\D/g, '').slice(0, 6);
    });

    // Paste handler: strip non-digits
    input.addEventListener('paste', function (e) {
        e.preventDefault();
        const pasted = (e.clipboardData || window.clipboardData).getData('text');
        this.value = pasted.replace(/\D/g, '').slice(0, 6);
    });
}());
</script>
