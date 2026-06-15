<?php if(auth()->user()->can('payment-list')): ?>
<div class="relative md:pt-28 pb-32 pt-12">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto mr-3">
        </div>
        <div class="bg-white p-5 rounded-md px-0 overflow-x-auto">
            <?php if(session()->has('message')): ?>
            <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert">
                <div class="flex">
                    <div>
                        <p class="text-sm"><?php echo e(session('message')); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- search and delete check box -->
            <div class="p-2 text-sm font-bold mx-5 items-center bg-gray-100 flex justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 mb-2 rounded">
                <div class="md:flex hidden flex-row flex-wrap items-center  border border-gray-300">
                    <button wire:click="$set('orders_toggle', 'bulk_orders')" class="bg-green-500 hover:bg-orange-500 text-white p-3  rounded focus:outline-none focus:border-orange-500 focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150" value="bulk_orders">Bulk Payment Orders</button>
                </div>

                <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-3 border border-gray-300">
                    <button wire:click="$set('orders_toggle', 'payment_orders')" class="bg-green-500 hover:bg-orange-500 text-white p-3  rounded focus:outline-none focus:border-orange-500 focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150" value="payment_orders">Payment Orders</button>
                </div>

                <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto border border-gray-300">
                    <div class="absolute z-50 pl-3"><i class="fas fa-search text-gray-400 "></i> </div>
                    <input type="text" wire:model="search" class="px-3 py-3 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-full pl-10" placeholder="Search, post or name">
                </div>
            </div>
            <!-- end search -->
            <?php  $title ='';?>
            <?php $__currentLoopData = $payment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(!empty($row->title) && isset($row->title)): ?>
            <?php  $title ='1';?>
            <?php else: ?>
            <?php  $title ='0';?>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <table class="table-auto w-full text-sm">
                <thead>
                    <tr class="border border-l-0 border-r-0">
                    <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Payment Orders Id</th>
                        <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Payment Details</th>
                        <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Package</th>
                        <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">User Info</th>
                       
                        <?php if($title == 1): ?>
                        <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Post Title</th>
                        <?php endif; ?>
                       
                        <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Created at</th>
                    </tr>
                </thead>
                <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
                    <?php $__currentLoopData = $payment; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                    $posted_on = date('d M Y h:i A', strtotime($row->created_at));
                    $start_date = date('d M Y h:i A', strtotime($row->start_date));
                    $end_date = date('d M Y h:i A', strtotime($row->end_date));
                    $visible_posts = App\Models\TblPost::check_payment_pack_expired($row->post_id); //check package expired start
                    $currency = App\Models\TblDefaultCurrency::where('id', $row->currency_id)->pluck('currency_hex')->first();
                    ?>
                    <tr>
                    <td class="border-b px-4 py-2 text-center">
                            <p><?php echo e($row->order_id); ?></p>
                            
                        </td>
                        <td class="border-b px-4 py-2 text-left">
                            <p class="text-gray-700 text-xs pt-1"><b>Type</b>: <?php echo e($row->payment_type); ?></p>
                            <p class="text-gray-700 text-xs pt-1"><b>Id:</b> <?php echo e($row->s_payment_id); ?></p>
                            <p class="text-gray-700 text-xs pt-1"><b>Start Date</b>: <?php echo e($start_date); ?></p>
                            <p class="text-gray-700 text-xs pt-1"><b>End Date</b>: <?php echo e($end_date); ?></p>
                            <p class="text-gray-700 text-xs pt-1"><b>Validity</b>: <?php echo e($row->live_days); ?> Days</p>
                        </td>
                        <td class="border-b px-4 py-2 text-left">
                            <p class="text-orange-500 font-bold text-xs pt-1"><?php echo e($row->package_name); ?> / <?php echo e($row->duration); ?> days </p>
                            <p class="text-xs pt-1">Coupon: <?php echo e($row->coupon_code); ?></p>
                            <p class="text-xs pt-1">Paid: <?php echo $currency; ?> <?php echo e($row->package_amount); ?></p>
                        </td>
                        <td class="border-b px-4 py-2 text-center">
                            <p>Name : <?php echo e($row->name); ?></p>
                            <p>Email : <?php echo e($row->email); ?></p>
                        </td>
                        <?php if(!empty($row->title) && isset($row->title)): ?>
                        <td class="border-b px-4 py-2 text-center">
                            <?php $slug = App\Models\TblPost::get_post_slug($row->slug); ?>
                            <a href="<?php echo e($slug); ?>" class="text-black-500 underline" target="_blank"><?php echo e($row->title); ?>

                                <?php if (empty($visible_posts)) { ?>
                                    <span title="post has been expired"><i class="far fa-check-circle bg-red-500 rounded-full  hover:bg-red-700"></i></span></a>
                        <?php } else { ?>
                            <span title="active post"><i class="far fa-check-circle bg-green-300 rounded-full  hover:bg-green-700"></i></span></a>
                        <?php } ?>
                        </a>
                        </td>
                        <?php endif; ?>
                        <td class="border-b px-4 py-2 text-center text-xs"><?php echo e($posted_on); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
        <div class="pt-5">
            <?php echo e($payment->links()); ?>

        </div>
    </div>
</div>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/payments/show.blade.php ENDPATH**/ ?>