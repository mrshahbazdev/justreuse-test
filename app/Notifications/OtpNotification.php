<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OtpNotification extends Notification
{
    use Queueable;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // === YEH MUKAMMAL FIX HAI ===
        // Hum ab Laravel ke default MailMessage ko istemal nahi kar rahe.
        // Hum seedha (directly) subject aur view set kar rahe hain.
        return (new MailMessage)
            ->subject('Your JustReused Verification Code')
            ->view(
                'emails.otp-email', // Sirf HTML view ka path dein
                ['otp' => $this->otp, 'user' => $notifiable] // Data view ko pass karein
            );
    }

    public function toArray($notifiable)
    {
        return [];
    }
}

