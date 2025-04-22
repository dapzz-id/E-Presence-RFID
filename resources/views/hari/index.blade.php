<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Jadwal Hari</title>
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
                        },
                        dark: {
                            800: '#1e293b',
                            900: '#0f172a'
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
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        /* Modern date picker styles */
        .date-picker-container {
            display: inline-flex;
            align-items: center;
            background-color: #1e293b;
            border-radius: 0.5rem;
            border: 1px solid #0ea5e9;
            padding: 0.5rem 0.75rem;
            width: 220px; /* Reduced width */
            position: relative;
        }
        
        .date-picker-month, .date-picker-year {
            color: white;
            font-weight: 500;
            cursor: pointer;
            position: relative;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
        }
        
        .date-picker-month {
            width: 60%; /* Adjust width ratio */
        }
        
        .date-picker-year {
            width: 40%; /* Adjust width ratio */
        }
        
        .date-picker-month:hover, .date-picker-year:hover {
            color: #38bdf8;
        }
        
        .date-picker-divider {
            width: 1px;
            height: 20px;
            background-color: rgba(255, 255, 255, 0.3);
            margin: 0 0.75rem;
        }
        
        .date-picker-dropdown {
            position: absolute;
            top: calc(100% + 5px);
            left: 0;
            width: 100%;
            background-color: #1e293b;
            border: 1px solid #0ea5e9;
            border-radius: 0.5rem;
            z-index: 50;
            max-height: 250px;
            overflow-y: auto;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            display: none; /* Changed from opacity/visibility to display for better compatibility */
        }
        
        .date-picker-dropdown.show {
            display: block; /* Changed to display block */
        }
        
        .date-picker-option {
            padding: 0.5rem 1rem;
            color: white;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        
        .date-picker-option:hover {
            background-color: rgba(14, 165, 233, 0.2);
        }
        
        .date-picker-option.selected {
            background-color: rgba(14, 165, 233, 0.3);
            font-weight: 500;
        }
        
        .date-picker-icon {
            margin-left: 0.5rem;
            font-size: 0.75rem;
            transition: transform 0.2s ease;
        }
        
        .date-picker-icon.rotate {
            transform: rotate(180deg);
        }
        
        /* Custom scrollbar for dropdowns */
        .date-picker-dropdown::-webkit-scrollbar {
            width: 6px;
        }
        
        .date-picker-dropdown::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }
        
        .date-picker-dropdown::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        
        .date-picker-dropdown::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.5);
        }
        
        /* Position the dropdowns correctly */
        #monthDropdown {
            left: 0;
            width: 65%; /* Slightly wider than the month button */
        }
        
        #yearDropdown {
            right: 0;
            left: auto;
            width: 40%; /* Match the year button width */
        }
    </style>
</head>

<body class="antialiased h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div class="min-h-full">
        @include('Cert.head')

        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 sm:truncate sm:text-3xl">Kelola Jadwal Hari</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 transition-colors duration-100">
                        Atur jadwal hari produktif, non-produktif, dan libur
                    </p>
                </div>
                <div class="mt-4 flex md:mt-0 md:ml-4">
                    <a href="{{ route('hari.month-form', ['bulan' => $bulan, 'tahun' => $tahun]) }}" 
                       class="inline-flex items-center max-md:w-full max-md:justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        <i class="fa-solid fa-calendar-plus mr-2"></i>
                        Atur Jadwal Bulan Ini
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                            Jadwal Bulan {{ Carbon\Carbon::createFromDate(null, $bulan, 1)->locale('id')->monthName }} {{ $tahun }}
                        </h3>
                        <div class="mt-3 sm:mt-0">
                            <form id="filterForm" action="{{ route('hari.index') }}" method="GET">
                                <input type="hidden" id="bulanInput" name="bulan" value="{{ $bulan }}">
                                <input type="hidden" id="tahunInput" name="tahun" value="{{ $tahun }}">
                                
                                <div class="date-picker-container">
                                    <div class="date-picker-month" id="monthPicker">
                                        <span id="selectedMonth">{{ Carbon\Carbon::createFromDate(null, $bulan, 1)->locale('id')->monthName }}</span>
                                        <i class="fa-solid fa-chevron-down date-picker-icon" id="monthIcon"></i>
                                    </div>
                                    
                                    <div class="date-picker-divider"></div>
                                    
                                    <div class="date-picker-year" id="yearPicker">
                                        <span id="selectedYear">{{ $tahun }}</span>
                                        <i class="fa-solid fa-chevron-down date-picker-icon" id="yearIcon"></i>
                                    </div>
                                    
                                    <!-- Month dropdown -->
                                    <div class="date-picker-dropdown" id="monthDropdown">
                                        @for ($m = 1; $m <= 12; $m++)
                                            @php
                                                $monthName = Carbon\Carbon::createFromDate(null, $m, 1)->locale('id')->monthName;
                                            @endphp
                                            <div class="date-picker-option {{ $bulan == $m ? 'selected' : '' }}" 
                                                data-value="{{ $m }}" 
                                                data-name="{{ $monthName }}">
                                                {{ $monthName }}
                                            </div>
                                        @endfor
                                    </div>
                                    
                                    <!-- Year dropdown -->
                                    <div class="date-picker-dropdown" id="yearDropdown">
                                        @for ($y = Carbon\Carbon::now()->year - 1; $y <= Carbon\Carbon::now()->year + 1; $y++)
                                            <div class="date-picker-option {{ $tahun == $y ? 'selected' : '' }}" 
                                                data-value="{{ $y }}">
                                                {{ $y }}
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                                
                                <button type="submit" class="hidden">Filter</button>
                            </form>
                        </div>
                    </div>
                </div>

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
                                    Tipe
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($dates as $dateInfo)
                                @php
                                    $date = $dateInfo['date'];
                                    $isWeekend = $date->isWeekend();
                                @endphp
                                <tr class="{{ $isWeekend ? 'bg-gray-50 dark:bg-gray-700' : '' }}">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $date->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $date->locale('id')->dayName }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($dateInfo['type'] == 'produktif')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                Hari Produktif
                                            </span>
                                        @elseif ($dateInfo['type'] == 'non_produktif')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                Hari Non-Produktif
                                            </span>
                                        @elseif ($dateInfo['type'] == 'libur')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                Hari Libur
                                            </span>
                                        @else
                                            @if ($isWeekend)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200">
                                                    Akhir Pekan
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-gray-600 text-gray-800 dark:text-gray-200">
                                                    Belum Diatur
                                                </span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    @include('Cert.foot')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterForm = document.getElementById('filterForm');
            const bulanInput = document.getElementById('bulanInput');
            const tahunInput = document.getElementById('tahunInput');
            
            const monthPicker = document.getElementById('monthPicker');
            const monthDropdown = document.getElementById('monthDropdown');
            const monthOptions = monthDropdown.querySelectorAll('.date-picker-option');
            const selectedMonth = document.getElementById('selectedMonth');
            const monthIcon = document.getElementById('monthIcon');
            
            const yearPicker = document.getElementById('yearPicker');
            const yearDropdown = document.getElementById('yearDropdown');
            const yearOptions = yearDropdown.querySelectorAll('.date-picker-option');
            const selectedYear = document.getElementById('selectedYear');
            const yearIcon = document.getElementById('yearIcon');
            
            monthPicker.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = monthDropdown.classList.contains('show');
                
                closeAllDropdowns();
                
                if (!isOpen) {
                    monthDropdown.classList.add('show');
                    monthIcon.classList.add('rotate');
                }
            });
            
            yearPicker.addEventListener('click', function(e) {
                e.stopPropagation();
                const isOpen = yearDropdown.classList.contains('show');
                
                closeAllDropdowns();
                
                if (!isOpen) {
                    yearDropdown.classList.add('show');
                    yearIcon.classList.add('rotate');
                }
            });
            
            monthOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    const name = this.getAttribute('data-name');
                    
                    bulanInput.value = value;
                    selectedMonth.textContent = name;
                    
                    monthOptions.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    
                    monthDropdown.classList.remove('show');
                    monthIcon.classList.remove('rotate');
                    
                    filterForm.submit();
                });
            });
            
            yearOptions.forEach(option => {
                option.addEventListener('click', function() {
                    const value = this.getAttribute('data-value');
                    
                    tahunInput.value = value;
                    selectedYear.textContent = value;
                    
                    yearOptions.forEach(opt => opt.classList.remove('selected'));
                    this.classList.add('selected');
                    
                    yearDropdown.classList.remove('show');
                    yearIcon.classList.remove('rotate');
                    
                    filterForm.submit();
                });
            });
            
            document.addEventListener('click', function() {
                closeAllDropdowns();
            });
            
            monthDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
            
            yearDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
            
            function closeAllDropdowns() {
                monthDropdown.classList.remove('show');
                yearDropdown.classList.remove('show');
                monthIcon.classList.remove('rotate');
                yearIcon.classList.remove('rotate');
            }
        });
    </script>
</body>

</html>