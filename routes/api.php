<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CreateAkunSiswaController;
use App\Http\Controllers\EditAkunSiswaController;
use App\Http\Controllers\WhatsAppController;
use App\Models\Hari;
use App\Models\LateEntry;
use App\Models\LeaveDocument;
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

    if($user->status_ban == 'inactive'){
        return response()->json([
            'status' => 'failed',
            'message' => 'Akun Anda telah dibekukan, silahkan hubungi admin untuk mengaktifkan kembali...'
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
            ->where('status_hari', 'Hari Produktif')
            ->select(DB::raw('MONTH(time_masuk) as bulan'), DB::raw('COUNT(*) as total'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $absensi_per_bulan_non = Presence::where('nis', $user->nis)
            ->whereYear('time_masuk', Carbon::now()->year)
            ->whereIn('status', ['Hadir', 'Terlambat'])
            ->where('status_hari', 'Hari Non-Produktif')
            ->select(DB::raw('MONTH(time_masuk) as bulan'), DB::raw('COUNT(*) as total'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get()
            ->keyBy('bulan');

        $fullData = [];
        $fullNonData = [];
        for ($i = 1; $i <= 12; $i++) {
            $hariMasuk = count($presenceController->getProductiveDays($i, Carbon::now()->year)['productive']);
            $totalKehadiran = $absensi_per_bulan[$i]->total ?? 0;
            $percentHadir = $hariMasuk > 0 ? ($totalKehadiran / $hariMasuk) * 100 : 0;
            $fullData[] = [
                'bulan' => $i,
                'total' => $absensi_per_bulan[$i]->total ?? 0,
                'persentase' => round($percentHadir, 0),
            ];

            $hariMasukNon = count($presenceController->getProductiveDays($i, Carbon::now()->year)['non_productive']);
            $totalKehadiranNon = $absensi_per_bulan_non[$i]->total ?? 0;
            $percentHadirNon = $hariMasukNon > 0 ? ($totalKehadiranNon / $hariMasukNon) * 100 : 0;
            $fullNonData[] = [
                'bulan' => $i,
                'total' => $absensi_per_bulan_non[$i]->total ?? 0,
                'persentase' => round($percentHadirNon, 0),
            ];
        }
        
        /** @var \Illuminate\Filesystem\AwsS3V3Adapter $disk */
        $disk = Storage::disk('s3');
        $url = $disk->temporaryUrl('profile/'.$user->warga_tels->foto_profile, now()->addMinutes(5));

        $kehadiran = Presence::where('nis', $user->nis)
            ->whereMonth('time_masuk', Carbon::now()->month)
            ->whereYear('time_masuk', Carbon::now()->year)
            ->whereNotIn('status', ['Izin', 'Sakit', 'Alpa']);

        $alpa = Presence::where('nis', $user->nis)
            ->whereMonth('time_masuk', Carbon::now()->month)
            ->whereYear('time_masuk', Carbon::now()->year)
            ->where('status', 'Alpa');

        return response()->json([
            'status' => 'success',
            'name' => $user->warga_tels->name,
            'kelas' => $user->warga_tels->kelas,
            'nis' => $user->nis,
            'absensi_per_bulan' => $fullData,
            'absensi_per_bulan_non_productive' => $fullNonData,
            'total_hadir_bulan_ini' => $kehadiran->count(),
            'total_alpa_bulan_ini' => $alpa->count(),
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

Route::post('/send-leave-document', function(Request $request){
    try {
        $request->validate([
            'nis' => 'required|exists:users,nis',
            'type' => 'required|in:izin,sakit',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png,mp4,mov,avi|max:10240',
        ], [
            'nis.required' => 'NIS tidak boleh kosong',
            'nis.exists' => 'NIS tidak terdaftar',
            'type.required' => 'Tipe izin tidak boleh kosong',
            'type.in' => 'Tipe izin tidak valid',
            'start_date.required' => 'Tanggal mulai tidak boleh kosong',
            'end_date.required' => 'Tanggal selesai tidak boleh kosong',
            'end_date.after_or_equal' => 'Tanggal selesai harus setelah atau sama dengan tanggal mulai',
            'reason.required' => 'Alasan tidak boleh kosong',
            'document.required' => 'Dokumen tidak boleh kosong',
            'document.file' => 'Dokumen harus berupa file',
            'document.mimes' => 'Dokumen harus berupa file dengan format pdf, jpg, jpeg, png, mp4',
            'document.max' => 'Dokumen tidak boleh lebih dari 10MiB',
        ]);
    } catch (ValidationException $e) {
        return response()->json([
            'status' => 'failed',
            'message' => collect($e->errors())->flatten()->first()
        ], 200);
    }

    $user = User::where('nis', $request->nis)->first();

    if($user){
        LeaveDocument::create([
            'nis' => $user->nis,
            'type' => ucwords($request->type),
            'start_date' => Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d'),
            'end_date' => Carbon::createFromFormat('d/m/Y', $request->end_date)->format('Y-m-d'),
            'reason' => $request->reason,
            'document_path' => Storage::disk('s3')->put('surat/'.$user->nis, $request->file('document')),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Dokumen izin berhasil dikirim...'
        ]);
    }else{
        return response()->json([
            'status' => 'failed',
            'message' => 'Pengiriman dokumen izin gagal...'
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
                'Alasan Datang' => $item->alasan_datang_telat ?? $item->alasan_datang ?? '-',
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

// Route::post('/absensiMasuk', function(Request $request){
//     $user = User::where('rfid_id', $request->id)->first();

//     if($user){
//         $presences = Presence::where('nis', $user->nis)
//             ->whereDate('time_masuk', Carbon::today())
//             ->first();
        
//         if(!$presences){
//             if(Carbon::now()->greaterThan(Carbon::today()->addHours(7)->addMinutes(00)->addSeconds(00))){
//                 $token = Illuminate\Support\Str::random(40);

//                 LateEntry::create([
//                     'nis' => $user->nis,
//                     'rfid_id' => $user->rfid_id,
//                     'time' => Carbon::now(),
//                     'type' => 'Presensi Masuk',
//                     'token' => $token
//                 ]);

//                 return response()->json([
//                     'status' => 'late',
//                     'redirect_url' => url('/late-arrival?token=' . $token),
//                     'message' => 'Anda terlambat, silakan isi form keterlambatan.'
//                 ]);
//             }else{
//                 Presence::create([
//                     'nis' => $user->nis,
//                     'time_masuk' => Carbon::now(),
//                     'status' => 'Hadir'
//                 ]);

//                 return response()->json([
//                     'status' => 'success',
//                     'message' => 'Absensi masuk berhasil...'
//                 ]);
//             }
//         }else{
//             return response()->json([
//                 'status' => 'failed',
//                 'message' => 'Anda sudah melakukan absensi masuk...'
//             ]);
//         }
//     }else{
//         return response()->json([
//             'status' => 'failed',
//             'message' => 'Absensi masuk gagal...'
//         ]);
//     }
// });

Route::post('/absensiMasuk', function(Request $request) {
    $user = User::where('rfid_id', $request->id)->first();

    if (!$user) {
        return response()->json([
            'status' => 'failed',
            'message' => 'Absensi masuk gagal, user tidak ditemukan.'
        ]);
    }

    $today = Carbon::today()->toDateString();

    // Ambil data hari untuk bulan dan tahun ini
    $hari = Hari::where('bulan', Carbon::now()->month)
        ->where('tahun', Carbon::now()->year)
        ->first();

    if (!$hari) {
        return response()->json([
            'status' => 'failed',
            'message' => 'Data hari untuk bulan ini tidak ditemukan.'
        ]);
    }

    // Ubah ke array
    $libur = $hari->hari_libur;
    $produktif = $hari->hari_produktif;
    $tambahan = $hari->hari_tambahan;

    // Cek apakah hari ini libur
    if (in_array($today, $libur)) {
        return response()->json([
            'status' => 'libur',
            'message' => 'Hari ini adalah hari libur.'
        ]);
    }

    // Cek apakah sudah absen masuk
    $presence = Presence::where('nis', $user->nis)
        ->whereDate('time_masuk', $today)
        ->first();

    if ($presence) {
        return response()->json([
            'status' => 'failed',
            'message' => 'Anda sudah melakukan absensi masuk.'
        ]);
    }

    $now = Carbon::now();
    $token = Illuminate\Support\Str::random(40);

    // Jika hari produktif
    if (in_array($today, $produktif)) {
        $batasTerlambat = Carbon::today()->addHours(7);

        if ($now->greaterThan($batasTerlambat)) {
            LateEntry::create([
                'nis' => $user->nis,
                'rfid_id' => $user->rfid_id,
                'time' => $now,
                'type' => 'Presensi Masuk',
                'token' => $token
            ]);

            return response()->json([
                'status' => 'late',
                'redirect_url' => url('/late-arrival?token=' . $token),
                'message' => 'Anda terlambat, silakan isi form keterlambatan.'
            ]);
        } else {
            Presence::create([
                'nis' => $user->nis,
                'time_masuk' => $now,
                'status' => 'Hadir'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Absensi masuk berhasil.'
            ]);
        }
    }

    // Jika hari tambahan
    if (in_array($today, $tambahan)) {
        LateEntry::create([
            'nis' => $user->nis,
            'rfid_id' => $user->rfid_id,
            'time' => $now,
            'type' => 'Presensi Masuk',
            'token' => $token
        ]);

        return response()->json([
            'status' => 'non_productive',
            'redirect_url' => url('/reason-coming?token=' . $token),
            'message' => 'Silakan isi form alasan hadir di hari Non-Produktif.'
        ]);
    }

    // Kalau tanggal ga masuk ke kategori apapun
    return response()->json([
        'status' => 'failed',
        'message' => 'Hari ini tidak terdaftar sebagai hari produktif, tambahan, atau libur.'
    ]);
});

Route::post('/absensiKeluar', function(Request $request) {
    $user = User::where('rfid_id', $request->id)->first();

    if (!$user) {
        return response()->json([
            'status' => 'failed',
            'message' => 'Absensi pulang gagal, pengguna tidak ditemukan.'
        ]);
    }

    $today = Carbon::today()->toDateString();

    // Ambil data hari
    $hari = Hari::where('bulan', Carbon::now()->month)
        ->where('tahun', Carbon::now()->year)
        ->first();

    if (!$hari) {
        return response()->json([
            'status' => 'failed',
            'message' => 'Data hari untuk bulan ini tidak ditemukan.'
        ]);
    }

    $libur = $hari->hari_libur;
    $produktif = $hari->hari_produktif;
    $tambahan = $hari->hari_tambahan;

    if (in_array($today, $libur)) {
        return response()->json([
            'status' => 'libur',
            'message' => 'Hari ini adalah hari libur.'
        ]);
    }

    $presence = Presence::where('nis', $user->nis)
        ->whereDate('time_masuk', $today)
        ->whereNotNull('time_masuk')
        ->first();

    if (!$presence) {
        return response()->json([
            'status' => 'failed',
            'message' => 'Anda belum melakukan absensi masuk hari ini.'
        ]);
    }

    if ($presence->time_keluar !== null) {
        return response()->json([
            'status' => 'failed',
            'message' => 'Anda sudah melakukan absensi keluar hari ini.'
        ]);
    }

    $now = Carbon::now();
    $token = Illuminate\Support\Str::random(40);

    if (in_array($today, $produktif)) {
        $jamPulang = Carbon::today()->addHours(16)->addMinutes(30);
        $jamPulangCepat = Carbon::today()->addHours(15)->addMinutes(30);

        if ($now->greaterThan($jamPulang)) {
            LateEntry::create([
                'nis' => $user->nis,
                'rfid_id' => $user->rfid_id,
                'time' => $now,
                'type' => 'Presensi Keluar',
                'token' => $token
            ]);

            return response()->json([
                'status' => 'late',
                'redirect_url' => url('/late-departure?token=' . $token),
                'message' => 'Anda terlambat pulang, silakan isi form keterlambatan.'
            ]);
        } elseif ($now->lessThan($jamPulangCepat)) {
            LateEntry::create([
                'nis' => $user->nis,
                'rfid_id' => $user->rfid_id,
                'time' => $now,
                'type' => 'Presensi Keluar',
                'token' => $token
            ]);

            return response()->json([
                'status' => 'early',
                'redirect_url' => url('/early-departure?token=' . $token),
                'message' => 'Anda pulang lebih awal, silakan isi form alasan pulang lebih awal.'
            ]);
        } else {
            $presence->update([
                'time_keluar' => $now,
                'status_hari' => 'Hari Produktif',
                'status_keluar' => 'Tepat Waktu'
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Absensi pulang tepat waktu.'
            ]);
        }
    } elseif (in_array($today, $tambahan)) {
        // Langsung update tanpa form
        $presence->update([
            'time_keluar' => $now,
            'status_hari' => 'Hari Non-Produktif',
            'status_keluar' => 'Tepat Waktu'
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Absensi pulang di hari Non-Produktif berhasil.'
        ]);
    }

    return response()->json([
        'status' => 'failed',
        'message' => 'Hari ini tidak terdaftar sebagai hari produktif, tambahan, atau libur.'
    ]);
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