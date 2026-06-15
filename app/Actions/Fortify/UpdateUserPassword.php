<?php

namespace App\Actions\Fortify;

use App\Models\Setting;
use App\Models\TblPost;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  mixed  $user
     * @param  array  $input
     * @return void
     */
    public function update($user, array $input)
    {
        // dd($user->id);

        Validator::make($input, [
            'current_password' => ['required', 'string'],
            'password' => $this->passwordRules(),
        ])->after(function ($validator) use ($user, $input) {
            if (! Hash::check($input['current_password'], $user->password)) {
                $validator->errors()->add('current_password', __('The provided password does not match your current password.'));
            }
        })->validateWithBag('updatePassword');

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();

        
        // sent notification
        $settings = Setting::get_logos();
        $site_name = $settings['name'];

        $get_user_info = User::where('id', $user->id)->first();
        $slug = url('/');
        $get_admin = User::role('superadmin')->get();
        $admin_id = $get_admin[0]->id;

        $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
        $message = array("notifydata" => array('to_id' => $user->id, 'from_id' => $admin_id, 'message' => "Your account password has been updated.", 'notify_from' => 'update_password', 'notify_title' => "Password Updated In ".$site_name."!..", 'post_id' => "", 'slug' => $slug));

        TblPost::send_push_notification($fcmid, $message);
    }
}
