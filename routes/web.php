<?php

use App\Exports\PresenceExport;
use App\Exports\PresencePerMonthExport;
use App\Http\Controllers\AkunSiswaController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthAdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CreateAkunSiswaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EditAkunSiswaController;
use App\Http\Controllers\LateController;
use App\Http\Controllers\PhotoController;
use App\Http\Controllers\SiswaController;
use App\Models\AdminAccount;
use App\Models\Hari;
use App\Models\Presence;
use App\Models\User;
use App\Models\WargaTels;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\Facades\Image;

Route::get('/', function () {
    $place_id = "ChIJlZZ9CZrbnoERmn8A2W3p36g";
    $api_key = env('GOOGLE_MAPS_API_KEY');

    $response = Http::get("https://maps.googleapis.com/maps/api/place/details/json", [
        'place_id' => $place_id,
        'fields' => 'rating,reviews,user_ratings_total',
        'key' => $api_key
    ]);

    $data = $response->json();

    return view('main', [
        'response' => $data,
        'averageRating' => $data['result']['rating'] ?? 0,
        'totalReviews' => $data['result']['user_ratings_total'] ?? 0
    ]);
})->name('view.main');

Route::get('/privacy', function () {
    return view('privacy');
})->name('view.privacy');

Route::get('/login', function(){
    if(Auth::guard('admin')->check()){
        return redirect()->route('dashboard');
    }else if (Auth::guard('superadmin')->check()){
        return redirect()->route('dashboard.sa');
    }else{
        return view('Auth.Login');
    }
})->name('login');


Route::post('/', function (Request $request) {
    // Key unik dengan IP
    $key = $request->ip();
    $maxAttempts = 3; // maksimal 3 percobaan
    $decaySeconds = 60; // 1 menit

    if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
        $seconds = RateLimiter::availableIn($key);
        return back()->withErrors([
            'login' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik."
        ])->withInput();
    }

    RateLimiter::hit($key, $decaySeconds);

    $credentials = $request->validate([
        'username' => 'required|string|min:6',
        'password' => 'required|string|min:6'
    ], [
        'username.required' => 'Kolom username wajib diisi',
        'username.min' => 'Panjang username minimal 6 karakter',
        'password.required' => 'Kolom password wajib diisi',
        'password.min' => 'Panjang password minimal 6 karakter',
    ]);

    if (Auth::guard('admin')->attempt($credentials)) {
        $user = Auth::guard('admin')->user();

        if ($user->membership == 1) {
            return redirect()->route('dashboard')->with('success', 'Login sebagai Admin berhasil!');
        }

        Auth::guard('admin')->logout();
        return back()->withErrors(['login' => 'Akun Anda belum berlangganan pada layanan ini'])->withInput();
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

Route::get('/late-arrival', [LateController::class, 'showArrivalForm']);
Route::post('/late-arrival', [LateController::class, 'storeArrival'])->name('late-arrival.store');

Route::get('/reason-coming', [LateController::class, 'showReasonForm']);
Route::post('/reason-coming', [LateController::class, 'storeReason'])->name('reason-coming.store');

Route::get('/late-departure', [LateController::class, 'showDepartureForm']);
Route::post('/late-departure', [LateController::class, 'storeDeparture'])->name('late-departure.store');

Route::get('/early-departure', [LateController::class, 'showEarlyDepartureForm']);
Route::post('/early-departure', [LateController::class, 'storeEarlyDeparture'])->name('early-departure.store');

Route::get('/get-reviews', function () {
    $place_id = "ChIJlZZ9CZrbnoERmn8A2W3p36g"; // Ganti dengan milikmu
    $api_key = env('GOOGLE_MAPS_API_KEY'); // Ganti dengan API key-mu

    $url = "https://maps.googleapis.com/maps/api/place/details/json?place_id={$place_id}&fields=rating,reviews,user_ratings_total&key={$api_key}";

    $response = Http::get($url);

    return response()->json($response->json());
});

Route::middleware('auth')->group(function () {
    Route::patch('/akun-siswa/ban-akun/{id}', [AkunSiswaController::class, 'banAccount'])->name('akun.ban');
    Route::patch('/akun-siswa/unban-akun/{id}', [AkunSiswaController::class, 'unbanAccount'])->name('akun.unban');
});

Route::get('/dataHariIni', [DashboardController::class, 'index2'])->name('check.hari.ini');

Route::prefix('admin')->middleware('auth:admin')->group(function (){
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/', function(){
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $user->update(['last_seen' => Carbon::now()]);
        }

        return redirect()->route('dashboard');
    })->name('dashboard.redirect');

    Route::get('/akun-siswa/create-akun', [CreateAkunSiswaController::class, 'showForm'])->name('akun.siswa.create');
    Route::post('/akun-siswa/create-akun', [CreateAkunSiswaController::class, 'store'])->name('akun.siswa.create.post');
    Route::post('/akun-siswa/delete-multiple', [AkunSiswaController::class, 'deleteMultiple'])->name('akun.delete.multiple');
    Route::post('/siswa/delete-multiple', [AkunSiswaController::class, 'deleteMultipleSiswa'])->name('siswa.delete.multiple');
    Route::get('/akun-siswa/edit-akun/{id}', [EditAkunSiswaController::class, 'showForm'])->name('akun.siswa.edit');
    Route::post('/akun-siswa/edit-akun/{id}', [EditAkunSiswaController::class, 'update'])->name('akun.siswa.update');
    Route::post('/akun-siswa/edit-akun/rfid/remove/{id}', [EditAkunSiswaController::class, 'removeRfid'])->name('akun.siswa.rfid.remove');

    Route::get('/hari', [App\Http\Controllers\HariController::class, 'index'])->name('hari.index');
    Route::get('/hari/form', [App\Http\Controllers\HariController::class, 'monthForm'])->name('hari.month-form');
    Route::post('/hari/save', [App\Http\Controllers\HariController::class, 'saveMonth'])->name('hari.save-month');

    Route::get('/connections', [App\Http\Controllers\ConnectionController::class, 'index'])->name('connections.index');
    Route::post('/connect-service', [App\Http\Controllers\ConnectionController::class, 'connect'])->name('connect.service');
    Route::post('/disconnect-service', [App\Http\Controllers\ConnectionController::class, 'disconnect'])->name('disconnect.service');

    Route::get('/akun-siswa/getChart', [AttendanceController::class, 'index'])->name('attendance.index');

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

    Route::get('/export-presence', [App\Http\Controllers\ExportController::class, 'exportPresence'])->name('export.presence');
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