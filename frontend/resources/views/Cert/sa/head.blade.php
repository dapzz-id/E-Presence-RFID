<script async>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const content = document.getElementById('content');
        const overlay = document.getElementById('overlay');
        
        if (sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
            
            if (window.innerWidth >= 768) {
                content.classList.add('md:ml-64');
            }
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            content.classList.remove('md:ml-64');
        }
    }
</script>

<div id="sidebar" class="fixed top-0 left-0 h-full w-64 bg-white dark:bg-gray-800 shadow-md transform -translate-x-full transition-transform duration-300 ease-in-out z-20">
    <div class="pt-16 px-4">
        <nav class="space-y-1">
            <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 {{ request()->path() == 'superadmin/dashboard' ? 'bg-gray-100 dark:bg-gray-700 border-l-4' : 'hover:border-l-4 hover:bg-gray-100 dark:hover:bg-gray-700' }} rounded-md border-primary max-md:text-sm">Home</a>
            <a href="{{ route('siswa') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 {{ request()->path() == 'superadmin/notifikasi' ? 'bg-gray-100 dark:bg-gray-700 border-l-4' : 'hover:border-l-4 hover:bg-gray-100 dark:hover:bg-gray-700' }} rounded-md border-primary max-md:text-sm">Notifikasi</a>
            <a href="#" class="block px-4 py-2 text-gray-700 dark:text-gray-200 {{ request()->path() == 'rfid-card' ? 'bg-gray-100 dark:bg-gray-700 border-l-4' : 'hover:border-l-4 hover:bg-gray-100 dark:hover:bg-gray-700' }} rounded-md border-primary max-md:text-sm">Register Card</a>
            <a href="{{ route('logout') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-200 {{ request()->path() == 'logout' ? 'bg-gray-100 dark:bg-gray-700 border-l-4' : 'hover:border-l-4 hover:bg-gray-100 dark:hover:bg-gray-700' }} rounded-md border-primary max-md:text-sm">Logout</a>
        </nav>
    </div>
    <footer class="text-xs text-center text-gray-500 dark:text-gray-400 absolute bottom-0 w-full mb-2 p-4">
        Version 1.0 | 2025
    </footer>
</div>

<div id="overlay" class="fixed inset-0 bg-black bg-opacity-50 z-10 hidden" onclick="toggleSidebar()"></div>

<nav class="sticky top-0 bg-white dark:bg-gray-800 shadow-sm transition-colors duration-300 z-30">
    <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between items-center">
            <div class="flex items-center">
                <button 
                    onclick="toggleSidebar()" 
                    class="p-2 mr-3 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                    aria-label="Toggle sidebar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <h1 class="text-xl font-semibold max-md:text-lg max-sm:text-sm">E-PRESENCE</h1>
            </div>
            <div class="flex items-center space-x-4">
                <span id="current-time" class="text-sm font-medium max-md:text-xs"></span>
                <button 
                    onclick="toggleDarkMode()" 
                    class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200"
                    aria-label="Toggle dark mode"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</nav>

<script>
    function updateTime() {
        const now = new Date();
        const options = { 
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        };

        let timeString = now.toLocaleTimeString('id-ID', options).replace(/\./g, ':');
        document.getElementById('current-time').textContent = timeString;
    }

    updateTime();
    setInterval(updateTime, 1000);
</script>