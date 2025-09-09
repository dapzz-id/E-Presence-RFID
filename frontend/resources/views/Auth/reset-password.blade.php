@extends('layouts.app')

@section('title', 'Reset Password')

@section('content')
    <div class="text-center mb-4">
        <h2 class="fw-bold mb-2 mb-md-3">Reset Password</h2>
        <p class="text-muted px-md-4">Silakan masukkan password baru Anda</p>
    </div>

    <form action="{{ route('password.update') }}" method="POST">
        @csrf
        <input type="hidden" name="token" value="{{ request('token') }}">
        <input type="hidden" name="email" value="{{ request('email') }}">

        <div class="mb-3 mb-md-4">
            <label class="form-label">Password Baru</label>
            <input type="password" name="password" class="form-control" required autocomplete="new-password">
            <div class="form-text small mt-2">Password minimal 8 karakter</div>
        </div>

        <div class="mb-4">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
        </div>

        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-primary">Reset Password</button>
        </div>
    </form>
@endsection
