<?php if(auth()->user()->can('dashboard-list')): ?>

<div class="p-6 border-t border-gray-200">
     
<?php echo $__env->make('livewire.admin.dashboard.cardstats', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('livewire.admin.dashboard.chart', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>        


</div>

 
<?php endif; ?> 


  
<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/dashboard/show.blade.php ENDPATH**/ ?>