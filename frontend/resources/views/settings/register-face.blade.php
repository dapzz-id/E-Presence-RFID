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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Registration Form -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700">
                    <h2 class="text-xl font-semibold text-white">
                        <i class="fas fa-camera mr-2"></i>Registrasi Wajah
                    </h2>
                </div>
                
                <div class="p-6">
                    <!-- Camera Container -->
                    <div class="relative bg-gray-900 rounded-lg overflow-hidden mb-6" style="aspect-ratio: 4/3;">
                        <video id="video" autoplay muted playsinline class="w-full h-full object-cover"></video>
                        <canvas id="canvas" class="hidden"></canvas>
                        
                        <!-- Face Detection Overlay -->
                        <div id="faceOverlay" class="absolute inset-0 pointer-events-none">
                            <!-- Face detection box will be drawn here -->
                        </div>
                        
                        <!-- Status Overlay -->
                        <div id="statusOverlay" class="absolute top-4 left-4 right-4">
                            <div id="statusMessage" class="bg-black bg-opacity-75 text-white px-4 py-2 rounded-lg text-sm text-center hidden">
                                Memulai kamera...
                            </div>
                        </div>
                        
                        <!-- Capture Counter -->
                        <div id="captureCounter" class="absolute bottom-4 right-4">
                            <div class="bg-primary-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                                <span id="captureCount">0</span>/3 Foto
                            </div>
                        </div>
                    </div>

                    <!-- Controls -->
                    <div class="space-y-4">
                        <button id="startCamera" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                            <i class="fas fa-camera mr-2"></i>Mulai Kamera
                        </button>
                        
                        <button id="captureBtn" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center hidden" disabled>
                            <i class="fas fa-camera-retro mr-2"></i><span id="captureButtonText">Ambil Foto</span>
                        </button>
                        
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
                </div>
            </div>

            <!-- Instructions & Preview -->
            <div class="space-y-6">
                <!-- Captured Photos Preview -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700">
                        <h3 class="text-lg font-semibold text-white">
                            <i class="fas fa-images mr-2"></i>Foto yang Diambil
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <div id="photoPreview" class="grid grid-cols-3 gap-4">
                            <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                <i class="fas fa-camera text-gray-400 text-2xl"></i>
                            </div>
                            <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                <i class="fas fa-camera text-gray-400 text-2xl"></i>
                            </div>
                            <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                <i class="fas fa-camera text-gray-400 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700">
                        <h3 class="text-lg font-semibold text-white">
                            <i class="fas fa-info-circle mr-2"></i>Petunjuk Registrasi
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                                Ambil 3 foto wajah dari sudut yang berbeda
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                                Pastikan pencahayaan cukup dan wajah terlihat jelas
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                                Jangan memakai kacamata gelap atau masker
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                                Foto 1: Wajah menghadap lurus ke kamera
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                                Foto 2: Wajah sedikit miring ke kiri
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                                Foto 3: Wajah sedikit miring ke kanan
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Registration Status -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-purple-700">
                        <h3 class="text-lg font-semibold text-white">
                            <i class="fas fa-chart-pie mr-2"></i>Status Registrasi
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <div id="registrationStatus">
                            <div class="text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-hourglass-start text-4xl mb-4"></i>
                                <p>Siap untuk memulai registrasi</p>
                            </div>
                        </div>
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
        this.captureBtn = document.getElementById('captureBtn');
        this.registerBtn = document.getElementById('registerBtn');
        this.resetBtn = document.getElementById('resetBtn');
        this.stopBtn = document.getElementById('stopCamera');
        this.statusMessage = document.getElementById('statusMessage');
        this.captureCount = document.getElementById('captureCount');
        this.captureButtonText = document.getElementById('captureButtonText');
        this.photoPreview = document.getElementById('photoPreview');
        this.registrationStatus = document.getElementById('registrationStatus');
        
        this.startBtn.addEventListener('click', () => this.startCamera());
        this.captureBtn.addEventListener('click', () => this.capturePhoto());
        this.registerBtn.addEventListener('click', () => this.registerFace());
        this.resetBtn.addEventListener('click', () => this.resetCapture());
        this.stopBtn.addEventListener('click', () => this.stopCamera());
    }
    
    initializeCounters() {
        // Initialize counters to show correct values from start
        this.updateCaptureCount(); // Should show 0
        // Don't update button text yet - button is still hidden
    }
    
    async initializeSystem() {
        // Enable start button immediately for better UX
        this.startBtn.disabled = false;
        this.showStatus('Sistem siap - Klik "Mulai Kamera" untuk memulai', 'success');
        
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
                    faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                    faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                    faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
                    faceapi.nets.faceExpressionNet.loadFromUri('/models')
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
                this.captureBtn.classList.remove('hidden');
                this.stopBtn.classList.remove('hidden');
                
                // Enable capture button temporarily for testing
                this.captureBtn.disabled = false;
                
                // Initialize button text correctly - should show "Ambil Foto Pertama"
                this.updateCaptureButtonText();
                
                this.showStatus('Kamera aktif - Posisikan wajah Anda', 'success');
                
                // Start detection if models are loaded
                if (this.isModelLoaded) {
                    this.startDetection();
                } else {
                    this.showStatus('Kamera aktif - Model AI sedang dimuat...', 'info');
                }
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
    
    startDetection() {
        if (!this.isModelLoaded) return;
        
        this.isDetecting = true;
        this.detectionInterval = setInterval(async () => {
            await this.detectFace();
        }, 100);
    }
    
    async detectFace() {
        if (!this.video.videoWidth || !this.video.videoHeight) return;
        
        try {
            const detections = await faceapi.detectAllFaces(
                this.video,
                new faceapi.TinyFaceDetectorOptions()
            ).withFaceLandmarks().withFaceExpressions();
            
            // Clear previous overlays
            const overlay = document.getElementById('faceOverlay');
            overlay.innerHTML = '';
            
            if (detections.length > 0) {
                const detection = detections[0];
                
                // Draw face detection box
                this.drawFaceBox(detection.detection.box, overlay);
                
                // Enable capture if face is detected
                this.captureBtn.disabled = false;
                this.showStatus(`Wajah terdeteksi - Siap untuk foto ${this.capturedPhotos.length + 1}`, 'success');
                
            } else {
                this.captureBtn.disabled = true;
                this.showStatus('Posisikan wajah Anda di depan kamera', 'info');
            }
            
        } catch (error) {
            console.error('Detection error:', error);
        }
    }
    
    drawFaceBox(box, overlay) {
        const faceBox = document.createElement('div');
        faceBox.style.position = 'absolute';
        faceBox.style.left = `${box.x}px`;
        faceBox.style.top = `${box.y}px`;
        faceBox.style.width = `${box.width}px`;
        faceBox.style.height = `${box.height}px`;
        faceBox.style.border = '3px solid #10B981';
        faceBox.style.borderRadius = '8px';
        faceBox.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.5)';
        
        overlay.appendChild(faceBox);
    }
    
    async capturePhoto() {
        try {
            // Capture current frame
            this.ctx.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);
            const imageData = this.canvas.toDataURL('image/jpeg', 0.8);
            
            let faceDescriptor = null;
            
            // Try to detect face, but don't fail if not detected
            try {
                if (this.isModelLoaded && typeof faceapi !== 'undefined') {
                    const detections = await faceapi.detectSingleFace(
                        this.video,
                        new faceapi.TinyFaceDetectorOptions()
                    ).withFaceLandmarks().withFaceDescriptor();
                    
                    if (detections) {
                        faceDescriptor = detections.descriptor;
                        this.showStatus(`Foto ${this.capturedPhotos.length + 1} berhasil dengan deteksi wajah`, 'success');
                    } else {
                        this.showStatus(`Foto ${this.capturedPhotos.length + 1} berhasil (tanpa deteksi wajah)`, 'warning');
                    }
                } else {
                    this.showStatus(`Foto ${this.capturedPhotos.length + 1} berhasil (mode manual)`, 'success');
                }
            } catch (detectionError) {
                console.warn('Face detection failed, continuing without:', detectionError);
                this.showStatus(`Foto ${this.capturedPhotos.length + 1} berhasil (deteksi gagal)`, 'warning');
            }
            
            // Store photo data
            this.capturedPhotos.push({
                imageData: imageData,
                descriptor: faceDescriptor,
                timestamp: Date.now()
            });
            
            // Update UI
            this.updatePhotoPreview();
            this.updateCaptureCount();
            
            // Check if we have enough photos
            if (this.capturedPhotos.length >= this.maxPhotos) {
                this.captureBtn.classList.add('hidden');
                this.registerBtn.classList.remove('hidden');
                this.registerBtn.disabled = false; // Enable register button
                this.resetBtn.classList.remove('hidden');
                this.showStatus('Semua foto berhasil diambil - Siap untuk registrasi', 'success');
            } else {
                const nextPhoto = this.capturedPhotos.length + 1;
                this.showStatus(`Foto ${this.capturedPhotos.length} berhasil - Siap untuk foto ${nextPhoto}`, 'success');
                this.updateCaptureButtonText();
            }
            
        } catch (error) {
            console.error('Capture error:', error);
            this.showStatus('Gagal mengambil foto: ' + error.message, 'error');
        }
    }
    
    updatePhotoPreview() {
        const previewDivs = this.photoPreview.children;
        const photoIndex = this.capturedPhotos.length - 1;
        
        if (photoIndex >= 0 && photoIndex < previewDivs.length) {
            const previewDiv = previewDivs[photoIndex];
            previewDiv.innerHTML = `
                <img src="${this.capturedPhotos[photoIndex].imageData}" 
                     class="w-full h-full object-cover rounded-lg" 
                     alt="Foto ${photoIndex + 1}">
            `;
        }
    }
    
    updateCaptureCount() {
        this.captureCount.textContent = this.capturedPhotos.length;
    }
    
    updateCaptureButtonText() {
        if (this.capturedPhotos.length === 0) {
            // Belum ada foto yang diambil
            this.captureButtonText.textContent = 'Ambil Foto Pertama';
        } else if (this.capturedPhotos.length < this.maxPhotos) {
            // Masih ada foto yang perlu diambil
            const nextPhoto = this.capturedPhotos.length + 1;
            this.captureButtonText.textContent = `Ambil Foto ${nextPhoto}`;
        }
    }
    
    async registerFace() {
        try {
            this.showStatus('Mendaftarkan Face ID...', 'info');
            this.registerBtn.disabled = true;
            
            // Prepare face data - simplified
            const faceData = this.capturedPhotos.map(photo => photo.imageData);
            
            // Send to backend - simplified endpoint
            const response = await fetch('/admin/face-id/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    face_images: faceData
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showRegistrationSuccess(result);
                this.showStatus('Registrasi Face ID berhasil!', 'success');
                
                // Redirect ke settings setelah 2 detik
                setTimeout(() => {
                    window.location.href = '/admin/settings';
                }, 2000);
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
        this.registrationStatus.innerHTML = `
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-2xl text-green-600 dark:text-green-400"></i>
                </div>
                <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">✅ Registrasi Berhasil!</h4>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">${result.message}</p>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span>Foto Terdaftar:</span>
                        <span class="font-medium">${this.capturedPhotos.length}</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-500">Mengalihkan ke halaman settings...</p>
            </div>
        `;
    }
    
    showRegistrationError(result) {
        this.registrationStatus.innerHTML = `
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
        this.updateCaptureButtonText();
        
        // Reset photo preview
        const previewDivs = this.photoPreview.children;
        for (let i = 0; i < previewDivs.length; i++) {
            previewDivs[i].innerHTML = `
                <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <i class="fas fa-camera text-gray-400 text-2xl"></i>
                </div>
            `;
        }
        
        // Reset buttons
        this.captureBtn.classList.remove('hidden');
        this.registerBtn.classList.add('hidden');
        this.resetBtn.classList.add('hidden');
        this.registerBtn.disabled = false;
        
        // Reset status
        this.registrationStatus.innerHTML = `
            <div class="text-center text-gray-500 dark:text-gray-400">
                <i class="fas fa-hourglass-start text-4xl mb-4"></i>
                <p>Siap untuk memulai registrasi</p>
            </div>
        `;
        
        this.showStatus('Reset berhasil - Mulai ambil foto lagi', 'info');
    }
    
    stopCamera() {
        if (this.stream) {
            this.stream.getTracks().forEach(track => track.stop());
            this.stream = null;
        }
        
        if (this.detectionInterval) {
            clearInterval(this.detectionInterval);
            this.detectionInterval = null;
        }
        
        this.isDetecting = false;
        this.video.srcObject = null;
        
        this.startBtn.classList.remove('hidden');
        this.captureBtn.classList.add('hidden');
        this.registerBtn.classList.add('hidden');
        this.resetBtn.classList.add('hidden');
        this.stopBtn.classList.add('hidden');
        
        this.showStatus('Kamera dihentikan', 'info');
    }
    
    showStatus(message, type = 'info') {
        const statusDiv = this.statusMessage;
        statusDiv.textContent = message;
        statusDiv.classList.remove('hidden');
        
        // Remove existing type classes
        statusDiv.classList.remove('bg-blue-600', 'bg-green-600', 'bg-yellow-600', 'bg-red-600');
        
        // Add appropriate color based on type
        switch (type) {
            case 'success':
                statusDiv.classList.add('bg-green-600');
                break;
            case 'warning':
                statusDiv.classList.add('bg-yellow-600');
                break;
            case 'error':
                statusDiv.classList.add('bg-red-600');
                break;
            default:
                statusDiv.classList.add('bg-blue-600');
        }
        
        // Auto-hide after 3 seconds for non-error messages
        if (type !== 'error') {
            setTimeout(() => {
                statusDiv.classList.add('hidden');
            }, 3000);
        }
    }
}

// Initialize Face Registration System when page loads
document.addEventListener('DOMContentLoaded', function() {
    new FaceRegistrationSystem();
});
</script>
    </div>
</body>
</html>
