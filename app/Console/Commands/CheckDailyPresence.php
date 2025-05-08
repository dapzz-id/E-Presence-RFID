<?php

namespace App\Console\Commands;

use App\Http\Controllers\AttendanceController;
use App\Models\User;
use App\Models\Presence;
use App\Models\LeaveDocument;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckDailyPresence extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-daily-presence';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek presensi harian, set Alpa & Izin/Sakit jika sesuai ketentuan.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking daily presence...');

        $presenceController = new AttendanceController();
        $productiveDays = $presenceController->getProductiveDays(Carbon::now()->month, Carbon::now()->year)['productive'];

        $today = Carbon::now();
        $limit = Carbon::createFromTime(8, 0, 0);
        $skippingSchool = Carbon::createFromTime(15, 0, 0);

        $isProductive = in_array($today->toDateString(), $productiveDays);
        !$isProductive ? $this->info('Hari ini bukan hari produktif.') : $this->info('Hari ini adalah hari produktif.');
        $users = User::pluck('nis');

        foreach ($users as $nis) {
            $presence = Presence::where('nis', $nis)
                ->whereDate('time_masuk', $today->toDateString())
                ->first();

            if ($presence) {
                continue;
            }

            if ($today->greaterThan($skippingSchool) && $isProductive) {
                Presence::create([
                    'nis'            => $nis,
                    'time_masuk'     => $today,
                    'status'         => 'Alpa',
                    'status_hari'    => 'Hari Produktif',
                ]);

                $this->info("NIS {$nis} status Alpa (karena lewat 15:00).");

            } elseif ($today->greaterThan($limit) && $today->lessThan($skippingSchool) && $isProductive) {
                $leave = LeaveDocument::where('nis', $nis)
                    ->whereDate('start_date', '<=', $today->toDateString())
                    ->whereDate('end_date', '>=', $today->toDateString())
                    ->first();

                if ($leave) {
                    Presence::create([
                        'nis'            => $nis,
                        'time_masuk'     => Carbon::createFromTime(6, 0, 0),
                        'status'         => $leave->type,
                        'status_hari'    => 'Hari Produktif',
                    ]);

                    $this->info("NIS {$nis} status {$leave->type}.");
                }
            }
        }

        $this->info('Daily presence check completed.');
    }
}
