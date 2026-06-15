<?php if(auth()->user()->can('settings-list')): ?>
<div>
<div class="py-24 hidden">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
            <?php if(session()->has('message')): ?>
            <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert">
                <div class="flex">
                    <div>
                        <p class="text-sm"><?php echo e(session('message')); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <br>
            <table class="table-auto w-full">
                <thead>
                    <tr class="border">
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Setting</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">description</th>
                        <?php if(auth()->user()->can('settings-edit')): ?>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="border grid-style">
                    <?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr align="center">
                        <td class="border-b px-4 py-2"><?php echo e($row->name); ?></td>
                        <td class="border-b px-4 py-2"><?php echo e($row->description); ?></td>
                        <?php if(auth()->user()->can('settings-edit')): ?>
                        <td class="border-b px-4 py-2">                            
                            <button wire:click="edit('<?php echo e($row->id); ?>')" class="editbutton  bg-green-500 hover:bg-orange-500 text-white font-bold py-1 px-2 text-xs rounded focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-edit"></i></button>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="mt-4">
              <?php echo e($settings->links()); ?> 
            </div>           
        </div>
        
       
    </div>
    <?php if($cnfopen): ?>
    <?php echo $__env->make('livewire.common.confirmation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div>


<!-- tab section start -->

<div class="py-4">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-3 xl:p-5 top-shadow">
            <!-- start tab -->
        <div x-data="
   {
   openTab: 1,
   activeClasses: 'bg-green-500 text-white',
   inactiveClasses: 'text-body-color hover:bg-orange-500 hover:text-white',
   }
   " class="w-full mb-14 border border-[#E4E4E4] rounded-lg">
   <div class="gap-2 xl:gap-3
      flex flex-wrap
      rounded-lg
      py-3
      px-2
      xl:px-4
      ">

      
      <?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <?php 
        if($row->id==$setting_id){
            $clsname = "bg-green-500 text-white";
        }else{
            $clsname = "text-body-color hover:bg-gray-100 hover:text-black";
        }
      ?>

      <button wire:click="edit('<?php echo e($row->id); ?>')" class="<?php echo $clsname; ?> text-sm md:text-base font-medium rounded-md py-3 px-2 md:px-3 lg:px-4 uppercase">
      <?php echo e($row->name); ?>

      </button>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

   </div>
   
    <?php if($message = Session::get('message')): ?>
		<script>
			toastr.success("<?php echo e($message); ?>");
		</script>
	<?php endif; ?>

   <div>
       
    <?php if($updateMode): ?>
	    <?php echo $__env->make('livewire.admin.settings.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif($freeAdMode): ?>
        <?php echo $__env->make('livewire.admin.settings.free-ad', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif($homeBannerMapMode): ?>
        <?php echo $__env->make('livewire.admin.settings.home-banner-map', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif($DistanceAdMode): ?>
        <?php echo $__env->make('livewire.admin.settings.distance-range', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif($ImageSizeAdMode): ?>
        <?php echo $__env->make('livewire.admin.settings.image-size-settings', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif($TwilioMode): ?>
        <?php echo $__env->make('livewire.admin.settings.twilio-sms', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif($BannerAdvertisement): ?>
        <?php echo $__env->make('livewire.admin.settings.banner-advertisement', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php elseif($Currency): ?>
        <?php echo $__env->make('livewire.admin.settings.currency-rate', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php else: ?>
        <?php echo $__env->make('livewire.admin.settings.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>

     
   </div>
</div>

<!-- end tab -->
        </div>
    </div>
</div>

<script>
    $(document).ready(function(){
    $(".editbutton:first").click();

    });
</script>
</div>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/settings/show.blade.php ENDPATH**/ ?>