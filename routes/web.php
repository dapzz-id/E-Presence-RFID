<?php

use App\Exports\PresenceExport;
use App\Exports\PresencePerMonthExport;
use App\Http\Controllers\AuthAdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\SiswaController;
use App\Models\AdminAccount;
use App\Models\Presence;
use App\Models\User;
use App\Models\WargaTels;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\Facades\Image;

Route::get('/', function () {
    if(Auth::guard('admin')->check()){
        return redirect()->route('dashboard');
    }else if (Auth::guard('superadmin')->check()){
        return redirect()->route('dashboard.sa');
    }else{
        return view('Auth.Login');
    }
})->name('login');


Route::post('/', function (Request $request) {
    $credentials = $request->validate([
        'username' => 'required|string|min:6',
        'password' => 'required|string|min:6'
    ],[
        'username.required' => 'Kolom username wajib diisi',
        'username.min' => 'Panjang username minimal 6 karakter',
        'password.required' => 'Kolom password wajib diisi',
        'password.min' => 'Panjang password minimal 6 karakter',
    ]);

    if (Auth::guard('admin')->attempt($credentials)) {
        return redirect()->route('dashboard')->with('success', 'Login sebagai Admin berhasil!');
    }
    
    if (Auth::guard('superadmin')->attempt($credentials)) {
        return redirect()->route('dashboard.sa')->with('success', 'Login sebagai Superadmin berhasil!');
    }
    

    return back()->withErrors(['login' => 'Username atau password salah'])->withInput();
})->name('login.submit');

Route::post('/forgot-password/admin-sa', [AuthAdminController::class, 'forgotPassword'])->name('forgot-pw.ad');

Route::prefix('superadmin')->middleware('auth:superadmin')->group(function (){
    Route::get('/dashboard', function (Request $request) {        
        $total = (string)AdminAccount::count();
        
        $dataAkun = AdminAccount::latest()
            ->paginate(10);

        return view('Guard.dashboard', compact('total', 'dataAkun'));
    })->name('dashboard.sa');
});


Route::prefix('admin')->middleware('auth:admin')->group(function (){    
    Route::get('/dashboard', function (Request $request) {
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $user->update(['last_seen' => Carbon::now()]);
        }

        $filter = $request->query('filter', 'Hari ini');
        
        $dateFrom = null;
        $dateTo = null;
        
        switch ($filter) {
            case 'Hari ini':
                $dateFrom = Carbon::today();
                $dateTo = Carbon::today()->endOfDay();
                break;
            case 'Kemarin':
                $dateFrom = Carbon::yesterday();
                $dateTo = Carbon::yesterday()->endOfDay();
                break;
            case 'Minggu Lalu':                
                $dateFrom = Carbon::now()->subWeek()->startOfWeek();
                $dateTo = Carbon::now()->subWeek()->endOfWeek();
                break;
            case 'Minggu Ini':                
                $dateFrom = Carbon::now()->startOfWeek();
                $dateTo = Carbon::now()->endOfWeek();
                break;
            case 'Bulan Lalu':
                $dateFrom = Carbon::now()->subMonth()->startOfMonth();
                $dateTo = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'Bulan Ini':
                $dateFrom = Carbon::now()->startOfMonth();
                $dateTo = Carbon::now()->endOfMonth();
                break;
            case 'Tahun Lalu':
                $dateFrom = Carbon::now()->subYear()->startOfYear();
                $dateTo = Carbon::now()->subYear()->endOfYear();
                break;
            case 'Tahun Ini':
                $dateFrom = Carbon::now()->startOfYear();
                $dateTo = Carbon::now()->endOfDay();
                break;
            default:
                $dateFrom = Carbon::today();
                $dateTo = Carbon::today()->endOfDay();
                break;
        }
        
        $total = (string)User::count();
        $presencesQuery = Presence::whereBetween('time_masuk', [$dateFrom, $dateTo]);
        
        $presences = $presencesQuery->orderBy('time_masuk', 'desc')
            ->with('warga_tels')
            ->get()
            ->map(function ($item) {
                return [
                    'Status' => $item->status,
                ];
            })
            ->values()
            ->map(function ($item, $index) {
                $item['id'] = $index + 1;
                return $item;
            });
        
        $dataPresensi = Presence::whereBetween('time_masuk', [$dateFrom, $dateTo])
            ->with('warga_tels')
            ->paginate(10);
        
        $totalHariIni = (string)$presences->count();
        $totalTidakHadir = (string)($presences->filter(fn ($p) => $p['Status'] === 'Izin')->count() + 
                                   $presences->filter(fn ($p) => $p['Status'] === 'Sakit')->count());
        
        return view('Main.dashboard', compact('total', 'totalHariIni', 'totalTidakHadir', 'dataPresensi', 'filter'));
    })->name('dashboard');

    Route::get('/akun-siswa', function (Request $request) {
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $user->update(['last_seen' => Carbon::now()]);
        }

        $searchAkun = $request->query('search-akun');
        $kelasAkun = $request->query('kelas-akun');
    
        $query2 = User::with('warga_tels');

        if (!empty($searchAkun)) {
            $query2->where(function ($query) use ($searchAkun) {
                $query->where('nis', 'like', "%$searchAkun%")
                      ->orWhere('username', 'like', "%$searchAkun%");
            })->orWhereHas('warga_tels', function ($query) use ($searchAkun) {
                $query->where('name', 'like', "%$searchAkun%");
            });
        }

        if (!empty($kelasAkun) && $kelasAkun !== 'all') {
            $query2->whereHas('warga_tels', function ($query) use ($kelasAkun) {
                $query->where('kelas', $kelasAkun);
            });
        }
    
        $akunSiswa = $query2->paginate(10, ['*'], 'akun-siswa');
    
        return view('Main.Components.akun-siswa', compact('akunSiswa'));
    })->name('akun.siswa');

    Route::get('/siswa/template/download', [SiswaController::class, 'downloadTemplate'])->name('siswa.template.download');
    Route::post('/siswa/import', [SiswaController::class, 'importExcel'])->name('siswa.import');

    Route::get('/siswa', [SiswaController::class, 'index'])->name('siswa');
    Route::get('/siswa/add', [SiswaController::class, 'create'])->name('siswa.add');
    Route::post('/siswa/add', [SiswaController::class, 'store'])->name('siswa.store');
    Route::get('/siswa/edit/{nis}', [SiswaController::class, 'edit'])->name('siswa.edit');
    Route::put('/siswa/update', [SiswaController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/delete/{nis}', [SiswaController::class, 'destroy'])->name('siswa.destroy');
    Route::get('/get-profile-photos', [SiswaController::class, 'getProfilePhotos'])->name('siswa.photos');

    Route::get('/photos', [PhotoController::class, 'index'])->name('photos.index');
    Route::post('/photos/upload', [PhotoController::class, 'upload'])->name('photos.upload');
    Route::post('/photos/delete', [PhotoController::class, 'delete'])->name('photos.delete');

    Route::get('/checkStatusCard/{id}', function($id){

        $user = User::where('rfid_id', $id)->exists();
        if($user){
            return response()->json(['success' => true, 'message' => 'RFID telah terdaftar!']);
        }else{
            return response()->json(['success' => false, 'message' => 'RFID belum terdaftar!']);
        }
    })->name('checkStatusCard');

    Route::get('/rfid-connect', function(){
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $user->update(['last_seen' => Carbon::now()]);
        }

        $user = User::where(function ($query) {
            $query->whereNull('rfid_id')
                  ->orWhere('rfid_id', '');
        })->with('warga_tels')->get();
        
        return view('Main.rfid-connect', compact('user'));
    })->name('rfid.connect');

    Route::post('/rfid-connect', function(Request $request){
        try{
            $request->validate([
                'uid' => 'required',
                'nis' => 'required|exists:users,nis',
            ],[
                'uid.required' => 'RFID tidak boleh kosong',
                'nis.required' => 'Kolom NIS tidak boleh kosong',
                'nis.exists' => 'NIS tidak terdaftar',
            ]);
        }catch (ValidationException $e){
            return response()->json([
                'status' => false,
                'message' => collect($e->errors())->flatten()->first()
            ], 200);
        }

        $user = User::where('nis', $request->nis)->first();
        if($user){
            $user->rfid_id = $request->uid;
            $user->save();
            return response()->json(['success' => true, 'message' => 'RFID berhasil terdaftar!']);
        }else{
            return response()->json(['success' => false, 'message' => 'RFID gagal terdaftar!']);
        }
    })->name('rfid.store');

    Route::get('/export-presence', function () {
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $user->update(['last_seen' => Carbon::now()]);
        }
        return Excel::download(new PresencePerMonthExport(), 'Presensi-Tahun-' . now()->year . '.xlsx');
    });
});

Route::get('/logout', function(){
    if (Auth::guard('admin')->check()) {
        Auth::guard('admin')->logout();
    } else if (Auth::guard('superadmin')->check()) {
        Auth::guard('superadmin')->logout();
    }

    return redirect('/');
})->name('logout');

Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

Route::get('/reset-password/admin', [AuthAdminController::class, 'showResetForm'])->name('password.reset.ad');
Route::post('/reset-password/admin', [AuthAdminController::class, 'resetPassword'])->name('password.update.ad');