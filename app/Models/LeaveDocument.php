<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'nis',
        'type',
        'start_date',
        'end_date',
        'reason',
        'document_path',
        'status'
    ];

    public function warga_tels()
    {
        return $this->belongsTo(WargaTels::class, 'nis', 'nis');
    }
}