<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Hari extends Model
{
    use HasFactory;

    protected $table = 'hari';
    
    protected $fillable = [
        'bulan',
        'tahun',
        'hari_produktif',
        'hari_tambahan',
        'hari_libur'
    ];
    
    protected $casts = [
        'hari_produktif' => 'array',
        'hari_tambahan' => 'array',
        'hari_libur' => 'array'
    ];
    
    /**
     * Mendapatkan data untuk bulan dan tahun tertentu
     */
    public static function getForMonthYear($bulan, $tahun)
    {
        return self::where('bulan', $bulan)
                   ->where('tahun', $tahun)
                   ->first();
    }
    
    /**
     * Memeriksa apakah tanggal tertentu adalah hari produktif
     */
    public function isHariProduktif($tanggal)
    {
        return in_array($tanggal, $this->hari_produktif ?? []);
    }
    
    /**
     * Memeriksa apakah tanggal tertentu adalah hari tambahan (non-produktif)
     */
    public function isHariTambahan($tanggal)
    {
        return in_array($tanggal, $this->hari_tambahan ?? []);
    }
    
    /**
     * Memeriksa apakah tanggal tertentu adalah hari libur
     */
    public function isHariLibur($tanggal)
    {
        return in_array($tanggal, $this->hari_libur ?? []);
    }
    
    /**
     * Mendapatkan tipe hari untuk tanggal tertentu
     */
    public function getTipeHari($tanggal)
    {
        if ($this->isHariProduktif($tanggal)) {
            return 'produktif';
        } elseif ($this->isHariTambahan($tanggal)) {
            return 'non_produktif';
        } elseif ($this->isHariLibur($tanggal)) {
            return 'libur';
        }
        
        // Default: tanggal weekday adalah produktif, weekend adalah libur
        $date = Carbon::parse($tanggal);
        return $date->isWeekday() ? 'produktif' : 'libur';
    }
    
    /**
     * Mendapatkan semua tanggal dalam bulan dengan tipe harinya
     */
    public function getAllDaysWithType()
    {
        $bulan = $this->bulan;
        $tahun = $this->tahun;
        
        $startDate = Carbon::createFromDate($tahun, $bulan, 1);
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        
        $days = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $days[] = [
                'date' => $currentDate->copy(),
                'date_str' => $dateStr,
                'day' => $currentDate->day,
                'type' => $this->getTipeHari($dateStr)
            ];
            
            $currentDate->addDay();
        }
        
        return $days;
    }
}