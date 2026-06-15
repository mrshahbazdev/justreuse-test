<?php if(auth()->user()->can('reportad-list')): ?>
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
      <div class="flex flex-wrap items-center justify-between bg-gray-100 pl-2 pr-3 mb-2 mx-2 xl:mx-5">
        <?php if(auth()->user()->can('reportad-delete')): ?>
        <button class="multiple_del bg-green-500 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded-sm my-3"><i class="far fa-trash-alt"></i></button>
        <?php endif; ?>
        <!-- search -->
        <div class="flex flex-row flex-wrap items-center lg:ml-auto border border-gray-300 relative z-0">
          <div class="absolute z-10 pl-3"><i class="fas fa-search text-gray-400 "></i> </div>
          <input type="text" wire:model="search" class="px-3 py-3 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-full pl-8" placeholder="Search post or type">
        </div>
      </div>

      <!-- end search -->

      <table class="table-auto w-full">
        <thead>
          <tr class="border">
            <th class="border-b px-4 py-2 text-transform: uppercase text-xs"><input type="checkbox" id="master" /></th>
            <th class="border-b px-4 py-2 text-left text-transform: uppercase text-xs">User Name</th>
            <th class="border-b px-4 py-2 text-left  text-transform: uppercase text-xs">Post Title</th>
            <th class="border-b px-4 py-2 text-left text-transform: uppercase text-xs">Report type</th>
            <th class="border-b px-4 py-2 text-left whitespace-nowrap text-transform: uppercase text-xs">Posted date</th>
            <th class="border-b px-4 py-2 text-left text-transform: uppercase text-xs">Comment</th>
            <?php if(auth()->user()->can('reportad-view')): ?>
            <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">View</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody class="border border-b-0 grid-style tablelast">
          <?php $i = 0; ?>
          <?php $__currentLoopData = $record; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
          $fav_style = "bg-red-600";
          if ($row->view == "1") {
            $fav_style = "bg-green-500";
          }
          //check package expired start
          $visible_posts = App\Models\TblPost::check_payment_pack_expired($row->post_id);
          $i++;
          ?>
          <tr>
            <td class="border-b px-4 py-2 text-center">
              <input type="checkbox" class="del_check" id="del_chk_<?php echo e($i); ?>" id="del_chk" data-id="<?php echo e($row->id); ?>" data-delete-row-id="<?php echo e($i); ?>" />
            </td>
            <td class="border-b px-4 py-2">
              <?php echo e($row->user_name); ?>

              <p class="text-xs">Email : <?php echo e($row->user_email); ?></p>
            </td>
            <td class="border-b px-4 py-2" style="width:26%">
              <?php $slug = App\Models\TblPost::get_post_slug($row->slug); ?>
              <a class="underline" href="<?php echo e($slug); ?>" target="_blank">
                <?php echo e($row->title); ?>

                <?php if (empty($visible_posts)) { ?>
                  <span title="post has been expired"><i class="far fa-check-circle bg-red-500 rounded-full  hover:bg-red-700"></i></span></a>
            <?php } else { ?>
              <span title="active post"><i class="far fa-check-circle bg-green-300 rounded-full  hover:bg-green-700"></i></span></a>
            <?php } ?>
            </a>
            </td>
            <td class="border-b px-4 py-2"><?php echo e($row->report_name); ?></td>
            <td class="border-b px-4 py-2">
              <p class="text-sm"><?php echo date('d-m-y', strtotime($row->created_at)); ?></p>
            </td>
            <td class="border-b px-4 py-2"><?php echo e(Str::limit($row->comment, 10)); ?></td>
            <?php if(auth()->user()->can('reportad-view')): ?>
            <td class="border-b px-4 py-2 text-center">
              <button class="items-center px-2 py-1 border border-transparent rounded-md text-xs text-white  tracking-widest  focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150 <?php echo $fav_style; ?>" data-report-status="<?php echo e($row->view); ?>" data-report-id="<?php echo e($row['id']); ?>" id="view_report"><i class="far fa-eye"></i></button>
            </td>
            <?php endif; ?>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
    <!-- pagination -->
    <div class="pt-5">
      <?php echo e($record->links()); ?>

    </div>
    <!-- view comment -->
    <div class="fixed z-10 inset-0 overflow-y-auto" id="report" style="display:none">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="">
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                  Item report
                </h3>
                <div class="mt-2">
                  <div>
                    <label class="block text-gray-700 text-sm font-bold mb-2">Comment:</label>
                    <p id="fill_comment">test</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="button" id="cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
    <!-- end comment -->
  </div>
</div>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  //view comment
  $(document).on("click", "#view_report", function(e) {
    var id = $(this).attr('data-report-id');
    document.querySelector("#report").style.display = "block";
    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: "<?php echo e(URL::to('report-comment')); ?>",
      data: {
        id: id
      },
      success: function(data) {
        $("#fill_comment").text(data.message);
      }
    });
  });
  //cancel popup click
  $('#cancel').on('click', function(e) {
    document.querySelector("#report").style.display = "none";
  });
  // end comment view
  //multiple delete onclick
  $(document).ready(function() {
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
        toastr.warning("Please select record.");
      } else {
        var check = confirm("You want to delete selected rows?");
        if (check == true) {
          var join_selected_values = allVals.join(",");
          $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "<?php echo e(URL::to('delete-report')); ?>",
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
</script>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/report/show.blade.php ENDPATH**/ ?>