<?php if($updateMode): ?>
	<?php echo $__env->make('livewire.admin.staticpage.edit', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php elseif($insertMode): ?>
	<?php echo $__env->make('livewire.admin.staticpage.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php else: ?>
	<?php echo $__env->make('livewire.admin.staticpage.show', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/staticpage/compo.blade.php ENDPATH**/ ?>