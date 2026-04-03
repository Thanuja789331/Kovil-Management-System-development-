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
                <input type="hidden" name="role" value="management">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <svg class="w-8 h-8 mr-2 text-primary-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Management Login
                </h2>
                
                <?php if(!empty($error)): ?>
                <div class="mb-6 p-4 bg-red-500 text-white rounded-xl shadow-lg animate-pulse">
                    <?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>
                
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
                    
                    <div class="text-center mt-4 pt-4 border-t border-gray-200">
                        <p class="text-gray-600 text-sm mb-2">Don't have an account?</p>
                        <a href="?url=register" class="inline-block bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-400 hover:to-secondary-500 text-white font-semibold py-2 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-md">
                            Register Here
                        </a>
                    </div>
                </div>
            </form>

            <!-- Priest Login Form -->
            <form method="POST" id="form-priest" class="login-form hidden">
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
                    
                    <div class="text-center mt-4 pt-4 border-t border-gray-200">
                        <p class="text-gray-600 text-sm mb-2">Don't have an account?</p>
                        <a href="?url=register" class="inline-block bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-400 hover:to-secondary-500 text-white font-semibold py-2 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-md">
                            Register Here
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Demo Credentials -->
        <div class="mt-8 glass-card p-6">
            <h3 class="font-bold text-gray-800 mb-3 text-center">Demo Login Credentials</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
                <div class="bg-primary-50 p-3 rounded-lg border border-primary-200">
                    <p class="font-semibold text-primary-800 mb-1">Management:</p>
                    <p class="text-gray-700">admin@kovil.com</p>
                    <p class="text-gray-700 font-bold">password</p>
                </div>
                <div class="bg-secondary-50 p-3 rounded-lg border border-secondary-200">
                    <p class="font-semibold text-secondary-800 mb-1">Devotee:</p>
                    <p class="text-gray-700">devotee@kovil.com</p>
                    <p class="text-gray-700 font-bold">password</p>
                </div>
                <div class="bg-accent-50 p-3 rounded-lg border border-accent-200">
                    <p class="font-semibold text-accent-800 mb-1">Priest:</p>
                    <p class="text-gray-700">priest@kovil.com</p>
                    <p class="text-gray-700 font-bold">password</p>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-3 text-center">Note: Newly registered Priest/Management accounts require admin approval before login</p>
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

// Initialize with devotee login (most common)
showLogin('devotee');
</script>
