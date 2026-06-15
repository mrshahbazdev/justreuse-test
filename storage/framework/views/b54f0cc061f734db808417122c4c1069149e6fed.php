<?php if(auth()->user()->can('postlist-list')): ?>
<div class="relative md:pt-28 pb-32 pt-12 dashboard_wrap">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto mr-3"></div>
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
            <div class="flex flex-wrap items-center justify-between xl:justify-normal bg-gray-100 px-2 mb-2 mx-2 xl:mx-5">

                <?php if(auth()->user()->can('postlist-delete')): ?>
                <button class="multiple_del bg-green-500 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded-sm my-3"><i class="far fa-trash-alt"></i></button>
                <?php endif; ?>

                <div class="lg:flex flex-row flex-wrap items-center lg:ml-auto mt-0 lg:mt-0 mr-2">
							<select wire:model="deleted_post" class="px-4 py-2 placeholder-gray-400 text-gray-500 rounded text-sm md:text-lg shadow outline-none focus:outline-none focus:shadow-outline w-full border border-gray-300 h-12 lg:ml-4 capitalize">
							<option value="">-- select --</option>
							<option value="deleted_post">Show deleted post</option>
							</select>
						</div>

                <!-- search -->
                <div class="md:flex hidden flex-row flex-wrap items-center border border-gray-300">
                    <div class="absolute z-50 pl-3"><i class="fas fa-search text-gray-400 "></i> </div>
                    <input type="text" wire:model="search" class="px-3 py-3 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-full pl-8" placeholder="Search by post title">
                </div>
            </div>

            <!-- end search -->

            <table class="table-auto w-full">
                <thead>
                    <tr class="border border-l-0 border-r-0">
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs"><input type="checkbox" id="master" /></th>

                        <th class="border-b px-4 py-2 text-left text-transform: uppercase text-xs">Title</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Image</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">City</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Posted On</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Expired On</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Actions</th>
                    </tr>
                </thead>
                <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
                    <?php $i = 0; ?>
                    <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                    $i++;
                    $imgUrlfinal = App\Models\TblChat::getPostImgForList($row['id']);
                    $posted_on = date('d-m-y H:i A', strtotime($row['created_at']));
                    
                    $posturl = App\Models\TblPost::get_post_slug($row["slug"]);
                    //check package expired start
                    $visible_posts = App\Models\TblPost::check_payment_pack_expired($row['id']);

                    // block / unblock post
                    $getblockedPost = App\Models\TblBlockedPost::where('post_id', $row->id)->get();
                        
                        if(count($getblockedPost) == 0 || $getblockedPost[0]->active == 0)
                        {
                            $btnName = "Block post";
                            $confirmName = "block";
                            $className = "bg-red-500 hover:bg-red-700";
                        }else{
                            $btnName = "UnBlock post";
                            $confirmName = "unblock";
                            $className = "bg-green-400 hover:bg-green-600";
                        }
                    ?>

                    <tr>
                        <td class="border-b px-4 py-2"><input type="checkbox" class="del_check" id="del_chk_<?php echo e($i); ?>" id="del_chk" data-id="<?php echo e($row['id']); ?>" data-delete-row-id="<?php echo e($i); ?>" /></td>

                        <td class="border-b px-4 py-2"><a class="" href="<?php echo e($posturl); ?>" target="_blank"><?php echo e($row["title"]); ?>

                                <?php if (empty($visible_posts)) { ?>
                                    <span title="post has been expired"><i class="far fa-check-circle bg-red-500 rounded-full  hover:bg-red-700"></i></span></a>
                        <?php } else { ?>
                            <span title="active post"><i class="far fa-check-circle bg-green-300 rounded-full  hover:bg-green-700"></i></span></a>
                        <?php } ?>

                        <p class="text-gray-700 text-xs pt-1">Post Slug : <?php echo e($row["slug"]); ?></p>
                        <p class="text-gray-700 text-xs pt-1">Posted By: <?php echo e($row["user_name"]); ?></p>
                        </td>

                        <td class="border-b border-gray-300 p-1 text-center">
                            <img width="60px" height="60px" class="inline-block" src="<?php echo e($imgUrlfinal); ?>">
                        </td>
                        <td class="border-b px-4 py-2 text-center whitespace-normal" style="width: 17%;">
                            <p class="text-sm"><?php echo !empty($row["locality"]) ? $row["locality"] : $row["city_name"]; ?></p>
                        </td> 
                        <td class="border-b px-4 py-2 text-center text-sm"><?php echo e($posted_on); ?></td>
                        <?php 
                        
                        $check_post_package = App\Models\TblPost::check_post_expired_admin($row->id);
                            ?>
                        <td class="border-b px-4 py-2 text-center text-sm"> <?php echo e($check_post_package['to_date']); ?></td>
                        <td class="border-b px-4 py-2 text-center">
                            <?php if(auth()->user()->can('postlist-edit')): ?>
                            <button wire:click="edit('<?php echo e($row->id); ?>')" class="items-center px-2 py-1 bg-green-500 border border-transparent rounded-sm text-xs text-white  tracking-widest hover:bg-orange-500 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150 my-1"><i class="far fa-edit"></i> Post</button>
                            <?php endif; ?>
                            <?php if(auth()->user()->can('postlist-edit')): ?>
                            <button wire:click="editUser('<?php echo e($row->user_id); ?>')" class="items-center px-2 py-1 bg-black border border-transparent rounded-sm text-xs text-white tracking-widest hover:bg-gray-500 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150 mx-1 my-1"><i class="far fa-edit"></i> User</a></button>
                            <?php endif; ?>
                            <?php if(auth()->user()->can('postlist-block')): ?>
                            <button wire:click="block_post('<?php echo e($row->id); ?>')" onclick="confirm('Are you sure you want to <?php echo e($confirmName); ?> this post?') || event.stopImmediatePropagation()" class="<?php echo e($className); ?> items-center px-2 py-1 border border-transparent rounded-sm text-xs text-white tracking-widest active:bg-red-900 focus:outline-none focus:border-red-900 focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150 mx-1 my-1"><?php echo e($btnName); ?></a></button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

        </div>

        <div class="pt-5">
            <?php echo e($data->links()); ?>

        </div>

    </div>



</div>




<style>
    tr:nth-child(even) {
        background: #f4f5f7
    }
</style>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function() {

        // delete Multiple posted_add .. 



        $('#master').on('click', function(e) {

            if ($(this).is(':checked', true)) {
                $(".del_check").prop('checked', true);
            } else {
                $(".del_check").prop('checked', false);
            }
        });


        $('.multiple_del').on('click', function(e) {

            var allVals = [];

            $(".del_check:checked").each(function() {
                allVals.push($(this).attr('data-id'));
            });


            if (allVals.length <= 0) {
                toastr.warning("Please select row.");
            } else {

                var check = confirm("You want to delete this row?");
                if (check == true) {
                    var join_selected_values = allVals.join(",");

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: "<?php echo e(route('delete_posted')); ?>",
                        data: {
                            ids: join_selected_values
                        },
                        success: function(data) {
                            toastr.success(data.message);
                            window.location.reload();
                        }
                    });

                }

            }

        });

    });
    // end delete all..
</script>

<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/admin_post/show.blade.php ENDPATH**/ ?>