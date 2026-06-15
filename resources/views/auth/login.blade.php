<!DOCTYPE html>
<html lang="en">
<head>
  <?php 
	$get_meta = App\Models\TblOtherpage::get_meta('login');
	$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
	$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
	$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
	//set session - last visited url
	App\Models\Setting::set_last_visited_url();
	
	$user = isset(request()->u)?request()->u:'';
	$pass = isset(request()->p)?request()->p:'';

   $settings = App\Models\Setting::get_logos();
      ?>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $meta_title }}</title>
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="{{ URL::to('css/tailwind.min.css') }}">
     <style>
        /* Custom styles for advanced look */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5; /* A slightly different, modern light gray */
        }

        /* Custom Theme Colors */
        :root {
            --theme-green: #2c5e3f;
            --theme-orange: #f39c12;
        }

        /* Animation Keyframes */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadeInUp { animation: fadeInUp 0.5s ease-out forwards; }

        /* Floating Label Styling - FIXED */
        .floating-label-group {
            position: relative;
            margin-bottom: 1.5rem;
        }
        
        .floating-label {
            position: absolute;
            top: 50%;
            left: 3rem;
            transform: translateY(-50%);
            transition: all 0.2s ease-out;
            pointer-events: none;
            color: #9ca3af;
            background-color: transparent;
            padding: 0 0.25rem;
        }
        
        .floating-input:focus ~ .floating-label,
        .floating-input:not(:placeholder-shown) ~ .floating-label,
        .floating-input:valid ~ .floating-label {
            top: 0;
            transform: translateY(-50%) scale(0.8);
            left: 2.5rem;
            color: var(--theme-green);
            background-color: white;
        }
        
        .floating-input {
            background-color: #f3f4f6;
            border-color: #d1d5db;
            color: #111827;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }
        
        .floating-input::placeholder { 
            color: transparent; 
        }
        
        .floating-input:focus {
            background-color: #ffffff;
            border-color: var(--theme-green);
            outline: none;
        }
        
        .error-message {
            color: #ef4444;
            font-size: 0.8rem;
            height: 1rem;
        }

        /* Password Strength Meter */
        .strength-meter {
            height: 4px; background: #e5e7eb; border-radius: 4px;
            transition: all 0.3s; width: 0%;
        }
        .strength-weak { width: 25%; background: #ef4444; }
        .strength-medium { width: 50%; background: #f59e0b; }
        .strength-strong { width: 100%; background: #22c55e; }
        
        /* Social Icons Styling */
        .social-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            text-decoration: none;
            border-radius: 50%;
            transition: all 0.3s ease;
            background-color: #f3f4f6;
            border: 1px solid #e5e7eb;
            color: #4b5563;
        }
        
        .social-icon:hover {
            background-color: var(--theme-orange);
            color: white;
            transform: scale(1.1);
            border-color: var(--theme-orange);
        }
        .text-sm{
        	line-height: 3.25rem !important;
        }
        @media (max-width: 360px) {
			  .text-sm {
			    line-height: 1rem !important; /* jab screen ≤ 360px ho */
			  }
			}
        .space-x-4 > * + * {
			  margin-left: 1rem; /* 1rem = 16px, Tailwind space-x-4 ka size */
			}
			.text-red-600{
				color:red !important;
			}
    </style>
</head>
  
      @if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description))
        @section('meta_title', $meta_title)
        @section('meta_keywords', $meta_keywords)
        @section('meta_description', $meta_description)
	@endif

   <div>
      <?php
      $settings = App\Models\Setting::get_logos();
      ?>
      @if( Session::has( 'error' ))
      <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-red-500 alert-danger">
         <span class="text-xl inline-block mr-5 align-middle"><i class="fa fa-bell"></i></span>
         <span class="inline-block align-middle mr-8"><b class="capitalize"></b> {{ Session::get( 'error' )  }}</span>
         <button class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none close" onclick="closeAlert(event)"><span>×</span></button>
      </div>
      @endif

<body class="text-gray-800">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="flex w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden">
            
            <!-- Left Panel: Branding -->
            <div class="hidden md:flex flex-col justify-center w-1/2 p-12 bg-cover bg-center text-white" style=" background-image: url('https://images.unsplash.com/photo-1532999639857-b0038b056156?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3');">
                <div class=" bg-opacity-50 p-8 rounded-lg backdrop-blur-sm" style="background:#F5F5F5;">
                    <img src="/storage/{{ $settings['logo'] }}" alt="JustReused Logo" class="w-64 mb-4">
                    <h1 class="text-2xl font-bold mb-3 animate-fadeInUp" style="animation-delay: 0.2s;color:#2c5e3f;">Join the Movement</h1>
                    <p class="text-gray-200 animate-fadeInUp" style="animation-delay: 0.4s; color:#f39c12;">Give your items a second life. Sign in to make a sustainable impact.</p>
                </div>
            </div>
             @if (session('status'))
			      <div class=" mt-8 mb-8 sm:mx-14">
			         <p class="font-semibold text-sm md:text-base lg:text-lg text-green-600">{{ session('status') }}</p>
			      </div>
		      @endif

            <!-- Right Panel: Login Form -->
            <div class="w-full md:w-1/2 p-8 sm:p-12">
                <form id="loginForm" method="POST" action="{{ route('login') }}" class="space-y-4">
                		@csrf

                    <div class="text-center mb-8">
                        <img src="/storage/{{ $settings['logo'] }}" alt="JustReused Logo" class="w-64 mx-auto mb-4 md:hidden">
                        <h2 class="text-3xl font-bold">Welcome Back</h2>
                        <p class="text-gray-500">Sign in to continue your journey</p>
                    </div>
                    <x-jet-validation-errors class="mt-6" />
                    <!-- Email Input -->
                    <div class="animate-fadeInUp floating-label-group" style="animation-delay: 0.6s;">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400"><i class="fas fa-envelope"></i></span>
                            <input type="email" id="email" name="email" value="{{$user}}" placeholder=" " class="floating-input w-full py-3 pl-12 pr-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-700/50 transition" required>
                            <label for="email" class="floating-label">Email</label>
                        </div>
                        <div id="email-error" class="error-message pl-12 pt-1"></div>
                    </div>

                    <!-- Password Input -->
                    <div class="animate-fadeInUp floating-label-group" style="animation-delay: 0.7s;">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400"><i class="fas fa-lock"></i></span>
                            <input type="password" id="password" name="password" value="{{$pass}}" placeholder=" " class="floating-input w-full py-3 pl-12 pr-12 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-700/50 transition" required>
                            <label for="password" class="floating-label">Password</label>
                            <span id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 cursor-pointer hover:text-green-700"><i class="fas fa-eye-slash"></i></span>
                        </div>
                        <!-- Password Strength Meter -->
                        <div class="mt-2 pl-1">
                            <div id="strength-meter" class="strength-meter"></div>
                        </div>
                    </div>

                    <!-- Options -->
                    @if (Route::has('password.request'))
                    <div class="flex items-center justify-between text-sm animate-fadeInUp" style="animation-delay: 0.8s;">
                        <label for="remember" class="flex items-center text-gray-600 cursor-pointer"><input type="checkbox" name="remember" id="remember" class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500"><span class="ml-2">{{__('messages.remember me')}}</span></label>
                        <a href="{{ route('password.request') }}" class="font-semibold text-green-600 hover:text-green-700 hover:underline">{{ __('messages.forgot your password') }}</a>
                    </div>
                    @endif
                    <!-- Login Button -->
                    <input type="submit" style="background-color: var(--theme-green);"  value="{{__('messages.login')}}" class="w-full py-3 text-lg font-bold text-white rounded-lg hover:bg-opacity-90 focus:outline-none focus:ring-4 focus:ring-green-300 transition-all transform hover:-translate-y-1 shadow-lg animate-fadeInUp" style="animation-delay: 0.9s;">
                        
                </form>

                <!-- Separator and Socials -->
                <div class="mt-6 animate-fadeInUp" style="animation-delay: 1s;">
                    <div class="flex items-center my-6"><hr class="w-full border-t border-gray-300"><span class="px-4 text-sm font-semibold text-gray-400">OR</span><hr class="w-full border-t border-gray-300"></div>
                    <div class="flex justify-center space-x-4">
                        <a href="{{ URL::to('/auth/google') }}" class="social-icon">
                            <i class="fab fa-google"></i>
                        </a>
                        <a href="{{ URL::to('auth/facebook') }}" class="social-icon">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                    </div>
                </div>
                 <p class="mt-6 text-center text-gray-600 animate-fadeInUp" style="animation-delay: 1.2s;">
                    Not registered? <a href="{{ route('register') }}" class="font-bold text-green-600 hover:text-green-700 hover:underline">{{ __('messages.newuser') }}</a>
                </p>
                <?php
						$result =  App\Models\Setting::where('active', 1)->where('key', 'twilio_sms')->first();
						if (!empty($result)) {
						   $value = json_decode($result->value, true);
						   if ($value['enable_sms'] == 1) { ?>

							  <span class="align-bottom mx-3.5 inline-block w-12 h-12 leading-10 bg-white rounded-full hidden"><a class="flex items-center justify-center block " href="{{ URL::to('/loginwithotp') }}"><i class="fa fa-mobile fa-2x inline-block relative top-2" aria-hidden="true"></i></a></span>
						<?php }
						}
						?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const emailError = document.getElementById('email-error');

            const validateEmail = () => {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (emailInput.value !== '' && !emailRegex.test(emailInput.value)) {
                    emailError.textContent = 'Please enter a valid email.';
                    return false;
                }
                emailError.textContent = '';
                return true;
            };
            
            emailInput.addEventListener('input', validateEmail);

            // Password toggle
            const togglePassword = document.getElementById('togglePassword');
            const eyeIcon = togglePassword.querySelector('i');
            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                eyeIcon.classList.toggle('fa-eye');
                eyeIcon.classList.toggle('fa-eye-slash');
            });

            // Password strength
            const strengthMeter = document.getElementById('strength-meter');
            passwordInput.addEventListener('input', function() {
                const val = passwordInput.value;
                let strength = 0;
                if (val.length > 5) strength++; if (val.match(/[A-Z]/)) strength++;
                if (val.match(/[0-9]/)) strength++; if (val.match(/[^A-Za-z0-9]/)) strength++;
                strengthMeter.className = 'strength-meter';
                if (val.length > 0 && strength < 2) strengthMeter.classList.add('strength-weak');
                else if (strength >= 2 && strength <= 3) strengthMeter.classList.add('strength-medium');
                else if (strength > 3) strengthMeter.classList.add('strength-strong');
            });
            
            // Check if inputs have values on page load (for browser autofill)
            if (emailInput.value !== '') {
                emailInput.classList.add('has-value');
            }
            if (passwordInput.value !== '') {
                passwordInput.classList.add('has-value');
            }
        });
    </script>

</body>
</html>