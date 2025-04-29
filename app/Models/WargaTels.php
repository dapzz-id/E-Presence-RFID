<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WargaTels extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function presence()
    {
        return $this->hasOne(Presence::class, 'nis', 'nis');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'nis', 'nis');
    }

    public function leaveDocument()
    {
        return $this->hasOne(LeaveDocument::class, 'nis', 'nis');
    }

    public function lateEntry()
    {
        return $this->hasMany(LateEntry::class, 'nis', 'nis');
    }
}
