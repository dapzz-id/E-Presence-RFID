@extends('Main.manage-siswa')

@section('content')
<div class="p-4 sm:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
    <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-4 sm:mb-6">Tambah Akun Siswa Baru</h1>
    
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-3 sm:p-4 mb-4 sm:mb-6 text-sm sm:text-base" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-3 sm:p-4 mb-4 sm:mb-6 text-sm sm:text-base" role="alert">
        <p>{{ session('error') }}</p>
    </div>
    @endif
    
    <form action="{{ route('akun.siswa.create.post') }}" method="POST" class="space-y-4 sm:space-y-6">
        @csrf
        
        <!-- NIS Selection with Search -->
        <div class="grid grid-cols-1 gap-4 sm:gap-6">
            <div>
                <label for="nis" class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Siswa (NIS)</label>
                <select id="nis" name="nis" class="select2-nis w-full px-3 py-1.5 sm:px-4 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($siswa as $s)
                        <option value="{{ $s->nis }}"
                            data-name="{{ $s->name }}"
                            data-kelas="{{ $s->kelas }}"
                            data-profile="{{ $s->foto_profile != null && $s->foto_profile != '' ? Storage::disk('s3')->temporaryUrl('profile/' . $s->foto_profile, now()->addMinutes(5)) : asset('src/LOGO RAADEVELOPERZ (SUPER HD).png') }}"
                            {{ old('nis') == $s->nis ? 'selected' : '' }}>
                            {{ $s->nis }} - {{ $s->name }} ({{ $s->kelas }})
                        </option>
                    @endforeach
                </select>                
                @error('nis')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Student Info Card (akan ditampilkan setelah memilih siswa) -->
        <div id="student-info" class="hidden bg-gray-50 dark:bg-gray-700 p-3 sm:p-4 rounded-lg border border-gray-200 dark:border-gray-600">
            <div class="flex items-center space-x-3 sm:space-x-4">
                <div class="flex-shrink-0">
                    <div class="max-w-16 max-h-20 aspect-[3/4] flex items-center justify-center ml-2">
                        <img id="student-profile">
                    </div>                                      
                </div>
                <div>
                    <h3 id="student-name" class="text-base sm:text-lg font-medium text-gray-900 dark:text-white"></h3>
                    <div class="flex flex-col sm:flex-row sm:space-x-4 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                        <div>NIS: <span id="student-nis" class="font-medium"></span></div>
                        <div>Kelas: <span id="student-kelas" class="font-medium"></span></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Username, Email, dan Kode Verifikasi -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <div>
                <label for="username" class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
                <input type="text" id="username" value="{{ old('username') }}" name="username" class="w-full px-3 py-1.5 sm:px-4 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm" placeholder="Masukkan username">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Username akan digunakan untuk login</p>
                @error('username')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="email" class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                <input type="email" id="email" value="{{ old('email') }}" name="email" class="w-full px-3 py-1.5 sm:px-4 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm" placeholder="contoh@email.com">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email akan digunakan untuk verifikasi dan reset password</p>
                @error('email')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <div>
                <label for="password" class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Password</label>
                <input type="password" id="password" value="{{ old('password') }}" name="password" class="w-full px-3 py-1.5 sm:px-4 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm" placeholder="Masukkan password">
                @error('password')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="password_confirmation" class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Konfirmasi Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-3 py-1.5 sm:px-4 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm" placeholder="Konfirmasi password">
                @error('password_confirmation')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        {{-- <div>
            <label for="verification_code" class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Verifikasi Email</label>
            <div class="flex space-x-2">
                <input type="text" id="verification_code" value="{{ old('verification_code') }}" name="verification_code" class="w-full px-3 py-1.5 sm:px-4 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm" placeholder="Masukkan kode verifikasi">
                <button type="button" id="send-code-btn" class="px-3 py-1.5 sm:px-4 sm:py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 whitespace-nowrap">
                    Kirim Kode
                </button>
            </div>
            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kode verifikasi akan dikirim ke email yang dimasukkan</p>
            @error('verification_code')
            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div> --}}
        
        <div class="flex justify-end space-x-2 sm:space-x-3 pt-4 sm:pt-6">
            <a href="{{ route('akun.siswa') }}" class="px-3 py-1.5 sm:px-4 sm:py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-xs sm:text-sm font-medium rounded-md shadow-sm">
                Batal
            </a>
            <button type="submit" class="px-3 py-1.5 sm:px-4 sm:py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Simpan
            </button>
        </div>
    </form>
</div>

<!-- Tambahkan CSS untuk Select2 di head -->
@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Base Select2 Styling */
    .select2-container--default .select2-selection--single {
        height: 38px;
        padding: 4px 10px;
        border-color: rgb(209, 213, 219);
        border-radius: 0.375rem;
        font-size: 0.875rem;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 38px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        color: rgb(17, 24, 39);
        font-size: 0.875rem;
    }
    
    .select2-container--default .select2-search--dropdown .select2-search__field {
        border-color: rgb(209, 213, 219);
        border-radius: 0.25rem;
        padding: 4px 8px;
        font-size: 0.875rem;
    }
    
    .select2-dropdown {
        border-color: rgb(209, 213, 219);
        border-radius: 0.375rem;
    }
    
    .select2-results__option {
        padding: 6px 10px;
        font-size: 0.875rem;
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: rgb(59, 130, 246);
    }
    
    /* Dark mode support */
    .dark .select2-container--default .select2-selection--single {
        background-color: rgb(55, 65, 81);
        border-color: rgb(75, 85, 99);
    }
    
    .dark .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: rgb(243, 244, 246);
    }
    
    .dark .select2-dropdown {
        background-color: rgb(55, 65, 81);
        border-color: rgb(75, 85, 99);
    }
    
    .dark .select2-container--default .select2-results__option {
        color: rgb(243, 244, 246);
    }
    
    .dark .select2-container--default .select2-search--dropdown .select2-search__field {
        background-color: rgb(75, 85, 99);
        border-color: rgb(107, 114, 128);
        color: rgb(243, 244, 246);
    }
    
    /* Mobile Responsive Adjustments */
    @media (max-width: 640px) {
        .select2-container--default .select2-selection--single {
            height: 34px;
            padding: 3px 8px;
            font-size: 0.8125rem;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 34px;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 26px;
            font-size: 0.8125rem;
        }
        
        .select2-results__option {
            padding: 5px 8px;
            font-size: 0.8125rem;
        }
        
        .select2-container--default .select2-search--dropdown .select2-search__field {
            padding: 3px 6px;
            font-size: 0.8125rem;
        }
        
        .select2-dropdown {
            font-size: 0.8125rem;
        }
    }
</style>
@endpush

<!-- Tambahkan JavaScript untuk Select2 di bagian bawah -->
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi Select2 untuk dropdown NIS
    $(document).ready(function() {
        $('.select2-nis').select2({
            placeholder: "Cari siswa berdasarkan NIS atau nama...",
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "Tidak ada siswa yang ditemukan";
                },
                searching: function() {
                    return "Mencari...";
                }
            }
        });
        
        // Event handler saat nilai select berubah
        $('.select2-nis').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const studentInfo = document.getElementById('student-info');
            const studentName = document.getElementById('student-name');
            const studentNis = document.getElementById('student-nis');
            const studentKelas = document.getElementById('student-kelas');
            const studentProfile = document.getElementById('student-profile');
            const defaultProfile = "{{ asset('src/LOGO RAADEVELOPERZ (SUPER HD).png') }}";
            const usernameInput = document.getElementById('username');
            
            if (this.value) {
                // Tampilkan informasi siswa
                studentName.textContent = selectedOption.data('name');
                studentNis.textContent = this.value;
                studentKelas.textContent = selectedOption.data('kelas');
                studentProfile.src = selectedOption.data('profile');
                studentInfo.classList.remove('hidden');
                
                // Generate username dari nama (huruf pertama nama depan + nama belakang + 4 digit terakhir NIS)
                const nameParts = selectedOption.data('name').toLowerCase().split(' ');
                let username = '';
                
                if (nameParts.length > 1) {
                    username = nameParts[0].charAt(0) + nameParts[nameParts.length - 1];
                } else {
                    username = nameParts[0];
                }
                
                username += this.value.slice(-4);
                usernameInput.value = username;
            } else {
                studentInfo.classList.add('hidden');
                usernameInput.value = '';
            }
        });
    });
    
    // Kirim kode verifikasi
    const sendCodeBtn = document.getElementById('send-code-btn');
    const emailInput = document.getElementById('email');
    
    sendCodeBtn.addEventListener('click', function() {
        const email = emailInput.value;
        
        if (!email) {
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: 'Masukkan email terlebih dahulu',
                confirmButtonText: 'OK',
                customClass: {
                    title: 'text-base sm:text-lg',
                    content: 'text-sm',
                    confirmButton: 'text-sm'
                }
            });
            return;
        }
        
        // Tampilkan loading state
        const originalText = this.textContent;
        this.disabled = true;
        this.innerHTML = `
            <svg class="animate-spin -ml-1 mr-1 h-3 w-3 sm:h-4 sm:w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="text-xs sm:text-sm">Mengirim...</span>
        `;
        
        // Kirim request ke server
        fetch('/api/send-verification-code', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ email: email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                emailInput.readOnly = true;
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Kode verifikasi telah dikirim ke email',
                    confirmButtonText: 'OK',
                    customClass: {
                        title: 'text-base sm:text-lg',
                        content: 'text-sm',
                        confirmButton: 'text-sm'
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: data.message || 'Gagal mengirim kode verifikasi',
                    confirmButtonText: 'OK',
                    customClass: {
                        title: 'text-base sm:text-lg',
                        content: 'text-sm',
                        confirmButton: 'text-sm'
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat mengirim kode verifikasi',
                confirmButtonText: 'OK',
                customClass: {
                    title: 'text-base sm:text-lg',
                    content: 'text-sm',
                    confirmButton: 'text-sm'
                }
            });
        })
        .finally(() => {
            setTimeout(() => {
                this.disabled = false;
                this.textContent = originalText;
            }, 3000);
        });
    });
});
</script>
@endpush
@endsection