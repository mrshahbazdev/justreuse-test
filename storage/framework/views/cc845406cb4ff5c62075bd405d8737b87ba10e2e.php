<?php if(auth()->user()->can('bulk-email-list')): ?>
<div class="py-4">
    <div class="px-4 md:px-10 mx-auto w-full">        
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg px-0 py-5 top-shadow  top-shadow">            
            <div class="text-sm font-bold mx-5 items-center bg-gray-100 flex justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 mb-2 rounded">
            <?php if(auth()->user()->can('bulk-email-create')): ?>
                    <button wire:click="create()" class="bg-green-500 hover:bg-orange-500 text-white py-2 px-4 rounded-sm my-3 focus:outline-none focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150">Add Bulk Email</button>
            <?php endif; ?>
                <!-- search -->
                
            </div>           
            <?php if(session()->has('message')): ?>
            <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3 alert-<?php echo e(Session::get('class')); ?>" role="alert">
                <div class="flex">
                    <div>
                        <p class="text-sm"><?php echo e(session('message')); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <table class="table-auto w-full">
                <thead>
                    <tr class="border border-l-0 border-r-0">
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Title</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Email code</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs"></th>
                        <?php if(auth()->user()->can('bulk-email-preview') || auth()->user()->can('bulk-email-edit') || auth()->user()->can('bulk-email-delete')): ?>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
                    <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php 
                        $init_email = $row->init_send_mail;
                        $complete_status = $row->mail_complete_status;
                        $initBtn = ($init_email == 1) ? "disabled": "";
                        $status = ($complete_status == 1) ? "Completed": "Start sent mail";
                    ?>
                    <tr align="center">
                        <td class="border-b px-4 py-2"><?php echo e($row->title); ?></td>
                        <td class="border-b px-4 py-2"><?php echo e($row->email_code); ?></td>
                        <td class="border-b px-4 py-2">  
                        <?php if(auth()->user()->can('bulk-email-start-sent-mail')): ?>
                            <button wire:click="start_sent_mail('<?php echo e($row->id); ?>')" class="bg-green-500 hover:bg-orange-500 text-white py-1.5 px-3 rounded-sm text-xs focus:outline-none focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150" title="sent mail" <?php echo $initBtn; ?>><?php echo e($status); ?></button>
                        <?php endif; ?>
                        </td>
                        <td class="border-b px-4 py-2">
                        <?php if(auth()->user()->can('bulk-email-preview')): ?>
                            <button wire:click="preview('<?php echo e($row->id); ?>')" class="bg-green-500 hover:bg-orange-500 text-white py-1.5 px-3 rounded-sm text-xs focus:outline-none focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150" title="preview"><i class="far fa-eye"></i></button>
                        <?php endif; ?>
                        <?php if(auth()->user()->can('bulk-email-edit')): ?>
                            <button wire:click="edit('<?php echo e($row->id); ?>')" class="bg-green-500 hover:bg-orange-500 text-white py-1.5 px-3 rounded-sm text-xs focus:outline-none focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150" title="edit"><i class="far fa-edit"></i></button>
                         <?php endif; ?>
                        <?php if(auth()->user()->can('bulk-email-delete')): ?>
                            <button wire:click="deleteReq('<?php echo e($row->id); ?>')" class="bg-red-500 hover:bg-red-700 text-white py-1.5 px-3 rounded-sm delete text-xs focus:outline-none focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150" title="delete"><i class="far fa-trash-alt"></i></button>
                        <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="mt-4">
            <?php echo e($list->links()); ?>

            </div>
        </div>
    </div>
    <style>
        .alert-error {
            background: #ffc4c4;
            border-color: palevioletred;
        }
    </style>
    <?php if($cnfopen): ?>
    <?php echo $__env->make('livewire.common.confirmation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/bulk-email/show.blade.php ENDPATH**/ ?>