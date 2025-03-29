<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guard = 'web';

    protected $guarded = ['id'];

    public function warga_tels()
    {
        return $this->hasOne(WargaTels::class, 'nis', 'nis');
    }

    public function presence()
    {
        return $this->hasOne(Presence::class, 'nis', 'nis');
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
}
