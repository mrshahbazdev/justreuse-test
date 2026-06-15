<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Spatie\Permission\Models\Role;
use App\Models\User_profile;
use App\Models\Setting;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array  $input
     * @return \App\Models\User
     */
    public function create(array $input)
    {
        // $role = Role::where('id','b27d896c-f396-4670-91d5-1df724afe84c')->first(); 
        // dd(Role::find('b27d896c-f396-4670-91d5-1df724afe84c'),$input);
        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => $this->passwordRules(),
        ])->validate();

        $checkuser = User::where('email', $input['email'])->whereNull('deleted_at')->first();
        $checkmobile = User_profile::where('phone', $input['phone'])->pluck('user_id')->first();
        $get_user = "";
        if (!empty($checkmobile)) {
            $get_user = User::where('id', $checkmobile)->whereNull('deleted_at')->first();
        }

        if (empty($checkuser) && empty($get_user)) {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                // 'phone' => $input['phone'],
                'phone' =>  $input['phone_country'],
                'password' => Hash::make($input['password']),
            ]);
            //set user id to user profile table
            User_profile::create([
                'user_id' => $user->id,
                // 'phone' => $input['phone'],
                'phone' => $input['phone_country'],
            ]);

            // registration mail sent after verify email.

            // // user registeration welcome mail
            // $welcome_msg = "You have been registered successfully and Thankyou for signing up with us. We're excited to have you get started.";
            // $welcome_subject = "";
            // $mail_data = array("send_maildata" => array('to_id' => $user->id, 'message' => $welcome_msg, 'subject' => $welcome_subject, "ad_url" => URL::to('/')));
            // $mail_key = "new_registration";
            // Setting::notification_mail($mail_data, $mail_key);

            $role = Role::find('b27d896c-f396-4670-91d5-1df724afe84c'); //default - user role id
            $user->assignRole($role);
            $model_role =  DB::table('model_has_roles')
            ->where('model_id', $user->id) // Adjust this according to your model namespace
            ->update(['role_id' =>'b27d896c-f396-4670-91d5-1df724afe84c']);
            return $user;
        } else if (!empty($checkuser)) {
            Validator::make($input, [
                'name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10', 'unique:users'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules(),
            ])->validate();
        } else {
            Validator::make($input, [
                'name' => ['required', 'string', 'max:255'],
                'phone' => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10', 'unique:users'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => $this->passwordRules(),
            ])->validate();
        }
    }
}
