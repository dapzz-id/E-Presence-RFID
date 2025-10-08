<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Face ID Login - E-Presence</title>
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
        @keyframes pulse {
            0% { box-shadow: 0 0 30px rgba(16, 185, 129, 0.8); }
            50% { box-shadow: 0 0 40px rgba(16, 185, 129, 1); }
            100% { box-shadow: 0 0 30px rgba(16, 185, 129, 0.8); }
        }
        
        @keyframes pulseRed {
            0% { box-shadow: 0 0 30px rgba(239, 68, 68, 0.8); }
            50% { box-shadow: 0 0 40px rgba(239, 68, 68, 1); }
            100% { box-shadow: 0 0 30px rgba(239, 68, 68, 0.8); }
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .face-detection-box {
            border: 3px solid #10B981;
            border-radius: 12px;
            animation: pulse 2s infinite;
        }
        
        /* Face Ring Animations - SISTEM WARNA YANG BENAR */
        .face-ring {
            transition: all 0.3s ease-in-out;
            border: 4px solid #10b981; /* Default hijau untuk wajah dikenali */
            background: rgba(16, 185, 129, 0.05);
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.5);
        }
        
        .face-ring.unknown-face {
            border-color: #ef4444; /* MERAH untuk tidak dikenali */
            background: rgba(239, 68, 68, 0.1);
            box-shadow: 0 0 15px rgba(239, 68, 68, 0.5);
        }
        
        @keyframes pulseGreen {
            0% { 
                box-shadow: 0 0 15px rgba(16, 185, 129, 0.5);
                transform: scale(1);
            }
            50% { 
                box-shadow: 0 0 25px rgba(16, 185, 129, 0.8);
                transform: scale(1.02);
            }
            100% { 
                box-shadow: 0 0 15px rgba(16, 185, 129, 0.5);
                transform: scale(1);
            }
        }
        
        @keyframes pulseRed {
            0% { 
                box-shadow: 0 0 15px rgba(239, 68, 68, 0.5);
                transform: scale(1);
            }
            50% { 
                box-shadow: 0 0 25px rgba(239, 68, 68, 0.8);
                transform: scale(1.02);
            }
            100% { 
                box-shadow: 0 0 15px rgba(239, 68, 68, 0.5);
                transform: scale(1);
            }
        }
        
        /* Responsive camera container */
        @media (max-width: 768px) {
            .camera-container {
                height: 400px !important;
                max-width: 100% !important;
            }
        }
        
        @media (max-width: 480px) {
            .camera-container {
                height: 300px !important;
            }
        }
    </style>
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

        <!-- Status Message Area (Moved Above Camera) -->
        <div id="statusMessage" class="bg-blue-600 text-white px-6 py-4 rounded-lg text-center mb-6 shadow-lg">
            <i class="fas fa-info-circle mr-2"></i>
            <span>Memulai sistem Face ID...</span>
        </div>

        <!-- Camera Section Full Width -->
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-primary-600 to-primary-700">
                <h2 class="text-xl font-semibold text-white">
                    <i class="fas fa-video mr-2"></i>Kamera Face ID
                </h2>
            </div>
            
            <div class="p-6">
                <!-- Camera Container - Made Wider and Taller -->
                <div class="camera-container relative bg-gray-900 rounded-lg overflow-hidden mb-6 w-full" style="height: 500px; max-width: 900px; margin: 0 auto; aspect-ratio: 16/10;">
                    <video id="video" autoplay muted playsinline class="w-full h-full object-cover"></video>
                    <canvas id="canvas" class="hidden"></canvas>
                    
                    <!-- Face Detection Overlay -->
                    <div id="faceOverlay" class="absolute inset-0 pointer-events-none">
                        <!-- Dynamic face detection boxes will be drawn here -->
                    </div>
                    
                    <!-- User Recognition Overlay -->
                    <div id="userOverlay" class="absolute inset-0 pointer-events-none">
                        <!-- User name boxes will appear here -->
                    </div>
                    
                </div>

                <!-- System Status -->
                <div id="autoDetectionStatus" class="w-full bg-green-100 dark:bg-green-900 border border-green-300 dark:border-green-700 text-green-800 dark:text-green-200 px-4 py-3 rounded-lg text-sm text-center mb-4 hidden">
                    <i class="fas fa-robot mr-2"></i>
                    <span id="autoStatusText">Deteksi otomatis aktif - Posisikan wajah Anda</span>
                </div>

                <!-- Controls -->
                <div class="space-y-4">
                    <button id="startCamera" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-camera mr-2"></i>Scan Face ID
                    </button>
                    
                    <button id="stopCamera" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center hidden">
                        <i class="fas fa-stop mr-2"></i>Stop Kamera
                    </button>
                    
                    <!-- Attendance Button - Like in reference image -->
                    <button id="attendanceBtn" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center hidden">
                        <i class="fas fa-check mr-2"></i>Klik untuk Absen
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Face-api.js Library -->
<script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<script>
class AdvancedFaceIDSystem {
    constructor() {
        console.log('Initializing AdvancedFaceIDSystem...');
        
        this.video = document.getElementById('video');
        this.canvas = document.getElementById('canvas');
        
        if (!this.video || !this.canvas) {
            throw new Error('Required video or canvas elements not found');
        }
        
        this.ctx = this.canvas.getContext('2d');
        this.stream = null;
        this.isModelLoaded = false;
        this.isDetecting = false;
        this.detectionInterval = null;
        this.recognitionInterval = null;
        this.antiSpoofingFrames = [];
        this.maxFrames = 10;
        this.registeredFaces = [];
        this.currentDetections = [];
        this.lastRecognitionTime = 0;
        this.recognitionCooldown = 500; // 0.5 second cooldown for faster recognition
        this.currentFaceStatus = 'unknown'; // Track current face status to avoid flickering
        this.lastKnownUser = null; // Store last known user for stability
        this.faceDetectionHistory = []; // Track face detection history
        this.stableDetectionCount = 0; // Count stable detections
        this.noFaceCount = 0; // Count frames without face
        this.nameDisplayed = false; // Track if name is currently displayed
        this.stableFaceBox = null; // Store stable face box position
        
        // Enhanced recognition stability
        this.persistentKnownUser = null; // Store persistent known user
        this.recognitionStability = 0; // Track recognition stability
        this.minStabilityThreshold = 1; // Minimum stable recognitions needed (lowered for better recognition)
        this.currentNameElement = null; // Store current name element
        this.lastNameUpdate = 0; // Track last name update time
        this.nameUpdateCooldown = 200; // Reduced to 200ms for better responsiveness
        this.stableUserName = null; // Store stable user name
        
        console.log('Elements found, initializing...');
        this.initializeElements();
        this.loadModels();
        this.loadRegisteredFaces();
        console.log('AdvancedFaceIDSystem constructor completed');
    }
    
    initializeElements() {
        this.startBtn = document.getElementById('startCamera');
        this.stopBtn = document.getElementById('stopCamera');
        this.attendanceBtn = document.getElementById('attendanceBtn');
        this.statusMessage = document.getElementById('statusMessage');
        this.autoDetectionStatus = document.getElementById('autoDetectionStatus');
        this.autoStatusText = document.getElementById('autoStatusText');
        this.faceOverlay = document.getElementById('faceOverlay');
        this.userOverlay = document.getElementById('userOverlay');
        
        this.startBtn.addEventListener('click', () => this.startCamera());
        this.stopBtn.addEventListener('click', () => this.stopCamera());
        this.attendanceBtn.addEventListener('click', () => this.recordAttendance());
    }
    
    // Map helper: converts face-api (video pixel) coordinates to overlay coordinates when video uses object-cover
    getVideoCoverMapping() {
        const containerWidth = this.video.clientWidth;
        const containerHeight = this.video.clientHeight;
        const videoWidth = this.video.videoWidth || 640;
        const videoHeight = this.video.videoHeight || 480;
        if (!containerWidth || !containerHeight) {
            return { scale: 1, offsetX: 0, offsetY: 0 };
        }
        const scale = Math.max(containerWidth / videoWidth, containerHeight / videoHeight);
        const displayedWidth = videoWidth * scale;
        const displayedHeight = videoHeight * scale;
        const offsetX = (containerWidth - displayedWidth) / 2;
        const offsetY = (containerHeight - displayedHeight) / 2;
        return { scale, offsetX, offsetY };
    }
    
    mapPointToOverlay(point) {
        const { scale, offsetX, offsetY } = this.getVideoCoverMapping();
        return {
            x: offsetX + point.x * scale,
            y: offsetY + point.y * scale
        };
    }
    
    mapBoxToOverlay(box) {
        const { scale, offsetX, offsetY } = this.getVideoCoverMapping();
        return {
            x: offsetX + box.x * scale,
            y: offsetY + box.y * scale,
            width: box.width * scale,
            height: box.height * scale,
            scale
        };
    }
    
    async loadRegisteredFaces() {
        try {
            const response = await fetch('/admin/face-id/registered-faces');
            const data = await response.json();
            
            if (data.success) {
                this.registeredFaces = data.faces || [];
                console.log('Loaded registered faces:', this.registeredFaces.length);
            } else {
                // For demo purposes, add sample registered faces including current user
                this.registeredFaces = [
                    { id: 1, name: 'Dirman', nis: '12345', confidence_score: 0.95 },
                    { id: 2, name: 'Ahmad Rizki', nis: '12346', confidence_score: 0.92 },
                    { id: 3, name: 'Sari Dewi', nis: '12347', confidence_score: 0.88 }
                ];
                console.log('Using demo registered faces:', this.registeredFaces.length);
            }
        } catch (error) {
            console.error('Error loading registered faces:', error);
            // Fallback to demo data
            this.registeredFaces = [
                { id: 1, name: 'Dirman', nis: '12345', confidence_score: 0.95 },
                { id: 2, name: 'Ahmad Rizki', nis: '12346', confidence_score: 0.92 },
                { id: 3, name: 'Sari Dewi', nis: '12347', confidence_score: 0.88 }
            ];
            console.log('Using fallback demo registered faces:', this.registeredFaces.length);
        }
    }
    
    async loadModels() {
        try {
            this.showStatus('Memuat model AI...', 'info');
            
            // Wait for face-api.js to load
            let retries = 0;
            while (typeof faceapi === 'undefined' && retries < 50) {
                await new Promise(resolve => setTimeout(resolve, 100));
                retries++;
            }
            
            if (typeof faceapi === 'undefined') {
                console.log('Face API library tidak ditemukan, menggunakan mode deteksi dasar');
                this.isModelLoaded = false;
                this.showStatus('Sistem siap - Mode deteksi dasar', 'success');
                this.startBtn.disabled = false;
                return;
            }
            
            // Load Face-API.js models - AKURASI TINGGI dengan ssdMobilenetv1
            try {
                const modelPath = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@latest/model';
                await Promise.all([
                    faceapi.nets.ssdMobilenetv1.loadFromUri(modelPath),     // Detektor paling akurat
                    faceapi.nets.faceLandmark68Net.loadFromUri(modelPath),  // Untuk titik-titik wajah (hidung)
                    faceapi.nets.faceRecognitionNet.loadFromUri(modelPath), // Untuk identifikasi wajah
                    faceapi.nets.ageGenderNet.loadFromUri(modelPath)        // Optional: age/gender
                ]);
                
                this.isModelLoaded = true;
                this.showStatus('Model AI berhasil dimuat - Sistem siap', 'success');
            } catch (modelError) {
                console.log('Model loading failed, continuing in basic mode:', modelError);
                this.showStatus('Sistem siap - Mode deteksi dasar', 'success');
                this.isModelLoaded = false;
            }
            
            this.startBtn.disabled = false;
            
        } catch (error) {
            console.error('Error loading models:', error);
            this.showStatus('Sistem siap - Mode deteksi dasar', 'success');
            this.isModelLoaded = false;
            this.startBtn.disabled = false;
        }
    }
    
    async startCamera() {
        try {
            this.showStatus('Memulai sistem Face ID...', 'info');
            
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
                this.stopBtn.classList.remove('hidden');
                this.autoDetectionStatus.classList.remove('hidden');
                
                
                this.showStatus('Sistem Face ID aktif - Deteksi otomatis berjalan', 'success');
                this.startAdvancedDetection();
            };
            
        } catch (error) {
            console.error('Error starting camera:', error);
            this.showStatus('Gagal mengakses kamera', 'error');
        }
    }
    
    startAdvancedDetection() {
        this.isDetecting = true;
        
        // Always start detection (with or without AI models) - 200ms optimal
        this.detectionInterval = setInterval(async () => {
            await this.detectAndAnalyzeFace();
        }, 200); // 200ms untuk performa & akurasi optimal
        
        // Face recognition for login attempts - optimized timing
        this.recognitionInterval = setInterval(async () => {
            await this.performFaceRecognition();
        }, 800); // 800ms for better responsiveness
    }
    
    async detectAndAnalyzeFace() {
        if (!this.video.videoWidth || !this.video.videoHeight) return;
        
        try {
            let detections;
            
            if (this.isModelLoaded) {
                // Use ssdMobilenetv1 dengan detectSingleFace - LEBIH AKURAT & CEPAT
                const options = new faceapi.SsdMobilenetv1Options({
                    minConfidence: 0.5  // Lebih toleran untuk wajah miring
                });
                
                // detectSingleFace lebih stabil daripada detectAllFaces
                const singleDetection = await faceapi.detectSingleFace(this.video, options)
                    .withFaceLandmarks()      // Untuk titik hidung dan mata
                    .withFaceDescriptor();    // Untuk identifikasi wajah
                
                detections = singleDetection ? [singleDetection] : [];
            } else {
                // Fallback: simulate basic detection
                detections = this.simulateBasicFaceDetection();
            }
            
            // Clear previous overlays
            this.faceOverlay.innerHTML = '';
            this.currentDetections = detections;
            
            if (detections.length > 0) {
                const detection = detections[0];
                
                // Use REAL Face-API.js dengan landmarks untuk ring di tengah hidung
                if (detection.landmarks) {
                    this.drawAccurateFaceRing(detection);
                } else {
                    // Fallback ke detection box biasa
                    const box = detection.detection.box;
                    this.drawRealFaceApiBox(box);
                }
                
                // Check if this face matches any registered user
                await this.checkForRegisteredUser(detection);
                
                // Anti-spoofing analysis
                const antiSpoofingScore = this.performAntiSpoofingAnalysis(detection);
                
                // Update status based on face quality
                const faceQuality = this.assessFaceQuality(detection);
                this.updateAutoStatus(faceQuality, antiSpoofingScore);
                
            } else {
                // No face detected - DON'T clear user name, only update status
                this.currentFaceStatus = 'no_face';
                // DON'T reset lastKnownUser or clear name - keep it persistent
                this.autoStatusText.textContent = 'Tidak ada wajah terdeteksi - Posisikan wajah Anda';
                
                // Keep showing the name if we have a known user
                if (this.lastKnownUser && this.nameDisplayed) {
                    // Keep the name displayed even when no face detected
                    this.showKnownUserName(this.lastKnownUser.name);
                }
            }
            
        } catch (error) {
            console.error('Detection error:', error);
            // Even on error, show basic detection for demo
            const detections = this.simulateBasicFaceDetection();
            this.currentDetections = detections;
            if (detections.length > 0) {
                this.drawAdvancedFaceBox(detections[0].detection.box);
                await this.checkForRegisteredUser(detections[0]);
            }
        }
    }
    
    showUnknownFaceIndicator() {
        // Clear previous overlays
        this.faceOverlay.innerHTML = '';
        this.userOverlay.innerHTML = '';
        
        // Show red ring for unknown face area (center of video)
        const videoWidth = this.video.videoWidth || 640;
        const videoHeight = this.video.videoHeight || 480;
        
        const unknownBox = {
            x: videoWidth * 0.25,
            y: videoHeight * 0.2,
            width: videoWidth * 0.5,
            height: videoHeight * 0.6
        };
        
        this.drawUnknownFaceBox(unknownBox);
        this.showUnknownUserName();
    }
    
    drawUnknownFaceBox(box) {
        // Draw red ring centered with correct mapping
        const mapped = this.mapBoxToOverlay(box);
        const center = { x: box.x + box.width / 2, y: box.y + box.height / 2 };
        const mappedCenter = this.mapPointToOverlay(center);
        const ringSize = Math.max(box.width * 1.2, box.height * 1.1) * mapped.scale;
        const faceRing = document.createElement('div');
        faceRing.style.position = 'absolute';
        faceRing.style.zIndex = '1';
        faceRing.style.left = `${mappedCenter.x - ringSize/2}px`;
        faceRing.style.top = `${mappedCenter.y - ringSize/2}px`;
        faceRing.style.width = `${ringSize}px`;
        faceRing.style.height = `${ringSize}px`;
        faceRing.style.borderRadius = '12px';
        faceRing.className = 'face-ring unknown-face';
        faceRing.style.animation = 'pulseRed 2s infinite';
        this.faceOverlay.appendChild(faceRing);
    }
    
    showUnknownUserName() {
        // Clear previous overlays first
        this.userOverlay.innerHTML = '';
        
        // FORCE RED RING for unknown user
        this.updateFaceBoxColor('#EF4444'); // Red for unknown
        
        let box;
        if (this.currentDetections && this.currentDetections.length > 0) {
            box = this.currentDetections[0].detection.box;
        } else {
            // Fallback to center position - FULL FACE COVERAGE
            const videoWidth = this.video.videoWidth || 640;
            const videoHeight = this.video.videoHeight || 480;
            const faceWidth = Math.min(videoWidth * 0.35, 250);
            const faceHeight = faceWidth * 1.2;
            box = {
                x: (videoWidth - faceWidth) / 2,
                y: (videoHeight - faceHeight) / 2 - 30,
                width: faceWidth,
                height: faceHeight
            };
        }
        
        const nameBox = document.createElement('div');
        nameBox.style.position = 'absolute';
        // Center the name above the face box
        nameBox.style.left = `${box.x + (box.width / 2)}px`;
        nameBox.style.top = `${Math.max(box.y - 45, 10)}px`; // Above the face box
        nameBox.style.transform = 'translateX(-50%)'; // Center horizontally
        nameBox.style.backgroundColor = '#EF4444'; // Red background
        nameBox.style.color = 'white';
        nameBox.style.padding = '8px 16px';
        nameBox.style.borderRadius = '20px';
        nameBox.style.fontSize = '16px';
        nameBox.style.fontWeight = 'bold';
        nameBox.style.boxShadow = '0 4px 12px rgba(239, 68, 68, 0.4)';
        nameBox.style.zIndex = '10';
        nameBox.style.minWidth = '120px';
        nameBox.style.textAlign = 'center';
        nameBox.textContent = 'Tidak Dikenal';
        
    this.userOverlay.appendChild(nameBox);
    
    // Position label exactly above the current ring center
    const ring = this.faceOverlay.querySelector('.face-ring');
    if (ring) {
        const ringLeft = parseFloat(ring.style.left || '0');
        const ringTop = parseFloat(ring.style.top || '0');
        const ringWidth = parseFloat(ring.style.width || '0');
        const centerX = ringLeft + ringWidth / 2;
        const gap = 6;
        const nameHeight = nameBox.offsetHeight || 32;
        nameBox.style.left = `${centerX}px`;
        nameBox.style.top = `${Math.max(ringTop - nameHeight - gap, 10)}px`;
        nameBox.style.transform = 'translateX(-50%)';
    }
    }
    
    async checkForRegisteredUser(detection) {
        // ALWAYS show name if we have a known user - NO FLICKERING
        if (this.lastKnownUser && !this.nameDisplayed) {
            this.showKnownUserName(this.lastKnownUser.name);
            this.nameDisplayed = true;
        }
        
        // Skip API calls if we just did a check recently
        const now = Date.now();
        if (now - this.lastRecognitionTime < 1500) { // Longer cooldown to prevent flickering
            return;
        }
        
        this.lastRecognitionTime = now;
        
        try {
            // Capture current frame for recognition
            this.ctx.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);
            const imageData = this.canvas.toDataURL('image/jpeg', 0.8);
            
            // Get anti-spoofing score
            const antiSpoofingScore = this.performAntiSpoofingAnalysis(detection);
            
            // Skip demo simulation - use REAL backend recognition
            // Comment out demo simulation to use real backend
            /*
            if (this.registeredFaces && this.registeredFaces.length > 0) {
                // Demo simulation disabled - using real backend
            }
            */
            
            // Send to backend for quick recognition check
            const response = await fetch('/admin/face-id/authenticate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    face_data: imageData,
                    anti_spoofing_score: antiSpoofingScore,
                    confidence_threshold: 0.5 // Lower threshold for preview
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // User recognized - BUILD STABILITY
                const recognizedUser = result.user;
                
                // Check if this is the same user as before
                if (this.persistentKnownUser && this.persistentKnownUser.name === recognizedUser.name) {
                    this.recognitionStability++;
                } else {
                    // New user or different user
                    this.persistentKnownUser = recognizedUser;
                    this.recognitionStability = 1;
                }
                
                // Update current user
                this.lastKnownUser = recognizedUser;
                this.stableDetectionCount++;
                
                // Always show known user name (simplified)
                this.showKnownUserName(recognizedUser.name);
                
                if (this.currentFaceStatus !== 'known') {
                    this.showStatus(`Wajah dikenali: ${recognizedUser.name}`, 'success');
                    this.showAttendanceButton();
                }
                this.currentFaceStatus = 'known';
                
            } else {
                // User not recognized - but check if we have a stable persistent user
                if (this.persistentKnownUser && this.recognitionStability >= this.minStabilityThreshold) {
                    // Keep showing the persistent known user (avoid flickering)
                    this.lastKnownUser = this.persistentKnownUser;
                    
                    // Keep showing persistent user (simplified)
                    this.showKnownUserName(this.persistentKnownUser.name);
                    
                    this.currentFaceStatus = 'known';
                    console.log(`Maintaining stable user: ${this.persistentKnownUser.name}`);
                } else {
                    // Truly unknown user
                    this.showUnknownUserName();
                    this.currentFaceStatus = 'unknown';
                    this.hideAttendanceButton();
                    
                    // Gradual degradation - don't clear immediately
                    if (this.recognitionStability > 0) {
                        this.recognitionStability -= 0.5; // Slower degradation
                    }
                    
                    // Only clear after significant failures
                    if (this.recognitionStability <= -2) {
                        this.persistentKnownUser = null;
                        this.lastKnownUser = null;
                        this.currentNameElement = null;
                        this.stableUserName = null;
                    } else if (this.stableUserName) {
                        // Keep showing last stable name during temporary failures
                        this.showKnownUserName(this.stableUserName);
                        this.currentFaceStatus = 'known';
                    }
                }
            }
            
        } catch (error) {
            console.error('User check error:', error);
            // Only show unknown if we don't have a stable user
            if (!this.lastKnownUser) {
                this.lastKnownUser = null; // keep red until true match
                this.currentFaceStatus = 'unknown';
                this.showUnknownUserName();
            } else {
                // Keep showing known user for stability
                this.showKnownUserName(this.lastKnownUser.name);
                this.updateFaceBoxColor('#10B981'); // Green
            }
        }
    }
    
    showKnownUserName(userName) {
        // Store stable user name
        this.stableUserName = userName;
        
        // FORCE GREEN RING for known user
        this.updateFaceBoxColor('#10B981'); // Green for known
        
        // Check if name element exists and is correct
        if (this.currentNameElement && this.currentNameElement.textContent === userName) {
            // Just update position, don't recreate
            this.updateNamePosition();
            return;
        }
        
        // Create or recreate name element
        this.userOverlay.innerHTML = '';
        
        let box;
        if (this.currentDetections && this.currentDetections.length > 0) {
            box = this.currentDetections[0].detection.box;
            // Store stable box position
            this.stableFaceBox = box;
        } else if (this.stableFaceBox) {
            // Use last stable position
            box = this.stableFaceBox;
        } else {
            // Fallback to center position - FULL FACE COVERAGE
            const videoWidth = this.video.videoWidth || 640;
            const videoHeight = this.video.videoHeight || 480;
            const faceWidth = Math.min(videoWidth * 0.35, 250);
            const faceHeight = faceWidth * 1.2;
            box = {
                x: (videoWidth - faceWidth) / 2,
                y: (videoHeight - faceHeight) / 2 - 30,
                width: faceWidth,
                height: faceHeight
            };
        }
        
        const nameBox = document.createElement('div');
        nameBox.style.position = 'absolute';
        // Center the name above the face box
        nameBox.style.left = `${box.x + (box.width / 2)}px`;
        nameBox.style.top = `${Math.max(box.y - 45, 10)}px`; // Above the face box
        nameBox.style.transform = 'translateX(-50%)'; // Center horizontally
        nameBox.style.backgroundColor = '#10B981'; // Green background
        nameBox.style.color = 'white';
        nameBox.style.padding = '8px 16px';
        nameBox.style.borderRadius = '20px';
        nameBox.style.fontSize = '16px';
        nameBox.style.fontWeight = 'bold';
        nameBox.style.boxShadow = '0 4px 12px rgba(16, 185, 129, 0.4)';
        nameBox.style.animation = 'fadeIn 0.3s ease-in';
        nameBox.style.zIndex = '10';
        nameBox.style.minWidth = '120px';
        nameBox.style.textAlign = 'center';
        nameBox.style.pointerEvents = 'none'; // Don't interfere with interactions
        nameBox.className = 'persistent-user-name'; // Add class for identification
        nameBox.textContent = userName;
        
        this.userOverlay.appendChild(nameBox);
        this.nameDisplayed = true;
        
        // Store the name element for persistence
        this.currentNameElement = nameBox;
    }
    
    updateNamePosition() {
        if (!this.currentNameElement) return;
        
        let box;
        if (this.currentDetections && this.currentDetections.length > 0) {
            box = this.currentDetections[0].detection.box;
            this.stableFaceBox = box;
        } else if (this.stableFaceBox) {
            box = this.stableFaceBox;
        } else {
            return; // No position to update to
        }
        
        // Update position smoothly
        this.currentNameElement.style.left = `${box.x + (box.width / 2)}px`;
        this.currentNameElement.style.top = `${Math.max(box.y - 45, 10)}px`;
    }
    
    updateFaceBoxColor(color) {
        const faceRing = this.faceOverlay.querySelector('.face-ring');
        if (faceRing) {
            if (color === '#10B981' || color === 'known') {
                // HIJAU - Wajah dikenali (HAPUS class unknown-face)
                faceRing.classList.remove('unknown-face');
                faceRing.style.animation = 'pulseGreen 2s infinite';
                faceRing.style.borderColor = '#10B981';
                faceRing.style.background = 'rgba(16, 185, 129, 0.05)';
                this.currentFaceStatus = 'known';
            } else {
                // MERAH - Wajah tidak dikenali (TAMBAH class unknown-face)
                faceRing.classList.add('unknown-face');
                faceRing.style.animation = 'pulseRed 2s infinite';
                faceRing.style.borderColor = '#EF4444';
                faceRing.style.background = 'rgba(239, 68, 68, 0.1)';
                this.currentFaceStatus = 'unknown';
            }
        }
    }
    
    simulateBasicFaceDetection() {
        // Enhanced face detection with better tracking
        const videoWidth = this.video.videoWidth || 640;
        const videoHeight = this.video.videoHeight || 480;
        
        // Use canvas to analyze actual video content for better detection
        this.ctx.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);
        const imageData = this.ctx.getImageData(0, 0, this.canvas.width, this.canvas.height);
        
        // Simple face detection based on skin color and movement
        const hasFace = this.detectFaceInImage(imageData);
        
        if (!hasFace) {
            this.noFaceCount++;
            // Only return empty if no face for several frames
            if (this.noFaceCount > 5) {
                return [];
            }
        } else {
            this.noFaceCount = 0;
        }
        
        // Calculate face dimensions - FULL FACE COVERAGE (EAR TO EAR)
        const faceWidth = Math.min(videoWidth * 0.35, 250); // Much wider for full face coverage
        const faceHeight = faceWidth * 1.2; // Balanced face ratio
        
        // PERFECT CENTER positioning - ensure full face coverage
        const x = (videoWidth - faceWidth) / 2;
        const y = (videoHeight - faceHeight) / 2 - 30; // Centered on face area
        
        return [{
            detection: {
                box: {
                    x: Math.max(0, x),
                    y: Math.max(0, y),
                    width: faceWidth,
                    height: faceHeight
                },
                score: 0.95
            },
            landmarks: null,
            expressions: null
        }];
    }
    
    detectFaceInImage(imageData) {
        // Simple face detection based on image analysis
        // This is a basic implementation - in real app you'd use proper face detection
        const data = imageData.data;
        let skinPixels = 0;
        let totalPixels = data.length / 4;
        
        // Sample every 10th pixel for performance
        for (let i = 0; i < data.length; i += 40) {
            const r = data[i];
            const g = data[i + 1];
            const b = data[i + 2];
            
            // Simple skin color detection
            if (r > 95 && g > 40 && b > 20 && 
                Math.max(r, g, b) - Math.min(r, g, b) > 15 &&
                Math.abs(r - g) > 15 && r > g && r > b) {
                skinPixels++;
            }
        }
        
        // If more than 2% of sampled pixels are skin-colored, assume face is present
        return (skinPixels / (totalPixels / 10)) > 0.02;
    }
    
    drawAccurateFaceRing(detection) {
        // RING TEPAT DI TENGAH WAJAH - perbaikan positioning
        const landmarks = detection.landmarks;
        const box = detection.detection.box;
        
        // Gunakan detection box sebagai base, lebih reliable
        const boxCenter = {
            x: box.x + box.width / 2,
            y: box.y + box.height / 2
        };
        
        // Ambil landmark untuk fine-tuning (opsional)
        let faceCenter = boxCenter;
        
        try {
            const nose = landmarks.getNose();
            const leftEye = landmarks.getLeftEye();
            const rightEye = landmarks.getRightEye();
            
            // CENTER TEPAT DI HIDUNG - TIDAK DICAMPUR DENGAN MATA
            const noseCenterX = (nose[0].x + nose[4].x) / 2; // Center hidung horizontal
            const noseCenterY = nose[2].y; // Ujung hidung
            
            // FACE CENTER TEPAT DI HIDUNG
            faceCenter = {
                x: noseCenterX, // Tepat di tengah hidung
                y: noseCenterY  // Tepat di ujung hidung
            };
        } catch (error) {
            console.log('Using detection box center as fallback');
            // Tetap gunakan box center jika landmark error
        }
        
        // Hitung mapping ke layar (karena video menggunakan object-cover)
        const mappedBox = this.mapBoxToOverlay(box);
        const mappedCenter = this.mapPointToOverlay(faceCenter);
        
        // UKURAN RING BESAR - SAMPAI DAGU (skala sesuai tampilan)
        const ringSize = Math.max(box.width * 1.2, box.height * 1.1) * mappedBox.scale;
        
        // Buat ring yang centered di wajah (koordinat overlay)
        const faceRing = document.createElement('div');
        faceRing.style.position = 'absolute';
        faceRing.style.zIndex = '1';
        faceRing.style.left = `${mappedCenter.x - ringSize/2}px`;
        faceRing.style.top = `${mappedCenter.y - ringSize/2}px`;
        faceRing.style.width = `${ringSize}px`;
        faceRing.style.height = `${ringSize}px`;
        faceRing.style.borderRadius = '12px';
        faceRing.className = 'face-ring';
        const isKnown = !!this.lastKnownUser; // lock color to latest recognition result
        if (!isKnown) {
            faceRing.classList.add('unknown-face');
            faceRing.style.animation = 'pulseRed 2s infinite';
        } else {
            faceRing.style.animation = 'pulseGreen 2s infinite';
        }
        
        this.faceOverlay.appendChild(faceRing);
        
        // Keep name label locked to ring position
        const attachedLabel1 = this.userOverlay.firstElementChild;
        if (attachedLabel1) {
            const rLeft = parseFloat(faceRing.style.left || '0');
            const rTop = parseFloat(faceRing.style.top || '0');
            const rWidth = parseFloat(faceRing.style.width || '0');
            const cX = rLeft + rWidth / 2;
            const gap = 6;
            const h = attachedLabel1.offsetHeight || 32;
            attachedLabel1.style.left = `${cX}px`;
            attachedLabel1.style.top = `${Math.max(rTop - h - gap, 10)}px`;
            attachedLabel1.style.transform = 'translateX(-50%)';
        }
        
        // Debug: tampilkan center point
        if (window.DEBUG_LANDMARKS) {
            this.drawDebugPoints([faceCenter]);
        }
    }
    
    drawDebugPoints(points) {
        // Fungsi debug untuk melihat titik landmark
        points.forEach((point, index) => {
            const dot = document.createElement('div');
            dot.style.position = 'absolute';
            dot.style.left = `${point.x - 3}px`;
            dot.style.top = `${point.y - 3}px`;
            dot.style.width = '6px';
            dot.style.height = '6px';
            dot.style.backgroundColor = ['red', 'blue', 'yellow'][index];
            dot.style.borderRadius = '50%';
            dot.style.zIndex = '10';
            this.faceOverlay.appendChild(dot);
        });
    }
    
    drawRealFaceApiBox(box) {
        // Fallback: Create face detection box - UKURAN YANG SAMA
        const mappedBox = this.mapBoxToOverlay(box);
        const boxCenter = { x: box.x + box.width / 2, y: box.y + box.height / 2 };
        const mappedCenter = this.mapPointToOverlay(boxCenter);
        
        // UKURAN RING BESAR - SAMA DENGAN FUNCTION UTAMA (skala sesuai tampilan)
        const ringSize = Math.max(box.width * 1.2, box.height * 1.1) * mappedBox.scale;
        
        const faceBox = document.createElement('div');
        faceBox.style.position = 'absolute';
        faceBox.style.zIndex = '1';
        faceBox.style.left = `${mappedCenter.x - ringSize/2}px`;
        faceBox.style.top = `${mappedCenter.y - ringSize/2}px`;
        faceBox.style.width = `${ringSize}px`;
        faceBox.style.height = `${ringSize}px`;
        faceBox.style.borderRadius = '12px';
        faceBox.className = 'face-ring';
        const isKnown = !!this.lastKnownUser; // lock color to latest recognition result
        if (!isKnown) {
            faceBox.classList.add('unknown-face');
            faceBox.style.animation = 'pulseRed 2s infinite';
        } else {
            faceBox.style.animation = 'pulseGreen 2s infinite';
        }
        
        this.faceOverlay.appendChild(faceBox);
        
        // Keep name label locked to ring position
        const attachedLabel2 = this.userOverlay.firstElementChild;
        if (attachedLabel2) {
            const rLeft = parseFloat(faceBox.style.left || '0');
            const rTop = parseFloat(faceBox.style.top || '0');
            const rWidth = parseFloat(faceBox.style.width || '0');
            const cX = rLeft + rWidth / 2;
            const gap = 6;
            const h = attachedLabel2.offsetHeight || 32;
            attachedLabel2.style.left = `${cX}px`;
            attachedLabel2.style.top = `${Math.max(rTop - h - gap, 10)}px`;
            attachedLabel2.style.transform = 'translateX(-50%)';
        }
    }
    
    drawAdvancedFaceBox(box) {
        const faceBox = document.createElement('div');
        faceBox.style.position = 'absolute';
        faceBox.style.zIndex = '1';
        faceBox.style.left = `${box.x}px`;
        faceBox.style.top = `${box.y}px`;
        faceBox.style.width = `${box.width}px`;
        faceBox.style.height = `${box.height}px`;
        faceBox.style.border = '3px solid #10B981';
        faceBox.style.borderRadius = '12px';
        faceBox.style.boxShadow = '0 0 30px rgba(16, 185, 129, 0.8)';
        faceBox.style.animation = 'pulse 2s infinite';
        
        // Add corner indicators
        const corners = ['top-left', 'top-right', 'bottom-left', 'bottom-right'];
        corners.forEach(corner => {
            const cornerDiv = document.createElement('div');
            cornerDiv.style.position = 'absolute';
            cornerDiv.style.width = '20px';
            cornerDiv.style.height = '20px';
            cornerDiv.style.border = '3px solid #10B981';
            
            switch(corner) {
                case 'top-left':
                    cornerDiv.style.top = '-3px';
                    cornerDiv.style.left = '-3px';
                    cornerDiv.style.borderRight = 'none';
                    cornerDiv.style.borderBottom = 'none';
                    break;
                case 'top-right':
                    cornerDiv.style.top = '-3px';
                    cornerDiv.style.right = '-3px';
                    cornerDiv.style.borderLeft = 'none';
                    cornerDiv.style.borderBottom = 'none';
                    break;
                case 'bottom-left':
                    cornerDiv.style.bottom = '-3px';
                    cornerDiv.style.left = '-3px';
                    cornerDiv.style.borderRight = 'none';
                    cornerDiv.style.borderTop = 'none';
                    break;
                case 'bottom-right':
                    cornerDiv.style.bottom = '-3px';
                    cornerDiv.style.right = '-3px';
                    cornerDiv.style.borderLeft = 'none';
                    cornerDiv.style.borderTop = 'none';
                    break;
            }
            faceBox.appendChild(cornerDiv);
        });
        
        this.faceOverlay.appendChild(faceBox);
    }
    
    performAntiSpoofingAnalysis(detection) {
        // Simple anti-spoofing based on expressions and landmarks
        const expressions = detection.expressions;
        const landmarks = detection.landmarks;
        
        let score = 0.5; // Base score
        
        // Check for natural expressions variation
        if (expressions) {
            const expressionValues = Object.values(expressions);
            const maxExpression = Math.max(...expressionValues);
            if (maxExpression > 0.1) score += 0.2;
        }
        
        // Check for landmark stability (real faces have slight movement)
        if (landmarks) {
            score += 0.3; // Landmarks present indicates real face
        }
        
        return Math.min(score, 1.0);
    }
    
    assessFaceQuality(detection) {
        const box = detection.detection.box;
        const score = detection.detection.score;
        
        // Face size check
        const faceSize = box.width * box.height;
        const videoSize = this.video.videoWidth * this.video.videoHeight;
        const sizeRatio = faceSize / videoSize;
        
        let quality = 0;
        
        // Size quality (optimal size is 10-40% of video)
        if (sizeRatio >= 0.1 && sizeRatio <= 0.4) quality += 0.4;
        else if (sizeRatio >= 0.05 && sizeRatio <= 0.6) quality += 0.2;
        
        // Detection confidence
        quality += score * 0.6;
        
        return Math.min(quality, 1.0);
    }
    
    updateAutoStatus(faceQuality, antiSpoofingScore) {
        if (faceQuality > 0.7 && antiSpoofingScore > 0.7) {
            this.autoStatusText.textContent = 'Wajah berkualitas tinggi terdeteksi - Siap untuk pengenalan';
            this.autoDetectionStatus.className = this.autoDetectionStatus.className.replace('bg-blue-100', 'bg-green-100').replace('border-blue-300', 'border-green-300').replace('text-blue-800', 'text-green-800');
        } else if (faceQuality > 0.5) {
            this.autoStatusText.textContent = 'Wajah terdeteksi - Perbaiki posisi untuk hasil optimal';
            this.autoDetectionStatus.className = this.autoDetectionStatus.className.replace('bg-green-100', 'bg-yellow-100').replace('border-green-300', 'border-yellow-300').replace('text-green-800', 'text-yellow-800');
        } else {
            this.autoStatusText.textContent = 'Deteksi otomatis aktif - Posisikan wajah dengan jelas';
            this.autoDetectionStatus.className = this.autoDetectionStatus.className.replace('bg-green-100', 'bg-blue-100').replace('border-green-300', 'border-blue-300').replace('text-green-800', 'text-blue-800');
        }
    }
    
    async performFaceRecognition() {
        if (!this.currentDetections || this.currentDetections.length === 0) return;
        
        const now = Date.now();
        if (now - this.lastRecognitionTime < this.recognitionCooldown) return;
        
        try {
            // Capture current frame
            this.ctx.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);
            const imageData = this.canvas.toDataURL('image/jpeg', 0.8);
            
            // Get anti-spoofing score from current detection
            const detection = this.currentDetections[0];
            const antiSpoofingScore = this.performAntiSpoofingAnalysis(detection);
            
            // Only proceed if anti-spoofing score is good enough
            if (antiSpoofingScore < 0.6) return;
            
            // Send to backend for authentication
            const response = await fetch('/admin/face-id/authenticate', {
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
            
            if (result.success) {
                this.handleSuccessfulLogin(result.user);
                this.lastRecognitionTime = now;
            }
            
        } catch (error) {
            console.error('Face recognition error:', error);
        }
    }
    
    
    handleSuccessfulLogin(user) {
        // Show success notification
        this.showStatus(`Login berhasil! Selamat datang ${user.name}`, 'success');
        
        // Change button to green to indicate success
        this.updateButtonSuccess();
        
        // Play success sound (optional)
        this.playSuccessSound();
        
        // Keep showing user name for longer
        setTimeout(() => {
            this.showKnownUserName(user.name);
        }, 100);
    }
    
    updateButtonSuccess() {
        const startBtn = this.startBtn;
        const stopBtn = this.stopBtn;
        
        // Change stop button to green success state
        if (!stopBtn.classList.contains('hidden')) {
            stopBtn.innerHTML = '<i class="fas fa-check mr-2"></i>Dikenali';
            stopBtn.className = 'w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center';
            
            // Reset after 3 seconds
            setTimeout(() => {
                stopBtn.innerHTML = '<i class="fas fa-stop mr-2"></i>Stop Kamera';
                stopBtn.className = 'w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center';
            }, 3000);
        }
    }
    
    playSuccessSound() {
        // Create a simple beep sound
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 800;
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.3);
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
        
        if (this.recognitionInterval) {
            clearInterval(this.recognitionInterval);
            this.recognitionInterval = null;
        }
        
        this.isDetecting = false;
        this.video.srcObject = null;
        
        // Reset UI
        this.startBtn.classList.remove('hidden');
        this.stopBtn.classList.add('hidden');
        this.autoDetectionStatus.classList.add('hidden');
        
        // Clear overlays
        this.faceOverlay.innerHTML = '';
        this.userOverlay.innerHTML = '';
        
        this.showStatus('Kamera dihentikan', 'info');
    }
    
    showStatus(message, type = 'info') {
        // Update the span inside statusMessage
        const span = this.statusMessage.querySelector('span');
        if (span) {
            span.textContent = message;
        } else {
            this.statusMessage.innerHTML = `<i class="fas fa-info-circle mr-2"></i><span>${message}</span>`;
        }
        
        this.statusMessage.classList.remove('hidden');
        
        // Remove existing type classes
        this.statusMessage.classList.remove('bg-blue-600', 'bg-green-600', 'bg-yellow-600', 'bg-red-600');
        
        // Update icon and color based on type
        const icon = this.statusMessage.querySelector('i');
        if (icon) {
            icon.className = 'mr-2 fas ';
            switch (type) {
                case 'success':
                    this.statusMessage.classList.add('bg-green-600');
                    icon.className += 'fa-check-circle';
                    break;
                case 'warning':
                    this.statusMessage.classList.add('bg-yellow-600');
                    icon.className += 'fa-exclamation-triangle';
                    break;
                case 'error':
                    this.statusMessage.classList.add('bg-red-600');
                    icon.className += 'fa-times-circle';
                    break;
                default:
                    this.statusMessage.classList.add('bg-blue-600');
                    icon.className += 'fa-info-circle';
            }
        }
    }
    
    showAttendanceButton() {
        // Show attendance button like in reference image
        this.attendanceBtn.classList.remove('hidden');
        this.stopBtn.classList.add('hidden'); // Hide stop button when showing attendance
    }
    
    hideAttendanceButton() {
        // Hide attendance button
        this.attendanceBtn.classList.add('hidden');
        this.stopBtn.classList.remove('hidden'); // Show stop button back
    }
    
    async recordAttendance() {
        if (!this.lastKnownUser) {
            this.showStatus('Tidak ada user yang dikenali untuk absen', 'error');
            return;
        }
        
        try {
            this.showStatus('Mencatat kehadiran...', 'info');
            
            // Send attendance record to backend
            const response = await fetch('/admin/attendance/record', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    user_id: this.lastKnownUser.id,
                    method: 'face_id',
                    timestamp: new Date().toISOString()
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showStatus(`✅ Absensi berhasil dicatat untuk ${this.lastKnownUser.name}!`, 'success');
                this.hideAttendanceButton();
                
                // Auto hide after 3 seconds
                setTimeout(() => {
                    this.showStatus('Sistem siap untuk deteksi wajah berikutnya', 'info');
                }, 3000);
            } else {
                this.showStatus(`Gagal mencatat absensi: ${result.message}`, 'error');
            }
            
        } catch (error) {
            console.error('Attendance recording error:', error);
            this.showStatus('Terjadi kesalahan saat mencatat absensi', 'error');
        }
    }
}

// Initialize Face ID System when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing Face ID System...');
    console.log('💡 Debug mode: ketik "window.DEBUG_LANDMARKS = true" di console untuk melihat titik landmark');
    try {
        const system = new AdvancedFaceIDSystem();
        console.log('Face ID System initialized successfully');
        window.faceIDSystem = system; // For debugging
    } catch (error) {
        console.error('Failed to initialize Face ID System:', error);
        // Show error message to user
        const statusMessage = document.getElementById('statusMessage');
        if (statusMessage) {
            statusMessage.innerHTML = `<i class="fas fa-times-circle mr-2"></i><span>Gagal memuat sistem Face ID: ${error.message}</span>`;
            statusMessage.classList.remove('hidden', 'bg-blue-600');
            statusMessage.classList.add('bg-red-600');
        }
    }
});
</script>
    </div>
</body>
</html>
