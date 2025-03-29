<?php

use App\Models\Presence;
use App\Models\User;
use App\Models\WargaTels;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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
    ]);
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

Route::get('/allAbsensiMasuk', function(){
    $presences = Presence::where('presence_type', 'Absensi Masuk')
        ->whereDate('time', Carbon::today())
        ->orderBy('time', 'desc')
        ->with('user')
        ->get()
        ->map(function ($item, $index) {
            return [
                'id' => $index + 1,
                'NIS' => $item->nis,
                'Waktu Masuk' => $item->time,
                'Status' => $item->status,
                'Nama Lengkap' => $item->user ? ucwords(strtolower($item->user->name)) : '-',
                'Kelas' => $item->user ? "{$item->user->kelas} {$item->user->jurusan} {$item->user->angka_kelas}" : '-',
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
            ->whereDate('time', Carbon::today())
            ->where('presence_type', "Absensi Masuk")
            ->first();
        
        if(!$presences){
            Presence::create([
                'nis' => $user->nis,
                'presence_type' => 'Absensi Masuk',
                'time' => Carbon::now()->toDateTimeString(),
                'status' => 'Hadir'
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

Route::get('/totalUsers', function(){
    $total = User::count();
    return response()->json(['total' => $total]);
});

Route::post('/daftar-akun', function(Request $request){
    try{
        $data = $request->validate([
            'nis' => 'required|digits:9',
            'username' => 'required|unique:users,username',
            'password' => 'required|string'
        ],[
            'nis.required' => 'NIS tidak boleh kosong',
            'nis.numeric' => 'NIS harus berupa angka',
            'nis.digits' => 'NIS harus 9 digit',
            'username.required' => 'Username tidak boleh kosong',
            'username.unique' => 'Username sudah digunakan',
            'password.required' => 'Password tidak boleh kosong',
            'password.string' => 'Password harus berupa huruf'
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
    $kelasArray = explode(" ", $request->kelas);
    
    $data = User::create([
        'nis' => $request->nis,
        'name' => $request->name,
        'username' => $request->username,
        'password' => Hash::make($request->password),
        'kelas' => ($kelasArray[0] ?? ''),
        'jurusan' => ($kelasArray[1] ?? '')
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
