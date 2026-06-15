
<?php if($post_updateMode): ?>
	<?php echo $__env->make('livewire.admin.admin_post.edit_post', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php elseif($user_updateMode): ?>
	<?php echo $__env->make('livewire.admin.admin_post.edit_user', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php else: ?>
	<?php echo $__env->make('livewire.admin.admin_post.show', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/admin_post/compo.blade.php ENDPATH**/ ?>