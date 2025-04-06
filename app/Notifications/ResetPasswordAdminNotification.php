<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordAdminNotification extends Notification
{
    use Queueable;

    protected $token;

    /**
     * Buat notifikasi dengan token reset password
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Tentukan bagaimana notifikasi dikirim
     */
    public function via($notifiable)
    {
        return ['mail']; // Notifikasi dikirim via email
    }

    /**
     * Buat email reset password
     */
    public function toMail($notifiable)
    {
        // URL aplikasi frontend (ganti dengan domain frontend Anda)
        $resetUrl = env('FRONTEND_URL', 'http://your-frontend.com') . '/reset-password/admin?token=' . $this->token . '&email=' . $notifiable->email;

        return (new MailMessage)
            ->subject('Reset Password')
            ->greeting('Halo ' . $notifiable->name . ',')
            ->line('Kami menerima permintaan untuk mereset password akun Anda.')
            ->action('Reset Password', $resetUrl)
            ->line('Jika Anda tidak meminta reset password, abaikan email ini.')
            ->line('Terima kasih telah menggunakan layanan kami!');
    }
}
