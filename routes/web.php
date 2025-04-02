<?php

use App\Exports\PresenceExport;
use App\Exports\PresencePerMonthExport;
use App\Http\Controllers\AuthController;
use App\Models\AdminAccount;
use App\Models\Presence;
use App\Models\User;
use App\Models\WargaTels;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

Route::get('/', function () {
    if(Auth::guard('admin')->check()){
        return redirect()->route('dashboard');
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
    Route::get('/dashboard', function (Request $request) {
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

    Route::get('/siswa', function (Request $request) {
        $search = $request->query('search');
        $kelas = $request->query('kelas');
    
        $query = WargaTels::query();
    
        // Filter by search query
        if (!empty($search)) {
            $query->where('name', 'like', "%$search%")
                  ->orWhere('nis', 'like', "%$search%")
                  ->orWhere('alamat', 'like', "%$search%");
        }
    
        // Filter by kelas
        if (!empty($kelas) && $kelas !== 'all') {
            $query->where('kelas', $kelas);
        }
    
        // Pagination 10 data per halaman
        $wargaTels = $query->paginate(10);

        $search2 = $request->query('search2');
        $kelas2 = $request->query('kelas2');
    
        $query2 = User::with('warga_tels');
    
        // Filter by search query
        if (!empty($search2)) {
            $query2->Where('nis', 'like', "%$search2%")
                  ->orWhere('username', 'like', "%$search2%")
                  ->orWhereHas('warga_tels', function ($q) use ($search2) {
                    $q->where('name', 'like', "%$search2%");
                });
        }
    
        // Filter by kelas
        if (!empty($kelas2) && $kelas2 !== 'all') {
            $query2->whereHas('warga_tels', function ($q) use ($kelas2) {
                $q->where('kelas', $kelas2);
            });
        }
    
        // Pagination 10 data per halaman
        $akunSiswa = $query2->paginate(10);
    
        return view('Main.manage-siswa', compact('wargaTels', 'akunSiswa', 'search', 'kelas'));
    })->name('siswa');

    Route::get('/export-presence', function () {
        return Excel::download(new PresencePerMonthExport(), 'Presensi-Tahun-' . now()->year . '.xlsx');
    });
});

Route::get('/logout', function(){
    if (Auth::guard('admin')->check()) {
        Auth::guard('admin')->logout();
    } elseif (Auth::guard('web')->check()) {
        Auth::guard('web')->logout();
    }

    return redirect('/');
})->name('logout');

Route::get('/reset-password', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');