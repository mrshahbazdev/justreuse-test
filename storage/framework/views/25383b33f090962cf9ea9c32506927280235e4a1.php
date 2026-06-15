<?php if(auth()->user()->can('currency-list')): ?>
<div class="relative md:pt-28 pb-32 pt-12">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="bg-white overflow-x-auto shadow-xl sm:rounded-lg p-5 px-0">
            <?php if(session()->has('message')): ?>
            <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3 alert-<?php echo e(Session::get('class')); ?>"" role=" alert">
                <div class="flex">
                    <div>
                        <p class="text-sm"><?php echo e(session('message')); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <!-- search with button -->
            <div class="rounded text-sm font-bold mx-2 items-center bg-gray-100 flex justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 mb-4 pb-2 xl:pb-0">
                <?php if(auth()->user()->can('currency-create')): ?>
                <button wire:click="create()" class="bg-green-500 hover:bg-orange-500 text-white py-2 px-4 rounded-sm my-3">New Currency</button>
                <?php endif; ?>
                <!-- search -->
                <div class="flex flex-row flex-wrap items-center lg:ml-auto border border-gray-300 relative z-0">
                    <div class="absolute z-10 pl-3"><i class="fas fa-search text-gray-400 "></i> </div>
                    <input type="text" wire:model="search" class="px-3 py-3 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-full pl-8" placeholder="Search short code">
                </div>
            </div>
            <!-- end search -->
            <?php if($insertMode): ?>
            <?php echo $__env->make('livewire.admin.currencies.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>

            <table class="w-full">
                <thead>
                    <tr class="border border-l-0 border-r-0">
                        <th class="border-b px-4 py-2 text-left text-transform: uppercase text-xs">Name</th>
                        <th class="border-b px-4 py-2 text-left text-transform: uppercase text-xs">Short code</th>
                        <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Symbol</th>
                        <?php if(auth()->user()->can('currency-delete')): ?>
                        <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
                    <?php $__currentLoopData = $currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td class="border-b px-4 py-2"><?php echo e($currency->currency_name); ?></td>
                        <td class="border-b px-4 py-2 whitespace-normal"><?php echo e($currency->short_code); ?></td>
                        <td class="border-b px-4 py-2 text-center"><?php echo $currency->currency_hex; ?></td>
                        <td class="border-b px-4 py-2 text-center">
                            <?php if(auth()->user()->can('currency-delete')): ?>
                            <button wire:click="deleteReq('<?php echo e($currency->id); ?>')" class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 text-xs rounded-sm delete focus:outline-none focus:border-red-900 focus:shadow-outline-red transition ease-in-out duration-150"><i class="far fa-trash-alt"></i></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="mt-4 px-3">
                <?php echo e($currencies->links()); ?>

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
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/currencies/show.blade.php ENDPATH**/ ?>