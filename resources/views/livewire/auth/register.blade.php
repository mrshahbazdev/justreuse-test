<div>
    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css">
        <style>
            :root { --theme-green: #39763a; --theme-orange: #f8991b; }
            @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
            .animate-fadeInUp { animation: fadeInUp 0.5s ease-out forwards; }
            .floating-label-group { position: relative; margin-bottom: 1.5rem; }
            .floating-label { position: absolute; top: 50%; left: 3rem; transform: translateY(-50%); transition: all 0.2s ease-out; pointer-events: none; color: #9ca3af; background-color: transparent; padding: 0 0.25rem; }
            .floating-input:focus~.floating-label, .floating-input:not(:placeholder-shown)~.floating-label, .floating-input:valid~.floating-label { top: 0; transform: translateY(-50%) scale(0.8); left: 2.5rem; color: var(--theme-green); background-color: white; }
            .floating-input { background-color: #f3f4f6; border-color: #d1d5db; color: #111827; padding-top: 1rem; padding-bottom: 1rem; }
            .floating-input::placeholder { color: transparent; }
            .floating-input:focus { background-color: #ffffff; border-color: var(--theme-green); outline: none; }
            
            /* === PHONE INPUT FINAL FIX === */
            .iti { width: 100%; display: block !important; }
            .iti__flag-container { z-index: 2; }
            .iti input[type="tel"] { position: relative; z-index: 1; padding-left: 100px !important; /* Padding theek kar di hai */ }
            .phone-input-wrapper { position: relative; }
            .phone-input-wrapper .floating-label { left: 100px; z-index: 3; } /* Label ki position theek kar di hai */
            
            .phone-input-wrapper.active .floating-label,
            .phone-input-wrapper .floating-input:focus~.floating-label {
                top: 0;
                transform: translateY(-50%) scale(0.8);
                left: 2.5rem !important;
                color: var(--theme-green);
                background-color: white;
            }

            .social-icon-hover:hover { background-color: var(--theme-orange); color: white; }
        </style>
    @endpush

    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="flex w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden">
            <div class="hidden md:flex flex-col justify-center w-1/2 p-12 bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-15329996398na7-b0038b056156?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3');">
                <div class="p-8 rounded-lg backdrop-blur-sm" style="background: rgba(245, 245, 245, 0.9);">
                    <img src="{{ asset('storage/' . \App\Models\Setting::get_logos()['logo']) }}" alt="Logo" class="w-54 mb-4">
                    <h1 class="text-2xl font-bold mb-3 animate-fadeInUp" style="animation-delay: 0.2s; color:#2c5e3f;">Create Your Account</h1>
                    <p class="text-gray-600 animate-fadeInUp" style="animation-delay: 0.4s;">Join a community dedicated to sustainability.</p>
                </div>
            </div>

            <div class="w-full md:w-1/2 p-8 sm:p-12">
                <div class="text-center mb-6">
                    <h2 class="text-2xl font-bold">Get Started</h2>
                    <p class="text-gray-500">Create an account to continue</p>
                </div>

                <form wire:submit.prevent="register">
                    {{-- Form fields... --}}
                    <div class="floating-label-group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400"><i class="fas fa-user"></i></span>
                        <input type="text" id="name" wire:model.lazy="name" placeholder=" " class="floating-input w-full py-3 pl-12 pr-4 border rounded-lg" required>
                        <label for="name" class="floating-label">Name</label>
                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="floating-label-group">
                         <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" wire:model.lazy="email" placeholder=" " class="floating-input w-full py-3 pl-12 pr-4 border rounded-lg" required>
                        <label for="email" class="floating-label">Email</label>
                        @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <div wire:ignore class="floating-label-group">
                            <div class="phone-input-wrapper">
                                <input type="tel" id="phone" placeholder=" " class="floating-input w-full py-3 border rounded-lg" required>
                                <label for="phone" class="floating-label">Phone</label>
                            </div>
                        </div>
                        @error('phone_country') <span class="text-red-500 text-xs -mt-4 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="floating-label-group">
                        <div class="relative">
                             <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400"><i class="fas fa-lock"></i></span>
                            <input type="password" id="password" wire:model.lazy="password" placeholder=" " class="floating-input w-full py-3 pl-12 pr-12 border rounded-lg" required>
                            <label for="password" class="floating-label">Password</label>
                             <span id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 cursor-pointer"><i class="fas fa-eye-slash"></i></span>
                        </div>
                        @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <div class="floating-label-group">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400"><i class="fas fa-check-circle"></i></span>
                            <input type="password" id="password_confirmation" wire:model.lazy="password_confirmation" placeholder=" " class="floating-input w-full py-3 pl-12 pr-12 border rounded-lg" required>
                            <label for="password_confirmation" class="floating-label">Confirm Password</label>
                        </div>
                    </div>
                     <div wire:ignore class="mb-4">
                        <div class="g-recaptcha" data-sitekey="{{ env('GOOGLE_RECAPTCHA_SITEKEY') }}" data-callback="reCaptchaCallback"></div>
                        @error('recaptcha') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    <button type="submit" style="background-color: var(--theme-green);" class="w-full flex justify-center items-center py-3 text-lg font-bold text-white rounded-lg hover:bg-opacity-90" wire:loading.attr="disabled" wire:target="register">
                        <span wire:loading.remove wire:target="register">Register</span>
                        <span wire:loading wire:target="register"><i class="fa fa-spinner fa-spin mr-2"></i> Processing...</span>
                    </button>
                </form>
                
                <div class="mt-4">
                    <div class="flex items-center my-4"><hr class="w-full"><span class="px-4 text-sm text-gray-400">OR</span><hr class="w-full"></div>
                    <div class="flex justify-center space-x-4">
                        <a href="{{ URL::to('/auth/google') }}" class="social-icon-hover flex items-center justify-center w-12 h-12 text-xl text-gray-600 bg-gray-100 border rounded-full"><i class="fab fa-google"></i></a>
                        <a href="{{ URL::to('auth/facebook') }}" class="social-icon-hover flex items-center justify-center w-12 h-12 text-xl text-gray-600 bg-gray-100 border rounded-full"><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>
                <p class="mt-6 text-center text-sm text-gray-600">Already registered? <a href="{{ route('login') }}" class="font-bold text-green-600 hover:underline">Sign In</a></p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <script>
            function reCaptchaCallback(response) { @this.set('recaptcha', response); }
            document.addEventListener('DOMContentLoaded', function () {
                const phoneInputField = document.querySelector("#phone");
                if (!phoneInputField) return;
                const phoneInputWrapper = phoneInputField.closest('.phone-input-wrapper');

                const phoneInput = window.intlTelInput(phoneInputField, {
                    initialCountry: "auto",
                    geoIpLookup: cb => { fetch("https://ipapi.co/json").then(res => res.json()).then(data => cb(data.country_code)).catch(() => cb("us")); },
                    separateDialCode: true,
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"
                });

                function handlePhoneInput() {
                    if (phoneInput.isValidNumber()) { @this.set('phone_country', phoneInput.getNumber()); } else { @this.set('phone_country', ''); }
                    if (phoneInputField.value.trim() !== '') {
                        phoneInputWrapper.classList.add('active');
                    } else {
                        phoneInputWrapper.classList.remove('active');
                    }
                }
                phoneInputField.addEventListener('blur', handlePhoneInput);
                phoneInputField.addEventListener('keyup', handlePhoneInput);
                phoneInputField.addEventListener('countrychange', handlePhoneInput);
                phoneInputField.addEventListener('focus', () => phoneInputWrapper.classList.add('active'));
                
                function setupPasswordToggle(toggleId, inputId) {
                    const toggle = document.getElementById(toggleId);
                    if (!toggle) return;
                    const input = document.getElementById(inputId);
                    toggle.addEventListener('click', () => {
                        const type = input.type === 'password' ? 'text' : 'password';
                        input.type = type;
                        toggle.querySelector('i').classList.toggle('fa-eye-slash');
                        toggle.querySelector('i').classList.toggle('fa-eye');
                    });
                }
                setupPasswordToggle('togglePassword', 'password');
            });
        </script>
    @endpush
</div>

