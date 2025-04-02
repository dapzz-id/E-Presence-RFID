<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function warga_tels()
    {
        return $this->belongsTo(WargaTels::class, 'nis', 'nis');
    }
}
