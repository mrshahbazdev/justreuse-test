<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\TblContactUs;
use App\Models\Setting;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Mail;

class ContactUsComponent extends Component {

    use WithFileUploads;

    public $name, $email, $description, $attachment, $phone, $ad_link;

    public function render() {
        return view('livewire.contact-us.show');
    }

    protected $rules = [
        'name' => 'required',
        'email' => 'required|email',
        'description' => 'required'
    ];

    public function store() {
        // dd($this->name,$this->email,$this->description,$this->phone,$this->ad_link,$this->attachment);
        $settings = Setting::where('key', 'app')->get()[0];
        $admin_mail = json_decode($settings->value);
        $this->validate();
        $imagename = "";

        if ($this->attachment != null) {
            $imagename = $this->attachment->store('contact-us', 'public');
        }
        $adlink = ($this->ad_link == null) ? "-" : $this->ad_link;
        $phonenum = ($this->phone == null) ? "-" : $this->phone;

        $last_id = TblContactUs::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'description' => $this->description,
                    'phone' => $phonenum,
                    'ad_link' => $adlink,
                    'attachment' => $imagename
        ]);

        // Mail::send('livewire.contact-us.mail-content', array(
        //     'name' => $this->name,
        //     'email' => $this->email,
        //     'phone' => $phonenum,
        //     'description' => $this->description,
        //         ), function($message) {
        //     $settings = Setting::where('key', 'app')->get()[0];
        //     $admin_mail = json_decode($settings->value);
        //     $message->from($admin_mail->smtp_mail_username, 'Admin');
		// 	$message->to($admin_mail->email)->subject('Your Site Contect Form'); 
        // }
        // );
		
		
			$mail_data = array("send_maildata" => array('name' => $this->name, 'email' => $this->email, 'phone' => $phonenum, 'description' => $this->description, 'ad_link' => $adlink));
			$mail_key = "contact_us";
			Setting::notification_mail($mail_data, $mail_key);
		
		
        session()->flash('message', 'form submitted Successfully.');
        return redirect('contact-us');
    }

}
