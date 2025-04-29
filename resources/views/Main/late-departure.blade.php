@extends('layouts.formKeterlambatan')

@section('contentKeterlambatan')
<div class="min-h-screen bg-gray-50 flex flex-col justify-center py-4 px-2 sm:px-6 lg:px-8">
    <div class="mx-auto w-full max-w-xs sm:max-w-sm md:max-w-md">
        <h2 class="text-center text-2xl sm:text-3xl font-bold text-gray-800">
            Form Keterlambatan Pulang
        </h2>
    </div>

    <div class="mt-6 mx-auto w-full max-w-xs sm:max-w-sm md:max-w-md">
        <div class="bg-white py-6 px-4 shadow rounded-lg sm:px-6">
            <form class="space-y-4" action="{{ route('late-departure.store') }}" method="POST">
                @csrf
                
                <input type="hidden" name="token" value="{{ $lateEntry->token }}" readonly autocomplete="off">

                <div>
                    <label for="current_time" class="block text-xs sm:text-sm font-medium text-gray-700">Jam Pulang</label>
                    <div class="mt-1">
                        <input id="current_time" name="current_time" type="text" value="{{ $lateEntry->time }}" readonly 
                            class="bg-gray-100 w-full px-2 py-1.5 text-xs sm:text-sm border border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>

                <div>
                    <label for="nis" class="block text-xs sm:text-sm font-medium text-gray-700">NIS</label>
                    <div class="mt-1">
                        <input id="nis" name="nis" type="text" value="{{ $lateEntry->nis }}" readonly 
                            class="bg-gray-100 w-full px-2 py-1.5 text-xs sm:text-sm border border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>

                <div>
                    <label for="nis" class="block text-xs sm:text-sm font-medium text-gray-700">Nama Lengkap</label>
                    <div class="mt-1">
                        <input id="nis" name="nis" type="text" value="{{ $user->warga_tels->name }}" readonly 
                            class="bg-gray-100 w-full px-2 py-1.5 text-xs sm:text-sm border border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>

                <div>
                    <label for="alasan" class="block text-xs sm:text-sm font-medium text-gray-700">Alasan Telat Pulang <span class="font-bold text-red-600">*</span></label>
                    <div class="mt-1">
                        <textarea id="alasan" name="alasan" rows="2" 
                            class="w-full px-2 py-1.5 text-xs sm:text-sm border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Masukkan alasan keterlambatan"></textarea>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        class="w-full py-1.5 px-4 text-xs sm:text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection