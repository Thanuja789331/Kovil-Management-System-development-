<!-- Home Page - Landing Page with Registration Options -->
<div class="min-h-[calc(100vh-8rem)] flex items-center justify-center py-12 px-4">
    <div class="w-full max-w-6xl">
        <!-- Hero Section -->
        <div class="text-center mb-12">
            <h1 class="text-5xl md:text-6xl font-bold text-white mb-4 drop-shadow-lg">
                Welcome to Kovil Management System
            </h1>
            <p class="text-xl text-white/90 font-medium drop-shadow">
                Manage temple activities, bookings, and ceremonies seamlessly
            </p>
        </div>

        <!-- Main Action Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
            <!-- Devotee Registration Card -->
            <div class="glass-card p-8 card-hover group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-secondary-400/20 to-secondary-600/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-150 duration-500"></div>
                
                <div class="relative">
                    <!-- Icon -->
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-secondary-500 to-secondary-600 rounded-2xl flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>

                    <!-- Content -->
                    <h2 class="text-2xl font-bold text-gray-800 text-center mb-3">Devotee</h2>
                    <p class="text-gray-600 text-center mb-6">Book poojas, view schedules, and manage your temple visits</p>

                    <!-- Buttons -->
                    <div class="space-y-3">
                        <a href="?url=register#devotee" class="block w-full bg-gradient-to-r from-secondary-500 to-secondary-600 hover:from-secondary-600 hover:to-secondary-700 text-white font-bold py-3 px-6 rounded-xl text-center transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
                            Register Now
                        </a>
                        <a href="?url=login#devotee" class="block w-full bg-white hover:bg-gray-50 text-secondary-600 font-bold py-3 px-6 rounded-xl text-center border-2 border-secondary-500 transition-all transform hover:scale-105">
                            Login
                        </a>
                    </div>
                </div>
            </div>

            <!-- Priest Registration Card -->
            <div class="glass-card p-8 card-hover group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-accent-400/30 to-accent-600/30 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-150 duration-500"></div>
                
                <div class="relative">
                    <!-- Icon -->
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-accent-500 to-accent-600 rounded-2xl flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </div>

                    <!-- Content -->
                    <h2 class="text-2xl font-bold text-gray-800 text-center mb-3">Priest</h2>
                    <p class="text-gray-600 text-center mb-6">Manage assignments, view duties, and perform ceremonies</p>

                    <!-- Buttons -->
                    <div class="space-y-3">
                        <a href="?url=register#priest" class="block w-full bg-gradient-to-r from-accent-500 to-accent-600 hover:from-accent-600 hover:to-accent-700 text-white font-bold py-3 px-6 rounded-xl text-center transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
                            Register Now
                        </a>
                        <a href="?url=login#priest" class="block w-full bg-white hover:bg-gray-50 text-accent-600 font-bold py-3 px-6 rounded-xl text-center border-2 border-accent-500 transition-all transform hover:scale-105">
                            Login
                        </a>
                    </div>
                </div>
            </div>

            <!-- Admin Card -->
            <div class="glass-card p-8 card-hover group relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-primary-400/20 to-primary-600/20 rounded-bl-full -mr-8 -mt-8 transition-transform group-hover:scale-150 duration-500"></div>
                
                <div class="relative">
                    <!-- Icon -->
                    <div class="w-20 h-20 mx-auto mb-6 bg-gradient-to-br from-primary-600 to-primary-700 rounded-2xl flex items-center justify-center shadow-xl group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>

                    <!-- Content -->
                    <h2 class="text-2xl font-bold text-gray-800 text-center mb-3">Management</h2>
                    <p class="text-gray-600 text-center mb-6">Admin panel for managing temple operations and staff</p>

                    <!-- Buttons -->
                    <div class="space-y-3">
                        <a href="?url=register#management" class="block w-full bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white font-bold py-3 px-6 rounded-xl text-center transition-all transform hover:scale-105 shadow-lg hover:shadow-xl">
                            Register Now
                        </a>
                        <a href="?url=login#management" class="block w-full bg-white hover:bg-gray-50 text-primary-600 font-bold py-3 px-6 rounded-xl text-center border-2 border-primary-600 transition-all transform hover:scale-105">
                            Login
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Links -->
        <div class="glass-card p-8">
            <h3 class="text-2xl font-bold text-gray-800 text-center mb-6">Quick Access</h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="?url=schedule" class="flex flex-col items-center p-4 bg-white/80 hover:bg-white backdrop-blur-sm rounded-xl shadow-md transition-all hover:scale-105 group">
                    <svg class="w-8 h-8 text-primary-600 mb-2 group-hover:text-primary-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span class="font-semibold text-gray-700 group-hover:text-gray-800">Pooja Schedule</span>
                </a>

                <a href="?url=festival" class="flex flex-col items-center p-4 bg-white/80 hover:bg-white backdrop-blur-sm rounded-xl shadow-md transition-all hover:scale-105 group">
                    <svg class="w-8 h-8 text-secondary-500 mb-2 group-hover:text-secondary-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                    <span class="font-semibold text-gray-700 group-hover:text-gray-800">Festivals</span>
                </a>

                <a href="?url=announcement" class="flex flex-col items-center p-4 bg-white/80 hover:bg-white backdrop-blur-sm rounded-xl shadow-md transition-all hover:scale-105 group">
                    <svg class="w-8 h-8 text-purple-500 mb-2 group-hover:text-purple-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                    </svg>
                    <span class="font-semibold text-gray-700 group-hover:text-gray-800">Announcements</span>
                </a>

                <a href="?url=donation" class="flex flex-col items-center p-4 bg-white/80 hover:bg-white backdrop-blur-sm rounded-xl shadow-md transition-all hover:scale-105 group">
                    <svg class="w-8 h-8 text-green-600 mb-2 group-hover:text-green-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-semibold text-gray-700 group-hover:text-gray-800">Donations</span>
                </a>
            </div>
        </div>

        <!-- Already have an account? -->
        <div class="text-center mt-8">
            <p class="text-white font-medium text-lg">
                Already registered? 
                <a href="?url=login" class="underline font-bold hover:text-yellow-300 transition-colors">Login here</a>
            </p>
        </div>
    </div>
</div>
