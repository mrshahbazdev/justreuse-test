<div wire:poll.10s class="flex flex-col w-full bg-white h-full">
    <div class="p-5 border-b">
         <h2 class="text-2xl font-bold">Inbox</h2>
    </div>

    <div class="flex-grow overflow-y-auto p-2">
        <?php $__empty_1 = true; $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
                $senderId = ($conv->from_id == auth()->id()) ? $conv->to_id : $conv->from_id;
                $sender = \App\Models\User::find($senderId);
                $lastChat = \App\Models\TblChat::where('post_id', $conv->post_id)
                    ->where(function($q) use($senderId) {
                        $q->where('from_id', auth()->id())->where('to_id', $senderId)
                          ->orWhere('from_id', $senderId)->where('to_id', auth()->id());
                    })
                    ->latest()->first();
                $unreadCount = \App\Models\TblChat::getUnreadCount(auth()->id(), $senderId, $conv->post_id);
                $convId = $senderId . '-' . $conv->post_id;
            ?>
            <?php if($sender && $lastChat): ?>
      			<?php
                    $safeSenderId = $senderId ?? '';
                    $safePostId = $conv->post_id ?? '';
                ?>
            <div wire:click="$emit('conversationSelected', '<?php echo e($safeSenderId); ?>', '<?php echo e($safePostId); ?>')"
                 class="flex items-center p-3 my-1 rounded-xl cursor-pointer <?php echo e(($selectedConvId == $convId) ? 'bg-orange-100' : 'hover:bg-gray-100'); ?>">
                <div class="relative">
                     <img src="<?php echo e($sender->profile_photo_path ? asset('storage/' . $sender->profile_photo_path) : 'https://placehold.co/40x40'); ?>" class="h-10 w-10 rounded-full object-cover">
                </div>
                <div class="ml-3 flex-grow text-sm">
                    <p class="font-semibold"><?php echo e($sender->name); ?></p>
                    <p class="text-xs text-gray-500 truncate"><?php echo e($lastChat->msg ?? 'Attachment'); ?></p>
                </div>
                <div class="flex flex-col items-end text-xs">
                    <span class="text-gray-500 mb-1.5"><?php echo e($lastChat->created_at->format('h:i a')); ?></span>
                    <?php if($unreadCount > 0): ?>
                        <span class="flex items-center justify-center bg-green-500 text-white rounded-full h-5 w-5 font-bold"><?php echo e($unreadCount); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="text-center p-4 text-gray-500">No conversations yet.</p>
        <?php endif; ?>
    </div>
</div><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/conversation-list.blade.php ENDPATH**/ ?>