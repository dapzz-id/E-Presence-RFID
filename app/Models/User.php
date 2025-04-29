<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword as ResetPasswordTrait;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements CanResetPassword
{
    use HasFactory, Notifiable, ResetPasswordTrait, HasApiTokens;

    protected $guard = 'web';

    protected $guarded = ['id'];

    public function warga_tels()
    {
        return $this->belongsTo(WargaTels::class, 'nis', 'nis');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'nis', 'nis');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function lateEntry()
    {
        return $this->hasMany(LateEntry::class, 'rfid_id', 'rfid_id');
    }

    public function presence()
    {
        return $this->hasMany(Presence::class, 'nis', 'nis');
    }
}
