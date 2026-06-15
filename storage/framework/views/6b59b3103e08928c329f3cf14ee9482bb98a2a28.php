<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <meta name="turbolinks-cache-control" content="no-preview">
        <title><?php echo e(config('app.name', 'Laravel')); ?></title>

        <!-- Fonts -->
        <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

        <!-- Styles -->
        <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">

        <link rel="stylesheet" href="<?php echo e(URL::to('font-awesome-4.7.0/css/font-awesome.min.css')); ?>">

        <!-- <script src="<?php echo e(URL::to('js/toastr.js')); ?>"></script>
        <link rel="stylesheet" href="<?php echo e(URL::to('css/toastr.css')); ?>"> -->
        

        <?php echo \Livewire\Livewire::styles(); ?>

        
        <!-- Scripts -->
        <script src="<?php echo e(asset('js/app.js')); ?>" defer></script>
    
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('navigation-dropdown')->html();
} elseif ($_instance->childHasBeenRendered('1JaVCWE')) {
    $componentId = $_instance->getRenderedChildComponentId('1JaVCWE');
    $componentTag = $_instance->getRenderedChildComponentTagName('1JaVCWE');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('1JaVCWE');
} else {
    $response = \Livewire\Livewire::mount('navigation-dropdown');
    $html = $response->html();
    $_instance->logRenderedChild('1JaVCWE', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>

            <!-- Page Heading -->
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <?php echo e($header); ?>

                </div>
            </header>

            <!-- Page Content -->
            <main>
                <?php echo e($slot); ?>

            </main>
        </div>

        <?php echo $__env->yieldPushContent('modals'); ?>
		

        <?php echo \Livewire\Livewire::scripts(); ?>



    </body>
</html>
<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/layouts/app.blade.php ENDPATH**/ ?>