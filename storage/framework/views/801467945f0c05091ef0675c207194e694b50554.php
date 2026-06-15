<?php if($updateMode): ?>
	<?php echo $__env->make('livewire.admin.bulk-email.edit', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php elseif($insertMode): ?>
	<?php echo $__env->make('livewire.admin.bulk-email.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php elseif($previewMode): ?>
	<?php echo $__env->make('livewire.admin.bulk-email.preview', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php else: ?>
	<?php echo $__env->make('livewire.admin.bulk-email.show', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/bulk-email/compo.blade.php ENDPATH**/ ?>