<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <?php
    $get_meta = App\Models\TblOtherpage::get_meta('forgot-password');
    $meta_title = (!empty($get_meta->meta_title) ? $get_meta->meta_title : "Forgot Password");
    $meta_keywords = (!empty($get_meta->meta_key) ? $get_meta->meta_key : "");
    $meta_description = (!empty($get_meta->meta_description) ? $get_meta->meta_description : "");
    ?>
    
    <title>{{ $meta_title }} </title>
    @if(!empty($meta_keywords))
        <meta name="keywords" content="{{ $meta_keywords }}">
    @endif
    @if(!empty($meta_description))
        <meta name="description" content="{{ $meta_description }}">
    @endif
    <?php
    $settings = App\Models\Setting::get_logos();
    ?>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ URL::to('css/tailwind.min.css') }}">
    <style>
        /* Custom styles for advanced look */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
        }

        /* Custom Theme Colors */
        :root {
            --theme-green: #39763a;
            --theme-orange: #f8991b;
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
        
        .space-y-6 > * + * {
            margin-top: 1.75rem;
        }
        
        /* Ensure the input field has enough padding for the label */
        .input-with-icon {
            padding-left: 3rem;
        }
    </style>
</head>
<body class="text-gray-800">

    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="flex w-full max-w-4xl bg-white rounded-2xl shadow-2xl overflow-hidden">
            
            <div class="hidden md:flex flex-col justify-center w-1/2 p-12 bg-cover bg-center text-white" style="background-image: url('https://images.unsplash.com/photo-15329996398na7-b0038b056156?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3');">
                <div class=" bg-opacity-50 p-8 rounded-lg backdrop-blur-sm" style="background:#F5F5F5;">
                    <img src="{{ !empty($settings['logo']) ? asset('storage/' . $settings['logo']) : 'https://justreused.com/assets/images/logo-white.png' }}" alt="JustReused Logo" class="w-64 mb-4">
                    <h1 class="text-2xl font-bold mb-3 animate-fadeInUp" style="animation-delay: 0.2s; color:#2c5e3f;">Forgot Password?</h1>
                    <p class="text-gray-200 animate-fadeInUp" style="animation-delay: 0.4s; color:#f39c12;">No worries. Enter your email and we'll send you a reset link.</p>
                </div>
            </div>

            <div class="w-full md:w-1/2 p-8 sm:p-12 flex flex-col justify-center">
                
                <div class="text-center mb-6">
                    <img src="{{ !empty($settings['logo']) ? asset('storage/' . $settings['logo']) : 'https://justreused.com/assets/images/logo.png' }}" alt="JustReused Logo" class="w-64 mx-auto mb-4 md:hidden">
                    <h2 class="text-2xl font-bold">Reset Your Password</h2>
                </div>

                <div class="mb-4 text-sm text-gray-600 text-center">
                    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                </div>

                @if (session('status'))
                <div class="mb-4 font-medium text-sm text-center text-green-600 bg-green-100 p-3 rounded-lg">
                    {{ session('status') }}
                </div>
                @endif
                
                <x-jet-validation-errors class="mb-4 text-red-500 text-sm" />
                
                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf
                    
                    <div class="animate-fadeInUp floating-label-group" style="animation-delay: 0.5s;">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" name="email" :value="old('email')" placeholder=" " class="floating-input input-with-icon w-full py-3 pl-12 pr-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-700/50 transition" required autofocus>
                        <label for="email" class="floating-label">Email</label>
                    </div>

                    <button type="submit" style="background-color: var(--theme-green);" class="w-full py-3 text-lg font-bold text-white rounded-lg hover:bg-opacity-90 focus:outline-none focus:ring-4 focus:ring-green-300 transition-all transform hover:-translate-y-1 shadow-lg animate-fadeInUp" style="animation-delay: 0.6s;">
                        {{ __('Email Password Reset Link') }}
                    </button>
                </form>

                 <p class="mt-8 text-center text-gray-600 animate-fadeInUp" style="animation-delay: 0.7s;">
                    <a href="{{ route('login') }}" class="font-bold text-green-600 hover:text-green-700 hover:underline"><i class="fas fa-arrow-left mr-2"></i>{{ __('messages.back to login') }}</a>
                </p>

            </div>
        </div>
    </div>
    
    <script>
        // Add a small JavaScript enhancement to ensure labels work correctly
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.getElementById('email');
            
            // Check if the input has value on page load (for browser autofill)
            if (emailInput.value !== '') {
                emailInput.classList.add('has-value');
            }
            
            // Update label when input changes
            emailInput.addEventListener('input', function() {
                if (this.value !== '') {
                    this.classList.add('has-value');
                } else {
                    this.classList.remove('has-value');
                }
            });
        });
    </script>
</body>
</html>