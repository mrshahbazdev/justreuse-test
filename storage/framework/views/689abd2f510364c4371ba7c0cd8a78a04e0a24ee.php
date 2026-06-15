<?php if(auth()->user()->can('otherpages-list')): ?>
<div class="py-4">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-0 py-5 top-shadow">
        <?php if(auth()->user()->can('otherpages-create')): ?>
			<div class="bg-gray-100  pl-2 pr-2 mb-2 rounded-sm mx-5">
        <button wire:click="create()" class="bg-green-500 hover:bg-orange-500 text-white  py-2 px-4 rounded-sm my-3">Create New Page</button>
		</div>
	   <?php endif; ?>

        <div class="bg-gray-50 text-left  ">
            <?php if(session()->has('message')): ?>
            <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3 alert-<?php echo e(Session::get('class')); ?>" role="alert">
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
                    <tr class="border border-l-0 border-r-0">
                        <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Slug</th>
                        <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Title</th>
                        <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
                    <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr align="center">
                        <td class="border-b px-4 py-2"><?php echo e($row->slug); ?></td>
                        <td class="border-b px-4 py-2"><?php echo e($row->title); ?></td>
                        <td class="border-b px-4 py-2">
                        <?php if(auth()->user()->can('otherpages-edit')): ?>
                            <button wire:click="edit('<?php echo e($row->id); ?>')" class="bg-green-500 hover:bg-orange-500 text-white text-xs py-1 px-2 rounded-sm focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-edit"></i> </button>
                        <?php endif; ?>
                        <?php if(auth()->user()->can('otherpages-delete')): ?>
                            <!-- <button wire:click="deleteReq('<?php echo e($row->id); ?>')" class="bg-red-500 hover:bg-red-700 text-white text-xs py-1 px-2 rounded delete focus:outline-none focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-trash-alt"></i></button> -->
                        <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="mt-4">
            <?php echo e($list->links()); ?>

            </div>            
        </div>
    </div>
    <style>
        .alert-error {
            background: #ffc4c4;
            border-color: palevioletred;
        }
    </style>
    <?php if($cnfopen): ?>
    <?php echo $__env->make('livewire.common.confirmation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/otherpage/show.blade.php ENDPATH**/ ?>