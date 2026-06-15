<!-- Reviews Modal -->
<div x-data="{ show: <?php if ((object) ('showReviewsModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e('showReviewsModal'->value()); ?>')<?php echo e('showReviewsModal'->hasModifier('defer') ? '.defer' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e('showReviewsModal'); ?>')<?php endif; ?> }" x-show="show" @keydown.escape.window="show = false" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen">
        <div x-show="show" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div x-show="show" x-transition class="inline-block bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-2xl w-full">
            <div class="bg-white px-6 py-4">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Seller Reviews</h3>
                
                <!-- New Review Form -->
                <?php if(Auth::check() && $isBuyer): ?>
                <div class="border rounded-lg p-4 mb-6" x-data="{ rating: <?php if ((object) ('reviewRating') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e('reviewRating'->value()); ?>')<?php echo e('reviewRating'->hasModifier('defer') ? '.defer' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e('reviewRating'); ?>')<?php endif; ?>, hoverRating: 0 }">
                    <h4 class="font-semibold mb-2">Write a Review</h4>
                    <form wire:submit.prevent="submitReview">
                        <div class="flex items-center mb-2">
                             <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                <button type="button" @click="rating = star; $wire.setRating(star)" @mouseover="hoverRating = star" @mouseleave="hoverRating = 0"
                                        class="text-2xl" :class="(hoverRating >= star || rating >= star) ? 'text-yellow-400' : 'text-gray-300'">
                                    ★
                                </button>
                            </template>
                        </div>
                        <?php $__errorArgs = ['reviewRating'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mb-2"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <textarea wire:model="reviewText" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" rows="3" placeholder="Share your experience..."></textarea>
                        <?php $__errorArgs = ['reviewText'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <div class="text-right mt-2">
                             <button type="submit" wire:loading.attr="disabled" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:w-auto sm:text-sm">
                                Submit Review
                            </button>
                        </div>
                    </form>
                </div>
                <?php elseif(Auth::check() && !$isBuyer): ?>
                 <div class="bg-blue-50 text-blue-700 p-4 rounded-lg mb-6 text-center">
                    You need to purchase an item from this seller before you can leave a review.
                </div>
                <?php else: ?>
                <div class="bg-gray-100 p-4 rounded-lg mb-6 text-center">
                    <a href="<?php echo e(route('login')); ?>" class="font-semibold text-green-600 hover:underline">Log in</a> to leave a review.
                </div>
                <?php endif; ?>


                <!-- Existing Reviews List -->
                <div class="max-h-80 overflow-y-auto space-y-4">
                    <?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="border-b pb-4">
                        <div class="flex items-center mb-1">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="fa fa-star <?php echo e($i <= $review->ratings ? 'text-yellow-400' : 'text-gray-300'); ?>"></i>
                            <?php endfor; ?>
                            <span class="ml-2 font-bold text-gray-800"><?php echo e($review->name); ?></span>
                        </div>
                         <p class="text-sm text-gray-500 mb-2"><?php echo e(\Carbon\Carbon::parse($review->created_at)->format('d M Y')); ?></p>
                        <p class="text-gray-700"><?php echo e($review->comment); ?></p>
                        <?php if(!$review->approved): ?>
                            <p class="text-sm text-yellow-600 mt-2">(Your comment is waiting for approval)</p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-gray-500 text-center py-4">This seller has no reviews yet.</p>
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
<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/seller-profile/modals/reviews.blade.php ENDPATH**/ ?>