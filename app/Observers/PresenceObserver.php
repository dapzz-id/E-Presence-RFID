<?php

namespace App\Observers;

use App\Models\Presence;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\PresenceNotification;

class PresenceObserver
{
    /**
     * Get the greeting message based on the current time.
     *
     * @return string
     */
    protected function getGreeting(): string
    {
        $hour = now()->hour;

        if ($hour >= 5 && $hour < 11) return 'Selamat Pagi';
        elseif ($hour >= 11 && $hour < 15) return 'Selamat Siang';
        elseif ($hour >= 15 && $hour < 18) return 'Selamat Sore';
        else return 'Selamat Malam';
    }

    /**
     * Format the message for email when the student arrives.
     *
     * @param string $nama
     * @param string|null $alasan
     * @return array
     */
    protected function formatEmailMessageDatang(string $nama, string $alasan = null): array
    {
        $greeting = $this->getGreeting();
        $waktu = now()->format('d-m-Y H:i');
        $jamSekarang = now()->hour;
        $alasan = $alasan ?? '-';

        $pesanAlasan = '';
        if ($jamSekarang >= 7 && $alasan !== '-' && $alasan !== '' && $alasan !== null) {
            $pesanAlasan = "<b>Alasan telat datang:</b> {$alasan} <br><br>";
        }        

        return [
            'subject' => "Laporan Presensi Datang - {$nama}",
            'body' => <<<MSG
                Halo, {$greeting} bapak/ibu!<br>
                Berikut adalah laporan presensi harian untuk siswa/i {$nama}, bahwasanya telah berhasil melakukan presensi datang!<br><br>
                <b>Tanggal & Waktu:</b> {$waktu}<br>
                <b>Lokasi:</b> SMKS Telekomunikasi Telesandi Bekasi<br>
                {$pesanAlasan}
                Pesan ini dikirim otomatis oleh sistem presensi digital raadeveloperz.
            MSG
        ];
    }

    /**
     * Format the message for email when the student goes home.
     *
     * @param string $nama
     * @param string|null $alasan
     * @return array
     */
    protected function formatEmailMessagePulang(string $nama, string $alasan = null): array
    {
        $greeting = $this->getGreeting();
        $waktu = now()->format('d-m-Y H:i');
        $jamSekarang = now()->hour;
        $alasan = $alasan ?? '-';

        $pesanAlasan = '';
        if (now()->greaterThan(Carbon::createFromTime(16, 30)) && $alasan !== '-' && $alasan !== '' && $alasan !== null) {
            $pesanAlasan = "<b>Alasan telat pulang:</b> {$alasan} <br><br>";
        }else if (now()->lessThan(Carbon::createFromTime(16, 30)) && now()->lessThan(Carbon::createFromTime(15, 29, 59)) && $alasan !== '-' && $alasan !== '' && $alasan !== null){
            $pesanAlasan = "<b>Alasan pulang lebih awal:</b> {$alasan} <br><br>";
        }

        return [
            'subject' => "Laporan Presensi Pulang - {$nama}",
            'body' => <<<MSG
                Halo, {$greeting} bapak/ibu!
                Berikut adalah laporan presensi harian untuk siswa/i {$nama}, bahwasanya telah berhasil melakukan presensi pulang!
                
                <b>Tanggal & Waktu:</b> {$waktu}
                <b>Lokasi:</b> SMKS Telekomunikasi Telesandi Bekasi<br>
                {$pesanAlasan}
                Pesan ini dikirim otomatis oleh sistem presensi digital raadeveloperz.
            MSG
        ];
    }

    /**
     * Handle the Presence "created" event.
     */
    public function created(Presence $presence)
    {
        $siswa = $presence->warga_tels;
        $user = $presence->users;

        if ($siswa && $user->email) {
            $messageData = $this->formatEmailMessageDatang($siswa->name, $presence->alasan_datang_telat ?? null);
            
            Mail::to($user->email)
            ->send(new PresenceNotification(
                $messageData['subject'],
                $messageData['body']
            ));
        }
    }

    /**
     * Handle the Presence "updated" event.
     */
    public function updated(Presence $presence): void
    {
        $siswa = $presence->warga_tels;
        $user = $presence->users;

        if ($siswa && $user->email) {
            $messageData = $this->formatEmailMessagePulang($siswa->name, $presence->alasan_pulang_telat != null ? $presence->alasan_pulang_telat : $presence->alasan_pulang_duluan ?? null);
            
            Mail::to($user->email)
            ->send(new PresenceNotification(
                $messageData['subject'],
                $messageData['body']
            ));
        }
    }

    /**
     * Handle the Presence "deleted" event.
     */
    public function deleted(Presence $presence): void
    {
        //
    }

    /**
     * Handle the Presence "restored" event.
     */
    public function restored(Presence $presence): void
    {
        //
    }

    /**
     * Handle the Presence "force deleted" event.
     */
    public function forceDeleted(Presence $presence): void
    {
        //
    }
}