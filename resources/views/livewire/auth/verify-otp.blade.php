<div class="flex items-center justify-center min-h-screen p-4 ">
    <div class="w-full max-w-md  rounded-2xl shadow-xl p-8 sm:p-12">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Verify Your Account</h2>
            <p class="text-gray-500 mt-2">An OTP has been sent to <span class="font-medium text-gray-700">{{ $email }}</span>. Please enter it below.</p>
        </div>

        <form wire:submit.prevent="verifyOtp">
            <div class="mb-4">
                <label for="otp" class="block text-sm font-medium text-gray-700 sr-only">6-Digit OTP</label>
                
                {{-- === YEH FIX HAI: `.lazy` hata diya gaya hai === --}}
                <input type="text" id="otp" wire:model="otp" 
                       class="mt-1 block w-full p-3 text-center text-3xl tracking-[0.5em] border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500" 
                       required maxlength="6" inputmode="numeric" pattern="[0-9]*" style="border: 1px solid;">
                
                @error('otp') <span class="text-red-500 text-xs mt-2 block text-center">{{ $message }}</span> @enderror
            </div>

            <button type="submit" style="background-color: #39763a;" class="w-full flex justify-center items-center py-3 text-lg font-bold text-white rounded-lg hover:bg-opacity-90 transition" wire:loading.attr="disabled">
                <span wire:loading.remove>Verify & Continue</span>
                <span wire:loading><i class="fa fa-spinner fa-spin mr-2"></i>Verifying...</span>
            </button>
        </form>

        <div class="mt-6 text-center" 
             x-data="{
                timer: 0,
                canResend: false,
                initTimer() {
                    const endTime = localStorage.getItem('otpCooldownEndTime');
                    if (endTime && endTime > Date.now()) {
                        this.startCountdown(endTime);
                    } else {
                        this.canResend = true;
                    }
                },
                startCountdown(endTime) {
                    this.canResend = false;
                    const interval = setInterval(() => {
                        const remaining = Math.round((endTime - Date.now()) / 1000);
                        if (remaining <= 0) {
                            clearInterval(interval);
                            this.timer = 0;
                            this.canResend = true;
                            localStorage.removeItem('otpCooldownEndTime');
                        } else {
                            this.timer = remaining;
                        }
                    }, 1000);
                },
                startNewCooldown() {
                    const newEndTime = Date.now() + 60000; // 60 seconds from now
                    localStorage.setItem('otpCooldownEndTime', newEndTime);
                    this.startCountdown(newEndTime);
                }
             }" 
             x-init="initTimer()"
             @otp-resent.window="startNewCooldown()">

            <p class="text-sm text-gray-600">
                Didn't receive the code? 
                <button x-show="canResend" wire:click="resendOtp" class="font-bold text-green-600 hover:underline focus:outline-none" wire:loading.attr="disabled">
                    Resend OTP
                </button>
                <span x-show="!canResend" class="text-gray-400">
                    Resend again in <span x-text="timer" class="font-medium"></span>s
                </span>
            </p>
        </div>
    </div>
</div>

