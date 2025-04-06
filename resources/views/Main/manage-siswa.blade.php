<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
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
        
        /* For Webkit browsers like Chrome/Safari */
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

        /* For dark mode */
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
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
    <div class="min-h-full">
        <!-- Header -->
        @include('Cert.head')

        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 content">
            <!-- Page Header -->
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 sm:truncate sm:text-3xl">Kelola Data Siswa</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Kelola data siswa dan akun siswa
                    </p>
                </div>
            </div>

            <!-- Tabs -->
            <div class="mb-6">
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <nav class="-mb-px flex space-x-8">
                        <button id="btn-dataSiswa" class="border-primary-600 text-primary-600 dark:text-primary-400 dark:border-primary-400 hover:text-primary-700 dark:hover:text-primary-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" onclick="showTab('data-siswa')">
                            Data Siswa
                        </button>
                        <button id="btn-akunSiswa" class="border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" onclick="showTab('akun-siswa')">
                            Akun Siswa
                        </button>
                    </nav>
                </div>
            </div> 

            @yield('content')
        </main>
    </div>

    <script>
        function showTab(tabId) {
            if(tabId === 'akun-siswa'){
                window.location.href = '{{ route('akun.siswa') }}'
            } else {
                window.location.href = '{{ route('siswa') }}'
            }
        }
    
        document.addEventListener('DOMContentLoaded', () => {
            const currentPath = '{{ request()->route()->getName() }}';
    
            if (currentPath === 'siswa') {
                document.getElementById('btn-dataSiswa').classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                document.getElementById('btn-dataSiswa').classList.add('border-primary-600', 'text-primary-600', 'dark:text-primary-400', 'dark:border-primary-400');
                document.getElementById('btn-akunSiswa').classList.remove('border-primary-600', 'text-primary-600', 'dark:text-primary-400', 'dark:border-primary-400')
                document.getElementById('btn-akunSiswa').classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            } else if (currentPath === 'akun.siswa') {
                document.getElementById('btn-akunSiswa').classList.remove('border-transparent', 'text-gray-500', 'dark:text-gray-400');
                document.getElementById('btn-akunSiswa').classList.add('border-primary-600', 'text-primary-600', 'dark:text-primary-400', 'dark:border-primary-400');
                document.getElementById('btn-dataSiswa').classList.remove('border-primary-600', 'text-primary-600', 'dark:text-primary-400', 'dark:border-primary-400')
                document.getElementById('btn-dataSiswa').classList.add('border-transparent', 'text-gray-500', 'dark:text-gray-400');
            }
        });
    </script>    
</body>
</html>