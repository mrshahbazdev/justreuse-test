<div>
    @push('styles')
        <style>
            :root { --theme-green: #2c5e3f; --theme-orange: #f39c12; }
            @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
            .animate-fadeInUp { animation: fadeInUp 0.5s ease-out forwards; }
            .floating-label-group { position: relative; margin-bottom: 1.5rem; }
            .floating-label { position: absolute; top: 50%; left: 3rem; transform: translateY(-50%); transition: all 0.2s ease-out; pointer-events: none; color: #9ca3af; background-color: transparent; padding: 0 0.25rem; }
            .floating-input:focus~.floating-label, .floating-input:not(:placeholder-shown)~.floating-label, .floating-input:valid~.floating-label { top: 0; transform: translateY(-50%) scale(0.8); left: 2.5rem; color: var(--theme-green); background-color: white; }
            .floating-input { background-color: #f3f4f6; border-color: #d1d5db; color: #111827; padding-top: 1rem; padding-bottom: 1rem; }
            .floating-input::placeholder { color: transparent; }
            .floating-input:focus { background-color: #ffffff; border-color: var(--theme-green); outline: none; }
            .social-icon { display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; text-decoration: none; border-radius: 50%; transition: all 0.3s ease; background-color: #f3f4f6; border: 1px solid #e5e7eb; color: #4b5563; }
            .social-icon:hover { background-color: var(--theme-orange); color: white; transform: scale(1.1); border-color: var(--theme-orange); }
        </style>
    @endpush

    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="flex w-full max-w-4xl  rounded-2xl shadow-2xl overflow-hidden">
            <div class="hidden md:flex flex-col justify-center w-1/2 p-12 bg-cover bg-center text-white" style=" background-image: url('https://images.unsplash.com/photo-1532999639857-b0038b056156?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3');">
                <div class=" bg-opacity-50 p-8 rounded-lg backdrop-blur-sm" style="background:#F5F5F5;">
                    <img src="{{ asset('storage/' . \App\Models\Setting::get_logos()['logo']) }}" alt="JustReused Logo" class="w-64 mb-4">
                    <h1 class="text-2xl font-bold mb-3 animate-fadeInUp" style="animation-delay: 0.2s;color:#2c5e3f;">Join the Movement</h1>
                    <p class="animate-fadeInUp" style="animation-delay: 0.4s; color:#f39c12;">Give your items a second life. Sign in to make a sustainable impact.</p>
                </div>
            </div>
            
            <div class="w-full md:w-1/2 p-8 sm:p-12">
                <form wire:submit.prevent="login" class="space-y-4">
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold">Welcome Back</h2>
                        <p class="text-gray-500">Sign in to continue your journey</p>
                    </div>

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">{{ session('status') }}</div>
                    @endif

                    <!-- Email Input -->
                    <div class="animate-fadeInUp floating-label-group" style="animation-delay: 0.6s;">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400"><i class="fas fa-envelope"></i></span>
                            <input type="email" id="email" wire:model.lazy="email" placeholder=" " class="floating-input w-full py-3 pl-12 pr-4 border rounded-lg" required>
                            <label for="email" class="floating-label">Email</label>
                        </div>
                        @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Password Input -->
                    <div class="animate-fadeInUp floating-label-group" style="animation-delay: 0.7s;">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400"><i class="fas fa-lock"></i></span>
                            <input type="password" id="password" wire:model.lazy="password" placeholder=" " class="floating-input w-full py-3 pl-12 pr-12 border rounded-lg" required>
                            <label for="password" class="floating-label">Password</label>
                        </div>
                        @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    @if (Route::has('password.request'))
                    <div class="flex items-center justify-between text-sm animate-fadeInUp" style="animation-delay: 0.8s;">
                        <label for="remember" class="flex items-center text-gray-600 cursor-pointer"><input type="checkbox" wire:model="remember" id="remember" class="h-4 w-4 text-green-600 rounded"><span class="ml-2">Remember me</span></label>
                        <a href="{{ route('password.request') }}" class="font-semibold text-green-600 hover:underline">Forgot password?</a>
                    </div>
                    @endif

                    {{-- reCAPTCHA --}}
                    <div wire:ignore class="animate-fadeInUp" style="animation-delay: 0.9s;">
                        <div class="g-recaptcha" data-sitekey="{{ env('GOOGLE_RECAPTCHA_SITEKEY') }}" data-callback="reCaptchaCallback"></div>
                        @error('recaptcha') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <button type="submit" style="background-color: var(--theme-green);" class="w-full flex justify-center items-center py-3 text-lg font-bold text-white rounded-lg hover:bg-opacity-90" wire:loading.attr="disabled" wire:target="login">
                        <span wire:loading.remove wire:target="login">Login</span>
                        <span wire:loading wire:target="login"><i class="fa fa-spinner fa-spin mr-2"></i> Signing In...</span>
                    </button>
                </form>

                <div class="mt-6 animate-fadeInUp" style="animation-delay: 1s;">
                    <div class="flex items-center my-6"><hr class="w-full"><span class="px-4 text-sm text-gray-400">OR</span><hr class="w-full"></div>
                    <div class="flex justify-center space-x-4">
                        <a href="{{ URL::to('/auth/google') }}" class="social-icon"><i class="fab fa-google"></i></a>
                        <a href="{{ URL::to('auth/facebook') }}" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>
                <p class="mt-6 text-center text-gray-600 animate-fadeInUp" style="animation-delay: 1.2s;">
                    Not registered? <a href="{{ route('register') }}" class="font-bold text-green-600 hover:underline">Create an account</a>
                </p>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <script>
            function reCaptchaCallback(response) {
                // Livewire component ko reCAPTCHA ka response bhejien
                @this.set('recaptcha', response);
            }
        </script>
    @endpush
</div>

