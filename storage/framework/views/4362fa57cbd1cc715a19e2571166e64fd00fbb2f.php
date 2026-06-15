<?php if(auth()->user()->can('reporttype-list')): ?>
<div class="relative md:pt-28 pb-32 pt-12">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto mr-3">
        </div>
        <div class="bg-white p-5 rounded-md px-0 overflow-x-auto">
            <?php if(session()->has('message')): ?>
            <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert">
                <div class="flex">
                    <div>
                        <p class="text-sm"><?php echo e(session('message')); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="text-sm font-bold mx-5 items-center bg-gray-100 flex justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 mb-2 rounded">
                <?php if(auth()->user()->can('reporttype-create')): ?>
                <button wire:click="create()" class="bg-green-500 hover:bg-orange-500 text-white py-2 px-4 rounded-sm my-3 focus:outline-none focus:border-orange-500 focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150">Create Report Type</button>
                <?php endif; ?>
                <!-- search -->
                <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto border border-gray-300">
                    <div class="absolute z-50 pl-3"><i class="fas fa-search text-gray-400 "></i> </div>
                    <input type="text" wire:model="search" class="px-3 py-3 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-full pl-8" placeholder="Search report name">
                </div>
            </div>


            <table class="table-auto w-full">
                <thead>
                    <tr class="border border-l-0 border-r-0">
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Name</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Type</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Created_at</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
                    <?php $__currentLoopData = $report_type; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="border-b px-4 py-2 text-center"><?php echo e($row->name); ?></td>
                        <td class="border-b px-4 py-2 text-center"><?php echo e($row->type); ?></td>
                        <td class="border-b px-4 py-2 text-center"><?php echo e($row->created_at->format('d-m-Y h:i a')); ?></td>
                        <td class="border-b px-4 py-2 text-center">
                            <?php if(auth()->user()->can('reporttype-edit')): ?>
                            <button wire:click="edit('<?php echo e($row->id); ?>')" class="items-center px-2 py-1 bg-green-500 border border-transparent rounded-sm text-xs text-white  tracking-widest hover:bg-orange-500 active:bg-orange-500 focus:outline-none focus:border-orange-500 focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-edit"></i></button>
                            <?php endif; ?>
                            <?php if(auth()->user()->can('reporttype-delete')): ?>
                            <button wire:click="deleteReq('<?php echo e($row->id); ?>')" class="items-center px-2 py-1 bg-red-500 border border-transparent rounded-sm text-xs text-white tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150 ml-5"><i class="far fa-trash-alt"></i></a></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="mt-4">
           <?php echo e($report_type->links()); ?>

        </div>
    </div>
    <?php if($cnfopen): ?>
    <?php echo $__env->make('livewire.common.confirmation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/report_type/show.blade.php ENDPATH**/ ?>