<?php 
    $dir_rtl =  App\Models\Setting::is_dir_rtl();
    echo "<script>var dir_rtl = ".$dir_rtl."</script>";
?>
<script src="<?php echo e(URL::to('js/jquery.min.js')); ?>"></script>
<script src="<?php echo e(URL::to('js/jquery-ui.js')); ?>"></script>
<script src="<?php echo e(URL::to('js/tailwindcss-stimulus-components.umd.js')); ?>"></script>
<script src="<?php echo e(URL::to('js/slick.min.js')); ?>"></script>
<script src="<?php echo e(URL::to('js/slick-lightbox.min.js')); ?>"></script>
<script src="<?php echo e(URL::to('js/owl.carousel.js')); ?>"></script>
<script src="<?php echo e(URL::to('js/alpine.min.js')); ?>" defer></script>
<script src="<?php echo e(URL::to('js/toastr.min.js')); ?>"></script>
<script src="<?php echo e(URL::to('js/jquery.hoverIntent.js')); ?>"></script>
<!-- <script src="<?php echo e(URL::to('js/stimulus.umd.js')); ?>"></script> -->
 
<script src="<?php echo e(URL::to('js/tailwind_popper.js')); ?>" defer></script>

<?php echo $__env->make('livewire.common.seo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('livewire.common.location_default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/layouts/apm/frontend_other_js.blade.php ENDPATH**/ ?>