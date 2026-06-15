<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    $get_meta = App\Models\TblOtherpage::get_meta('register');
    $meta_title = (!empty($get_meta->meta_title) ? $get_meta->meta_title : "Register");
    $meta_keywords = (!empty($get_meta->meta_key) ? $get_meta->meta_key : "");
    $meta_description = (!empty($get_meta->meta_description) ? $get_meta->meta_description : "");
    $dir_rtl = App\Models\Setting::is_dir_rtl();
    ?>

    <title><?php echo e($meta_title); ?> </title>
    <?php if(!empty($meta_keywords)): ?>
    <meta name="keywords" content="<?php echo e($meta_keywords); ?>">
    <?php endif; ?>
    <?php if(!empty($meta_description)): ?>
    <meta name="description" content="<?php echo e($meta_description); ?>">
    <?php endif; ?>
    <?php
    $settings = App\Models\Setting::get_logos();
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css">
    <link rel="stylesheet" href="<?php echo e(URL::to('css/tailwind.min.css')); ?>">
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
        @keyframes  fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.5s ease-out forwards;
        }

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

        .floating-input:focus~.floating-label,
        .floating-input:not(:placeholder-shown)~.floating-label,
        .floating-input:valid~.floating-label {
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

        /* Intl Tel Input Custom Styles */
        .iti {
            width: 100%;
        }

        .iti .floating-input {
            padding-left: 95px !important;
        }

        .iti__flag-container {
            background-color: #f3f4f6;
            border-radius: 0.5rem 0 0 0.5rem;
        }

        .iti .floating-input:focus+.iti__flag-container {
            background-color: #fff;
        }

        .iti__country-list {
            z-index: 10000 !important;
        }

        /* Password Strength Meter from your code */
        .meter {
            height: 6px;
            border-radius: 3px;
            background: #e5e7eb;
            overflow: hidden;
        }

        .meter-bar {
            display: block;
            width: 0;
            height: 100%;
            transition: width .4s ease-in-out, background .4s ease-in-out;
        }

        .social-icon-hover:hover {
            background-color: var(--theme-orange);
            color: white;
        }

        .text-sm {
            line-height: 3.25rem !important;
        }

        .space-x-4>*+* {
            margin-left: 1rem;
            /* 1rem = 16px, Tailwind space-x-4 ka size */
        }

        .text-red-600 {
            color: red;
        }

        @media (max-width: 360px) {
            .text-sm {
                line-height: 1rem !important;
                /* jab screen ≤ 360px ho */
            }
        }

        .space-y-3>*+* {
            margin-top: 0.75rem;
            /* Tailwind scale: 3 = 0.75rem = 12px */
        }

        .phone-z-fix {
            position: relative;
            z-index: 20;
            /* Yeh isko baaqi inputs se upar le aayega */
        }

        .floating-label-group .iti {
            position: relative;
        }

        .floating-label-group .iti input.floating-input:focus~.floating-label,
        .floating-label-group .iti input.floating-input:not(:placeholder-shown)~.floating-label {
            top: 0;
            transform: translateY(-50%) scale(0.8);
            left: 2.5rem;
            color: var(--theme-green);
            background-color: white;
        }
    </style>
</head>

<body class="text-gray-800">

    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="flex w-full max-w-4xl bg-white rounded-2xl shadow-2xl">

            <div class="hidden md:flex flex-col justify-center w-1/2 p-12 bg-cover bg-center text-white" style="background-image: url('https://images.unsplash.com/photo-15329996398na7-b0038b056156?q=80&w=1974&auto=format&fit=crop&ixlib=rb-4.0.3');">
                <div class="bg-opacity-50 p-8 rounded-lg backdrop-blur-sm" style="background: #F5F5F5;">
                    <img src="<?php echo e(!empty($settings['logo']) ? asset('storage/' . $settings['logo']) : 'https://justreused.com/assets/images/logo.png'); ?>" alt="JustReused Logo" class="w-54 mb-4">
                    <h1 class="text-2xl font-bold mb-3 animate-fadeInUp" style="animation-delay: 0.2s; color:#2c5e3f;">Create Your Account</h1>
                    <p class="text-gray-200 animate-fadeInUp" style="animation-delay: 0.4s; color:#f39c12;">Join a community dedicated to sustainability and conscious consumption.</p>
                </div>
            </div>

            <div class="w-full md:w-1/2 p-8 sm:p-12">

                <div class="text-center mb-6">
                    <img src="<?php echo e(!empty($settings['logo']) ? asset('storage/' . $settings['logo']) : 'https://justreused.com/assets/images/logo.png'); ?>" alt="JustReused Logo" class="w-54 mx-auto mb-4 md:hidden">
                    <h2 class="text-2xl font-bold">Get Started</h2>
                    <p class="text-gray-500">Create an account to continue</p>
                </div>

                <?php if( Session::has( 'error' )): ?>
                <div class="text-white px-4 py-3 border-0 rounded relative mb-4 bg-red-500 alert-danger">
                    <span class="inline-block align-middle mr-8"><?php echo e(Session::get( 'error' )); ?></span>
                    <button class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-3 mr-4 outline-none focus:outline-none" onclick="this.parentElement.remove()"><span>×</span></button>
                </div>
                <?php endif; ?>

                <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'jetstream::components.validation-errors','data' => ['class' => 'mb-4 text-red-500']]); ?>
<?php $component->withName('jet-validation-errors'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['class' => 'mb-4 text-red-500']); ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>

                <form method="POST" action="<?php echo e(route('register')); ?>" id="registerForm" class="space-y-3">
                    <?php echo csrf_field(); ?>

                    <div class="animate-fadeInUp floating-label-group" style="animation-delay: 0.5s;">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400"><i class="fas fa-user"></i></span>
                        <input type="text" id="name" name="name" :value="old('name')" placeholder=" " class="floating-input w-full py-3 pl-12 pr-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-700/50 transition" required>
                        <label for="name" class="floating-label">Name</label>
                    </div>

                    <div class="animate-fadeInUp floating-label-group" style="animation-delay: 0.6s;">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400"><i class="fas fa-envelope"></i></span>
                        <input type="email" id="email" name="email" :value="old('email')" placeholder=" " class="floating-input w-full py-3 pl-12 pr-4 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-700/50 transition" required>
                        <label for="email" class="floating-label">Email</label>
                    </div>

                    <div class="animate-fadeInUp floating-label-group phone-z-fix" style="animation-delay: 0.7s;">

                        <input type="tel" id="phone" name="phone" :value="old('phone')" placeholder=" " class="floating-input w-full py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-700/50 transition" required>
                        <label for="phone" class="floating-label" style="left: 6rem;">Phone</label> <input type="hidden" name="phone_country" id="phone_country">
                    </div>

                    <div class="animate-fadeInUp floating-label-group" style="animation-delay: 0.8s;">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400"><i class="fas fa-lock"></i></span>
                            <input type="password" id="password" name="password" placeholder=" " class="password-strength floating-input w-full py-3 pl-12 pr-12 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-700/50 transition" required>
                            <label for="password" class="floating-label">Password</label>
                            <span id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 cursor-pointer hover:text-green-700"><i class="fas fa-eye-slash"></i></span>
                        </div>
                    </div>

                    <div class="animate-fadeInUp floating-label-group" style="animation-delay: 0.9s;">
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400"><i class="fas fa-check-circle"></i></span>
                            <input type="password" id="password_confirmation" name="password_confirmation" placeholder=" " class="floating-input w-full py-3 pl-12 pr-12 border rounded-lg focus:outline-none focus:ring-2 focus:ring-green-700/50 transition" required>
                            <label for="password_confirmation" class="floating-label">Confirm Password</label>
                            <span id="toggleConfirmPassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500 cursor-pointer hover:text-green-700"><i class="fas fa-eye-slash"></i></span>
                        </div>
                    </div>

                    <?php
                    $recaptcha_sitekey = env("GOOGLE_RECAPTCHA_SITEKEY");
                    ?>
                    <div class="text-left pl-4 mb-4" wire:ignore>
                        <div class="g-recaptcha" data-sitekey="<?php echo e($recaptcha_sitekey); ?>"></div>
                    </div>

                    <button type="submit" id="submit" style="background-color: var(--theme-green);" class="w-full py-3 text-lg font-bold text-white rounded-lg hover:bg-opacity-90 focus:outline-none focus:ring-4 focus:ring-green-300 transition-all transform hover:-translate-y-1 shadow-lg animate-fadeInUp" style="animation-delay: 1s;">
                        <?php echo e(__('messages.register')); ?>

                    </button>
                </form>

                <div class="mt-4 animate-fadeInUp" style="animation-delay: 1.1s;">
                    <div class="flex items-center my-4">
                        <hr class="w-full border-t border-gray-300"><span class="px-4 text-sm font-semibold text-gray-400">OR</span>
                        <hr class="w-full border-t border-gray-300">
                    </div>
                    <div class="flex justify-center space-x-4">
                        <a href="<?php echo e(URL::to('/auth/google')); ?>" class="social-icon-hover flex items-center justify-center w-12 h-12 text-xl text-gray-600 bg-gray-100 border border-gray-200 rounded-full transition transform hover:scale-110"><i class="fab fa-google"></i></a>
                        <a href="<?php echo e(URL::to('auth/facebook')); ?>" class="social-icon-hover flex items-center justify-center w-12 h-12 text-xl text-gray-600 bg-gray-100 border border-gray-200 rounded-full transition transform hover:scale-110"><i class="fab fa-facebook-f"></i></a>
                    </div>
                </div>

                <p class="mt-6 text-center text-gray-600 animate-fadeInUp" style="animation-delay: 1.2s;">
                    <?php echo e(__('messages.already registered')); ?>? <a href="<?php echo e(route('login')); ?>" class="font-bold text-green-600 hover:text-green-700 hover:underline"><?php echo e(__('messages.sign in')); ?></a>
                </p>

            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
    <script>
        // Fix floating label for intl-tel-input
        const phoneField = document.querySelector("#phone");
        const phoneLabel = document.querySelector('label[for="phone"]');

        phoneField.addEventListener("focus", () => {
            phoneLabel.style.top = "0";
            phoneLabel.style.transform = "translateY(-50%) scale(0.8)";
            phoneLabel.style.left = "2.5rem";
            phoneLabel.style.color = "var(--theme-green)";
            phoneLabel.style.backgroundColor = "white";
        });

        phoneField.addEventListener("blur", () => {
            if (!phoneField.value.trim()) {
                phoneLabel.removeAttribute("style");
            }
        });

        // --- Intl Tel Input Initialization ---
        const phoneInputField = document.querySelector("#phone");
        const phoneInput = window.intlTelInput(phoneInputField, {
            initialCountry: "au", // Default country Australia set kar di hai
            separateDialCode: true,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"
        });

        // --- Form Submission Logic (CORRECTED) ---
        const registerForm = document.querySelector("#registerForm");
        registerForm.addEventListener("submit", function(event) {
            // Check reCAPTCHA first
            const recaptchaResponse = grecaptcha.getResponse();
            if (!recaptchaResponse) {
                alert("Please verify you are not a robot.");
                event.preventDefault();
                return;
            }
            
            // Check if the phone number is valid
            if (!phoneInput.isValidNumber()) {
                // If not valid, show error and prevent form submission
                alert("Please enter a valid phone number.");
                event.preventDefault();
                console.log("Invalid phone number entered.");
                return;
            }
            
            // If valid, set the phone country value and allow form to submit normally
            const phoneNumber = phoneInput.getNumber();
            document.getElementById("phone_country").value = phoneNumber;
            console.log("Phone number is valid:", phoneNumber);
            
            // Form will submit normally since we're not calling preventDefault() when validation passes
        });

        // --- Password Visibility Toggles ---
        function setupPasswordToggle(toggleId, inputId) {
            const toggle = document.getElementById(toggleId);
            if (!toggle) return;
            const input = document.getElementById(inputId);
            const icon = toggle.querySelector('i');
            toggle.addEventListener('click', function() {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                icon.classList.toggle('fa-eye');
                icon.classList.toggle('fa-eye-slash');
            });
        }
        setupPasswordToggle('togglePassword', 'password');
        setupPasswordToggle('toggleConfirmPassword', 'password_confirmation');

        // --- Password Strength Meter (jQuery from your code) ---
        function checkStrength(password) {
            let meterBar = $("#meter-bar");
            let meterStatus = $("#meter-status");
            let strength = 0;
            if (password.length < 6) {
                meterBar.css({
                    "background": "#6B778D",
                    "width": "10%"
                });
                meterStatus.css("color", "#6B778D").text("too short");
                return;
            }
            if (password.length > 7) strength += 1;
            if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength += 1;
            if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)) strength += 1;
            if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1;

            if (strength < 2) {
                meterBar.css({
                    "background": "#ef4444",
                    "width": "25%"
                });
                meterStatus.css("color", "#ef4444").text("weak");
            } else if (strength == 2) {
                meterBar.css({
                    "background": "#f59e0b",
                    "width": "75%"
                });
                meterStatus.css("color", "#f59e0b").text("good");
            } else {
                meterBar.css({
                    "background": "#10b981",
                    "width": "100%"
                });
                meterStatus.css("color", "#10b981").text("strong");
            }
        }

        $(".password-strength").on("keyup", function() {
            checkStrength($(this).val());
        });
    </script>
    <script src="https://www.google.com/recaptcha/api.js" async defer="defer"></script>
    <script>
        document.getElementById("submit").addEventListener("click", function() {
            var $recaptcha = document.querySelector('#g-recaptcha-response');
            if ($recaptcha) {
                $recaptcha.setAttribute("required", "required");
            }
        });
        $(function() {
            function rescaleCaptcha() {
                var width = $('.g-recaptcha').parent().width();
                var scale;
                if (width < 302) {
                    scale = width / 302;
                } else {
                    scale = 1.0;
                }

                $('.g-recaptcha').css('transform', 'scale(' + scale + ')');
                $('.g-recaptcha').css('-webkit-transform', 'scale(' + scale + ')');
                $('.g-recaptcha').css('transform-origin', '0 0');
                $('.g-recaptcha').css('-webkit-transform-origin', '0 0');
            }

            rescaleCaptcha();
            $(window).resize(function() {
                rescaleCaptcha();
            });

        });
    </script>
</body>

</html><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/auth/register.blade.php ENDPATH**/ ?>