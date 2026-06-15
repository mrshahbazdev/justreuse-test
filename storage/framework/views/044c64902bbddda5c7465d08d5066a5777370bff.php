<?php
	$slug = request()->segment(2);
    ?>
<?php if($slug=='verify'): ?> 
<div class="mx-4 sm:mx-12 md:mx-20 xl:mx-28 py-6 sm:py-10 lg:py-16">
    <div>
        <?php echo e($logo); ?>

    </div>

    <div class="">
        <?php echo e($slot); ?>

    </div>
</div>

<?php else: ?>
<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
    <div>
        <?php echo e($logo); ?>

    </div>

    <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
        <?php echo e($slot); ?>

    </div>
</div>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/vendor/jetstream/components/authentication-card.blade.php ENDPATH**/ ?>