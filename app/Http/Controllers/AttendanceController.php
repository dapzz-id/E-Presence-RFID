<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display the attendance data.
     */
    public function index(Request $request)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        $month = $request->input('bulan', $currentMonth);
        $year = $request->input('tahun', $currentYear);
        $class = $request->input('kelas', 'all');
        $search = $request->input('search', '');
        
        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        
        // Get years for dropdown (last 5 years)
        $years = range($currentYear - 4, $currentYear);
        
        // Get productive days from hari table
        $productiveDays = $this->getProductiveDays($month, $year);
        $totalProductiveDays = count($this->getProductiveDays($month, $year)['productive']);
        
        $attendanceData = $this->getAttendanceData($month, $year, $class, $search);
        $chartData = $this->getChartData($attendanceData);
        
        return view('Main.Data.ChartDataSiswa', compact(
            'attendanceData', 
            'chartData', 
            'months', 
            'years', 
            'currentMonth',
            'totalProductiveDays',
            'currentYear',
            'productiveDays'
        ));
    }
    
    /**
     * Get productive days from hari table.
     */
    private function getProductiveDays($month, $year)
    {
        $hari = DB::table('hari')
            ->where('bulan', $month)
            ->where('tahun', $year)
            ->first();
            
        if (!$hari) {
            return [
                'productive' => [],
                'non_productive' => []
            ];
        }
        
        return [
            'productive' => json_decode($hari->hari_produktif, true) ?? [],
            'non_productive' => json_decode($hari->hari_tambahan, true) ?? []
        ];
    }
    
    /**
     * Get attendance data.
     */
    private function getAttendanceData($month, $year, $class, $search)
    {
        $query = DB::table('presences')
            ->join('users', 'presences.nis', '=', 'users.nis')
            ->join('warga_tels', 'users.nis', '=', 'warga_tels.nis')
            ->select(
                'users.id',
                'warga_tels.name as name',
                'warga_tels.kelas as class',
                DB::raw('COUNT(DISTINCT CASE WHEN presences.status_hari = "Hari Produktif" THEN DATE(presences.time_masuk) END) as productive_days'),
                DB::raw('COUNT(DISTINCT CASE WHEN presences.status_hari = "Hari Non-Produktif" THEN DATE(presences.time_masuk) END) as non_productive_days')
            )
            ->whereMonth('presences.time_masuk', $month)
            ->whereYear('presences.time_masuk', $year)
            ->whereIn('presences.status', ['Hadir', 'Terlambat'])
            ->groupBy('users.id', 'warga_tels.name', 'warga_tels.kelas')
            ->orderBy('presences.nis', 'asc');
            
        if ($class !== 'all') {
            $query->where('warga_tels.kelas', $class);
        }
        
        if (!empty($search)) {
            $query->where('warga_tels.name', 'like', "%{$search}%");
        }
        
        $results = $query->paginate(10);
        
        // Get previous month data for comparison
        $previousMonth = $month - 1;
        $previousYear = $year;
        
        if ($previousMonth < 1) {
            $previousMonth = 12;
            $previousYear--;
        }
        
        $previousData = DB::table('presences')
            ->join('users', 'presences.nis', '=', 'users.nis')
            ->select(
                'users.id',
                DB::raw('COUNT(DISTINCT CASE WHEN presences.status_hari = "Hari Produktif" THEN DATE(presences.time_masuk) END) as productive_days')
            )
            ->whereMonth('presences.time_masuk', $previousMonth)
            ->whereYear('presences.time_masuk', $previousYear)
            ->whereIn('presences.status', ['Hadir', 'Terlambat'])
            ->groupBy('users.id')
            ->pluck('productive_days', 'users.id')
            ->toArray();
            
        // Calculate total productive days in the month
        $totalProductiveDays = count($this->getProductiveDays($month, $year)['productive']);
        
        // Process the results to add comparison and percentage
        foreach ($results as $result) {
            $previousCount = $previousData[$result->id] ?? 0;
            $result->comparison = $result->productive_days - $previousCount;
            $result->percentage = $totalProductiveDays > 0 
                ? round(($result->productive_days / $totalProductiveDays) * 100, 1) 
                : 0;
        }
        
        return $results;
    }
    
    /**
     * Get chart data.
     */
    private function getChartData($attendanceData)
    {
        $labels = [];
        $productiveDays = [];
        $nonProductiveDays = [];
        
        foreach ($attendanceData as $student) {
            $labels[] = $student->name;
            $productiveDays[] = $student->productive_days;
            $nonProductiveDays[] = $student->non_productive_days;
        }
        
        return [
            'labels' => $labels,
            'productiveDays' => $productiveDays,
            'nonProductiveDays' => $nonProductiveDays
        ];
    }
}
