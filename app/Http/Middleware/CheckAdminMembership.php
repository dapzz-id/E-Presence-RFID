<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminMembership
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('admin')->user();

        if (!$user) {
            return redirect()->route('login')->withErrors(['login' => 'Silakan login terlebih dahulu']);
        }

        if ($user->membership == 0) {
            Auth::guard('admin')->logout();
            return redirect()->route('login')->withErrors(['login' => 'Akun Anda belum berlangganan pada layanan ini']);
        }

        return $next($request);
    }
}
