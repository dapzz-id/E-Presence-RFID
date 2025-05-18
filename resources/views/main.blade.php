<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Presence | Sistem Absensi Siswa Modern dengan RFID</title>
    
    <!-- Tailwind CSS -->
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
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                },
            },
        }
    </script>
    
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        /* Custom styles */
        html, body {
            overflow-x: hidden;
            width: 100%;
            position: relative;
        }
        
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #1e293b;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #475569;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }

        /* 3D Card Effect */
        .card-3d-container {
            perspective: 1500px;
            transform-style: preserve-3d;
            width: 100%;
            max-width: 500px; /* Slightly longer card as requested */
            margin: 0 auto;
        }

        .card-3d {
            position: relative;
            width: 100%;
            height: 100%;
            transform-style: preserve-3d;
            transition: transform 0.3s ease-out;
        }

        .card-inner {
            position: relative;
            width: 100%;
            height: 100%;
            transform-style: preserve-3d;
        }

        .card-shine {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                135deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.1) 50%,
                rgba(255, 255, 255, 0) 100%
            );
            opacity: 0;
            transition: opacity 0.6s;
            pointer-events: none;
            z-index: 10;
            border-radius: 12px;
        }

        /* RFID Card Animation */
        @keyframes rfidPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(56, 189, 248, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(56, 189, 248, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(56, 189, 248, 0);
            }
        }

        .rfid-pulse {
            animation: rfidPulse 2s infinite;
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(90deg, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-fill-color: transparent;
        }

        /* Floating animation */
        @keyframes float {
            0% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
            100% {
                transform: translateY(0px);
            }
        }

        .floating {
            animation: float 4s ease-in-out infinite;
        }

        /* Barcode animation */
        @keyframes scanline {
            0% {
                transform: translateY(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(100%);
                opacity: 0;
            }
        }

        .barcode-scan {
            position: relative;
            overflow: hidden;
        }

        .barcode-scan::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: rgba(56, 189, 248, 0.5);
            box-shadow: 0 0 10px 3px rgba(56, 189, 248, 0.5);
            animation: scanline 3s ease-in-out infinite;
            opacity: 0;
        }

        /* Hologram effect */
        .hologram {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(
                135deg,
                rgba(255, 255, 255, 0) 0%,
                rgba(255, 255, 255, 0.05) 25%,
                rgba(255, 255, 255, 0.1) 50%,
                rgba(255, 255, 255, 0.05) 75%,
                rgba(255, 255, 255, 0) 100%
            );
            opacity: 0;
            transition: opacity 0.6s;
            z-index: 5;
            border-radius: 12px;
        }

        /* Barcode styling */
        .barcode {
            height: 40px;
            width: 100%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
            border-radius: 4px;
            position: relative;
            overflow: hidden;
        }

        .barcode-lines {
            display: flex;
            width: 100%;
            height: 100%;
        }

        .barcode-line {
            height: 100%;
            width: 1px;
            background-color: #000;
            margin-right: 0.5px;
        }

        /* Responsive fixes */
        @media (max-width: 1280px) {
            .card-3d-container {
                max-width: 480px;
            }
        }

        @media (max-width: 1024px) {
            .card-3d-container {
                max-width: 450px;
                margin-right: 2.5rem
            }
        }

        @media (max-width: 768px) {
            .card-3d-container {
                max-width: 340px;
            }
            
            .barcode {
                height: 35px;
            }
        }

        @media (max-width: 640px) {
            .card-3d-container {
                max-width: 320px;
            }
            
            .barcode {
                height: 30px;
            }
        }

        /* Responsive text sizes */
        .id-card-title {
            font-size: clamp(0.875rem, 1vw + 0.5rem, 1.125rem);
            font-weight: bold;
        }

        .id-card-subtitle {
            font-size: clamp(0.75rem, 0.8vw + 0.4rem, 1rem);
        }

        .id-card-text {
            font-size: clamp(0.7rem, 0.6vw + 0.4rem, 0.875rem);
        }

        .id-card-micro {
            font-size: clamp(0.6rem, 0.5vw + 0.35rem, 0.75rem);
        }

        /* Card display for mobile */
        .card-display-section {
            display: block !important;
        }

        @media (max-width: 768px) {
            .card-display-section {
                margin-top: 2rem;
                margin-bottom: 2rem;
            }
        }
        
        /* Fix for RFID animation overflow */
        .rfid-animation-container {
            position: relative;
            overflow: hidden;
            width: 100%;
        }
        
        .rfid-waves {
            position: absolute;
            top: 50%;
            right: 0;
            transform: translateY(-50%);
            width: auto;
            height: auto;
            z-index: 10;
        }
    </style>
</head>
<body class="bg-gray-900 text-gray-100 font-sans custom-scrollbar">
    <nav class="fixed top-0 left-0 right-0 z-50 bg-gray-900/90 backdrop-blur-md border-b border-gray-800">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('view.main') }}" class="flex items-center cursor-pointer">
                        <i class="bi bi-credit-card-2-front text-primary-400 text-2xl mr-2"></i>
                        <span class="text-xl font-bold text-white">E-PRESENCE</span>
                    </a>
                </div>
                
                <div class="hidden md:flex items-center space-x-8">
                    <a href="#beranda" class="text-gray-300 hover:text-white transition-colors">Beranda</a>
                    <a href="#fitur" class="text-gray-300 hover:text-white transition-colors">Fitur</a>
                    <a href="#tentang" class="text-gray-300 hover:text-white transition-colors">Tentang</a>
                    <a href="#manfaat" class="text-gray-300 hover:text-white transition-colors">Manfaat</a>
                    <a href="#reviews" class="text-gray-300 hover:text-white transition-colors">Ulasan</a>
                </div>
                
                <div class="flex items-center">
                    <a href="#kontak" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors hidden md:block">
                        Hubungi Kami
                    </a>
                    
                    <button type="button" class="md:hidden ml-4 text-gray-300 hover:text-white" id="mobile-menu-button">
                        <i class="bi bi-list text-2xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile menu -->
        <div class="md:hidden hidden bg-gray-800 border-b border-gray-700" id="mobile-menu">
            <div class="px-2 pt-2 pb-3 space-y-1 sm:px-3">
                <a href="#beranda" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700">Beranda</a>
                <a href="#fitur" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700">Fitur</a>
                <a href="#tentang" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700">Tentang</a>
                <a href="#manfaat" class="block px-3 py-2 rounded-md text-base font-medium text-gray-300 hover:text-white hover:bg-gray-700">Manfaat</a>
                <a href="#kontak" class="block px-3 py-2 rounded-md text-base font-medium text-primary-400 hover:text-primary-300">Hubungi Kami</a>
            </div>
        </div>
    </nav>
    
    <section id="beranda" class="pt-24 md:pt-32 pb-16 md:pb-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-primary-900/30 to-transparent"></div>
        <div class="absolute top-0 left-0 right-0 h-px bg-gradient-to-r from-transparent via-primary-500/50 to-transparent"></div>
        
        <!-- Animated background elements -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-primary-500/10 rounded-full filter blur-3xl"></div>
            <div class="absolute bottom-1/3 right-1/3 w-96 h-96 bg-purple-500/10 rounded-full filter blur-3xl"></div>
        </div>
        
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-8 md:gap-12">
                <div class="w-full lg:w-1/2" data-aos="fade-right" data-aos-duration="1000">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-4 md:mb-6 leading-tight">
                        Sistem Absensi <span class="gradient-text">RFID</span> untuk Sekolah Modern
                    </h1>
                    <p class="text-base md:text-lg text-gray-300 mb-6 md:mb-8">
                        E-Presence memudahkan pengelolaan kehadiran siswa dengan teknologi kartu RFID. Pantau, analisis, dan tingkatkan kedisiplinan siswa dengan sistem yang terintegrasi.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('check.hari.ini') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium text-center transition-colors">
                            Cek Data Hari Ini
                        </a>
                    </div>
                </div>
                
                <div class="w-full lg:w-1/2 relative mt-8 lg:mt-0 card-display-section max-lg:!hidden" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <div class="relative">
                        <!-- 3D RFID Card -->
                        <div class="card-3d-container rfid-pulse" id="card-container">
                            <div class="card-3d" id="card">
                                <div class="card-inner">
                                    <div class="card-shine"></div>
                                    <div class="hologram"></div>
                                    <div class="bg-white rounded-xl overflow-hidden shadow-2xl">
                                        <!-- Header section with school info -->
                                        <div class="p-3 md:p-4 bg-white">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 md:w-12 lg:w-16 lg:h-16 md:h-12 mr-2 md:mr-3 flex-shrink-0">
                                                        <img src="{{ asset('src/logotelesandi.png') }}" alt="" loading="lazy" class="w-full h-full object-contain">
                                                    </div>
                                                    <div>
                                                        <p class="text-blue-400 id-card-micro">Sekolah Standar Nasional (SSN)</p>
                                                        <h3 class="text-blue-600 font-bold id-card-title leading-tight">SMK Telekomunikasi</h3>
                                                        <h3 class="text-blue-600 font-bold id-card-title leading-tight">Telesandi Bekasi</h3>
                                                        <p class="text-blue-400 id-card-micro">www.smktelekomunikasitelesandi.sch.id</p>
                                                    </div>
                                                </div>
                                                <!-- Indonesian Flag -->
                                                <div class="w-10 h-6 md:w-12 md:h-8 relative overflow-hidden rounded-sm border border-gray-300 flex-shrink-0">
                                                    <div class="absolute top-0 left-0 right-0 h-1/2 bg-red-600"></div>
                                                    <div class="absolute bottom-0 left-0 right-0 h-1/2 bg-white"></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Main card content with student info -->
                                        <div class="bg-gradient-to-b from-blue-500 to-blue-600 p-3 md:p-4 relative">
                                            <div class="absolute top-0 left-0 right-0 h-8 bg-gradient-to-b from-blue-400 to-transparent opacity-30"></div>
                                            
                                            <div class="flex justify-between">
                                                <div class="w-2/3 pr-2">
                                                    <h4 class="text-white font-bold id-card-title mb-1">Kadavi Raditya Alvino</h4>
                                                    <div class="flex flex-col space-y-1 text-white">
                                                        <p class="id-card-text">NIS 232410012 &nbsp; RPL 2023/2024</p>
                                                        <p class="id-card-micro">Taman Puri Cendana</p>
                                                        <p class="id-card-micro">Jawa Barat, Desa Tridaya Sakti</p>
                                                        <p class="id-card-micro">Kec. Tambun Selatan, Kab. Bekasi</p>
                                                    </div>
                                                    
                                                    <!-- Barcode - Improved version -->
                                                    <div class="mt-2 md:mt-3 barcode barcode-scan">
                                                        <div class="barcode-lines" id="barcode-container">
                                                            <!-- Barcode lines will be generated by JavaScript -->
                                                        </div>
                                                    </div>
                                                    
                                                    <p class="id-card-micro text-white/80 mt-1">Kartu ini berlaku selama menjadi Siswa</p>
                                                </div>
                                                
                                                <!-- Student photo -->
                                                <div class="w-1/3 pl-2 flex-shrink-0">
                                                    <div class="bg-red-500">
                                                        <img src="{{ asset('src/8b98c84e-7b27-4b03-a9bf-963af9b378b7.png') }}" loading="lazy" alt="Student Photo" class="w-full aspect-[3/4] object-cover rounded">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- RFID Animation Elements -->
                        <div class="rfid-animation-container">
                            <div class="absolute -top-4 -right-4 w-12 md:w-16 h-12 md:h-16 bg-primary-500/20 rounded-full filter blur-xl floating"></div>
                            <div class="absolute -bottom-6 -left-6 w-16 md:w-20 h-16 md:h-20 bg-purple-500/20 rounded-full filter blur-xl" style="animation: float 5s ease-in-out infinite;"></div>
                            
                            <!-- RFID Waves Animation -->
                            <div class="rfid-waves">
                                <div class="relative">
                                    <div class="w-5 h-8 md:w-6 md:h-10 bg-primary-600 rounded-l-md flex items-center justify-center">
                                        <i class="bi bi-wifi text-white text-xs md:text-sm"></i>
                                    </div>
                                    <div class="absolute top-1/2 right-5 md:right-6 transform -translate-y-1/2">
                                        <div class="w-3 h-3 md:w-4 md:h-4 border-2 border-primary-400 rounded-full animate-ping opacity-75"></div>
                                    </div>
                                    <div class="absolute top-1/2 right-5 md:right-6 transform -translate-y-1/2">
                                        <div class="w-6 h-6 md:w-8 md:h-8 border-2 border-primary-400 rounded-full animate-ping opacity-50" style="animation-delay: 0.3s"></div>
                                    </div>
                                    <div class="absolute top-1/2 right-5 md:right-6 transform -translate-y-1/2">
                                        <div class="w-9 h-9 md:w-12 md:h-12 border-2 border-primary-400 rounded-full animate-ping opacity-25" style="animation-delay: 0.6s"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="mt-16 md:mt-20 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6 md:gap-8" data-aos="fade-up" data-aos-duration="1000">
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-5 md:p-6">
                    <div class="text-primary-400 text-2xl md:text-3xl font-bold mb-2">Tingkat Akurasi</div>
                    <p class="text-gray-400 text-xs md:text-sm">Sistem absensi dengan tingkat akurasi tinggi menggunakan teknologi RFID pada kartu pelajar. Kartu pelajar hanya untuk satu akun saja untuk sistem keamanan presensi.</p>
                </div>
                
                <div class="bg-gray-800/50 backdrop-blur-sm border border-gray-700 rounded-xl p-5 md:p-6">
                    <div class="text-primary-400 text-2xl md:text-3xl font-bold mb-2">Peningkatan Efisiensi</div>
                    <p class="text-gray-400 text-xs md:text-sm">Sangat menghemat waktu dan sumber daya dengan otomatisasi proses absensi dan pelaporan. Laporan dapat diakses oleh guru maupun siswa itu sendiri.</p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section id="fitur" class="py-16 md:py-20 bg-gray-900 relative">
        <div class="absolute inset-0 bg-gradient-to-b from-gray-900 via-gray-900 to-gray-800"></div>
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-12 md:mb-16" data-aos="fade-up">
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-3 md:mb-4">Fitur Unggulan E-Presence</h2>
                <p class="text-gray-400 text-base md:text-lg">
                    Sistem absensi digital berbasis RFID yang dirancang khusus untuk memenuhi kebutuhan sekolah modern dengan berbagai fitur canggih.
                </p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 md:gap-8">
                <!-- Feature 1 -->
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden" data-aos="fade-up" data-aos-delay="100">
                    <div class="p-5 md:p-6">
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-primary-500/20 rounded-lg flex items-center justify-center mb-4">
                            <i class="bi bi-credit-card-2-front text-primary-400 text-xl md:text-2xl"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-semibold mb-3">Absensi Kartu RFID</h3>
                        <p class="text-gray-400 text-sm md:text-base mb-4">
                            Sistem absensi modern menggunakan kartu pelajar RFID yang dapat dipindai dengan cepat dan akurat.
                        </p>
                        <ul class="space-y-2">
                            <li class="flex items-start">
                                <i class="bi bi-check-circle text-primary-400 mr-2 mt-1"></i>
                                <span class="text-gray-300 text-xs md:text-sm mt-1.5">Kartu RFID terintegrasi dengan kartu pelajar</span>
                            </li>
                            <li class="flex items-start">
                                <i class="bi bi-check-circle text-primary-400 mr-2 mt-1"></i>
                                <span class="text-gray-300 text-xs md:text-sm mt-1.5">Pemindaian cepat kurang dari 1 detik</span>
                            </li>
                            <li class="flex items-start">
                                <i class="bi bi-check-circle text-primary-400 mr-2 mt-1"></i>
                                <span class="text-gray-300 text-xs md:text-sm mt-1.5">Anti-kecurangan dengan teknologi RFID</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Feature 2 -->
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden" data-aos="fade-up" data-aos-delay="200">
                    <div class="p-5 md:p-6">
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-primary-500/20 rounded-lg flex items-center justify-center mb-4">
                            <i class="bi bi-graph-up text-primary-400 text-xl md:text-2xl"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-semibold mb-3">Analisis & Laporan</h3>
                        <p class="text-gray-400 text-sm md:text-base mb-4">
                            Dapatkan wawasan mendalam tentang pola kehadiran siswa melalui analisis data.
                        </p>
                        <ul class="space-y-2">
                            <li class="flex items-start">
                                <i class="bi bi-check-circle text-primary-400 mr-2 mt-1"></i>
                                <span class="text-gray-300 text-xs md:text-sm mt-1.5">Dashboard analitik real-time</span>
                            </li>
                            <li class="flex items-start">
                                <i class="bi bi-check-circle text-primary-400 mr-2 mt-1"></i>
                                <span class="text-gray-300 text-xs md:text-sm mt-1.5">Laporan mingguan dan bulanan otomatis</span>
                            </li>
                            <li class="flex items-start">
                                <i class="bi bi-check-circle text-primary-400 mr-2 mt-1"></i>
                                <span class="text-gray-300 text-xs md:text-sm mt-1.5">Ekspor data dalam format excel maupun chart</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Feature 3 -->
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden" data-aos="fade-up" data-aos-delay="400">
                    <div class="p-5 md:p-6">
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-primary-500/20 rounded-lg flex items-center justify-center mb-4">
                            <i class="bi bi-people text-primary-400 text-xl md:text-2xl"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-semibold mb-3">Manajemen Siswa</h3>
                        <p class="text-gray-400 text-sm md:text-base mb-4">
                            Mengelola data siswa dengan mudah, termasuk profil, kelas, dan riwayat kehadiran.
                        </p>
                        <ul class="space-y-2">
                            <li class="flex items-start">
                                <i class="bi bi-check-circle text-primary-400 mr-2 mt-1"></i>
                                <span class="text-gray-300 text-xs md:text-sm mt-1.5">Database siswa terintegrasi</span>
                            </li>
                            <li class="flex items-start">
                                <i class="bi bi-check-circle text-primary-400 mr-2 mt-1"></i>
                                <span class="text-gray-300 text-xs md:text-sm mt-1.5">Pengelompokan berdasarkan kelas</span>
                            </li>
                            <li class="flex items-start">
                                <i class="bi bi-check-circle text-primary-400 mr-2 mt-1"></i>
                                <span class="text-gray-300 text-xs md:text-sm mt-1.5">Impor/ekspor data siswa via Excel</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <!-- Feature 5 -->
                <div class="bg-gray-800 border border-gray-700 rounded-xl overflow-hidden" data-aos="fade-up" data-aos-delay="500">
                    <div class="p-5 md:p-6">
                        <div class="w-10 h-10 md:w-12 md:h-12 bg-primary-500/20 rounded-lg flex items-center justify-center mb-4">
                            <i class="bi bi-calendar-check text-primary-400 text-xl md:text-2xl"></i>
                        </div>
                        <h3 class="text-lg md:text-xl font-semibold mb-3">Pengajuan Izin/Sakit</h3>
                        <p class="text-gray-400 text-sm md:text-base mb-4">
                            Kelola permintaan izin atau sakit siswa dalam satu platform terpadu.
                        </p>
                        <ul class="space-y-2">
                            <li class="flex items-start">
                                <i class="bi bi-check-circle text-primary-400 mr-2 mt-1"></i>
                                <span class="text-gray-300 text-xs md:text-sm mt-1.5">Sistem pengajuan izin online</span>
                            </li>
                            <li class="flex items-start">
                                <i class="bi bi-check-circle text-primary-400 mr-2 mt-1"></i>
                                <span class="text-gray-300 text-xs md:text-sm mt-1.5">Verifikasi surat dokter dan bukti</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- About Section -->
    <section id="tentang" class="py-16 md:py-20 bg-gray-800 relative">
        <div class="absolute inset-0 bg-gradient-to-b from-gray-800 via-gray-800 to-gray-900"></div>
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-8 md:gap-12">
                <div class="w-full lg:w-1/2" data-aos="fade-right" data-aos-duration="1000">
                    <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-4 md:mb-6">Tentang E-Presence</h2>
                    <p class="text-gray-300 text-sm md:text-base mb-4 md:mb-6">
                        E-Presence adalah sistem absensi digital berbasis RFID yang dikembangkan khusus untuk memenuhi kebutuhan sekolah di era digital. Kami memahami tantangan dalam mengelola kehadiran siswa secara efisien dan akurat.
                    </p>
                    <p class="text-gray-300 text-sm md:text-base mb-4 md:mb-6">
                        Didirikan pada tahun 2020, E-Presence telah berkembang menjadi solusi terdepan dalam manajemen kehadiran siswa dengan teknologi RFID, analisis data, dan integrasi yang mulus dengan sistem pendidikan.
                    </p>
                    
                    <div class="space-y-4 mb-6 md:mb-8">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 md:w-10 md:h-10 bg-primary-500/20 rounded-lg flex items-center justify-center mr-3 md:mr-4">
                                <i class="bi bi-lightbulb text-primary-400 text-sm md:text-base"></i>
                            </div>
                            <div>
                                <h4 class="text-base md:text-lg font-medium text-white mb-1">Visi Kami</h4>
                                <p class="text-gray-400 text-xs md:text-sm">Menjadi pionir dalam transformasi digital sistem pendidikan di Indonesia melalui teknologi absensi yang inovatif.</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-8 h-8 md:w-10 md:h-10 bg-primary-500/20 rounded-lg flex items-center justify-center mr-3 md:mr-4">
                                <i class="bi bi-bullseye text-primary-400 text-sm md:text-base"></i>
                            </div>
                            <div>
                                <h4 class="text-base md:text-lg font-medium text-white mb-1">Misi Kami</h4>
                                <p class="text-gray-400 text-xs md:text-sm">Menyediakan solusi absensi digital yang mudah digunakan, aman, dan dapat diandalkan untuk meningkatkan efisiensi dan akuntabilitas di sekolah.</p>
                            </div>
                        </div>
                    </div>
                    
                    <a href="#kontak" class="inline-flex items-center text-primary-400 hover:text-primary-300 font-medium text-sm md:text-base">
                        Pelajari lebih lanjut tentang kami
                        <i class="bi bi-arrow-right ml-2"></i>
                    </a>
                </div>
                
                <div class="w-full lg:w-1/2 mt-8 lg:mt-0" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <div class="relative">
                        <div class="grid grid-cols-2 gap-3 md:gap-4">
                            <div class="space-y-3 md:space-y-4">
                                <div class="bg-gray-700 rounded-lg overflow-hidden shadow-lg transform rotate-2 hover:rotate-0 transition-transform duration-300">
                                    <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" alt="Students using E-Presence" class="w-full h-32 sm:h-40 md:h-48 object-cover">
                                </div>
                                <div class="bg-gray-700 rounded-lg overflow-hidden shadow-lg transform -rotate-3 hover:rotate-0 transition-transform duration-300">
                                    <img src="https://images.unsplash.com/photo-1571260899304-425eee4c7efc?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" alt="School administration" class="w-full h-40 sm:h-48 md:h-64 object-cover">
                                </div>
                            </div>
                            <div class="space-y-3 md:space-y-4 pt-4 md:pt-8">
                                <div class="bg-gray-700 rounded-lg overflow-hidden shadow-lg transform -rotate-2 hover:rotate-0 transition-transform duration-300">
                                    <img src="https://images.unsplash.com/photo-1516321318423-f06f85e504b3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" alt="Digital technology" class="w-full h-40 sm:h-48 md:h-64 object-cover">
                                </div>
                                <div class="bg-gray-700 rounded-lg overflow-hidden shadow-lg transform rotate-3 hover:rotate-0 transition-transform duration-300">
                                    <img src="https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" alt="Mobile app" class="w-full h-32 sm:h-40 md:h-48 object-cover">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Decorative elements -->
                        <div class="absolute -top-6 -right-6 w-16 md:w-20 h-16 md:h-20 bg-primary-500/20 rounded-full filter blur-xl floating"></div>
                        <div class="absolute -bottom-8 -left-8 w-20 md:w-24 h-20 md:h-24 bg-purple-500/20 rounded-full filter blur-xl" style="animation: float 5s ease-in-out infinite;"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Benefits Section -->
    <section id="manfaat" class="py-16 md:py-20 bg-gray-900 relative">
        <div class="absolute inset-0 bg-gradient-to-b from-gray-900 via-gray-900 to-gray-800"></div>
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-12 md:mb-16" data-aos="fade-up">
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-3 md:mb-4">Manfaat E-Presence</h2>
                <p class="text-gray-400 text-base md:text-lg">
                    Rasakan berbagai keuntungan menggunakan sistem absensi digital E-Presence untuk sekolah Anda.
                </p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                <!-- Benefit 1 -->
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-6" data-aos="fade-up" data-aos-delay="100">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-primary-500/20 rounded-full flex items-center justify-center mb-4">
                        <i class="bi bi-clock text-primary-400 text-xl md:text-2xl"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-semibold mb-3">Hemat Waktu</h3>
                    <p class="text-gray-400 text-sm md:text-base prose prose-justify prose-sm dark:prose-invert">
                        Otomatisasi proses absensi menghemat waktu guru dan staf hingga 85%, memungkinkan mereka fokus pada tugas-tugas penting lainnya.
                    </p>
                </div>
                
                <!-- Benefit 2 -->
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-6" data-aos="fade-up" data-aos-delay="200">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-primary-500/20 rounded-full flex items-center justify-center mb-4">
                        <i class="bi bi-graph-up-arrow text-primary-400 text-xl md:text-2xl"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-semibold mb-3">Tingkatkan Kedisiplinan</h3>
                    <p class="text-gray-400 text-sm md:text-base prose prose-justify prose-sm dark:prose-invert">
                        Sistem laporan kehadiran membantu meningkatkan kedisiplinan siswa.
                    </p>
                </div>
                
                <!-- Benefit 3 -->
                <div class="bg-gray-800 border border-gray-700 rounded-xl p-5 md:p-6" data-aos="fade-up" data-aos-delay="500">
                    <div class="w-10 h-10 md:w-12 md:h-12 bg-primary-500/20 rounded-full flex items-center justify-center mb-4">
                        <i class="bi bi-file-earmark-text text-primary-400 text-xl md:text-2xl"></i>
                    </div>
                    <h3 class="text-lg md:text-xl font-semibold mb-3">Laporan Komprehensif</h3>
                    <p class="text-gray-400 text-sm md:text-base prose prose-justify prose-sm dark:prose-invert">
                        Dapatkan wawasan mendalam tentang pola kehadiran dengan laporan analitik yang dapat disesuaikan untuk pengambilan keputusan berbasis data.
                    </p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Contact Section -->
    <section id="kontak" class="py-16 md:py-20 bg-gray-800 relative">
        <div class="absolute inset-0 bg-gradient-to-b from-gray-800 via-gray-800 to-gray-800"></div>
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto mb-12 md:mb-16" data-aos="fade-up">
                <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-3 md:mb-4">Hubungi Kami</h2>
                <p class="text-gray-400 text-base md:text-lg max-md:hidden">
                    Tertarik untuk mengimplementasikan E-Presence di sekolah/kampus Anda? <br> Hubungi kami untuk konsultasi <u>gratis</u>.
                </p>
                <p class="text-gray-400 text-base md:text-lg max-md:block hidden">
                    Tertarik untuk mengimplementasikan E-Presence di sekolah/kampus Anda? <br><br> Hubungi kami untuk konsultasi <u>gratis</u>.
                </p>
            </div>
            
            <div class="flex flex-col lg:flex-row gap-8 md:gap-12">                
                <div class="w-full lg:w-1/2 mx-auto" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
                    <div class="bg-gray-900 border border-gray-700 rounded-xl p-5 md:p-8 h-full">
                        <h3 class="text-xl md:text-2xl font-semibold mb-4 md:mb-12">Informasi Kontak</h3>
                        
                        <div class="space-y-4 md:space-y-6">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 md:w-12 md:h-12 bg-primary-500/20 rounded-lg flex items-center justify-center mr-3 md:mr-4">
                                    <i class="bi bi-instagram text-primary-400 text-xl md:text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs md:text-sm mt-0.5">
                                        @raadeveloperz <br>
                                        @x.dapzz
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 md:w-12 md:h-12 bg-primary-500/20 rounded-lg flex items-center justify-center mr-3 md:mr-4">
                                    <i class="bi bi-whatsapp text-primary-400 text-xl md:text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs md:text-sm mt-0.5">
                                        (WhatsApp Only) <br>
                                         +62 895-3831-07479
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-start">
                                <div class="flex-shrink-0 w-10 h-10 md:w-12 md:h-12 bg-primary-500/20 rounded-lg flex items-center justify-center mr-3 md:mr-4">
                                    <i class="bi bi-envelope text-primary-400 text-xl md:text-2xl"></i>
                                </div>
                                <div>
                                    <p class="text-gray-400 text-xs md:text-sm mt-0.5">
                                        ditzztechid@gmail.com <br>
                                        raadeveloperz@gmail.com
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section id="reviews" class="py-16 md:py-20 bg-gray-800 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-b from-gray-800 via-gray-900 to-gray-900"></div>
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto mb-10" data-aos="fade-up" data-aos-duration="1000">
            <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-3 md:mb-4 text-white">Ulasan Pelanggan</h2>
            <p class="text-gray-400 text-base md:text-lg">
            Lihat apa kata pelanggan kami tentang raadeveloperz
            </p>
        </div>
        
        <!-- Google Rating Summary -->
        <div class="flex flex-col items-center justify-center mb-10" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
            <div class="flex items-center mb-4">
                <img src="https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_92x30dp.png" 
                    alt="Google" class="h-12 mt-3 mb-2">
            </div>
            
            <button class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-6 rounded-md flex items-center transition-all duration-300" onclick="window.open('https://g.page/r/CZp_ANlt6d-oEBM/review', '_blank')">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
                Tulis Review
            </button>
        </div>
        
        <!-- Reviews Header with Navigation -->
        <div class="flex justify-between items-center mb-6" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="300">
            <h4 class="text-lg font-medium text-white">Google Reviews</h4>
            <div class="flex gap-2">
            <button id="scroll-left" class="bg-gray-800 hover:bg-gray-700 text-white rounded-full p-2 transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <button id="scroll-right" class="bg-gray-800 hover:bg-gray-700 text-white rounded-full p-2 transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
            </div>
        </div>
        
        <!-- Scrollable Container -->
        <div class="overflow-x-auto pb-4 hide-scrollbar" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="400" id="scroll-wrapper">
            <div class="flex gap-5 min-w-max" id="reviews-container">
            <!-- Reviews will be loaded here -->
            </div>
        </div>
        </div>
    </section>
    
    <!-- Include Axios from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    
    <style>
        /* Hide scrollbar but keep functionality */
        .hide-scrollbar {
        -ms-overflow-style: none;  /* IE and Edge */
        scrollbar-width: none;  /* Firefox */
        }
        .hide-scrollbar::-webkit-scrollbar {
        display: none;  /* Chrome, Safari, Opera */
        }
    </style>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            fetchReviews();
            
            document.getElementById('scroll-left').addEventListener('click', function() {
                document.getElementById('scroll-wrapper').scrollBy({
                left: -300,
                behavior: 'smooth'
                });
            });
            
            document.getElementById('scroll-right').addEventListener('click', function() {
                document.getElementById('scroll-wrapper').scrollBy({
                left: 300,
                behavior: 'smooth'
                });
            });
        });
        
        function fetchReviews() {
            axios.get('./get-reviews')
            .then(response => {
                const data = response.data;
                const container = document.getElementById("reviews-container");

                // Cek dulu data.result dan data.result.reviews ada gak
                if (data.result && Array.isArray(data.result.reviews) && data.result.reviews.length > 0) {
                    container.innerHTML = data.result.reviews.slice(0, 8).map((review, index) => `
                        <div class="bg-gray-800 border border-gray-700 rounded-lg shadow-lg flex flex-col w-80 min-w-[320px]" 
                            data-aos="fade-up" data-aos-duration="1000" data-aos-delay="${400 + (index * 100)}">
                            <!-- Header with profile, name, stars -->
                            <div class="p-5">
                                <div class="flex justify-between items-start">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-3 mb-2">
                                    <img src="${review.profile_photo_url}" 
                                        alt="${review.author_name}" 
                                        loading="lazy"
                                        class="w-10 h-10 rounded-full object-cover">
                                    <h4 class="font-medium text-white">${review.author_name}</h4>
                                    </div>
                                    <div class="text-yellow-400 flex">
                                    ${'★'.repeat(review.rating)}${'☆'.repeat(5 - review.rating)}
                                    </div>
                                </div>
                                <span class="text-2xl font-bold text-white">G</span>
                                </div>
                            
                                <!-- Review Content -->
                                <div class="mt-4">
                                <p class="text-gray-300">
                                    ${review.text || '-'}
                                </p>
                                </div>
                            </div>
                            
                            <!-- Footer -->
                            <div class="mt-auto px-5 py-3 border-t border-gray-700">
                                <p class="text-xs text-gray-500">
                                Diposting pada: ${new Date(review.time * 1000).toLocaleDateString('id-ID')}
                                </p>
                            </div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = `
                        <div class="text-center py-10 text-gray-400 w-full">
                            Belum ada ulasan. Jadilah yang pertama memberikan ulasan di Google!
                        </div>`;
                }

            }).catch(err => {
                document.getElementById("reviews-container").innerHTML = `
                    <div class="text-center py-10 text-red-400 w-full">
                    Gagal memuat ulasan. Silakan coba lagi nanti.
                    </div>`;
                console.error("Error:", err);
            });
        }
    </script>
    
    <footer class="py-6 md:py-8 bg-gray-900 border-t border-gray-800">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-500 text-xs md:text-sm">
                &copy; {{ date('Y') }} E-Presence | All rights reserved.
            </p>
        </div>
    </footer>
    
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true,
            duration: 800,
            easing: 'ease-out-cubic',
        });
    </script>
    
    <script>
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
        
        function toggleFAQ(button) {
            const faqItem = button.parentNode;
            const answer = faqItem.querySelector('div');
            const icon = button.querySelector('i');
            
            answer.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }
    </script>

    <script>
        // Generate barcode lines
        const barcodeContainer = document.getElementById('barcode-container');
        if (barcodeContainer) {
            barcodeContainer.innerHTML = '';
            
            const lineCount = window.innerWidth < 640 ? 80 : (window.innerWidth < 1024 ? 100 : 170);
            
            for (let i = 0; i < lineCount; i++) {
                const line = document.createElement('div');
                line.className = 'barcode-line';
                
                const width = Math.random() > 0.7 ? 
                    (Math.random() > 0.5 ? 3 : 2) : 1;
                
                line.style.width = `${width}px`;
                line.style.opacity = Math.random() > 0.2 ? 1 : 0;
                
                barcodeContainer.appendChild(line);
            }
        }

        // 3D Card Effect based on cursor position
        const card = document.getElementById('card');
        const cardContainer = document.getElementById('card-container');
        
        if (card && cardContainer) {
            function handleCardTilt(e) {
                const rect = cardContainer.getBoundingClientRect();
                const x = e.clientX - rect.left; // x position within the element
                const y = e.clientY - rect.top; // y position within the element
                
                // Calculate the position relative to the center of the card
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                
                // Calculate the quadrant of the cursor
                const isLeft = x < centerX;
                const isTop = y < centerY;
                
                let rotateY, rotateX;
                
                if (isLeft && isTop) {
                    // Top-left quadrant: tilt toward bottom-right
                    rotateY = 15; // Tilt right
                    rotateX = -15; // Tilt down
                } else if (isLeft && !isTop) {
                    // Bottom-left quadrant: tilt toward top-right
                    rotateY = 15; // Tilt right
                    rotateX = 15; // Tilt up
                } else if (!isLeft && isTop) {
                    // Top-right quadrant: tilt toward bottom-left
                    rotateY = -15; // Tilt left
                    rotateX = -15; // Tilt down
                } else {
                    // Bottom-right quadrant: tilt toward top-left
                    rotateY = -15; // Tilt left
                    rotateX = 15; // Tilt up
                }
                
                card.style.transform = `rotateY(${rotateY}deg) rotateX(${rotateX}deg)`;
                
                // Update shine effect based on cursor position
                const shine = card.querySelector('.card-shine');
                const hologram = card.querySelector('.hologram');
                
                if (shine && hologram) {
                    shine.style.opacity = '0.2';
                    hologram.style.opacity = '0.2';
                    
                    // Adjust the gradient angle based on cursor position
                    const angle = Math.atan2(y - centerY, x - centerX) * (180 / Math.PI);
                    shine.style.background = `linear-gradient(${angle + 90}deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0) 100%)`;
                    hologram.style.background = `linear-gradient(${angle}deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.1) 50%, rgba(255, 255, 255, 0) 100%)`;
                }
            }
            
            // Reset card position when mouse leaves
            function resetCardPosition() {
                card.style.transform = 'rotateY(0deg) rotateX(0deg)';
                
                const shine = card.querySelector('.card-shine');
                const hologram = card.querySelector('.hologram');
                
                if (shine && hologram) {
                    shine.style.opacity = '0';
                    hologram.style.opacity = '0';
                }
            }
            
            cardContainer.addEventListener('mousemove', handleCardTilt);
            cardContainer.addEventListener('mouseleave', resetCardPosition);
            
            cardContainer.addEventListener('touchmove', function(e) {
                e.preventDefault();
                const touch = e.touches[0];
                const touchEvent = new MouseEvent('mousemove', {
                    clientX: touch.clientX,
                    clientY: touch.clientY
                });
                handleCardTilt(touchEvent);
            });
            
            cardContainer.addEventListener('touchend', resetCardPosition);
        }

        // Responsive adjustments
        function handleResize() {
            // Regenerate barcode on resize for better responsiveness
            const barcodeContainer = document.getElementById('barcode-container');
            if (barcodeContainer) {
                barcodeContainer.innerHTML = '';
                const lineCount = window.innerWidth < 640 ? 80 : 
                         window.innerWidth < 768 ? 100 : 
                         window.innerWidth < 1024 ? 110 : 170;
        
                for (let i = 0; i < lineCount; i++) {
                    const line = document.createElement('div');
                    line.className = 'barcode-line';
                    const width = Math.random() > 0.7 ? (Math.random() > 0.5 ? 3 : 2) : 1;
                    line.style.width = `${width}px`;
                    line.style.opacity = Math.random() > 0.2 ? 1 : 0;
                    barcodeContainer.appendChild(line);
                }
            }
            
            if (window.innerWidth < 768) {
                AOS.init({
                    disable: true
                });
            } else {
                AOS.init({
                    once: true,
                    duration: 800,
                    easing: 'ease-out-cubic',
                    disable: false
                });
            }
            
            // Adjust card display for mobile
            const cardSection = document.querySelector('.card-display-section');
            if (cardSection) {
                if (window.innerWidth < 768) {
                    cardSection.classList.remove('max-md:hidden');
                }
            }
        }
        
        handleResize();
        window.addEventListener('resize', handleResize);
    </script>
</body>
</html>
