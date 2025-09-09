@extends('Main.manage-siswa')

@section('content')
<div class="p-4 sm:p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
    <div class="flex items-center justify-between mb-4 sm:mb-6">
        <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">Edit Akun Siswa</h1>
        <a href="{{ route('akun.siswa') }}" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 flex items-center text-sm">
            <i class="bi bi-arrow-left mr-1"></i> Kembali
        </a>
    </div>
    
    @if(session('success'))
    <div class="bg-green-100 dark:bg-green-900 border-l-4 border-green-500 text-green-700 dark:text-green-200 p-3 sm:p-4 mb-4 sm:mb-6 text-sm sm:text-base" role="alert">
        <p>{{ session('success') }}</p>
    </div>
    @endif
    
    @if(session('error'))
    <div class="bg-red-100 dark:bg-red-900 border-l-4 border-red-500 text-red-700 dark:text-red-200 p-3 sm:p-4 mb-4 sm:mb-6 text-sm sm:text-base" role="alert">
        <p>{{ session('error') }}</p>
    </div>
    @endif

    <!-- Informasi Siswa -->
    <div class="bg-gray-50 dark:bg-gray-700 p-3 sm:p-4 rounded-lg border border-gray-200 dark:border-gray-600 mb-4 sm:mb-6">
        <div class="flex items-center space-x-3 sm:space-x-4">
            <div class="flex-shrink-0">
                <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center">
                    @if(isset($user->warga_tels->foto_profile) && $user->warga_tels->foto_profile)
                        <img src="{{ Storage::disk('s3')->temporaryUrl('profile/' . $user->warga_tels->foto_profile, now()->addMinutes(5)) }}" 
                             alt="Profile" class="w-full h-full rounded-full object-cover">
                    @else
                        <i class="bi bi-person text-2xl sm:text-3xl text-gray-500 dark:text-gray-400"></i>
                    @endif
                </div>
            </div>
            <div>
                <h2 class="text-lg sm:text-xl font-medium text-gray-900 dark:text-white">{{ $user->warga_tels->name }}</h2>
                <div class="flex flex-col sm:flex-row sm:space-x-4 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                    <p>NIS: {{ $user->nis }}</p>
                    <p>Kelas: {{ $user->warga_tels->kelas }}</p>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('akun.siswa.update', $user->id) }}" method="POST" class="space-y-4 sm:space-y-6">
        @csrf
        
        <!-- Username dan Email -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <div>
                <label for="username" class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" class="w-full px-3 py-1.5 sm:px-4 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Username digunakan untuk login</p>
                @error('username')
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="email" class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-3 py-1.5 sm:px-4 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Email digunakan untuk verifikasi dan reset password</p>
                @error('email')
                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- RFID Card Management -->
        <div class="space-y-3 sm:space-y-4">
            <h3 class="text-base sm:text-lg font-medium text-gray-900 dark:text-white">RFID Card</h3>
            
            <div id="rfid-status" class="mb-3 sm:mb-4">
                @if($user->rfid_id)
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-3 sm:p-4 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-green-100 dark:bg-green-800 flex items-center justify-center mr-3">
                            <i class="bi bi-credit-card text-green-600 dark:text-green-300"></i>
                        </div>
                        <div>
                            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Kartu RFID Terhubung</p>
                            <p class="font-mono font-medium text-sm sm:text-base">{{ $user->rfid_id }}</p>
                        </div>
                    </div>
                    <button type="button" id="btn-remove-rfid" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs sm:text-sm">
                        <i class="bi bi-trash mr-1"></i> Hapus
                    </button>
                </div>
                @else
                <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-3 sm:p-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gray-100 dark:bg-gray-600 flex items-center justify-center mr-3">
                            <i class="bi bi-credit-card-x text-gray-500 dark:text-gray-400"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada kartu RFID yang terhubung</p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 sm:p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm sm:text-base font-medium text-gray-900 dark:text-white">Hubungkan Kartu RFID</h4>
                    <button type="button" id="btn-scan-rfid" class="text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300 text-xs sm:text-sm">
                        <i class="bi bi-upc-scan mr-1"></i> Scan Kartu
                    </button>
                </div>
                
                <div id="rfid-scan-area" class="hidden">
                    <div class="flex flex-col items-center justify-center py-4 sm:py-6">
                        <div id="nfc-icon" class="mb-4 sm:mb-6 pulse-animation">
                            <svg class="nfc-icon text-black dark:text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20,2L4,2c-1.1,0 -2,0.9 -2,2v16c0,1.1 0.9,2 2,2h16c1.1,0 2,-0.9 2,-2L22,4c0,-1.1 -0.9,-2 -2,-2zM20,20L4,20L4,4h16v16zM18,6h-5c-1.1,0 -2,0.9 -2,2v2.28c-0.6,0.35 -1,0.98 -1,1.72 0,1.1 0.9,2 2,2s2,-0.9 2,-2c0,-0.74 -0.4,-1.38 -1,-1.72L13,8h3v8L8,16L8,8h2L10,6L6,6v12h12L18,6z" />
                            </svg>
                        </div>
                        
                        <div id="success-icon" class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-green-500 flex items-center justify-center mb-4 sm:mb-6 hidden">
                            <i class="bi bi-check-lg text-white text-3xl sm:text-4xl"></i>
                        </div>
                        
                        <p id="scan-status" class="text-center text-sm sm:text-base mb-4">Tempelkan kartu RFID</p>
                        
                        <div class="flex space-x-2 sm:space-x-3">
                            <button type="button" id="btn-cancel-scan" class="px-3 py-1.5 sm:px-4 sm:py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-xs sm:text-sm font-medium rounded-md">
                                Batal
                            </button>
                            <button type="button" id="btn-confirm-rfid" class="px-3 py-1.5 sm:px-4 sm:py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-medium rounded-md hidden">
                                Gunakan Kartu Ini
                            </button>
                        </div>
                    </div>
                </div>
                
                <div id="rfid-manual-input" class="mt-3">
                    <label for="rfid_id" class="block text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">ID RFID (Manual Input)</label>
                    <input type="text" id="rfid_id" name="rfid_id" value="{{ old('rfid_id', $user->rfid_id) }}" class="w-full px-3 py-1.5 sm:px-4 sm:py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Masukkan ID RFID secara manual jika diperlukan</p>
                    @error('rfid_id')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-2 sm:space-x-3 pt-4 sm:pt-6">
            <a href="{{ route('akun.siswa') }}" class="px-3 py-1.5 sm:px-4 sm:py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 text-xs sm:text-sm font-medium rounded-md shadow-sm">
                Batal
            </a>
            <button type="submit" class="px-3 py-1.5 sm:px-4 sm:py-2 bg-primary-600 hover:bg-primary-700 text-white text-xs sm:text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

@push('styles')
<style>
    @keyframes pulse {
        0% {
            transform: scale(0.95);
            opacity: 0.7;
        }

        70% {
            transform: scale(1);
            opacity: 1;
        }

        100% {
            transform: scale(0.95);
            opacity: 0.7;
        }
    }

    .pulse-animation {
        animation: pulse 1.5s infinite;
    }

    .nfc-icon {
        width: 120px;
        height: 120px;
    }
    
    /* Responsive font sizes */
    @media (max-width: 640px) {
        .text-responsive {
            font-size: 0.875rem;
        }
        .text-responsive-sm {
            font-size: 0.8125rem;
        }
        .text-responsive-xs {
            font-size: 0.75rem;
        }
        .nfc-icon {
            width: 80px;
            height: 80px;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let rfidBuffer = "";
    let timeout = null;
    let scannedRfid = "";
    
    const btnScanRfid = document.getElementById('btn-scan-rfid');
    const btnCancelScan = document.getElementById('btn-cancel-scan');
    const btnConfirmRfid = document.getElementById('btn-confirm-rfid');
    const btnRemoveRfid = document.getElementById('btn-remove-rfid');
    const rfidScanArea = document.getElementById('rfid-scan-area');
    const rfidManualInput = document.getElementById('rfid-manual-input');
    const nfcIcon = document.getElementById('nfc-icon');
    const successIcon = document.getElementById('success-icon');
    const scanStatus = document.getElementById('scan-status');
    const rfidInput = document.getElementById('rfid_id');
    
    // Scan RFID button
    btnScanRfid.addEventListener('click', function() {
        rfidScanArea.classList.remove('hidden');
        rfidManualInput.classList.add('hidden');
        nfcIcon.classList.remove('hidden');
        successIcon.classList.add('hidden');
        btnConfirmRfid.classList.add('hidden');
        scanStatus.textContent = 'Tempelkan kartu RFID';
        scannedRfid = "";
    });
    
    // Cancel scan button
    btnCancelScan.addEventListener('click', function() {
        rfidScanArea.classList.add('hidden');
        rfidManualInput.classList.remove('hidden');
        scannedRfid = "";
    });
    
    // Confirm RFID button
    btnConfirmRfid.addEventListener('click', function() {
        // Check if RFID is already used by another account
        axios.post('{{ route("api.check.rfid.status") }}', {
            rfid_id: scannedRfid,
            current_user_id: {{ $user->id }}
        })
        .then(function(response) {
            if (response.data.success) {
                // RFID is available, set it to input
                rfidInput.value = scannedRfid;
                rfidScanArea.classList.add('hidden');
                rfidManualInput.classList.remove('hidden');
                
                Swal.fire({
                    icon: 'success',
                    title: 'RFID Terdeteksi',
                    text: 'RFID berhasil ditambahkan ke form',
                    confirmButtonText: 'OK',
                    customClass: {
                        title: 'text-base sm:text-lg',
                        content: 'text-sm',
                        confirmButton: 'text-sm'
                    }
                });
            } else {
                // RFID is already used
                Swal.fire({
                    icon: 'error',
                    title: 'RFID Sudah Digunakan',
                    html: `RFID ini sudah digunakan oleh:<br><b>${response.data.user.name}</b> (${response.data.user.kelas})`,
                    confirmButtonText: 'OK',
                    customClass: {
                        title: 'text-base sm:text-lg',
                        content: 'text-sm',
                        confirmButton: 'text-sm'
                    }
                });
            }
        })
        .catch(function(error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Terjadi kesalahan saat memeriksa RFID',
                confirmButtonText: 'OK',
                customClass: {
                    title: 'text-base sm:text-lg',
                    content: 'text-sm',
                    confirmButton: 'text-sm'
                }
            });
        });
    });
    
    // Remove RFID button
    if (btnRemoveRfid) {
        btnRemoveRfid.addEventListener('click', function() {
            Swal.fire({
                title: 'Remove RFID?',
                text: 'Are you sure you want to remove the RFID card from this account??',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, do it!',
                cancelButtonText: 'Cancel',
                customClass: {
                    title: 'text-base sm:text-lg',
                    content: 'text-sm',
                    confirmButton: 'text-sm',
                    cancelButton: 'text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post('{{ route("akun.siswa.rfid.remove", $user->id) }}')
                        .then(function(response) {
                            if (response.data.success) {
                                const rfidStatus = document.getElementById('rfid-status');
                                rfidStatus.innerHTML = `
                                    <div class="bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg p-3 sm:p-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gray-100 dark:bg-gray-600 flex items-center justify-center mr-3">
                                                <i class="bi bi-credit-card-x text-gray-500 dark:text-gray-400"></i>
                                            </div>
                                            <div>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada kartu RFID yang terhubung</p>
                                            </div>
                                        </div>
                                    </div>
                                `;
                                
                                // Clear RFID input
                                rfidInput.value = '';
                                
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Successfully',
                                    text: 'RFID successfully removed from this account',
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
                                    text: response.data.message,
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        title: 'text-base sm:text-lg',
                                        content: 'text-sm',
                                        confirmButton: 'text-sm'
                                    }
                                });
                            }
                        })
                        .catch(function(error) {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan saat menghapus RFID',
                                confirmButtonText: 'OK',
                                customClass: {
                                    title: 'text-base sm:text-lg',
                                    content: 'text-sm',
                                    confirmButton: 'text-sm'
                                }
                            });
                        });
                }
            });
        });
    }
    
    // Listen for RFID scan
    document.addEventListener("keydown", function(e) {
        // Only process keydown events when scan area is visible
        if (rfidScanArea.classList.contains('hidden')) {
            return;
        }
        
        if (timeout) clearTimeout(timeout);
        
        if (e.key === "Enter") {
            scannedRfid = rfidBuffer;
            
            axios.post('{{ route("api.check.rfid.status") }}', {
                rfid_id: scannedRfid,
                current_user_id: {{ $user->id }}
            })
            .then(function(response) {
                if (!response.data.success) {
                    // RFID is already used
                    Swal.fire({
                        icon: 'warning',
                        title: 'RFID Sudah Digunakan',
                        html: `RFID ini sudah digunakan oleh:<br><b>${response.data.user.name}</b> (${response.data.user.kelas})`,
                        confirmButtonText: 'OK',
                        customClass: {
                            title: 'text-base sm:text-lg',
                            content: 'text-sm',
                            confirmButton: 'text-sm'
                        }
                    });
                }else {
                    nfcIcon.classList.add('hidden');
                    successIcon.classList.remove('hidden');
                    scanStatus.textContent = 'Kartu RFID terdeteksi';
                    btnConfirmRfid.classList.remove('hidden');
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
            });
            
            rfidBuffer = "";
            return;
        }
        
        rfidBuffer += e.key;
        
        timeout = setTimeout(() => {
            rfidBuffer = "";
        }, 1000);
    });
    
    // Manual RFID input validation
    rfidInput.addEventListener('change', function() {
        if (this.value) {
            axios.post('{{ route("api.check.rfid.status") }}', {
                rfid_id: this.value,
                current_user_id: {{ $user->id }}
            })
            .then(function(response) {
                if (!response.data.success) {
                    // RFID is already used
                    Swal.fire({
                        icon: 'warning',
                        title: 'RFID Sudah Digunakan',
                        html: `RFID ini sudah digunakan oleh:<br><b>${response.data.user.name}</b> (${response.data.user.kelas})`,
                        confirmButtonText: 'OK',
                        customClass: {
                            title: 'text-base sm:text-lg',
                            content: 'text-sm',
                            confirmButton: 'text-sm'
                        }
                    });
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
            });
        }
    });
});
</script>
@endpush
@endsection