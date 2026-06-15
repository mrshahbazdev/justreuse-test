<!-- Report User Modal -->
<div x-data="{ show: <?php if ((object) ('showReportModal') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e('showReportModal'->value()); ?>')<?php echo e('showReportModal'->hasModifier('defer') ? '.defer' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($_instance->id); ?>').entangle('<?php echo e('showReportModal'); ?>')<?php endif; ?> }" x-show="show" @keydown.escape.window="show = false" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen">
        <div x-show="show" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div x-show="show" x-transition class="inline-block bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full">
            <form wire:submit.prevent="submitReport">
                <div class="bg-white px-6 py-4">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">Report User</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Reason for reporting</label>
                            <?php $__currentLoopData = $report_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <label class="flex items-center mt-2">
                                    <input wire:model="reportType" type="radio" value="<?php echo e($type->id); ?>" class="form-radio h-4 w-4 text-green-600 transition duration-150 ease-in-out">
                                    <span class="ml-2 text-gray-700"><?php echo e($type->name); ?></span>
                                </label>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                             <?php $__errorArgs = ['reportType'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div>
                            <label for="report_comment" class="block text-sm font-medium text-gray-700">Comment (max 500 chars)</label>
                            <textarea wire:model="reportComment" id="report_comment" rows="3" maxlength="500" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500"></textarea>
                            <?php $__errorArgs = ['reportComment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500 text-sm mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" wire:loading.attr="disabled" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Submit Report
                    </button>
                     <button @click="show = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/seller-profile/modals/report.blade.php ENDPATH**/ ?>