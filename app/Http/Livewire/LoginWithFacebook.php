<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Spatie\Permission\Models\Role;
use App\Models\User_profile;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LoginWithFacebook extends Component {

    public function render() {
        
    }

    //redirect to facebook login
    public function redirectToFacebook() {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback($provider = 'facebook') {

        try {


        $user = Socialite::driver($provider)->user();
            // dd($user);
        if ($user->email == null) {
            return redirect()->route('login')->with('error','Please update Email address to your facebook account, otherwise you cannot login..' );
        } else {

            $finduser = User::where('email', $user->email)->whereNull('deleted_at')->first();
			
			if (!empty($finduser)) {
				
				if($finduser->is_blocked==1){
				
				return redirect()->route('login')->with('error','Your account has been blocked by admin!, please contact admin!' );
				}
			}


            if ($finduser) {

                $update_id = User::where('email', $user->email)->update([
                    'facebook_id' => $user->id,
                ]);

                Auth::login($finduser);
            
                return redirect()->intended('/');
            } else {
                
                $newUser = User::create([
                    'name' => $user->name,
                    'email' => $user->email,
                    'facebook_id' => $user->id,
                    'password' => encrypt('123456789')
                ]);
                //set user id to user profile table
                User_profile::create([
                    'user_id' => $newUser->id,
                ]);

                $role = Role::find('b27d896c-f396-4670-91d5-1df724afe84c'); //default - user role id
                $newUser->assignRole([$role]);


                Auth::login($newUser);

                return redirect()->intended('/');

            }



        }




        } catch (Exception $e) {
           \Session::put('payment_nofy','Please update Email address to your facebook account, otherwise you cannot login..');
           return redirect()->intended('/');
        }
    }

}
