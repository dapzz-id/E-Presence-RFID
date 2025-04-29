<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LateEntry extends Model
{
    protected $table = 'late_entries';

    protected $guarded = ['id'];

    public function warga_tels()
    {
        return $this->belongsTo(WargaTels::class, 'nis', 'nis');
    }

    public function users()
    {
        return $this->belongsTo(User::class, 'rfid_id', 'rfid_id');
    }
}
