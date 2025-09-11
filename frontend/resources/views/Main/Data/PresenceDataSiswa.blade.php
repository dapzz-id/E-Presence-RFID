<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-PRESENCE</title>
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
                    animation: {
                        'bounce-slow': 'bounce 3s infinite',
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        }
                    },
                    fontSize: {
                        'xs-mobile': '0.7rem',
                        'sm-mobile': '0.8rem',
                        'base-mobile': '0.95rem',
                        'lg-mobile': '1.05rem',
                        'xl-mobile': '1.15rem',
                        '2xl-mobile': '1.35rem',
                        '3xl-mobile': '1.65rem',
                    }
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

        localStorage.removeItem("uid");
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        /* Improved font rendering */
        html {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            text-rendering: optimizeLegibility;
        }

        /* Mobile typography adjustments */
        @media (max-width: 640px) {
            .mobile-text-adjust {
                letter-spacing: -0.01em;
                line-height: 1.4;
            }
            
            .mobile-heading {
                font-size: 1.25rem !important;
                font-weight: 700;
                letter-spacing: -0.02em;
            }
            
            .mobile-subheading {
                font-size: 1rem !important;
                font-weight: 600;
            }
            
            .mobile-card-title {
                font-size: 0.7rem !important;
                letter-spacing: 0.02em;
            }
            
            .mobile-card-value {
                font-size: 1.5rem !important;
                font-weight: 700;
                letter-spacing: -0.02em;
            }
            
            .mobile-table-header {
                font-size: 0.65rem !important;
                font-weight: 600;
                letter-spacing: 0.03em;
            }
            
            .mobile-table-data {
                font-size: 0.8rem !important;
            }
            
            .mobile-badge {
                font-size: 0.65rem !important;
                padding: 0.15rem 0.4rem;
            }
        }

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

        .tab-active {
            border-bottom: 2px solid #0ea5e9;
            color: #0ea5e9;
        }

        .dark .tab-active {
            border-bottom: 2px solid #38bdf8;
            color: #38bdf8;
        }

        .today-tab {
            background-color: rgba(14, 165, 233, 0.1);
            font-weight: 600;
        }

        .dark .today-tab {
            background-color: rgba(56, 189, 248, 0.1);
        }

        .modal {
            transition: opacity 0.25s ease;
        }

        .modal-active {
            overflow-x: hidden;
            overflow-y: visible !important;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            50% { transform: translateX(5px); }
            75% { transform: translateX(-5px); }
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        @keyframes glow {
            0%, 100% { box-shadow: 0 0 5px rgba(14, 165, 233, 0.5); }
            50% { box-shadow: 0 0 20px rgba(14, 165, 233, 0.8); }
        }

        .date-input-container:hover .date-input {
            border-color: rgba(14, 165, 233, 0.5);
        }

        .date-input:focus + .date-indicator {
            opacity: 1;
        }

        .date-input-filled .date-indicator {
            opacity: 1;
            background-color: #10b981;
        }

        .modal-active {
            overflow: hidden !important;
        }

        .dark .bg-gray-750 {
            background-color: #1e293b;
        }

        .dark .bg-gray-650 {
            background-color: #334155;
        }

        .backdrop-blur-sm {
            backdrop-filter: blur(4px);
        }

        .quick-date-btn.active {
            background-color: rgba(14, 165, 233, 0.2);
            color: #0ea5e9;
            font-weight: 500;
        }

        .dark .quick-date-btn.active {
            background-color: rgba(56, 189, 248, 0.2);
            color: #38bdf8;
        }
    </style>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
</head>

<body class="antialiased h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div class="min-h-full">
        <header class="sticky top-0 bg-white dark:bg-gray-800 shadow">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <div class="flex items-center">
                    <a href="{{ route('view.main') }}" class="flex items-center cursor-pointer">
                        <i class="bi bi-credit-card-2-front text-primary-400 text-2xl mr-2"></i>
                        <span class="text-xl max-md:text-base font-bold text-black dark:text-white">E-PRESENCE</span>
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <span id="current-time" class="text-sm font-medium max-md:text-xs"></span>
                    <button onclick="toggleDarkMode()" class="p-1 rounded-full text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
            <!-- Class Selection Section -->
            <div class="mb-6 sm:mb-8">
                <h2 class="text-lg max-md:text-sm font-medium mb-2 mobile-subheading">Pilih Kelas</h2>
                <form action="{{ route('check.hari.ini') }}" method="GET" id="kelasForm" class="flex flex-col sm:flex-row gap-3 sm:gap-4 items-start sm:items-end">
                    <div class="w-full sm:w-64">
                        <select id="kelas" name="kelas" onchange="this.form.submit()" class="mt-1 block w-full pl-3 pr-10 py-2 text-base max-md:text-sm border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-primary-500 focus:border-primary-500 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white mobile-text-adjust">
                            <option value="" disabled selected>Pilih Kelas</option>
                            @foreach (['X RPL 1', 'X RPL 2', 'X RPL 3', 'X RPL 4', 'XI RPL 1', 'XI RPL 2', 'XI RPL 3', 'XI RPL 4', 'XII RPL 1', 'XII RPL 2', 'XII RPL 3', 'XII RPL 4', 'X DKV 1', 'X DKV 2', 'X DKV 3', 'XI DKV 1', 'XI DKV 2', 'XI DKV 3', 'XII DKV 1', 'XII DKV 2', 'XII DKV 3', 'X TKJ 1', 'X TKJ 2', 'X TKJ 3', 'XI TKJ 1', 'XI TKJ 2', 'XI TKJ 3', 'XII TKJ 1', 'XII TKJ 2', 'XII TKJ 3', 'X TRANSMISI', 'XI TRANSMISI', 'XII TRANSMISI'] as $kelasItem)
                                <option value="{{ $kelasItem }}" {{ request('kelas') == $kelasItem ? 'selected' : '' }}>{{ $kelasItem }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <!-- Date Display -->
            <div class="mb-5 sm:mb-6">
                <p class="text-sm max-md:text-xs text-gray-500 dark:text-gray-400 transition-colors duration-100 mobile-text-adjust">
                    {{ date('l, d F Y') }}
                </p>
            </div>

            @if(request('kelas') || request()->has('filter'))
                <!-- Statistics Cards -->
                <div class="grid grid-cols-1 gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6 sm:mb-8">
                    <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow">
                        <div class="p-4 sm:p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 rounded-md bg-primary-100 dark:bg-primary-900 p-2 sm:p-3">
                                    <svg class="h-5 w-5 sm:h-6 sm:w-6 text-primary-600 dark:text-primary-300" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4 sm:ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate uppercase mobile-card-title">TOTAL
                                            DATANG SISWA {{ $filter ?? 'HARI INI' }}</dt>
                                        <dd>
                                            <div class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white mobile-card-value">
                                                {{ $totalHariIni ?? 0 }}
                                                <span class="text-xs font-normal ml-1 px-1.5 py-0.5 sm:px-2 sm:py-1 rounded-full mobile-badge
                                                    @if($todayDayType == 'Hari Produktif') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                    @elseif($todayDayType == 'Hari Non-Produktif') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                                    {{ $todayDayType }}
                                                </span>
                                            </div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow">
                        <div class="p-4 sm:p-5">
                            <div class="flex items-center">
                                <div class="cursor-pointer flex-shrink-0 rounded-md bg-yellow-100 dark:bg-yellow-900 p-2 sm:p-3">
                                    <svg class="h-5 w-5 sm:h-6 sm:w-6 text-yellow-600 dark:text-yellow-300" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="ml-4 sm:ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate mobile-card-title">TOTAL
                                            SISWA IZIN/SAKIT</dt>
                                        <dd>
                                            <div class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white mobile-card-value">
                                                {{ $totalTidakHadir ?? 0 }}</div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow">
                        <div class="p-4 sm:p-5">
                            <div class="flex items-center">
                                <div class="cursor-pointer flex-shrink-0 rounded-md bg-red-100 dark:bg-red-900 p-2 sm:p-3">
                                    <svg class="h-5 w-5 sm:h-6 sm:w-6 text-red-600 dark:text-red-300" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                    </svg>
                                </div>
                                <div class="ml-4 sm:ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate mobile-card-title">TOTAL
                                            SISWA ALPA</dt>
                                        <dd>
                                            <div class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white mobile-card-value">
                                                {{ $totalAlpa ?? 0 }}</div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow">
                        <div class="p-4 sm:p-5">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 rounded-md bg-green-100 dark:bg-green-900 p-2 sm:p-3">
                                    <svg class="h-5 w-5 sm:h-6 sm:w-6 text-green-600 dark:text-green-300" fill="none"
                                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                    </svg>
                                </div>
                                <div class="ml-4 sm:ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate mobile-card-title">TOTAL
                                            SISWA KESELURUHAN</dt>
                                        <dd>
                                            <div class="text-2xl sm:text-3xl font-semibold text-gray-900 dark:text-white mobile-card-value">
                                                {{ $total ?? 0 }}</div>
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Data Table Section -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg h-full">
                    <div class="flex flex-row justify-between items-center">
                        <div class="px-4 py-4 sm:py-5 ml-1 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-base sm:text-lg font-medium leading-6 text-gray-900 dark:text-white mobile-subheading">Data Absensi Siswa</h3>
                            <p class="mt-1 max-w-2xl text-xs sm:text-sm text-gray-500 dark:text-gray-400 mobile-text-adjust">Daftar absensi siswa
                                {{ $filter ?? 'hari ini' }}</p>
                        </div>
                        <div class="flex items-center space-x-2 mr-3 sm:mr-4">
                            <div class="relative">
                                <button type="button"
                                    class="inline-flex items-center px-2 py-2 sm:px-3 sm:py-3 border border-gray-300 dark:border-gray-600 text-xs sm:text-sm font-medium rounded-md shadow-sm text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                    id="filter-menu-button" aria-expanded="false" aria-haspopup="true">
                                    <i class="fa-solid fa-filter"></i>
                                    <span class="hidden sm:inline-block sm:ml-2">{{ $filter ?? 'Hari ini' }}</span>
                                    <i class="fa-solid fa-chevron-down ml-1 sm:ml-2"></i>
                                </button>
                                <div class="hidden absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white dark:bg-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                    id="filter-menu" role="menu" aria-orientation="vertical"
                                    aria-labelledby="filter-menu-button" tabindex="-1">
                                    <div class="py-1" role="none">
                                        <a href="{{ route('check.hari.ini', ['filter' => 'Hari ini', 'tab' => $tab, 'kelas' => request('kelas')]) }}"
                                            class="text-gray-700 dark:text-gray-200 block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"
                                            role="menuitem" tabindex="-1">Hari ini</a>
                                        <a href="{{ route('check.hari.ini', ['filter' => 'Kemarin', 'tab' => $tab, 'kelas' => request('kelas')]) }}"
                                            class="text-gray-700 dark:text-gray-200 block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"
                                            role="menuitem" tabindex="-1">Kemarin</a>
                                        <a href="{{ route('check.hari.ini', ['filter' => 'Minggu Lalu', 'tab' => $tab, 'kelas' => request('kelas')]) }}"
                                            class="text-gray-700 dark:text-gray-200 block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"
                                            role="menuitem" tabindex="-1">Minggu Lalu</a>
                                        <a href="{{ route('check.hari.ini', ['filter' => 'Minggu Ini', 'tab' => $tab, 'kelas' => request('kelas')]) }}"
                                            class="text-gray-700 dark:text-gray-200 block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"
                                            role="menuitem" tabindex="-1">Minggu Ini</a>
                                        <a href="{{ route('check.hari.ini', ['filter' => 'Bulan Lalu', 'tab' => $tab, 'kelas' => request('kelas')]) }}"
                                            class="text-gray-700 dark:text-gray-200 block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"
                                            role="menuitem" tabindex="-1">Bulan Lalu</a>
                                        <a href="{{ route('check.hari.ini', ['filter' => 'Bulan Ini', 'tab' => $tab, 'kelas' => request('kelas')]) }}"
                                            class="text-gray-700 dark:text-gray-200 block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"
                                            role="menuitem" tabindex="-1">Bulan Ini</a>
                                        <a href="{{ route('check.hari.ini', ['filter' => 'Tahun Lalu', 'tab' => $tab, 'kelas' => request('kelas')]) }}"
                                            class="text-gray-700 dark:text-gray-200 block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"
                                            role="menuitem" tabindex="-1">Tahun Lalu</a>
                                        <a href="{{ route('check.hari.ini', ['filter' => 'Tahun Ini', 'tab' => $tab, 'kelas' => request('kelas')]) }}"
                                            class="text-gray-700 dark:text-gray-200 block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"
                                            role="menuitem" tabindex="-1">Tahun Ini</a>
                                        <hr class="my-1 border-gray-200 dark:border-gray-600">
                                        <button id="custom-date-btn"
                                            class="w-full text-left text-gray-700 dark:text-gray-200 block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-600"
                                            role="menuitem" tabindex="-1">Pilih Tanggal</button>
                                    </div>
                                </div>
                            </div>
                            <button
                                class="inline-flex items-center px-2 py-2 sm:px-3 sm:py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                                title="Refresh Page Button" onclick="window.location.reload()">
                                <i class="fa-solid fa-rotate-right"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Tabs -->
                    <div class="border-b border-gray-200 dark:border-gray-700">
                        <nav class="flex flex-wrap -mb-px px-2 sm:px-6 overflow-x-auto" aria-label="Tabs">
                            @php
                                $queryParams = request()->query();
                                unset($queryParams['tab']);
                                $queryString = http_build_query($queryParams);
                                $queryPrefix = !empty($queryString) ? '&' : '';
                            @endphp

                            <a href="{{ route('check.hari.ini', ['tab' => 'all', 'kelas' => request('kelas')] + request()->except('tab')) }}"
                                class="py-2 sm:py-3 px-2 sm:px-3 text-center border-b-2 font-medium text-xs sm:text-sm mr-1 sm:mr-4 whitespace-nowrap {{ $tab === 'all' ? 'tab-active' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-gray-600' }}">
                                Semua
                            </a>
                            <a href="{{ route('check.hari.ini', ['tab' => 'hadir', 'kelas' => request('kelas')] + request()->except('tab')) }}"
                                class="py-2 sm:py-3 px-2 sm:px-3 text-center border-b-2 font-medium text-xs sm:text-sm mr-1 sm:mr-4 whitespace-nowrap {{ $tab === 'hadir' ? 'tab-active' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-gray-600' }}">
                                Hadir
                            </a>
                            <a href="{{ route('check.hari.ini', ['tab' => 'izin', 'kelas' => request('kelas')] + request()->except('tab')) }}"
                                class="py-2 sm:py-3 px-2 sm:px-3 text-center border-b-2 font-medium text-xs sm:text-sm mr-1 sm:mr-4 whitespace-nowrap {{ $tab === 'izin' ? 'tab-active' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-gray-600' }}">
                                Izin
                            </a>
                            <a href="{{ route('check.hari.ini', ['tab' => 'sakit', 'kelas' => request('kelas')] + request()->except('tab')) }}"
                                class="py-2 sm:py-3 px-2 sm:px-3 text-center border-b-2 font-medium text-xs sm:text-sm mr-1 sm:mr-4 whitespace-nowrap {{ $tab === 'sakit' ? 'tab-active' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-gray-600' }}">
                                Sakit
                            </a>
                            <a href="{{ route('check.hari.ini', ['tab' => 'alpa', 'kelas' => request('kelas')] + request()->except('tab')) }}"
                                class="py-2 sm:py-3 px-2 sm:px-3 text-center border-b-2 font-medium text-xs sm:text-sm mr-1 sm:mr-4 whitespace-nowrap {{ $tab === 'alpa' ? 'tab-active' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300 dark:hover:border-gray-600' }}">
                                Alpa
                            </a>
                        </nav>
                    </div>

                    @if($dayType === 'Hari Libur' && !in_array($tab, ['izin', 'sakit', 'hadir', 'alpa']))
                        <div class="flex flex-col items-center justify-center py-10 sm:py-12 animate-fade-in">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 sm:h-16 sm:w-16 text-gray-400 dark:text-gray-600 animate-pulse-slow"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 13.5V15m-6 4h12a2 2 0 002-2v-12a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h3 class="mt-4 text-base sm:text-lg font-medium text-gray-900 dark:text-gray-100 mobile-subheading">
                                    {{ $filter ?? 'Hari ini' }} Libur</h3>
                                <p class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400 mobile-text-adjust">
                                    Tidak ada kegiatan belajar mengajar {{ $filter ?? 'hari ini' }}
                                </p>
                            </div>
                        </div>
                    @elseif(count($dataPresensi) > 0)
                        <div class="overflow-x-auto custom-scrollbar">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 table-fixed md:table-auto">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-10 mobile-table-header">
                                            No</th>
                                        <th scope="col"
                                            class="px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider mobile-table-header">
                                            NIS</th>
                                        <th scope="col"
                                            class="px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider mobile-table-header">
                                            Nama</th>
                                        <th scope="col"
                                            class="px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider mobile-table-header">
                                            Kelas</th>
                                        <th scope="col"
                                            class="px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider mobile-table-header">
                                            Tanggal Masuk</th>
                                        <th scope="col"
                                            class="px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider mobile-table-header">
                                            Status Masuk</th>
                                        <th scope="col"
                                            class="px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider mobile-table-header">
                                            Tanggal Keluar</th>
                                        <th scope="col"
                                            class="px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider mobile-table-header">
                                            Status Keluar</th>
                                        @if($tab === 'izin' || $tab === 'sakit')
                                            <th scope="col"
                                                class="px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider hidden sm:table-cell mobile-table-header">
                                                Keterangan Lainnya</th>
                                            <th scope="col"
                                                class="px-2 sm:px-6 py-2 sm:py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider mobile-table-header">
                                                Dokumen</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($dataPresensi as $dataku)
                                        @php
                                            $disk = Storage::disk('s3');
                                            $leaveDoc = $leaveDocuments
                                                ->where('nis', $dataku->nis)
                                                ->where('type', $dataku->status)
                                                ->first();
                                        @endphp
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td
                                                class="px-2 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900 dark:text-gray-100 mobile-table-data">
                                                {{ $loop->iteration }} .</td>
                                            <td
                                                class="px-2 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900 dark:text-gray-100 mobile-table-data">
                                                {{ $dataku->nis }}</td>
                                            <td
                                                class="px-2 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-900 dark:text-gray-100 mobile-table-data">
                                                {{ $dataku->warga_tels->name }}</td>
                                            <td
                                                class="px-2 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-900 dark:text-gray-100 mobile-table-data">
                                                {{ $dataku->warga_tels->kelas }}</td>
                                            <td
                                                class="px-2 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-900 dark:text-gray-100 mobile-table-data">
                                                {{ $dataku->time_masuk ?? '-' }}</td>
                                            <td class="px-2 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                                @if($dataku->status == 'Hadir')
                                                    <span
                                                        class="px-1.5 sm:px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 mobile-badge">Hadir</span>
                                                @elseif($dataku->status == 'Izin')
                                                    <span
                                                        class="px-1.5 sm:px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 mobile-badge">Izin</span>
                                                @elseif($dataku->status == 'Sakit')
                                                    <span
                                                        class="px-1.5 sm:px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 mobile-badge">Sakit</span>
                                                @elseif($dataku->status == 'Alpa')
                                                    <span
                                                        class="px-1.5 sm:px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 mobile-badge">Alpa</span>
                                                @elseif($dataku->status == 'Terlambat')
                                                    <span
                                                        class="px-1.5 sm:px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 mobile-badge">Terlambat</span>
                                                @endif
                                            </td>
                                            <td
                                                class="px-2 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm font-medium text-gray-900 dark:text-gray-100 mobile-table-data">
                                                {{ $dataku->time_keluar ?? '-' }}</td>
                                            <td class="px-2 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                                @if($dataku->status_keluar == 'Tepat Waktu')
                                                    <span
                                                        class="px-1.5 sm:px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 mobile-badge">Tepat
                                                        Waktu</span>
                                                @elseif($dataku->status_keluar == 'Belum Waktunya')
                                                    <span
                                                        class="px-1.5 sm:px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 mobile-badge">Belum
                                                        Waktunya</span>
                                                @elseif($dataku->status_keluar == 'Terlambat')
                                                    <span
                                                        class="px-1.5 sm:px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 mobile-badge">Terlambat</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            @if($tab === 'izin' || $tab === 'sakit')
                                                <td
                                                    class="px-2 sm:px-6 py-3 sm:py-4 text-xs sm:text-sm text-gray-900 dark:text-gray-100 hidden sm:table-cell mobile-table-data">
                                                    {{ $leaveDoc->reason ?? '-' }}
                                                </td>
                                                <td
                                                    class="px-2 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-xs sm:text-sm text-gray-900 dark:text-gray-100 mobile-table-data">
                                                    @if(isset($leaveDoc) && $leaveDoc->document_path)
                                                        <button
                                                            onclick="showDocument('{{ $disk->temporaryUrl($leaveDoc->document_path, now()->addMinutes(5)) }}')"
                                                            class="text-primary-600 hover:text-primary-900 dark:text-primary-400 dark:hover:text-primary-300">
                                                            <i class="fa-solid fa-file-image mr-1"></i> Lihat
                                                        </button>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-10 sm:py-12 animate-fade-in">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 sm:h-16 sm:w-16 text-gray-400 dark:text-gray-600 animate-pulse-slow"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 13.5V15m-6 4h12a2 2 0 002-2v-12a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h3 class="mt-4 text-base max-md:text-xs font-medium text-gray-900 dark:text-gray-100 mobile-subheading">
                                    @if($dayType === 'Hari Libur')
                                        @if($filter === 'Hari ini')
                                            Hari Ini Libur
                                        @elseif($filter === 'Kemarin')
                                            Kemarin Libur
                                        @else
                                            Libur
                                        @endif
                                    @else
                                        Tidak menemukan data satupun
                                    @endif
                                </h3>
                                <p class="mt-1 text-sm max-md:text-xs text-gray-500 dark:text-gray-400 mobile-text-adjust">
                                    @if($dayType === 'Hari Libur')
                                        Tidak ada kegiatan belajar mengajar pada periode ini.
                                    @else
                                        Tidak ada data absensi yang tersedia untuk periode ini.
                                    @endif
                                </p>
                            </div>
                        </div>
                    @endif

                    @if(count($dataPresensi) > 0)
                        <!-- Pagination -->
                        <div
                            class="bg-white dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
                            <div class="flex-1 flex justify-between sm:hidden">
                                {{-- Tombol Previous Mobile --}}
                                @if ($dataPresensi->onFirstPage())
                                    <span
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700">
                                        Previous
                                    </span>
                                @else
                                    <a href="{{ $dataPresensi->previousPageUrl() }}"
                                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        Previous
                                    </a>
                                @endif
                
                                {{-- Tombol Next Mobile --}}
                                @if ($dataPresensi->hasMorePages())
                                    <a href="{{ $dataPresensi->nextPageUrl() }}"
                                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        Next
                                    </a>
                                @else
                                    <span
                                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700">
                                        Next
                                    </span>
                                @endif
                            </div>
                
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700 dark:text-gray-300">
                                        Showing <span class="font-medium">{{ $dataPresensi->firstItem() }}</span>
                                        to <span class="font-medium">{{ $dataPresensi->lastItem() }}</span>
                                        of <span class="font-medium">{{ $dataPresensi->total() }}</span> results
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                        {{-- Tombol Previous --}}
                                        @if ($dataPresensi->onFirstPage())
                                            <span
                                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @else
                                            <a href="{{ $dataPresensi->previousPageUrl() }}"
                                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        @endif
                
                                        {{-- Nomor Halaman dengan Ellipses --}}
                                        @php
                                            $currentPage = $dataPresensi->currentPage();
                                            $lastPage = $dataPresensi->lastPage();
                                            $start = max($currentPage - 2, 1);
                                            $end = min($currentPage + 2, $lastPage);
                                        @endphp
                
                                        {{-- Halaman pertama --}}
                                        @if ($start > 1)
                                            <a href="{{ $dataPresensi->url(1) }}"
                                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium {{ $currentPage == 1 ? 'bg-primary-500 text-white' : 'bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                                                1
                                            </a>
                                            @if ($start > 2)
                                                <span
                                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">…</span>
                                            @endif
                                        @endif
                
                                        {{-- Halaman di sekitar current --}}
                                        @for ($page = $start; $page <= $end; $page++)
                                            @if ($page == $currentPage)
                                                <span
                                                    class="z-10 bg-primary-50 dark:bg-primary-900 border-primary-500 dark:border-primary-500 text-primary-600 dark:text-primary-200 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                                    {{ $page }}
                                                </span>
                                            @else
                                                <a href="{{ $dataPresensi->url($page) }}"
                                                    class="bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                                    {{ $page }}
                                                </a>
                                            @endif
                                        @endfor
                
                                        {{-- Halaman terakhir --}}
                                        @if ($end < $lastPage)
                                            @if ($end < $lastPage - 1)
                                                <span
                                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">…</span>
                                            @endif
                                            <a href="{{ $dataPresensi->url($lastPage) }}"
                                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium {{ $currentPage == $lastPage ? 'bg-primary-500 text-white' : 'bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                                                {{ $lastPage }}
                                            </a>
                                        @endif
                
                                        {{-- Tombol Next --}}
                                        @if ($dataPresensi->hasMorePages())
                                            <a href="{{ $dataPresensi->nextPageUrl() }}"
                                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        @else
                                            <span
                                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd"
                                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @endif
                                    </nav>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <!-- Show message when no class is selected -->
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 sm:p-8 text-center">
                    <svg class="mx-auto h-12 w-12 sm:h-16 sm:w-16 text-gray-400 dark:text-gray-600 animate-pulse-slow"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M12 13.5V15m-6 4h12a2 2 0 002-2v-12a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-4 text-base sm:text-lg font-medium text-gray-900 dark:text-gray-100 mobile-subheading">Silahkan Pilih Kelas</h3>
                    <p class="mt-1 text-xs sm:text-sm text-gray-500 dark:text-gray-400 mobile-text-adjust">
                        Pilih kelas untuk melihat data absensi siswa
                    </p>
                </div>
            @endif
        </main>
    </div>

    <!-- Custom Date Modal -->
    <div id="custom-date-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75 backdrop-blur-sm"></div>
            </div>

            <!-- Modal container -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal content -->
            <div id="date-modal-content"
                class="inline-block align-bottom rounded-xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95 bg-white dark:bg-gray-800">
                <!-- Modal header -->
                <div class="bg-primary-600 dark:bg-primary-800 px-6 py-4 rounded-t-xl">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg sm:text-xl font-bold text-white" id="modal-title">
                            <i class="fa-solid fa-calendar-days mr-2"></i>Pilih Rentang Tanggal
                        </h3>
                        <button type="button" id="close-custom-date-x"
                            class="text-white hover:text-gray-200 focus:outline-none transition-transform duration-300 hover:rotate-90">
                            <i class="fa-solid fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal body -->
                <div class="px-5 sm:px-6 py-4 sm:py-5 bg-white dark:bg-gray-800">
                    <div class="space-y-5 sm:space-y-6">
                        <!-- Date range selection -->
                        <div class="grid grid-cols-1 gap-5 sm:gap-6 sm:grid-cols-2">
                            <!-- Start date -->
                            <div class="date-input-container">
                                <label for="date-from"
                                    class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 sm:mb-2 mobile-text-adjust">
                                    Tanggal Mulai
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-calendar text-gray-400 dark:text-gray-500"></i>
                                    </div>
                                    <input type="date" name="date-from" id="date-from"
                                        class="date-input block w-full pl-10 pr-10 py-2 sm:py-3 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm transition-all duration-300 ease-in-out hover:border-primary-400 dark:hover:border-primary-500 mobile-text-adjust"
                                        placeholder="DD/MM/YYYY">
                                    <div class="absolute inset-y-0 right-0 flex items-center">
                                        <div class="date-indicator h-2 w-2 rounded-full bg-green-500 mr-3 opacity-0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- End date -->
                            <div class="date-input-container">
                                <label for="date-to"
                                    class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 sm:mb-2 mobile-text-adjust">
                                    Tanggal Akhir
                                </label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fa-solid fa-calendar text-gray-400 dark:text-gray-500"></i>
                                    </div>
                                    <input type="date" name="date-to" id="date-to"
                                        class="date-input block w-full pl-10 pr-10 py-2 sm:py-3 border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm transition-all duration-300 ease-in-out hover:border-primary-400 dark:hover:border-primary-500 mobile-text-adjust"
                                        placeholder="DD/MM/YYYY">
                                    <div class="absolute inset-y-0 right-0 flex items-center">
                                        <div class="date-indicator h-2 w-2 rounded-full bg-green-500 mr-3 opacity-0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Quick date selections -->
                        <div class="mt-3 sm:mt-4">
                            <p class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5 sm:mb-2 mobile-text-adjust">Pilihan Cepat</p>
                            <div class="flex flex-wrap gap-1.5 sm:gap-2">
                                <button type="button"
                                    class="quick-date-btn px-2 sm:px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs hover:bg-primary-100 dark:hover:bg-primary-900 transition-all duration-300 mobile-text-adjust"
                                    data-days="7">7 Hari</button>
                                <button type="button"
                                    class="quick-date-btn px-2 sm:px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs hover:bg-primary-100 dark:hover:bg-primary-900 transition-all duration-300 mobile-text-adjust"
                                    data-days="14">14 Hari</button>
                                <button type="button"
                                    class="quick-date-btn px-2 sm:px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs hover:bg-primary-100 dark:hover:bg-primary-900 transition-all duration-300 mobile-text-adjust"
                                    data-days="30">30 Hari</button>
                                <button type="button"
                                    class="quick-date-btn px-2 sm:px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs hover:bg-primary-100 dark:hover:bg-primary-900 transition-all duration-300 mobile-text-adjust"
                                    data-days="90">90 Hari</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="bg-gray-50 dark:bg-gray-800 px-5 sm:px-6 py-3 sm:py-4 rounded-b-xl">
                    <div class="flex flex-col-reverse sm:flex-row sm:justify-between sm:space-x-2">
                        <button type="button" id="close-custom-date"
                            class="mt-3 sm:mt-0 w-full sm:w-auto inline-flex justify-center items-center px-4 sm:px-6 py-2 sm:py-3 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm sm:text-base font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-650 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300 mobile-text-adjust">
                            <i class="fa-solid fa-times mr-2"></i>Batal
                        </button>
                        <button type="button" id="apply-custom-date"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 sm:px-6 py-2 sm:py-3 border border-transparent rounded-lg shadow-sm text-sm sm:text-base font-medium text-white bg-primary-600 dark:bg-primary-700 hover:bg-primary-500 dark:hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 transition-all duration-300 mobile-text-adjust">
                            <i class="fa-solid fa-check mr-2"></i>Terapkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Viewer Modal -->
    <div id="document-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
            </div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-base sm:text-lg leading-6 font-medium text-gray-900 dark:text-white mobile-subheading"
                                id="document-modal-title">
                                Dokumen Surat
                            </h3>
                            <div class="mt-4 flex justify-center">
                                <img id="document-image" src="/placeholder.svg" alt="Dokumen Surat"
                                    class="max-w-full max-h-[70vh] object-contain">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="close-document-modal"
                        class="w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto mobile-text-adjust">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('Cert.foot')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterButton = document.getElementById('filter-menu-button');
            const filterMenu = document.getElementById('filter-menu');
            const customDateBtn = document.getElementById('custom-date-btn');
            const customDateModal = document.getElementById('custom-date-modal');
            const closeCustomDateBtn = document.getElementById('close-custom-date');
            const applyCustomDateBtn = document.getElementById('apply-custom-date');
            const documentModal = document.getElementById('document-modal');
            const closeDocumentModalBtn = document.getElementById('close-document-modal');
            const documentImage = document.getElementById('document-image');
            const dateFromInput = document.getElementById('date-from');
            const dateToInput = document.getElementById('date-to');

            // Filter menu toggle
            if (filterButton && filterMenu) {
                filterButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    filterMenu.classList.toggle('hidden');
                });

                // Close filter menu when clicking outside
                document.addEventListener('click', function(event) {
                    if (!filterButton.contains(event.target) && !filterMenu.contains(event.target)) {
                        filterMenu.classList.add('hidden');
                    }
                });
            }

            // Date picker functionality
            if (customDateBtn) {
                customDateBtn.addEventListener('click', function() {
                    customDateModal.classList.remove('hidden');
                    document.body.classList.add('modal-active');
                    filterMenu.classList.add('hidden');

                    // Set default dates (today and 7 days from now)
                    const today = new Date();
                    const nextWeek = new Date();
                    nextWeek.setDate(today.getDate() + 7);

                    // Format dates for input
                    const formatDate = (date) => {
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    };

                    dateFromInput.value = formatDate(today);
                    dateToInput.value = formatDate(nextWeek);

                    // Set min date for end date based on start date
                    dateToInput.min = dateFromInput.value;

                    const modalContent = document.getElementById('date-modal-content');
                    setTimeout(() => {
                        modalContent.classList.remove('opacity-0', 'translate-y-4',
                            'sm:translate-y-0', 'sm:scale-95');
                        modalContent.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
                        modalContent.style.transition =
                            'opacity 300ms ease-out, transform 300ms ease-out';
                    }, 10);

                    updateDateInputState();
                });
            }

            if (closeCustomDateBtn) {
                closeCustomDateBtn.addEventListener('click', closeCustomDateModal);
            }

            const closeCustomDateX = document.getElementById('close-custom-date-x');
            if (closeCustomDateX) {
                closeCustomDateX.addEventListener('click', closeCustomDateModal);
            }

            function closeCustomDateModal() {
                const modalContent = document.getElementById('date-modal-content');
                modalContent.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
                modalContent.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');

                setTimeout(() => {
                    customDateModal.classList.add('hidden');
                    document.body.classList.remove('modal-active');
                }, 300);
            }

            if (applyCustomDateBtn) {
                applyCustomDateBtn.addEventListener('click', function() {
                    const dateFrom = dateFromInput.value;
                    const dateTo = dateToInput.value;

                    if (dateFrom && dateTo) {
                        this.classList.add('animate-pulse');
                        setTimeout(() => {
                            window.location.href =
                                `{{ route('check.hari.ini') }}?filter=Custom&date_from=${dateFrom}&date_to=${dateTo}&tab={{ $tab }}&kelas={{ request('kelas') }}`;
                        }, 300);
                    } else {
                        const dateInputs = document.querySelectorAll('.date-input');
                        dateInputs.forEach(input => {
                            if (!input.value) {
                                input.classList.add('border-red-500');
                                input.parentElement.classList.add('animate-shake');
                                setTimeout(() => {
                                    input.parentElement.classList.remove('animate-shake');
                                }, 500);
                            }
                        });
                    }
                });
            }

            const quickDateBtns = document.querySelectorAll('.quick-date-btn');
            quickDateBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    quickDateBtns.forEach(b => b.classList.remove('active'));

                    this.classList.add('active');

                    // Set date range based on button
                    const days = parseInt(this.getAttribute('data-days'));
                    const endDate = new Date();
                    const startDate = new Date();
                    startDate.setDate(endDate.getDate() - days);

                    const formatDate = (date) => {
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        return `${year}-${month}-${day}`;
                    };

                    // Set input values with animation
                    dateFromInput.value = formatDate(startDate);
                    dateToInput.value = formatDate(endDate);

                    // Update min date for end date
                    dateToInput.min = dateFromInput.value;

                    dateFromInput.classList.add('animate-pulse');
                    dateToInput.classList.add('animate-pulse');

                    setTimeout(() => {
                        dateFromInput.classList.remove('animate-pulse');
                        dateToInput.classList.remove('animate-pulse');
                        updateDateInputState();
                    }, 500);
                });
            });

            // Update date input visual state
            function updateDateInputState() {
                const dateInputs = document.querySelectorAll('.date-input');
                dateInputs.forEach(input => {
                    const container = input.closest('.date-input-container');
                    if (input.value) {
                        container.classList.add('date-input-filled');
                        input.classList.remove('border-red-500');
                    } else {
                        container.classList.remove('date-input-filled');
                    }
                });
            }

            if (dateFromInput && dateToInput) {
                dateFromInput.addEventListener('change', function() {
                    // Set minimum date for end date input
                    dateToInput.min = this.value;

                    // If end date is before start date, update it
                    if (dateToInput.value && dateToInput.value < this.value) {
                        dateToInput.value = this.value;
                    }

                    updateDateInputState();
                });

                dateToInput.addEventListener('change', function() {
                    updateDateInputState();
                });

                dateFromInput.addEventListener('focus', function() {
                    this.parentElement.classList.add('animate-glow');
                });

                dateFromInput.addEventListener('blur', function() {
                    this.parentElement.classList.remove('animate-glow');
                });

                dateToInput.addEventListener('focus', function() {
                    this.parentElement.classList.add('animate-glow');
                });

                dateToInput.addEventListener('blur', function() {
                    this.parentElement.classList.remove('animate-glow');
                });
            }

            function showDocument(url) {
                documentImage.src = url;
                documentModal.classList.remove('hidden');
                document.body.classList.add('modal-active');
            }

            if (closeDocumentModalBtn) {
                closeDocumentModalBtn.addEventListener('click', function() {
                    documentModal.classList.add('hidden');
                    document.body.classList.remove('modal-active');
                });
            }

            window.showDocument = showDocument;
        });
    </script>
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
</body>
</html>