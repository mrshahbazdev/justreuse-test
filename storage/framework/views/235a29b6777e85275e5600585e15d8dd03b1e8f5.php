<?php if(auth()->user()->can('package-list')): ?>
<div class="py-4">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="bg-white overflow-x-auto top-shadow rounded px-0 py-5">
            <?php
            //$settings = App\Models\Setting::get_logos();
            //$currency_symbol = App\Models\TblPost::get_post_currency($settings['default_currency']);

            $currency_symbol = App\Models\Setting::get_admin_default_currency();
            ?>
            <?php if(auth()->user()->can('package-create')): ?>
            <?php if (!empty($currency_symbol)) { ?>
                <div class="text-sm font-bold mx-5 items-center bg-gray-100 flex justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 mb-2 rounded">
                    <button wire:click="create()" class="bg-green-500 hover:bg-orange-500 text-white py-2 px-4 rounded-sm my-3 focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150">Create New Package</button>
                </div>
            <?php } else { ?>
                <div class="text-sm font-bold mx-5 items-center bg-gray-100 flex justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 mb-2 rounded">
                    <button class="not-allowed cursor-not-allowed bg-green-500 hover:bg-orange-500 text-white py-2 px-4 rounded my-3 focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150">Create New Package</button>
                </div>
            <?php } ?>
            <?php endif; ?>
            <?php if(session()->has('message')): ?>
            <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert">
                <div class="flex">
                    <div>
                        <p class="text-sm"><?php echo e(session('message')); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <table class="table-auto w-full text-md">
                <thead>
                    <tr class="border border-l-0 border-r-0">
                        <th class="border-b px-4 py-2 text-left text-left text-transform: uppercase text-xs">Name</th>
                        <th class="border-b px-4 py-2 text-left text-transform: uppercase text-xs">Price</th>
                        <th class="border-b px-4 py-2 text-left text-transform: uppercase text-xs">Is-Bulk Ads</th>
                        <th class="border-b px-4 py-2 text-left text-transform: uppercase text-xs">Duration</th>
                        <th class="border-b px-4 py-2 text-left text-transform: uppercase text-xs">Currency</th>
                        <th class="border-b px-4 py-2 text-left text-transform: uppercase text-xs">Status</th>
                        <?php if(auth()->user()->can('package-edit') || auth()->user()->can('package-delete')): ?>
                        <th class="border-b px-4 py-2 text-left text-transform: uppercase text-xs">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
                    <?php $__currentLoopData = $packages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr align="center">
                        <td class="border-b px-4 py-2 text-sm text-left"><?php echo e($row->name); ?>

                            <span class="text-xs font-semibold inline-block py-0.5 px-2 rounded text-pink-600 bg-pink-200 ml-2 mr-2"><?php echo e(str_replace('_', ' ', $row->ad_type)); ?></span>
                        </td>
                        <td class="border-b px-4 py-2"><?php echo e($row->price); ?></td>
                        <td class="border-b px-4 py-2">
                            <?php if($row->bulk_ads == 1): ?>
                            <p class="text-sm"><?php echo e($row->bulk_limit); ?> Ads</p>
                            <?php
                            $bulk_type_title = "";
                            if ($row->bulk_type == "1") {
                                $bulk_type_title = "Based On Package";
                            }
                            if ($row->bulk_type == "2") {
                                $bulk_type_title = "Based On Item";
                            }
                            ?>
                            <p class="text-sm"><?php echo e($bulk_type_title); ?></p>
                            <?php endif; ?>
                        </td>
                        <td class="border-b px-4 py-2"><?php echo e($row->duration); ?> days</td>
                        <td class="border-b px-4 py-2"><?php echo $currency_symbol['currency_hex']; ?></td>
                        <td class="border-b px-4 py-2">
                            <?php if ($row->active == 1) {
                                echo "Active";
                            } else {
                                echo "Inactive";
                            } ?>
                        </td>
                        <td class="border-b px-4 py-2">
                            <?php if(auth()->user()->can('package-edit')): ?>
                            <button wire:click="edit('<?php echo e($row->id); ?>')" class=" bg-green-500 hover:bg-orange-500 text-white py-1 px-2 rounded-sm text-xs focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-edit"></i></button>
                            <?php endif; ?>
                            <?php if(auth()->user()->can('package-delete')): ?>
                            <?php if ($row->lft != 1) { ?>
                                <button wire:click="deleteReq('<?php echo e($row->id); ?>')" class=" bg-red-500 hover:bg-red-700 text-white py-1 px-2 rounded delete text-xs focus:outline-none focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-trash-alt"></i></button>
                            <?php } ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="mt-4">
                <?php echo e($packages->links()); ?>

            </div>
        </div>
    </div>
    <?php if($cnfopen): ?>
    <?php echo $__env->make('livewire.common.confirmation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
    <script>
        $(document).on("click", ".not-allowed", function(e) {
            toastr.warning("Please set the default currency in the application settings!");
        });
    </script>
</div>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/package/show.blade.php ENDPATH**/ ?>