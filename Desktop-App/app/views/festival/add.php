<div class="space-y-6">
    <div class="flex items-center space-x-4 mb-4">
        <a href="?url=festival" class="group flex items-center space-x-2 bg-white/80 hover:bg-white backdrop-blur-sm px-4 py-2 rounded-xl shadow-md transition-all duration-200">
            <span class="font-semibold text-primary-700">Back</span>
        </a>
    </div>

    <div class="glass-card p-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Add Festival</h1>
        <p class="text-gray-600 mb-6">Create a new festival entry.</p>
        <form action="?url=festival&action=store" method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Festival Name *</label>
                <input type="text" name="name" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Date *</label>
                <input type="date" name="date" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 outline-none" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                <textarea name="description" rows="4" class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:border-primary-500 outline-none"></textarea>
            </div>
            <button type="submit" class="w-full bg-gradient-to-r from-primary-700 to-primary-800 text-white font-bold py-4 px-6 rounded-xl shadow-lg">
                Save Festival
            </button>
        </form>
    </div>
</div>
