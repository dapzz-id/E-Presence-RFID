<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Siswa - {{ old('name', $siswa->name) }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        background: '#0f172a',
                        foreground: '#f8fafc',
                        primary: '#0ea5e9',
                        secondary: '#1e293b',
                        accent: '#10b981',
                        muted: '#334155',
                        destructive: '#ef4444',
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
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-track {
            background: #1f2937;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #4b5563;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }

        .required-field::after {
            content: '*';
            color: #ef4444;
            margin-left: 4px;
        }
    </style>
</head>

<body class="h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
    @include('Cert.head')

    <div class="container mx-auto px-8 py-6 mt-10">
        <div class="mb-6">
            <h1 class="text-3xl font-bold">Edit Data Siswa</h1>
            <p class="text-gray-400">Perbarui data siswa</p>
        </div>

        <div class="max-w-3xl">
            <form action="{{ route('siswa.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" id="existing_photo" name="existing_photo" value="{{ $siswa->foto_profile }}">
                <input type="hidden" id="original_nis" name="original_nis" value="{{ $siswa->nis }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label for="nis" class="block text-sm font-medium mb-1 required-field">NIS</label>
                            <input type="text" id="nis" name="nis" placeholder="Masukkan NIS"
                                class="w-full px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded @error('nis') border-red-500 @enderror"
                                required readonly value="{{ old('nis', $siswa->nis) }}" />
                            @error('nis')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium mb-1 required-field">Nama
                                Lengkap</label>
                            <input type="text" id="name" name="name" placeholder="Masukkan nama lengkap"
                                class="w-full px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded @error('name') border-red-500 @enderror"
                                required value="{{ old('name', $siswa->name) }}" />
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="class" class="block text-sm font-medium mb-1 required-field">Kelas</label>
                            <select id="class" name="class"
                                class="w-full px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded @error('class') border-red-500 @enderror"
                                required>
                                <option value="" disabled>Pilih kelas</option>
                                @foreach (['X RPL 1', 'X RPL 2', 'X RPL 3', 'X RPL 4', 'XI RPL 1', 'XI RPL 2', 'XI RPL 3', 'XI RPL 4', 'XII RPL 1', 'XII RPL 2', 'XII RPL 3', 'XII RPL 4', 'X DKV 1', 'X DKV 2', 'X DKV 3', 'XI DKV 1', 'XI DKV 2', 'XI DKV 3', 'XII DKV 1', 'XII DKV 2', 'XII DKV 3', 'X TKJ 1', 'X TKJ 2', 'X TKJ 3', 'XI TKJ 1', 'XI TKJ 2', 'XI TKJ 3', 'XII TKJ 1', 'XII TKJ 2', 'XII TKJ 3', 'X TRANSMISI', 'XI TRANSMISI', 'XII TRANSMISI'] as $kelasOption)
                                    <option value="{{ $kelasOption }}"
                                        {{ old('class', $siswa->kelas) == $kelasOption ? 'selected' : '' }}>
                                        {{ $kelasOption }}</option>
                                @endforeach
                            </select>
                            @error('class')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium mb-1 required-field">Alamat</label>
                            <textarea name="address" id="address" autocomplete="street-address" cols="30" rows="5" required
                                class="w-full px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded @error('address') border-red-500 @enderror"
                                placeholder="Masukan alamat rumah">{{ old('address', $siswa->alamat) }}</textarea>
                            @error('address')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium mb-1 ml-4 max-sm:ml-0 required-field">Foto</label>
                            <div class="mt-2 flex flex-col items-center justify-center border-2 border-dashed aspect-[3/4] ml-4 max-sm:mx-auto border-gray-600 rounded-lg p-6 h-64 @error('photo') border-red-500 @enderror"
                                id="photo-container">
                                <div class="text-center {{ $siswa->foto_profile ? 'hidden' : '' }}"
                                    id="upload-placeholder">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-400">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" x2="12" y1="3" y2="15"></line>
                                    </svg>
                                    <div class="mt-2">
                                        <label for="photo-upload"
                                            class="cursor-pointer bg-primary hover:bg-primary/90 dark:text-white py-2 px-4 rounded-md"
                                            onclick="openPhotoBrowser()">
                                            Pilih Foto
                                        </label>
                                        <input id="photo-upload" name="photo" type="file" accept="image/*"
                                            class="hidden" disabled />
                                    </div>
                                    <p class="mt-2 text-sm text-gray-400 max-sm:text-xs">PNG, JPG up to 2MiB</p>

                                    <button type="button" id="browse-photos-btn"
                                        class="mt-3 text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300"
                                        onclick="openPhotoBrowser()">
                                        <i class="bi bi-images mr-1"></i> Browse existing photos
                                    </button>
                                </div>
                                <div class="relative w-full h-full {{ $siswa->foto_profile ? '' : 'hidden' }}"
                                    id="photo-preview-container">
                                    <img src="{{ $siswa->foto_profile 
                                        ? Storage::disk('s3')->temporaryUrl('profile/' . $siswa->foto_profile, now()->addMinutes(5)) 
                                        : '/placeholder.svg' }}"
                                        alt="Preview"
                                        id="photo-preview"
                                        loading="lazy"
                                        class="w-full h-full object-cover rounded-lg cursor-pointer"
                                        onclick="openPhotoBrowser()" />

                                    <button type="button" id="remove-photo"
                                        class="absolute top-2 right-2 bg-black text-white p-1 rounded-full">
                                        ×
                                    </button>
                                </div>
                            </div>
                            <span id="current_photo_name"
                                class="text-sm text-gray-500 dark:text-gray-400 ml-4 mt-2 block">
                                {{ $siswa->foto_profile ? $siswa->foto_profile : 'No photo selected' }}
                            </span>
                            @error('photo')
                                <p class="text-red-500 text-xs mt-1 ml-4">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <a href="{{ route('siswa') }}">
                        <button type="button"
                            class="px-4 py-2 border border-gray-600 text-gray-700 dark:text-white rounded-md hover:bg-gray-200 dark:hover:bg-gray-700">Batal</button>
                    </a>
                    <button type="submit" class="px-4 py-2 bg-accent hover:bg-accent/90 text-white rounded-md">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Photo Browser Modal -->
    <div id="photoBrowserModal" class="fixed inset-0 z-[60] hidden overflow-y-auto backdrop-blur-sm">
        <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-80"></div>

        <div class="relative h-full overflow-y-auto">
            <div
                class="flex items-center justify-center min-h-full px-4 pt-4 pb-20 text-center sm:block sm:p-0 relative">
                <div
                    class="inline-block w-full max-w-3xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 rounded-xl shadow-xl sm:scale-100 scale-95 border border-gray-200 dark:border-gray-700 relative">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-xl font-semibold leading-6 text-gray-900 dark:text-gray-100">
                            Browse Photos
                        </h3>
                        <button type="button" onclick="closePhotoBrowser()"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="mb-4">
                        <input type="text" id="photo-search" placeholder="Search photos..."
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50 py-2 px-4 border bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    </div>

                    <div id="photos-loading" class="py-10 text-center">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-primary-600">
                        </div>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Loading photos...</p>
                    </div>

                    <div id="photos-container"
                        class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 max-h-[400px] overflow-y-auto p-1 hidden custom-scrollbar">
                        <!-- Photos will be loaded here -->
                    </div>

                    <div id="no-photos" class="py-10 text-center hidden">
                        <p class="text-gray-600 dark:text-gray-400">No photos found</p>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                        <button type="button" onclick="closePhotoBrowser()"
                            class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700 transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });
            const currentTimeElement = document.getElementById('current-time');
            if (currentTimeElement) {
                currentTimeElement.textContent = timeString;
            }
        }

        setInterval(updateTime, 1000);
        updateTime();

        const photoUpload = document.getElementById('photo-upload');
        const uploadPlaceholder = document.getElementById('upload-placeholder');
        const photoPreviewContainer = document.getElementById('photo-preview-container');
        const photoPreview = document.getElementById('photo-preview');
        const removePhotoBtn = document.getElementById('remove-photo');
        const currentPhotoName = document.getElementById('current_photo_name');

        if (photoUpload) {
            photoUpload.addEventListener('change', function(e) {
                if (e.target.files && e.target.files[0]) {
                    const file = e.target.files[0];

                    const maxSize = 2 * 1024 * 1024;
                    if (file.size > maxSize) {
                        alert('Ukuran file tidak boleh lebih dari 2 MiB');
                        photoUpload.value = '';
                        return;
                    }

                    if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
                        alert('Hanya file JPG dan PNG yang diperbolehkan');
                        photoUpload.value = '';
                        return;
                    }

                    const reader = new FileReader();

                    reader.onload = function(event) {
                        photoPreview.src = event.target.result;
                        uploadPlaceholder.classList.add('hidden');
                        photoPreviewContainer.classList.remove('hidden');
                        if (currentPhotoName) {
                            currentPhotoName.textContent = file.name;
                        }

                        const existingPhoto = document.getElementById('existing_photo');
                        if (existingPhoto) {
                            existingPhoto.value = '';
                        }
                    }

                    reader.readAsDataURL(file);
                }
            });
        }

        if (removePhotoBtn) {
            removePhotoBtn.addEventListener('click', function() {
                if (photoUpload) photoUpload.value = '';
                if (photoPreview) photoPreview.src = '';
                if (photoPreviewContainer) photoPreviewContainer.classList.add('hidden');
                if (uploadPlaceholder) uploadPlaceholder.classList.remove('hidden');
                if (currentPhotoName) currentPhotoName.textContent = 'No photo selected';

                const existingPhoto = document.getElementById('existing_photo');
                if (existingPhoto) {
                    existingPhoto.value = '';
                }
            });
        }

        const photoBrowserModal = document.getElementById('photoBrowserModal');
        const photosContainer = document.getElementById('photos-container');
        const photosLoading = document.getElementById('photos-loading');
        const noPhotos = document.getElementById('no-photos');
        const photoSearch = document.getElementById('photo-search');
        const browsePhotosBtn = document.getElementById('browse-photos-btn');
        let allPhotos = [];

        if (browsePhotosBtn) {
            browsePhotosBtn.addEventListener('click', function() {
                openPhotoBrowser();
            });
        }

        function openPhotoBrowser() {
            if (photoBrowserModal) {
                photoBrowserModal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                loadPhotos();
            }
        }

        function closePhotoBrowser() {
            if (photoBrowserModal) {
                photoBrowserModal.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        }

        function loadPhotos() {
            if (photosContainer) photosContainer.classList.add('hidden');
            if (photosLoading) photosLoading.classList.remove('hidden');
            if (noPhotos) noPhotos.classList.add('hidden');

            fetch('{{ route('siswa.photos') }}')
                .then(response => response.json())
                .then(data => {
                    allPhotos = data;
                    renderPhotos(data);
                    if (photosLoading) photosLoading.classList.add('hidden');

                    if (data.length === 0) {
                        if (noPhotos) noPhotos.classList.remove('hidden');
                    } else {
                        if (photosContainer) photosContainer.classList.remove('hidden');
                    }
                })
                .catch(error => {
                    console.error('Error loading photos:', error);
                    if (photosLoading) photosLoading.classList.add('hidden');
                    if (noPhotos) noPhotos.classList.remove('hidden');
                });
        }

        function renderPhotos(photos) {
            if (!photosContainer) return;

            photosContainer.innerHTML = '';

            photos.forEach(photo => {
                const photoElement = document.createElement('div');
                photoElement.className =
                    'border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden cursor-pointer hover:border-primary-500 dark:hover:border-primary-500 transition-colors';
                photoElement.innerHTML = `
          <div class="aspect-[3/4] relative">
            <img src="${photo.url}" alt="${photo.name}" loading="lazy" class="w-full h-full object-cover">
          </div>
          <div class="p-2 text-xs truncate" title="${photo.name}">
            ${photo.name}
          </div>
          <div class="px-2 pb-2 text-xs text-gray-500 dark:text-gray-400">
            ${photo.size}
          </div>
        `;

                photoElement.addEventListener('click', function() {
                    selectPhoto(photo);
                });

                photosContainer.appendChild(photoElement);
            });
        }

        function selectPhoto(photo) {
            if (photoPreview) photoPreview.src = photo.url;
            if (photoPreviewContainer) photoPreviewContainer.classList.remove('hidden');
            if (uploadPlaceholder) uploadPlaceholder.classList.add('hidden');

            const existingPhoto = document.getElementById('existing_photo');
            if (existingPhoto) {
                existingPhoto.value = photo.name;
            }

            if (currentPhotoName) {
                currentPhotoName.textContent = photo.name + ' (selected from library)';
            }

            if (photoUpload) {
                photoUpload.value = '';
            }

            closePhotoBrowser();
        }

        if (photoSearch) {
            photoSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();

                if (searchTerm === '') {
                    renderPhotos(allPhotos);
                } else {
                    const filteredPhotos = allPhotos.filter(photo =>
                        photo.name.toLowerCase().includes(searchTerm)
                    );

                    renderPhotos(filteredPhotos);

                    if (filteredPhotos.length === 0) {
                        if (noPhotos) noPhotos.classList.remove('hidden');
                    } else {
                        if (noPhotos) noPhotos.classList.add('hidden');
                    }
                }
            });
        }

        window.addEventListener('click', function(event) {
            if (event.target === photoBrowserModal) {
                closePhotoBrowser();
            }
        });

        function closeAlert(alertId) {
            const alertElement = document.getElementById(alertId);
            if (alertElement) {
                alertElement.style.display = 'none';
            }
        }
    </script>
</body>

</html>
