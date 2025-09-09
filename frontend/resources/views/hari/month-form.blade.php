<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Jadwal Bulanan</title>
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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    </style>
</head>

<body class="antialiased h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div class="min-h-full">
        @include('Cert.head')

        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 sm:truncate sm:text-3xl">Atur Jadwal Bulanan</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 transition-colors duration-100">
                        Atur jadwal hari produktif, non-produktif, dan libur untuk bulan {{ Carbon\Carbon::createFromDate(null, $bulan, 1)->locale('id')->monthName }} {{ $tahun }}
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('hari.index', ['bulan' => $bulan, 'tahun' => $tahun]) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i class="fa-solid fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                        Form Pengaturan Jadwal
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                        Pilih tipe hari untuk setiap tanggal dalam bulan {{ Carbon\Carbon::createFromDate(null, $bulan, 1)->locale('id')->monthName }} {{ $tahun }}.
                    </p>
                </div>

                <form action="{{ route('hari.save-month') }}" method="POST">
                    @csrf
                    <input type="hidden" name="bulan" value="{{ $bulan }}">
                    <input type="hidden" name="tahun" value="{{ $tahun }}">
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tanggal
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Hari
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                        Tipe Hari
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach ($dates as $dateInfo)
                                    @php
                                        $date = $dateInfo['date'];
                                        $isWeekend = $date->isWeekend();
                                        $dateStr = $dateInfo['date_str'];
                                    @endphp
                                    <tr class="{{ $isWeekend ? 'bg-gray-50 dark:bg-gray-700' : '' }}">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $date->format('d M Y') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                            {{ $date->locale('id')->dayName }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <select name="types[{{ $dateStr }}]" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                                <option value="">-- Pilih Tipe Hari --</option>
                                                <option value="produktif" {{ $dateInfo['type'] == 'produktif' ? 'selected' : '' }}>Hari Produktif</option>
                                                <option value="non_produktif" {{ $dateInfo['type'] == 'non_produktif' ? 'selected' : '' }}>Hari Non-Produktif</option>
                                                <option value="libur" {{ $dateInfo['type'] == 'libur' ? 'selected' : '' }}>Hari Libur</option>
                                            </select>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-4 py-3 bg-gray-50 dark:bg-gray-700 text-right sm:px-6 border-t border-gray-200 dark:border-gray-600">
                        <button type="submit" class="inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            <i class="fa-solid fa-save mr-2"></i>
                            Simpan Jadwal
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-8 bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                        Pengaturan Cepat
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                        Gunakan tombol di bawah untuk mengatur jadwal dengan cepat.
                    </p>
                </div>
                <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Card 1: Atur Hari Produktif -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg flex flex-col h-full">
                        <div class="flex-grow">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-2">Atur Hari Produktif</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Atur semua hari Senin-Jumat sebagai hari produktif (Masuk normal dengan KBM dan mengharuskan presensi).
                            </p>
                        </div>
                        <div class="mt-4">
                            <button type="button" id="setWorkdays" class="w-full inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="fa-solid fa-school mr-2"></i>
                                Atur Hari Produktif
                            </button>
                        </div>
                    </div>
                    
                    <!-- Card 2: Atur Akhir Pekan Penuh -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg flex flex-col h-full">
                        <div class="flex-grow">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-2">Atur Akhir Pekan Penuh</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Atur semua hari Sabtu-Minggu sebagai hari libur.
                            </p>
                        </div>
                        <div class="mt-4">
                            <button type="button" id="setWeekendsFully" class="w-full inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fa-solid fa-umbrella-beach mr-2"></i>
                                Atur Akhir Pekan Penuh
                            </button>
                        </div>
                    </div>
                    
                    <!-- Card 3: Reset Semua -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg flex flex-col h-full">
                        <div class="flex-grow">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-2">Reset Semua</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Kosongkan semua pengaturan hari pada bulan ini.
                            </p>
                        </div>
                        <div class="mt-4">
                            <button type="button" id="resetAll" class="w-full inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                <i class="fa-solid fa-trash mr-2"></i>
                                Reset Semua
                            </button>
                        </div>
                    </div>

                    <!-- Card 4: Atur Hari Non-Produktif -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg flex flex-col h-full">
                        <div class="flex-grow">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-2">Atur Hari Non-Produktif</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Atur semua hari Sabtu sebagai hari non-produktif (Masuk tetapi tidak ada KBM dan tetap mengharuskan presensi).
                            </p>
                        </div>
                        <div class="mt-4">
                            <button type="button" id="setNonWorkdays" class="w-full inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fa-solid fa-calendar-check mr-2"></i>
                                Atur Hari Non-Produktif
                            </button>
                        </div>
                    </div>

                    <!-- Card 2: Atur Akhir Pekan -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg flex flex-col h-full">
                        <div class="flex-grow">
                            <h4 class="text-md font-medium text-gray-900 dark:text-white mb-2">Atur Akhir Pekan</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Atur semua hari Minggu sebagai hari libur.
                            </p>
                        </div>
                        <div class="mt-4">
                            <button type="button" id="setWeekends" class="w-full inline-flex justify-center items-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
                                <i class="fa-solid fa-umbrella-beach mr-2"></i>
                                Atur Akhir Pekan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    @include('Cert.foot')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Set workdays (Monday-Friday) as productive days
            document.getElementById('setWorkdays').addEventListener('click', function() {
                const selects = document.querySelectorAll('select[name^="types"]');
                selects.forEach(select => {
                    const dateStr = select.name.match(/\[(.*?)\]/)[1];
                    const date = new Date(dateStr);
                    const day = date.getDay();
                    
                    // 1-5 is Monday to Friday
                    if (day >= 1 && day <= 5) {
                        select.value = 'produktif';
                    }
                });
            });
            
            // Set weekends fully (Saturday-Sunday) as holidays
            document.getElementById('setWeekendsFully').addEventListener('click', function() {
                const selects = document.querySelectorAll('select[name^="types"]');
                selects.forEach(select => {
                    const dateStr = select.name.match(/\[(.*?)\]/)[1];
                    const date = new Date(dateStr);
                    const day = date.getDay();
                    
                    // 0 is Sunday, 6 is Saturday
                    if (day === 0 || day === 6) {
                        select.value = 'libur';
                    }
                });
            });

            // Set weekends (Sunday) as holidays
            document.getElementById('setWeekends').addEventListener('click', function() {
                const selects = document.querySelectorAll('select[name^="types"]');
                selects.forEach(select => {
                    const dateStr = select.name.match(/\[(.*?)\]/)[1];
                    const date = new Date(dateStr);
                    const day = date.getDay();
                    
                    // 0 is Sunday
                    if (day === 0) {
                        select.value = 'libur';
                    }

                    if (day === 6){
                        select.value = '';
                    }
                });
            });
            
            // Reset all selections
            document.getElementById('resetAll').addEventListener('click', function() {
                const selects = document.querySelectorAll('select[name^="types"]');
                
                selects.forEach(select => {
                    select.value = '';
                });
            });
            
            // Set Saturdays as non-productive days
            document.getElementById('setNonWorkdays').addEventListener('click', function() {
                const selects = document.querySelectorAll('select[name^="types"]');
                selects.forEach(select => {
                    const dateStr = select.name.match(/\[(.*?)\]/)[1];
                    const date = new Date(dateStr);
                    const day = date.getDay();
                    
                    // 6 is Saturday
                    if (day === 6) {
                        select.value = 'non_produktif';
                    }
                });
            });
        });
    </script>
</body>

</html>
