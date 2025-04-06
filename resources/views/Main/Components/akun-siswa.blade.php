@extends('Main.manage-siswa')

@section('content')
    <div id="akun-siswa" class="tab-content">
        <form method="GET" action="{{ route('akun.siswa') }}" class="flex flex-col md:flex-row justify-between gap-4 mb-6 w-full">
            <!-- Search section -->
            <div class="flex w-full md:w-auto mb-4 md:mb-0">
                <input type="text" name="search-akun" value="{{ request('search-akun') }}" placeholder="Cari siswa..." class="w-full rounded-l-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50 py-2 px-4 border bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-r-md whitespace-nowrap">
                    Cari
                </button>
            </div>

            <!-- Filter and Add button section -->
            <div class="flex flex-col sm:flex-row gap-2 w-full md:w-auto">
                <select name="kelas-akun" onchange="this.form.submit()" class="w-full sm:w-auto rounded-md border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50 py-2 px-4 border bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100">
                    <option value="all" {{ request('kelas-akun') == 'all' ? 'selected' : '' }}>Semua Kelas</option>
                    @foreach (['X RPL 1', 'X RPL 2', 'X RPL 3', 'X RPL 4', 'XI RPL 1', 'XI RPL 2', 'XI RPL 3', 'XI RPL 4', 'XII RPL 1', 'XII RPL 2', 'XII RPL 3', 'XII RPL 4', 'X DKV 1', 'X DKV 2', 'X DKV 3', 'XI DKV 1', 'XI DKV 2', 'XI DKV 3', 'XII DKV 1', 'XII DKV 2', 'XII DKV 3', 'X TKJ 1', 'X TKJ 2', 'X TKJ 3', 'XI TKJ 1', 'XI TKJ 2', 'XI TKJ 3', 'XII TKJ 1', 'XII TKJ 2', 'XII TKJ 3', 'X TRANSMISI', 'XI TRANSMISI', 'XII TRANSMISI'] as $kelasOption2)
                        <option value="{{ $kelasOption2 }}" {{ request('kelas-akun') == $kelasOption2 ? 'selected' : '' }}>{{ $kelasOption2 }}</option>
                    @endforeach
                </select>
            </div>
        </form>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-6">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">NIS</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Username</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Kelas</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">RFID</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <!-- Dummy Data -->
                        @foreach ($akunSiswa as $akunku)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $akunku->nis }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $akunku->username }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">{{ $akunku->warga_tels->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $akunku->warga_tels->kelas }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                    @if ($akunku->rfid_id == '' || $akunku->rfid_id == null)
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200 items-center">
                                            <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                            Belum Terdaftar
                                        </span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 items-center">
                                            <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            {{ $akunku->rfid_id }}
                                        </span>
                                    @endif
                                </td>
                                {{-- <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300 mr-3">Edit</button>
                                    <button class="text-red-600 dark:text-red-400 hover:text-red-900 dark:hover:text-red-300">Reset Password</button>
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        <div class="bg-white dark:bg-gray-800 px-4 py-3 flex items-center justify-between border-t border-gray-200 dark:border-gray-700 sm:px-6">
            <div class="flex-1 flex justify-between sm:hidden">
                @if ($akunSiswa->onFirstPage())
                    <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700">
                        Previous
                    </span>
                @else
                    <a href="{{ $akunSiswa->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        Previous
                    </a>
                @endif
        
                @if ($akunSiswa->hasMorePages())
                    <a href="{{ $akunSiswa->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        Next
                    </a>
                @else
                    <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700">
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
                            <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                        @else
                            <a href="{{ $akunSiswa->previousPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        @endif
        
                        {{-- Nomor Halaman --}}
                        @foreach ($akunSiswa->links()->elements[0] as $page => $url)
                            @if ($page == $akunSiswa->currentPage())
                                <span class="z-10 bg-primary-50 dark:bg-primary-900 border-primary-500 dark:border-primary-500 text-primary-600 dark:text-primary-200 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
        
                        {{-- Tombol Next --}}
                        @if ($akunSiswa->hasMorePages())
                            <a href="{{ $akunSiswa->nextPageUrl() }}" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </a>
                        @else
                            <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700 text-sm font-medium text-gray-500 dark:text-gray-400">
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
@endsection