<?php
$isGoogleUser  = !empty($profile['google_id']);
$has2faSupport = in_array($profile['role'], ['devotee', 'priest'], true);
$twoFaEnabled  = !empty($profile['two_factor_enabled']);
$memberSince   = !empty($profile['created_at'])
    ? date('F j, Y', strtotime($profile['created_at']))
    : 'N/A';

// Resolve avatar URL (custom upload > Google photo > null)
$avatarFile = !empty($profile['avatar'])
    ? (defined('UPLOAD_PATH') ? UPLOAD_PATH : __DIR__ . '/../../../public/uploads/') . 'avatars/' . $profile['avatar']
    : null;
$avatarUrl = ($avatarFile && file_exists($avatarFile))
    ? (defined('UPLOAD_URL') ? UPLOAD_URL : APP_URL . '/public/uploads/') . 'avatars/' . htmlspecialchars($profile['avatar']) . '?v=' . filemtime($avatarFile)
    : (!empty($profile['google_avatar']) ? htmlspecialchars($profile['google_avatar']) : null);
?>

<div class="max-w-3xl mx-auto space-y-6">

    <!-- Page Title -->
    <div class="flex items-center space-x-3">
        <div class="w-10 h-10 bg-gradient-to-br from-primary-600 to-primary-800 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Profile Settings</h1>
            <p class="text-sm text-gray-500">Manage your account information and security</p>
        </div>
    </div>

    <!-- Flash message (shown after redirect) -->
    <?php if (!empty($message)): ?>
    <div class="p-4 rounded-xl font-medium <?= $messageType === 'success' ? 'bg-green-500' : 'bg-red-500' ?> text-white shadow-lg">
        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <!-- ── Profile Summary Card ───────────────────────────────────── -->
    <div class="glass-card p-6 flex flex-col sm:flex-row items-center sm:items-start gap-5">
        <!-- Avatar — click to change photo -->
        <form id="avatar-form" method="POST" action="?url=profile-avatar" enctype="multipart/form-data" class="hidden">
            <?= csrfField() ?>
            <input type="file" id="avatar-input" name="avatar" accept="image/jpeg,image/png,image/webp">
        </form>

        <div class="flex flex-col items-center gap-1 flex-shrink-0">
            <button type="button" onclick="document.getElementById('avatar-input').click()"
                    id="avatar-btn"
                    class="relative group/av focus:outline-none"
                    title="Click to change profile photo">
                <?php if ($avatarUrl): ?>
                    <img src="<?= $avatarUrl ?>"
                         alt="Profile photo"
                         class="w-20 h-20 rounded-full object-cover ring-4 ring-white shadow-lg">
                <?php else: ?>
                    <div class="w-20 h-20 bg-gradient-to-br from-accent-400 to-accent-600 rounded-full flex items-center justify-center shadow-lg">
                        <span class="text-white font-bold text-3xl">
                            <?= strtoupper(substr($profile['name'], 0, 1)) ?>
                        </span>
                    </div>
                <?php endif; ?>
                <!-- Camera overlay on hover -->
                <div class="absolute inset-0 rounded-full bg-black/50 opacity-0 group-hover/av:opacity-100 transition-opacity flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </button>
            <span class="text-xs text-gray-400 cursor-pointer hover:text-primary-600 transition-colors"
                  onclick="document.getElementById('avatar-input').click()">Change photo</span>
        </div>

        <!-- Info -->
        <div class="flex-1 text-center sm:text-left">
            <h2 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($profile['name']) ?></h2>
            <p class="text-gray-500 text-sm"><?= htmlspecialchars($profile['email']) ?></p>

            <div class="flex flex-wrap justify-center sm:justify-start gap-2 mt-3">
                <span class="px-3 py-1 bg-primary-100 text-primary-800 text-xs font-semibold rounded-full capitalize">
                    <?= htmlspecialchars($profile['role']) ?>
                </span>
                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full capitalize">
                    <?= htmlspecialchars($profile['approval_status']) ?>
                </span>
                <?php if ($isGoogleUser): ?>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full flex items-center gap-1">
                    <svg class="w-3 h-3" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Google Account
                </span>
                <?php endif; ?>
            </div>

            <p class="text-xs text-gray-400 mt-2">Member since <?= $memberSince ?></p>
        </div>
    </div>

    <!-- ── Edit Personal Information ─────────────────────────────── -->
    <div class="glass-card p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-5 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Personal Information
        </h3>

        <form method="POST" action="?url=profile-update" class="space-y-4">
            <?= csrfField() ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name"
                           value="<?= htmlspecialchars($profile['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl"
                           required maxlength="100">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email"
                           value="<?= htmlspecialchars($profile['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl"
                           required maxlength="100">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" name="phone"
                           value="<?= htmlspecialchars($profile['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                           placeholder="10-digit mobile number"
                           class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl"
                           maxlength="15">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Role</label>
                    <input type="text"
                           value="<?= ucfirst(htmlspecialchars($profile['role'] ?? '')) ?>"
                           class="w-full px-4 py-3 bg-gray-100 border-2 border-gray-200 text-gray-500 rounded-xl cursor-not-allowed"
                           disabled>
                    <p class="text-xs text-gray-400 mt-1">Role can only be changed by an admin.</p>
                </div>
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="bg-gradient-to-r from-primary-700 to-primary-800 hover:from-primary-600 hover:to-primary-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- ── Password ─────────────────────────────────────────────────── -->
    <?php $hasPassword = !empty($profile['has_password']); ?>
    <div class="glass-card p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-1 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <?= $hasPassword ? 'Change Password' : 'Create Password' ?>
        </h3>
        <p class="text-sm text-gray-500 mb-5">
            <?= $hasPassword
                ? 'Update your password. You must enter your current password to confirm.'
                : 'Your account was created with Google. Set a password so you can also log in with your email and password.' ?>
        </p>

        <form method="POST" action="?url=profile-change-password" class="space-y-4">
            <?= csrfField() ?>
            <input type="hidden" name="has_password" value="<?= $hasPassword ? '1' : '0' ?>">

            <?php if ($hasPassword): ?>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Current Password <span class="text-red-500">*</span></label>
                <input type="password" name="current_password"
                       placeholder="Enter your current password"
                       class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl"
                       required>
            </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">New Password <span class="text-red-500">*</span></label>
                    <input type="password" name="new_password"
                           placeholder="Min. 6 characters"
                           class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl"
                           minlength="6" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Confirm Password <span class="text-red-500">*</span></label>
                    <input type="password" name="confirm_password"
                           placeholder="Re-enter new password"
                           class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl"
                           minlength="6" required>
                </div>
            </div>

            <div class="pt-1">
                <button type="submit"
                        class="bg-gradient-to-r from-primary-700 to-primary-800 hover:from-primary-600 hover:to-primary-700 text-white font-bold py-3 px-8 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                    <?= $hasPassword ? 'Update Password' : 'Create Password' ?>
                </button>
            </div>
        </form>
    </div>

    <!-- ── Security / 2FA ─────────────────────────────────────────── -->
    <?php if ($has2faSupport): ?>
    <div class="glass-card p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-1 flex items-center gap-2">
            <svg class="w-5 h-5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
            </svg>
            Two-Factor Authentication
        </h3>
        <p class="text-sm text-gray-500 mb-5">
            When enabled, you will be sent a 6-digit verification code by email each time you log in with your password.
        </p>

        <!-- Status indicator -->
        <div class="flex items-center gap-3 mb-5 p-4 rounded-xl <?= $twoFaEnabled ? 'bg-green-50 border border-green-200' : 'bg-gray-50 border border-gray-200' ?>">
            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 <?= $twoFaEnabled ? 'bg-green-100' : 'bg-gray-200' ?>">
                <?php if ($twoFaEnabled): ?>
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                <?php else: ?>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                <?php endif; ?>
            </div>
            <div>
                <p class="font-semibold text-sm <?= $twoFaEnabled ? 'text-green-800' : 'text-gray-600' ?>">
                    <?= $twoFaEnabled ? 'Enabled' : 'Disabled' ?>
                </p>
                <p class="text-xs <?= $twoFaEnabled ? 'text-green-600' : 'text-gray-400' ?>">
                    <?= $twoFaEnabled
                        ? 'Your account has an extra layer of protection.'
                        : 'Enable 2FA for a more secure login experience.' ?>
                </p>
            </div>

            <!-- Visual toggle switch (read-only indicator) -->
            <div class="ml-auto">
                <div class="relative w-12 h-6 rounded-full transition-colors duration-300 <?= $twoFaEnabled ? 'bg-green-500' : 'bg-gray-300' ?>">
                    <div class="absolute top-1 w-4 h-4 bg-white rounded-full shadow transition-all duration-300 <?= $twoFaEnabled ? 'left-7' : 'left-1' ?>"></div>
                </div>
            </div>
        </div>

        <?php if ($isGoogleUser && !$twoFaEnabled): ?>
        <p class="text-xs text-blue-600 bg-blue-50 border border-blue-100 rounded-lg px-3 py-2 mb-4">
            You signed in with Google. 2FA applies to password-based login only.
        </p>
        <?php endif; ?>

        <!-- Toggle action button -->
        <form method="POST" action="?url=profile-toggle-2fa"
              onsubmit="return confirm('<?= $twoFaEnabled
                  ? 'Disable two-factor authentication? Your account will be less secure.'
                  : 'Enable two-factor authentication? You will receive an email code each time you log in.' ?>')">
            <?= csrfField() ?>
            <input type="hidden" name="action" value="<?= $twoFaEnabled ? 'disable' : 'enable' ?>">
            <button type="submit"
                    class="font-bold py-3 px-8 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98] <?= $twoFaEnabled
                        ? 'bg-gradient-to-r from-red-500 to-red-600 hover:from-red-400 hover:to-red-500 text-white'
                        : 'bg-gradient-to-r from-green-500 to-green-600 hover:from-green-400 hover:to-green-500 text-white' ?>">
                <?= $twoFaEnabled ? 'Disable 2FA' : 'Enable 2FA' ?>
            </button>
        </form>
    </div>
    <?php endif; ?>

</div>

<script>
(function () {
    var input = document.getElementById('avatar-input');
    var btn   = document.getElementById('avatar-btn');
    if (!input) return;

    input.addEventListener('change', function () {
        if (!input.files || !input.files[0]) return;

        var file = input.files[0];

        // Client-side size check (5 MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Image must be under 5 MB. Please choose a smaller file.');
            input.value = '';
            return;
        }

        // Preview immediately before upload
        var reader = new FileReader();
        reader.onload = function (e) {
            var img = btn.querySelector('img');
            if (img) {
                img.src = e.target.result;
            } else {
                // Replace the letter-initial div with a preview img
                var placeholder = btn.querySelector('div:not(.absolute)');
                if (placeholder) {
                    var preview = document.createElement('img');
                    preview.src = e.target.result;
                    preview.alt = 'Preview';
                    preview.className = 'w-20 h-20 rounded-full object-cover ring-4 ring-white shadow-lg';
                    placeholder.replaceWith(preview);
                }
            }
        };
        reader.readAsDataURL(file);

        // Submit the form
        document.getElementById('avatar-form').submit();
    });
})();
</script>
