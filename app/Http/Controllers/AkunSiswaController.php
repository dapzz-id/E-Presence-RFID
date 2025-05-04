<?php

namespace App\Http\Controllers;

use App\Models\User;
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
}
