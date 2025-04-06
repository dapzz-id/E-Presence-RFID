<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>E-PRESENCE - Tambah Siswa</title>
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
    
    /* For Webkit browsers like Chrome/Safari */
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

    /* For dark mode */
    .dark .custom-scrollbar::-webkit-scrollbar-track {
        background: #1f2937;
    }
    
    .dark .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #4b5563;
    }
    
    .dark .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #6b7280;
    }

    /* body {
      background-color: #0f172a;
      color: #f8fafc;
    } */
</style>
</head>
<body class="h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
  <!-- Header -->
  @include('Cert.head')

  <!-- Main Content -->
  <div class="container mx-auto px-4 py-6">
    <div class="mb-6">
      <a href="{{ route('siswa') }}" class="flex items-center text-primary mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
          <path d="m12 19-7-7 7-7"></path>
          <path d="M19 12H5"></path>
        </svg>
        Kembali
      </a>
      <h1 class="text-3xl font-bold">Tambah Siswa</h1>
      <p class="text-gray-400">Tambahkan data siswa baru</p>
    </div>

    <div class="max-w-3xl">
      <form action="{{ route('siswa.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="space-y-4">
            <div>
              <label for="nis" class="block text-sm font-medium mb-1">NIS</label>
              <input
                type="text"
                id="nis"
                name="nis"
                placeholder="Masukkan NIS"
                class="w-full px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded"
                required
              />
            </div>
            
            <div>
              <label for="name" class="block text-sm font-medium mb-1">Nama Lengkap</label>
              <input
                type="text"
                id="name"
                name="name"
                placeholder="Masukkan nama lengkap"
                class="w-full px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded"
                required
              />
            </div>
            
            <div>
              <label for="class" class="block text-sm font-medium mb-1">Kelas</label>
              <select
                id="class"
                name="class"
                class="w-full px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded"
                required
              >
                <option value="" disabled selected>Pilih kelas</option>
                @foreach (['X RPL 1', 'X RPL 2', 'X RPL 3', 'X RPL 4', 'XI RPL 1', 'XI RPL 2', 'XI RPL 3', 'XI RPL 4', 'XII RPL 1', 'XII RPL 2', 'XII RPL 3', 'XII RPL 4', 'X DKV 1', 'X DKV 2', 'X DKV 3', 'XI DKV 1', 'XI DKV 2', 'XI DKV 3', 'XII DKV 1', 'XII DKV 2', 'XII DKV 3', 'X TKJ 1', 'X TKJ 2', 'X TKJ 3', 'XI TKJ 1', 'XI TKJ 2', 'XI TKJ 3', 'XII TKJ 1', 'XII TKJ 2', 'XII TKJ 3', 'X TRANSMISI', 'XI TRANSMISI', 'XII TRANSMISI'] as $kelasOption)
                    <option value="{{ $kelasOption }}" {{ request('kelas') == $kelasOption ? 'selected' : '' }}>{{ $kelasOption }}</option>
                @endforeach
              </select>
            </div>
            
            <div>
              <label for="address" class="block text-sm font-medium mb-1">Alamat</label>
              <textarea name="address" id="address" autocomplete="street-address" cols="30" rows="5" required class="w-full px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 rounded" placeholder="Masukan alamat rumah"></textarea>
            </div>
          </div>
          
          <div class="space-y-4">
            <div>
              <label class="block text-sm font-medium mb-1 ml-4 max-sm:ml-0">Foto</label>
              <div class="mt-2 flex flex-col items-center justify-center border-2 border-dashed aspect-[3/4] ml-4 max-sm:mx-auto border-gray-600 rounded-lg p-6 h-64" id="photo-container">
                <div class="text-center" id="upload-placeholder">
                  <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-400">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" x2="12" y1="3" y2="15"></line>
                  </svg>
                  <div class="mt-2">
                    <label
                      for="photo-upload"
                      class="cursor-pointer bg-primary hover:bg-primary/90 dark:text-white py-2 px-4 rounded-md"
                    >
                      Pilih Foto
                    </label>
                    <input
                      id="photo-upload"
                      name="photo"
                      type="file"
                      accept="image/*"
                      class="hidden"
                    />
                  </div>
                  <p class="mt-2 text-sm text-gray-400 max-sm:text-xs">PNG, JPG up to 2MiB</p>
                </div>
                <div class="relative w-full h-full hidden" id="photo-preview-container">
                  <img 
                    src="/placeholder.svg" 
                    alt="Preview" 
                    id="photo-preview"
                    class="w-full h-full object-cover rounded-lg"
                  />
                  <button
                    type="button"
                    id="remove-photo"
                    class="absolute top-2 right-2 bg-black text-white p-1 rounded-full"
                  >
                    ×
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <div class="mt-8 flex justify-end space-x-4">
          <a href="{{ route('siswa') }}">
            <button type="button" class="px-4 py-2 border border-gray-600 text-white rounded-md hover:bg-gray-700">Batal</button>
          </a>
          <button type="submit" class="px-4 py-2 bg-accent hover:bg-accent/90 text-white rounded-md">Simpan</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Update time
    function updateTime() {
      const now = new Date();
      const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
      document.getElementById('current-time').textContent = timeString;
    }
    
    setInterval(updateTime, 1000);
    updateTime();
    
    // Handle photo upload
    const photoUpload = document.getElementById('photo-upload');
    const uploadPlaceholder = document.getElementById('upload-placeholder');
    const photoPreviewContainer = document.getElementById('photo-preview-container');
    const photoPreview = document.getElementById('photo-preview');
    const removePhotoBtn = document.getElementById('remove-photo');
    
    photoUpload.addEventListener('change', function(e) {
      if (e.target.files && e.target.files[0]) {
        const file = e.target.files[0];
        const reader = new FileReader();
        
        reader.onload = function(event) {
          photoPreview.src = event.target.result;
          uploadPlaceholder.classList.add('hidden');
          photoPreviewContainer.classList.remove('hidden');
        }
        
        reader.readAsDataURL(file);
      }
    });
    
    removePhotoBtn.addEventListener('click', function() {
      photoUpload.value = '';
      photoPreview.src = '';
      photoPreviewContainer.classList.add('hidden');
      uploadPlaceholder.classList.remove('hidden');
    });
  </script>
</body>
</html>