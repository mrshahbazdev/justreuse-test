<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php
        $settings = \App\Models\Setting::get_logos();
    ?>

    <title><?php echo e($settings['meta_title'] ?? 'Register'); ?></title>
    
    
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css">

    <?php echo \Livewire\Livewire::styles(); ?>

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body style="background:#fffbfa;">

    <?php echo e($slot); ?>


    
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"></script>

    <?php echo \Livewire\Livewire::scripts(); ?>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        toastr.options = { "positionClass": "toast-top-right", "progressBar": true };
    </script>
    
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>

<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/layouts/auth.blade.php ENDPATH**/ ?>