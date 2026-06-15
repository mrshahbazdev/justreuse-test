<div>
    
    <div class="w-full float-left py-4 md:py-5 bg-light-green">
        <div class="container m-auto px-4">
            <div class="w-full float-left md:flex">
                
                <div class="w-full md:w-4/12 lg:w-3/12 relative mb-6 md:pr-4">
                    <div class="bg-white p-4 shadow-lg rounded-lg border w-full sticky top-4">
                        
                        <div class="flex items-center mb-4">
                            <img class="w-16 h-16 object-cover rounded-full mr-4 border-2 border-green-200" src="<?php echo e($seller->profile_photo_url); ?>" alt="<?php echo e($seller->name); ?>" />
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-gray-800 poppins-600"><?php echo e($seller->name); ?></h3>
                                <p class="text-xs text-gray-600 bg-gray-100 rounded-full inline-block px-2 py-1">Joined: <?php echo e($seller->created_at->isoFormat('DD MMM YYYY')); ?></p>
                            </div>
                        </div>

                        
                        <div class="flex items-center mb-4">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <svg class="w-4 h-4 <?php echo e($avgRating >= $i ? 'text-yellow-400' : 'text-gray-300'); ?>" fill="currentColor" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                            <?php endfor; ?>
                            <span class="text-xs text-gray-500 ml-2">(<?php echo e($reviewsCount); ?> Reviews)</span>
                        </div>

                        
                        <div class="flex justify-between items-center text-xs text-gray-500 mb-4">
                            <span class="flex items-center"><span class="inline-block w-2 h-2 rounded-full mr-2 <?php echo e($seller->current_chat_status == 'online' ? 'bg-green-500' : 'bg-red-500'); ?>"></span> <?php echo e(ucfirst($seller->current_chat_status)); ?></span>
                            <span><?php echo e(\App\Models\TblChat::timeAgo($seller->created_at)); ?> on JustreUsed</span>
                        </div>

                        
                        <div class="space-y-2">
                            <a href="tel:<?php echo e($seller->phone); ?>" class="w-full flex items-center justify-center py-2.5 px-4 bg-green-500 text-white rounded-md poppins-600 hover:bg-green-600 transition duration-300">
                                <i class="fa fa-phone mr-2"></i> Show Contact
                            </a>

                            <?php if($isDifferentUser): ?>
                                <button wire:click="toggleFollow" wire:loading.attr="disabled" class="w-full py-2.5 px-4 border-2 border-green-500 text-green-500 rounded-md hover:bg-green-500 hover:text-white transition duration-300">
                                    <span wire:loading.remove wire:target="toggleFollow"><?php echo e($isFollowing ? 'Unfollow' : 'Follow'); ?></span>
                                    <span wire:loading wire:target="toggleFollow">Processing...</span>
                                </button>
                                <button wire:click="openReportModal" class="w-full text-center text-sm text-gray-500 hover:text-red-500 py-1">Report User</button>
                            <?php else: ?>
                                <button wire:click="$set('showInviteModal', true)" class="w-full py-2.5 px-4 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition duration-300">
                                    Invite Friends
                                </button>
                            <?php endif; ?>
                        </div>

                        
                        <div class="flex justify-around text-center mt-4 pt-4 border-t">
                            <div class="cursor-pointer" wire:click="$set('showFollowersModal', true)">
                                <p class="font-bold text-gray-700"><?php echo e($followersCount); ?></p>
                                <p class="text-xs text-gray-500">Followers</p>
                            </div>
                            <div class="cursor-pointer" wire:click="$set('showFollowingModal', true)">
                                <p class="font-bold text-gray-700"><?php echo e($followingCount); ?></p>
                                <p class="text-xs text-gray-500">Following</p>
                            </div>
                             <div class="cursor-pointer" wire:click="$set('showReviewsModal', true)">
                                <p class="font-bold text-gray-700"><?php echo e($reviewsCount); ?></p>
                                <p class="text-xs text-gray-500">Reviews</p>
                            </div>
                        </div>
                    </div>
                </div>

                
                <div class="w-full md:w-8/12 lg:w-9/12">
                    
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-700 mb-3">Filter by Category</h2>
                        <div class="flex flex-wrap gap-2">
                            <button wire:click="filterByCategory(null)" class="px-3 py-1 text-sm rounded-full <?php echo e(!$selectedCategory ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700'); ?>">All Categories (<?php echo e($totalAdsCount); ?>)</button>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <button wire:click="filterByCategory(<?php echo e($category->id); ?>)" class="px-3 py-1 text-sm rounded-full <?php echo e($selectedCategory == $category->id ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-700'); ?>"><?php echo e($category->title); ?> (<?php echo e($category->posts_count); ?>)</button>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    
                    <div class="flex justify-between items-center mb-4">
                        <h1 class="text-black font-bold text-xl">Published Ads</h1>
                        <div class="flex items-center">
                             <div class="hidden sm:flex items-center">
                                <button wire:click="$set('view', 'grid')" class="mr-1 px-2 py-1 rounded <?php echo e($view === 'grid' ? 'bg-green-500 text-white' : 'hover:bg-gray-200'); ?>"><i class="fa fa-th"></i></button>
                                <button wire:click="$set('view', 'list')" class="mr-4 px-2 py-1 rounded <?php echo e($view === 'list' ? 'bg-green-500 text-white' : 'hover:bg-gray-200'); ?>"><i class="fa fa-th-list"></i></button>
                            </div>
                            <div>
                                <label for="sort_by" class="text-gray-700 text-sm mr-2">Sort by:</label>
                                <select wire:model.lazy="sortBy" id="sort_by" class="text-dark-gray poppins-500 focus:outline-none bg-transparent py-2 px-3 rounded-md text-sm border">
                                    <option value="post-desc">Recently Posted</option>
                                    <option value="price-asc">Price: Low to High</option>
                                    <option value="price-desc">Price: High to Low</option>
                                    <option value="most-viewed">Popular</option>
                                </select>
                            </div>
                        </div>
                    </div>
                     <div wire:loading.flex class="w-full items-center justify-center p-8">
                        <i class="fa fa-spinner fa-spin text-green-500 text-3xl"></i>
                    </div>

                    
                    <div wire:loading.remove>
                        <?php if($sellerPosts->count() > 0): ?>
                            <div class="<?php echo e($view === 'grid' ? 'grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 gap-4' : 'space-y-4'); ?>">
                                <?php $__currentLoopData = $sellerPosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($view === 'grid'): ?>
                                        <?php echo \App\Models\Setting::htmlAdBlock($ad->id); ?>

                                    <?php else: ?>
                                        <?php echo \App\Models\Setting::viewblock($ad->id); ?>

                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-12 bg-gray-50 rounded-lg">
                                <p class="text-gray-500">No ads found for this filter.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    
                    <div class="mt-8">
                        <?php echo e($sellerPosts->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <?php echo $__env->make('livewire.seller-profile.modals.followers', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('livewire.seller-profile.modals.following', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('livewire.seller-profile.modals.invite', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('livewire.seller-profile.modals.report', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('livewire.seller-profile.modals.reviews', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    
    <div id="toast-notification"
         class="fixed bottom-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg"
         style="display: none; opacity: 0; transform: translateY(0.5rem); transition: all 0.3s ease-out;">
        <p id="toast-message"></p>
    </div>

    <script>
        document.addEventListener('livewire:load', function () {
            const toast = document.getElementById('toast-notification');
            const toastMessage = document.getElementById('toast-message');
            let toastTimeout;

            window.addEventListener('show-toast', event => {
                // Clear any existing timeout to reset the timer
                if (toastTimeout) {
                    clearTimeout(toastTimeout);
                }

                // Set message and show the toast
                toastMessage.innerText = event.detail.message;
                toast.style.display = 'block';

                // We use a short timeout to allow the display property to apply before starting the transition
                setTimeout(() => {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateY(0)';
                }, 10);


                // Set a timeout to hide the toast
                toastTimeout = setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(0.5rem)';

                    // Hide the element completely after the transition ends
                    setTimeout(() => {
                        toast.style.display = 'none';
                    }, 300); // This duration should match the transition duration
                }, 3000);
            });
        });
    </script>
</div>

<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/seller-profile.blade.php ENDPATH**/ ?>