<!-- Separate Login Page with Role Selection -->
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-5xl">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Kovil Management System</h1>
            <p class="text-gray-600 font-medium">Select your role and sign in to your account</p>
        </div>

        <!-- Role Tabs -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
            <button onclick="showLogin('devotee')" id="tab-devotee" class="role-tab bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-400 hover:to-secondary-500 text-white p-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow-lg active-tab">
                <div class="flex items-center justify-center space-x-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Devotee</span>
                </div>
            </button>
            
            <button onclick="showLogin('priest')" id="tab-priest" class="role-tab bg-white/50 hover:bg-white/80 text-gray-700 p-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow">
                <div class="flex items-center justify-center space-x-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span>Priest</span>
                </div>
            </button>
            
            <button onclick="showLogin('management')" id="tab-management" class="role-tab bg-white/50 hover:bg-white/80 text-gray-700 p-4 rounded-xl font-semibold transition-all duration-300 transform hover:scale-105 shadow">
                <div class="flex items-center justify-center space-x-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Management/Admin</span>
                </div>
            </button>
        </div>

        <?php if (!empty($error)): ?>
        <div class="mb-6 p-4 bg-red-500 text-white rounded-xl shadow-lg text-center">
            <?= $error ?>
        </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['registration_success']) && $_SESSION['registration_success']): ?>
        <div class="mb-6 p-4 bg-green-500 text-white rounded-xl shadow-lg animate-pulse text-center">
            <div class="flex items-center justify-center space-x-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span class="font-bold"><?php echo htmlspecialchars($_SESSION['registration_message'] ?? 'Registration successful!'); ?></span>
            </div>
            <p class="text-sm mt-2">Please login with your credentials</p>
        </div>
        <?php 
        unset($_SESSION['registration_success']);
        unset($_SESSION['registration_message']);
        endif; 
        ?>

        <!-- Login Forms Container -->
        <div class="glass-card p-8 card-hover">
            <!-- Management Login Form -->
            <form method="POST" id="form-management" class="login-form">
                <?= csrfField() ?>
                <input type="hidden" name="role" value="management">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-8 h-8 mr-2 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Management Login
                </h2>
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email" placeholder="admin@kovil.com" class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" placeholder="Enter your password" class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-primary-500 focus:bg-white transition-all duration-200 rounded-xl" required>
                    </div>
                    
                    <button type="submit" name="login" class="w-full bg-gradient-to-r from-primary-700 to-primary-800 hover:from-primary-600 hover:to-primary-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                        Sign In as Management
                    </button>
                    
                    <div class="text-center mt-4 pt-4 border-t border-gray-200">
                        <p class="text-gray-600 text-sm mb-2">Don't have an account?</p>
                        <a href="?url=register" class="inline-block bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-400 hover:to-secondary-500 text-white font-semibold py-2 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-md">
                            Register Here
                        </a>
                    </div>
                </div>
            </form>

            <!-- Devotee Login Form -->
            <form method="POST" id="form-devotee" class="login-form hidden">
                <?= csrfField() ?>
                <input type="hidden" name="role" value="devotee">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-8 h-8 mr-2 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    Devotee Login
                </h2>
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email" placeholder="devotee@kovil.com" class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-secondary-500 focus:bg-white transition-all duration-200 rounded-xl" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" placeholder="Enter your password" class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-secondary-500 focus:bg-white transition-all duration-200 rounded-xl" required>
                    </div>
                    
                    <button type="submit" name="login" class="w-full bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-400 hover:to-secondary-500 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                        Sign In as Devotee
                    </button>

                    <div class="relative flex items-center my-2">
                        <div class="flex-grow border-t border-gray-300"></div>
                        <span class="mx-3 text-gray-400 text-sm font-medium">or</span>
                        <div class="flex-grow border-t border-gray-300"></div>
                    </div>

                    <a href="?url=oauth-redirect" class="w-full flex items-center justify-center gap-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3 px-6 rounded-xl border-2 border-gray-200 shadow-sm transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Sign in with Google
                    </a>

                    <div class="text-center mt-2 pt-4 border-t border-gray-200">
                        <p class="text-gray-600 text-sm mb-2">Don't have an account?</p>
                        <a href="?url=register" class="inline-block bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-400 hover:to-secondary-500 text-white font-semibold py-2 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-md">
                            Register Here
                        </a>
                    </div>
                </div>
            </form>

            <!-- Priest Login Form -->
            <form method="POST" id="form-priest" class="login-form hidden">
                <?= csrfField() ?>
                <input type="hidden" name="role" value="priest">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-8 h-8 mr-2 text-accent-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Priest Login
                </h2>
                
                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email" placeholder="priest@kovil.com" class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-accent-500 focus:bg-white transition-all duration-200 rounded-xl" required>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <input type="password" name="password" placeholder="Enter your password" class="input-field w-full px-4 py-3 bg-white/80 border-2 border-gray-200 text-gray-800 focus:border-accent-500 focus:bg-white transition-all duration-200 rounded-xl" required>
                    </div>
                    
                    <button type="submit" name="login" class="w-full bg-gradient-to-r from-accent-500 to-accent-600 hover:from-accent-400 hover:to-accent-500 text-white font-bold py-4 px-6 rounded-xl shadow-lg transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                        Sign In as Priest
                    </button>

                    <div class="relative flex items-center my-2">
                        <div class="flex-grow border-t border-gray-300"></div>
                        <span class="mx-3 text-gray-400 text-sm font-medium">or</span>
                        <div class="flex-grow border-t border-gray-300"></div>
                    </div>

                    <a href="?url=oauth-redirect" class="w-full flex items-center justify-center gap-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold py-3 px-6 rounded-xl border-2 border-gray-200 shadow-sm transition-all duration-300 transform hover:scale-[1.02] active:scale-[0.98]">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Sign in with Google
                    </a>

                    <div class="text-center mt-2 pt-4 border-t border-gray-200">
                        <p class="text-gray-600 text-sm mb-2">Don't have an account?</p>
                        <a href="?url=register" class="inline-block bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-400 hover:to-secondary-500 text-white font-semibold py-2 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-md">
                            Register Here
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <div class="text-center mt-4">
            <a href="?url=forgot-password" class="text-sm font-semibold text-primary-700 hover:text-primary-800 underline">
                Forgot password?
            </a>
        </div>

    </div>
</div>

<script>
function showLogin(role) {
    // Hide all forms
    document.querySelectorAll('.login-form').forEach(form => {
        form.classList.add('hidden');
    });
    
    // Remove active class from all tabs
    document.querySelectorAll('.role-tab').forEach(tab => {
        tab.classList.remove('active-tab', 'bg-gradient-to-r', 'from-secondary-500', 'to-secondary-600', 'from-accent-500', 'to-accent-600', 'from-primary-700', 'to-primary-800', 'text-white');
        tab.classList.add('bg-white/50', 'text-gray-700');
    });
    
    // Show selected form
    document.getElementById('form-' + role).classList.remove('hidden');
    
    // Add active class and appropriate colors to selected tab
    const activeTab = document.getElementById('tab-' + role);
    activeTab.classList.add('active-tab');
    activeTab.classList.remove('bg-white/50', 'text-gray-700');
    
    if (role === 'devotee') {
        activeTab.classList.add('bg-gradient-to-r', 'from-secondary-500', 'to-secondary-600', 'text-white');
    } else if (role === 'priest') {
        activeTab.classList.add('bg-gradient-to-r', 'from-accent-500', 'to-accent-600', 'text-white');
    } else if (role === 'management') {
        activeTab.classList.add('bg-gradient-to-r', 'from-primary-700', 'to-primary-800', 'text-white');
    }
}

function initializePasswordToggles() {
    document.querySelectorAll('.login-form input[type="password"]').forEach((input, idx) => {
        const wrapper = document.createElement('div');
        wrapper.className = 'relative';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);

        input.classList.add('pr-16');
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'absolute right-3 top-1/2 -translate-y-1/2 text-xs text-gray-600 hover:text-gray-900 font-semibold';
        btn.textContent = 'Show';
        btn.setAttribute('aria-label', 'Toggle password visibility ' + idx);
        btn.addEventListener('click', () => {
            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            btn.textContent = isHidden ? 'Hide' : 'Show';
        });
        wrapper.appendChild(btn);
    });
}

// Initialize with devotee login (most common)
showLogin('devotee');
initializePasswordToggles();
</script>

<style>
/* Error message styling improvements */
.bg-red-500 small {
    opacity: 0.9;
    font-size: 0.85rem;
    display: block;
    margin-top: 0.5rem;
}

.bg-red-500 div {
    line-height: 1.5;
}

.bg-red-500 div + div {
    margin-top: 0.5rem;
}
</style>
