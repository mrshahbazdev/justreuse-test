<?php if($updateMode): ?>
	<?php echo $__env->make('livewire.admin.otherpage.edit', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php elseif($insertMode): ?>
	<?php echo $__env->make('livewire.admin.otherpage.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php else: ?>
	<?php echo $__env->make('livewire.admin.otherpage.show', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/otherpage/compo.blade.php ENDPATH**/ ?>