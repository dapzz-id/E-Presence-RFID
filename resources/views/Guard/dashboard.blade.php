<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management</title>
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border-radius: 0.5rem;
            width: 425px;
            max-width: 90%;
        }

        .dark .modal-content {
            background-color: #1f2937;
            color: #f3f4f6;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }

        .dark .close:hover,
        .dark .close:focus {
            color: white;
        }
    </style>
</head>
<body class="antialiased h-full bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-gray-100 transition-colors duration-300">
    <div class="min-h-full">
        @php
            if (!function_exists('isUserOnline')) {
                function isUserOnline($user)
                {
                    if (!$user || !$user->last_seen) {
                        return false;
                    }

                    return $user->last_seen->gt(\Carbon\Carbon::now()->subMinutes(2));
                }
            }
            $totalOnline = $dataAkun->filter(function ($user) {
                return isUserOnline($user);
            })->count();
        @endphp
        @include('Cert.sa.head')

        <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
            <div class="md:flex md:items-center md:justify-between mb-8">
                <div class="min-w-0 flex-1">
                    <h2 class="text-2xl font-bold leading-7 sm:truncate sm:text-3xl">Admin Management</h2>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Manage administrator accounts
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 mb-8">
                <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 rounded-md bg-primary-100 dark:bg-primary-900 p-3">
                                <i class="fa-solid fa-users text-primary-600 dark:text-primary-300 h-6 w-6"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Admins</dt>
                                    <dd>
                                        <div class="text-3xl font-semibold text-gray-900 dark:text-white">{{ $dataAkun->count() }}</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 rounded-md bg-green-100 dark:bg-green-900 p-3">
                                <i class="fa-solid fa-user-check text-green-600 dark:text-green-300 h-6 w-6"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Active Admins</dt>
                                    <dd>
                                        <div class="text-3xl font-semibold text-gray-900 dark:text-white">{{ $totalOnline }}</div>
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <h3 class="text-lg font-medium">Admin Accounts</h3>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <div class="relative">
                                <i class="fa-solid fa-search absolute left-2.5 top-2.5 h-4 w-4 text-gray-500 dark:text-gray-400"></i>
                                <input 
                                    type="search" 
                                    placeholder="Search admins..." 
                                    class="flex h-10 w-full rounded-md border text-black border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 pl-8 md:w-[200px] lg:w-[300px]"
                                    id="searchInput"
                                    onkeyup="filterAdmins()"
                                >
                            </div>
                            <button 
                                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
                                onclick="window.location.reload()"
                            >
                                <i class="fa-solid fa-rotate-right mr-2 h-4 w-4"></i>
                                Refresh
                            </button>
                            <button 
                                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
                                onclick="openAddModal()"
                            >
                                <i class="fa-solid fa-plus mr-2 h-4 w-4"></i>
                                Add Admin
                            </button>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-[80px]">ID</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Username</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Last Login</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700" id="adminTableBody">
                            @foreach ($dataAkun as $dataku)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $dataku->username }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $dataku->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $dataku->email }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $dataku->last_seen ?? "-" }}</td>
                                    @if (isUserOnline($dataku))
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">Active</span>
                                        </td>
                                    @else
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">Inactive</span>
                                        </td>
                                    @endif
                                    
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <form action="{{ route('forgot-pw.ad') }}" method="post">
                                                @csrf
                                                <input type="hidden" name="email" value="{{ $dataku->email ?? "kadaviradityaa@gmail.com" }}">
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-8 w-8 p-0"
                                                    title="Reset Password Button">
                                                    <i class="fa-solid fa-rotate"></i>
                                                    <span class="sr-only">Reset Password</span>
                                                </button>
                                            </form>
                                            <button 
                                                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-8 w-8 p-0"
                                                onclick="openEditModal({{ $dataku->id }}, '{{ addslashes($dataku->username) }}', '{{ addslashes($dataku->name) }}', '{{ addslashes($dataku->email) }}')"
                                            >
                                                <i class="fa-solid fa-edit h-4 w-4"></i>
                                                <span class="sr-only">Edit</span>
                                            </button>
                                            <button 
                                                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-8 w-8 p-0"
                                                onclick="openDeleteModal({{ $dataku->id }}, '{{ addslashes($dataku->username) }}', '{{ addslashes($dataku->name) }}', '{{ addslashes($dataku->email) }}')"
                                            >
                                                <i class="fa-solid fa-trash-alt h-4 w-4"></i>
                                                <span class="sr-only">Delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="bg-white dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
                    <div class="flex-1 flex justify-between sm:hidden">
                        @if ($dataAkun->onFirstPage())
                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700">
                                Previous
                            </span>
                        @else
                            <a href="{{ $dataAkun->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                Previous
                            </a>
                        @endif

                        @if ($dataAkun->hasMorePages())
                            <a href="{{ $dataAkun->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                Next
                            </a>
                        @else
                            <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700">
                                Next
                            </span>
                        @endif
                    </div>

                    {{-- Desktop Pagination --}}
                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                Showing <span class="font-medium">{{ $dataAkun->firstItem() }}</span> 
                                to <span class="font-medium">{{ $dataAkun->lastItem() }}</span> 
                                of <span class="font-medium">{{ $dataAkun->total() }}</span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                @if ($dataAkun->onFirstPage())
                                    <span class="relative inline-flex items-center px-2 py-2 cursor-not-allowed rounded-l-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                @else
                                    <a href="{{ $dataAkun->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </a>
                                @endif
                
                                {{-- Nomor Halaman --}}
                                @foreach ($dataAkun->links()->elements[0] as $page => $url)
                                    @if ($page == $dataAkun->currentPage())
                                        <span class="z-10 bg-primary-50 dark:bg-primary-900 cursor-not-allowed border-primary-500 dark:border-primary-500 text-primary-600 dark:text-primary-200 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $url }}" class="bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach
                
                                {{-- Tombol Next --}}
                                @if ($dataAkun->hasMorePages())
                                    <a href="{{ $dataAkun->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </a>
                                @else
                                    <span class="relative inline-flex items-center px-2 py-2 cursor-not-allowed rounded-r-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </span>
                                @endif
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Admin Modal -->
    <div id="addAdminModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Add New Admin</h3>
                <span class="close" onclick="closeAddModal()">&times;</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Create a new administrator account. Click save when you're done.
            </p>
            <form action="admin/store" method="POST">
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 items-center gap-4">
                        <label for="username" class="text-right text-sm font-medium">
                            Username
                        </label>
                        <input 
                            id="username" 
                            name="username" 
                            class="col-span-3 flex h-10 rounded-md border text-black border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" 
                            required
                        />
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <label for="name" class="text-right text-sm font-medium">
                            Full Name
                        </label>
                        <input 
                            id="name" 
                            name="name" 
                            class="col-span-3 flex h-10 rounded-md border text-black border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" 
                            required
                        />
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <label for="email" class="text-right text-sm font-medium">
                            Email
                        </label>
                        <input 
                            id="email" 
                            name="email" 
                            type="email" 
                            class="col-span-3 flex h-10 rounded-md border text-black border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" 
                            required
                        />
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <label for="password" class="text-right text-sm font-medium">
                            Password
                        </label>
                        <input 
                            id="password" 
                            name="password" 
                            type="password" 
                            class="col-span-3 flex h-10 rounded-md border text-black border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" 
                            required
                        />
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <button 
                        type="submit" 
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
                    >
                        Save Admin
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Admin Modal -->
    <div id="editAdminModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Edit Admin</h3>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Update administrator account details. Click save when you're done.
            </p>
            <form action="admin/update" method="POST">
                <input type="hidden" id="edit_admin_id" name="id">
                <div class="grid gap-4 py-4">
                    <div class="grid grid-cols-4 items-center gap-4">
                        <label for="edit_username" class="text-right text-sm font-medium">
                            Username
                        </label>
                        <input 
                            id="edit_username" 
                            name="username" 
                            class="col-span-3 flex h-10 rounded-md border text-black border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" 
                        />
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <label for="edit_name" class="text-right text-sm font-medium">
                            Full Name
                        </label>
                        <input 
                            id="edit_name" 
                            name="name" 
                            class="col-span-3 flex h-10 rounded-md border text-black border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" 
                        />
                    </div>
                    <div class="grid grid-cols-4 items-center gap-4">
                        <label for="edit_email" class="text-right text-sm font-medium">
                            Email
                        </label>
                        <input 
                            id="edit_email" 
                            name="email" 
                            type="email" 
                            class="col-span-3 flex h-10 rounded-md border text-black border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" 
                        />
                    </div>
                </div>
                <div class="flex justify-end mt-4">
                    <button 
                        type="submit" 
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2"
                    >
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Admin Modal -->
    <div id="deleteAdminModal" class="modal">
        <div class="modal-content">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium">Delete Admin</h3>
                <span class="close" onclick="closeDeleteModal()">&times;</span>
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Are you sure you want to delete this administrator account? This action cannot be undone.
            </p>
            <div class="py-4">
                <p class="mb-4">You are about to delete the following admin account:</p>
                <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-md">
                    <p><strong>Username:</strong> <span id="delete_username"></span></p>
                    <p><strong>Name:</strong> <span id="delete_name"></span></p>
                    <p><strong>Email:</strong> <span id="delete_email"></span></p>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-4">
                <button 
                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2"
                    onclick="closeDeleteModal()"
                >
                    Cancel
                </button>
                <form action="admin/destroy" method="POST" class="inline">
                    <input type="hidden" id="delete_admin_id" name="id">
                    <button 
                        type="submit" 
                        class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-destructive text-destructive-foreground hover:bg-destructive/90 h-10 px-4 py-2"
                    >
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        // User dropdown toggle
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('userDropdown');
            const button = document.querySelector('button[onclick="document.getElementById(\'userDropdown\').classList.toggle(\'hidden\')"]');
            
            if (dropdown && !dropdown.classList.contains('hidden') && !dropdown.contains(event.target) && !button.contains(event.target)) {
                dropdown.classList.add('hidden');
            }
        });

        // Modal functions
        function openAddModal() {
            document.getElementById('addAdminModal').style.display = 'block';
        }
        
        function closeAddModal() {
            document.getElementById('addAdminModal').style.display = 'none';
        }
        
        function openEditModal(id, username, name, email) {
            document.getElementById('edit_admin_id').value = id;
            document.getElementById('edit_username').value = username;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_email').value = email;
            document.getElementById('editAdminModal').style.display = 'block';
        }
        
        function closeEditModal() {
            document.getElementById('editAdminModal').style.display = 'none';
        }
        
        function openDeleteModal(id, username, name, email) {
            document.getElementById('delete_admin_id').value = id;
            document.getElementById('delete_username').textContent = username;
            document.getElementById('delete_name').textContent = name;
            document.getElementById('delete_email').textContent = email;
            document.getElementById('deleteAdminModal').style.display = 'block';
        }
        
        function closeDeleteModal() {
            document.getElementById('deleteAdminModal').style.display = 'none';
        }

        // Search functionality
        function filterAdmins() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const tbody = document.getElementById('adminTableBody');
            const tr = tbody.getElementsByTagName('tr');
            
            for (let i = 0; i < tr.length; i++) {
                const tdUsername = tr[i].getElementsByTagName('td')[1];
                const tdName = tr[i].getElementsByTagName('td')[2];
                const tdEmail = tr[i].getElementsByTagName('td')[3];
                
                if (tdUsername || tdName || tdEmail) {
                    const usernameValue = tdUsername.textContent || tdUsername.innerText;
                    const nameValue = tdName.textContent || tdName.innerText;
                    const emailValue = tdEmail.textContent || tdEmail.innerText;
                    
                    if (
                        usernameValue.toUpperCase().indexOf(filter) > -1 || 
                        nameValue.toUpperCase().indexOf(filter) > -1 || 
                        emailValue.toUpperCase().indexOf(filter) > -1
                    ) {
                        tr[i].style.display = '';
                    } else {
                        tr[i].style.display = 'none';
                    }
                }
            }
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const addModal = document.getElementById('addAdminModal');
            const editModal = document.getElementById('editAdminModal');
            const deleteModal = document.getElementById('deleteAdminModal');
            
            if (event.target == addModal) {
                addModal.style.display = 'none';
            }
            
            if (event.target == editModal) {
                editModal.style.display = 'none';
            }
            
            if (event.target == deleteModal) {
                deleteModal.style.display = 'none';
            }
        }
    </script>
</body>
</html>