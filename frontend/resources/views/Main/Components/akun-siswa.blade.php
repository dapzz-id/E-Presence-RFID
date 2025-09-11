@extends('Main.manage-siswa')

@section('content')
    <div id="akun-siswa" class="tab-content">
        <form method="GET" action="{{ route('akun.siswa') }}"
            class="flex flex-col md:flex-row justify-between gap-4 mb-6 w-full">
            <div class="flex w-full md:w-auto mb-4 md:mb-0">
                <input type="text" name="search-akun" value="{{ request('search-akun') }}" placeholder="Cari akun siswa..."
                    class="w-full rounded-l-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50 py-2 px-4 border bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                <button type="submit"
                    class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-r-md whitespace-nowrap">
                    Cari
                </button>
            </div>

            <div class="flex flex-row max-md:flex-col">
                <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                    <select name="kelas-akun" onchange="this.form.submit()"
                        class="w-full sm:w-auto rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50 py-2 px-4 border bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                        <option value="all" {{ request('kelas-akun') == 'all' ? 'selected' : '' }}>Semua Kelas</option>
                        @foreach (['X RPL 1', 'X RPL 2', 'X RPL 3', 'X RPL 4', 'XI RPL 1', 'XI RPL 2', 'XI RPL 3', 'XI RPL 4', 'XII RPL 1', 'XII RPL 2', 'XII RPL 3', 'XII RPL 4', 'X DKV 1', 'X DKV 2', 'X DKV 3', 'XI DKV 1', 'XI DKV 2', 'XI DKV 3', 'XII DKV 1', 'XII DKV 2', 'XII DKV 3', 'X TKJ 1', 'X TKJ 2', 'X TKJ 3', 'XI TKJ 1', 'XI TKJ 2', 'XI TKJ 3', 'XII TKJ 1', 'XII TKJ 2', 'XII TKJ 3', 'X TRANSMISI', 'XI TRANSMISI', 'XII TRANSMISI'] as $kelasOption2)
                            <option value="{{ $kelasOption2 }}" {{ request('kelas-akun') == $kelasOption2 ? 'selected' : '' }}>
                                {{ $kelasOption2 }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex flex-row max-w-max justify-center items-center max-md:mt-4 max-md:mx-auto">
                    <div title="Chart Akun Siswa" class="bg-blue-700 p-4 px-4 rounded-full max-md:rounded-lg flex items-center justify-center ms-8 me-2 cursor-pointer" onclick="window.location.href='{{ route('attendance.index') }}'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chart-no-axes-combined-icon lucide-chart-no-axes-combined text-white"><path d="M12 16v5"/><path d="M16 14v7"/><path d="M20 10v11"/><path d="m22 3-8.646 8.646a.5.5 0 0 1-.708 0L9.354 8.354a.5.5 0 0 0-.707 0L2 15"/><path d="M4 18v3"/><path d="M8 14v7"/></svg>
                    </div>

                    <div title="Add Akun Siswa" class="bg-green-700 p-2 px-4 rounded-full max-md:rounded-lg flex items-center justify-center me-2 cursor-pointer" onclick="window.location.href='{{ route('akun.siswa.create') }}'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-plus-icon lucide-circle-plus my-2 text-white"><circle cx="12" cy="12" r="10"/><path d="M8 12h8"/><path d="M12 8v8"/></svg>
                    </div>

                    <div id="deleteSelectedBtn" title="Delete Akun Siswa" class="bg-red-700 p-4 px-4 rounded-full max-md:rounded-lg flex items-center justify-center me-2 cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-trash2-icon lucide-trash-2 text-white"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" x2="10" y1="11" y2="17"/><line x1="14" x2="14" y1="11" y2="17"/></svg>
                    </div>
                </div>
            </div>
        </form>
        
        <form id="deleteForm" action="{{ route('akun.delete.multiple') }}" method="POST">
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
                                    Username</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Email</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Nama</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Kelas</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    RFID</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach ($akunSiswa as $akunku)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        <input type="checkbox" name="selected_ids[]" value="{{ $akunku->id }}" class="account-checkbox rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $akunku->nis }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $akunku->username }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $akunku->email ?? '-' }}
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $akunku->warga_tels->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $akunku->warga_tels->kelas }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        @if ($akunku->status_ban == 'active')
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 items-center">
                                                Aktif
                                            </span>
                                        @else
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 items-center">
                                                Banned
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        @if ($akunku->rfid_id == '' || $akunku->rfid_id == null)
                                            <span
                                                class="p-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 items-center">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @else
                                            <span
                                                class="p-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 items-center">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="max-w-full flex flex-row justify-start items-center">
                                            @if ($akunku->status_ban == 'active')
                                                <div data-idAkun="{{ $akunku->id }}" data-namaAkun="{{ $akunku->warga_tels->name }}" title="Non-Aktifkan Akun Siswa" class="btnBan">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ban-icon lucide-ban text-red-700 cursor-pointer"><circle cx="12" cy="12" r="10"/><path d="m4.9 4.9 14.2 14.2"/></svg>
                                                </div>
                                            @else
                                                <div data-idAkun="{{ $akunku->id }}" data-namaAkun="{{ $akunku->warga_tels->name }}" title="Aktifkan Akun Siswa" class="btnActive">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big-icon lucide-circle-check-big text-green-600 cursor-pointer"><path d="M21.801 10A10 10 0 1 1 17 3.335"/><path d="m9 11 3 3L22 4"/></svg>
                                                </div>
                                            @endif
                                            <div id="btnEdit" title="Edit Akun Siswa" onclick="window.location.href='{{ route('akun.siswa.edit', $akunku->id) }}'">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-square-pen-icon lucide-square-pen text-orange-600 mx-2 cursor-pointer"><path d="M12 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.375 2.625a1 1 0 0 1 3 3l-9.013 9.014a2 2 0 0 1-.853.505l-2.873.84a.5.5 0 0 1-.62-.62l.84-2.873a2 2 0 0 1 .506-.852z"/></svg>
                                            </div>
                                            <div data-emailAkun="{{ $akunku->email }}" data-namaAkun="{{ $akunku->warga_tels->name }}" title="Reset Password Akun Siswa" class="btnResetPw">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-rotate-ccw-key-icon lucide-rotate-ccw-key text-blue-600 cursor-pointer"><path d="m14.5 9.5 1 1"/><path d="m15.5 8.5-4 4"/><path d="M3 12a9 9 0 1 0 9-9 9.74 9.74 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><circle cx="10" cy="14" r="2"/></svg>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>

        <div
            class="bg-white dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                {{-- Tombol Previous Mobile --}}
                @if ($akunSiswa->onFirstPage())
                    <span
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700">
                        Previous
                    </span>
                @else
                    <a href="{{ $akunSiswa->previousPageUrl() }}"
                        class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        Previous
                    </a>
                @endif

                {{-- Tombol Next Mobile --}}
                @if ($akunSiswa->hasMorePages())
                    <a href="{{ $akunSiswa->nextPageUrl() }}"
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
                        Showing <span class="font-medium">{{ $akunSiswa->firstItem() }}</span>
                        to <span class="font-medium">{{ $akunSiswa->lastItem() }}</span>
                        of <span class="font-medium">{{ $akunSiswa->total() }}</span> results
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        {{-- Tombol Previous --}}
                        @if ($akunSiswa->onFirstPage())
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
                            <a href="{{ $akunSiswa->previousPageUrl() }}"
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
                            $currentPage = $akunSiswa->currentPage();
                            $lastPage = $akunSiswa->lastPage();
                            $start = max($currentPage - 2, 1);
                            $end = min($currentPage + 2, $lastPage);
                        @endphp

                        {{-- Halaman pertama --}}
                        @if ($start > 1)
                            <a href="{{ $akunSiswa->url(1) }}"
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
                                <a href="{{ $akunSiswa->url($page) }}"
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
                            <a href="{{ $akunSiswa->url($lastPage) }}"
                                class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium {{ $currentPage == $lastPage ? 'bg-primary-500 text-white' : 'bg-white dark:bg-gray-700 text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600' }}">
                                {{ $lastPage }}
                            </a>
                        @endif

                        {{-- Tombol Next --}}
                        @if ($akunSiswa->hasMorePages())
                            <a href="{{ $akunSiswa->nextPageUrl() }}"
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all checkbox functionality
            const selectAllCheckbox = document.getElementById('selectAll');
            const accountCheckboxes = document.querySelectorAll('.account-checkbox');
            
            selectAllCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                accountCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
            });
            
            // Delete selected accounts
            const deleteSelectedBtn = document.getElementById('deleteSelectedBtn');
            const deleteForm = document.getElementById('deleteForm');
            
            deleteSelectedBtn.addEventListener('click', function() {
                const selectedCheckboxes = document.querySelectorAll('.account-checkbox:checked');
                
                if (selectedCheckboxes.length === 0) {
                    Swal.fire({
                        title: "Warning",
                        text: "Select at least one account to delete.",
                        icon: "warning"
                    });
                    return;
                }
                
                // Get names of selected accounts for confirmation
                const selectedIds = Array.from(selectedCheckboxes).map(checkbox => checkbox.value);
                
                Swal.fire({
                    title: "Confirm Delete",
                    html: `Are you sure you want to delete the selected ${selectedCheckboxes.length} student accounts?`,
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

            document.querySelectorAll('.btnBan').forEach(function(button) {
                button.addEventListener('click', function() {
                    const idAkun = this.getAttribute('data-idAkun');
                    const namaAkun = this.getAttribute('data-namaAkun')

                    Swal.fire({
                        title: "Are you sure?",
                        html: "Are you sure you want to banned: <br><b>" + namaAkun + "</b>?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, do it!",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.patch('/akun-siswa/ban-akun/' + idAkun)
                            .then(response => {
                                if(response.data?.status == 'success'){
                                    let timerInterval;                                                    
                                    Swal.fire({
                                        title: "Banned!",
                                        text: "This account has been successfully banned",
                                        icon: "success",
                                        timer: 2000,
                                        timerProgressBar: true,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                        didOpen: () => {
                                            Swal.showLoading();
                                            const timer = Swal.getPopup().querySelector("b");
                                            timerInterval = setInterval(() => {
                                            timer.textContent = `${Swal.getTimerLeft()}`;
                                            }, 100);
                                        },
                                        willClose: () => {
                                            clearInterval(timerInterval);
                                        }
                                    }).then((result) => {
                                        /* Read more about handling dismissals below */
                                        if (result.dismiss === Swal.DismissReason.timer) {
                                            window.location.reload();
                                        }
                                    });
                                }else{
                                    Swal.fire({
                                        title: "Banned!",
                                        text: "This account failed to banned",
                                        icon: "error"
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire('Gagal', error.message, 'error');
                            });
                        }
                    });
                });
            });

            document.querySelectorAll('.btnActive').forEach(function(button) {
                button.addEventListener('click', function() {
                    const idAkun = this.getAttribute('data-idAkun');
                    const namaAkun = this.getAttribute('data-namaAkun')

                    Swal.fire({
                        title: "Are you sure?",
                        html: "Are you sure you want to unbanned: <br><b>" + namaAkun + "</b>?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, do it!",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.patch(`/akun-siswa/unban-akun/${idAkun}`)
                            .then(response => {
                                if(response.data?.status == 'success'){
                                    let timerInterval;                                                    
                                    Swal.fire({
                                        title: "Unbanned!",
                                        text: "This account has been successfully unbanned",
                                        icon: "success",
                                        timer: 2000,
                                        timerProgressBar: true,
                                        allowOutsideClick: false,
                                        allowEscapeKey: false,
                                        didOpen: () => {
                                            Swal.showLoading();
                                            const timer = Swal.getPopup().querySelector("b");
                                            timerInterval = setInterval(() => {
                                            timer.textContent = `${Swal.getTimerLeft()}`;
                                            }, 100);
                                        },
                                        willClose: () => {
                                            clearInterval(timerInterval);
                                        }
                                    }).then((result) => {
                                        if (result.dismiss === Swal.DismissReason.timer) {
                                            window.location.reload();
                                        }
                                    });
                                }else{
                                    Swal.fire({
                                        title: "Unbanned!",
                                        text: "This account failed to unbanned",
                                        icon: "error"
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire('Gagal', 'Terjadi kesalahan', 'error');
                            });
                        }
                    });
                });
            });
            
            document.querySelectorAll('.btnResetPw').forEach(function(button) {
                button.addEventListener('click', function() {
                    const emailAkun = this.getAttribute('data-emailAkun');
                    const namaAkun = this.getAttribute('data-namaAkun');
                    
                    Swal.fire({
                        title: "Reset Password",
                        html: `Are you sure you want to reset the password for the account:<br><b>${namaAkun}</b>?`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Yes, do it!",
                        cancelButtonText: "Cancel",
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Send reset password request
                            axios.post('/api/forgot-password', {
                                email: emailAkun
                            })
                            .then(response => {
                                if(response.data?.status == 'success'){
                                    Swal.fire({
                                        title: "Successfully!",
                                        text: "Reset Password link has been sent to user's email",
                                        icon: "success"
                                    });
                                } else {
                                    Swal.fire({
                                        title: "Failed!",
                                        text: "Failed to reset password",
                                        icon: "error"
                                    });
                                }
                            })
                            .catch(error => {
                                Swal.fire('Gagal', 'Terjadi kesalahan', 'error');
                            });
                        }
                    });
                });
            });
        });
    </script>
@endsection
