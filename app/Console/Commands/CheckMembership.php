<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class CheckMembership extends Command
{
    protected $signature = 'membership:check';
    protected $description = 'Cek membership admin, kirim notif sebelum habis, dan ubah status jika expired';

    public function handle()
    {
        $today = Carbon::now();

        $admins = DB::table('admin_accounts')->where('membership', 1)->get();

        foreach ($admins as $admin) {
            $expiredAt = Carbon::parse($admin->last_membership);
            $daysLeft  = $today->diffInDays($expiredAt, false);

            // Membership sudah habis
            if ($daysLeft < 0) {
                DB::table('admin_accounts')
                    ->where('id', $admin->id)
                    ->update(['membership' => 0]);

                Mail::send('emails.membership-expired', [
                    'name' => $admin->name,
                ], function ($message) use ($admin) {
                    $message->to($admin->email)
                        ->subject('Membership Anda Telah Habis');
                });

                $this->info("Membership expired: {$admin->email}");
            }

            // Membership hampir habis (3,2,1 hari lagi)
            if (in_array($daysLeft, [1, 2, 3])) {
                Mail::send('emails.membership-warning', [
                    'name' => $admin->name,
                    'daysLeft' => $daysLeft,
                ], function ($message) use ($admin) {
                    $message->to($admin->email)
                        ->subject('Peringatan Membership Hampir Habis');
                });

                $this->info("Warning email sent to: {$admin->email}");
            }
        }

        return Command::SUCCESS;
    }
}
