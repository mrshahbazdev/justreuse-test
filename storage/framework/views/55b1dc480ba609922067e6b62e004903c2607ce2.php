<div class="flex items-center justify-center min-h-screen p-4 ">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 sm:p-12">

        
        <div x-data="{}" x-show="$wire.currentStep === 1" x-transition>
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800">Reset Your Password</h2>
                <p class="text-gray-500 mt-2">Enter your email and we'll send you an OTP to reset your password.</p>
            </div>
            <form wire:submit.prevent="sendOtp">
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" id="email" wire:model.lazy="email" class="mt-1 block w-full p-3 border rounded-md shadow-sm" style="border:1px solid #d1d5db;" required>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div wire:ignore class="mb-4">
                    <div class="g-recaptcha" data-sitekey="<?php echo e(env('GOOGLE_RECAPTCHA_SITEKEY')); ?>" data-callback="reCaptchaCallback"></div>
                    <?php $__errorArgs = ['recaptcha'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <button type="submit" style="background-color: #39763a;" class="w-full flex justify-center py-3 text-lg font-bold text-white rounded-lg" wire:loading.attr="disabled">
                    <span wire:loading.remove>Send OTP</span>
                    <span wire:loading><i class="fa fa-spinner fa-spin mr-2"></i>Sending...</span>
                </button>
            </form>
        </div>

        
        <div x-data="{}" x-show="$wire.currentStep === 2" x-transition>
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold">Verify Your Account</h2>
                <p class="text-gray-500 mt-2">An OTP has been sent to <span class="font-medium"><?php echo e($email); ?></span>.</p>
            </div>
             <form wire:submit.prevent="verifyOtp">
                <div class="mb-4">
                    <label for="otp" class="block text-sm font-medium text-gray-700 sr-only">6-Digit OTP</label>
                    <input type="text" id="otp" wire:model.lazy="otp" class="mt-1 block w-full p-3 text-center text-3xl tracking-[0.5em] border rounded-md" style="border:1px solid #d1d5db;" required>
                    <?php $__errorArgs = ['otp'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-2 block text-center"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <button type="submit" style="background-color: #39763a;" class="w-full flex justify-center py-3 text-lg font-bold text-white rounded-lg" wire:loading.attr="disabled">
                    <span wire:loading.remove>Verify</span>
                    <span wire:loading><i class="fa fa-spinner fa-spin mr-2"></i>Verifying...</span>
                </button>
            </form>
            <div class="mt-6 text-center" 
                 x-data="{ timer: 0, canResend: true, startTimer() { /* timer logic */ } }" 
                 x-init="startTimer()" 
                 @otp-resent.window="startTimer()">
                <p class="text-sm text-gray-600">
                    Didn't receive code? 
                    <button x-show="canResend" wire:click="resendOtp" class="font-bold text-green-600 hover:underline">Resend OTP</button>
                    <span x-show="!canResend" class="text-gray-400">Resend in <span x-text="timer"></span>s</span>
                </p>
            </div>
        </div>
        
        
        <div x-data="{}" x-show="$wire.currentStep === 3" x-transition>
            <div class="text-center mb-6"><h2 class="text-2xl font-bold">Set New Password</h2></div>
            <form wire:submit.prevent="resetPassword">
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" id="password" wire:model.lazy="password" class="mt-1 block w-full p-3 border rounded-md shadow-sm" style="border:1px solid #d1d5db;" required>
                </div>
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input type="password" id="password_confirmation" wire:model.lazy="password_confirmation" class="mt-1 block w-full p-3 border rounded-md shadow-sm" style="border:1px solid #d1d5db;" required>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <button type="submit" style="background-color: #39763a;" class="w-full flex justify-center py-3 text-lg font-bold text-white rounded-lg" wire:loading.attr="disabled">
                    <span wire:loading.remove>Reset Password</span>
                    <span wire:loading><i class="fa fa-spinner fa-spin mr-2"></i>Resetting...</span>
                </button>
            </form>
        </div>
    </div>
    
    <div id="toast-notification" class="fixed bottom-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg" style="display: none; opacity: 0; transform: translateY(0.5rem); transition: all 0.3s ease-out;">
        <p id="toast-message"></p>
    </div>

    <?php $__env->startPush('scripts'); ?>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        <script>
            function reCaptchaCallback(response) { window.livewire.find('<?php echo e($_instance->id); ?>').set('recaptcha', response); }
            window.addEventListener('reset-recaptcha', () => { if (typeof grecaptcha !== 'undefined') { grecaptcha.reset(); } });

            document.addEventListener('livewire:load', function () {
                const toast = document.getElementById('toast-notification');
                const toastMessage = document.getElementById('toast-message');
                let toastTimeout;
                window.addEventListener('show-toast', event => {
                    if (toastTimeout) clearTimeout(toastTimeout);
                    toastMessage.innerText = event.detail.message;
                    toast.style.display = 'block';
                    setTimeout(() => {
                        toast.style.opacity = '1';
                        toast.style.transform = 'translateY(0)';
                    }, 10);
                    toastTimeout = setTimeout(() => {
                        toast.style.opacity = '0';
                        toast.style.transform = 'translateY(0.5rem)';
                        setTimeout(() => { toast.style.display = 'none'; }, 300);
                    }, 3000);
                });
            });
        </script>
    <?php $__env->stopPush(); ?>
</div>

<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/auth/forgot-password.blade.php ENDPATH**/ ?>