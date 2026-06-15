<div class="fixed z-10 inset-0 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen">
        <div class="fixed inset-0 transition-opacity"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
        <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
            <form wire:submit.prevent="store">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900"><?php echo e($zone_id ? 'Edit' : 'Create'); ?> Ad Zone</h3>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Zone Name</label>
                            <input type="text" wire:model.defer="name" id="name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                         <div>
                            <label for="page_location" class="block text-sm font-medium text-gray-700">Page Location</label>
                            <input type="text" wire:model.defer="page_location" id="page_location" placeholder="e.g., home, search, post_detail" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                             <?php $__errorArgs = ['page_location'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="price_per_day" class="block text-sm font-medium text-gray-700">Price Per Day</label>
                            <input type="number" step="0.01" wire:model.defer="price_per_day" id="price_per_day" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                             <?php $__errorArgs = ['price_per_day'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                             <div>
                                <label for="width" class="block text-sm font-medium text-gray-700">Width (px)</label>
                                <input type="number" wire:model.defer="width" id="width" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                 <?php $__errorArgs = ['width'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                             <div>
                                <label for="height" class="block text-sm font-medium text-gray-700">Height (px)</label>
                                <input type="number" wire:model.defer="height" id="height" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                 <?php $__errorArgs = ['height'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                         <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="auto_approve" wire:model.defer="auto_approve" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="auto_approve" class="font-medium text-gray-700">Auto Approve Ads</label>
                            </div>
                        </div>
                         <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="is_active" wire:model.defer="is_active" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="is_active" class="font-medium text-gray-700">Is Active</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm">Save</button>
                    <button wire:click="closeModal()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/ad-zone-modal.blade.php ENDPATH**/ ?>