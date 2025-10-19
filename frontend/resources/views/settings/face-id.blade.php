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
            transition: all 0.1s ease-out; /* Faster transition for real-time tracking */
            border: 4px solid #10b981; /* Default hijau untuk wajah dikenali */
            background: rgba(16, 185, 129, 0.05);
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.5);
        }
        
        /* Smooth name label transitions */
        .user-name-label {
            transition: all 0.1s ease-out; /* Fast smooth movement */
            will-change: transform; /* Optimize for animations */
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
                Sistem absensi otomatis menggunakan pengenalan wajah. Scan pertama untuk absen masuk, scan kedua untuk absen keluar.
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
                    <span id="autoStatusText">🤖 Deteksi otomatis aktif - Posisikan wajah Anda di depan kamera</span>
                </div>

                <!-- Controls -->
                <div class="space-y-4">
                    <button id="startCamera" class="w-full bg-primary-600 hover:bg-primary-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center">
                        <i class="fas fa-camera mr-2"></i>Mulai Scan Wajah
                    </button>
                    
                    <button id="stopCamera" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center hidden">
                        <i class="fas fa-stop mr-2"></i>Hentikan Kamera
                    </button>
                    
                    <!-- Attendance Button - Like in reference image -->
                    <button id="attendanceBtn" class="w-full bg-green-600 hover:bg-green-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center hidden">
                        <i class="fas fa-check mr-2"></i>Konfirmasi Absensi
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Face-api.js Library -->
<script defer src="https://cdn.jsdelivr.net/npm/face-api.js@0.22.2/dist/face-api.min.js"></script>

<script>
class SimpleFaceIDSystem {
    constructor() {
        console.log('Initializing SimpleFaceIDSystem...');
        
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
        
        // Simplified state management
        this.currentUser = null; // Currently recognized user
        this.faceDetected = false; // Is face currently detected
        this.lastRecognitionTime = 0;
        this.recognitionCooldown = 2000; // 2 second cooldown for stability
        this.isLoggingIn = false; // Prevent multiple login attempts
        
        // Training data storage (9 photos per user)
        this.trainingData = new Map(); // userId -> [descriptors]
        this.knownUsers = []; // Will be loaded from database
        
        // UI state
        this.currentRing = null;
        this.currentNameLabel = null;
        
        console.log('Elements found, initializing...');
        this.initializeElements();
        this.initializeSystem();
        console.log('SimpleFaceIDSystem constructor completed');
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
    
    async initializeSystem() {
        // Load models first
        await this.loadModels();
        
        // Then load users from database
        await this.loadKnownUsersFromDatabase();
        
        console.log('System initialization completed');
    }
    
    // Generate training data for 9 photos per user (simulated)
    async loadKnownUsersFromDatabase() {
        try {
            console.log('Loading users from database...');
            
            // Fetch users with face data from Laravel backend
            const response = await fetch('/admin/api/face-id/users', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                this.knownUsers = data.users || [];
                console.log(`✅ Loaded ${this.knownUsers.length} users from database:`, this.knownUsers);
                
                if (this.knownUsers.length === 0) {
                    console.log('⚠️ NO USERS FOUND IN DATABASE - All faces will be UNKNOWN (RED RING)');
                } else {
                    console.log('👥 Users available for recognition:', this.knownUsers.map(u => u.name));
                    
                    // Generate training data from actual photos
                    await this.generateTrainingData();
                }
            } else {
                console.log('❌ API Error - No users found in database or API error');
                console.log('Response status:', response.status);
                this.knownUsers = [];
                this.trainingData.clear();
            }
        } catch (error) {
            console.error('Error loading users from database:', error);
            console.log('Using empty user list - all faces will be unknown');
            this.knownUsers = [];
            this.trainingData.clear();
        }
    }

    async generateTrainingData() {
        console.log('🔄 Generating training data from actual photos...');
        
        if (this.knownUsers.length === 0) {
            console.log('❌ No users to generate training data for');
            return;
        }
        
        if (!this.isModelLoaded) {
            console.log('❌ Cannot generate training data - face-api.js model not loaded');
            console.log('   Please refresh the page and wait for models to load');
            return;
        }
        
        // Process each user's photos to extract face descriptors
        for (const user of this.knownUsers) {
            const descriptors = [];
            
            // Get all face photos for this user (face_data, face_data_2, face_data_3)
            const facePhotos = [user.face_data, user.face_data_2, user.face_data_3].filter(photo => photo);
            
            if (facePhotos.length === 0) {
                console.warn(`⚠️ No face photos found for user: ${user.name}`);
                continue;
            }
            
            console.log(`📸 Processing ${facePhotos.length} photos for ${user.name}...`);
            
            // Extract descriptors from each photo
            for (let i = 0; i < facePhotos.length; i++) {
                try {
                    const photoData = facePhotos[i];
                    console.log(`   Processing photo ${i + 1}/${facePhotos.length}...`);
                    const descriptor = await this.extractDescriptorFromPhoto(photoData);
                    if (descriptor) {
                        descriptors.push(descriptor);
                        console.log(`   ✅ Photo ${i + 1} processed successfully`);
                    } else {
                        console.warn(`   ⚠️ Photo ${i + 1} - no face detected`);
                    }
                } catch (error) {
                    console.error(`   ❌ Error processing photo ${i + 1} for ${user.name}:`, error);
                }
            }
            
            if (descriptors.length > 0) {
                this.trainingData.set(user.id, descriptors);
                console.log(`✅ Generated ${descriptors.length} training descriptors for ${user.name}`);
            } else {
                console.warn(`⚠️ No valid descriptors generated for ${user.name} - user will not be recognized`);
            }
        }
        
        console.log(`\n🎉 Training data generation completed!`);
        console.log(`   Total users ready: ${this.trainingData.size}`);
        console.log(`   Users in database: ${this.knownUsers.length}`);
        
        if (this.trainingData.size === 0) {
            console.error('❌ NO TRAINING DATA GENERATED - Face recognition will not work!');
            console.error('   Please check if face photos are properly saved in database');
        }
    }
    
    async extractDescriptorFromPhoto(photoData) {
        try {
            // Create image element from base64 data
            const img = await this.loadImage(photoData);
            
            // Detect face and extract descriptor
            if (this.isModelLoaded) {
                const detection = await faceapi.detectSingleFace(img)
                    .withFaceLandmarks()
                    .withFaceDescriptor();
                
                if (detection && detection.descriptor) {
                    return detection.descriptor;
                } else {
                    console.warn('No face detected in training photo');
                    return null;
                }
            } else {
                console.warn('Model not loaded, cannot extract descriptor');
                return null;
            }
        } catch (error) {
            console.error('Error extracting descriptor:', error);
            return null;
        }
    }
    
    loadImage(dataUrl) {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => resolve(img);
            img.onerror = reject;
            img.src = dataUrl;
        });
    }
    
    // Coordinate mapping for overlay positioning
    mapBoxToOverlay(box) {
        const containerWidth = this.video.clientWidth;
        const containerHeight = this.video.clientHeight;
        const videoWidth = this.video.videoWidth || 640;
        const videoHeight = this.video.videoHeight || 480;
        
        if (!containerWidth || !containerHeight) {
            return box;
        }
        
        const scale = Math.max(containerWidth / videoWidth, containerHeight / videoHeight);
        const displayedWidth = videoWidth * scale;
        const displayedHeight = videoHeight * scale;
        const offsetX = (containerWidth - displayedWidth) / 2;
        const offsetY = (containerHeight - displayedHeight) / 2;
        
        return {
            x: offsetX + box.x * scale,
            y: offsetY + box.y * scale,
            width: box.width * scale,
            height: box.height * scale
        };
    }
    
    async loadModels() {
        try {
            this.showStatus('🚀 Memuat model AI...', 'info');
            
            // Wait for face-api.js to load
            let retries = 0;
            while (typeof faceapi === 'undefined' && retries < 50) {
                await new Promise(resolve => setTimeout(resolve, 100));
                retries++;
            }
            
            if (typeof faceapi === 'undefined') {
                console.log('Face API library tidak ditemukan');
                this.isModelLoaded = false;
                this.showStatus('Sistem siap - Mode simulasi', 'success');
                this.startBtn.disabled = false;
                return;
            }
            
            // Load only essential models for accuracy
            try {
                const modelPath = 'https://cdn.jsdelivr.net/npm/@vladmandic/face-api@latest/model';
                await Promise.all([
                    faceapi.nets.ssdMobilenetv1.loadFromUri(modelPath),     // Face detection
                    faceapi.nets.faceLandmark68Net.loadFromUri(modelPath),  // Face landmarks
                    faceapi.nets.faceRecognitionNet.loadFromUri(modelPath)  // Face recognition
                ]);
                
                this.isModelLoaded = true;
                this.showStatus('Model AI berhasil dimuat - Sistem siap', 'success');
                console.log('Face-API.js models loaded successfully');
            } catch (modelError) {
                console.log('Model loading failed:', modelError);
                this.showStatus('Sistem siap - Mode simulasi', 'success');
                this.isModelLoaded = false;
            }
            
            this.startBtn.disabled = false;
            
        } catch (error) {
            console.error('Error loading models:', error);
            this.showStatus('Sistem siap - Mode simulasi', 'success');
            this.isModelLoaded = false;
            this.startBtn.disabled = false;
        }
    }
    
    async startCamera() {
        try {
            this.showStatus('🚀 Memulai sistem Face ID...', 'info');
            
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
                
                this.showStatus('✅ Sistem Face ID aktif - Wajah akan terdeteksi otomatis', 'success');
                this.startDetection();
            };
            
        } catch (error) {
            console.error('Error starting camera:', error);
            this.showStatus('Gagal mengakses kamera', 'error');
        }
    }
    
    startDetection() {
        this.isDetecting = true;
        
        // Real-time detection loop for smooth tracking
        this.detectionInterval = setInterval(async () => {
            await this.detectAndRecognizeFace();
        }, 100); // 100ms for real-time smooth tracking
    }
    
    async detectAndRecognizeFace() {
        if (!this.video.videoWidth || !this.video.videoHeight) return;
        
        try {
            let detection = null;
            
            if (this.isModelLoaded) {
                // Use face-api.js for real detection with lower confidence for better tracking
                const options = new faceapi.SsdMobilenetv1Options({
                    minConfidence: 0.4  // Lower confidence for better real-time tracking
                });
                
                detection = await faceapi.detectSingleFace(this.video, options)
                    .withFaceLandmarks()
                    .withFaceDescriptor();
            } else {
                // Real-time simulation based on actual video analysis
                detection = this.simulateRealTimeFaceDetection();
            }
            
            if (detection) {
                this.faceDetected = true;
                
                // ALWAYS update ring position in real-time
                this.updateFaceRingPosition(detection);
                
                // Recognize user (with longer cooldown to prevent flickering)
                const now = Date.now();
                if (now - this.lastRecognitionTime > this.recognitionCooldown) {
                    await this.recognizeUser(detection);
                    this.lastRecognitionTime = now;
                }
                
                // ALWAYS update name position to follow ring
                if (this.currentUser) {
                    this.updateUserLabelPosition();
                }
                
                // Update status
                this.updateStatus('👤 Wajah terdeteksi - Mengenali identitas...');
                
            } else {
                // Don't clear UI immediately - keep showing for smooth experience
                if (this.faceDetected) {
                    this.updateStatus('🔍 Mencari wajah...');
                } else {
                    this.updateStatus('📷 Posisikan wajah Anda di depan kamera');
                }
                this.faceDetected = false;
            }
            
        } catch (error) {
            console.error('Detection error:', error);
            this.updateStatus('⚠️ Terjadi kesalahan dalam deteksi wajah');
        }
    }
    
    // Real-time face detection simulation with movement tracking
    simulateRealTimeFaceDetection() {
        const videoWidth = this.video.videoWidth || 640;
        const videoHeight = this.video.videoHeight || 480;
        
        // Analyze actual video content for face-like regions
        this.ctx.drawImage(this.video, 0, 0, this.canvas.width, this.canvas.height);
        const imageData = this.ctx.getImageData(0, 0, this.canvas.width, this.canvas.height);
        
        // Find the brightest/most active region (likely face area)
        const faceRegion = this.findFaceRegion(imageData, videoWidth, videoHeight);
        
        if (faceRegion) {
            return {
                detection: {
                    box: faceRegion,
                    score: 0.9
                },
                landmarks: null,
                descriptor: this.generateSimulatedDescriptor()
            };
        }
        
        return null; // No face detected
    }
    
    findFaceRegion(imageData, videoWidth, videoHeight) {
        const data = imageData.data;
        let maxBrightness = 0;
        let bestRegion = null;
        
        // Scan in grid to find brightest region (likely face)
        const gridSize = 32;
        for (let y = 0; y < videoHeight - 100; y += gridSize) {
            for (let x = 0; x < videoWidth - 100; x += gridSize) {
                let brightness = 0;
                let skinPixels = 0;
                
                // Sample region
                for (let dy = 0; dy < 100; dy += 4) {
                    for (let dx = 0; dx < 100; dx += 4) {
                        const idx = ((y + dy) * videoWidth + (x + dx)) * 4;
                        if (idx < data.length) {
                            const r = data[idx];
                            const g = data[idx + 1];
                            const b = data[idx + 2];
                            
                            brightness += (r + g + b) / 3;
                            
                            // Check for skin-like colors
                            if (r > 95 && g > 40 && b > 20 && 
                                Math.max(r, g, b) - Math.min(r, g, b) > 15 &&
                                Math.abs(r - g) > 15 && r > g && r > b) {
                                skinPixels++;
                            }
                        }
                    }
                }
                
                // Score based on brightness and skin pixels
                const score = brightness + (skinPixels * 50);
                
                if (score > maxBrightness && skinPixels > 10) {
                    maxBrightness = score;
                    const faceWidth = Math.min(videoWidth * 0.35, 250);
                    const faceHeight = faceWidth * 1.2;
                    
                    bestRegion = {
                        x: Math.max(0, x - faceWidth/4),
                        y: Math.max(0, y - faceHeight/4),
                        width: faceWidth,
                        height: faceHeight
                    };
                }
            }
        }
        
        return bestRegion;
    }
    
    hasMotion(imageData) {
        // Simple motion detection based on pixel changes
        const data = imageData.data;
        let brightness = 0;
        
        // Sample every 100th pixel for performance
        for (let i = 0; i < data.length; i += 400) {
            brightness += (data[i] + data[i + 1] + data[i + 2]) / 3;
        }
        
        const avgBrightness = brightness / (data.length / 400);
        
        // Store previous brightness for comparison
        if (!this.lastBrightness) {
            this.lastBrightness = avgBrightness;
            return true; // First frame, assume motion
        }
        
        const change = Math.abs(avgBrightness - this.lastBrightness);
        this.lastBrightness = avgBrightness;
        
        // If brightness changed significantly, assume face is present
        return change > 5 || avgBrightness > 50; // Adjust threshold as needed
    }
    
    generateSimulatedDescriptor() {
        // Generate a random descriptor for simulation
        const descriptor = new Float32Array(128);
        for (let i = 0; i < 128; i++) {
            descriptor[i] = (Math.random() - 0.5) * 2;
        }
        return descriptor;
    }
    
    clearOverlays() {
        // Don't clear overlays in real-time mode to prevent flickering
        // Only clear when absolutely necessary
    }
    
    updateFaceRingPosition(detection) {
        const box = detection.detection.box;
        const mappedBox = this.mapBoxToOverlay(box);
        
        // Create or update existing ring
        if (!this.currentRing) {
            this.drawFaceRing(detection);
        } else {
            // Smoothly update existing ring position
            this.currentRing.style.left = `${mappedBox.x}px`;
            this.currentRing.style.top = `${mappedBox.y}px`;
            this.currentRing.style.width = `${mappedBox.width}px`;
            this.currentRing.style.height = `${mappedBox.height}px`;
        }
    }
    
    updateUserLabelPosition() {
        if (!this.currentNameLabel || !this.currentRing) return;
        
        // Get current ring position
        const ringLeft = parseFloat(this.currentRing.style.left) || 0;
        const ringTop = parseFloat(this.currentRing.style.top) || 0;
        const ringWidth = parseFloat(this.currentRing.style.width) || 200;
        
        // Update name position to follow ring smoothly
        this.currentNameLabel.style.left = `${ringLeft + ringWidth / 2}px`;
        this.currentNameLabel.style.top = `${Math.max(ringTop - 50, 10)}px`;
        this.currentNameLabel.style.transform = 'translateX(-50%)';
    }
    
    drawFaceRing(detection) {
        const box = detection.detection.box;
        const mappedBox = this.mapBoxToOverlay(box);
        
        // Create face ring
        const ring = document.createElement('div');
        ring.style.position = 'absolute';
        ring.style.left = `${mappedBox.x}px`;
        ring.style.top = `${mappedBox.y}px`;
        ring.style.width = `${mappedBox.width}px`;
        ring.style.height = `${mappedBox.height}px`;
        ring.style.border = '4px solid #10b981'; // Default green
        ring.style.borderRadius = '12px';
        ring.style.background = 'rgba(16, 185, 129, 0.1)';
        ring.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.5)';
        ring.style.animation = 'pulseGreen 2s infinite';
        ring.className = 'face-ring';
        
        this.faceOverlay.appendChild(ring);
        this.currentRing = ring;
    }
    
    async recognizeUser(detection) {
        // STRICT VALIDATION: Check if we have any users in database first
        if (this.knownUsers.length === 0) {
            console.log('❌ No users in database - showing unknown');
            this.setCurrentUser(null); // Unknown user
            return;
        }
        
        // STRICT VALIDATION: Check if we have training data
        if (this.trainingData.size === 0) {
            console.log('❌ No training data available - showing unknown');
            this.setCurrentUser(null); // Unknown user
            return;
        }

        // Real face-api.js recognition ONLY
        if (this.isModelLoaded && detection.descriptor) {
            let bestMatch = null;
            let bestDistance = Infinity;

            // Check against training data
            for (const user of this.knownUsers) {
                const userDescriptors = this.trainingData.get(user.id);
                if (!userDescriptors || userDescriptors.length === 0) {
                    console.log(`No training data for user: ${user.name}`);
                    continue;
                }

                for (const trainedDescriptor of userDescriptors) {
                    const distance = this.calculateDistance(detection.descriptor, trainedDescriptor);

                    if (distance < bestDistance) {
                        bestDistance = distance;
                        bestMatch = user;
                    }
                }
            }

            // Strict threshold for accurate recognition (0.5 is very strict, 0.6 is standard)
            const threshold = 0.5; // Lebih ketat untuk akurasi tinggi
            if (bestDistance < threshold && bestMatch) {
                console.log(`✅ Face recognized: ${bestMatch.name} (distance: ${bestDistance.toFixed(3)}, threshold: ${threshold})`);
                this.setCurrentUser(bestMatch);
            } else {
                console.log(`❌ Face not recognized (best distance: ${bestDistance.toFixed(3)}, threshold: ${threshold})`);
                if (bestMatch) {
                    console.log(`   Closest match was: ${bestMatch.name} (but distance too high)`);
                }
                this.setCurrentUser(null); // Unknown user
            }
        } else {
            // Model not loaded - cannot recognize without face descriptors
            console.log('⚠️ Face-api.js model not loaded - cannot perform face recognition');
            console.log('   All faces will be marked as UNKNOWN until model is loaded');
            this.setCurrentUser(null); // Unknown user - no recognition without model
        }
    }
    
    calculateDistance(descriptor1, descriptor2) {
        // Euclidean distance between two face descriptors
        let sum = 0;
        for (let i = 0; i < descriptor1.length; i++) {
            const diff = descriptor1[i] - descriptor2[i];
            sum += diff * diff;
        }
        return Math.sqrt(sum);
    }
    
    setCurrentUser(user) {
        // Prevent unnecessary updates to avoid flickering
        if (this.currentUser && user && this.currentUser.id === user.id) {
            console.log(`Same user detected: ${user.name}, skipping update`);
            return; // Same user, no need to update
        }
        
        console.log(`Setting current user: ${user ? user.name : 'Unknown'}`);
        console.log(`Database has ${this.knownUsers.length} registered users`);
        this.currentUser = user;
        
        if (user) {
            // Known user - show green ring
            console.log(`✅ Showing GREEN ring and name for: ${user.name}`);
            this.updateRingColor('green');
            this.showUserLabel(user);
            this.updateStatus(`✅ Wajah dikenali: ${user.name} - Mencatat absensi...`);
            
            // AUTO LOGIN after 2 seconds
            setTimeout(() => {
                this.autoLogin(user);
            }, 2000);
        } else {
            // Unknown user - show red ring
            console.log('❌ Showing RED ring for unknown user');
            this.updateRingColor('red');
            this.showUnknownLabel();
            this.hideAttendanceButton();
            this.updateStatus('❌ Wajah tidak terdaftar - Silakan daftarkan wajah terlebih dahulu');
        }
    }
    
    updateRingColor(color) {
        if (!this.currentRing) return;
        
        if (color === 'green') {
            this.currentRing.style.borderColor = '#10b981';
            this.currentRing.style.background = 'rgba(16, 185, 129, 0.1)';
            this.currentRing.style.boxShadow = '0 0 20px rgba(16, 185, 129, 0.5)';
            this.currentRing.style.animation = 'pulseGreen 2s infinite';
        } else {
            this.currentRing.style.borderColor = '#ef4444';
            this.currentRing.style.background = 'rgba(239, 68, 68, 0.1)';
            this.currentRing.style.boxShadow = '0 0 20px rgba(239, 68, 68, 0.5)';
            this.currentRing.style.animation = 'pulseRed 2s infinite';
        }
    }
    
    showUserLabel(user) {
        // Clear previous label
        this.userOverlay.innerHTML = '';
        
        if (!this.currentRing) {
            // If no ring yet, wait a bit and try again
            setTimeout(() => this.showUserLabel(user), 100);
            return;
        }
        
        // Get ring position directly from style (more reliable)
        const ringLeft = parseFloat(this.currentRing.style.left) || 0;
        const ringTop = parseFloat(this.currentRing.style.top) || 0;
        const ringWidth = parseFloat(this.currentRing.style.width) || 200;
        
        const label = document.createElement('div');
        label.className = 'user-name-label'; // Add CSS class for smooth transitions
        label.style.position = 'absolute';
        label.style.left = `${ringLeft + ringWidth / 2}px`;
        label.style.top = `${Math.max(ringTop - 50, 10)}px`; // 50px above ring
        label.style.transform = 'translateX(-50%)';
        label.style.backgroundColor = '#10b981';
        label.style.color = 'white';
        label.style.padding = '8px 16px';
        label.style.borderRadius = '20px';
        label.style.fontSize = '16px';
        label.style.fontWeight = 'bold';
        label.style.boxShadow = '0 4px 12px rgba(16, 185, 129, 0.4)';
        label.style.zIndex = '20'; // Higher z-index
        label.style.minWidth = '120px';
        label.style.textAlign = 'center';
        label.style.pointerEvents = 'none';
        label.textContent = user.name;
        
        this.userOverlay.appendChild(label);
        this.currentNameLabel = label;
        
        console.log(`Showing name label: ${user.name} at position (${ringLeft + ringWidth / 2}, ${ringTop - 50})`);
    }
    
    showUnknownLabel() {
        // Clear previous label
        this.userOverlay.innerHTML = '';
        
        if (!this.currentRing) {
            setTimeout(() => this.showUnknownLabel(), 100);
            return;
        }
        
        // Get ring position directly from style (same as showUserLabel)
        const ringLeft = parseFloat(this.currentRing.style.left) || 0;
        const ringTop = parseFloat(this.currentRing.style.top) || 0;
        const ringWidth = parseFloat(this.currentRing.style.width) || 200;
        
        const label = document.createElement('div');
        label.style.position = 'absolute';
        label.style.left = `${ringLeft + ringWidth / 2}px`;
        label.style.top = `${Math.max(ringTop - 50, 10)}px`; // 50px above ring
        label.style.transform = 'translateX(-50%)';
        label.style.backgroundColor = '#ef4444';
        label.style.color = 'white';
        label.style.padding = '8px 16px';
        label.style.borderRadius = '20px';
        label.style.fontSize = '16px';
        label.style.fontWeight = 'bold';
        label.style.boxShadow = '0 4px 12px rgba(239, 68, 68, 0.4)';
        label.style.zIndex = '20'; // Higher z-index
        label.style.minWidth = '120px';
        label.style.textAlign = 'center';
        label.style.pointerEvents = 'none';
        label.textContent = 'Tidak Dikenal';
        
        this.userOverlay.appendChild(label);
        this.currentNameLabel = label;
        
        console.log(`Showing unknown label at position (${ringLeft + ringWidth / 2}, ${ringTop - 50})`);
    }
    
    updateStatus(message) {
        if (this.autoStatusText) {
            this.autoStatusText.textContent = message;
        }
    }
    
    showAttendanceButton() {
        this.attendanceBtn.classList.remove('hidden');
        this.stopBtn.classList.add('hidden');
    }
    
    hideAttendanceButton() {
        this.attendanceBtn.classList.add('hidden');
        this.stopBtn.classList.remove('hidden');
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
        
        // Reset UI
        this.startBtn.classList.remove('hidden');
        this.stopBtn.classList.add('hidden');
        this.autoDetectionStatus.classList.add('hidden');
        this.attendanceBtn.classList.add('hidden');
        
        // Clear overlays
        this.faceOverlay.innerHTML = '';
        this.userOverlay.innerHTML = '';
        
        // Reset state
        this.currentUser = null;
        this.faceDetected = false;
        this.currentRing = null;
        this.currentNameLabel = null;
        
        this.showStatus('⏹️ Kamera dihentikan', 'info');
    }
    
    showStatus(message, type = 'info') {
        const span = this.statusMessage.querySelector('span');
        if (span) {
            span.textContent = message;
        } else {
            this.statusMessage.innerHTML = `<i class="fas fa-info-circle mr-2"></i><span>${message}</span>`;
        }
        
        this.statusMessage.classList.remove('hidden');
        this.statusMessage.classList.remove('bg-blue-600', 'bg-green-600', 'bg-yellow-600', 'bg-red-600');
        
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
    
    async autoLogin(user) {
        if (!user) {
            console.error('Cannot auto-login: no user provided');
            return;
        }
        
        // Prevent multiple attendance records
        if (this.isLoggingIn) {
            console.log('Attendance already in progress, skipping...');
            return;
        }
        
        this.isLoggingIn = true;
        
        try {
            console.log(`✅ Recording attendance for user: ${user.name} (NIS: ${user.nis})`);
            this.updateStatus(`⏳ Mencatat absensi untuk ${user.name}...`);
            
            // Stop camera
            this.stopCamera();
            
            // Call backend to record attendance
            const response = await fetch('/admin/face-id/authenticate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    nis: user.nis,
                    auto_login: true
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                const attendanceType = result.attendance_type || 'check_in';
                console.log(`✅ Attendance recorded successfully: ${attendanceType}`);
                
                // Update status based on attendance type
                if (attendanceType === 'check_in') {
                    this.updateStatus(`🎉 Absen Masuk berhasil dicatat untuk ${user.name}!`);
                } else if (attendanceType === 'check_out') {
                    this.updateStatus(`🎉 Absen Keluar berhasil dicatat untuk ${user.name}!`);
                } else if (attendanceType === 'already_complete') {
                    this.updateStatus(`ℹ️ ${user.name} sudah absen masuk dan keluar hari ini`);
                } else {
                    this.updateStatus(`🎉 Absensi berhasil dicatat untuk ${user.name}!`);
                }
                
                // Show success message
                this.showAttendanceSuccess(user, result);
                
                // Reset after 5 seconds for next user
                setTimeout(() => {
                    this.resetForNextUser();
                }, 5000);
            } else {
                console.error('❌ Attendance failed:', result.message);
                this.updateStatus(`❌ Gagal: ${result.message}`);
                this.isLoggingIn = false;
            }
            
        } catch (error) {
            console.error('Auto-attendance error:', error);
            this.updateStatus('❌ Kesalahan sistem - Silakan coba lagi');
            this.isLoggingIn = false;
        }
    }
    
    showAttendanceSuccess(user, result) {
        // Determine attendance type and customize message
        const attendanceType = result.attendance_type || 'check_in';
        let title = 'Absensi Berhasil!';
        let greeting = `Selamat datang, <strong>${user.name}</strong>`;
        let icon = 'fa-check-circle';
        let iconColor = 'text-green-600 dark:text-green-400';
        let bgColor = 'bg-green-100 dark:bg-green-900';
        let statusMessage = '';
        let timeInfo = '';
        
        if (attendanceType === 'check_in') {
            title = '✅ Absen Masuk Berhasil!';
            greeting = `Selamat datang, <strong>${user.name}</strong>`;
            icon = 'fa-sign-in-alt';
            statusMessage = 'Absen masuk Anda telah tercatat';
            timeInfo = `Waktu masuk: ${new Date().toLocaleTimeString('id-ID')}`;
        } else if (attendanceType === 'check_out') {
            title = '✅ Absen Keluar Berhasil!';
            greeting = `Sampai jumpa, <strong>${user.name}</strong>`;
            icon = 'fa-sign-out-alt';
            statusMessage = 'Absen keluar Anda telah tercatat';
            timeInfo = `Waktu keluar: ${new Date().toLocaleTimeString('id-ID')}`;
        } else if (attendanceType === 'already_complete') {
            title = 'ℹ️ Absensi Sudah Lengkap';
            greeting = `<strong>${user.name}</strong>`;
            icon = 'fa-info-circle';
            iconColor = 'text-blue-600 dark:text-blue-400';
            bgColor = 'bg-blue-100 dark:bg-blue-900';
            statusMessage = 'Anda sudah absen masuk dan keluar hari ini';
            timeInfo = `Tanggal: ${new Date().toLocaleDateString('id-ID')}`;
        }
        
        // Show success overlay
        const successOverlay = document.createElement('div');
        successOverlay.className = 'fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50';
        successOverlay.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-lg p-8 max-w-md mx-4 text-center">
                <div class="w-20 h-20 ${bgColor} rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas ${icon} text-4xl ${iconColor}"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">${title}</h2>
                <p class="text-lg text-gray-700 dark:text-gray-300 mb-4">${greeting}</p>
                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4 mb-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">NIS</div>
                    <div class="text-lg font-bold text-gray-900 dark:text-white">${user.nis}</div>
                </div>
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                    ${statusMessage}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-500">
                    ${timeInfo}
                </div>
            </div>
        `;
        
        document.body.appendChild(successOverlay);
        
        // Remove overlay after 5 seconds
        setTimeout(() => {
            successOverlay.remove();
        }, 5000);
    }
    
    resetForNextUser() {
        // Reset state for next user
        this.isLoggingIn = false;
        this.currentUser = null;
        this.faceDetected = false;
        
        // Clear overlays
        this.faceOverlay.innerHTML = '';
        this.userOverlay.innerHTML = '';
        
        // Reset UI
        this.updateStatus('✅ Sistem siap untuk pengguna berikutnya');
        this.startBtn.classList.remove('hidden');
        
        console.log('🔄 System reset, ready for next user');
    }
    
    async recordAttendance() {
        if (!this.currentUser) {
            this.showStatus('❌ Tidak ada wajah yang dikenali', 'error');
            return;
        }
        
        try {
            this.showStatus('⏳ Mencatat kehadiran...', 'info');
            
            // Call auto-login instead
            await this.autoLogin(this.currentUser);
            
        } catch (error) {
            console.error('Attendance recording error:', error);
            this.showStatus('❌ Kesalahan sistem - Silakan coba lagi', 'error');
        }
    }
}

// Initialize Face ID System when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing Simple Face ID System...');
    try {
        const system = new SimpleFaceIDSystem();
        console.log('Simple Face ID System initialized successfully');
        window.faceIDSystem = system; // For debugging
    } catch (error) {
        console.error('Failed to initialize Face ID System:', error);
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
       