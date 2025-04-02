<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function forgotPassword(Request $request)
    {
        try{
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ],[
                'email.required' => 'Email tidak boleh kosong',
                'email.exists' => 'Email tidak ditemukan',
                'email.email' => 'Email tidak valid'
            ]);
        }catch (ValidationException $e){
            return response()->json([
                'status' => 'failed',
                'message' => collect($e->errors())->flatten()->first()
            ], 422);
        }

        $status = Password::broker('users')->sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['status' => 'success', 'message' => 'Link reset password telah dikirim ke email'], 200)
            : response()->json(['status' => 'failed', 'message' => 'Gagal mengirim link reset password'], 500);
    }


    // Proses reset password dari link yang dikirim ke email
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Data tidak valid'], 400);
        }

        $status = Password::broker('users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? view('Auth.successfully')
            : view('Auth.failed');
    }

    public function showResetForm(Request $request)
    {
        return view('Auth.reset-password', ['token' => $request->token, 'email' => $request->email]);
    }
}
