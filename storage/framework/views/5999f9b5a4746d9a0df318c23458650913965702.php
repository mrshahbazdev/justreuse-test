<?php 
    $dir_rtl =  App\Models\Setting::is_dir_rtl();
    echo "<script>var dir_rtl = ".$dir_rtl."</script>";
?>
<script src="<?php echo e(URL::to('js/jquery.min.js')); ?>"></script>
<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/layouts/apm/frontend_js.blade.php ENDPATH**/ ?>