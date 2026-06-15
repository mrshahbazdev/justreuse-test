<div class="relative md:pt-28 pb-32 pt-12">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="bg-white overflow-x-auto shadow-xl sm:rounded-lg p-5 px-0">
            <?php if(session()->has('message')): ?>
            <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3 alert-<?php echo e(Session::get('class')); ?>" role="alert">
                <div class="flex">
                    <div>
                        <p class="text-sm"><?php echo e(session('message')); ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>


            <!-- search with button -->
            <div class="rounded text-sm font-bold items-center bg-gray-100 flex py-2 justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 mb-4 mx-5">
                <?php if(auth()->user()->can('user-create')): ?>
                <button wire:click="create()" class="bg-green-500 hover:bg-orange-500 text-white py-2 px-4 rounded-sm my-3">New User</button>
                <?php endif; ?>
                <div class="flex items-center gap-4">
                    <div class="select_verification">
                        <select wire:model="get_role" class="select_role cursor-pointer border border-gray-300 text-base text-gray-600 rounded-md outline-none focus:ring-blue-500 focus:border-blue-500 block w-full pl-4 px-10 py-2.5">
                            <option value="">-- Select --</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <!-- search -->
                    <div class="flex  flex-row flex-wrap items-center  rounded-md border border-gray-300 relative z-0">
                        <div class="absolute z-10 pl-3"><i class="fas fa-search text-gray-400 "></i> </div>
                        <input type="text" wire:model="search" class="px-3 py-3 placeholder-gray-700 text-gray-700 relative bg-white bg-white rounded-md text-sm outline-none focus:outline-none focus:shadow-outline w-full pl-8" placeholder="Search Email,Mobile">
                    </div>
                </div>
                <!-- 
                                <div class="select_verification" >
                                    <select  id="select_role"class="select_role cursor-pointer border border-gray-300 text-base text-gray-600 rounded-md outline-none focus:ring-blue-500 focus:border-blue-500 block w-full pl-4 px-10 py-2.5 ">
                                        <option value="User" id="user">User</option>
                                        <option value="Admin" id="admin">Admin</option>
                                      </select>
                                </div> -->





            </div>
            <!-- end search -->
            <?php if($insertMode): ?>
            <?php echo $__env->make('livewire.admin.user.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>
            <?php if($updateMode): ?>
            <?php echo $__env->make('livewire.admin.user.update', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <?php endif; ?>

            <table class="w-full">
                <thead>
                    <tr class="border border-l-0 border-r-0">
                        <th class="border-b px-4 py-2 text-left">Name</th>
                        <th class="border-b px-4 py-2 text-left">Email</th>
                        <th class="border-b px-4 py-2 text-left">Phone</th>
                        <th class="border-b px-4 py-2">Role</th>
                        <th class="border-b px-4 py-2"></th>

                        <?php if(auth()->user()->can('user-edit') || auth()->user()->can('user-delete')): ?>
                        <th class="border-b px-4 py-2">Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="border border-l-0 border-r-0 border-b-0 grid-style tablelast">
                    <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                    <tr>
                        <?php $__currentLoopData = $user->getRoleNames(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

                        <td class="border-b px-4 py-2"><?php echo e($user->name); ?>

                            <p class="text-gray-700 text-xs pt-1">Registerd at: <?php echo date('d-m-Y h:i a', strtotime($user->created_at)); ?></p>
                        </td>
                        <td class="border-b px-4 py-2 whitespace-normal"><?php echo e($user->email); ?></td>
                        <td class="border-b px-4 py-2"><?php echo e(!empty($user->phone) ?$user->phone :"-"); ?></td>
                        <td class="border-b px-8 py-2 text-center">
                            <?php if(!empty($user->getRoleNames())): ?>
                            <label class="text-xs font-semibold inline-block py-1 px-2 rounded-full text-pink-600 bg-pink-200  last:mr-0 mr-1"><?php echo e($v); ?></label>
                            <?php endif; ?>
                        </td>
                        <td class="border-b px-4 py-2">

                            <select id="select_active_<?php echo e($user->id); ?>" class="select_active" data-row-id='<?php echo e($user->id); ?>'>
                                <option value="0" <?php echo ($user->is_blocked == 0) ? "selected='selected'" : ""; ?>> Active </option>
                                <option value="1" <?php echo ($user->is_blocked == 1) ? "selected='selected'" : ""; ?>> Block </option>
                            </select>

                        </td>

                        <td class="border-b px-4 py-2">
                            <?php if(auth()->user()->can('user-edit')): ?>
                            <button wire:click="edit('<?php echo e($user->id); ?>')" class="bg-green-500 hover:bg-orange-500 text-white px-2 py-1 text-xs rounded-sm focus:outline-none focus:border-orange-500 focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-edit"></i> </button>
                            <?php endif; ?>
                            <?php if(auth()->user()->can('user-delete')): ?>
                            <?php
                            $disable_opacity = "";
                            $disabled = "";
                            if (strtolower($v) == "superadmin") {
                                $disable_opacity = "disabled:opacity-50";
                                $disabled = "disabled";
                            }
                            ?>
                            <button wire:click="deleteReq('<?php echo e($user->id); ?>')" class="bg-red-500 hover:bg-red-700 text-white px-2 py-1 text-xs rounded-sm delete <?php echo e($disable_opacity); ?> focus:outline-none focus:border-red-900 focus:shadow-outline-red transition ease-in-out duration-150" <?php echo e($disabled); ?>><i class="far fa-trash-alt"></i></button>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
            <div class="mt-4 px-3">
                <?php echo e($users->links()); ?>

            </div>

        </div>
    </div>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });


        $("body").on("change", ".select_active", function(e) {
            //$(".select_currency").on('change', function(e) {
            var user_id = $(this).attr("data-row-id");
            var value = $("#select_active_" + user_id).val();

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo e(route('user-blocked')); ?>",
                data: {
                    blocked: value,
                    id: user_id
                },
                success: function(data) {
                    // alert(data.message);
                    toastr.success(data.message);
                }
            });

        });


        //     $("body").on("change", ".select_role", function(e){
        // //$(".select_currency").on('change', function(e) {
        //     var selected_value = $(this).val();
        //     console.log(selected_value);
        //     // var value = $("#select_active_"+user_id).val();

        //         $.ajax({
        //             type:'POST',
        //             dataType: 'json',
        //             url:"",
        //             data:{selected_value:selected_value},
        //             success:function(data){
        //                     // alert(data.message);
        //                     toastr.success(data.message);
        //             }
        //         });

        // });






        $("#select_role").on("click", searchFunction);

        function searchFunction() {
            window.location = '/get-role/' + $("#select_role option:selected").val();
        }
    </script>



    <?php if($cnfopen): ?>
    <?php echo $__env->make('livewire.common.confirmation', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/user/show.blade.php ENDPATH**/ ?>