<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CreateAkunSiswaController;
use App\Http\Controllers\EditAkunSiswaController;
use App\Http\Controllers\WhatsAppController;
use App\Models\LateEntry;
use App\Models\Notification;
use App\Models\Presence;
use App\Models\User;
use App\Models\WargaTels;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Mockery\Undefined;

Route::post('/sanctum/token', function (Request $request) {
    try {
        $request->validate([
            'username' => 'required',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username tidak boleh kosong',
            'password.required' => 'Password tidak boleh kosong',
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'status' => 'failed',
            'message' => collect($e->errors())->flatten()->first()
        ], 200);
    }
 
    $user = User::where('username', $request->username)->first();
 
    if (! $user || ! Hash::check($request->password, $user->password)) {
        return response()->json([
            'status' => 'failed',
            'message' => 'Username atau password salah. '
        ], 200);
    }

    $user->tokens()->delete();
 
    return response()->json([
        'status' => 'success',
        'token' => $user->createToken($request->username)->plainTextToken,
        'uid' => $user->id,
    ]);
});

Route::post('/sanctum/tap', function(Request $request){
    try {
        $request->validate([
            'id' => 'required|exists:users,rfid_id'
        ], [
            'id.required' => 'ID kartu tidak boleh kosong',
            'id.exists' => 'ID kartu tidak terdaftar',
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'status' => 'failed',
            'message' => collect($e->errors())->flatten()->first()
        ], 200);
    }

    $user = User::where('rfid_id', $request->id)->first();

    if($user){
        $user->tokens()->delete();
        return response()->json([
            'status' => 'success',
            'token' => $user->createToken($user->username)->plainTextToken,
            'uid' => $user->id,
        ]);
    }else{
        return response()->json([
            'status' => 'failed',
            'message' => 'Kartu Anda tidak terdaftar...'
        ]);
    }
});

Route::middleware('auth:sanctum')->get('/getMyAccount/{id}', function($id){
    $user = User::with('warga_tels')->where('id', $id)->first();
    $presenceController = new AttendanceController();
    if($user){
        $absensi_per_bulan = Presence::where('nis', $user->nis)
            ->whereYear('time_masuk', Carbon::now()->year)
            ->whereIn('status', ['Hadir', 'Terlambat'])
            ->select(DB::raw('MONTH(time_masuk) as bulan'), DB::raw('COUNT(*) as total'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $fullData = [];
        for ($i = 1; $i <= 12; $i++) {
            $hariMasuk = count($presenceController->getProductiveDays($i, Carbon::now()->year)['productive']);
            $totalKehadiran = $absensi_per_bulan[$i]->total ?? 0;
            $percentHadir = $hariMasuk > 0 ? ($totalKehadiran / $hariMasuk) * 100 : 0;
            $fullData[] = [
                'bulan' => $i,
                'total' => $absensi_per_bulan[$i]->total ?? 0,
                'persentase' => round($percentHadir, 0),
            ];
        }
        /** @var \Illuminate\Filesystem\AwsS3V3Adapter $disk */
        $disk = Storage::disk('s3');
        $url = $disk->temporaryUrl('profile/'.$user->warga_tels->foto_profile, now()->addMinutes(5));

        $kehadiran = Presence::where('nis', $user->nis)
            ->whereMonth('time_masuk', Carbon::now()->month)
            ->whereYear('time_masuk', Carbon::now()->year)
            ->whereNotIn('status', ['Izin', 'Sakit', 'Alpa']);

        return response()->json([
            'status' => 'success',
            'name' => $user->warga_tels->name,
            'kelas' => $user->warga_tels->kelas,
            'nis' => $user->nis,
            'absensi_per_bulan' => $fullData,
            'total_hadir_bulan_ini' => $kehadiran->count(),
            'total_izin_bulan_ini' => Presence::where('nis', $user->nis)
                ->whereMonth('time_masuk', Carbon::now()->month)
                ->whereYear('time_masuk', Carbon::now()->year)
                ->whereNotIn('status', ['Hadir', 'Terlambat', 'Alpa'])
                ->count(),
            'absen_hari_ini_status' => Presence::where('nis', $user->nis)
                ->whereDate('time_masuk', Carbon::today())
                ->exists(),
            'rfid_status' => $user->rfid_id != "" || $user->rfid_id != null ? true : false,
            'profile' => $url,
        ]);
    }

    return response()->json([
        'status' => 'failed',
        'message' => 'Data tidak ditemukan...'
    ], 200);
});

Route::post('/validate-acc', function (Request $request) {
    $id = $request->input('id');
    $user = User::with('warga_tels')->where('rfid_id', $id)->first();

    if($user){
        return response()->json([
            'status' => 'success',
            'data' => $user,
            'profile' => env('APP_URL') . 'storage/profile/' . $user->warga_tels->foto_profile,
        ]);
    }else{
        return response()->json([
            'status' => 'failed',
            'message' => 'Data kartu Anda tidak terdaftar...'
        ]);
    }
});

Route::get('/allAbsensi', function(){
    $presences = Presence::whereDate('time_masuk', Carbon::today())
        ->orderBy('time_masuk', 'desc')
        ->with('warga_tels')
        ->get()
        ->map(function ($item, $index) {
            return [
                'id' => $index + 1,
                'NIS' => $item->nis,
                'Waktu Masuk' => $item->time_masuk,
                'Waktu Keluar' => $item->time_keluar ?? '-',
                'Status Masuk' => $item->status,
                'Status Keluar' => $item->status_keluar ?? '-',
                'Alasan Datang' => $item->alasan_datang_telat ?? '-',
                'Alasan Pulang' => $item->alasan_pulang_telat ?? $item->alasan_pulang_duluan ?? '-',
                'Nama Lengkap' => $item->warga_tels->name ? ucwords(strtolower($item->warga_tels->name)) : '-',
                'Kelas' => $item->warga_tels->kelas ?? '-',
            ];
        })
        ->values()
        ->map(function ($item, $index) {
            $item['id'] = $index + 1;
            return $item;
        });

    return response()->json(["data" => $presences, "count" => $presences->count(), "total_tidak_hadir" => ($presences->filter(fn ($p) => $p['Status Masuk'] === 'Izin')->count() + $presences->filter(fn ($p) => $p['Status Masuk'] === 'Sakit')->count())]);
});

Route::get('/siswa/{nis}', [CreateAkunSiswaController::class, 'getSiswaByNis']);
Route::post('/send-verification-code', [CreateAkunSiswaController::class, 'sendVerificationCode']);
Route::post('/check-rfid-status', [EditAkunSiswaController::class, 'checkRfidStatus'])->name('api.check.rfid.status');

Route::middleware('auth:sanctum')->patch('/linkedCard/{id}', function(Request $request, $id){
    try {
        $request->validate([
            'id_card' => 'required|unique:users,rfid_id',
        ], [
            'id_card.required' => 'ID kartu tidak boleh kosong',
            'id_card.unique' => 'ID kartu sudah terdaftar',
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'status' => 'failed',
            'message' => collect($e->errors())->flatten()->first()
        ], 200);
    }

    $user = User::find($id);

    if($user && is_null($user->rfid_id)){
        $user->update(['rfid_id' => $request->id_card]);

        return response()->json([
            'status' => 'success',
            'message' => 'Kartu berhasil dihubungkan...'
        ]);
    }else{
        if(!is_null($user->rfid_id)){
            return response()->json([
                'status' => 'failed',
                'message' => 'Akun Anda telah terhubung dengan kartu pelajar...'
            ]);
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Akun Anda tidak ditemukan...'
            ]);
        }
    }
});

Route::post('/absensiMasuk', function(Request $request){
    $user = User::where('rfid_id', $request->id)->first();

    if($user){
        $presences = Presence::where('nis', $user->nis)
            ->whereDate('time_masuk', Carbon::today())
            ->first();
        
        if(!$presences){
            if(Carbon::now()->greaterThan(Carbon::today()->addHours(7)->addMinutes(00)->addSeconds(00))){
                $token = Illuminate\Support\Str::random(40);

                LateEntry::create([
                    'nis' => $user->nis,
                    'rfid_id' => $user->rfid_id,
                    'time' => Carbon::now(),
                    'type' => 'Presensi Masuk',
                    'token' => $token
                ]);

                return response()->json([
                    'status' => 'late',
                    'redirect_url' => url('/late-arrival?token=' . $token),
                    'message' => 'Anda terlambat, silakan isi form keterlambatan.'
                ]);
            }else{
                Presence::create([
                    'nis' => $user->nis,
                    'time_masuk' => Carbon::now(),
                    'status' => 'Hadir'
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Absensi masuk berhasil...'
                ]);
            }
        }else{
            return response()->json([
                'status' => 'failed',
                'message' => 'Anda sudah melakukan absensi masuk...'
            ]);
        }
    }else{
        return response()->json([
            'status' => 'failed',
            'message' => 'Absensi masuk gagal...'
        ]);
    }
});

Route::post('/absensiKeluar', function(Request $request){
    $user = User::where('rfid_id', $request->id)->first();

    if($user){
        $presences = Presence::where('nis', $user->nis)
            ->whereDate('time_masuk', Carbon::today())
            ->whereNotNull('time_masuk')
            ->first();

        if (!$presences) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Anda belum melakukan absensi masuk hari ini...'
            ]);
        }
        
        if($presences->time_keluar !== null){
            return response()->json([
                'status' => 'failed',
                'message' => 'Anda sudah melakukan absensi keluar hari ini...'
            ]);
        }

        if (Carbon::now()->greaterThan(Carbon::today()->addHours(16)->addMinutes(30))) {
            $token = Illuminate\Support\Str::random(40);

            LateEntry::create([
                'nis' => $user->nis,
                'rfid_id' => $user->rfid_id,
                'time' => Carbon::now(),
                'type' => 'Presensi Keluar',
                'token' => $token
            ]);

            return response()->json([
                'status' => 'late',
                'redirect_url' => url('/late-departure?token=' . $token),
                'message' => 'Anda terlambat pulang, silakan isi form keterlambatan.'
            ]);
        }else if (Carbon::now()->lessThan(Carbon::today()->addHours(16)->addMinutes(30)) && Carbon::now()->lessThan(Carbon::today()->addHours(15)->addMinutes(29)->addSeconds(59))) {
            $token = Illuminate\Support\Str::random(40);

            LateEntry::create([
                'nis' => $user->nis,
                'rfid_id' => $user->rfid_id,
                'time' => Carbon::now(),
                'type' => 'Presensi Keluar',
                'token' => $token
            ]);

            return response()->json([
                'status' => 'early',
                'redirect_url' => url('/early-departure?token=' . $token),
                'message' => 'Anda pulang lebih awal, silakan isi form alasan pulang lebih awal.'
            ]);
        }else if(Carbon::now()->lessThan(Carbon::today()->addHours(16)->addMinutes(30)) && Carbon::now()->greaterThan(Carbon::today()->addHours(15)->addMinutes(29)->addSeconds(59))){
            $presences->update([
                'time_keluar' => Carbon::now(),
                'status_keluar' => 'Tepat Waktu'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Absensi pulang tepat waktu...'
            ]);
        }
    }else{
        return response()->json([
            'status' => 'failed',
            'message' => 'Absensi pulang gagal...'
        ]);
    }
});

Route::get('/totalUsers', function(){
    $total = User::count();
    return response()->json(['total' => $total]);
});

Route::post('/send-whatsapp', [WhatsAppController::class, 'send']);

Route::post('/daftar-akun', function(Request $request){
    try{
        $data = $request->validate([
            'nis' => 'required|digits:9|unique:users,nis',
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string'
        ],[
            'nis.required' => 'NIS tidak boleh kosong',
            'nis.numeric' => 'NIS harus berupa angka',
            'nis.digits' => 'NIS harus 9 digit',
            'username.required' => 'Username tidak boleh kosong',
            'username.unique' => 'Username sudah digunakan',
            'password.required' => 'Password tidak boleh kosong',
            'password.string' => 'Password harus berupa huruf',
            'email.required' => 'Email tidak boleh kosong',
            'email.email' => 'Email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'nis.unique' => 'NIS sudah digunakan',
        ]);
    }catch (ValidationException $e){
        return response()->json([
            'status' => 'failed',
            'message' => collect($e->errors())->flatten()->first()
        ], 200);
    }
    

    if (User::where('nis', $request->nis)->exists()) {
        return response()->json(['status' => 'failed', 'message' => 'NIS sudah terdaftar'], 200);
    } 
    
    $resultData = WargaTels::where('nis', $request->nis)->first();
    if ($data && $resultData) {
        return response()->json([
            'status' => 'success', 
            'data' => $resultData,
            'include' => $data
        ], 200);
    }
});

Route::post('/register-account', function(Request $request){    
    $data = User::create([
        'nis' => $request->nis,
        'username' => $request->username,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    if($data){
        return response()->json([
            'status' => 'success',
            'message' => 'Akun Anda berhasil dibuat, silahkan login untuk melanjutkan...'
        ], 200);
    }else{
        return response()->json([
            'status' => 'failed',
            'message' => 'Akun Anda gagal dibuat, silahkan coba lagi nanti...'
        ], 200);
    }
});

Route::middleware('auth:sanctum')->get('/profile', function (Request $request) {
    return response()->json(['status' => 'success']);
});

Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);