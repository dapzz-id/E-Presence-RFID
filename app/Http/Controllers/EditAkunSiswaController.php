<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\WargaTel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class EditAkunSiswaController extends Controller
{
    /**
     * Menampilkan form untuk edit akun siswa
     */
    public function showForm($id)
    {
        $user = User::with('warga_tels')->findOrFail($id);
        
        return view('Main.Components.Forms.edit-akun-siswa', compact('user'));
    }
    
    /**
     * Update akun siswa
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Validasi input
        $validator = Validator::make($request->all(), [
            'username' => [
                'required',
                'string',
                'min:5',
                'max:20',
                Rule::unique('users')->ignore($user->id),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'rfid_id' => [
                'nullable',
                'string',
                Rule::unique('users')->ignore($user->id),
            ],
        ], [
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'email.required' => 'Email harus diisi',
            'email.email' => 'Format email tidak valid',
            'email.unique' => 'Email sudah digunakan',
            'rfid_id.unique' => 'RFID sudah digunakan oleh akun lain',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        try {
            DB::beginTransaction();
            
            // Update data akun
            $user->username = $request->username;
            $user->email = $request->email;
            
            // Update RFID jika ada
            if ($request->filled('rfid_id')) {
                $user->rfid_id = $request->rfid_id;
            }
            
            $user->save();
            
            DB::commit();
            
            return redirect()->route('akun.siswa')->with('success', 'Akun siswa berhasil diperbarui');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memperbarui akun: ' . $e->getMessage())->withInput();
        }
    }
    
    /**
     * Hapus RFID dari akun siswa
     */
    public function removeRfid($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->rfid_id = null;
            $user->save();
            
            return response()->json([
                'success' => true,
                'message' => 'RFID berhasil dihapus dari akun'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus RFID: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Cek status RFID
     */
    public function checkRfidStatus(Request $request)
    {
        $request->validate([
            'rfid_id' => 'required|string',
            'current_user_id' => 'required|integer'
        ]);
        
        $rfidId = $request->rfid_id;
        $currentUserId = $request->current_user_id;
        
        // Cek apakah RFID sudah digunakan oleh akun lain
        $existingUser = User::where('rfid_id', $rfidId)
            ->where('id', '!=', $currentUserId)
            ->first();
        
        if ($existingUser) {
            return response()->json([
                'success' => false,
                'message' => 'RFID ini sudah digunakan oleh akun lain',
                'user' => [
                    'name' => $existingUser->warga_tels->name ?? 'Unknown',
                    'kelas' => $existingUser->warga_tels->kelas ?? 'Unknown'
                ]
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'RFID tersedia untuk digunakan'
        ]);
    }
}