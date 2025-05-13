<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LateAttendance; // Model untuk menyimpan data keterlambatan
use App\Models\LateEntry;
use App\Models\Presence;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LateController extends Controller
{
    /**
     * Menampilkan form keterlambatan datang
     * * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showArrivalForm(Request $request)
    {
        $token = $request->query('token');
        $lateEntry = LateEntry::where('token', $token)->first();

        if (!$lateEntry || $lateEntry->type != 'Presensi Masuk') {
            abort(404);
        }

        $user = User::where('nis', $lateEntry->nis)->first();

        return view('Main.late-arrival', compact('user', 'lateEntry'));
    }

    /**
     * Menyimpan data keterlambatan datang
     * * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeArrival(Request $request)
    {
        $validated = $request->validate([
            'alasan' => 'required',
            'token' => 'required'
        ],[
            'alasan.required' => 'Alasan keterlambatan datang tidak boleh kosong!',
            'token.required' => 'Token tidak valid atau sudah kadaluwarsa!'
        ]);

        $lateEntry = LateEntry::where('token', $request->token)->first();

        if (!$lateEntry) {
            return redirect()->back()->withErrors([
                'token' => 'Token tidak valid atau sudah kadaluwarsa!'
            ]);
        }
        // Cek apakah sudah ada data presensi untuk hari ini
        $presence = Presence::where('nis', $lateEntry->nis)
            ->whereDate('time_masuk', Carbon::today())
            ->first();

        if (!$presence) {
            Presence::create([
                'nis' => $lateEntry->nis,
                'time_masuk' => $lateEntry->time,
                'status' => 'Terlambat',
                'status_hari' => 'Hari Produktif',
                'alasan_datang_telat' => $validated['alasan'],
            ]);

            $lateEntry->delete();
            return redirect('/');
        } else {
            $lateEntry->delete();
            return redirect()->back()->withErrors([
                'current_time' => 'Anda sudah melakukan absensi masuk hari ini...'
            ]);
        }
    }

    /**
     * Menampilkan form keterlambatan pulang
     * * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showDepartureForm(Request $request)
    {
        $token = $request->query('token');
        $lateEntry = LateEntry::where('token', $token)->first();

        if (!$lateEntry || $lateEntry->type != 'Presensi Keluar') {
            abort(404);
        }

        $user = User::where('nis', $lateEntry->nis)->first();

        return view('Main.late-departure', compact('user', 'lateEntry'));
    }

    /**
     * Menampilkan form datang di hari non-productive
     * * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showReasonForm(Request $request)
    {
        $token = $request->query('token');
        $lateEntry = LateEntry::where('token', $token)->first();

        if (!$lateEntry || $lateEntry->type != 'Presensi Masuk') {
            abort(404);
        }

        $user = User::where('nis', $lateEntry->nis)->first();
        $reasonEntry = $lateEntry;

        return view('Main.reason-coming', compact('user', 'reasonEntry'));
    }

    /**
     * Menyimpan data datang di hari non-productive
     * * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeReason(Request $request)
    {
        $validated = $request->validate([
            'alasan' => 'required',
            'token' => 'required'
        ],[
            'alasan.required' => 'Alasan datang hari ini tidak boleh kosong!',
            'token.required' => 'Token tidak valid atau sudah kadaluwarsa!'
        ]);

        $lateEntry = LateEntry::where('token', $request->token)->first();

        if (!$lateEntry) {
            return redirect()->back()->withErrors([
                'token' => 'Token tidak valid atau sudah kadaluwarsa!'
            ]);
        }
        // Cek apakah sudah ada data presensi untuk hari ini
        $presence = Presence::where('nis', $lateEntry->nis)
            ->whereDate('time_masuk', Carbon::today())
            ->first();

        if (!$presence) {
            Presence::create([
                'nis' => $lateEntry->nis,
                'time_masuk' => $lateEntry->time,
                'status' => 'Hadir',
                'status_hari' => 'Hari Non-Produktif',
                'alasan_datang' => $validated['alasan'],
            ]);

            $lateEntry->delete();
            return redirect('/');
        } else {
            $lateEntry->delete();
            return redirect()->back()->withErrors([
                'current_time' => 'Anda sudah melakukan absensi masuk hari ini...'
            ]);
        }
    }

    /**
     * Menyimpan data keterlambatan pulang
     * * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeDeparture(Request $request)
    {
        $validated = $request->validate([
            'alasan' => 'required',
            'token' => 'required'
        ],[
            'alasan.required' => 'Alasan keterlambatan pulang tidak boleh kosong!',
            'token.required' => 'Token tidak valid atau sudah kadaluwarsa!'
        ]);

        $lateEntry = LateEntry::where('token', $request->token)->first();

        if (!$lateEntry) {
            return redirect()->back()->withErrors([
                'token' => 'Token tidak valid atau sudah kadaluwarsa!'
            ]);
        }

        $presence = Presence::where('nis', $lateEntry->nis)
            ->whereDate('time_masuk', Carbon::today())
            ->whereNotNull('time_masuk')
            ->first();

        if ($presence) {
            $presence->update([
                'alasan_pulang_telat' => $validated['alasan'],
                'status_keluar' => 'Terlambat',
                'time_keluar' => $lateEntry->time,
            ]);

            $lateEntry->delete();
            return redirect('/');
        } else {
            $lateEntry->delete();
            return redirect()->back()->withErrors([
                'current_time' => 'Anda belum melakukan absensi masuk hari ini...'
            ]);
        }
    }

    /**
     * Menampilkan form pulang belum waktunya
     * * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showEarlyDepartureForm(Request $request)
    {
        $token = $request->query('token');
        $earlyEntry = LateEntry::where('token', $token)->first();

        if (!$earlyEntry || $earlyEntry->type != 'Presensi Keluar') {
            abort(404);
        }

        $user = User::where('nis', $earlyEntry->nis)->first();

        return view('Main.early-departure', compact('user', 'earlyEntry'));
    }

    /**
     * Menyimpan data pulang lebih awal
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function storeEarlyDeparture(Request $request)
    {
        $validated = $request->validate([
            'alasan' => 'required',
            'token' => 'required'
        ],[
            'alasan.required' => 'Alasan untuk pulang lebih awal tidak boleh kosong!',
            'token.required' => 'Token tidak valid atau sudah kadaluwarsa!'
        ]);

        $lateEntry = LateEntry::where('token', $request->token)->first();

        if (!$lateEntry) {
            return redirect()->back()->withErrors([
                'token' => 'Token tidak valid atau sudah kadaluwarsa!'
            ]);
        }

        $presence = Presence::where('nis', $lateEntry->nis)
            ->whereDate('time_masuk', Carbon::today())
            ->first();

        if ($presence) {
            $presence->update([
                'alasan_pulang_duluan' => $validated['alasan'],
                'status_keluar' => 'Belum Waktunya',
                'time_keluar' => $lateEntry->time,
            ]);
            $lateEntry->delete();
            return redirect('/');
        } else {
            $lateEntry->delete();
            return redirect()->back()->withErrors([
                'current_time' => 'Anda belum melakukan absensi masuk hari ini...'
            ]);
        }
    }
}