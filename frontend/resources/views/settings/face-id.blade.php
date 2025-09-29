<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Face ID Authentication - E-Presence</title>
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
                    <i class="fas fa-camera mr-3 text-primary-600"></i>Face ID Authentication
                </h1>
            </div>
            <p class="text-gray-600 dark:text-gray-400">
                Gunakan kamera untuk melakukan absensi dengan Face ID
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Camera Section -->
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                <div class="px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700">
                    <h2 class="text-xl font-semibold text-white">
                        <i class="fas fa-video mr-2"></i>Kamera Face ID
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
                        
                        <!-- Anti-Spoofing Indicator -->
                        <div id="antiSpoofingIndicator" class="absolute top-4 right-4">
                            <div class="bg-green-600 text-white px-3 py-1 rounded-full text-xs font-medium flex items-center">
                                <i class="fas fa-shield-alt mr-1"></i>
                                <span id="spoofingStatus">Protected</span>
                            </div>
                        </div>
                    </div>

                    <!-- Controls -->
                    <div class="space-y-4">
                        <button id="startCamera" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                            <i class="fas fa-camera mr-2"></i>Mulai Kamera
                        </button>
                        
                        <button id="captureBtn" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center hidden" disabled>
                            <i class="fas fa-user-check mr-2"></i>Scan Face ID
                        </button>
                        
                        <button id="stopCamera" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center hidden">
                            <i class="fas fa-stop mr-2"></i>Stop Kamera
                        </button>
                    </div>
                </div>
            </div>

            <!-- Results Section -->
            <div class="space-y-6">
                <!-- Detection Results -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-green-700">
                        <h3 class="text-lg font-semibold text-white">
                            <i class="fas fa-chart-line mr-2"></i>Hasil Deteksi
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <div id="detectionResults" class="space-y-4">
                            <div class="text-center text-gray-500 dark:text-gray-400">
                                <i class="fas fa-search text-4xl mb-4"></i>
                                <p>Belum ada hasil deteksi</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-700">
                        <h3 class="text-lg font-semibold text-white">
                            <i class="fas fa-info-circle mr-2"></i>Petunjuk Penggunaan
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <ul class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                                Pastikan wajah Anda terlihat jelas di kamera
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                                Hindari pencahayaan yang terlalu terang atau gelap
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                                Jangan menggunakan foto atau video untuk bypass
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                                Sistem akan otomatis mendeteksi gerakan hidup
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-green-500 mr-3 mt-0.5"></i>
                                Tunggu hingga deteksi selesai sebelum bergerak
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Security Features -->
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gradient-to-r from-purple-600 to-purple-700">
                        <h3 class="text-lg font-semibold text-white">
                            <i class="fas fa-shield-alt mr-2"></i>Fitur Keamanan
                        </h3>
                    </div>
                    
                    <div class="p-6 space-y-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Liveness Detection</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Anti-Spoofing Protection</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Real-time Face Recognition</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                            <span class="text-sm text-gray-700 dark:text-gray-300">Encrypted Face Data</span>
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
class FaceIDSystem {
    constructor() {
        this.video = document.getElementById('video');
        this.canvas = document.getElementById('canvas');
        this.ctx = this.canvas.getContext('2d');
        this.stream = null;
        this.isModelLoaded = false;
        this.isDetecting = false;
        this.detectionInterval = null;
        this.antiSpoofingFrames = [];
        this.maxFrames = 10;
        
        this.initializeElements();
        this.loadModels();
    }
    
    initializeElements() {
        this.startBtn = document.getElementById('startCamera');
        this.captureBtn = document.getElementById('captureBtn');
        this.stopBtn = document.getElementById('stopCamera');
        this.statusMessage = document.getElementById('statusMessage');
        this.detectionResults = document.getElementById('detectionResults');
        this.spoofingStatus = document.getElementById('spoofingStatus');
        
        this.startBtn.addEventListener('click', () => this.startCamera());
        this.captureBtn.addEventListener('click', () => this.performFaceRecognition());
        this.stopBtn.addEventListener('click', () => this.stopCamera());
    }
    
    async loadModels() {
        try {
            this.showStatus('Memuat model AI...', 'info');
            
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri('/models'),
                faceapi.nets.faceLandmark68Net.loadFromUri('/models'),
                faceapi.nets.faceRecognitionNet.loadFromUri('/models'),
                faceapi.nets.faceExpressionNet.loadFromUri('/models')
            ]);
            
            this.isModelLoaded = true;
            this.showStatus('Model AI berhasil dimuat', 'success');
            this.startBtn.disabled = false;
            
        } catch (error) {
            console.error('Error loading models:', error);
            this.showStatus('Gagal memuat model AI', 'error');
        }
    }
    
    async startCamera() {
        try {
            this.showStatus('Memulai kamera...', 'info');
            
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
                
                this.showStatus('Kamera aktif - Posisikan wajah Anda', 'success');
                this.startDetection();
            };
            
        } catch (error) {
            console.error('Error starting camera:', error);
            this.showStatus('Gagal mengakses kamera', 'error');
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
                
                // Anti-spoofing check
                const antiSpoofingScore = await this.performAntiSpoofingCheck(detection);
                this.updateAntiSpoofingStatus(antiSpoofingScore);
                
                // Enable capture if face is detected and passes anti-spoofing
                this.captureBtn.disabled = antiSpoofingScore < 0.7;
                
                if (antiSpoofingScore >= 0.7) {
                    this.showStatus('Wajah terdeteksi - Siap untuk scan', 'success');
                } else {
                    this.showStatus('Deteksi gerakan hidup...', 'warning');
                }
                
            } else {
                this.captureBtn.disabled = true;
                this.showStatus('Posisikan wajah Anda di depan kamera', 'info');
                this.spoofingStatus.textContent = 'Scanning...';
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
    
    async performAntiSpoofingCheck(detection) {
        // Simple anti-spoofing based on face expressions and movement
        const expressions = detection.expressions;
        
        // Store current frame data for movement analysis
        this.antiSpoofingFrames.push({
            timestamp: Date.now(),
            expressions: expressions,
            landmarks: detection.landmarks
        });
        
        if (this.antiSpoofingFrames.length > this.maxFrames) {
            this.antiSpoofingFrames.shift();
        }
        
        // Calculate movement and expression variance
        let movementScore = 0;
        let expressionScore = 0;
        
        if (this.antiSpoofingFrames.length >= 5) {
            // Check for natural micro-movements in landmarks
            const firstFrame = this.antiSpoofingFrames[0];
            const lastFrame = this.antiSpoofingFrames[this.antiSpoofingFrames.length - 1];
            
            // Calculate landmark movement
            const movement = this.calculateLandmarkMovement(firstFrame.landmarks, lastFrame.landmarks);
            movementScore = Math.min(movement * 10, 1.0);
            
            // Check for expression changes (natural blinking, micro-expressions)
            expressionScore = this.calculateExpressionVariance();
        }
        
        // Combine scores (movement is more important for anti-spoofing)
        const antiSpoofingScore = (movementScore * 0.7) + (expressionScore * 0.3);
        
        return Math.min(antiSpoofingScore, 1.0);
    }
    
    calculateLandmarkMovement(landmarks1, landmarks2) {
        if (!landmarks1 || !landmarks2) return 0;
        
        const points1 = landmarks1.positions;
        const points2 = landmarks2.positions;
        
        let totalMovement = 0;
        for (let i = 0; i < Math.min(points1.length, points2.length); i++) {
            const dx = points1[i].x - points2[i].x;
            const dy = points1[i].y - points2[i].y;
            totalMovement += Math.sqrt(dx * dx + dy * dy);
        }
        
        return totalMovement / points1.length;
    }
    
    calculateExpressionVariance() {
        if (this.antiSpoofingFrames.length < 3) return 0;
        
        const expressions = ['neutral', 'happy', 'sad', 'angry', 'fearful', 'disgusted', 'surprised'];
        let totalVariance = 0;
        
        expressions.forEach(expr => {
            const values = this.antiSpoofingFrames.map(frame => frame.expressions[expr]);
            const variance = this.calculateVariance(values);
            totalVariance += variance;
        });
        
        return Math.min(totalVariance * 5, 1.0);
    }
    
    calculateVariance(values) {
        const mean = values.reduce((a, b) => a + b, 0) / values.length;
        const variance = values.reduce((a, b) => a + Math.pow(b - mean, 2), 0) / values.length;
        return variance;
    }
    
    updateAntiSpoofingStatus(score) {
        const indicator = document.getElementById('antiSpoofingIndicator');
        const status = this.spoofingStatus;
        
        if (score >= 0.7) {
            indicator.className = 'absolute top-4 right-4';
            indicator.innerHTML = `
                <div class="bg-green-600 text-white px-3 py-1 rounded-full text-xs font-medium flex items-center">
                    <i class="fas fa-shield-alt mr-1"></i>
                    <span>Live Person</span>
                </div>
            `;
        } else if (score >= 0.4) {
            indicator.className = 'absolute top-4 right-4';
            indicator.innerHTML = `
                <div class="bg-yellow-600 text-white px-3 py-1 rounded-full text-xs font-medium flex items-center">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <span>Verifying...</span>
                </div>
            `;
        } else {
            indicator.className = 'absolute top-4 right-4';
            indicator.innerHTML = `
                <div class="bg-red-600 text-white px-3 py-1 rounded-full text-xs font-medium flex items-center">
                    <i class="fas fa-times mr-1"></i>
                    <span>Suspicious</span>
                </div>
            `;
        }
    }
    
    async performFaceRecognition() {
        try {
            this.showStatus('Melakukan pengenalan wajah...', 'info');
            
            // Capture current frame
            this.ctx.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);
            const imageData = this.canvas.toDataURL('image/jpeg', 0.8);
            
            // Get face descriptor
            const detections = await faceapi.detectSingleFace(
                this.video,
                new faceapi.TinyFaceDetectorOptions()
            ).withFaceLandmarks().withFaceDescriptor();
            
            if (!detections) {
                this.showStatus('Wajah tidak terdeteksi', 'error');
                return;
            }
            
            // Perform anti-spoofing final check
            const antiSpoofingScore = await this.performAntiSpoofingCheck(detections);
            
            if (antiSpoofingScore < 0.7) {
                this.showStatus('Gagal verifikasi anti-spoofing', 'error');
                this.showResults({
                    success: false,
                    message: 'Deteksi foto/video. Gunakan wajah asli.',
                    antiSpoofingScore: antiSpoofingScore
                });
                return;
            }
            
            // Send to backend for recognition
            const response = await fetch('/api/face-recognition/authenticate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    face_data: imageData,
                    anti_spoofing_score: antiSpoofingScore,
                    confidence_threshold: 0.7
                })
            });
            
            const result = await response.json();
            this.showResults(result);
            
        } catch (error) {
            console.error('Recognition error:', error);
            this.showStatus('Gagal melakukan pengenalan wajah', 'error');
        }
    }
    
    showResults(result) {
        const resultsDiv = this.detectionResults;
        
        if (result.success) {
            resultsDiv.innerHTML = `
                <div class="text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-2xl text-green-600"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Berhasil!</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">${result.message}</p>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span>Confidence:</span>
                            <span class="font-medium">${Math.round((result.confidence || 0) * 100)}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Anti-Spoofing:</span>
                            <span class="font-medium text-green-600">${Math.round((result.antiSpoofingScore || 0) * 100)}%</span>
                        </div>
                        ${result.type ? `
                        <div class="flex justify-between">
                            <span>Type:</span>
                            <span class="font-medium">${result.type}</span>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
        } else {
            resultsDiv.innerHTML = `
                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-times-circle text-2xl text-red-600"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Gagal</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">${result.message}</p>
                    <div class="space-y-2 text-sm">
                        ${result.confidence ? `
                        <div class="flex justify-between">
                            <span>Confidence:</span>
                            <span class="font-medium">${Math.round(result.confidence * 100)}%</span>
                        </div>
                        ` : ''}
                        ${result.antiSpoofingScore ? `
                        <div class="flex justify-between">
                            <span>Anti-Spoofing:</span>
                            <span class="font-medium ${result.antiSpoofingScore >= 0.7 ? 'text-green-600' : 'text-red-600'}">${Math.round(result.antiSpoofingScore * 100)}%</span>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
        }
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
        this.stopBtn.classList.add('hidden');
        
        this.showStatus('Kamera dihentikan', 'info');
        
        // Reset results
        this.detectionResults.innerHTML = `
            <div class="text-center text-gray-500 dark:text-gray-400">
                <i class="fas fa-search text-4xl mb-4"></i>
                <p>Belum ada hasil deteksi</p>
            </div>
        `;
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

// Initialize Face ID System when page loads
document.addEventListener('DOMContentLoaded', function() {
    new FaceIDSystem();
});
</script>
    </div>
</body>
</html>
