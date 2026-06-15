<!-- Followers List Modal -->
<div x-data="{ show: <?php if ((object) ('showFollowersModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e('showFollowersModal'->value()); ?>')<?php echo e('showFollowersModal'->hasModifier('defer') ? '.defer' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e('showFollowersModal'); ?>')<?php endif; ?> }" x-show="show" @keydown.escape.window="show = false" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <!-- Modal panel -->
        <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full"
             role="dialog" aria-modal="true">
            <div class="bg-white px-6 py-4">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Followers</h3>
                <div class="max-h-96 overflow-y-auto">
                    <?php $__empty_1 = true; $__currentLoopData = $followers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $follower): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <a href="<?php echo e(route('seller.profile', $follower->id)); ?>" class="flex items-center py-3 border-b hover:bg-gray-50">
                        <img class="w-12 h-12 rounded-full mr-4" src="<?php echo e($follower->profile_photo_path ? Storage::url($follower->profile_photo_path) : asset('storage/profile-avatar.jpg')); ?>" alt="<?php echo e($follower->name); ?>">
                        <span class="font-medium text-gray-800"><?php echo e($follower->name); ?></span>
                    </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500 text-center py-4">This user has no followers yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button @click="show = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/seller-profile/modals/followers.blade.php ENDPATH**/ ?>