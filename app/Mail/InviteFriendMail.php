<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteFriendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $details;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // $from_email = "classifieds@gmail.com";
		// $from_email = "sstechjacky@gmail.com";
        return $this->subject('Classified Invitation Email')
            ->view('auth.invitefriend-email');
            // ->from($from_email);
    }
}