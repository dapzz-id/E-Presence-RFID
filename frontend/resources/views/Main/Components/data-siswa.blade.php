@extends('Main.manage-siswa')

@section('content')
    <div id="data-siswa" class="tab-content">
        <form method="GET" action="{{ route('siswa') }}" class="flex flex-col md:flex-row justify-between gap-4 mb-6 w-full">
            <div class="flex w-full md:w-auto mb-4 md:mb-0">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari siswa..."
                    class="w-full rounded-l-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50 py-2 px-4 border bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                <button type="submit"
                    class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-r-md whitespace-nowrap">
                    Cari
                </button>
            </div>

            <div class="flex flex-row max-md:flex-col">
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                    <select name="kelas" onchange="this.form.submit()"
                        class="w-full sm:w-auto rounded-md border-gray-300 max-sm:text-sm dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50 py-2 px-4 border bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="all" {{ request('kelas') == 'all' ? 'selected' : '' }}>Semua Kelas</option>
                        @foreach (['X RPL 1', 'X RPL 2', 'X RPL 3', 'X RPL 4', 'XI RPL 1', 'XI RPL 2', 'XI RPL 3', 'XI RPL 4', 'XII RPL 1', 'XII RPL 2', 'XII RPL 3', 'XII RPL 4', 'X DKV 1', 'X DKV 2', 'X DKV 3', 'XI DKV 1', 'XI DKV 2', 'XI DKV 3', 'XII DKV 1', 'XII DKV 2', 'XII DKV 3', 'X TKJ 1', 'X TKJ 2', 'X TKJ 3', 'XI TKJ 1', 'XI TKJ 2', 'XI TKJ 3', 'XII TKJ 1', 'XII TKJ 2', 'XII TKJ 3', 'X TRANSMISI', 'XI TRANSMISI', 'XII TRANSMISI'] as $kelasOption)
                            <option value="{{ $kelasOption }}" {{ request('kelas') == $kelasOption ? 'selected' : '' }}>
                                {{ $kelasOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-row max-w-max justify-center items-center max-md:mt-4 max-md:mx-auto">
                    <div title="Import Data Siswa" class="bg-blue-700 p-2 px-4 rounded-full max-md:rounded-lg flex items-center justify-center ms-8 me-2 cursor-pointer" onclick="openImportModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-sheet-icon lucide-sheet my-2 text-white"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><line x1="3" x2="21" y1="9" y2="9"/><line x1="3" x2="21" y1="15" y2="15"/><line x1="9" x2="9" y1="9" y2="21"/><line x1="15" x2="15" y1="9" y2="21"/></svg>
                    </div>
                    
                    <div title="Add Data Siswa" class="bg-green-700 p-2 px-4 rounded-full max-md:rounded-lg flex items-center justify-center me-2 cursor-pointer" onclick="window.location.href='{{ route('siswa.add') }}'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-plus-icon lucide-circle-plus my-2 text-white"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8v8"/></svg>
                    </div>

                    <div id="deleteSelectedBtn" title="Delete Data Siswa" class="bg-red-700 p-4 px-4 rounded-full max-md:rounded-lg flex items-center justify-center me-2 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2-icon lucide-trash-2 text-white"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                    </div>
                </div>
            </div>
        </form>

        <form id="deleteForm" action="{{ route('siswa.delete.multiple') }}" method="POST">
            @csrf
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-6">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    NIS</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Nama</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Kelas</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Alamat</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Foto</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($wargaTels as $wargaku)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        <input type="checkbox" name="selected_ids[]" value="{{ $wargaku->id }}" class="account-checkbox rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $wargaku->nis }}</td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $wargaku->name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $wargaku->kelas }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-900 dark:text-gray-100">{{ $wargaku->alamat }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        <img src="{{ Storage::disk('s3')->temporaryUrl('profile/' . $wargaku->foto_profile, now()->addMinutes(5)) }}" loading="lazy" alt="{{ $wargaku->name }}" class="h-auto w-14 aspect-[3/4]">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('siswa.edit', $wargaku->nis) }}" 
                                           title="Edit Button"
                                           class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3">
                                            <i class="bi bi-pencil text-orange-300"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectAllCheckbox = document.getElementById('selectAll');
                const accountCheckboxes = document.querySelectorAll('.account-checkbox');

                selectAllCheckbox.addEventListener('change', function() {
                    const isChecked = this.checked;
                    accountCheckboxes.forEach(checkbox => {
                        checkbox.checked = isChecked;
                    });
                });

                const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
                const deleteForm = document.getElementById('deleteForm');

                deleteSelectedBtn.addEventListener('click', function() {
                    const selectedCheckboxes = document.querySelectorAll('.account-checkbox:checked');
                    
                    if (selectedCheckboxes.length === 0) {
                        Swal.fire({
                            title: "Warning",
                            text: "Select at least one data to delete.",
                            icon: "warning"
                        });
                        return;
                    }
                    
                    // Get names of selected accounts for confirmation
                    const selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
                    
                    Swal.fire({
                        title: "Confirm Delete",
                        html: `Are you sure you want to delete the selected ${selectedCheckboxes.length} student data?`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Yes, do it!",
                        cancelButtonText: "Cancel",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            deleteForm.submit();
                        }
                    });
                });
            });
        </script>

        <!-- Pagination -->
        <div
            class="bg-white dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                {{-- Tombol Previous Mobile --}}
                @if ($wargaTels->onFirstPage())
                    <span
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700">
                        Previous
                    </span>
                @else
                    <a href="{{ $wargaTels->previousPageUrl() }}"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        Previous
                    </a>
                @endif

                {{-- Tombol Next Mobile --}}
                @if ($wargaTels->hasMorePages())
                    <a href="{{ $wargaTels->nextPageUrl() }}"
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        Next
                    </a>
                @else
                    <span
                        class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700">
                        Next
                    </span>
                @endif
            </div>

            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm text-gray-700 dark:text-gray-300">
                        Showing <span class="font-medium">{{ $wargaTels->firstItem() }}</span>
                        to <span class="font-medium">{{ $wargaTels->lastItem() }}</span>
                        of <span class="font-medium">{{ $wargaTels->total() }}</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        {{-- Tombol Previous --}}
                        @if ($wargaTels->onFirstPage())
                            <span
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        @else
                            <a href="{{ $wargaTels->previousPageUrl() }}"
                                class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>
                        @endif

                        {{-- Nomor Halaman dengan Ellipses --}}
                        @php
                            $currentPage = $wargaTels->currentPage();
                            $lastPage = $wargaTels->lastPage();
                            $start = max($currentPage - 2, 1);
                            $end = min($currentPage + 2, $lastPage);
                        @endphp

                        {{-- Halaman pertama --}}
                        @if ($start > 1)
                            <a href="{{ $wargaTels->url(1) }}"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium {{ $currentPage == 1 ? 'bg-primary-500 text-white' : 'bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                                1
                            </a>
                            @if ($start > 2)
                                <span
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">…</span>
                            @endif
                        @endif

                        {{-- Halaman di sekitar current --}}
                        @for ($page = $start; $page <= $end; $page++)
                            @if ($page == $currentPage)
                                <span
                                    class="z-10 bg-primary-50 dark:bg-primary-900 border-primary-500 dark:border-primary-500 text-primary-600 dark:text-primary-200 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $wargaTels->url($page) }}"
                                    class="bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    {{ $page }}
                                </a>
                            @endif
                        @endfor

                        {{-- Halaman terakhir --}}
                        @if ($end < $lastPage)
                            @if ($end < $lastPage - 1)
                                <span
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">…</span>
                            @endif
                            <a href="{{ $wargaTels->url($lastPage) }}"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium {{ $currentPage == $lastPage ? 'bg-primary-500 text-white' : 'bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                                {{ $lastPage }}
                            </a>
                        @endif

                        {{-- Tombol Next --}}
                        @if ($wargaTels->hasMorePages())
                            <a href="{{ $wargaTels->nextPageUrl() }}"
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>
                        @else
                            <span
                                class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                        clip-rule="evenodd" />
                                </svg>
                            </span>
                        @endif
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Excel Modal -->
    <div id="importExcelModal" class="fixed inset-0 z-50 hidden overflow-y-auto backdrop-blur-sm">
        <div class="fixed inset-0 bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-80"></div>

        <div class="relative h-full overflow-y-auto">
            <div class="flex items-center justify-center min-h-full px-4 pt-4 pb-20 text-center sm:block sm:p-0 relative">
                <!-- Modal panel -->
                <div
                    class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 rounded-xl shadow-xl sm:scale-100 scale-95 border border-gray-200 dark:border-gray-700 relative">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-xl font-semibold leading-6 text-gray-900 dark:text-gray-100">
                            Import Data Siswa
                        </h3>
                        <button type="button" onclick="closeImportModal()"
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
                        <div
                            class="p-4 mb-4 border border-blue-100 rounded-lg bg-blue-50 dark:bg-blue-900/30 dark:border-blue-900/50">
                            <div class="flex items-start">
                                <svg class="flex-shrink-0 w-5 h-5 mr-3 text-blue-600 dark:text-blue-400" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-sm text-blue-700 dark:text-blue-300">
                                    Unggah file Excel untuk mengimpor data siswa dan file ZIP untuk foto profil. Pastikan
                                    format sesuai dengan template.
                                </p>
                            </div>
                        </div>

                        <form id="importForm" action="{{ route('siswa.import') }}" method="POST"
                            enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            <div>
                                <label for="excel_file"
                                    class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    File Excel <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <div class="flex items-center justify-center w-full">
                                        <div id="excel-drop-area"
                                            class="flex flex-col items-center justify-center w-full h-28 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-8 h-8 mb-2 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                                    </path>
                                                </svg>
                                                <p class="mb-1 text-sm text-gray-500 dark:text-gray-400">
                                                    <span class="font-semibold">Excel</span> - Klik atau seret file
                                                </p>
                                                <p class="mb-1 text-sm text-gray-500 dark:text-gray-400">
                                                    Maksimal 10MiB
                                                </p>
                                            </div>
                                            <input type="file" name="excel_file" id="excel_file" accept=".xlsx,.xls"
                                                class="hidden" />
                                        </div>
                                    </div>
                                </div>
                                <div id="excelFileSelected"
                                    class="hidden mt-2 p-2 border border-green-100 rounded-lg bg-green-50 dark:bg-green-900/30 dark:border-green-900/50">
                                    <div class="flex items-center">
                                        <svg class="flex-shrink-0 w-4 h-4 mr-2 text-green-600 dark:text-green-400"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-xs text-green-700 dark:text-green-300">
                                            Excel: <span id="selectedExcelName" class="font-medium"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- ZIP File Upload -->
                            <div>
                                <label for="photo_zip"
                                    class="block mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                    File ZIP Foto (Opsional)
                                </label>
                                <div class="relative">
                                    <div class="flex items-center justify-center w-full">
                                        <div id="zip-drop-area"
                                            class="flex flex-col items-center justify-center w-full h-28 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-8 h-8 mb-2 text-gray-400" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                    </path>
                                                </svg>
                                                <p class="mb-1 text-sm text-gray-500 dark:text-gray-400">
                                                    <span class="font-semibold">ZIP Foto</span> - Klik atau seret file
                                                </p>
                                                <p class="mb-1 text-sm text-gray-500 dark:text-gray-400">
                                                    Maksimal 150MiB
                                                </p>
                                            </div>
                                            <input type="file" name="photo_zip" id="photo_zip" accept=".zip"
                                                class="hidden" />
                                        </div>
                                    </div>
                                </div>
                                <div id="zipFileSelected"
                                    class="hidden mt-2 p-2 border border-green-100 rounded-lg bg-green-50 dark:bg-green-900/30 dark:border-green-900/50">
                                    <div class="flex items-center">
                                        <svg class="flex-shrink-0 w-4 h-4 mr-2 text-green-600 dark:text-green-400"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-xs text-green-700 dark:text-green-300">
                                            ZIP: <span id="selectedZipName" class="font-medium"></span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div
                                class="p-3 border border-yellow-100 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 dark:border-yellow-900/30">
                                <p class="text-xs text-yellow-700 dark:text-yellow-300 font-medium">
                                    Catatan:
                                </p>
                                <ul class="mt-1 ml-5 list-disc text-xs text-yellow-700 dark:text-yellow-300">
                                    <li>Data harus sama/menggunakan template excel dari kami, untuk meminimalisir terjadinya
                                        error sistem</li>
                                    <li>Nama file foto dalam Excel harus sama dengan nama file dalam ZIP</li>
                                    <li>Data dengan NIS yang sudah ada akan diperbarui jika ada data didalam excel (Nama,
                                        Kelas, Alamat, Foto)</li>
                                    <li>Pastikan file ZIP foto tidak dikunci dan tidak ada folder didalamnya</li>
                                    <li>Jika foto telah diupload tetapi ada nama file (foto) baru yang sama, maka akan
                                        menimpa/mengganti file foto yang lama</li>
                                </ul>
                            </div>

                            <div class="flex flex-col sm:flex-row justify-between gap-3 pt-2">
                                <a href="{{ route('siswa.template.download') }}"
                                    class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-teal-500 rounded-lg hover:bg-teal-600 focus:ring-4 focus:ring-teal-300 dark:focus:ring-teal-800 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Unduh Template
                                </a>
                                <div class="flex gap-2 sm:gap-3">
                                    <button type="button" onclick="closeImportModal()"
                                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700 transition-colors">
                                        Batal
                                    </button>
                                    <button type="submit" id="submitImport"
                                        class="inline-flex items-center w-full justify-center px-4 py-2.5 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:ring-4 focus:ring-primary-300 dark:focus:ring-primary-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                        disabled>
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                        </svg>
                                        Unggah
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const importModal = document.getElementById('importExcelModal');
        const excelInput = document.getElementById('excel_file');
        const zipInput = document.getElementById('photo_zip');
        const excelDropArea = document.getElementById('excel-drop-area');
        const zipDropArea = document.getElementById('zip-drop-area');
        const excelFileSelected = document.getElementById('excelFileSelected');
        const zipFileSelected = document.getElementById('zipFileSelected');
        const selectedExcelName = document.getElementById('selectedExcelName');
        const selectedZipName = document.getElementById('selectedZipName');
        const submitButton = document.getElementById('submitImport');

        function openImportModal() {
            importModal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }

        function closeImportModal() {
            importModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');

            document.getElementById('importForm').reset();
            excelFileSelected.classList.add('hidden');
            zipFileSelected.classList.add('hidden');
            submitButton.disabled = true;
        }

        excelInput.addEventListener('change', function() {
            handleExcelFile(this.files);
        });

        zipInput.addEventListener('change', function() {
            handleZipFile(this.files);
        });

        function handleExcelFile(files) {
            if (files && files[0]) {
                const file = files[0];
                const maxSize = 10 * 1024 * 1024;

                if (file.size > maxSize) {
                    alert('Ukuran file tidak boleh lebih dari 10 MiB');
                    zipInput.value = '';
                    zipFileSelected.classList.add('hidden');
                    return;
                }

                if (file.type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ||
                    file.type === 'application/vnd.ms-excel') {
                    selectedExcelName.textContent = file.name;
                    excelFileSelected.classList.remove('hidden');
                    submitButton.disabled = false;
                } else {
                    alert('Hanya file Excel (.xlsx, .xls) yang diperbolehkan');
                    excelInput.value = '';
                    excelFileSelected.classList.add('hidden');
                    submitButton.disabled = true;
                }
            } else {
                excelFileSelected.classList.add('hidden');
                submitButton.disabled = true;
            }
        }

        function handleZipFile(files) {
            if (files && files[0]) {
                const file = files[0];
                const maxSize = 150 * 1024 * 1024;

                if (file.size > maxSize) {
                    alert('Ukuran file tidak boleh lebih dari 150 MiB');
                    zipInput.value = '';
                    zipFileSelected.classList.add('hidden');
                    return;
                }

                if (file.type === 'application/zip' ||
                    file.type === 'application/x-zip-compressed') {
                    selectedZipName.textContent = file.name;
                    zipFileSelected.classList.remove('hidden');
                } else {
                    alert('Hanya file ZIP yang diperbolehkan');
                    zipInput.value = '';
                    zipFileSelected.classList.add('hidden');
                }
            } else {
                zipFileSelected.classList.add('hidden');
            }
        }

        excelDropArea.addEventListener('click', function() {
            excelInput.click();
        });

        zipDropArea.addEventListener('click', function() {
            zipInput.click();
        });

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            excelDropArea.addEventListener(eventName, preventDefaults, false);
            zipDropArea.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            excelDropArea.addEventListener(eventName, function() {
                highlightDropArea(excelDropArea);
            }, false);

            zipDropArea.addEventListener(eventName, function() {
                highlightDropArea(zipDropArea);
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            excelDropArea.addEventListener(eventName, function() {
                unhighlightDropArea(excelDropArea);
            }, false);

            zipDropArea.addEventListener(eventName, function() {
                unhighlightDropArea(zipDropArea);
            }, false);
        });

        function highlightDropArea(dropArea) {
            dropArea.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
        }

        function unhighlightDropArea(dropArea) {
            dropArea.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
        }

        excelDropArea.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files && files.length > 0) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(files[0]);
                excelInput.files = dataTransfer.files;

                handleExcelFile(files);
            }
        }, false);

        zipDropArea.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files && files.length > 0) {
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(files[0]);
                zipInput.files = dataTransfer.files;

                handleZipFile(files);
            }
        }, false);

        window.addEventListener('click', function(event) {
            if (event.target === importModal) {
                closeImportModal();
            }
        });

        document.getElementById('importForm').addEventListener('submit', function(event) {
            if (!excelInput.files || !excelInput.files[0]) {
                event.preventDefault();
                alert('Silakan pilih file Excel terlebih dahulu');
            }
        });
    </script>
@endsection
