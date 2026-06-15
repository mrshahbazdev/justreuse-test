<div>
    
    <div class="w-full float-left bg-gray-50 border-b border-gray-200">
        <div class="m-auto container px-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 py-8">Create a New Banner Ad</h1>
        </div>
    </div>

    
    <div class="w-full float-left py-6">
        <div class="container mx-auto px-4">
            <form wire:submit.prevent="saveBannerAd" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                
                <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-6">
                    
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Ad Details</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Display Page</label>
                                <select wire:model="page" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="home">Home Page</option>
                                    <option value="search">Search/Category Page</option>
                                </select>
                            </div>

                            <?php if($page === 'search'): ?>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Category</label>
                                <select wire:model="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Select a Category</option>
                                    <?php $__currentLoopData = $categorylist; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($category->id); ?>"><?php echo e(str_repeat('--', $category->depth)); ?> <?php echo e($category->title); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Duration</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input type="date" wire:model="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <?php $__errorArgs = ['start_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700">End Date</label>
                                <input type="date" wire:model="end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <?php $__errorArgs = ['end_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Links & Banners</h2>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Web Link (URL)</label>
                                <input type="url" wire:model.lazy="web_link" placeholder="https://example.com" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <?php $__errorArgs = ['web_link'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700">App Link (URL)</label>
                                <input type="url" wire:model.lazy="app_link" placeholder="https://example.com/app" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <?php $__errorArgs = ['app_link'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Web Banner (Image)</label>
                                <input type="file" wire:model="web_banner" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                                <div wire:loading wire:target="web_banner">Uploading...</div>
                                <?php $__errorArgs = ['web_banner'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <?php if($web_banner && !$errors->has('web_banner')): ?>
                                    <img src="<?php echo e(asset('storage/livewire-tmp/' . $web_banner->getFilename())); ?>" class="mt-2 h-24 rounded-lg border">
                                <?php endif; ?>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">App Banner (Image)</label>
                                <input type="file" wire:model="app_banner" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                                <div wire:loading wire:target="app_banner">Uploading...</div>
                                <?php $__errorArgs = ['app_banner'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <?php if($app_banner && !$errors->has('app_banner')): ?>
                                    <img src="<?php echo e(asset('storage/livewire-tmp/' . $app_banner->getFilename())); ?>" class="mt-2 h-24 rounded-lg border">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="lg:col-span-1">
                    <div class="sticky top-28 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-4">Order Summary</h2>
                        
                        <div wire:loading.class="opacity-50" wire:target="calculatePrice" class="space-y-4 transition-opacity">
                            <div class="flex justify-between items-center text-gray-600">
                                <span>Price Per Day</span>
                                <span class="font-semibold"><?php echo $currencySymbol; ?><?php echo e(number_format($pricePerDay, 2)); ?></span>
                            </div>
                             <div class="flex justify-between items-center text-gray-600">
                                <span>Live Days</span>
                                <span class="font-semibold"><?php echo e($live_days); ?></span>
                            </div>
                            <div class="flex justify-between items-center text-2xl font-bold text-gray-800 mt-2 pt-4 border-t">
                                <span>Total Amount</span>
                                <span><?php echo $currencySymbol; ?><?php echo e(number_format($totalAmount, 2)); ?></span>
                            </div>
                        </div>

                        <div class="mt-6">
                             <h3 class="text-md font-semibold text-gray-700 mb-3">Payment Method</h3>
                             <div class="space-y-3">
                                <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer <?php echo e($selectedPaymentMethod == $method['name'] ? 'bg-green-50 border-green-500' : 'border-gray-200'); ?>">
                                        <input type="radio" wire:model="selectedPaymentMethod" name="payment_type" value="<?php echo e($method['name']); ?>" class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500">
                                        <span class="ml-3 font-medium text-gray-700"><?php echo e($method['display_name']); ?></span>
                                    </label>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>

                        <button type="submit" wire:loading.attr="disabled"
                            class="w-full mt-6 inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-6 py-3 font-semibold text-white hover:bg-green-700 transition-all disabled:opacity-50">
                            <span wire:loading.remove wire:target="saveBannerAd">Create Ad & Proceed</span>
                            <span wire:loading wire:target="saveBannerAd">Creating Ad...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    
    <div id="toast-notification" class="fixed bottom-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg" style="display: none; opacity: 0; transform: translateY(0.5rem); transition: all 0.3s ease-out;"><p id="toast-message"></p></div>
    <script>
        document.addEventListener('livewire:load', function () {
            // Toastr script
        });
    </script>
</div>

<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/create-banner-ad.blade.php ENDPATH**/ ?>