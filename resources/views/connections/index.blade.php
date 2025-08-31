<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sambungkan Aplikasi</title>
    @stack('styles')
    @vite(['resources/js/app.js'])
    @stack('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
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
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
</head>

<body class="h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
    <div class="min-h-full">
        @include('Cert.head')

        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8 content">
            @if (session('success'))
                <div id="success-alert"
                    class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-green-500" role="button"
                            onclick="closeAlert('success-alert')" xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 20 20">
                            <title>Close</title>
                            <path
                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                        </svg>
                    </span>
                </div>
            @endif

            @if (session('error'))
                <div id="error-alert"
                    class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button"
                            onclick="closeAlert('error-alert')" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                            <title>Close</title>
                            <path
                                d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
                        </svg>
                    </span>
                </div>
            @endif

            <script>
                function closeAlert(alertId) {
                    const alertElement = document.getElementById(alertId);
                    if (alertElement) {
                        alertElement.style.display = 'none';
                    }
                }
            </script>
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-row items-center justify-between">
                        <h2 class="max-md:text-xl font-bold leading-7 sm:truncate text-2xl">Sambungkan Aplikasi</h2>
                    </div>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Kelola koneksi aplikasi eksternal
                    </p>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                        Sambungkan Aplikasi
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500 dark:text-gray-400">
                        Hubungkan akun Anda dengan aplikasi lain untuk memperluas fungsionalitas
                    </p>
                </div>

                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="md:flex md:items-center md:justify-between">
                            <div class="flex items-center">
                                <img src="{{ asset('src/sipola.webp') }}" class="w-12 h-auto" alt="">
                                <div class="ml-4">
                                    <h4 class="text-lg font-medium text-gray-900 dark:text-white">SIPOLA</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Sambungkan dengan aplikasi SIPOLA</p>
                                </div>
                            </div>
                            <div class="mt-4 md:mt-0 flex items-center space-x-2">
                                @if(isset($connections['sipola']) && $connections['sipola'])
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <i class="fas fa-check-circle mr-1"></i> Connected
                                    </span>
                                    <button type="button" onclick="confirmDisconnect('sipola')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800">
                                        Disconnect
                                    </button>
                                @else
                                    @php
                                        $statusMembership = App\Models\AdminAccount::where('id', auth()->user()->id)->where('membership', true)->exists();
                                    @endphp

                                    @if ($statusMembership)
                                        <button type="button" onclick="showConnectModal('sipola', '{{ asset('src/sipola.webp') }}')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 cursor-pointer">
                                            Connect
                                        </button>
                                    @else
                                        <button type="button" onclick="showConnectModal('sipola', '{{ asset('src/sipola.webp') }}')" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 disabled:bg-gray-500 cursor-pointer disabled:cursor-not-allowed" disabled>
                                            Connect
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-crown-icon ml-2 text-yellow-500 lucide-crown"><path d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z"/><path d="M5 21h14"/></svg>
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Connection Modal -->
            <div id="connectModal" class="fixed inset-0 z-10 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <img src="" class="w-12 h-auto" id="modalIcon" alt="">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                        Connect to <span id="serviceName"></span>
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Please enter your API key or secret to connect to this service.
                                        </p>
                                        <div class="mt-4">
                                            <label for="apiKey" class="block text-sm font-medium text-gray-700 dark:text-gray-300">API Key / Secret <span class="text-red-600 font-bold">*</span></label>
                                            <input type="text" name="apiKey" id="apiKey" class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-md p-2" required>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" onclick="connectService()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 sm:ml-3 sm:w-auto sm:text-sm">
                                Connect
                            </button>
                            <button type="button" onclick="closeModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Disconnect Confirmation Modal -->
            <div id="disconnectModal" class="fixed inset-0 z-10 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400"></i>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                        Disconnect <span id="disconnectServiceName"></span>
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Are you sure you want to disconnect this service? Any integrations using this connection will stop working.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" id="confirmDisconnectBtn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 dark:focus:ring-offset-gray-800 sm:ml-3 sm:w-auto sm:text-sm">
                                Disconnect
                            </button>
                            <button type="button" onclick="closeDisconnectModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:focus:ring-offset-gray-800 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        let currentService = '';
        
        function showConnectModal(service, pathImg) {
            currentService = service;
            document.getElementById('serviceName').textContent = service.charAt(0).toUpperCase() + service.slice(1);
            
            // Set the appropriate icon
            const iconElement = document.getElementById('modalIcon');
            iconElement.src = `${pathImg}`;
            
            document.getElementById('connectModal').classList.remove('hidden');
        }
        
        function closeModal() {
            document.getElementById('connectModal').classList.add('hidden');
            document.getElementById('apiKey').value = '';
        }
        
        function connectService() {
            const apiKey = document.getElementById('apiKey').value.trim();
            
            if (!apiKey) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please enter a valid API key or secret',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0ea5e9',
                    background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
                });
                return;
            }
            
            // Here you would normally send an AJAX request to your backend
            
            fetch('{{ route("connect.service") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    service: currentService,
                    apiKey: apiKey
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: `Successfully connected to ${currentService}`,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#0ea5e9',
                        background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to connect. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#0ea5e9',
                        background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'An unexpected error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0ea5e9',
                    background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
                });
                console.error('Error:', error);
            });
            
            closeModal();
        }
        
        function confirmDisconnect(service) {
            currentService = service;
            document.getElementById('disconnectServiceName').textContent = service.charAt(0).toUpperCase() + service.slice(1);
            
            // Set up the disconnect button
            const disconnectBtn = document.getElementById('confirmDisconnectBtn');
            disconnectBtn.onclick = function() {
                disconnectService(service);
            };
            
            document.getElementById('disconnectModal').classList.remove('hidden');
        }
        
        function closeDisconnectModal() {
            document.getElementById('disconnectModal').classList.add('hidden');
        }
        
        function disconnectService(service) {
            fetch('{{ route("disconnect.service") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    service: service
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: `Successfully disconnected from ${service}`,
                        icon: 'success',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#0ea5e9',
                        background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data.message || 'Failed to disconnect. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#0ea5e9',
                        background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                        color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: 'Error!',
                    text: 'An unexpected error occurred. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#0ea5e9',
                    background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                    color: document.documentElement.classList.contains('dark') ? '#fff' : '#000'
                });
                console.error('Error:', error);
            });
            
            closeDisconnectModal();
        }

        document.addEventListener('DOMContentLoaded', () => {
            const currentPath = '{{ request()->route()->getName() }}';

            if (currentPath === 'connections.index') {
                document.getElementById('btn-connections').classList.remove('border-transparent', 'text-gray-500',
                    'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300',
                    'hover:border-gray-300', 'dark:hover:border-gray-600');
                document.getElementById('btn-connections').classList.add('border-primary-600', 'text-primary-600',
                    'dark:text-primary-400', 'dark:border-primary-400', 'hover:text-primary-700',
                    'dark:hover:text-primary-300');
            } else if (currentPath === 'connections.logs') {
                document.getElementById('btn-logs').classList.remove('border-transparent', 'text-gray-500',
                    'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300',
                    'hover:border-gray-300', 'dark:hover:border-gray-600');
                document.getElementById('btn-logs').classList.add('border-primary-600', 'text-primary-600',
                    'dark:text-primary-400', 'dark:border-primary-400', 'hover:text-primary-700',
                    'dark:hover:text-primary-300');
                document.getElementById('btn-connections').classList.remove('border-primary-600', 'text-primary-600',
                    'dark:text-primary-400', 'dark:border-primary-400', 'hover:text-primary-700',
                    'dark:hover:text-primary-300')
                document.getElementById('btn-connections').classList.add('border-transparent', 'text-gray-500',
                    'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300',
                    'hover:border-gray-300', 'dark:hover:border-gray-600');
            } else if (currentPath === 'connections.settings') {
                document.getElementById('btn-settings').classList.remove('border-transparent', 'text-gray-500',
                    'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300',
                    'hover:border-gray-300', 'dark:hover:border-gray-600');
                document.getElementById('btn-settings').classList.add('border-primary-600', 'text-primary-600',
                    'dark:text-primary-400', 'dark:border-primary-400', 'hover:text-primary-700',
                    'dark:hover:text-primary-300');
                document.getElementById('btn-connections').classList.remove('border-primary-600', 'text-primary-600',
                    'dark:text-primary-400', 'dark:border-primary-400', 'hover:text-primary-700',
                    'dark:hover:text-primary-300')
                document.getElementById('btn-connections').classList.add('border-transparent', 'text-gray-500',
                    'dark:text-gray-400', 'hover:text-gray-700', 'dark:hover:text-gray-300',
                    'hover:border-gray-300', 'dark:hover:border-gray-600');
            }
        });
    </script>
</body>

</html>