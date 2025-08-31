<?php

namespace App\Models;

use App\Notifications\ResetPasswordAdminNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword as ResetPasswordTrait;

class AdminAccount extends Authenticatable implements CanResetPassword
{
    use HasFactory, Notifiable, ResetPasswordTrait;

    protected $guard = 'admin';
    protected $guarded = ['id'];
    protected $dates = ['last_seen'];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'last_seen' => 'datetime',
            'last_membership' => 'datetime',
        ];
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordAdminNotification($token));
    }
}
