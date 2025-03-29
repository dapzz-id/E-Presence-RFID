<?php

use App\Models\AdminAccount;
use App\Models\Presence;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

Route::get('/', function () {
    if(Auth::guard('admin')->check()){
        return route('dashboard');
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
    
    if (Auth::guard('web')->attempt($credentials)) {
        return redirect()->route('dashboard')->with('success', 'Login sebagai User berhasil!');
    }
    

    return back()->withErrors(['login' => 'Username atau password salah'])->withInput();
})->name('login.submit');

Route::middleware('auth:admin')->group(function (){
    Route::get('/dashboard', function (){
        $total = (string)User::count();
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

        $dataPresensi = Presence::with('user')->get();

        $totalHariIni = (string)$presences->count();
        $totalTidakHadir = (string)($presences->filter(fn ($p) => $p['Status'] === 'Izin')->count() + $presences->filter(fn ($p) => $p['Status'] === 'Sakit')->count());

        return view('welcome', compact('total', 'totalHariIni', 'totalTidakHadir', 'dataPresensi'));
    })->name('dashboard');
});

Route::get('/logout', function(){
    if (Auth::guard('admin')->check()) {
        Auth::guard('admin')->logout();
    } elseif (Auth::guard('web')->check()) {
        Auth::guard('web')->logout();
    }

    return redirect('/');
});