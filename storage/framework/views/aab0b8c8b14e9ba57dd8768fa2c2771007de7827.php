<?php if($bannerads_view_mode): ?>
	<?php echo $__env->make('livewire.admin.banner_advertisements.view', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php else: ?>
	<?php echo $__env->make('livewire.admin.banner_advertisements.show', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/banner_advertisements/compo.blade.php ENDPATH**/ ?>