<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    public function __construct()
    {
        //
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $verificationUrl = $this->verificationUrl($notifiable);

        // === YEH FIX HAI: HTML aur Plain-text templates ko aek saath array mein bhejein ===
        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->view(
                ['emails.verify-email', 'emails.verify-email-plain'], // HTML aur text, dono views
                ['url' => $verificationUrl, 'user' => $notifiable]   // Data dono views ko jayega
            );
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    public function toArray($notifiable)
    {
        return [];
    }
}

