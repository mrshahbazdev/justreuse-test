<?php if($buynoworder_view_mode): ?>
	<?php echo $__env->make('livewire.admin.buynow_orders.view', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php else: ?>
	<?php echo $__env->make('livewire.admin.buynow_orders.show', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/buynow_orders/compo.blade.php ENDPATH**/ ?>