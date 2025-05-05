<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WargaTels;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AkunSiswaController extends Controller
{
    /**
     * Hapus multiple akun siswa yang dipilih
     */
    public function deleteMultiple(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $selectedIds = $request->input('selected_ids', []);
            
            if (empty($selectedIds)) {
                return redirect()->back()->with('error', 'Tidak ada akun yang dipilih untuk dihapus');
            }
            
            // Hapus akun yang dipilih
            // Sesuaikan dengan relasi dan model yang digunakan di aplikasi Anda
            $deletedCount = User::whereIn('id', $selectedIds)->delete();
            
            DB::commit();
            
            return redirect()->route('akun.siswa')->with('success', $deletedCount . ' akun siswa berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus akun: ' . $e->getMessage());
        }
    }

    public function deleteMultipleSiswa(Request $request)
    {
        try {
            DB::beginTransaction();
            
            $selectedIds = $request->input('selected_ids', []);
            
            if (empty($selectedIds)) {
                return redirect()->back()->with('error', 'Tidak ada akun yang dipilih untuk dihapus');
            }
            
            // Hapus akun yang dipilih
            // Sesuaikan dengan relasi dan model yang digunakan di aplikasi Anda
            $deletedCount = WargaTels::whereIn('id', $selectedIds)->delete();
            
            DB::commit();
            
            return redirect()->route('siswa')->with('success', $deletedCount . ' data siswa berhasil dihapus');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus akun: ' . $e->getMessage());
        }
    }

    public function banAccount($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->status_ban = 'inactive';
            $user->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Akun berhasil di-banned'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mem-banned akun: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Aktifkan kembali akun siswa (unban)
     */
    public function unbanAccount($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->status_ban = 'active';
            $user->save();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Akun berhasil di-unban'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal meng-unban akun: ' . $e->getMessage()
            ], 500);
        }
    }
}
