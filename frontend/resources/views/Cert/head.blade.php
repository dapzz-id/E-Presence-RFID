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

        this.blur();

        const dummy = document.getElementById('main-guard');
        if (dummy) dummy.focus();
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

<div id="sidebar"
    class="fixed top-0 left-0 h-full w-64 bg-white dark:bg-gray-800 shadow-md transform -translate-x-full transition-transform duration-300 ease-in-out z-20">
    <div class="pt-16 px-4">
        <nav class="space-y-1">
            <a href="{{ route('dashboard') }}"
                class="block px-4 py-2 text-gray-700 dark:text-gray-200 {{ request()->path() == 'admin/dashboard' ? 'bg-gray-100 dark:bg-gray-700 border-l-4' : 'hover:border-l-4 hover:bg-gray-100 dark:hover:bg-gray-700' }} rounded-md border-primary max-md:text-sm">Dasbor</a>
            <a href="{{ route('hari.index') }}"
                class="block px-4 py-2 text-gray-700 dark:text-gray-200 {{ request()->path() == 'admin/hari' || request()->path() == 'admin/hari/form' ? 'bg-gray-100 dark:bg-gray-700 border-l-4' : 'hover:border-l-4 hover:bg-gray-100 dark:hover:bg-gray-700' }} rounded-md border-primary max-md:text-sm">Tentukan Hari</a>
            <a href="{{ route('siswa') }}"
                class="block px-4 py-2 text-gray-700 dark:text-gray-200 {{ request()->path() == 'admin/siswa' || request()->path() == 'admin/siswa/add' || Str::startsWith(request()->path(), 'admin/akun-siswa') || request()->path() == 'admin/photos' || Str::startsWith(request()->path(), 'admin/siswa/edit/') ? 'bg-gray-100 dark:bg-gray-700 border-l-4' : 'hover:border-l-4 hover:bg-gray-100 dark:hover:bg-gray-700' }} rounded-md border-primary max-md:text-sm">Kelola Siswa</a>
            @php
                $systemSettings = App\Models\Settings::first();
                $attendanceMethod = $systemSettings->attendance_method ?? 'rfid';
            @endphp
            
            @if($attendanceMethod === 'face_id')
                <a href="{{ route('settings.register-face') }}"
                    class="block px-4 py-2 text-gray-700 dark:text-gray-200 {{ request()->routeIs('settings.register-face') ? 'bg-gray-100 dark:bg-gray-700 border-l-4' : 'hover:border-l-4 hover:bg-gray-100 dark:hover:bg-gray-700' }} rounded-md border-primary max-md:text-sm">
                    Daftarkan Wajah
                </a>
            @else
                <a href="{{ route('rfid.connect') }}"
                    class="block px-4 py-2 text-gray-700 dark:text-gray-200 {{ request()->path() == 'admin/rfid-connect' ? 'bg-gray-100 dark:bg-gray-700 border-l-4' : 'hover:border-l-4 hover:bg-gray-100 dark:hover:bg-gray-700' }} rounded-md border-primary max-md:text-sm">
                   Daftarkan Kartu
                </a>
            @endif
                @php
                    $statusMembership = App\Models\AdminAccount::where('id', auth()->user()->id)->where('membership', true)->exists();
                @endphp
            <a href="{{ route('connections.index') }}"
                class="flex flex-row items-center px-4 py-2 text-gray-700 dark:text-gray-200 {{ request()->path() == 'admin/connections' ? 'bg-gray-100 dark:bg-gray-700 border-l-4' : 'hover:border-l-4 hover:bg-gray-100 dark:hover:bg-gray-700' }} rounded-md border-primary max-md:text-sm">Hubungkan Data @if (!$statusMembership)  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-crown-icon ml-auto text-yellow-500 lucide-crown"><path d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z"/><path d="M5 21h14"/></svg>  @endif</a>
            <a href="{{ route('settings.index') }}"
                class="block px-4 py-2 text-gray-700 dark:text-gray-200 {{ request()->path() == 'admin/settings' ? 'bg-gray-100 dark:bg-gray-700 border-l-4' : 'hover:border-l-4 hover:bg-gray-100 dark:hover:bg-gray-700' }} rounded-md border-primary max-md:text-sm">
                Setting
            </a>
            <a onclick="confirmLogout()"
                class="block px-4 py-2 text-gray-700 dark:text-gray-200 {{ request()->path() == 'logout' ? 'bg-gray-100 dark:bg-gray-700 border-l-4' : 'hover:border-l-4 hover:bg-gray-100 dark:hover:bg-gray-700' }} rounded-md border-primary max-md:text-sm">Logout</a>
        </nav>
        <script>
            confirmLogout = () => {
                Swal.fire({
                    title: "Are you sure?",
                    text: "Are you sure you want to log out of this account?",
                    icon: "question",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, continue",
                    cancelButtonText: "No, cancel",
                    theme: localStorage.getItem('theme') === 'dark' ? 'dark' : 'light',
                    allowOutsideClick: true,
                    allowEscapeKey: true,

                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "{{ route('logout') }}";
                    }
                });
            };
        </script>
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
                @if (request()->path() !== 'dataHariIni')
                    <button onclick="toggleSidebar()"
                        class="p-2 mr-3 rounded-md text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700"
                        aria-label="Toggle sidebar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                @endif
                <div class="flex items-center me-1">
                    <a href="{{ route('view.main') }}" class="flex items-center cursor-pointer">
                        <i class="bi bi-credit-card-2-front text-primary-400 text-2xl mr-2"></i>
                        <span class="max-md:text-sm font-bold dark:text-white max-md:hidden text-black">E-PRESENCE</span>
                    </a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span id="current-time" class="text-sm font-medium max-md:text-xs"></span>
                <button onclick="toggleDarkMode()"
                    class="p-2 rounded-full bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200"
                    aria-label="Toggle dark mode">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden dark:block" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 block dark:hidden" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
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
