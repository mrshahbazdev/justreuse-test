<?php

namespace App\Http\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Http; // Yeh line shamil karein

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;
    public $recaptcha; // reCAPTCHA ke liye nayi property

    protected function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
            'recaptcha' => 'required', // Validation rule shamil karein
        ];
    }
    
    protected $messages = [
        'recaptcha.required' => 'Please verify you are not a robot.',
    ];

    private function validateRecaptcha($token)
    {
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('GOOGLE_RECAPTCHA_SECRETKEY'),
            'response' => $token,
        ]);

        return $response->json()['success'];
    }

    public function login()
    {
        $this->validate();
        
        if (!$this->validateRecaptcha($this->recaptcha)) {
            $this->addError('recaptcha', 'reCAPTCHA verification failed. Please try again.');
            return;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            
            $user = Auth::user();

            if (is_null($user->email_verified_at)) {
                Auth::logout();
                return redirect()->route('otp.verify', ['email' => $user->email]);
            }
            
            session()->regenerate();
            return redirect()->intended(config('fortify.home'));
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }

    public function render()
    {
        return view('livewire.auth.login')
            ->layout('layouts.auth');
    }
}

