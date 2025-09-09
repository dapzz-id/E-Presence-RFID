<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Siswa</title>
    @stack('styles')
    @vite(['resources/js/app.js'])
    @stack('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }

        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark')
        } else {
            document.documentElement.classList.remove('dark')
        }

        function toggleDarkMode() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark')
                localStorage.theme = 'light'
            } else {
                document.documentElement.classList.add('dark')
                localStorage.theme = 'dark'
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-track {
            background: #1f2937;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4b5563;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
</head>

<body class="h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
    <div class="min-h-full">
        @include('Cert.head')

        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 content">
            @if (session('success'))
                <div id="success-alert"
                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-green-500" role="button"
                            onclick="closeAlert('success-alert')" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20">
                            <title>Close</title>
                            <path
                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                        </svg>
                    </span>
                </div>
            @endif

            @if (session('error'))
                <div id="error-alert"
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button"
                            onclick="closeAlert('error-alert')" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Close</title>
                            <path
                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                        </svg>
                    </span>
                </div>
            @endif

            <script>
                function closeAlert(alertId) {
                    const alertElement = document.getElementById(alertId);
                    if (alertElement) {
                        alertElement.style.display = 'none';
                    }
                }
            </script>
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-row items-center justify-between">
                        <h2 class="max-md:text-xl font-bold leading-7 sm:truncate text-2xl">Kelola Data Siswa</h2>
                        @if (request()->path() === 'admin/akun-siswa/getChart')
                            <a href="{{ route('akun.siswa') }}" class="text-primary-600 hover:text-primary-700 mr-2 dark:text-primary-400 dark:hover:text-primary-300 flex items-center text-sm">
                                <i class="bi bi-arrow-left mr-1"></i> Kembali
                            </a>
                        @endif
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Kelola data siswa dan akun siswa
                    </p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex flex-wrap gap-2 sm:gap-4 sm:flex-nowrap sm:space-x-8">
                        <button id="btn-dataSiswa"
                            class="border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-400 hover:text-primary-700 dark:hover:text-primary-300 whitespace-nowrap py-2 px-3 border-b-2 font-medium text-sm sm:py-4 sm:px-1"
                            onclick="showTab('data-siswa')">
                            Data Siswa
                        </button>
                        <button id="btn-akunSiswa"
                            class="border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap py-2 px-3 border-b-2 font-medium text-sm sm:py-4 sm:px-1"
                            onclick="showTab('akun-siswa')">
                            Akun Siswa
                        </button>
                        <button id="btn-fotoSiswa"
                            class="border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap py-2 px-3 border-b-2 font-medium text-sm sm:py-4 sm:px-1"
                            onclick="showTab('photos.index')">
                            Foto Siswa
                        </button>
                    </nav>
                </div>
            </div>

            @yield('content')
        </main>
    </div>

    <script>
        function showTab(tabId) {
            if (tabId === 'akun-siswa') {
                window.location.href = '{{ route('akun.siswa') }}'
            } else if (tabId === 'data-siswa') {
                window.location.href = '{{ route('siswa') }}'
            } else if (tabId === 'photos.index') {
                window.location.href = '{{ route('photos.index') }}'
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const currentPath = '{{ request()->route()->getName() }}';

            if (currentPath === 'siswa') {
                document.getElementById('btn-dataSiswa').classList.remove('border-transparent', 'text-gray-500',
                    'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300',
                    'hover:border-gray-300', 'dark:hover:border-gray-600');
                document.getElementById('btn-dataSiswa').classList.add('border-primary-600', 'text-primary-600',
                    'dark:text-primary-400', 'dark:border-primary-400', 'hover:text-primary-700',
                    'dark:hover:text-primary-300');
                document.getElementById('btn-akunSiswa').classList.remove('border-primary-600', 'text-primary-600',
                    'dark:text-primary-400', 'dark:border-primary-400', 'hover:text-primary-700',
                    'dark:hover:text-primary-300')
                document.getElementById('btn-akunSiswa').classList.add('border-transparent', 'text-gray-500',
                    'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300',
                    'hover:border-gray-300', 'dark:hover:border-gray-600');
                document.getElementById('btn-fotoSiswa').classList.remove('border-primary-600', 'text-primary-600',
                    'dark:text-primary-400', 'dark:border-primary-400', 'hover:text-primary-700',
                    'dark:hover:text-primary-300')
                document.getElementById('btn-fotoSiswa').classList.add('border-transparent', 'text-gray-500',
                    'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300',
                    'hover:border-gray-300', 'dark:hover:border-gray-600');
            } else if (currentPath === 'akun.siswa' || currentPath === 'akun.siswa.create' || currentPath === 'akun.siswa.edit' || currentPath === 'attendance.index') {
                document.getElementById('btn-akunSiswa').classList.remove('border-transparent', 'text-gray-500',
                    'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300',
                    'hover:border-gray-300', 'dark:hover:border-gray-600');
                document.getElementById('btn-akunSiswa').classList.add('border-primary-600', 'text-primary-600',
                    'dark:text-primary-400', 'dark:border-primary-400', 'hover:text-primary-700',
                    'dark:hover:text-primary-300');
                document.getElementById('btn-dataSiswa').classList.remove('border-primary-600', 'text-primary-600',
                    'dark:text-primary-400', 'dark:border-primary-400', 'hover:text-primary-700',
                    'dark:hover:text-primary-300')
                document.getElementById('btn-dataSiswa').classList.add('border-transparent', 'text-gray-500',
                    'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300',
                    'hover:border-gray-300', 'dark:hover:border-gray-600');
                document.getElementById('btn-fotoSiswa').classList.remove('border-primary-600', 'text-primary-600',
                    'dark:text-primary-400', 'dark:border-primary-400', 'hover:text-primary-700',
                    'dark:hover:text-primary-300')
                document.getElementById('btn-fotoSiswa').classList.add('border-transparent', 'text-gray-500',
                    'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300',
                    'hover:border-gray-300', 'dark:hover:border-gray-600');
            } else if (currentPath === 'photos.index') {
                document.getElementById('btn-dataSiswa').classList.remove('border-primary-600', 'text-primary-600',
                    'dark:text-primary-400', 'dark:border-primary-400', 'hover:text-primary-700',
                    'dark:hover:text-primary-300');
                document.getElementById('btn-dataSiswa').classList.add('border-transparent', 'text-gray-500',
                    'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300',
                    'hover:border-gray-300', 'dark:hover:border-gray-600');
                document.getElementById('btn-akunSiswa').classList.remove('border-primary-600', 'text-primary-600',
                    'dark:text-primary-400', 'dark:border-primary-400', 'hover:text-primary-700',
                    'dark:hover:text-primary-300')
                document.getElementById('btn-akunSiswa').classList.add('border-transparent', 'text-gray-500',
                    'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300',
                    'hover:border-gray-300', 'dark:hover:border-gray-600');
                document.getElementById('btn-fotoSiswa').classList.remove('border-transparent', 'text-gray-500',
                    'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300',
                    'hover:border-gray-300', 'dark:hover:border-gray-600');
                document.getElementById('btn-fotoSiswa').classList.add('border-primary-600', 'text-primary-600',
                    'dark:text-primary-400', 'dark:border-primary-400', 'hover:text-primary-700',
                    'dark:hover:text-primary-300')
            }
        });

        localStorage.removeItem("uid");
    </script>
</body>

</html>
