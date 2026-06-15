<!DOCTYPE html>
<html>
    <head>
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">       
        <script src="<?php echo e(URL::to('js/jquery-1.10.2.js')); ?>"></script>    
        <script src="<?php echo e(URL::to('js/jquery.min.js')); ?>"></script> 
        <title>Classified</title>
        <?php echo \Livewire\Livewire::scripts(); ?>

    </head>
    <body class="antialiased">       
        <main>           
            <!--content start--> 
            <?php echo $__env->yieldContent('content'); ?>
            <!--content end-->
        </main>       
    </body>
</html>
<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/layouts/payment.blade.php ENDPATH**/ ?>