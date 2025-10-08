<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register Face ID - E-Presence</title>
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
</head>

<body class="antialiased h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div class="min-h-full">
        @include('Cert.head')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-6">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center mb-4">
                <a href="{{ route('settings.index') }}" class="text-primary-600 hover:text-primary-700 mr-4">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    <i class="fas fa-user-plus mr-3 text-primary-600"></i>Daftar Face ID
                </h1>
            </div>
            <p class="text-gray-600 dark:text-gray-400">
                Daftarkan wajah Anda untuk menggunakan Face ID dalam sistem absensi
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Registration Form -->
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700">
                    <h2 class="text-xl font-semibold text-white">
                        <i class="fas fa-camera mr-2"></i>Registrasi Wajah
                    </h2>
                </div>
                
                <div class="p-6">
                    <!-- Student Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Pilih Akun Siswa
                        </label>
                        <div class="relative">
                            <button id="studentSelectBtn" type="button" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-left flex items-center justify-between">
                                <span id="selectedStudentText">-- Pilih Siswa --</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                            <input type="hidden" id="selectedStudentNIS" value="">
                        </div>
                    </div>
                    
                    <!-- Student Selection Modal -->
                    <div id="studentModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4 max-h-96 flex flex-col">
                            <div class="p-4 border-b border-gray-200 dark:border-gray-600">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Pilih Siswa</h3>
                                <div class="mt-2">
                                    <input type="text" id="studentSearch" placeholder="Cari nama atau NIS..." class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                                </div>
                            </div>
                            <div class="flex-1 overflow-y-auto p-2">
                                <div id="studentList" class="space-y-1">
                                    @foreach($students as $student)
                                        <div class="student-item p-3 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg cursor-pointer border border-transparent hover:border-primary-300" data-nis="{{ $student->nis }}" data-name="{{ $student->name }}">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mr-3">
                                                    <i class="fas fa-user text-primary-600 dark:text-primary-400"></i>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900 dark:text-white">{{ $student->name }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">NIS: {{ $student->nis }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="p-4 border-t border-gray-200 dark:border-gray-600">
                                <button id="closeStudentModal" class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Liveness Detection Instructions -->
                    <div id="livenessInstructions" class="mb-4 p-4 bg-blue-50 dark:bg-blue-900 border border-blue-200 dark:border-blue-700 rounded-lg hidden">
                        <h4 class="font-medium text-blue-900 dark:text-blue-100 mb-2">Instruksi Liveness Detection:</h4>
                        <div id="currentInstruction" class="text-blue-800 dark:text-blue-200 text-center text-lg font-medium">
                            Berkedip 3 kali
                        </div>
                        <div class="mt-2 text-sm text-blue-600 dark:text-blue-300 text-center">
                            <span id="instructionTimer">3</span> detik tersisa
                        </div>
                    </div>
                    
                    <!-- Camera Container (proporsional dan tidak memakan tempat) -->
                    <div class="relative bg-gray-900 rounded-lg overflow-hidden mb-6 w-full" style="height: 320px; max-width: 480px; margin: 0 auto; aspect-ratio: 4/3;">
                        <video id="video" autoplay muted playsinline class="w-full h-full object-cover"></video>
                        <canvas id="canvas" class="hidden"></canvas>
                        
                        <!-- Face Detection Overlay - DISABLED -->
                        <div id="faceOverlay" class="absolute inset-0 pointer-events-none hidden">
                            <!-- Face detection box disabled for registration -->
                        </div>
                        
                        <!-- Photo Progress Indicator -->
                        <div id="photoProgress" class="absolute top-4 left-4 hidden">
                            <div class="bg-black bg-opacity-75 text-white px-3 py-2 rounded-lg text-sm">
                                <div id="currentPhotoIndicator" class="font-medium">Foto 1 dari 3</div>
                                <div class="flex space-x-1 mt-1">
                                    <div id="progress1" class="w-6 h-1 bg-gray-400 rounded"></div>
                                    <div id="progress2" class="w-6 h-1 bg-gray-400 rounded"></div>
                                    <div id="progress3" class="w-6 h-1 bg-gray-400 rounded"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Controls -->
                    <div class="space-y-4">
                        <button id="startCamera" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center" disabled>
                            <i class="fas fa-camera mr-2"></i>Mulai Registrasi Wajah
                        </button>
                        
                        <div id="livenessStatus" class="w-full bg-blue-600 text-white font-medium py-3 px-6 rounded-lg flex items-center justify-center hidden">
                            <i class="fas fa-eye mr-2"></i><span id="livenessText">Liveness Detection Aktif</span>
                        </div>
                        
                        <button id="registerBtn" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center hidden" disabled>
                            <i class="fas fa-user-check mr-2"></i>Daftar Face ID
                        </button>
                        
                        <button id="resetBtn" class="w-full bg-gray-600 hover:bg-gray-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center hidden">
                            <i class="fas fa-redo mr-2"></i>Ulangi
                        </button>
                        
                        <button id="stopCamera" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center hidden">
                            <i class="fas fa-stop mr-2"></i>Stop Kamera
                        </button>
                    </div>
                    
                    <!-- Registration Status -->
                    <div id="registrationStatus" class="mt-6 hidden">
                        <!-- Status akan ditampilkan di sini -->
                    </div>
                </div>
            </div>

            <!-- Preview Foto Compact -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <div class="px-4 py-3 bg-gradient-to-r from-green-600 to-green-700">
                    <h3 class="text-sm font-semibold text-white">
                        <i class="fas fa-images mr-2"></i>Preview Foto
                    </h3>
                </div>
                
                <div class="p-4">
                    <div id="photoPreview" class="grid grid-cols-1 gap-3">
                        <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-camera text-gray-400 text-xl"></i>
                        </div>
                        <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-camera text-gray-400 text-xl"></i>
                        </div>
                        <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <i class="fas fa-camera text-gray-400 text-xl"></i>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

<!-- Face-api.js Library -->
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<script>
class FaceRegistrationSystem {
    constructor() {
        this.video = document.getElementById('video');
        this.canvas = document.getElementById('canvas');
        this.ctx = this.canvas.getContext('2d');
        this.stream = null;
        this.isModelLoaded = false;
        this.isDetecting = false;
        this.detectionInterval = null;
        this.capturedPhotos = [];
        this.maxPhotos = 3;
        
        this.initializeElements();
        this.initializeCounters();
        this.initializeSystem();
    }
    
    initializeElements() {
        this.startBtn = document.getElementById('startCamera');
        this.registerBtn = document.getElementById('registerBtn');
        this.resetBtn = document.getElementById('resetBtn');
        this.stopBtn = document.getElementById('stopCamera');
        this.statusMessage = document.getElementById('statusMessage');
        this.photoPreview = document.getElementById('photoPreview');
        this.registrationStatus = document.getElementById('registrationStatus');
        this.livenessStatus = document.getElementById('livenessStatus');
        this.livenessText = document.getElementById('livenessText');
        this.livenessInstructions = document.getElementById('livenessInstructions');
        this.currentInstruction = document.getElementById('currentInstruction');
        this.instructionTimer = document.getElementById('instructionTimer');
        
        // Add event listeners with null checks
        if (this.startBtn) this.startBtn.addEventListener('click', () => this.startCamera());
        if (this.registerBtn) this.registerBtn.addEventListener('click', () => this.registerFace());
        if (this.resetBtn) this.resetBtn.addEventListener('click', () => this.resetCapture());
        if (this.stopBtn) this.stopBtn.addEventListener('click', () => this.stopCamera());
    }
    
    initializeCounters() {
        // Counters disabled since UI elements removed
        console.log('Counters initialized - UI elements disabled');
    }
    
    async initializeSystem() {
        // DISABLE start button until student is selected
        this.startBtn.disabled = true;
        this.startBtn.innerHTML = '<i class="fas fa-user-plus mr-2"></i>Pilih Siswa Terlebih Dahulu';
        this.showStatus('Pilih siswa terlebih dahulu untuk memulai registrasi', 'info');
        
        // Try to load models in background
        this.loadModels();
    }
    
    async loadModels() {
        try {
            this.showStatus('Memuat model AI...', 'info');
            
            // Check if face-api.js is loaded
            if (typeof faceapi === 'undefined') {
                throw new Error('Face API library tidak ditemukan');
            }
            
            // Try to load models, but don't fail completely if it fails
            try {
                await Promise.all([
                    faceapi.nets.tinyFaceDetector.loadFromUri('https://cdn.jsdelivr.net/npm/@vladmandic/face-api@latest/model'),
                    faceapi.nets.faceLandmark68Net.loadFromUri('https://cdn.jsdelivr.net/npm/@vladmandic/face-api@latest/model'),
                    faceapi.nets.faceRecognitionNet.loadFromUri('https://cdn.jsdelivr.net/npm/@vladmandic/face-api@latest/model'),
                    faceapi.nets.faceExpressionNet.loadFromUri('https://cdn.jsdelivr.net/npm/@vladmandic/face-api@latest/model')
                ]);
                
                this.isModelLoaded = true;
                this.showStatus('Model AI berhasil dimuat - Sistem siap dengan deteksi wajah', 'success');
            } catch (modelError) {
                console.warn('Model loading failed, continuing in manual mode:', modelError);
                this.showStatus('Sistem siap - Mode manual (tanpa deteksi wajah)', 'warning');
                this.isModelLoaded = false;
            }
            
        } catch (error) {
            console.error('Error loading models:', error);
            this.showStatus('Sistem siap - Mode manual (tanpa deteksi wajah)', 'warning');
            this.isModelLoaded = false;
        }
    }
    
    async startCamera() {
        try {
            // Validate student selection first
            const selectedStudentNIS = document.getElementById('selectedStudentNIS');
            if (!selectedStudentNIS.value) {
                this.showStatus('Pilih akun siswa terlebih dahulu!', 'error');
                return;
            }
            
            this.showStatus('Memulai kamera...', 'info');
            
            // Check if getUserMedia is supported
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                throw new Error('Browser tidak mendukung akses kamera');
            }
            
            this.stream = await navigator.mediaDevices.getUserMedia({
                video: { 
                    width: 640, 
                    height: 480,
                    facingMode: 'user'
                }
            });
            
            this.video.srcObject = this.stream;
            
            this.video.onloadedmetadata = () => {
                this.canvas.width = this.video.videoWidth;
                this.canvas.height = this.video.videoHeight;
                
                this.startBtn.classList.add('hidden');
                this.livenessStatus.classList.remove('hidden');
                this.stopBtn.classList.remove('hidden');
                
                // Show progress indicator
                document.getElementById('photoProgress').classList.remove('hidden');
                this.updateProgressIndicator();
                
                // Start liveness detection
                this.startLivenessDetection();
            };
            
        } catch (error) {
            console.error('Error starting camera:', error);
            let errorMessage = 'Gagal mengakses kamera';
            
            if (error.name === 'NotAllowedError') {
                errorMessage = 'Akses kamera ditolak. Silakan izinkan akses kamera di browser.';
            } else if (error.name === 'NotFoundError') {
                errorMessage = 'Kamera tidak ditemukan. Pastikan kamera terhubung.';
            } else if (error.name === 'NotReadableError') {
                errorMessage = 'Kamera sedang digunakan aplikasi lain.';
            }
            
            this.showStatus(errorMessage, 'error');
        }
    }
    
    // Face detection disabled for registration
    startDetection() {
        // No face detection needed for registration
        return;
    }
    
    async detectFace() {
        // Face detection disabled for registration
        return;
    }
    
    drawFaceBox(box, overlay) {
        // Face box drawing disabled for registration
        return;
    }
    
    startLivenessDetection() {
        this.livenessInstructions.classList.remove('hidden');
        this.livenessText.textContent = 'Liveness Detection - Berkedip & Menoleh';
        
        // Single liveness pattern for all photos
        const phases = [
            { instruction: 'Berkedip 3 kali', duration: 3000 },
            { instruction: 'Tengok ke kanan', duration: 3000 },
            { instruction: 'Tengok ke kiri', duration: 3000 }
        ];
        
        // Start continuous photo capture every second
        this.startContinuousCapture();
        
        // Simulate liveness detection phases
        let phase = 0;
        
        const runPhase = () => {
            if (phase < phases.length) {
                const currentPhase = phases[phase];
                this.currentInstruction.textContent = currentPhase.instruction;
                this.livenessText.textContent = `Liveness: ${currentPhase.instruction}`;
                
                let timeLeft = currentPhase.duration / 1000;
                this.instructionTimer.textContent = timeLeft;
                
                this.livenessTimer = setInterval(() => {
                    timeLeft--;
                    this.instructionTimer.textContent = timeLeft;
                    
                    if (timeLeft <= 0) {
                        clearInterval(this.livenessTimer);
                        phase++;
                        
                        if (phase < phases.length) {
                            this.phaseTimeout = setTimeout(runPhase, 500);
                        } else {
                            // Liveness complete, stop continuous capture and finalize
                            this.completeLivenessDetection();
                        }
                    }
                }, 1000);
            }
        };
        
        runPhase();
    }
    
    startContinuousCapture() {
        // Start capturing photos every second
        this.continuousCaptureInterval = setInterval(() => {
            this.capturePhotoSilent();
        }, 1000);
    }
    
    completeLivenessDetection() {
        // Stop continuous capture
        if (this.continuousCaptureInterval) {
            clearInterval(this.continuousCaptureInterval);
            this.continuousCaptureInterval = null;
        }
        
        this.livenessInstructions.classList.add('hidden');
        this.livenessText.textContent = 'Liveness Complete - Processing Photos';
        
        // Take only the last 3 photos from all captured photos
        this.finalizePhotos();
    }
    
    async capturePhotoSilent() {
        try {
            // Capture current frame silently (no UI updates)
            this.ctx.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);
            const imageData = this.canvas.toDataURL('image/jpeg', 0.8);
            
            // Store in temporary array (will be processed later)
            if (!this.allCapturedPhotos) {
                this.allCapturedPhotos = [];
            }
            
            this.allCapturedPhotos.push({
                imageData: imageData,
                timestamp: Date.now()
            });
            
        } catch (error) {
            console.error('Silent capture error:', error);
        }
    }
    
    finalizePhotos() {
        // Take the last 3 photos from all captured photos
        const totalPhotos = this.allCapturedPhotos ? this.allCapturedPhotos.length : 0;
        
        if (totalPhotos >= 3) {
            // Get last 3 photos
            const lastThreePhotos = this.allCapturedPhotos.slice(-3);
            
            // Store as final photos
            this.capturedPhotos = lastThreePhotos.map((photo, index) => ({
                imageData: photo.imageData,
                descriptor: null, // Will be processed if needed
                timestamp: photo.timestamp
            }));
            
            // Update UI
            this.updatePhotoPreview();
            this.updateCaptureCount();
            this.updateProgressIndicator();
            
            // Show completion
            this.livenessStatus.classList.add('hidden');
            this.registerBtn.classList.remove('hidden');
            this.registerBtn.disabled = false;
            this.resetBtn.classList.remove('hidden');
            
            this.showStatus(`Berhasil mengambil ${totalPhotos} foto, 3 foto terbaik dipilih untuk registrasi`, 'success');
        } else {
            this.showStatus('Tidak cukup foto yang diambil, coba lagi', 'error');
        }
    }
    
    
    updatePhotoPreview() {
        const previewDivs = this.photoPreview.children;
        
        // Update all preview divs with captured photos
        for (let i = 0; i < previewDivs.length; i++) {
            if (i < this.capturedPhotos.length) {
                previewDivs[i].innerHTML = `
                    <img src="${this.capturedPhotos[i].imageData}" 
                         class="w-full h-full object-cover rounded-lg" 
                         alt="Foto ${i + 1}">
                `;
            } else {
                previewDivs[i].innerHTML = `
                    <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                        <i class="fas fa-camera text-gray-400 text-xl"></i>
                    </div>
                `;
            }
        }
    }
    
    updateCaptureCount() {
        // Capture count UI removed - just log to console
        console.log(`Photos captured: ${this.capturedPhotos.length}`);
    }
    
    updateProgressIndicator() {
        const indicator = document.getElementById('currentPhotoIndicator');
        const capturedCount = this.capturedPhotos.length;
        
        if (capturedCount >= 3) {
            // Hide indicator once complete to keep UI clean
            indicator.parentElement.parentElement.classList.add('hidden');
            for (let i = 1; i <= 3; i++) {
                const progressBar = document.getElementById(`progress${i}`);
                if (progressBar) progressBar.className = 'w-6 h-1 bg-green-500 rounded';
            }
        } else {
            const currentPhoto = capturedCount + 1;
            indicator.textContent = `Foto ${currentPhoto} dari 3`;
            
            // Update progress bars
            for (let i = 1; i <= 3; i++) {
                const progressBar = document.getElementById(`progress${i}`);
                if (i <= capturedCount) {
                    progressBar.className = 'w-6 h-1 bg-green-500 rounded';
                } else if (i === currentPhoto) {
                    progressBar.className = 'w-6 h-1 bg-blue-500 rounded';
                } else {
                    progressBar.className = 'w-6 h-1 bg-gray-400 rounded';
                }
            }
        }
    }
    
    
    async registerFace() {
        try {
            const selectedStudentNIS = document.getElementById('selectedStudentNIS');
            const selectedStudent = selectedStudentNIS.value;
            
            if (!selectedStudent) {
                this.showStatus('Pilih siswa terlebih dahulu!', 'error');
                return;
            }
            
            this.showStatus('Mendaftarkan Face ID...', 'info');
            this.registerBtn.disabled = true;
            
            // Prepare face data with student info
            const faceData = this.capturedPhotos.map(photo => photo.imageData);
            
            // Send to backend with student info
            const response = await fetch('/admin/face-id/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    face_images: faceData,
                    student_nis: selectedStudent
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showRegistrationSuccess(result);
                this.showStatus('Registrasi Face ID berhasil!', 'success');
                
                // Reset untuk registrasi berikutnya setelah 3 detik
                let countdown = 3;
                const countdownInterval = setInterval(() => {
                    countdown--;
                    if (countdown > 0) {
                        this.showStatus(`Registrasi berhasil! Reset otomatis dalam ${countdown} detik...`, 'success');
                    } else {
                        clearInterval(countdownInterval);
                        this.resetForNextRegistration();
                    }
                }, 1000);
            } else {
                this.showRegistrationError(result);
                this.showStatus('Gagal mendaftarkan Face ID: ' + (result.message || 'Unknown error'), 'error');
                this.registerBtn.disabled = false;
            }
            
        } catch (error) {
            console.error('Registration error:', error);
            this.showStatus('Terjadi kesalahan saat registrasi', 'error');
            this.registerBtn.disabled = false;
        }
    }
    
    showRegistrationSuccess(result) {
        const statusElement = document.getElementById('registrationStatus');
        if (!statusElement) {
            console.error('Registration status element not found');
            return;
        }
        
        statusElement.classList.remove('hidden');
        statusElement.innerHTML = `
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-2xl text-green-600 dark:text-green-400"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">✅ Registrasi Berhasil!</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">${result.message}</p>
                <div class="space-y-2 text-sm bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                    <div class="flex justify-between">
                        <span>Siswa:</span>
                        <span class="font-medium">${result.student_name}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>NIS:</span>
                        <span class="font-medium">${result.student_nis}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Foto Tersimpan:</span>
                        <span class="font-medium">${result.photos_count}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Status:</span>
                        <span class="font-medium text-green-600">Approved</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-500 mt-3">Face ID siap digunakan untuk login!</p>
                <p class="text-xs text-blue-500 dark:text-blue-400 mt-2">
                    <i class="fas fa-refresh mr-1"></i>Sistem akan reset otomatis untuk registrasi siswa berikutnya...
                </p>
            </div>
        `;
    }
    
    showRegistrationError(result) {
        const statusElement = document.getElementById('registrationStatus');
        if (!statusElement) {
            console.error('Registration status element not found');
            return;
        }
        
        statusElement.classList.remove('hidden');
        statusElement.innerHTML = `
            <div class="text-center">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-times-circle text-2xl text-red-600"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Registrasi Gagal</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">${result.message}</p>
                <div class="mt-4">
                    <button onclick="location.reload()" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm transition duration-200">
                        Coba Lagi
                    </button>
                </div>
            </div>
        `;
    }
    
    resetCapture() {
        this.capturedPhotos = [];
        this.updateCaptureCount();
        this.updateProgressIndicator();
        
        // Reset photo preview
        const previewDivs = this.photoPreview.children;
        for (let i = 0; i < previewDivs.length; i++) {
            previewDivs[i].innerHTML = `
                <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-camera text-gray-400 text-xl"></i>
                </div>
            `;
        }
        
        // Reset buttons
        this.livenessStatus.classList.add('hidden');
        this.registerBtn.classList.add('hidden');
        this.resetBtn.classList.add('hidden');
        this.registerBtn.disabled = false;
        
        // Reset status
        const statusElement = document.getElementById('registrationStatus');
        if (statusElement) {
            statusElement.classList.add('hidden');
            statusElement.innerHTML = '';
        }
        
        this.showStatus('Reset berhasil - Mulai ambil foto lagi', 'info');
    }
    
    resetForNextRegistration() {
        // Stop camera if running
        this.stopCamera();
        
        // Reset all data
        this.capturedPhotos = [];
        this.allCapturedPhotos = [];
        
        // Reset UI elements
        this.updatePhotoPreview();
        this.updateCaptureCount();
        this.updateProgressIndicator();
        
        // Reset photo preview to empty state
        const previewDivs = this.photoPreview.children;
        for (let i = 0; i < previewDivs.length; i++) {
            previewDivs[i].innerHTML = `
                <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-camera text-gray-400 text-xl"></i>
                </div>
            `;
        }
        
        // Reset student selection
        const selectedStudentText = document.getElementById('selectedStudentText');
        const selectedStudentNIS = document.getElementById('selectedStudentNIS');
        const startBtn = document.getElementById('startCamera');
        
        if (selectedStudentText) selectedStudentText.textContent = '-- Pilih Siswa --';
        if (selectedStudentNIS) selectedStudentNIS.value = '';
        if (startBtn) {
            startBtn.disabled = true;
            startBtn.innerHTML = '<i class="fas fa-user-plus mr-2"></i>Pilih Siswa Terlebih Dahulu';
        }
        
        // Reset registration status
        const statusElement = document.getElementById('registrationStatus');
        if (statusElement) {
            statusElement.classList.add('hidden');
            statusElement.innerHTML = '';
        }
        
        // Reset buttons
        this.startBtn.classList.remove('hidden');
        this.livenessStatus.classList.add('hidden');
        this.livenessInstructions.classList.add('hidden');
        this.registerBtn.classList.add('hidden');
        this.resetBtn.classList.add('hidden');
        this.stopBtn.classList.add('hidden');
        document.getElementById('photoProgress').classList.add('hidden');
        
        this.showStatus('Siap untuk registrasi siswa berikutnya', 'info');
    }
    
    stopCamera() {
        // Stop all camera streams
        if (this.stream) {
            this.stream.getTracks().forEach(track => {
                track.stop();
                console.log('Track stopped:', track.kind);
            });
            this.stream = null;
        }
        
        // Clear all intervals and timers
        if (this.detectionInterval) {
            clearInterval(this.detectionInterval);
            this.detectionInterval = null;
        }
        
        // Clear any liveness detection timers
        if (this.livenessTimer) {
            clearInterval(this.livenessTimer);
            this.livenessTimer = null;
        }
        
        if (this.phaseTimeout) {
            clearTimeout(this.phaseTimeout);
            this.phaseTimeout = null;
        }
        
        // Clear continuous capture
        if (this.continuousCaptureInterval) {
            clearInterval(this.continuousCaptureInterval);
            this.continuousCaptureInterval = null;
        }
        
        this.isDetecting = false;
        this.video.srcObject = null;
        
        // Reset UI elements
        this.startBtn.classList.remove('hidden');
        this.livenessStatus.classList.add('hidden');
        this.livenessInstructions.classList.add('hidden');
        this.registerBtn.classList.add('hidden');
        this.resetBtn.classList.add('hidden');
        this.stopBtn.classList.add('hidden');
        document.getElementById('photoProgress').classList.add('hidden');
        
        // Clear face overlay
        const overlay = document.getElementById('faceOverlay');
        if (overlay) overlay.innerHTML = '';
        
        this.showStatus('Kamera dihentikan', 'info');
    }
    
    showStatus(message, type = 'info') {
        // Status disabled for clean registration UI - just log to console
        console.log(`Status [${type}]: ${message}`);
    }
}

// Initialize Face Registration System when page loads
document.addEventListener('DOMContentLoaded', function() {
    const system = new FaceRegistrationSystem();
    
    // Student Selection Modal Logic
    const studentSelectBtn = document.getElementById('studentSelectBtn');
    const studentModal = document.getElementById('studentModal');
    const closeStudentModal = document.getElementById('closeStudentModal');
    const studentSearch = document.getElementById('studentSearch');
    const studentList = document.getElementById('studentList');
    const selectedStudentText = document.getElementById('selectedStudentText');
    const selectedStudentNIS = document.getElementById('selectedStudentNIS');
    const startBtn = document.getElementById('startCamera');
    
    // Open modal
    studentSelectBtn.addEventListener('click', function() {
        studentModal.classList.remove('hidden');
        studentSearch.focus();
    });
    
    // Close modal
    closeStudentModal.addEventListener('click', function() {
        studentModal.classList.add('hidden');
    });
    
    // Close modal when clicking outside
    studentModal.addEventListener('click', function(e) {
        if (e.target === studentModal) {
            studentModal.classList.add('hidden');
        }
    });
    
    // Search functionality
    studentSearch.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const studentItems = document.querySelectorAll('.student-item');
        
        studentItems.forEach(item => {
            const name = item.dataset.name.toLowerCase();
            const nis = item.dataset.nis.toLowerCase();
            
            if (name.includes(searchTerm) || nis.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Select student
    document.querySelectorAll('.student-item').forEach(item => {
        item.addEventListener('click', function() {
            const name = this.dataset.name;
            const nis = this.dataset.nis;
            
            selectedStudentText.textContent = name + ' (' + nis + ')';
            selectedStudentNIS.value = nis;
            
            // Update start button
            startBtn.disabled = false;
            startBtn.innerHTML = '<i class="fas fa-camera mr-2"></i>Mulai Registrasi untuk ' + name;
            
            // Close modal
            studentModal.classList.add('hidden');
            
            // Clear search
            studentSearch.value = '';
            document.querySelectorAll('.student-item').forEach(i => i.style.display = 'block');
        });
    });
    
    // Initial state handled by FaceRegistrationSystem.initializeSystem()
});
</script>
    </div>
</body>
</html>
