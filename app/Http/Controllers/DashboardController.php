<?php

namespace App\Http\Controllers;

use App\Models\Hari;
use App\Models\Presence;
use App\Models\User;
use App\Models\LeaveDocument;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $user->update(['last_seen' => Carbon::now()]);
        }

        $filter = $request->query('filter', 'Hari ini');
        $tab = $request->query('tab', 'all');
        $headTab = $request->query('head-tabs', $this->getDefaultHeadTab());
        
        // Determine date range
        $dateRange = $this->getDateRange($filter, $request);
        $dateFrom = $dateRange['dateFrom'];
        $dateTo = $dateRange['dateTo'];
        
        // Get day type for the filtered date (use first date for display purposes)
        $dayType = $this->determineDayType($dateFrom);
        $todayDayType = $this->determineDayType(Carbon::today());
        
        // Main query
        $presencesQuery = Presence::whereBetween('time_masuk', [$dateFrom, $dateTo])
            ->with('warga_tels');
        
        // Apply head-tab filter (produktif/non-produktif)
        if ($headTab === 'produktif') {
            $presencesQuery->where('status_hari', 'Hari Produktif');
        } elseif ($headTab === 'non_produktif') {
            $presencesQuery->where('status_hari', 'Hari Non-Produktif');
        }
        
        // Apply status filter
        if (in_array($tab, ['hadir', 'izin', 'sakit'])) {
            if ($tab === 'hadir') {
                $presencesQuery->where(function($query) {
                    $query->where('status', 'Hadir')
                          ->orWhere('status', 'Terlambat');
                });
            } else {
                $presencesQuery->where('status', ucfirst($tab));
            }
        }        
        
        // Get data
        $presences = $presencesQuery->get();
        $dataPresensi = $presencesQuery->paginate(10)->withQueryString();
        
        // Leave documents
        $leaveDocuments = $this->getLeaveDocuments($dateFrom, $dateTo, $tab);
        
        // Counts
        $total = (string)User::count();
        $totalHariIni = (string)$presences->count();
        $totalTidakHadir = (string)$presences->whereIn('status', ['Izin', 'Sakit'])->count();
        
        // Monthly stats
        $monthlyStats = $this->getMonthlyStats();
        
        // Get current month's schedule
        $jadwalBulanIni = Hari::getForMonthYear(Carbon::now()->month, Carbon::now()->year);
        $totalMasukHariNonProduktif = $this->getNonProductiveDaysCount($jadwalBulanIni);
        
        return view('Main.dashboard', compact(
            'total',
            'totalHariIni',
            'totalTidakHadir',
            'dataPresensi',
            'filter',
            'tab',
            'headTab',
            'dayType',
            'todayDayType',
            'leaveDocuments',
            'monthlyStats',
            'totalMasukHariNonProduktif',
            'dateFrom',
            'dateTo'
        ));
    }

    protected function getDateRange(string $filter, Request $request): array
    {
        switch ($filter) {
            case 'Kemarin':
                $dateFrom = Carbon::yesterday();
                $dateTo = Carbon::yesterday()->endOfDay();
                break;
            case 'Minggu Lalu':
                $dateFrom = Carbon::now()->subWeek()->startOfWeek();
                $dateTo = Carbon::now()->subWeek()->endOfWeek();
                break;
            case 'Minggu Ini':
                $dateFrom = Carbon::now()->startOfWeek();
                $dateTo = Carbon::now()->endOfWeek();
                break;
            case 'Bulan Lalu':
                $dateFrom = Carbon::now()->subMonth()->startOfMonth();
                $dateTo = Carbon::now()->subMonth()->endOfMonth();
                break;
            case 'Bulan Ini':
                $dateFrom = Carbon::now()->startOfMonth();
                $dateTo = Carbon::now()->endOfMonth();
                break;
            case 'Tahun Lalu':
                $dateFrom = Carbon::now()->subYear()->startOfYear();
                $dateTo = Carbon::now()->subYear()->endOfYear();
                break;
            case 'Tahun Ini':
                $dateFrom = Carbon::now()->startOfYear();
                $dateTo = Carbon::now()->endOfDay();
                break;
            case 'Custom':
                $dateFrom = Carbon::parse($request->query('date_from'))->startOfDay();
                $dateTo = Carbon::parse($request->query('date_to'))->endOfDay();
                break;
            default: // Hari ini
                $dateFrom = Carbon::today();
                $dateTo = Carbon::today()->endOfDay();
        }
        
        return compact('dateFrom', 'dateTo');
    }

    protected function determineDayType(Carbon $date): string
    {
        $dateString = $date->toDateString();
        $hari = Hari::whereJsonContains('hari_produktif', $dateString)
                ->orWhereJsonContains('hari_tambahan', $dateString)
                ->orWhereJsonContains('hari_libur', $dateString)
                ->first();

        if (!$hari) {
            return 'Hari Produktif';
        }

        if (in_array($dateString, $hari->hari_produktif ?? [])) {
            return 'Hari Produktif';
        }

        if (in_array($dateString, $hari->hari_tambahan ?? [])) {
            return 'Hari Non-Produktif';
        }

        if (in_array($dateString, $hari->hari_libur ?? [])) {
            return 'Hari Libur';
        }

        return 'Hari Produktif';
    }

    protected function getDefaultHeadTab(): string
    {
        $dayType = $this->determineDayType(Carbon::today());
        return $dayType === 'Hari Non-Produktif' ? 'non_produktif' : 'produktif';
    }

    protected function getLeaveDocuments(Carbon $dateFrom, Carbon $dateTo, string $tab)
    {
        return DB::table('leave_documents')
            ->join('warga_tels', 'leave_documents.nis', '=', 'warga_tels.nis')
            ->where(function($query) use ($dateFrom, $dateTo) {
                $query->whereBetween('start_date', [$dateFrom->toDateString(), $dateTo->toDateString()])
                      ->orWhereBetween('end_date', [$dateFrom->toDateString(), $dateTo->toDateString()])
                      ->orWhere(function($q) use ($dateFrom, $dateTo) {
                          $q->where('start_date', '<=', $dateFrom->toDateString())
                            ->where('end_date', '>=', $dateTo->toDateString());
                      });
            })
            ->when($tab === 'izin', fn($q) => $q->where('type', 'Izin'))
            ->when($tab === 'sakit', fn($q) => $q->where('type', 'Sakit'))
            ->select('leave_documents.*', 'warga_tels.name', 'warga_tels.kelas')
            ->get();
    }

    protected function getMonthlyStats(): array
    {
        $currentMonthStart = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();
        
        return [
            'totalHadir' => (string)Presence::whereBetween('time_masuk', [$currentMonthStart, $currentMonthEnd])
                ->where('status', 'Hadir')
                ->count(),
            'totalIzinSakit' => (string)Presence::whereBetween('time_masuk', [$currentMonthStart, $currentMonthEnd])
                ->whereIn('status', ['Izin', 'Sakit'])
                ->count()
        ];
    }

    protected function getNonProductiveDaysCount($jadwalBulanIni): string
    {
        if (!$jadwalBulanIni || empty($jadwalBulanIni->hari_tambahan)) {
            return '0';
        }
        
        return (string)Presence::whereIn(DB::raw('DATE(time_masuk)'), $jadwalBulanIni->hari_tambahan)
            ->count();
    }
}