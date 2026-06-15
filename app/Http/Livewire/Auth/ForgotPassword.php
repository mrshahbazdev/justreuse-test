<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use App\Notifications\PasswordResetOtpNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Component;
use Illuminate\Support\Facades\Http;

class ForgotPassword extends Component
{
    public $currentStep = 1;
    public $email = '';
    public $otp = '';
    public $password = '';
    public $password_confirmation = '';
    public $recaptcha;

    public function sendOtp()
    {
        $this->validate([
            'email' => 'required|email|exists:users,email',
            'recaptcha' => 'required',
        ], [
            'email.exists' => 'No account found with this email address.',
            'recaptcha.required' => 'Please verify you are not a robot.',
        ]);

        if (!$this->validateRecaptcha($this->recaptcha)) {
            $this->addError('recaptcha', 'reCAPTCHA verification failed.');
            $this->dispatchBrowserEvent('reset-recaptcha'); // Captcha reset karein
            $this->reset('recaptcha');
            return;
        }

        $user = User::where('email', $this->email)->first();
        $otp = rand(100000, 999999);

        $user->forceFill([
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ])->save();

        try {
            $user->notify(new PasswordResetOtpNotification($otp));
            $this->currentStep = 2;
            $this->dispatchBrowserEvent('otp-sent');
        } catch (\Exception $e) {
            $this->addError('email', 'Could not send OTP email. Please try again later.');
        }

        // === YEH FIX HAI: reCAPTCHA ko reset karein ===
        $this->dispatchBrowserEvent('reset-recaptcha');
        $this->reset('recaptcha');
    }

    public function verifyOtp()
    {
        $this->validate(['otp' => 'required|numeric|digits:6']);
        $user = User::where('email', $this->email)->first();
        $attempts = session('otp_attempts_' . $this->email, 0);

        if ($attempts >= 3) {
            $this->addError('otp', 'Too many failed attempts. Please request a new OTP.');
            return;
        }

        if (!$user || $user->otp !== $this->otp || now()->gt($user->otp_expires_at)) {
            $attempts++;
            session(['otp_attempts_' . $this->email => $attempts]);
            $remaining = 3 - $attempts;
            $this->addError('otp', "Invalid or expired OTP. You have {$remaining} attempt(s) left.");
            return;
        }
        
        session()->forget('otp_attempts_' . $this->email);
        $this->currentStep = 3;
    }

    public function resendOtp()
    {
        $user = User::where('email', $this->email)->first();
        if ($user) {
            $lastResent = session('last_otp_resent_at_forgot');
            if ($lastResent && now()->diffInSeconds($lastResent) < 60) {
                $this->dispatchBrowserEvent('show-toast', ['message' => 'Please wait before requesting another OTP.']);
                return;
            }
            $otp = rand(100000, 999999);
            $user->forceFill(['otp' => $otp, 'otp_expires_at' => now()->addMinutes(10)])->save();
            $user->notify(new PasswordResetOtpNotification($otp));
            
            session(['last_otp_resent_at_forgot' => now()]);
            session()->forget('otp_attempts_' . $this->email);
            $this->dispatchBrowserEvent('otp-resent');
            $this->dispatchBrowserEvent('show-toast', ['message' => 'A new OTP has been sent.']);
        }
    }

    public function resetPassword()
    {
        $this->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::where('email', $this->email)->first();
        if ($user) {
            $user->forceFill([
                'password' => Hash::make($this->password),
                'otp' => null,
                'otp_expires_at' => null,
            ])->save();

            session()->flash('status', 'Your password has been successfully reset. You can now log in.');
            return redirect()->route('login');
        }
    }
    
    private function validateRecaptcha($token)
    {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('GOOGLE_RECAPTCHA_SECRETKEY'),
            'response' => $token,
        ]);
        return $response->json()['success'];
    }

    public function render()
    {
        return view('livewire.auth.forgot-password')->layout('layouts.auth');
    }
}

