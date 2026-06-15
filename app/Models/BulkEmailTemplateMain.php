<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mail;

class BulkEmailTemplateMain extends Model
{
    use HasFactory;
	use SoftDeletes;
	
	 protected $fillable = [
        'unique_id', 'title', 'email_code', 'notification_msg', 'mail_complete_status', 'init_send_mail','html_template', 'deleted_at'
    ];


	public static function notification_mail($mail_data)
	{

			$settings = Setting::get_logos();

			$mail_body_logo = asset('storage/' . $settings['logo']);
			$site_name = $settings['name'];
			$subject = $mail_data['send_maildata']['subject'];
            $unique_id = $mail_data['send_maildata']['unique_id'];
            $to_user_id = $mail_data['send_maildata']['to_id'];

            $get_to_user = User::where('id', $to_user_id)->first();
            $get_template = BulkEmailTemplateMain::where('unique_id', $unique_id)->first();
            $to_user_name = $get_to_user->name;
            $content = $get_template->html_template;

			$content = str_replace('[[site_logo]]', $mail_body_logo, $content);
			$content = str_replace('[[site_name]]', $site_name, $content);
			$content = str_replace('[[subject_title]]', $subject, $content);
            $content = str_replace('[[to_user_name]]', $to_user_name, $content);

			$result = Mail::send(
				'livewire.admin.bulk-email.email-template',
				array('content' => $content),
				function ($message) use ($subject, $get_to_user) {
					$settings = Setting::where('key', 'app')->get()[0];
					$admin_mail = json_decode($settings->value);

					$message->from($admin_mail->smtp_mail_username, 'Admin');
					$message->to($get_to_user->email)->subject($subject);
				}
			);

			if (Mail::failures()) {
				return false;
			}else{
				return true;
			}
			
    }

}
