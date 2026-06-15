<?php if($insertMode): ?>
	<?php echo $__env->make('livewire.admin.email-template.add', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php elseif($updateMode): ?>
	<?php echo $__env->make('livewire.admin.email-template.edit', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php else: ?>
    <?php echo $__env->make('livewire.admin.email-template.show', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/email-template/compo.blade.php ENDPATH**/ ?>