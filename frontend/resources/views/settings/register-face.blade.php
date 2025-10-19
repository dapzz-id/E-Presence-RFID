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
                    
                    <!-- Auto Capture Instructions -->
                    <div id="captureInstructions" class="mb-4 p-4 bg-green-50 dark:bg-green-900 border border-green-200 dark:border-green-700 rounded-lg hidden">
                        <div class="text-center">
                            <h4 class="font-medium text-green-900 dark:text-green-100 mb-2">
                                <i class="fas fa-camera mr-2"></i>Deteksi Otomatis Aktif
                            </h4>
                            <p class="text-sm text-green-700 dark:text-green-300">
                                Posisikan wajah Anda di dalam ring hijau
                            </p>
                            <div id="captureStatus" class="mt-3 text-lg font-bold text-green-600 dark:text-green-300">
                                Foto: 0/9
                            </div>
                            <div id="captureMessage" class="mt-2 text-sm text-green-600 dark:text-green-400">
                                Menunggu wajah terdeteksi...
                            </div>
                        </div>
                    </div>
                    
                    <!-- Camera Container with Ring Overlay -->
                    <div class="relative bg-gray-900 rounded-lg overflow-hidden mb-6 w-full" style="height: 480px; max-width: 480px; margin: 0 auto;">
                        <video id="video" autoplay muted playsinline class="w-full h-full object-cover"></video>
                        <canvas id="canvas" class="hidden"></canvas>
                        
                        <!-- Ring Overlay for Face Detection -->
                        <div id="faceOverlay" class="absolute inset-0 pointer-events-none flex items-center justify-center">
                            <div id="faceRing" class="relative" style="width: 280px; height: 350px;">
                                <!-- Oval Ring -->
                                <svg class="absolute inset-0 w-full h-full" viewBox="0 0 280 350">
                                    <ellipse cx="140" cy="175" rx="130" ry="165" 
                                             fill="none" 
                                             stroke="#10b981" 
                                             stroke-width="4" 
                                             stroke-dasharray="10,5"
                                             opacity="0.8"
                                             id="ringStroke"/>
                                </svg>
                                <!-- Corner Guides -->
                                <div class="absolute top-0 left-0 w-8 h-8 border-t-4 border-l-4 border-green-500 rounded-tl-lg"></div>
                                <div class="absolute top-0 right-0 w-8 h-8 border-t-4 border-r-4 border-green-500 rounded-tr-lg"></div>
                                <div class="absolute bottom-0 left-0 w-8 h-8 border-b-4 border-l-4 border-green-500 rounded-bl-lg"></div>
                                <div class="absolute bottom-0 right-0 w-8 h-8 border-b-4 border-r-4 border-green-500 rounded-br-lg"></div>
                            </div>
                        </div>
                        
                        <!-- Photo Progress Indicator -->
                        <div id="photoProgress" class="absolute top-4 left-4 hidden">
                            <div class="bg-black bg-opacity-75 text-white px-3 py-2 rounded-lg text-sm">
                                <div id="currentPhotoIndicator" class="font-medium">Foto 1 dari 9</div>
                                <div class="flex space-x-1 mt-1">
                                    <div id="progress1" class="w-2 h-1 bg-gray-400 rounded"></div>
                                    <div id="progress2" class="w-2 h-1 bg-gray-400 rounded"></div>
                                    <div id="progress3" class="w-2 h-1 bg-gray-400 rounded"></div>
                                    <div id="progress4" class="w-2 h-1 bg-gray-400 rounded"></div>
                                    <div id="progress5" class="w-2 h-1 bg-gray-400 rounded"></div>
                                    <div id="progress6" class="w-2 h-1 bg-gray-400 rounded"></div>
                                    <div id="progress7" class="w-2 h-1 bg-gray-400 rounded"></div>
                                    <div id="progress8" class="w-2 h-1 bg-gray-400 rounded"></div>
                                    <div id="progress9" class="w-2 h-1 bg-gray-400 rounded"></div>
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
                    <div id="photoPreview" class="grid grid-cols-3 gap-2">
                        <!-- 9 foto preview slots -->
                        <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-xs">
                            <i class="fas fa-camera text-gray-400"></i>
                        </div>
                        <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-xs">
                            <i class="fas fa-camera text-gray-400"></i>
                        </div>
                        <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-xs">
                            <i class="fas fa-camera text-gray-400"></i>
                        </div>
                        <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-xs">
                            <i class="fas fa-camera text-gray-400"></i>
                        </div>
                        <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-xs">
                            <i class="fas fa-camera text-gray-400"></i>
                        </div>
                        <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-xs">
                            <i class="fas fa-camera text-gray-400"></i>
                        </div>
                        <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-xs">
                            <i class="fas fa-camera text-gray-400"></i>
                        </div>
                        <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-xs">
                            <i class="fas fa-camera text-gray-400"></i>
                        </div>
                        <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-xs">
                            <i class="fas fa-camera text-gray-400"></i>
                        </div>
                    </div>
                </div>
        </div>
    </div>
</div>

<!-- Face-api.js Library -->
<script src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<!-- MediaPipe Face Mesh for Liveness Detection - Fixed Version -->
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils@0.3.1640029074/camera_utils.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/control_utils@0.6.1629159505/control_utils.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/drawing_utils@0.3.1620248257/drawing_utils.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh@0.4.1633559619/face_mesh.js" crossorigin="anonymous"></script>

<script>
class FaceRegistrationSystem {
    constructor() {
        this.video = document.getElementById('video');
        this.canvas = document.getElementById('canvas');
        this.ctx = this.canvas.getContext('2d');
        this.stream = null;
        this.isModelLoaded = false;
        this.detectionInterval = null;
        this.capturedPhotos = [];
        this.maxPhotos = 9;
        this.captureInProgress = false;
        this.lastCaptureTime = 0;
        this.captureDelay = 800; // 800ms between captures
        
        this.initializeElements();
        this.initializeSystem();
    }
    
    initializeElements() {
        this.startBtn = document.getElementById('startCamera');
        this.stopBtn = document.getElementById('stopCamera');
        this.photoPreview = document.getElementById('photoPreview');
        this.registrationStatus = document.getElementById('registrationStatus');
        this.captureInstructions = document.getElementById('captureInstructions');
        this.captureStatus = document.getElementById('captureStatus');
        this.captureMessage = document.getElementById('captureMessage');
        this.ringStroke = document.getElementById('ringStroke');
        
        // Add event listeners
        if (this.startBtn) this.startBtn.addEventListener('click', () => this.startCamera());
        if (this.stopBtn) this.stopBtn.addEventListener('click', () => this.stopCamera());
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
            this.showStatus('Memuat model deteksi wajah...', 'info');
            
            // Check if face-api.js is loaded
            if (typeof faceapi === 'undefined') {
                console.warn('Face API library tidak ditemukan - mode manual');
                this.isModelLoaded = false;
                this.showStatus('Sistem siap - Mode manual', 'info');
                return;
            }
            
            // Load face detection model only
            await faceapi.nets.tinyFaceDetector.loadFromUri('https://cdn.jsdelivr.net/npm/@vladmandic/face-api@latest/model');
            
            this.isModelLoaded = true;
            console.log('✅ Face detection model loaded');
            this.showStatus('✅ Deteksi wajah siap', 'success');
            
        } catch (error) {
            console.error('Error loading models:', error);
            this.showStatus('⚠️ Mode manual - Tanpa deteksi otomatis', 'warning');
            this.isModelLoaded = false;
        }
    }
    
    // MediaPipe removed - using simple face detection only
    
    async startCamera() {
        try {
            // Validate student selection first
            const selectedStudentNIS = document.getElementById('selectedStudentNIS');
            if (!selectedStudentNIS.value) {
                alert('Pilih akun siswa terlebih dahulu!');
                return;
            }
            
            console.log('Starting camera...');
            
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
                
                // Update UI
                this.startBtn.classList.add('hidden');
                this.stopBtn.classList.remove('hidden');
                this.captureInstructions.classList.remove('hidden');
                document.getElementById('photoProgress').classList.remove('hidden');
                
                // Start auto-capture with face detection
                this.startAutoCapture();
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
            
            alert(errorMessage);
        }
    }
    
    startAutoCapture() {
        console.log('🎯 Starting auto-capture with face detection');
        this.updateProgressIndicator();
        
        // Start face detection loop
        this.detectionInterval = setInterval(async () => {
            await this.detectAndCapture();
        }, 200); // Check every 200ms
    }
    
    async detectAndCapture() {
        try {
            // Skip if already capturing or completed
            if (this.captureInProgress || this.capturedPhotos.length >= this.maxPhotos) {
                if (this.capturedPhotos.length >= this.maxPhotos) {
                    this.completeCapture();
                }
                return;
            }
            
            // Check cooldown between captures
            const now = Date.now();
            if (now - this.lastCaptureTime < this.captureDelay) {
                return;
            }
            
            // Detect face
            let faceDetected = false;
            let faceInPosition = false;
            
            if (this.isModelLoaded && this.video.videoWidth > 0) {
                const detection = await faceapi.detectSingleFace(this.video, new faceapi.TinyFaceDetectorOptions());
                
                if (detection) {
                    faceDetected = true;
                    
                    // Check if face is in the ring area (centered and proper size)
                    const box = detection.box;
                    const videoWidth = this.video.videoWidth;
                    const videoHeight = this.video.videoHeight;
                    
                    // Calculate center of face
                    const faceCenterX = box.x + box.width / 2;
                    const faceCenterY = box.y + box.height / 2;
                    
                    // Calculate center of video
                    const videoCenterX = videoWidth / 2;
                    const videoCenterY = videoHeight / 2;
                    
                    // Check if face is centered (within 25% of center)
                    const centerThresholdX = videoWidth * 0.25;
                    const centerThresholdY = videoHeight * 0.25;
                    const isCentered = Math.abs(faceCenterX - videoCenterX) < centerThresholdX && 
                                      Math.abs(faceCenterY - videoCenterY) < centerThresholdY;
                    
                    // Check if face size is appropriate (30-70% of video height)
                    const minSize = videoHeight * 0.3;
                    const maxSize = videoHeight * 0.7;
                    const isGoodSize = box.height > minSize && box.height < maxSize;
                    
                    faceInPosition = isCentered && isGoodSize;
                    
                    // Update ring color based on position
                    if (faceInPosition) {
                        this.ringStroke.setAttribute('stroke', '#10b981'); // Green
                        this.ringStroke.setAttribute('stroke-width', '6');
                        this.captureMessage.textContent = '✅ Posisi sempurna! Mengambil foto...';
                        this.captureMessage.className = 'mt-2 text-sm text-green-600 dark:text-green-400 font-bold';
                    } else if (isCentered) {
                        this.ringStroke.setAttribute('stroke', '#f59e0b'); // Orange
                        this.ringStroke.setAttribute('stroke-width', '4');
                        this.captureMessage.textContent = isGoodSize ? 'Posisikan lebih dekat/jauh' : 'Atur jarak kamera';
                        this.captureMessage.className = 'mt-2 text-sm text-orange-600 dark:text-orange-400';
                    } else {
                        this.ringStroke.setAttribute('stroke', '#ef4444'); // Red
                        this.ringStroke.setAttribute('stroke-width', '4');
                        this.captureMessage.textContent = 'Posisikan wajah di tengah ring';
                        this.captureMessage.className = 'mt-2 text-sm text-red-600 dark:text-red-400';
                    }
                } else {
                    // No face detected
                    this.ringStroke.setAttribute('stroke', '#6b7280'); // Gray
                    this.ringStroke.setAttribute('stroke-width', '4');
                    this.captureMessage.textContent = 'Menunggu wajah terdeteksi...';
                    this.captureMessage.className = 'mt-2 text-sm text-gray-600 dark:text-gray-400';
                }
            } else {
                // No model loaded - capture anyway
                faceDetected = true;
                faceInPosition = true;
            }
            
            // Auto-capture if face is in good position
            if (faceDetected && faceInPosition) {
                await this.capturePhoto();
            }
            
        } catch (error) {
            console.error('Detection error:', error);
        }
    }
    
    async capturePhoto() {
        if (this.captureInProgress || this.capturedPhotos.length >= this.maxPhotos) {
            return;
        }
        
        this.captureInProgress = true;
        this.lastCaptureTime = Date.now();
        
        try {
            // Capture frame
            this.ctx.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);
            const imageData = this.canvas.toDataURL('image/jpeg', 0.9);
            
            // Store photo
            this.capturedPhotos.push({
                imageData: imageData,
                timestamp: Date.now()
            });
            
            console.log(`📸 Photo ${this.capturedPhotos.length}/${this.maxPhotos} captured`);
            
            // Update UI
            this.updatePhotoPreview();
            this.updateProgressIndicator();
            this.captureStatus.textContent = `Foto: ${this.capturedPhotos.length}/9`;
            
            // Flash effect
            this.ringStroke.setAttribute('opacity', '1');
            setTimeout(() => {
                this.ringStroke.setAttribute('opacity', '0.8');
            }, 100);
            
        } catch (error) {
            console.error('Capture error:', error);
        } finally {
            this.captureInProgress = false;
        }
    }
    
    completeCapture() {
        // Stop detection
        if (this.detectionInterval) {
            clearInterval(this.detectionInterval);
            this.detectionInterval = null;
        }
        
        // Stop camera
        this.stopCamera();
        
        // Update UI
        this.captureInstructions.classList.add('hidden');
        this.captureMessage.textContent = '🎉 Semua foto berhasil diambil!';
        this.captureMessage.className = 'mt-2 text-sm text-green-600 dark:text-green-400 font-bold';
        
        console.log('✅ Capture complete, auto-registering...');
        
        // Auto-register
        setTimeout(() => {
            this.autoRegisterFace();
        }, 1000);
    }
    
    startLivenessDetection() {
        this.livenessInstructions.classList.remove('hidden');
        this.livenessText.textContent = 'Liveness Detection - Deteksi Otomatis';
        
        // Reset liveness variables
        this.blinkCount = 0;
        this.requiredHeadTurns = { right: false, left: false };
        this.livenessPhase = 'blink';
        this.phaseStartTime = Date.now();
        this.blinkDetectionState = null; // Reset detection state
        
        // Manual blink button hidden by default (clear UI)
        
        // Start real-time liveness detection with MediaPipe
        this.startMediaPipeLivenessDetection();
        
        // Start photo capture every second during liveness detection
        this.startContinuousCapture();
        
        // Set initial instruction and UI
        this.updatePhaseUI();
        this.updateBlinkUI();
    }
    
    manualBlink() {
        // Manual blink for testing purposes
        if (this.livenessPhase === 'blink') {
            this.blinkCount++;
            console.log(`🔧 Manual blink #${this.blinkCount}`);
            
            // Update UI immediately
            this.updateBlinkUI();
            this.instructionTimer.textContent = `✅ Manual kedipan ${this.blinkCount}`;
            
            if (this.blinkCount >= this.requiredBlinks) {
                this.livenessPhase = 'look_right';
                this.phaseStartTime = Date.now();
                this.updatePhaseUI();
                this.livenessText.textContent = 'Liveness: Tengok ke kanan';
                this.manualBlinkBtn.classList.add('hidden');
                this.instructionTimer.textContent = 'Sekarang tengok ke kanan';
                console.log('🎯 Manual blink phase completed');
            } else {
                setTimeout(() => {
                    this.instructionTimer.textContent = `Butuh ${this.requiredBlinks - this.blinkCount} kedipan lagi`;
                }, 1000);
            }
        }
    }
    
    manualHeadTurn(direction) {
        if (direction === 'right' && this.livenessPhase === 'look_right' && !this.requiredHeadTurns.right) {
            this.requiredHeadTurns.right = true;
            this.livenessPhase = 'look_left';
            this.phaseStartTime = Date.now();
            this.updatePhaseUI();
            if (this.instructionTimer) this.instructionTimer.textContent = '✅ Tengok kanan berhasil!';
            console.log('✅ Manual right head turn completed');
            
            setTimeout(() => {
                if (this.instructionTimer) this.instructionTimer.textContent = 'Sekarang tengok ke kiri';
            }, 1000);
            
        } else if (direction === 'left' && this.livenessPhase === 'look_left' && !this.requiredHeadTurns.left) {
            this.requiredHeadTurns.left = true;
            if (this.instructionTimer) this.instructionTimer.textContent = '✅ Tengok kiri berhasil!';
            console.log('✅ Manual left head turn completed');
            
            setTimeout(() => {
                this.checkLivenessCompletion();
            }, 500);
        }
    }
    
    updatePhaseUI() {
        // Update phase progress indicators
        const phase1 = document.getElementById('phase1');
        const phase2 = document.getElementById('phase2');
        const phase3 = document.getElementById('phase3');
        
        // Reset all phases
        phase1.querySelector('div').className = 'w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold';
        phase1.querySelector('span').className = 'text-sm text-gray-500';
        phase2.querySelector('div').className = 'w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold';
        phase2.querySelector('span').className = 'text-sm text-gray-500';
        phase3.querySelector('div').className = 'w-8 h-8 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center text-sm font-bold';
        phase3.querySelector('span').className = 'text-sm text-gray-500';
        
        // Highlight current phase and sync status
        switch (this.livenessPhase) {
            case 'blink':
                phase1.querySelector('div').className = 'w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-bold';
                phase1.querySelector('span').className = 'text-sm text-blue-600 font-medium';
                this.currentInstruction.textContent = 'Berkedip 3 kali secara normal';
                // Sync with liveness text
                this.livenessText.textContent = 'Liveness Detection - Fase Kedipan';
                // Show blink counter and manual button
                if (this.blinkCounter) {
                    this.blinkCounter.classList.remove('hidden');
                }
                if (this.manualBlinkBtn) {
                    this.manualBlinkBtn.classList.remove('hidden');
                }
                break;
            case 'look_right':
                phase1.querySelector('div').className = 'w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center text-sm font-bold';
                phase1.querySelector('span').className = 'text-sm text-green-600 font-medium';
                phase2.querySelector('div').className = 'w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-bold';
                phase2.querySelector('span').className = 'text-sm text-blue-600 font-medium';
                this.currentInstruction.textContent = 'Tengok ke kiri (akan terlihat kanan di kamera)';
                // Sync with liveness text
                this.livenessText.textContent = 'Liveness Detection - Tengok Kiri (Kanan di Kamera)';
                // Show manual right button, hide blink button
                if (this.manualBlinkBtn) this.manualBlinkBtn.classList.add('hidden');
                if (this.manualRightBtn) this.manualRightBtn.classList.remove('hidden');
                if (this.manualLeftBtn) this.manualLeftBtn.classList.add('hidden');
                break;
            case 'look_left':
                phase1.querySelector('div').className = 'w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center text-sm font-bold';
                phase1.querySelector('span').className = 'text-sm text-green-600 font-medium';
                phase2.querySelector('div').className = 'w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center text-sm font-bold';
                phase2.querySelector('span').className = 'text-sm text-green-600 font-medium';
                phase3.querySelector('div').className = 'w-8 h-8 rounded-full bg-blue-500 text-white flex items-center justify-center text-sm font-bold';
                phase3.querySelector('span').className = 'text-sm text-blue-600 font-medium';
                this.currentInstruction.textContent = 'Tengok ke kanan (akan terlihat kiri di kamera)';
                // Sync with liveness text
                this.livenessText.textContent = 'Liveness Detection - Tengok Kanan (Kiri di Kamera)';
                // Show manual left button, hide others
                if (this.manualBlinkBtn) this.manualBlinkBtn.classList.add('hidden');
                if (this.manualRightBtn) this.manualRightBtn.classList.add('hidden');
                if (this.manualLeftBtn) this.manualLeftBtn.classList.remove('hidden');
                break;
        }
    }
    
    updateBlinkUI() {
        // Update blink status text only (no dots) - with null checks
        if (this.blinkStatus) {
            this.blinkStatus.textContent = `Kedipan: ${this.blinkCount}/3`;
            
            // Change color based on progress
            if (this.blinkCount === 3) {
                this.blinkStatus.className = 'text-lg font-bold text-green-600 dark:text-green-300';
            } else if (this.blinkCount > 0) {
                this.blinkStatus.className = 'text-lg font-bold text-yellow-600 dark:text-yellow-300';
            } else {
                this.blinkStatus.className = 'text-lg font-bold text-blue-600 dark:text-blue-300';
            }
        } else {
            console.warn('⚠️ blinkStatus element not available for update');
        }
    }
    
    startMediaPipeLivenessDetection() {
        // Always try MediaPipe first, but with better fallback
        if (!this.isMediaPipeLoaded || !this.faceMesh) {
            console.log('⚠️ MediaPipe not available, using face-api.js fallback');
            this.startRealTimeLivenessDetection();
            return;
        }
        
        try {
            // Initialize MediaPipe detection state
            this.mediaBlinkState = {
                leftEyeHistory: [],
                rightEyeHistory: [],
                isBlinking: false,
                blinkStartTime: 0,
                framesSinceLastBlink: 0,
                errorCount: 0
            };
            
            // Start MediaPipe processing with error handling
            this.mediaDetectionInterval = setInterval(async () => {
                try {
                    if (this.video && this.video.videoWidth > 0 && this.faceMesh) {
                        await this.faceMesh.send({ image: this.video });
                    }
                } catch (sendError) {
                    this.mediaBlinkState.errorCount++;
                    console.warn('MediaPipe send error:', sendError);
                    
                    // If too many errors, fallback to face-api.js
                    if (this.mediaBlinkState.errorCount > 10) {
                        console.log('🔄 Too many MediaPipe errors, switching to face-api.js');
                        this.switchToFallbackDetection();
                    }
                }
            }, 150); // Process every 150ms for stability
            
            console.log('🎯 MediaPipe liveness detection started');
            
        } catch (error) {
            console.warn('MediaPipe start error:', error);
            this.switchToFallbackDetection();
        }
    }
    
    switchToFallbackDetection() {
        // Clean up MediaPipe
        if (this.mediaDetectionInterval) {
            clearInterval(this.mediaDetectionInterval);
            this.mediaDetectionInterval = null;
        }
        
        // Switch to face-api.js
        console.log('🔄 Switching to face-api.js detection');
        this.startRealTimeLivenessDetection();
    }
    
    processMediaPipeResults(results) {
        if (!results.multiFaceLandmarks || results.multiFaceLandmarks.length === 0) {
            if (this.currentInstruction) {
                this.currentInstruction.textContent = 'Posisikan wajah di depan kamera';
            }
            return;
        }
        
        const landmarks = results.multiFaceLandmarks[0];
        
        // ULTRA STRICT sequential processing - NEVER skip phases
        console.log(`🔍 Current Phase: ${this.livenessPhase}, Blinks: ${this.blinkCount}/${this.requiredBlinks}, Right: ${this.requiredHeadTurns.right}, Left: ${this.requiredHeadTurns.left}`);
        
        switch (this.livenessPhase) {
            case 'blink':
                // ONLY process blink detection, NEVER head pose
                this.detectMediaPipeBlink(landmarks);
                // NEVER process head pose in blink phase
                // STRICT: Do not advance until blink count is met
                if (this.blinkCount >= this.requiredBlinks) {
                    console.log('🎯 Blink phase completed, but staying in blink phase until timeout or manual advance');
                }
                break;
            case 'look_right':
                // STRICT validation - only if blink is COMPLETELY done
                if (this.blinkCount >= this.requiredBlinks) {
                    this.detectMediaPipeHeadPose(landmarks, 'right');
                } else {
                    // Force back to blink if not complete
                    console.log('⚠️ Forcing back to blink phase - blink count insufficient');
                    this.livenessPhase = 'blink';
                    this.updatePhaseUI();
                }
                break;
            case 'look_left':
                // STRICT validation - only if both blink and right are done
                if (this.blinkCount >= this.requiredBlinks && this.requiredHeadTurns.right) {
                    this.detectMediaPipeHeadPose(landmarks, 'left');
                } else {
                    // Force back to appropriate phase
                    if (this.blinkCount < this.requiredBlinks) {
                        console.log('⚠️ Forcing back to blink phase - blink not complete');
                        this.livenessPhase = 'blink';
                    } else if (!this.requiredHeadTurns.right) {
                        console.log('⚠️ Forcing back to right turn phase - right not complete');
                        this.livenessPhase = 'look_right';
                    }
                    this.updatePhaseUI();
                }
                break;
        }
        
        // Only check completion if in look_left phase and ALL requirements are met
        if (this.livenessPhase === 'look_left' && this.blinkCount >= this.requiredBlinks && this.requiredHeadTurns.right && this.requiredHeadTurns.left) {
            this.checkLivenessCompletion();
        }
    }
    
    detectMediaPipeBlink(landmarks) {
        try {
            // MediaPipe eye landmarks indices
            const leftEyeIndices = [33, 7, 163, 144, 145, 153, 154, 155, 133, 173, 157, 158, 159, 160, 161, 246];
            const rightEyeIndices = [362, 382, 381, 380, 374, 373, 390, 249, 263, 466, 388, 387, 386, 385, 384, 398];
            
            // Calculate Eye Aspect Ratio for both eyes
            const leftEAR = this.calculateMediaPipeEAR(landmarks, leftEyeIndices);
            const rightEAR = this.calculateMediaPipeEAR(landmarks, rightEyeIndices);
            const avgEAR = (leftEAR + rightEAR) / 2;
            
            console.log(`👁️ MediaPipe EAR: ${avgEAR.toFixed(3)}, Left: ${leftEAR.toFixed(3)}, Right: ${rightEAR.toFixed(3)}`);
            
            // Store EAR history
            this.mediaBlinkState.leftEyeHistory.push(leftEAR);
            this.mediaBlinkState.rightEyeHistory.push(rightEAR);
            
            if (this.mediaBlinkState.leftEyeHistory.length > 10) {
                this.mediaBlinkState.leftEyeHistory.shift();
                this.mediaBlinkState.rightEyeHistory.shift();
            }
            
            // Calculate baseline from recent history
            if (this.mediaBlinkState.leftEyeHistory.length >= 5) {
                const avgLeftEAR = this.mediaBlinkState.leftEyeHistory.reduce((a, b) => a + b, 0) / this.mediaBlinkState.leftEyeHistory.length;
                const avgRightEAR = this.mediaBlinkState.rightEyeHistory.reduce((a, b) => a + b, 0) / this.mediaBlinkState.rightEyeHistory.length;
                const baseline = (avgLeftEAR + avgRightEAR) / 2;
                
                // Blink detection with MediaPipe (much more sensitive)
                const blinkThreshold = baseline * 0.85; // Only 15% drop needed
                const strongBlinkThreshold = baseline * 0.70; // 30% drop for strong blink
                const isCurrentlyBlinking = avgEAR < blinkThreshold;
                const isStrongBlink = avgEAR < strongBlinkThreshold;
                
                this.mediaBlinkState.framesSinceLastBlink++;
                
                console.log(`🎯 MediaPipe Baseline: ${baseline.toFixed(3)}, Threshold: ${blinkThreshold.toFixed(3)}, Blinking: ${isCurrentlyBlinking}`);
                
                // Detect blink transition with multiple methods
                if (isCurrentlyBlinking && !this.mediaBlinkState.isBlinking) {
                    this.mediaBlinkState.isBlinking = true;
                    this.mediaBlinkState.blinkStartTime = Date.now();
                    console.log('👁️ MediaPipe blink start detected');
                    if (this.instructionTimer) this.instructionTimer.textContent = 'Mata tertutup...';
                } else if (!isCurrentlyBlinking && this.mediaBlinkState.isBlinking) {
                    const blinkDuration = Date.now() - this.mediaBlinkState.blinkStartTime;
                    
                    // More lenient blink validation
                    if (blinkDuration > 30 && blinkDuration < 1500 && this.mediaBlinkState.framesSinceLastBlink > 3) {
                        this.blinkCount++;
                        this.mediaBlinkState.framesSinceLastBlink = 0;
                        console.log(`✅ MediaPipe Blink #${this.blinkCount} detected! Duration: ${blinkDuration}ms`);
                        
                        this.updateBlinkUI();
                        if (this.instructionTimer) this.instructionTimer.textContent = `✅ Kedipan ${this.blinkCount} berhasil!`;
                        
                        // STRICT CHECK - only advance if blink phase is TRULY complete
                        if (this.blinkCount >= this.requiredBlinks && this.livenessPhase === 'blink') {
                            console.log('🎯 MediaPipe: Blink phase COMPLETED, preparing for head turn');
                            if (this.instructionTimer) this.instructionTimer.textContent = '✅ Kedipan selesai! Bersiap untuk tengok kiri...';
                            
                            // Wait longer before moving to head turn to ensure user understands
                            setTimeout(() => {
                                // Double check we're still in blink phase before advancing
                                if (this.livenessPhase === 'blink' && this.blinkCount >= this.requiredBlinks) {
                                    this.livenessPhase = 'look_right';
                                    this.phaseStartTime = Date.now();
                                    this.updatePhaseUI();
                                    if (this.manualBlinkBtn) this.manualBlinkBtn.classList.add('hidden');
                                    if (this.instructionTimer) this.instructionTimer.textContent = 'Sekarang tengok ke KIRI (akan terlihat kanan di kamera)';
                                    console.log('🎯 MediaPipe: NOW moving to head turn phase');
                                }
                            }, 2000); // Longer delay
                        } else {
                            setTimeout(() => {
                                if (this.instructionTimer && this.livenessPhase === 'blink') {
                                    this.instructionTimer.textContent = `Butuh ${this.requiredBlinks - this.blinkCount} kedipan lagi`;
                                }
                            }, 1500);
                        }
                    }
                    
                    this.mediaBlinkState.isBlinking = false;
                }
                
                // Alternative detection for very subtle blinks
                if (!this.mediaBlinkState.isBlinking && this.mediaBlinkState.framesSinceLastBlink > 10) {
                    // Check for strong blink patterns
                    if (isStrongBlink) {
                        this.blinkCount++;
                        this.mediaBlinkState.framesSinceLastBlink = 0;
                        console.log(`✅ MediaPipe Strong Blink #${this.blinkCount} detected! EAR: ${avgEAR.toFixed(3)}`);
                        
                        this.updateBlinkUI();
                        if (this.instructionTimer) this.instructionTimer.textContent = `✅ Kedipan ${this.blinkCount} berhasil!`;
                        
                        if (this.blinkCount >= this.requiredBlinks && this.livenessPhase === 'blink') {
                            console.log('🎯 MediaPipe: Strong blink completed phase');
                            if (this.instructionTimer) this.instructionTimer.textContent = '✅ Kedipan selesai! Bersiap untuk tengok kiri...';
                            
                            setTimeout(() => {
                                if (this.livenessPhase === 'blink' && this.blinkCount >= this.requiredBlinks) {
                                    this.livenessPhase = 'look_right';
                                    this.phaseStartTime = Date.now();
                                    this.updatePhaseUI();
                                    if (this.manualBlinkBtn) this.manualBlinkBtn.classList.add('hidden');
                                    if (this.instructionTimer) this.instructionTimer.textContent = 'Sekarang tengok ke KIRI (akan terlihat kanan di kamera)';
                                }
                            }, 2000);
                        }
                    }
                }
                
                // Update instruction
                if (this.livenessPhase === 'blink' && !isCurrentlyBlinking && this.blinkCount < this.requiredBlinks) {
                    if (this.instructionTimer && !this.instructionTimer.textContent.includes('✅') && !this.instructionTimer.textContent.includes('Butuh')) {
                        this.instructionTimer.textContent = `Berkedip normal (${this.blinkCount}/3)`;
                    }
                }
            } else {
                if (this.instructionTimer) this.instructionTimer.textContent = 'Kalibrasi MediaPipe...';
            }
            
            // Timeout for blink phase
            const phaseTime = Date.now() - this.phaseStartTime;
            if (this.livenessPhase === 'blink' && phaseTime > 10000) {
                console.log('⏰ MediaPipe blink timeout - advancing');
                this.livenessPhase = 'look_right';
                this.phaseStartTime = Date.now();
                this.updatePhaseUI();
                if (this.instructionTimer) this.instructionTimer.textContent = 'Timeout - Tengok ke kanan';
            }
            
        } catch (error) {
            console.error('MediaPipe blink detection error:', error);
        }
    }
    
    calculateMediaPipeEAR(landmarks, eyeIndices) {
        try {
            // Get key eye points for EAR calculation
            const p1 = landmarks[eyeIndices[1]]; // Top
            const p2 = landmarks[eyeIndices[5]]; // Bottom  
            const p3 = landmarks[eyeIndices[0]]; // Left corner
            const p4 = landmarks[eyeIndices[8]]; // Right corner
            
            // Calculate vertical and horizontal distances
            const vertical = Math.sqrt(Math.pow(p1.x - p2.x, 2) + Math.pow(p1.y - p2.y, 2));
            const horizontal = Math.sqrt(Math.pow(p3.x - p4.x, 2) + Math.pow(p3.y - p4.y, 2));
            
            if (horizontal === 0) return 0.3;
            
            const ear = vertical / horizontal;
            return Math.max(0.1, Math.min(0.5, ear));
        } catch (error) {
            console.error('MediaPipe EAR calculation error:', error);
            return 0.3;
        }
    }
    
    detectMediaPipeHeadPose(landmarks, direction) {
        try {
            // MediaPipe face landmarks for head pose detection
            const noseTip = landmarks[1];        // Nose tip
            const leftEyeCorner = landmarks[33]; // Left eye inner corner
            const rightEyeCorner = landmarks[362]; // Right eye inner corner
            const leftCheek = landmarks[234];    // Left cheek
            const rightCheek = landmarks[454];   // Right cheek
            const chin = landmarks[18];          // Chin center
            
            // Calculate face center and nose position relative to face
            const faceCenterX = (leftEyeCorner.x + rightEyeCorner.x) / 2;
            const faceCenterY = (leftEyeCorner.y + rightEyeCorner.y) / 2;
            
            // Calculate head turn ratios
            const noseOffsetX = noseTip.x - faceCenterX;
            const faceWidth = Math.abs(rightEyeCorner.x - leftEyeCorner.x);
            const headTurnRatio = noseOffsetX / faceWidth;
            
            // Additional check using cheek visibility
            const leftCheekDistance = Math.sqrt(Math.pow(leftCheek.x - faceCenterX, 2) + Math.pow(leftCheek.y - faceCenterY, 2));
            const rightCheekDistance = Math.sqrt(Math.pow(rightCheek.x - faceCenterX, 2) + Math.pow(rightCheek.y - faceCenterY, 2));
            const cheekRatio = (rightCheekDistance - leftCheekDistance) / faceWidth;
            
            // Combined ratio for more accurate detection
            const combinedRatio = (headTurnRatio + cheekRatio) / 2;
            
            console.log(`🎯 MediaPipe Head Pose - Direction: ${direction}, Combined Ratio: ${combinedRatio.toFixed(3)}, Nose Offset: ${headTurnRatio.toFixed(3)}, Cheek Ratio: ${cheekRatio.toFixed(3)}`);
            
            // Head pose detection with MediaPipe (CORRECTED ORIENTATION)
            // User tengok kiri = terlihat kanan di kamera = negative ratio
            // User tengok kanan = terlihat kiri di kamera = positive ratio
            // FIXED: direction 'right' means we want user to look LEFT (appears right in camera)
            if (direction === 'right' && combinedRatio < -0.15) { // User tengok kiri = kanan di kamera
                this.requiredHeadTurns.right = true;
                this.livenessPhase = 'look_left';
                this.phaseStartTime = Date.now();
                this.updatePhaseUI();
                if (this.instructionTimer) this.instructionTimer.textContent = '✅ Tengok kiri berhasil!';
                console.log('✅ MediaPipe Right phase completed! (User turned left, appears right in camera)');
                
                setTimeout(() => {
                    if (this.instructionTimer) this.instructionTimer.textContent = 'Sekarang tengok ke KANAN (akan terlihat kiri di kamera)';
                }, 1000);
                
            } else if (direction === 'left' && combinedRatio > 0.12) { // User tengok kanan = kiri di kamera
                this.requiredHeadTurns.left = true;
                if (this.instructionTimer) this.instructionTimer.textContent = '✅ Tengok kanan berhasil!';
                console.log('✅ MediaPipe Left phase completed! (User turned right, appears left in camera)');
                
                // Complete liveness detection
                setTimeout(() => {
                    this.checkLivenessCompletion();
                }, 500);
            }
            
            // Update instruction for current direction (corrected)
            if (direction === 'right' && !this.requiredHeadTurns.right) {
                if (this.instructionTimer && !this.instructionTimer.textContent.includes('✅')) {
                    this.instructionTimer.textContent = `Tengok ke KIRI... (${combinedRatio.toFixed(2)})`;
                }
            } else if (direction === 'left' && !this.requiredHeadTurns.left) {
                if (this.instructionTimer && !this.instructionTimer.textContent.includes('✅')) {
                    this.instructionTimer.textContent = `Tengok ke KANAN... (${combinedRatio.toFixed(2)})`;
                }
            }
            
            // Timeout for head pose phases
            const phaseTime = Date.now() - this.phaseStartTime;
            if (phaseTime > 8000) { // 8 seconds timeout
                if (direction === 'right' && !this.requiredHeadTurns.right) {
                    console.log('⏰ MediaPipe right turn timeout - advancing');
                    this.requiredHeadTurns.right = true;
                    this.livenessPhase = 'look_left';
                    this.phaseStartTime = Date.now();
                    this.updatePhaseUI();
                    if (this.instructionTimer) this.instructionTimer.textContent = 'Timeout - Tengok ke KANAN (kiri di kamera)';
                } else if (direction === 'left' && !this.requiredHeadTurns.left) {
                    console.log('⏰ MediaPipe left turn timeout - completing');
                    this.requiredHeadTurns.left = true;
                    this.checkLivenessCompletion();
                }
            }
            
        } catch (error) {
            console.error('MediaPipe head pose detection error:', error);
        }
    }
    
    async startRealTimeLivenessDetection() {
        if (!this.isModelLoaded) {
            console.log('Models not loaded, using timer-based capture');
            this.simulateLivenessDetection();
            return;
        }
        
        this.livenessDetectionInterval = setInterval(async () => {
            await this.performLivenessCheck();
        }, 200); // Check every 200ms for responsiveness
    }
    
    async performLivenessCheck() {
        try {
            if (!this.video || !this.video.videoWidth || !this.video.videoHeight) return;
            
            // Detect face with landmarks
            const detection = await faceapi.detectSingleFace(this.video, new faceapi.TinyFaceDetectorOptions())
                .withFaceLandmarks();
            
            if (!detection) {
                if (this.currentInstruction) {
                    this.currentInstruction.textContent = 'Posisikan wajah di depan kamera';
                }
                return;
            }
            
            const landmarks = detection.landmarks;
            
            // Check current phase and perform appropriate detection
            switch (this.livenessPhase) {
                case 'blink':
                    this.checkBlinkDetection(landmarks);
                    break;
                case 'look_right':
                    this.checkHeadPose(landmarks, 'right');
                    break;
                case 'look_left':
                    this.checkHeadPose(landmarks, 'left');
                    break;
            }
            
            // Check if all liveness requirements are met
            this.checkLivenessCompletion();
            
        } catch (error) {
            console.error('Liveness detection error:', error);
            // Don't crash the system, just skip this frame
            if (this.instructionTimer) {
                this.instructionTimer.textContent = 'Error deteksi - mencoba lagi...';
            }
        }
    }
    
    checkBlinkDetection(landmarks) {
        // Simplified blink detection using vertical eye distances
        const leftEye = landmarks.getLeftEye();
        const rightEye = landmarks.getRightEye();
        
        // Calculate Eye Aspect Ratio (EAR) for both eyes - more accurate
        const leftEAR = this.calculateEAR(leftEye);
        const rightEAR = this.calculateEAR(rightEye);
        const avgEAR = (leftEAR + rightEAR) / 2;
        
        // Debug logging with more detail
        console.log(`👁️ EAR: ${avgEAR.toFixed(3)}, Left: ${leftEAR.toFixed(3)}, Right: ${rightEAR.toFixed(3)}`);
        
        // Initialize blink detection variables if not set
        if (!this.blinkDetectionState) {
            this.blinkDetectionState = {
                isBlinking: false,
                blinkStartTime: 0,
                earHistory: [],
                openEyeBaseline: null
            };
        }
        
        // Store EAR history for baseline calculation
        this.blinkDetectionState.earHistory.push(avgEAR);
        if (this.blinkDetectionState.earHistory.length > 15) {
            this.blinkDetectionState.earHistory.shift();
        }
        
        // Calculate baseline for open eyes (use average of recent values)
        if (this.blinkDetectionState.earHistory.length >= 10) {
            this.blinkDetectionState.openEyeBaseline = this.blinkDetectionState.earHistory.reduce((a, b) => a + b, 0) / this.blinkDetectionState.earHistory.length;
        }
        
        // Simplified and more reliable blink detection
        if (this.blinkDetectionState.earHistory.length >= 5) {
            // Use dynamic threshold based on recent EAR variations
            const recentEARs = this.blinkDetectionState.earHistory.slice(-10);
            const maxEAR = Math.max(...recentEARs);
            const minEAR = Math.min(...recentEARs);
            const avgEARRecent = recentEARs.reduce((a, b) => a + b, 0) / recentEARs.length;
            
            // More sensitive thresholds
            const blinkThreshold = avgEARRecent * 0.8; // Only 20% drop needed
            const strongBlinkThreshold = avgEARRecent * 0.6; // 40% drop for strong blink
            
            const isCurrentlyBlinking = avgEAR < blinkThreshold;
            const isStrongBlink = avgEAR < strongBlinkThreshold;
            
            console.log(`🎯 Avg: ${avgEARRecent.toFixed(3)}, Threshold: ${blinkThreshold.toFixed(3)}, Strong: ${strongBlinkThreshold.toFixed(3)}, Current: ${avgEAR.toFixed(3)}, Blinking: ${isCurrentlyBlinking}, Strong: ${isStrongBlink}`);
            
            // Initialize frame counter if not exists
            if (!this.blinkDetectionState.framesSinceLastBlink) {
                this.blinkDetectionState.framesSinceLastBlink = 0;
            }
            
            this.blinkDetectionState.framesSinceLastBlink++;
            
            // Detect blink with more lenient conditions
            if (isCurrentlyBlinking && !this.blinkDetectionState.isBlinking) {
                this.blinkDetectionState.isBlinking = true;
                this.blinkDetectionState.blinkStartTime = Date.now();
                console.log('👁️ Blink start detected');
                if (this.instructionTimer) this.instructionTimer.textContent = 'Mata tertutup...';
            } else if (!isCurrentlyBlinking && this.blinkDetectionState.isBlinking) {
                const blinkDuration = Date.now() - this.blinkDetectionState.blinkStartTime;
                
                // More lenient blink validation
                if (blinkDuration > 50 && blinkDuration < 1000 && this.blinkDetectionState.framesSinceLastBlink > 5) {
                    this.blinkCount++;
                    this.blinkDetectionState.framesSinceLastBlink = 0;
                    console.log(`✅ Blink #${this.blinkCount} detected! Duration: ${blinkDuration}ms`);
                    
                    this.updateBlinkUI();
                    if (this.instructionTimer) this.instructionTimer.textContent = `✅ Kedipan ${this.blinkCount} berhasil!`;
                    
                    if (this.blinkCount >= this.requiredBlinks) {
                        setTimeout(() => {
                            this.livenessPhase = 'look_right';
                            this.phaseStartTime = Date.now();
                            this.updatePhaseUI();
                            if (this.manualBlinkBtn) this.manualBlinkBtn.classList.add('hidden');
                            if (this.instructionTimer) this.instructionTimer.textContent = 'Sekarang tengok ke kanan';
                            console.log('🎯 Moving to head turn phase');
                        }, 1000);
                    } else {
                        setTimeout(() => {
                            if (this.instructionTimer) this.instructionTimer.textContent = `Butuh ${this.requiredBlinks - this.blinkCount} kedipan lagi`;
                        }, 1500);
                    }
                }
                
                this.blinkDetectionState.isBlinking = false;
            }
            
            // Alternative detection for very subtle blinks
            if (!this.blinkDetectionState.isBlinking && this.blinkDetectionState.framesSinceLastBlink > 15) {
                const earDrop = maxEAR - avgEAR;
                if (earDrop > 0.03 && avgEAR < (maxEAR * 0.85)) { // 15% drop from max
                    this.blinkCount++;
                    this.blinkDetectionState.framesSinceLastBlink = 0;
                    console.log(`✅ Subtle blink #${this.blinkCount} detected! EAR drop: ${earDrop.toFixed(3)}`);
                    
                    this.updateBlinkUI();
                    if (this.instructionTimer) this.instructionTimer.textContent = `✅ Kedipan ${this.blinkCount} berhasil!`;
                    
                    if (this.blinkCount >= this.requiredBlinks) {
                        setTimeout(() => {
                            this.livenessPhase = 'look_right';
                            this.phaseStartTime = Date.now();
                            this.updatePhaseUI();
                            if (this.manualBlinkBtn) this.manualBlinkBtn.classList.add('hidden');
                            if (this.instructionTimer) this.instructionTimer.textContent = 'Sekarang tengok ke kanan';
                            console.log('🎯 Moving to head turn phase');
                        }, 1000);
                    }
                }
            }
            
            // Spike detection - detect sudden EAR changes
            if (this.blinkDetectionState.earHistory.length >= 3 && this.blinkDetectionState.framesSinceLastBlink > 10) {
                const lastThreeEARs = this.blinkDetectionState.earHistory.slice(-3);
                const earChange1 = Math.abs(lastThreeEARs[1] - lastThreeEARs[0]);
                const earChange2 = Math.abs(lastThreeEARs[2] - lastThreeEARs[1]);
                
                // Detect rapid EAR changes (blink pattern)
                if (earChange1 > 0.05 || earChange2 > 0.05) {
                    this.blinkCount++;
                    this.blinkDetectionState.framesSinceLastBlink = 0;
                    console.log(`✅ Spike blink #${this.blinkCount} detected! Changes: ${earChange1.toFixed(3)}, ${earChange2.toFixed(3)}`);
                    
                    this.updateBlinkUI();
                    if (this.instructionTimer) this.instructionTimer.textContent = `✅ Kedipan ${this.blinkCount} berhasil!`;
                    
                    if (this.blinkCount >= this.requiredBlinks) {
                        setTimeout(() => {
                            this.livenessPhase = 'look_right';
                            this.phaseStartTime = Date.now();
                            this.updatePhaseUI();
                            if (this.manualBlinkBtn) this.manualBlinkBtn.classList.add('hidden');
                            if (this.instructionTimer) this.instructionTimer.textContent = 'Sekarang tengok ke kanan';
                            console.log('🎯 Moving to head turn phase');
                        }, 1000);
                    }
                }
            }
            
            // Update instruction for current state
            if (this.livenessPhase === 'blink' && !isCurrentlyBlinking && this.blinkCount < this.requiredBlinks) {
                if (this.instructionTimer && !this.instructionTimer.textContent.includes('✅') && !this.instructionTimer.textContent.includes('Butuh')) {
                    this.instructionTimer.textContent = `Berkedip normal (${this.blinkCount}/3)`;
                }
            }
        } else {
            // Still calibrating
            if (this.instructionTimer) this.instructionTimer.textContent = 'Kalibrasi deteksi...';
        }
        
        // Add timeout for blink phase (auto-advance after 10 seconds)
        const phaseTime = Date.now() - this.phaseStartTime;
        if (this.livenessPhase === 'blink' && phaseTime > 10000) { // 10 seconds timeout
            console.log('⏰ Blink phase timeout - auto advancing');
            this.livenessPhase = 'look_right';
            this.phaseStartTime = Date.now();
            this.currentInstruction.textContent = 'Tengok ke kanan';
            this.livenessText.textContent = 'Liveness: Tengok ke kanan (timeout)';
            if (this.manualBlinkBtn) this.manualBlinkBtn.classList.add('hidden');
        }
    }
    
    calculateEAR(eye) {
        // Calculate Eye Aspect Ratio using 6 eye landmarks
        if (!eye || eye.length < 6) return 0.25; // Default open eye EAR
        
        try {
            // Get eye landmark points (face-api.js format)
            const p1 = eye[1]; // Top left
            const p2 = eye[2]; // Top right  
            const p3 = eye[3]; // Right corner
            const p4 = eye[4]; // Bottom right
            const p5 = eye[5]; // Bottom left
            const p6 = eye[0]; // Left corner
            
            // Calculate vertical distances
            const v1 = Math.sqrt(Math.pow(p2.x - p5.x, 2) + Math.pow(p2.y - p5.y, 2));
            const v2 = Math.sqrt(Math.pow(p1.x - p4.x, 2) + Math.pow(p1.y - p4.y, 2));
            
            // Calculate horizontal distance
            const h = Math.sqrt(Math.pow(p6.x - p3.x, 2) + Math.pow(p6.y - p3.y, 2));
            
            if (h === 0) return 0.25;
            
            // EAR formula
            const ear = (v1 + v2) / (2 * h);
            
            // Clamp to reasonable range
            return Math.max(0.1, Math.min(0.4, ear));
        } catch (error) {
            console.error('EAR calculation error:', error);
            return 0.25;
        }
    }
    
    checkHeadPose(landmarks, direction) {
        // Improved head pose detection using multiple facial landmarks
        const nose = landmarks.getNose();
        const leftEye = landmarks.getLeftEye();
        const rightEye = landmarks.getRightEye();
        const jawline = landmarks.getJawOutline();
        
        // Calculate multiple reference points for better accuracy
        const eyeMidX = (leftEye[0].x + rightEye[3].x) / 2;
        const noseX = nose[3].x; // Nose tip
        const jawMidX = (jawline[0].x + jawline[jawline.length - 1].x) / 2;
        
        // Calculate head turn ratios using multiple methods
        const noseEyeRatio = (noseX - eyeMidX) / 40; // Nose vs eye midpoint
        const noseJawRatio = (noseX - jawMidX) / 50; // Nose vs jaw midpoint
        const eyeDistanceRatio = (rightEye[3].x - leftEye[0].x) / 100; // Eye distance changes with head turn
        
        // Combined ratio for more accurate detection
        const combinedRatio = (noseEyeRatio + noseJawRatio) / 2;
        
        console.log(`Head pose - Direction: ${direction}, Combined Ratio: ${combinedRatio.toFixed(3)}, Eye Distance: ${eyeDistanceRatio.toFixed(3)}`);
        
        // Mirror corrected thresholds (kiri di kamera = kanan di dunia nyata)
        if (direction === 'right' && (combinedRatio < -0.15 || noseEyeRatio < -0.2)) { // Mirror corrected
            this.requiredHeadTurns.right = true;
            this.livenessPhase = 'look_left';
            this.phaseStartTime = Date.now();
            this.updatePhaseUI();
            this.instructionTimer.textContent = '✅ Tengok kanan berhasil!';
            console.log('✅ Right head turn detected! (Mirror corrected)');
            
            setTimeout(() => {
                this.instructionTimer.textContent = 'Sekarang tengok ke kiri';
            }, 1000);
        } else if (direction === 'left' && (combinedRatio > 0.1 || noseEyeRatio > 0.15)) { // Mirror corrected
            this.requiredHeadTurns.left = true;
            this.instructionTimer.textContent = '✅ Tengok kiri berhasil!';
            console.log('✅ Left head turn detected! (Mirror corrected)');
            
            // Immediately complete liveness detection after left turn
            setTimeout(() => {
                this.checkLivenessCompletion();
            }, 500);
        }
        
        // Update instruction - simple and clear
        if (direction === 'right' && !this.requiredHeadTurns.right) {
            this.instructionTimer.textContent = `Putar kepala ke kanan... (${combinedRatio.toFixed(2)})`;
        } else if (direction === 'left' && !this.requiredHeadTurns.left) {
            this.instructionTimer.textContent = `Putar kepala ke kiri... (${combinedRatio.toFixed(2)})`;
        }
        
        // Shorter timeout for head pose phases (auto-advance after 8 seconds)
        const phaseTime = Date.now() - this.phaseStartTime;
        if (phaseTime > 8000) { // 8 seconds timeout
            if (direction === 'right' && !this.requiredHeadTurns.right) {
                this.requiredHeadTurns.right = true;
                this.livenessPhase = 'look_left';
                this.phaseStartTime = Date.now();
                this.updatePhaseUI();
                this.instructionTimer.textContent = '⏰ Timeout - Lanjut ke kiri';
                console.log('⏰ Right head turn timeout - auto advancing');
            } else if (direction === 'left' && !this.requiredHeadTurns.left) {
                this.requiredHeadTurns.left = true;
                this.instructionTimer.textContent = '⏰ Timeout - Selesai';
                console.log('⏰ Left head turn timeout - auto advancing');
                setTimeout(() => {
                    this.checkLivenessCompletion();
                }, 500);
            }
        }
    }
    
    
    checkLivenessCompletion() {
        const allRequirementsMet = this.blinkCount >= this.requiredBlinks && 
                                  this.requiredHeadTurns.right && 
                                  this.requiredHeadTurns.left;
        
        if (allRequirementsMet) {
            // Update final phase UI
            const phase3 = document.getElementById('phase3');
            phase3.querySelector('div').className = 'w-8 h-8 rounded-full bg-green-500 text-white flex items-center justify-center text-sm font-bold';
            phase3.querySelector('span').className = 'text-sm text-green-600 font-medium';
            
            // Hide blink counter and manual buttons since we're done
            if (this.blinkCounter) {
                this.blinkCounter.classList.add('hidden');
            }
            if (this.manualBlinkBtn) this.manualBlinkBtn.classList.add('hidden');
            if (this.manualRightBtn) this.manualRightBtn.classList.add('hidden');
            if (this.manualLeftBtn) this.manualLeftBtn.classList.add('hidden');
            
            this.currentInstruction.textContent = '🎉 Liveness Detection Selesai!';
            this.instructionTimer.textContent = 'Memproses foto dan menyimpan...';
            
            this.completeLivenessDetection();
        }
    }
    
    simulateLivenessDetection() {
        // Fallback for when face-api.js models aren't loaded
        const phases = [
            { instruction: 'Berkedip 3 kali', duration: 3000 },
            { instruction: 'Tengok ke kanan', duration: 3000 },
            { instruction: 'Tengok ke kiri', duration: 3000 }
        ];
        
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
        
        // Stop liveness detection interval
        if (this.livenessDetectionInterval) {
            clearInterval(this.livenessDetectionInterval);
            this.livenessDetectionInterval = null;
        }
        
        // Hide liveness instructions
        this.livenessInstructions.classList.add('hidden');
        this.livenessText.textContent = '✅ Liveness Detection Selesai - Memproses Foto...';
        
        // Take the best 9 photos and auto-register
        this.finalizePhotosAndAutoRegister();
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
    
    finalizePhotosAndAutoRegister() {
        // Take the best 9 photos from all captured photos
        const totalPhotos = this.allCapturedPhotos ? this.allCapturedPhotos.length : 0;
        
        if (totalPhotos >= 9) {
            // Get last 9 photos (most recent during liveness detection)
            const lastNinePhotos = this.allCapturedPhotos.slice(-9);
            
            // Store as final photos
            this.capturedPhotos = lastNinePhotos.map((photo, index) => ({
                imageData: photo.imageData,
                descriptor: null, // Will be processed if needed
                timestamp: photo.timestamp
            }));
            
        } else if (totalPhotos >= 3) {
            // If we have at least 3 photos, use them and duplicate to make 9
            const availablePhotos = this.allCapturedPhotos.slice(-totalPhotos);
            const finalPhotos = [];
            
            // Duplicate photos to reach 9 total
            for (let i = 0; i < 9; i++) {
                const photoIndex = i % availablePhotos.length;
                finalPhotos.push(availablePhotos[photoIndex]);
            }
            
            this.capturedPhotos = finalPhotos.map((photo, index) => ({
                imageData: photo.imageData,
                descriptor: null,
                timestamp: photo.timestamp
            }));
        } else {
            this.showStatus('❌ Tidak cukup foto yang diambil, coba lagi', 'error');
            return;
        }
        
        // Update UI to show captured photos
        this.updatePhotoPreview();
        this.updateCaptureCount();
        this.updateProgressIndicator();
        
        // Hide camera controls
        this.livenessStatus.classList.add('hidden');
        this.stopBtn.classList.add('hidden');
        
        // Show success message and auto-register
        this.livenessText.textContent = `✅ ${this.capturedPhotos.length} foto berhasil diambil - Menyimpan ke database...`;
        
        // Auto-register after 1 second delay to show photos
        setTimeout(() => {
            this.autoRegisterFace();
        }, 1500);
    }
    
    async autoRegisterFace() {
        try {
            const selectedStudentNIS = document.getElementById('selectedStudentNIS');
            const selectedStudent = selectedStudentNIS.value;
            
            if (!selectedStudent) {
                alert('Error: Siswa tidak terpilih!');
                return;
            }
            
            this.captureMessage.textContent = '💾 Menyimpan Face ID ke database...';
            this.captureMessage.className = 'mt-2 text-sm text-blue-600 dark:text-blue-400 font-bold';
            
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
                this.showAutoRegistrationSuccess(result);
            } else {
                this.showAutoRegistrationError(result);
            }
            
        } catch (error) {
            console.error('Auto-registration error:', error);
            this.captureMessage.textContent = '❌ Gagal menyimpan ke database';
            this.captureMessage.className = 'mt-2 text-sm text-red-600 dark:text-red-400 font-bold';
            this.showAutoRegistrationError({ message: 'Terjadi kesalahan saat menyimpan' });
        }
    }
    
    showAutoRegistrationSuccess(result) {
        // Update status
        this.captureMessage.textContent = '🎉 Face ID berhasil disimpan!';
        this.captureMessage.className = 'mt-2 text-sm text-green-600 dark:text-green-400 font-bold';
        
        // Show success in registration status area
        const statusElement = document.getElementById('registrationStatus');
        if (statusElement) {
            statusElement.classList.remove('hidden');
            statusElement.innerHTML = `
                <div class="text-center bg-green-50 dark:bg-green-900 p-6 rounded-lg border border-green-200 dark:border-green-700">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-2xl text-green-600 dark:text-green-400"></i>
                    </div>
                    <h4 class="text-lg font-medium text-green-900 dark:text-green-100 mb-2">🎉 Registrasi Berhasil!</h4>
                    <p class="text-sm text-green-700 dark:text-green-300 mb-4">${result.message}</p>
                    <div class="space-y-2 text-sm bg-white dark:bg-green-800 p-4 rounded-lg border border-green-200 dark:border-green-600">
                        <div class="flex justify-between">
                            <span class="font-medium">Siswa:</span>
                            <span class="text-green-700 dark:text-green-300">${result.student_name}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">NIS:</span>
                            <span class="text-green-700 dark:text-green-300">${result.student_nis}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Foto Tersimpan:</span>
                            <span class="text-green-700 dark:text-green-300">${result.photos_count || 9}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium">Status:</span>
                            <span class="text-green-600 font-medium">✅ Aktif</span>
                        </div>
                    </div>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-4">
                        <i class="fas fa-info-circle mr-1"></i>Face ID siap digunakan untuk absensi!
                    </p>
                </div>
            `;
        }
        
        // Auto-reset after 5 seconds for next registration
        setTimeout(() => {
            this.resetForNextRegistration();
        }, 5000);
    }
    
    showAutoRegistrationError(result) {
        // Update status
        this.captureMessage.textContent = '❌ Gagal menyimpan Face ID';
        this.captureMessage.className = 'mt-2 text-sm text-red-600 dark:text-red-400 font-bold';
        
        // Show error in registration status area
        const statusElement = document.getElementById('registrationStatus');
        if (statusElement) {
            statusElement.classList.remove('hidden');
            statusElement.innerHTML = `
                <div class="text-center bg-red-50 dark:bg-red-900 p-6 rounded-lg border border-red-200 dark:border-red-700">
                    <div class="w-16 h-16 bg-red-100 dark:bg-red-800 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-times-circle text-2xl text-red-600 dark:text-red-400"></i>
                    </div>
                    <h4 class="text-lg font-medium text-red-900 dark:text-red-100 mb-2">❌ Registrasi Gagal</h4>
                    <p class="text-sm text-red-700 dark:text-red-300 mb-4">${result.message}</p>
                    <div class="mt-4 space-x-3">
                        <button onclick="location.reload()" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm transition duration-200">
                            <i class="fas fa-redo mr-2"></i>Coba Lagi
                        </button>
                        <button onclick="window.history.back()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm transition duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>Kembali
                        </button>
                    </div>
                </div>
            `;
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
        
        if (capturedCount >= 9) {
            // Hide indicator once complete to keep UI clean
            indicator.parentElement.parentElement.classList.add('hidden');
            for (let i = 1; i <= 9; i++) {
                const progressBar = document.getElementById(`progress${i}`);
                if (progressBar) progressBar.className = 'w-2 h-1 bg-green-500 rounded';
            }
        } else {
            const currentPhoto = capturedCount + 1;
            indicator.textContent = `Foto ${currentPhoto} dari 9`;
            
            // Update progress bars
            for (let i = 1; i <= 9; i++) {
                const progressBar = document.getElementById(`progress${i}`);
                if (progressBar) {
                    if (i <= capturedCount) {
                        progressBar.className = 'w-2 h-1 bg-green-500 rounded';
                    } else if (i === currentPhoto) {
                        progressBar.className = 'w-2 h-1 bg-blue-500 rounded';
                    } else {
                        progressBar.className = 'w-2 h-1 bg-gray-400 rounded';
                    }
                }
            }
        }
    }
    
    resetForNextRegistration() {
        // Reset all variables for next student registration
        this.capturedPhotos = [];
        this.allCapturedPhotos = [];
        this.blinkCount = 0;
        this.requiredHeadTurns = { right: false, left: false };
        this.livenessPhase = 'idle';
        this.blinkDetectionState = null;
        
        // Reset UI
        if (this.livenessInstructions) this.livenessInstructions.classList.add('hidden');
        if (this.livenessStatus) this.livenessStatus.classList.add('hidden');
        if (this.blinkCounter) this.blinkCounter.classList.add('hidden');
        const registrationStatus = document.getElementById('registrationStatus');
        if (registrationStatus) registrationStatus.classList.add('hidden');
        
        // Reset photo preview
        const previewDivs = this.photoPreview.children;
        for (let i = 0; i < previewDivs.length; i++) {
            previewDivs[i].innerHTML = `
                <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-xs">
                    <i class="fas fa-camera text-gray-400"></i>
                </div>
            `;
        }
        
        // Reset student selection
        const selectedStudentText = document.getElementById('selectedStudentText');
        const selectedStudentNIS = document.getElementById('selectedStudentNIS');
        if (selectedStudentText) selectedStudentText.textContent = '-- Pilih Siswa --';
        if (selectedStudentNIS) selectedStudentNIS.value = '';
        
        // Reset buttons
        this.startBtn.classList.remove('hidden');
        this.startBtn.disabled = true; // Will be enabled when student is selected
        this.registerBtn.classList.add('hidden');
        this.resetBtn.classList.add('hidden');
        this.stopBtn.classList.add('hidden');
        
        // Reset status
        this.livenessText.textContent = 'Pilih siswa untuk memulai registrasi Face ID';
        
        console.log('🔄 System reset for next registration');
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
                <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center text-xs">
                    <i class="fas fa-camera text-gray-400"></i>
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
        
        // Clear intervals
        if (this.livenessDetectionInterval) {
            clearInterval(this.livenessDetectionInterval);
            this.livenessDetectionInterval = null;
        }
        
        if (this.mediaDetectionInterval) {
            clearInterval(this.mediaDetectionInterval);
            this.mediaDetectionInterval = null;
        }
        
        if (this.captureInterval) {
            clearInterval(this.captureInterval);
            this.captureInterval = null;
        }
        
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
