<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Notifications\OtpNotification;

class VerifyOtp extends Component
{
    public $email;
    public $otp;

    public function mount()
    {
        $this->email = request()->query('email');
        if (!$this->email) {
            return redirect()->route('register');
        }
    }

    public function verifyOtp()
    {
        $this->validate(['otp' => 'required|numeric|digits:6']);

        $user = User::where('email', $this->email)->first();

        // === YEH MUKAMMAL FIX HAI: 3 attempts ki limit ===

        // 1. Session se purani attempts hasil karein
        $attempts = session('otp_attempts_' . $this->email, 0);

        // 2. Agar 3 se zyada attempts ho chuki hain, to error dein
        if ($attempts >= 3) {
            $this->addError('otp', 'Too many failed attempts. Please request a new OTP.');
            return;
        }

        if (!$user || $user->otp !== $this->otp || now()->gt($user->otp_expires_at)) {
            // 3. Agar OTP ghalat hai, to attempt count barhayein
            $attempts++;
            session(['otp_attempts_' . $this->email => $attempts]);
            
            $remaining = 3 - $attempts;
            $this->addError('otp', "The OTP is invalid or has expired. You have {$remaining} attempt(s) left.");
            return;
        }

        // 4. Agar OTP theek hai, to attempt count reset karein
        session()->forget('otp_attempts_' . $this->email);

        $user->email_verified_at = now();
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        Auth::login($user);
        return redirect()->intended(config('fortify.home'));
    }

    public function resendOtp()
    {
        $user = User::where('email', $this->email)->first();
        if ($user) {
            $lastResent = session('last_otp_resent_at');
            if ($lastResent && now()->diffInSeconds($lastResent) < 60) {
                $this->dispatchBrowserEvent('show-toast', ['message' => 'Please wait before requesting another OTP.']);
                return;
            }

            $user->otp = rand(100000, 999999);
            $user->otp_expires_at = now()->addMinutes(10);
            $user->save();
            $user->notify(new OtpNotification($user->otp));
            
            // 5. Naya OTP bhejne par attempt count reset karein
            session()->forget('otp_attempts_' . $this->email);
            session(['last_otp_resent_at' => now()]);

            $this->dispatchBrowserEvent('otp-resent');
            $this->dispatchBrowserEvent('show-toast', ['message' => 'A new OTP has been sent to your email.']);
        }
    }

    public function render()
    {
        return view('livewire.auth.verify-otp')->layout('layouts.auth');
    }
}

