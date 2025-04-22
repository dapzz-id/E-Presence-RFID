<?php

namespace App\Exports;

use App\Models\Presence;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Illuminate\Support\Collection;

class PresenceMultiSheetExport implements WithMultipleSheets
{
    protected $filter;
    protected $tab;
    protected $dateFrom;
    protected $dateTo;
    protected $presences;

    /**
     * Constructor untuk menyimpan parameter export
     * 
     * @param string $filter Filter waktu yang dipilih (Hari Ini, Kemarin, dll)
     * @param string $tab Tab yang dipilih (all, hadir, izin, sakit, alpa)
     * @param string|null $dateFrom Tanggal awal jika filter Custom
     * @param string|null $dateTo Tanggal akhir jika filter Custom
     * @param Collection $presences Data presensi yang akan diexport
     */
    public function __construct($filter, $tab, $dateFrom = null, $dateTo = null, $presences = null)
    {
        $this->filter = $filter;
        $this->tab = $tab;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->presences = $presences;
    }

    /**
     * Membuat multiple sheets berdasarkan status presensi
     */
    public function sheets(): array
    {
        $sheets = [];
        
        // Jika tab yang dipilih adalah 'all', buat sheet untuk setiap status
        if ($this->tab == 'all') {
            // Sheet untuk semua data
            $sheets[] = new PresenceExport($this->presences, $this->filter, 'all', $this->dateFrom, $this->dateTo);
            
            // Sheet untuk data hadir
            $hadirData = $this->presences->where('status', 'Hadir');
            if ($hadirData->count() > 0) {
                $sheets[] = new PresenceExport($hadirData, $this->filter, 'hadir', $this->dateFrom, $this->dateTo);
            }
            
            // Sheet untuk data izin
            $izinData = $this->presences->where('status', 'Izin');
            if ($izinData->count() > 0) {
                $sheets[] = new PresenceExport($izinData, $this->filter, 'izin', $this->dateFrom, $this->dateTo);
            }
            
            // Sheet untuk data sakit
            $sakitData = $this->presences->where('status', 'Sakit');
            if ($sakitData->count() > 0) {
                $sheets[] = new PresenceExport($sakitData, $this->filter, 'sakit', $this->dateFrom, $this->dateTo);
            }
            
            // Sheet untuk data alpa
            $alpaData = $this->presences->where('status', 'Alpa');
            if ($alpaData->count() > 0) {
                $sheets[] = new PresenceExport($alpaData, $this->filter, 'alpa', $this->dateFrom, $this->dateTo);
            }
            
            // Sheet untuk data terlambat
            $terlambatData = $this->presences->where('status', 'Terlambat');
            if ($terlambatData->count() > 0) {
                $sheets[] = new PresenceExport($terlambatData, $this->filter, 'terlambat', $this->dateFrom, $this->dateTo);
            }
        } else {
            // Jika tab spesifik dipilih, buat hanya satu sheet
            $sheets[] = new PresenceExport($this->presences, $this->filter, $this->tab, $this->dateFrom, $this->dateTo);
        }
        
        return $sheets;
    }
}
