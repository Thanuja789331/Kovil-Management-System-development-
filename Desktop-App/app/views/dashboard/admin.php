<!-- Admin Dashboard - Exact Design Match -->
<div class="space-y-8">
    <!-- Back Button & Header -->
    <div class="flex items-center space-x-4 mb-4">
        <a href="javascript:history.back()" class="group flex items-center space-x-2 bg-white/80 hover:bg-white backdrop-blur-sm px-4 py-2 rounded-xl shadow-md transition-all duration-200 hover:scale-105 active:scale-95">
            <svg class="w-5 h-5 text-primary-700 group-hover:text-primary-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            <span class="font-semibold text-primary-700 group-hover:text-primary-800">Back</span>
        </a>
    </div>

    <!-- Welcome Section -->
    <div class="glass-card p-6 card-hover">
        <h1 class="text-3xl font-bold text-gray-800 mb-2"><?= trans('dashboard') ?></h1>
        <p class="text-gray-600"><?= trans('welcome') ?>, <?= htmlspecialchars($_SESSION['user']['name']) ?>!</p>
    </div>

    <!-- Main Grid Layout - 3 Columns -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Book Pooja Card (Orange) -->
        <a href="?url=book" class="group glass-card-light p-6 card-hover border-l-4 border-secondary-500">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-gradient-to-br from-secondary-400 to-secondary-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="text-3xl opacity-20 group-hover:opacity-40 transition-opacity">📅</span>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-1"><?= trans('book_pooja') ?></h3>
            <p class="text-sm text-gray-600"><?= trans('schedule_pooja') ?></p>
        </a>

        <!-- View Schedule Card (Yellow) -->
        <a href="?url=schedule" class="group glass-card-light p-6 card-hover border-l-4 border-yellow-500">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <span class="text-3xl opacity-20 group-hover:opacity-40 transition-opacity">📋</span>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-1"><?= trans('view_schedule') ?></h3>
            <p class="text-sm text-gray-600"><?= trans('all_upcoming_events') ?></p>
        </a>

        <!-- Festivals & Events Card (Blue) -->
        <a href="?url=festival" class="group glass-card-light p-6 card-hover border-l-4 border-accent-500">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-gradient-to-br from-accent-400 to-accent-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                    </svg>
                </div>
                <span class="text-3xl opacity-20 group-hover:opacity-40 transition-opacity">🎉</span>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-1"><?= trans('festivals_events') ?></h3>
            <p class="text-sm text-gray-600"><?= trans('celebrate_together') ?></p>
        </a>

        <!-- Donations Card (Green, spans 2 columns) -->
        <a href="?url=donation" class="group glass-card-light p-6 card-hover border-l-4 border-green-600 md:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-gradient-to-br from-green-600 to-green-700 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-3xl opacity-20 group-hover:opacity-40 transition-opacity">💰</span>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-1"><?= trans('donations') ?></h3>
            <p class="text-sm text-gray-600"><?= trans('contribute_temple') ?></p>
        </a>

        <!-- Create Announcement Card (Primary/Accent) -->
        <a href="?url=announcement&action=create" class="group glass-card-light p-6 card-hover border-l-4 border-primary-600">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-gradient-to-br from-primary-500 to-primary-700 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <span class="text-3xl opacity-20 group-hover:opacity-40 transition-opacity">✏️</span>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-1"><?= trans('create_announcement') ?></h3>
            <p class="text-sm text-gray-600"><?= trans('send_updates_users') ?></p>
        </a>

        <!-- Add Pooja Card (Teal) -->
        <a href="?url=schedule&action=add" class="group glass-card-light p-6 card-hover border-l-4 border-teal-600">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-gradient-to-br from-teal-500 to-teal-700 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                <span class="text-3xl opacity-20 group-hover:opacity-40 transition-opacity">➕</span>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-1"><?= trans('add_pooja') ?></h3>
            <p class="text-sm text-gray-600"><?= trans('schedule_new_pooja') ?></p>
        </a>

        <!-- Assign Priest Duties Card (Indigo/Purple) -->
        <a href="?url=assign" class="group glass-card-light p-6 card-hover border-l-4 border-indigo-600">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <span class="text-3xl opacity-20 group-hover:opacity-40 transition-opacity">🙏</span>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-1">Assign Priest Duties</h3>
            <p class="text-sm text-gray-600">Assign poojas to priests & send SMS</p>
        </a>

        <!-- Reports Card (Dark Green) -->
        <a href="?url=report" class="group glass-card-light p-6 card-hover border-l-4 border-primary-800">
            <div class="flex items-center justify-between mb-4">
                <div class="w-14 h-14 bg-gradient-to-br from-primary-700 to-primary-900 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <span class="text-3xl opacity-20 group-hover:opacity-40 transition-opacity">📊</span>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-1"><?= trans('reports') ?></h3>
            <p class="text-sm text-gray-600"><?= trans('analytics_insights') ?></p>
        </a>

        <!-- Announcements Card (Full width orange bar) -->
        <a href="?url=announcement" class="group glass-card-light p-6 card-hover border-l-4 border-orange-500 lg:col-span-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 mb-1"><?= trans('announcements') ?></h3>
                        <p class="text-sm text-gray-600"><?= trans('latest_updates_notifications') ?></p>
                    </div>
                </div>
                <span class="text-4xl opacity-20 group-hover:opacity-40 transition-opacity">📢</span>
            </div>
        </a>
    </div>

    <!-- Bottom Section: Two White Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Upcoming Poojas -->
        <div class="glass-card-light p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-800"><?= trans('upcoming_poojas') ?></h3>
                <a href="?url=schedule" class="text-primary-600 hover:text-primary-700 font-semibold text-sm"><?= trans('view_all') ?> →</a>
            </div>
            <div class="space-y-3">
                <?php
                $stmt = $this->conn->prepare("SELECT * FROM pooja_schedule WHERE status = 'available' ORDER BY pooja_date LIMIT 3");
                $stmt->execute();
                $result = $stmt->get_result();
                while ($row = $result->fetch_assoc()):
                ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div>
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($row['pooja_name']) ?></p>
                        <p class="text-sm text-gray-600"><?= date('M d, Y', strtotime($row['pooja_date'])) ?> at <?= date('g:i A', strtotime($row['time_slot'])) ?></p>
                    </div>
                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">Available</span>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Upcoming Festivals -->
        <div class="glass-card-light p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-800"><?= trans('upcoming_festivals') ?></h3>
                <a href="?url=festival" class="text-primary-600 hover:text-primary-700 font-semibold text-sm"><?= trans('view_all') ?> →</a>
            </div>
            <div class="space-y-3">
                <?php
                // Use festival model instead of direct query
                require_once __DIR__ . '/../../models/Festival.php';
                $festivalModel = new Festival();
                $festivals = $festivalModel->getAll();
                $count = 0;
                if ($festivals instanceof mysqli_result && $festivals->num_rows > 0) {
                    while ($row = $festivals->fetch_assoc()):
                        if ($count >= 3) break;
                        $count++;
                ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div>
                        <p class="font-semibold text-gray-800"><?= htmlspecialchars($row['name'] ?? 'Festival') ?></p>
                        <p class="text-sm text-gray-600">
                            <?php if(isset($row['date']) && !empty($row['date'])): ?>
                                <?= date('M d, Y', strtotime($row['date'])) ?>
                            <?php else: ?>
                                <span class="text-gray-400">TBD</span>
                            <?php endif; ?>
                        </p>
                    </div>
                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-semibold rounded-full">Festival</span>
                </div>
                <?php 
                    endwhile;
                } else {
                    echo '<p class="text-gray-500 text-sm">No upcoming festivals</p>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
