@extends('Main.manage-siswa')

@section('content')
    <div id="data-siswa" class="tab-content">
        <div class="flex flex-col md:flex-row justify-between gap-4 mb-6 w-full">
            <div class="flex w-full md:w-auto mb-4 md:mb-0">
                <input type="text" id="search-input" placeholder="Cari file foto..."
                    class="w-full rounded-l-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50 py-2 px-4 border bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                <button type="button" id="search-button"
                    class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-r-md whitespace-nowrap">
                    Cari
                </button>
            </div>

            <div class="flex gap-2">
                <button type="button" onclick="openUploadModal()"
                    class="w-full bg-green-500 text-center hover:bg-green-600 text-white font-bold py-2 px-4 rounded-md whitespace-nowrap">
                    <i class="bi bi-upload mr-1"></i> Upload Foto
                </button>
            </div>
        </div>

        <!-- Photo Grid -->
        <div id="photo-grid"
            class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 xl:grid-cols-8 gap-4 mb-6">
            @foreach ($photos as $index => $photo)
                <div class="bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-md hover:shadow-lg transition-all duration-300 border border-gray-200 dark:border-gray-700 group relative photo-card"
                    data-filename="{{ $photo }}">
                    <div
                        class="absolute top-2 left-2 bg-primary-600/90 text-white text-xs font-bold px-2 py-1 rounded-md z-10">
                        {{ $index + 1 }}
                    </div>

                    <div class="aspect-[3/4] w-full overflow-hidden bg-gray-100 dark:bg-gray-700">
                        <img src="{{ Storage::disk('s3')->temporaryUrl('profile/' . $photo, now()->addMinutes(5)) }}" loading="lazy" alt="{{ $photo }}"
                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                            onerror="this.src='{{ asset('src/file-not-found.jpg') }}'; this.onerror=null;">
                    </div>

                    <div class="px-2 py-1 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-600 dark:text-gray-400 truncate" title="{{ $photo }}">
                            {{ $photo }}
                        </p>
                    </div>

                    <div class="flex justify-between items-center p-2 bg-white dark:bg-gray-800">
                        <button type="button" onclick="copyFilename('{{ $photo }}')" title="Copy Filename"
                            class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 p-2 rounded-md transition-colors flex-1 mr-1">
                            <i class="bi bi-clipboard text-sm"></i>
                        </button>
                        <button type="button" onclick="confirmDelete('{{ $photo }}')" title="Delete File"
                            class="bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-600 dark:text-red-400 p-2 rounded-md transition-colors flex-1 ml-1">
                            <i class="bi bi-trash3 text-sm"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Empty State -->
        <div id="empty-state"
            class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-8 text-center mb-6 {{ count($photos) > 0 ? 'hidden' : '' }}">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                <i class="bi bi-image text-2xl text-gray-500 dark:text-gray-400"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Tidak ada foto</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Tidak ada file foto yang ditemukan di direktori
                storage/profile.</p>
            <button onclick="openUploadModal()"
                class="inline-flex items-center justify-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-md transition-colors">
                <i class="bi bi-upload mr-2"></i> Upload Foto
            </button>
        </div>
    </div>

    <!-- Upload Photo Modal -->
    <div id="uploadModal" class="fixed inset-0 z-50 hidden overflow-hidden">
        <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-80 backdrop-blur-sm"></div>

        <div class="relative h-full overflow-y-auto">
            <div class="flex items-center justify-center min-h-full px-4 pt-4 pb-20 text-center sm:block sm:p-0 relative">
                <!-- Modal panel -->
                <div
                    class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 rounded-xl shadow-xl sm:scale-100 scale-95 border border-gray-200 dark:border-gray-700 relative">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-xl font-semibold leading-6 text-gray-900 dark:text-gray-100">
                            Upload Foto
                        </h3>
                        <button type="button" onclick="closeUploadModal()"
                            class="text-gray-400 hover:text-gray-500 dark:hover:text-gray-300 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="mb-5">
                        <form id="uploadForm" action="{{ route('photos.upload') }}" method="POST"
                            enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            <div>
                                <label for="photo_files"
                                    class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    File Foto <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="flex items-center justify-center w-full">
                                        <div id="photo-drop-area"
                                            class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                <p class="mb-1 text-sm text-gray-500 dark:text-gray-400">
                                                    <span class="font-semibold">Klik untuk upload</span> atau seret foto
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    JPG, PNG, GIF (Max. 5MB)
                                                </p>
                                            </div>
                                            <input type="file" name="photo_files[]" id="photo_files"
                                                accept=".jpg,.jpeg,.png" class="hidden" multiple>
                                        </div>
                                    </div>
                                </div>
                                <div id="selectedFiles"
                                    class="hidden mt-3 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        File terpilih: <span id="fileCount">0</span>
                                    </p>
                                    <ul id="fileList"
                                        class="text-xs text-gray-600 dark:text-gray-400 space-y-1 max-h-32 overflow-y-auto">
                                    </ul>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3 pt-2">
                                <button type="button" onclick="closeUploadModal()"
                                    class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" id="submitUpload"
                                    class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                    disabled>
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    Upload
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('uploadForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const submitBtn = document.getElementById('submitUpload');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Mengupload...';

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                    },
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        theme: localStorage.getItem('theme') === 'dark' ? 'dark' : 'light',
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        },
                        didClose: () => {
                            closeUploadModal();
                            location.reload();
                        }
                    });
                    Toast.fire({
                        icon: "success",
                        title: "Photo successfully saved to cloud storage"
                    });
                } else {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: "top-end",
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        theme: localStorage.getItem('theme') === 'dark' ? 'dark' : 'light',
                        didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer;
                            toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                    Toast.fire({
                        icon: "error",
                        title: result.message
                    });
                }

            } catch (error) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    theme: localStorage.getItem('theme') === 'dark' ? 'dark' : 'light',
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    },
                    didClose: () => {
                        closeUploadModal();
                        location.reload();
                    }
                });
                Toast.fire({
                    icon: "error",
                    title: error.message
                });
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Upload';
            }
        });
    </script>

    <script>
        const searchInput = document.getElementById('search-input');
        const searchButton = document.getElementById('search-button');
        const photoGrid = document.getElementById('photo-grid');
        const emptyState = document.getElementById('empty-state');
        const photoCards = document.querySelectorAll('.photo-card');
        const uploadModal = document.getElementById('uploadModal');
        const photoDropArea = document.getElementById('photo-drop-area');
        const photoFilesInput = document.getElementById('photo_files');
        const selectedFiles = document.getElementById('selectedFiles');
        const fileCount = document.getElementById('fileCount');
        const fileList = document.getElementById('fileList');
        const submitUpload = document.getElementById('submitUpload');

        // Search functionality
        function filterPhotos() {
            const searchTerm = searchInput.value.toLowerCase();
            let visibleCount = 0;

            photoCards.forEach(card => {
                const filename = card.dataset.filename.toLowerCase();
                if (filename.includes(searchTerm)) {
                    card.classList.remove('hidden');
                    visibleCount++;
                } else {
                    card.classList.add('hidden');
                }
            });

            // Show/hide empty state
            if (visibleCount === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        }

        searchButton.addEventListener('click', filterPhotos);
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                filterPhotos();
            }
        });

        function copyFilename(filename) {
            navigator.clipboard.writeText(filename).then(function() {
                const toast = document.createElement('div');
                toast.className =
                    'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 animate-fade-in-up';
                toast.textContent = 'Nama file berhasil disalin: ' + filename;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.classList.add('animate-fade-out');
                    setTimeout(() => {
                        document.body.removeChild(toast);
                    }, 300);
                }, 3000);
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                alert('Gagal menyalin nama file: ' + filename);
            });
        }

        function confirmDelete(filename) {
            Swal.fire({
                title: "Are you sure?",
                text: "Are you sure you want to delete photo " + filename + "?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                theme: localStorage.getItem('theme') === 'dark' ? 'dark' : 'light',
                allowOutsideClick: true,
                allowEscapeKey: true,

            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('{{ route('photos.delete') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                filename: filename
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                const card = document.querySelector(`.photo-card[data-filename="${filename}"]`);
                                if (card) {
                                    card.remove();
                                }

                                const toast = document.createElement('div');
                                toast.className =
                                    'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg z-50 animate-fade-in-up';
                                toast.textContent = 'File berhasil dihapus: ' + filename;
                                document.body.appendChild(toast);

                                setTimeout(() => {
                                    toast.classList.add('animate-fade-out');
                                    setTimeout(() => {
                                        document.body.removeChild(toast);
                                    }, 300);
                                }, 3000);

                                if (photoGrid.children.length === 0) {
                                    emptyState.classList.remove('hidden');
                                }
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    text: "Failed to delete file: " + data.message,
                                    theme: localStorage.getItem('theme') === 'dark' ? 'dark' : 'light',
                                    icon: "error"
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                title: "Error!",
                                text: "An error occurred while deleting the file",
                                theme: localStorage.getItem('theme') === 'dark' ? 'dark' : 'light',
                                icon: "error"
                            });
                        });
                }
            });
        }

        function openUploadModal() {
            uploadModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeUploadModal() {
            uploadModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
            resetUploadForm();
        }

        function resetUploadForm() {
            document.getElementById('uploadForm').reset();
            selectedFiles.classList.add('hidden');
            fileList.innerHTML = '';
            fileCount.textContent = '0';
            submitUpload.disabled = true;
        }

        photoFilesInput.addEventListener('change', function() {
            handleFileSelection(this.files);
        });

        function handleFileSelection(files) {
            if (files && files.length > 0) {
                selectedFiles.classList.remove('hidden');
                fileCount.textContent = files.length;
                fileList.innerHTML = '';

                let validFiles = true;
                const maxSize = 5 * 1024 * 1024;

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const fileItem = document.createElement('li');

                    if (file.size > maxSize) {
                        fileItem.className = 'text-red-500';
                        fileItem.textContent = `${file.name} (${formatFileSize(file.size)}) - File terlalu besar`;
                        validFiles = false;
                    } else {
                        fileItem.textContent = `${file.name} (${formatFileSize(file.size)})`;
                    }

                    fileList.appendChild(fileItem);
                }

                submitUpload.disabled = !validFiles || files.length === 0;
            } else {
                selectedFiles.classList.add('hidden');
                submitUpload.disabled = true;
            }
        }

        function formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            else return (bytes / 1048576).toFixed(1) + ' MB';
        }

        // Drag and drop functionality
        photoDropArea.addEventListener('click', function() {
            photoFilesInput.click();
        });

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            photoDropArea.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            photoDropArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            photoDropArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            photoDropArea.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
        }

        function unhighlight() {
            photoDropArea.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
        }

        photoDropArea.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            photoFilesInput.files = files;
            handleFileSelection(files);
        });

        window.addEventListener('click', function(event) {
            if (event.target === uploadModal) {
                closeUploadModal();
            }
        });
    </script>

    <style>
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
            }

            to {
                opacity: 0;
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.3s ease-out forwards;
        }

        .animate-fade-out {
            animation: fadeOut 0.3s ease-out forwards;
        }
    </style>
@endsection
