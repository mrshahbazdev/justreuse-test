<div>
    
    <div class="w-full float-left border-b border-gray-200">
        <div class="m-auto container px-4">
            <h1 class="text-2xl md:text-3xl font-bold  py-8">My Orders & Sales</h1>
        </div>
    </div>

    
    <div class="w-full float-left py-6">
        <div class="container mx-auto px-4">

            
            <div class="border-b border-gray-200 mb-6">
                <ul class="flex flex-wrap -mb-px">
                    <li class="mr-2">
                        <button wire:click="switchTab('orders')"
                           class="inline-flex items-center gap-2 py-4 px-4 text-sm font-medium text-center rounded-t-lg border-b-2 <?php echo e($activeTab == 'orders' ? 'text-green-600 border-green-600' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300'); ?>">
                            <i class="fa fa-shopping-bag"></i>
                            <span>My Orders (<?php echo e($orders->total()); ?>)</span>
                        </button>
                    </li>
                    <li class="mr-2">
                         <button wire:click="switchTab('sales')"
                           class="inline-block py-4 px-4 text-sm font-medium text-center rounded-t-lg border-b-2 <?php echo e($activeTab == 'sales' ? 'text-green-600 border-green-600' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300'); ?>">
                            <i class="fa fa-dollar-sign"></i>
                            <span>My Sales (<?php echo e($sales->total()); ?>)</span>
                        </button>
                    </li>
                </ul>
            </div>
            
            <div wire:loading.flex class="w-full justify-center items-center py-16">
                <i class="fa fa-spinner fa-spin text-green-500 text-4xl"></i>
            </div>

            <div wire:loading.remove>
                
                <?php if($activeTab == 'orders'): ?>
                    <div class="space-y-6">
                        <?php $__empty_1 = true; $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $post_info = \App\Models\TblPost::where('id', $order->post_id)->withTrashed()->first();
                                $post_img = \App\Models\TblChat::getPostImgForList($order->post_id);
                                $post_url = $post_info ? \App\Models\TblPost::get_post_slug($post_info->slug) : '#';
                                $seller_info = \App\Models\User::where('id', $order->seller_id)->withTrashed()->first();
                            ?>
                             <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">ORDER #<?php echo e($order->orderId); ?></p>
                                        <p class="text-xs text-gray-500">Placed on: <?php echo e($order->created_at->format('d M Y')); ?></p>
                                    </div>
                                    <span class="text-xs font-bold uppercase px-2 py-1 rounded-full 
                                        <?php if($order->order_status == 'pending'): ?> bg-yellow-100 text-yellow-800 <?php endif; ?>
                                        <?php if($order->order_status == 'delivered'): ?> bg-green-100 text-green-800 <?php endif; ?>
                                        <?php if($order->order_status == 'shipped'): ?> bg-blue-100 text-blue-800 <?php endif; ?>
                                        <?php if($order->order_status == 'processing'): ?> bg-indigo-100 text-indigo-800 <?php endif; ?>
                                        <?php if($order->order_status == 'cancelled'): ?> bg-red-100 text-red-800 <?php endif; ?>
                                    "><?php echo e($order->order_status); ?></span>
                                </div>
                                <div class="p-4 md:flex items-center gap-4">
                                    <div class="flex-shrink-0 mb-4 md:mb-0">
                                        <a href="<?php echo e($post_url); ?>">
                                            <img class="rounded-lg object-cover h-24 w-24 border" src="<?php echo e($post_img); ?>" />
                                        </a>
                                    </div>
                                    <div class="flex-grow">
                                        <a href="<?php echo e($post_url); ?>"><h3 class="text-lg font-semibold hover:text-green-500"><?php echo e($post_info->title ?? 'Post Not Available'); ?></h3></a>
                                        <p class="text-sm text-gray-500">Sold by: <a href="<?php echo e(url('/seller-profile/' . $order->seller_id)); ?>" class="text-green-600 font-medium"><?php echo e($seller_info->name ?? 'User Deleted'); ?></a></p>
                                    </div>
                                    <div class="text-right mt-4 md:mt-0">
                                        <a href="<?php echo e(url('/vieworder/' . $order->orderId)); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 text-white text-sm font-semibold rounded-lg hover:bg-green-600">View Details</a>
                                        <?php if($order->order_status == 'pending'): ?>
                                            <button wire:click="updateOrderStatus('<?php echo e($order->id); ?>', 'cancelled')" class="ml-2 inline-flex items-center gap-2 px-4 py-2 bg-red-100 text-red-700 text-sm font-semibold rounded-lg hover:bg-red-200">Cancel</button>
                                        <?php endif; ?>
                                        <?php if($order->order_status == 'shipped'): ?>
                                            <button wire:click="updateOrderStatus('<?php echo e($order->id); ?>', 'delivered')" class="ml-2 inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white text-sm font-semibold rounded-lg hover:bg-blue-600">Mark as Delivered</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center py-16"><p class="text-gray-500">You have not placed any orders yet.</p></div>
                        <?php endif; ?>
                        <div class="mt-6"><?php echo e($orders->links()); ?></div>
                    </div>
                <?php endif; ?>

                
                <?php if($activeTab == 'sales'): ?>
                     <div class="space-y-6">
                        <?php $__empty_1 = true; $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                             <?php
                                $post_info = \App\Models\TblPost::where('id', $sale->post_id)->withTrashed()->first();
                                $post_img = \App\Models\TblChat::getPostImgForList($sale->post_id);
                                $post_url = $post_info ? \App\Models\TblPost::get_post_slug($post_info->slug) : '#';
                                $buyer_info = \App\Models\User::where('id', $sale->user_id)->withTrashed()->first();
                            ?>
                             <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">SALE #<?php echo e($sale->orderId); ?></p>
                                        <p class="text-xs text-gray-500">Order from: <?php echo e($buyer_info->name ?? 'User Deleted'); ?></p>
                                    </div>
                                     <span class="text-xs font-bold uppercase px-2 py-1 rounded-full 
                                        <?php if($sale->order_status == 'pending'): ?> bg-yellow-100 text-yellow-800 <?php endif; ?>
                                        <?php if($sale->order_status == 'delivered'): ?> bg-green-100 text-green-800 <?php endif; ?>
                                        <?php if($sale->order_status == 'shipped'): ?> bg-blue-100 text-blue-800 <?php endif; ?>
                                        <?php if($sale->order_status == 'processing'): ?> bg-indigo-100 text-indigo-800 <?php endif; ?>
                                        <?php if($sale->order_status == 'cancelled'): ?> bg-red-100 text-red-800 <?php endif; ?>
                                    "><?php echo e($sale->order_status); ?></span>
                                </div>
                                <div class="p-4 md:flex items-center gap-4">
                                    <div class="flex-shrink-0 mb-4 md:mb-0">
                                        <a href="<?php echo e($post_url); ?>">
                                            <img class="rounded-lg object-cover h-24 w-24 border" src="<?php echo e($post_img); ?>" />
                                        </a>
                                    </div>
                                    <div class="flex-grow">
                                        <a href="<?php echo e($post_url); ?>"><h3 class="text-lg font-semibold hover:text-green-500"><?php echo e($post_info->title ?? 'Post Not Available'); ?></h3></a>
                                        <p class="text-sm text-gray-500">Order date: <?php echo e($sale->created_at->format('d M Y')); ?></p>
                                    </div>
                                    <div class="text-right mt-4 md:mt-0">
                                        <a href="<?php echo e(url('/vieworder/' . $sale->orderId)); ?>" class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 text-white text-sm font-semibold rounded-lg hover:bg-green-600">View Details</a>
                                        <?php if($sale->order_status == 'pending'): ?>
                                            <button wire:click="updateOrderStatus('<?php echo e($sale->id); ?>', 'processing')" class="ml-2 inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 text-white text-sm font-semibold rounded-lg hover:bg-yellow-600">Mark as Processing</button>
                                        <?php endif; ?>
                                        <?php if($sale->order_status == 'processing'): ?>
                                            <button wire:click="openShippingModal('<?php echo e($sale->id); ?>')" class="ml-2 inline-flex items-center gap-2 px-4 py-2 bg-indigo-500 text-white text-sm font-semibold rounded-lg hover:bg-indigo-600">Mark as Shipped</button>
                                        <?php endif; ?>
                                         <?php if($sale->order_status == 'shipped'): ?>
                                            <button wire:click="openShippingModal('<?php echo e($sale->id); ?>')" class="ml-2 inline-flex items-center gap-2 px-4 py-2 bg-blue-500 text-white text-sm font-semibold rounded-lg hover:bg-blue-600">Edit Tracking</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="text-center py-16"><p class="text-gray-500">You have not made any sales yet.</p></div>
                        <?php endif; ?>
                        <div class="mt-6"><?php echo e($sales->links()); ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    
    <div x-data="{ show: <?php if ((object) ('showShippingModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e('showShippingModal'->value()); ?>')<?php echo e('showShippingModal'->hasModifier('defer') ? '.defer' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e('showShippingModal'); ?>')<?php endif; ?> }" x-show="show" @keydown.escape.window="show = false" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen">
            <div x-show="show" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
            <div x-show="show" x-transition class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full">
                <form wire:submit.prevent="saveShippingDetails">
                    <div class="bg-white px-6 py-4">
                        <h3 class="text-xl font-semibold text-gray-900 mb-4">Shipping Details</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Shipment Date <span class="text-red-500">*</span></label>
                                <input type="date" wire:model.defer="shipping_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <?php $__errorArgs = ['shipping_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Courier Name <span class="text-red-500">*</span></label>
                                <input type="text" wire:model.defer="courier_name" placeholder="e.g., DHL, FedEx" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                 <?php $__errorArgs = ['courier_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700">Service Type <span class="text-red-500">*</span></label>
                                <input type="text" wire:model.defer="courier_service" placeholder="e.g., Express, Standard" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                 <?php $__errorArgs = ['courier_service'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700">Tracking ID <span class="text-red-500">*</span></label>
                                <input type="text" wire:model.defer="tracking_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <?php $__errorArgs = ['tracking_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-xs"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Additional Notes</label>
                                <textarea wire:model.defer="more_info" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm">Save & Mark as Shipped</button>
                        <button @click="show = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    
    <div id="toast-notification" class="fixed bottom-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg" style="display: none; opacity: 0; transform: translateY(0.5rem); transition: all 0.3s ease-out;"><p id="toast-message"></p></div>
    <script>
        document.addEventListener('livewire:load', function () {
            const toast = document.getElementById('toast-notification');
            const toastMessage = document.getElementById('toast-message');
            let toastTimeout;
            window.addEventListener('show-toast', event => {
                if (toastTimeout) clearTimeout(toastTimeout);
                toastMessage.innerText = event.detail.message;
                toast.style.display = 'block';
                setTimeout(() => {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateY(0)';
                }, 10);
                toastTimeout = setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(0.5rem)';
                    setTimeout(() => { toast.style.display = 'none'; }, 300);
                }, 3000);
            });
        });
    </script>
  <style>
  	
        
        
        html, body {
            height: 100%;
        }
        
        body {
            display: flex;
            flex-direction: column;
            background-color: #fffbfa;
            color: #0f172a;
            min-height: 100vh;
        }
        
        .page-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .main-content {
            flex: 1;
        }

        /* Footer */
        footer {
            border-top: 1px solid #e2e8f0;
            
            margin-top: auto;
            width: 100%;
        }
        
  </style>
</div>
<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/my-orders-sales.blade.php ENDPATH**/ ?>