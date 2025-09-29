<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Settings - E-Presence</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    }
                }
            }
        }
        
        // Dark mode functionality
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
    <style>
        .grayscale {
            filter: grayscale(100%);
        }
        
        .disabled-section {
            opacity: 0.5;
            pointer-events: none;
        }
        
        .disabled-section input[type="checkbox"]:disabled + div {
            background-color: #e5e7eb !important;
        }
        
        .disabled-section input[type="range"]:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* Fix radio button styling */
        input[type="radio"] {
            appearance: none;
            -webkit-appearance: none;
            width: 1rem;
            height: 1rem;
            border: 2px solid #d1d5db;
            border-radius: 50%;
            background-color: white;
            position: relative;
            cursor: pointer;
        }
        
        input[type="radio"]:checked {
            border-color: #0ea5e9;
            background-color: #0ea5e9;
        }
        
        input[type="radio"]:checked::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background-color: white;
        }
        
        input[type="radio"]:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.3);
        }
        
        /* Dark mode support for radio buttons */
        .dark input[type="radio"] {
            border-color: #6b7280;
            background-color: #374151;
        }
        
        .dark input[type="radio"]:checked {
            border-color: #0ea5e9;
            background-color: #0ea5e9;
        }
        
        .dark input[type="radio"]:checked::before {
            background-color: white;
        }
    </style>
</head>

<body class="antialiased h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div class="min-h-full">
        @include('Cert.head')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-cog mr-3 text-primary-600"></i>Pengaturan Sistem
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Kelola pengaturan absensi dan Face ID untuk sistem E-Presence
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Settings Form -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700">
                        <h2 class="text-xl font-semibold text-white">
                            <i class="fas fa-sliders-h mr-2"></i>Pengaturan Absensi
                        </h2>
                    </div>
                    
                    <form action="{{ route('settings.update') }}" method="POST" class="p-6 space-y-6">
                        @csrf
                        
                        <!-- Attendance Method -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Metode Absensi
                            </label>
                            <div class="space-y-3">
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="attendance_method" value="rfid" 
                                           {{ ($settings->attendance_method ?? 'rfid') == 'rfid' ? 'checked' : '' }}
                                           id="method_rfid">
                                    <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                        <i class="fas fa-credit-card mr-2 text-blue-500"></i>RFID Only
                                    </span>
                                </label>
                                <label class="flex items-center cursor-pointer">
                                    <input type="radio" name="attendance_method" value="face_id" 
                                           {{ ($settings->attendance_method ?? 'rfid') == 'face_id' ? 'checked' : '' }}
                                           id="method_face_id">
                                    <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">
                                        <i class="fas fa-user-circle mr-2 text-green-500"></i>Face ID Only
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Face Recognition Settings -->
                        <div class="border-t pt-6" id="faceIdSettings">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                                <i class="fas fa-face-smile mr-2 text-yellow-500"></i>Pengaturan Face ID
                            </h3>
                            
                            <div class="space-y-4" id="faceIdControls">
                                <!-- Enable Face Recognition -->
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Aktifkan Face Recognition
                                        </label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Menggunakan teknologi pengenalan wajah untuk absensi
                                        </p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="face_recognition_enabled" value="1" 
                                               {{ ($settings->face_recognition_enabled ?? false) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                                    </label>
                                </div>

                                <!-- Anti-Spoofing -->
                                <div class="flex items-center justify-between">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                            Anti-Spoofing Protection
                                        </label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            Mencegah penggunaan foto atau video untuk bypass sistem
                                        </p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="anti_spoofing_enabled" value="1" 
                                               {{ ($settings->anti_spoofing_enabled ?? true) ? 'checked' : '' }}
                                               class="sr-only peer">
                                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary-300 dark:peer-focus:ring-primary-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                                    </label>
                                </div>

                                <!-- Confidence Threshold -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Tingkat Kepercayaan Face ID
                                    </label>
                                    <div class="flex items-center space-x-4">
                                        <input type="range" name="face_confidence_threshold" 
                                               min="0.1" max="1.0" step="0.05" 
                                               value="{{ $settings->face_confidence_threshold ?? 0.70 }}"
                                               class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer dark:bg-gray-700"
                                               id="confidenceRange">
                                        <span id="confidenceValue" class="text-sm font-medium text-gray-700 dark:text-gray-300 min-w-[60px]">
                                            {{ number_format(($settings->face_confidence_threshold ?? 0.70) * 100, 0) }}%
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Semakin tinggi nilai, semakin ketat verifikasi wajah
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Save Button -->
                        <div class="flex justify-end pt-6 border-t">
                            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-medium py-2 px-6 rounded-lg transition duration-200 flex items-center">
                                <i class="fas fa-save mr-2"></i>Simpan Pengaturan
                            </button>
                        </div>
                        
                        <!-- Info about menu changes -->
                        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg" id="menuInfoBox">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5"></i>
                                <div class="text-sm text-blue-800">
                                    <p class="font-medium mb-1">Informasi Pengaturan:</p>
                                    <div id="settingsInfo">
                                        <ul class="space-y-1 text-xs">
                                            <li>• <strong>RFID Only:</strong> Menu "Daftarkan Kartu" akan muncul, pengaturan Face ID dinonaktifkan</li>
                                            <li>• <strong>Face ID Only:</strong> Menu "Daftarkan Wajah" akan muncul, Face Recognition otomatis aktif</li>
                                        </ul>
                                    </div>
                                    <p class="text-xs mt-2 italic">Menu sidebar dan pengaturan akan berubah otomatis setelah disimpan.</p>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Face ID Status & Actions -->
            <div class="space-y-6">
                <!-- Face ID Status -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700">
                        <h3 class="text-lg font-semibold text-white">
                            <i class="fas fa-user-check mr-2"></i>Status Face ID
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        @if($userFaceRegistration)
                            @if($userFaceRegistration->status == 'approved')
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-check-circle text-2xl text-green-600"></i>
                                    </div>
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">Terdaftar</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Face ID Anda sudah aktif dan dapat digunakan untuk absensi
                                    </p>
                                    <div class="mt-4 space-y-2">
                                        <a href="{{ route('settings.face-id') }}" class="block w-full bg-primary-600 hover:bg-primary-700 text-white text-center py-2 px-4 rounded-lg transition duration-200">
                                            <i class="fas fa-camera mr-2"></i>Test Face ID
                                        </a>
                                        <a href="{{ route('settings.register-face') }}" class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center py-2 px-4 rounded-lg transition duration-200">
                                            <i class="fas fa-sync-alt mr-2"></i>Daftar Ulang
                                        </a>
                                    </div>
                                </div>
                            @elseif($userFaceRegistration->status == 'pending')
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-clock text-2xl text-yellow-600"></i>
                                    </div>
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">Menunggu Persetujuan</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Registrasi Face ID Anda sedang diproses oleh admin
                                    </p>
                                </div>
                            @else
                                <div class="text-center">
                                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-times-circle text-2xl text-red-600"></i>
                                    </div>
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">Ditolak</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Registrasi Face ID ditolak. Silakan daftar ulang.
                                    </p>
                                    <div class="mt-4">
                                        <a href="{{ route('settings.register-face') }}" class="block w-full bg-primary-600 hover:bg-primary-700 text-white text-center py-2 px-4 rounded-lg transition duration-200">
                                            <i class="fas fa-redo mr-2"></i>Daftar Ulang
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-user-plus text-2xl text-gray-600"></i>
                                </div>
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white">Belum Terdaftar</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Daftarkan wajah Anda untuk menggunakan Face ID
                                </p>
                                <div class="mt-4">
                                    <a href="{{ route('settings.register-face') }}" class="block w-full bg-primary-600 hover:bg-primary-700 text-white text-center py-2 px-4 rounded-lg transition duration-200">
                                        <i class="fas fa-user-plus mr-2"></i>Daftar Face ID
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Current Settings Info -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700">
                        <h3 class="text-lg font-semibold text-white">
                            <i class="fas fa-info-circle mr-2"></i>Pengaturan Aktif
                        </h3>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Metode Absensi</span>
                            @php
                                $methodLabels = [
                                    'rfid' => 'RFID Only',
                                    'face_id' => 'Face ID Only'
                                ];
                                $methodColors = [
                                    'rfid' => 'text-blue-600',
                                    'face_id' => 'text-purple-600'
                                ];
                                $methodIcons = [
                                    'rfid' => 'fas fa-credit-card',
                                    'face_id' => 'fas fa-user-circle'
                                ];
                                $currentMethod = $settings->attendance_method ?? 'rfid';
                            @endphp
                            <span class="text-sm font-medium {{ $methodColors[$currentMethod] }}">
                                <i class="{{ $methodIcons[$currentMethod] }} mr-1"></i>{{ $methodLabels[$currentMethod] }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Face Recognition</span>
                            <span class="text-sm font-medium {{ ($settings->face_recognition_enabled ?? false) ? 'text-green-600' : 'text-red-600' }}">
                                <i class="fas {{ ($settings->face_recognition_enabled ?? false) ? 'fa-check-circle' : 'fa-times-circle' }} mr-1"></i>{{ ($settings->face_recognition_enabled ?? false) ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Anti-Spoofing</span>
                            <span class="text-sm font-medium {{ ($settings->anti_spoofing_enabled ?? true) ? 'text-green-600' : 'text-red-600' }}">
                                <i class="fas {{ ($settings->anti_spoofing_enabled ?? true) ? 'fa-shield-alt' : 'fa-shield' }} mr-1"></i>{{ ($settings->anti_spoofing_enabled ?? true) ? 'Protected' : 'Disabled' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Confidence Level</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ number_format(($settings->face_confidence_threshold ?? 0.70) * 100, 0) }}%
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Version</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">1.0 | 2025</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const confidenceRange = document.getElementById('confidenceRange');
    const confidenceValue = document.getElementById('confidenceValue');
    
    confidenceRange.addEventListener('input', function() {
        const value = Math.round(this.value * 100);
        confidenceValue.textContent = value + '%';
    });
    
    // Menu preview functionality
    const attendanceMethodRadios = document.querySelectorAll('input[name="attendance_method"]');
    const menuInfo = document.querySelector('.mt-4.p-4.bg-blue-50');
    
    attendanceMethodRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            updateMenuPreview(this.value);
            toggleFaceIdSettings(this.value);
        });
        
        // Also add click event for better compatibility
        radio.addEventListener('click', function() {
            // Small delay to ensure radio state is updated
            setTimeout(() => {
                updateMenuPreview(this.value);
                toggleFaceIdSettings(this.value);
            }, 10);
        });
    });
    
    function updateMenuPreview(method) {
        let menuText = '';
        let settingsText = '';
        let bgColor = 'bg-blue-50 border-blue-200';
        let textColor = 'text-blue-800';
        let iconColor = 'text-blue-500';
        
        switch(method) {
            case 'rfid':
                menuText = '<strong>Menu Aktif:</strong> "Daftarkan Kartu" <i class="fas fa-credit-card ml-1"></i>';
                settingsText = '<strong>Face ID:</strong> Pengaturan dinonaktifkan dan tidak akan disimpan';
                bgColor = 'bg-green-50 border-green-200';
                textColor = 'text-green-800';
                iconColor = 'text-green-500';
                break;
            case 'face_id':
                menuText = '<strong>Menu Aktif:</strong> "Daftarkan Wajah" <i class="fas fa-user-plus ml-1"></i>';
                settingsText = '<strong>Face ID:</strong> Face Recognition otomatis diaktifkan';
                bgColor = 'bg-purple-50 border-purple-200';
                textColor = 'text-purple-800';
                iconColor = 'text-purple-500';
                break;
        }
        
        menuInfo.className = `mt-4 p-4 ${bgColor} border rounded-lg`;
        menuInfo.innerHTML = `
            <div class="flex items-start">
                <i class="fas fa-info-circle ${iconColor} mr-3 mt-0.5"></i>
                <div class="text-sm ${textColor}">
                    <p class="font-medium mb-1">${menuText}</p>
                    <p class="font-medium mb-2">${settingsText}</p>
                    <p class="text-xs italic">Menu sidebar dan pengaturan akan berubah otomatis setelah disimpan.</p>
                </div>
            </div>
        `;
    }
    
    // Function to toggle Face ID settings visibility
    function toggleFaceIdSettings(method) {
        const faceIdSettings = document.getElementById('faceIdSettings');
        const faceIdControls = document.getElementById('faceIdControls');
        const faceRecognitionCheckbox = document.querySelector('input[name="face_recognition_enabled"]');
        const antiSpoofingCheckbox = document.querySelector('input[name="anti_spoofing_enabled"]');
        const confidenceRange = document.getElementById('confidenceRange');
        
        if (method === 'rfid') {
            // Disable Face ID settings for RFID only
            faceIdSettings.style.opacity = '0.5';
            faceIdControls.style.pointerEvents = 'none';
            
            // Disable Face ID settings
            faceRecognitionCheckbox.checked = false;
            faceRecognitionCheckbox.disabled = true;
            antiSpoofingCheckbox.disabled = true;
            confidenceRange.disabled = true;
            
            // Add disabled styling
            faceIdSettings.classList.add('grayscale');
        } else if (method === 'face_id') {
            // Enable Face ID settings for face_id only
            faceIdSettings.style.opacity = '1';
            faceIdControls.style.pointerEvents = 'auto';
            
            // Enable Face ID settings
            faceRecognitionCheckbox.disabled = false;
            antiSpoofingCheckbox.disabled = false;
            confidenceRange.disabled = false;
            
            // Auto-enable Face Recognition for face_id method
            faceRecognitionCheckbox.checked = true;
            
            // Remove disabled styling
            faceIdSettings.classList.remove('grayscale');
        }
    }
    
    // Initialize with current selection
    const checkedRadio = document.querySelector('input[name="attendance_method"]:checked');
    
    if (checkedRadio) {
        updateMenuPreview(checkedRadio.value);
        toggleFaceIdSettings(checkedRadio.value);
    } else {
        // Default to RFID if no selection
        updateMenuPreview('rfid');
        toggleFaceIdSettings('rfid');
    }
});
</script>
    </div>
</body>
</html>
