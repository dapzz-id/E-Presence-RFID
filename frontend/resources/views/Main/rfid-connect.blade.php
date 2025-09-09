<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungkan RFID</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute(
            'content');
    </script>
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

            this.blur();

            const dummy = document.getElementById('main-guard');
            if (dummy) dummy.focus();
        }
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

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
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
</head>

<body class="h-full bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-200">
    <div class="min-h-full flex flex-col" id="main-guard" tabindex="-1">
        @include('Cert.head')

        <main class="flex-1 flex flex-col items-center justify-center p-6">
            <div id="layoutFirst" class="w-full max-w-md hidden flex-col items-center">
                <form class="hidden">
                    <input type="hidden" id="rfid_input" name="uid" autofocus>
                </form>

                <div id="nfc-icon" class="mb-10 pulse-animation">
                    <svg class="nfc-icon text-black dark:text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path
                            d="M20,2L4,2c-1.1,0 -2,0.9 -2,2v16c0,1.1 0.9,2 2,2h16c1.1,0 2,-0.9 2,-2L22,4c0,-1.1 -0.9,-2 -2,-2zM20,20L4,20L4,4h16v16zM18,6h-5c-1.1,0 -2,0.9 -2,2v2.28c-0.6,0.35 -1,0.98 -1,1.72 0,1.1 0.9,2 2,2s2,-0.9 2,-2c0,-0.74 -0.4,-1.38 -1,-1.72L13,8h3v8L8,16L8,8h2L10,6L6,6v12h12L18,6z" />
                    </svg>
                </div>

                <div id="success-icon"
                    class="w-24 h-24 rounded-full bg-green-500 flex items-center justify-center mb-10 hidden">
                    <i class="bi bi-check-lg text-white text-5xl"></i>
                </div>

                <div id="uid-container" class="mb-8 hidden text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">UID Card:</p>
                    <p id="card-uid" class="font-mono font-bold text-lg">--</p>
                </div>

                <p id="status-text" class="text-center text-xl mb-12 font-medium">Tempelkan kartu pelajar Anda</p>

                <div class="flex gap-4 w-full max-w-xs mx-auto">
                    <button id="btn-continue"
                        class="w-full disabled:bg-gray-400 disabled:cursor-not-allowed cursor-pointer bg-green-600 hover:bg-green-700 text-white py-3 px-4 rounded-md font-medium transition-colors"
                        disabled>
                        Lanjutkan
                    </button>
                </div>
            </div>

            <div id="layoutSecond" class="flex-1 flex-col p-4 max-w-2xl mx-auto w-full hidden">
                <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <div
                            class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center mr-3">
                            <i class="bi bi-credit-card text-primary-600 dark:text-primary-300"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Kartu RFID</p>
                            <p class="font-mono font-medium" id="card-uid2">-</p>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-lg font-medium mb-3">Pilih Akun Siswa</h2>

                    <div class="flex flex-row justify-between items-center">
                        <div class="relative mb-4 w-full mr-4">
                            <i
                                class="bi bi-search absolute left-2.5 top-2.5 h-4 w-4 text-gray-500 dark:text-gray-400"></i>
                            <input type="search" placeholder="Cari nama, kelas, atau NIS..."
                                class="flex h-10 w-full rounded-md border text-black dark:text-white border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-gray-500 dark:placeholder:text-gray-400 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 dark:focus-visible:ring-primary-400 focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 pl-8"
                                id="searchInput" onkeyup="filterAccounts()">
                        </div>
                        <button
                            class="inline-flex items-center px-2 py-2 mb-4 sm:px-3 sm:py-3 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-orange-500 hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                            title="Refresh Page Button" onclick="refreshPage()">
                            <i class="fa-solid fa-rotate-right"></i>
                        </button>

                        <script>
                            function refreshPage() {
                                localStorage.setItem("forceUID", "true");
                                window.location.reload();
                            }

                            document.addEventListener("DOMContentLoaded", function() {
                                if (localStorage.getItem("forceUID") === "true") {
                                    localStorage.removeItem("forceUID");
                                    let uidConstant = localStorage.getItem("uid");

                                    if (uidConstant) {
                                        document.getElementById('layoutFirst').classList.remove('flex');
                                        document.getElementById('layoutFirst').classList.add('hidden');

                                        document.getElementById('layoutSecond').classList.remove('hidden');
                                        document.getElementById('layoutSecond').classList.add('flex');

                                        document.getElementById('card-uid2').textContent = uidConstant;
                                    }
                                } else {
                                    document.getElementById('layoutFirst').classList.remove('hidden');
                                    document.getElementById('layoutFirst').classList.add('flex');
                                }
                            });
                        </script>
                    </div>

                    <div id="accounts-list" class="space-y-3 max-h-96 overflow-y-auto custom-scrollbar pr-2">
                        @if ($user->isEmpty())
                            <div
                                class="text-center mt-10 h-full flex flex-col items-center text-gray-500 dark:text-gray-400">
                                <svg class="mx-auto mb-4 w-16 h-16 text-gray-300 dark:text-gray-600" fill="none"
                                    stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 9v2m0 4h.01M4.5 19.5h15a2 2 0 0 0 2-2v-11a2 2 0 0 0-2-2h-15a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2z" />
                                </svg>
                                <p class="text-lg font-medium">Semua akun sudah terhubung RFID 🎉</p>
                                <p class="text-sm">Tidak ada akun yang belum memiliki ID RFID.</p>
                            </div>
                        @else
                            @foreach ($user as $akunku)
                                <label class="block account-item">
                                    <div
                                        class="flex items-center border border-gray-200 dark:border-gray-700 rounded-lg p-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                        <input type="radio" name="account" value="1"
                                            class="mr-4 h-5 w-5 text-primary-600 focus:ring-primary-500" checked>
                                        <div class="flex-1">
                                            <div class="flex justify-between">
                                                <h3 class="font-medium account-name">{{ $akunku->warga_tels->name }}
                                                </h3>
                                                <span
                                                    class="text-sm text-gray-500 dark:text-gray-400 account-class">{{ $akunku->warga_tels->kelas }}</span>
                                            </div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400 account-nis">NIS:
                                                {{ $akunku->nis }}</p>
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="mt-auto pt-4 flex flex-row items-center">
                    <button id="btn-cancel"
                        class="w-full bg-red-600 hover:bg-red-700 mr-4 text-white py-3 px-4 rounded-md font-medium transition-colors">
                        Cancel
                    </button>
                    <button id="btn-continue2"
                        class="w-full bg-primary-600 hover:bg-primary-700 disabled:bg-gray-400 disabled:cursor-not-allowed cursor-pointer text-white py-3 px-4 rounded-md font-medium transition-colors"
                        disabled>
                        Next
                    </button>
                </div>
            </div>
        </main>
    </div>

    <script>
        let rfidBuffer = "";
        let timeout = null;
        let uidConstant;

        document.addEventListener("keydown", function(e) {
            if (timeout) clearTimeout(timeout);

            if (e.key === "Enter" && !document.getElementById('layoutFirst').classList.contains('hidden')) {
                uidConstant = rfidBuffer;
                axios.get("{{ route('checkStatusCard', ['id' => '__uid__']) }}".replace('__uid__', uidConstant))
                    .then(function(res) {
                        const data = res.data;
                        if (data.success) {
                            Swal.fire({
                                title: data.message,
                                icon: "error",
                                theme: localStorage.getItem('theme') === 'dark' ? 'dark' : 'light',
                                draggable: true
                            });
                            localStorage.removeItem("uid");
                        } else {
                            document.getElementById('card-uid').textContent = uidConstant;
                            document.getElementById('uid-container').classList.remove('hidden');

                            document.getElementById('nfc-icon').classList.add('hidden');
                            document.getElementById('success-icon').classList.remove('hidden');

                            localStorage.removeItem("uid");
                            localStorage.setItem("uid", uidConstant);
                            document.getElementById('status-text').textContent = 'Kartu berhasil terdeteksi...';

                            document.getElementById('rfid_input').value = uidConstant;
                            document.getElementById('btn-continue').disabled = false;
                        }
                    });

                rfidBuffer = "";
                return;
            }

            rfidBuffer += e.key;

            timeout = setTimeout(() => {
                rfidBuffer = "";
            }, 1000);
        });

        document.getElementById('btn-continue').addEventListener("click", function() {
            document.getElementById('layoutFirst').classList.remove('flex');
            document.getElementById('layoutFirst').classList.add('hidden');

            document.getElementById('layoutSecond').classList.remove('hidden');
            document.getElementById('layoutSecond').classList.add('flex');

            document.getElementById('card-uid2').textContent = uidConstant;
        });

        document.getElementById('btn-cancel').addEventListener("click", function() {
            document.getElementById('status-text').textContent = 'Tempelkan kartu pelajar Anda';
            document.getElementById('uid-container').classList.add('hidden');
            document.getElementById('nfc-icon').classList.remove('hidden');
            document.getElementById('success-icon').classList.add('hidden');
            document.getElementById('btn-continue').disabled = true;

            uidConstant = "";
            localStorage.removeItem("uid");

            document.getElementById('layoutFirst').classList.add('flex');
            document.getElementById('layoutFirst').classList.remove('hidden');

            document.getElementById('layoutSecond').classList.add('hidden');
            document.getElementById('layoutSecond').classList.remove('flex');
        });

        const accountItems = document.querySelectorAll('input[name="account"]');
        accountItems.forEach(item => {
            item.addEventListener('change', function() {
                document.querySelectorAll('input[name="account"]').forEach(radio => {
                    const parentDiv = radio.closest('div.flex');
                    parentDiv.classList.remove('bg-primary-50', 'dark:bg-primary-900/20',
                        'border-primary-300', 'dark:border-primary-700');
                });

                if (this.checked) {
                    const parentDiv = this.closest('div.flex');
                    parentDiv.classList.add('bg-primary-50', 'dark:bg-primary-900/20', 'border-primary-300',
                        'dark:border-primary-700');
                }
            });
        });

        const checkedAccount = document.querySelector('input[name="account"]:checked');
        if (checkedAccount) {
            checkedAccount.dispatchEvent(new Event('change'));
        }

        const accountRadios = document.querySelectorAll('input[name="account"]');
        const continueBtn = document.getElementById('btn-continue2');

        const checkSelected = () => {
            const selected = document.querySelector('input[name="account"]:checked');
            continueBtn.disabled = !selected;
        };

        checkSelected();

        accountRadios.forEach(radio => {
            radio.addEventListener('change', checkSelected);
        });

        document.getElementById('btn-continue2').addEventListener('click', function() {
            const selectedAccount = document.querySelector('input[name="account"]:checked');

            if (selectedAccount) {
                const accountNIS = selectedAccount
                    .closest('label')
                    .querySelector('.account-nis')
                    .textContent
                    .replace('NIS:', '')
                    .trim();

                Swal.fire({
                    title: "Are you sure?",
                    text: "Are you sure you want to link this card to that account?",
                    icon: "question",
                    theme: localStorage.getItem('theme') === 'dark' ? 'dark' : 'light',
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Yes, Continue",
                    cancelButtonText: "No, cancel"
                }).then((result) => {
                    if (result.isConfirmed) {
                        kirimData(uidConstant, accountNIS)
                    }
                });
            }
        });

        function kirimData(rfid_id, nis) {
            axios.post("{{ route('rfid.store') }}", {
                    uid: rfid_id,
                    nis: nis
                })
                .then(function(response) {
                    if (response.data?.success) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: "top-end",
                            theme: localStorage.getItem('theme') === 'dark' ? 'dark' : 'light',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.onmouseenter = Swal.stopTimer;
                                toast.onmouseleave = Swal.resumeTimer;
                            },
                            didClose: () => {
                                uidConstant = "";
                                localStorage.removeItem("uid");
                                window.location.reload()
                            }
                        });
                        Toast.fire({
                            icon: "success",
                            title: response.data?.message
                        });
                    } else {
                        Swal.fire({
                            title: response.data?.message,
                            icon: "error",
                            theme: localStorage.getItem('theme') === 'dark' ? 'dark' : 'light',
                            draggable: true
                        });
                    }
                })
                .catch(function(error) {
                    console.error('Error:', error.response?.data || error);
                });
        }

        function filterAccounts() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const accountsList = document.getElementById('accounts-list');
            const accountItems = document.querySelectorAll('.account-item');
            const noResults = document.getElementById('no-results');

            let visibleCount = 0;

            for (let i = 0; i < accountItems.length; i++) {
                const nameElement = accountItems[i].querySelector('.account-name');
                const classElement = accountItems[i].querySelector('.account-class');
                const nisElement = accountItems[i].querySelector('.account-nis');

                if (nameElement || classElement || nisElement) {
                    const nameValue = nameElement.textContent || nameElement.innerText;
                    const classValue = classElement.textContent || classElement.innerText;
                    const nisValue = nisElement.textContent || nisElement.innerText;

                    if (
                        nameValue.toUpperCase().indexOf(filter) > -1 ||
                        classValue.toUpperCase().indexOf(filter) > -1 ||
                        nisValue.toUpperCase().indexOf(filter) > -1
                    ) {
                        accountItems[i].style.display = '';
                        visibleCount++;
                    } else {
                        accountItems[i].style.display = 'none';
                    }
                }
            }

            if (visibleCount === 0) {
                noResults.classList.remove('hidden');
                accountsList.classList.add('hidden');
            } else {
                noResults.classList.add('hidden');
                accountsList.classList.remove('hidden');
            }
        }
    </script>
</body>

</html>
