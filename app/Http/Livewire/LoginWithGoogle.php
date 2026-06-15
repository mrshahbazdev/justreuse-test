<?php
namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\User_profile;
use App\Notifications\OtpNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class LoginWithGoogle extends Component
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->whereNull('deleted_at')->first();

            if ($user) {
                // Agar user pehle se mojood hai
                
                // 1. Check karein ke user block to nahi
                if($user->is_blocked == 1){
                    return redirect()->route('login')->with('error','Your account has been blocked by admin! Please contact admin!');
                }

                // 2. Uska google_id update karein (agar pehle se nahi hai)
                $user->update([
                    'google_id' => $googleUser->getId(),
                ]);

            } else {
                // Agar user mojood nahi hai, to naya banayein
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => Hash::make(uniqid()), // Random password
                ]);
                
                // 3. Associated User_profile banayein
                User_profile::create([
                    'user_id' => $user->id,
                ]);

                // 4. User ko 'User' role assign karein (aapke diye gaye UUID se)
                $userRole = Role::find('b27d896c-f396-4670-91d5-1df724afe84c');
                if ($userRole) {
                    $user->assignRole($userRole);
                }
            }

            // 5. Check karein ke user ki email verified hai ya nahi
            if (is_null($user->email_verified_at)) {
                // Agar nahi, to OTP generate karein, save karein, aur email bhejien
                $otp = rand(100000, 999999);
                $user->forceFill([
                    'otp' => $otp,
                    'otp_expires_at' => now()->addMinutes(10),
                ])->save();

                $user->notify(new OtpNotification($otp));

                // User ko OTP verification page par redirect karein
                return redirect()->route('otp.verify', ['email' => $user->email]);
            }

            // 6. Agar email pehle se verified hai, to user ko login karein aur dashboard par bhej dein
            Auth::login($user);
            return redirect()->intended(config('fortify.home'));

        } catch (\Exception $e) {
            // Agar koi error aaye to login page par wapas bhej dein
            \Log::error('Google login error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Unable to login using Google. Please try again.');
        }
    }
}

