<?php

namespace App\Http\Controllers;

use App\Models\Hari;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HariController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        
        $jadwalHari = Hari::getForMonthYear($bulan, $tahun);
        
        // Jika belum ada data untuk bulan dan tahun ini, buat array kosong
        if (!$jadwalHari) {
            $jadwalHari = new Hari([
                'bulan' => $bulan,
                'tahun' => $tahun,
                'hari_produktif' => [],
                'hari_tambahan' => [],
                'hari_libur' => []
            ]);
        }
        
        // Dapatkan semua tanggal dalam bulan dengan tipe harinya
        $dates = $this->generateDatesForMonth($bulan, $tahun, $jadwalHari);
        
        return view('hari.index', compact('jadwalHari', 'dates', 'bulan', 'tahun'));
    }
    
    public function monthForm(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->month);
        $tahun = $request->input('tahun', Carbon::now()->year);
        
        $jadwalHari = Hari::getForMonthYear($bulan, $tahun);
        
        // Jika belum ada data untuk bulan dan tahun ini, buat array kosong
        if (!$jadwalHari) {
            $jadwalHari = new Hari([
                'bulan' => $bulan,
                'tahun' => $tahun,
                'hari_produktif' => [],
                'hari_tambahan' => [],
                'hari_libur' => []
            ]);
        }
        
        // Dapatkan semua tanggal dalam bulan
        $dates = $this->generateDatesForMonth($bulan, $tahun, $jadwalHari);
        
        return view('hari.month-form', compact('jadwalHari', 'dates', 'bulan', 'tahun'));
    }
    
    public function saveMonth(Request $request)
    {
        $bulan = $request->input('bulan');
        $tahun = $request->input('tahun');
        $types = $request->input('types', []);
        
        // Kelompokkan tanggal berdasarkan tipe
        $hariProduktif = [];
        $hariTambahan = [];
        $hariLibur = [];
        
        foreach ($types as $date => $type) {
            if ($type === 'produktif') {
                $hariProduktif[] = $date;
            } elseif ($type === 'non_produktif') {
                $hariTambahan[] = $date;
            } elseif ($type === 'libur') {
                $hariLibur[] = $date;
            }
        }
        
        // Simpan atau update data
        Hari::updateOrCreate(
            ['bulan' => $bulan, 'tahun' => $tahun],
            [
                'hari_produktif' => $hariProduktif,
                'hari_tambahan' => $hariTambahan,
                'hari_libur' => $hariLibur
            ]
        );
        
        return redirect()->route('hari.index', ['bulan' => $bulan, 'tahun' => $tahun])
            ->with('success', 'Jadwal hari berhasil disimpan.');
    }
    
    /**
     * Generate array tanggal untuk bulan tertentu
     */
    private function generateDatesForMonth($bulan, $tahun, $jadwalHari = null)
    {
        $startDate = Carbon::createFromDate($tahun, $bulan, 1);
        $endDate = Carbon::createFromDate($tahun, $bulan, 1)->endOfMonth();
        
        $dates = [];
        $currentDate = $startDate->copy();
        
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $type = null;
            
            if ($jadwalHari) {
                if ($jadwalHari->isHariProduktif($dateStr)) {
                    $type = 'produktif';
                } elseif ($jadwalHari->isHariTambahan($dateStr)) {
                    $type = 'non_produktif';
                } elseif ($jadwalHari->isHariLibur($dateStr)) {
                    $type = 'libur';
                }
            }
            
            $dates[] = [
                'date' => $currentDate->copy(),
                'date_str' => $dateStr,
                'day' => $currentDate->day,
                'type' => $type
            ];
            
            $currentDate->addDay();
        }
        
        return $dates;
    }
}