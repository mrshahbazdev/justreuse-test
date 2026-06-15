<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">My Advertisements</h1>

    <?php if(session()->has('success')): ?>
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <div class="space-y-6">
        <?php $__empty_1 = true; $__currentLoopData = $advertisements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                    <div>
                        <p class="text-sm font-semibold text-gray-800"><?php echo e($ad->adZone->name ?? 'N/A'); ?></p>
                        <p class="text-xs text-gray-500">
                            Runs from <?php echo e(\Carbon\Carbon::parse($ad->start_date)->format('d M Y')); ?> to <?php echo e(\Carbon\Carbon::parse($ad->end_date)->format('d M Y')); ?>

                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="text-lg font-bold text-green-700">$<?php echo e(number_format($ad->total_amount, 2)); ?></span>
                         <span class="text-xs font-bold uppercase px-2 py-1 rounded-full 
                            <?php echo e($ad->status == 'approved' ? 'bg-green-100 text-green-800' : ''); ?>

                            <?php echo e($ad->status == 'pending_approval' ? 'bg-yellow-100 text-yellow-800' : ''); ?>

                            <?php echo e($ad->status == 'rejected' ? 'bg-red-100 text-red-800' : ''); ?>

                        "><?php echo e(str_replace('_', ' ', $ad->status)); ?></span>
                    </div>
                </div>
                <div class="p-6 flex items-center gap-6">
                    <?php if(isset($ad->content['image'])): ?>
                        <img src="<?php echo e(asset('storage/' . $ad->content['image'])); ?>" class="w-48 h-24 object-contain rounded-md border bg-gray-100">
                    <?php endif; ?>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900"><?php echo e($ad->content['headline'] ?? 'No Headline'); ?></h3>
                        <a href="<?php echo e($ad->content['link'] ?? '#'); ?>" target="_blank" class="text-sm text-blue-500 hover:underline truncate"><?php echo e($ad->content['link'] ?? ''); ?></a>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="text-center py-16 border-2 border-dashed rounded-lg">
                <p class="text-xl text-gray-600 font-semibold">No Advertisements Found</p>
                <p class="text-gray-500 mt-2">You haven't created any ads yet.</p>
                <a href="<?php echo e(route('ads.create')); ?>" class="mt-6 inline-block bg-green-600 text-white px-6 py-2 rounded-lg font-semibold">Create Your First Ad</a>
            </div>
        <?php endif; ?>

        <div class="mt-8">
            <?php echo e($advertisements->links()); ?>

        </div>
    </div>
</div>
<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/user/my-advertisements.blade.php ENDPATH**/ ?>