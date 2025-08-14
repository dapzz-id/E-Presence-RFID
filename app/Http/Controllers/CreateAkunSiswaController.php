<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\VerificationCode;
use App\Models\WargaTels;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Carbon\Carbon;

class CreateAkunSiswaController extends Controller
{
    /**
     * Menampilkan form untuk membuat akun siswa baru
     */
    public function showForm()
    {
        // Ambil data siswa yang belum memiliki akun
        $siswa = WargaTels::whereNotIn('nis', function($query) {
            $query->select('nis')
                  ->from('users')
                  ->whereNotNull('nis');
        })->orderBy('nis')->get();
        
        return view('Main.Components.Forms.create-akun-siswa', compact('siswa'));
    }
    
    /**
     * Menyimpan akun siswa baru
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nis' => 'required|exists:warga_tels,nis|unique:users,nis',
            'username' => 'required|string|min:5|max:20|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', 'confirmed', Password::min(8)],
            'verification_code' => 'required|string',
        ], [
            'nis.required' => 'NIS harus dipilih',
            'nis.exists' => 'NIS tidak ditemukan dalam database',
            'nis.unique' => 'NIS sudah memiliki akun',
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'password.required' => 'Password harus diisi',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'verification_code.required' => 'Kode verifikasi harus diisi',
        ]);
        
        // Verifikasi kode
        $verificationCode = VerificationCode::where('email', $request->email)
            ->where('code', $request->verification_code)
            ->where('expires_at', '>', Carbon::now())
            ->first();
            
        if (!$verificationCode) {
            return back()->withInput()->with('error', 'Kode verifikasi tidak valid atau sudah kadaluarsa');
        }
        
        try {
            DB::beginTransaction();
            
            // Ambil data siswa
            $siswa = WargaTels::where('nis', $request->nis)->first();
            
            // Buat akun baru
            $user = new User();
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->nis = $request->nis;
            $user->status_ban = 'active';
            $user->email_verified_at = Carbon::now();
            $user->save();
            
            // Hapus kode verifikasi
            $verificationCode->delete();
            
            DB::commit();
            
            // Kirim email notifikasi
            $this->sendAccountCreationNotification($user, $siswa);
            
            return redirect()->route('akun.siswa')->with('success', 'Akun siswa berhasil dibuat');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat akun: ' . $e->getMessage());
        }
    }
    
    /**
     * Mendapatkan data siswa berdasarkan NIS (API)
     */
    public function getSiswaByNis($nis)
    {
        $siswa = WargaTels::where('nis', $nis)->first();
        
        if (!$siswa) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa tidak ditemukan'
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'siswa' => $siswa
        ]);
    }
    
    /**
     * Mengirim kode verifikasi ke email (API)
     */
    public function sendVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        
        try {
            // Generate kode verifikasi 6 digit
            $code = mt_rand(100000, 999999);
            
            // Simpan kode verifikasi ke database
            VerificationCode::updateOrCreate(
                ['email' => $request->email],
                [
                    'code' => $code,
                    'expires_at' => Carbon::now()->addMinutes(15), // Berlaku 15 menit
                ]
            );
            
            // Kirim email
            $this->sendVerificationEmail($request->email, $code);
            
            return response()->json([
                'success' => true,
                'message' => 'Kode verifikasi berhasil dikirim'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengirim kode verifikasi: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Kirim email kode verifikasi
     */
    private function sendVerificationEmail($email, $code)
    {
        $data = [
            'code' => $code,
            'expires_in' => '15 menit'
        ];
        
        Mail::send('emails.verification-code', $data, function($message) use ($email) {
            $message->to($email);
            $message->subject('Kode Verifikasi Akun Siswa');
        });
    }
    
    /**
     * Kirim email notifikasi pembuatan akun
     */
    private function sendAccountCreationNotification($user, $siswa)
    {
        $data = [
            'name' => $siswa->name,
            'username' => $user->username,
            'email' => $user->email
        ];
        
        Mail::send('emails.account-created', $data, function($message) use ($user) {
            $message->to($user->email);
            $message->subject('Akun Siswa Berhasil Dibuat');
        });
    }
}