<div>
    <div class="pb-2">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5 top-shadow">
            <!-- <button wire:click="back()" class="bg-green-500 hover:bg-orange-700 text-white py-2 px-4 rounded my-3 focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150">Back</button> -->
            <div class="bg-white px-0 xl:px-4 pt-5 pb-4 sm:p-2 sm:pb-4 text-left">
                <h1 class="text-2xl font-medium text-gray-900">Image Size Settings</h1>
                <div class="flex flex-col mb-4 w-1/2">
                    <small class="bg-red-300 rounded p-1 mt-4 mb-4">Note :: Please mention with * , Ex - 200*200 (for both list & detal page )</small>
                    <label class="block text-gray-700 text-ssm font-bold mb-2">For List Page <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="list_page" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php $__errorArgs = ['list_page'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">For Detail Page <span class="text-red-500">*</span></label>
                    <input type="text" wire:model="detail_page" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php $__errorArgs = ['detail_page'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="flex flex-col mb-4 md:w-1/2">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Max upload image limit ( for add post ) <span class="text-red-500">*</span></label>
                    <input type="number" pattern="/^-?\d+\.?\d*$/" onKeyPress="if(this.value.length==1) return false;" wire:model="max_image_limit" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    <?php $__errorArgs = ['max_image_limit'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
                <div class="p-2">
                    <span class="flex w-full rounded-md pb-2 sm:w-auto">
                        <button wire:click.prevent="update()" type="button" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150">
                            Update
                        </button>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/settings/image-size-settings.blade.php ENDPATH**/ ?>