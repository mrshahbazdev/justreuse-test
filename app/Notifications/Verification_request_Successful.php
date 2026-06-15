<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;


class Verification_request_Successful extends Notification
{
    use Queueable;
    
    protected $user,$value,$reason;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user,$value,$reason)
    {
        $this->user = $user;
        $this->value = $value;
        $this->reason = $reason;
        //dd($value);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        //dd('ss');
        $user = $this->user;
        $reason= $this->reason;
   $value=   $this->value;

        $mailMessage = new MailMessage();
        $mailMessage
        ->greeting( __('Hello!'))
        ->line( __('Thank you for using our application!'));
                    if($this->value == '1'){
                        $mailMessage->line( __('Your request has been accepted'));
                        $mailMessage->subject( __('confirmation verification request'));
                        return $mailMessage;
                    }
                    if($this->value == '0'){
                        $mailMessage->line( __('Your request has been Declined for' . ',' .$this->reason));
                        $mailMessage->subject( __('verification Decline'));
                        return $mailMessage;
                    }
                   
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
