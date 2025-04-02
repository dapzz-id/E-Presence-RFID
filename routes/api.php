<?php

use App\Http\Controllers\AuthController;
use App\Models\Presence;
use App\Models\User;
use App\Models\WargaTels;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Mockery\Undefined;

Route::post('/sanctum/token', function (Request $request) {
    try {
        $dm = $request->validate([
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

Route::middleware('auth:sanctum')->get('/getMyAccount/{id}', function($id){
    $user = User::with('warga_tels')->where('id', $id)->first();
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
            $fullData[] = [
                'bulan' => $i,
                'total' => $absensi_per_bulan[$i]->total ?? 0
            ];
        }

        return response()->json([
            'status' => 'success',
            'name' => $user->warga_tels->name,
            'absensi_per_bulan' => $fullData,
            'total_hadir_bulan_ini' => Presence::where('nis', $user->nis)
                ->whereMonth('time_masuk', Carbon::now()->month)
                ->whereYear('time_masuk', Carbon::now()->year)
                ->whereNotIn('status', ['Izin', 'Sakit', 'Alpa'])
                ->count(),
            'total_izin_bulan_ini' => Presence::where('nis', $user->nis)
                ->whereMonth('time_masuk', Carbon::now()->month)
                ->whereYear('time_masuk', Carbon::now()->year)
                ->whereNotIn('status', ['Hadir', 'Terlambat'])
                ->count(),
            'absen_hari_ini_status' => Presence::where('nis', $user->nis)
                ->whereDate('time_masuk', Carbon::today())
                ->exists(),
            'rfid_status' => $user->rfid_id != "" || $user->rfid_id != null ? true : false,
            'profile' => env('APP_URL') . 'storage/profile/' . $user->warga_tels->foto_profile,
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
                'Waktu Keluar' => $item->time_keluar,
                'Status' => $item->status,
                'Nama Lengkap' => $item->warga_tels->name ? ucwords(strtolower($item->warga_tels->name)) : '-',
                'Kelas' => $item->warga_tels->kelas ?? '-',
            ];
        })
        ->values()
        ->map(function ($item, $index) {
            $item['id'] = $index + 1;
            return $item;
        });

    return response()->json(["data" => $presences, "count" => $presences->count(), "total_tidak_hadir" => ($presences->filter(fn ($p) => $p['Status'] === 'Izin')->count() + $presences->filter(fn ($p) => $p['Status'] === 'Sakit')->count())]);
});

Route::post('/absensiMasuk', function(Request $request){
    $user = User::where('rfid_id', $request->id)->first();

    if($user){
        $presences = Presence::where('nis', $user->nis)
            ->whereDate('time_masuk', Carbon::today())
            ->first();
        
        if(!$presences){
            if(Carbon::now()->greaterThan(Carbon::today()->addHours(7))){
                $status = 'Terlambat';
            }else{
                $status = 'Hadir';
            }

            Presence::create([
                'nis' => $user->nis,
                'time_masuk' => Carbon::now(),
                'status' => $status
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Absensi masuk berhasil...'
            ]);
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

        if ($presences->time_keluar !== null) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Anda sudah melakukan absensi keluar hari ini...'
            ]);
        }

        $presences->update(['time_keluar' => Carbon::now()]);

        return response()->json([
            'status' => 'success',
            'message' => 'Absensi keluar berhasil...'
        ]);
    }else{
        return response()->json([
            'status' => 'failed',
            'message' => 'Absensi keluar gagal...'
        ]);
    }
});

Route::get('/totalUsers', function(){
    $total = User::count();
    return response()->json(['total' => $total]);
});

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