<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Notifications\OtpNotification;

class Register extends Component
{
    public $name = '';
    public $email = '';
    public $phone = ''; // User's raw input
    public $phone_country = ''; // Full international number from JS
    public $password = '';
    public $password_confirmation = '';
    public $recaptcha;

    protected function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_country' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'recaptcha' => ['required'],
        ];
    }

    protected $messages = [
        'recaptcha.required' => 'Please verify you are not a robot.',
        'phone_country.required' => 'A valid phone number is required.',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    private function validateRecaptcha($token)
    {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('GOOGLE_RECAPTCHA_SECRETKEY'),
            'response' => $token,
        ]);

        return $response->json()['success'];
    }

    public function register()
    {
        $this->validate();

        if (!$this->validateRecaptcha($this->recaptcha)) {
            $this->addError('recaptcha', 'reCAPTCHA verification failed. Please try again.');
            return;
        }

        $otp = rand(100000, 999999);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone_country,
            'password' => Hash::make($this->password),
            'otp' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
        ]);

        $user->assignRole('User');

        try {
            $user->notify(new OtpNotification($otp));
        } catch (\Exception $e) {
            $this->addError('email', 'Could not send verification email. Please check your mail settings.');
            $user->delete(); 
            return;
        }

        return redirect()->route('otp.verify', ['email' => $user->email]);
    }

    public function render()
    {
        return view('livewire.auth.register')
            ->layout('layouts.auth');
    }
}

