<?php if(auth()->user()->can('reportuser-list')): ?>
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
      <div class="text-sm font-bold mx-5 items-center bg-gray-100 flex justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 mb-2 rounded">
        <?php if(auth()->user()->can('reportuser-delete')): ?>
        <button class="multiple_del bg-green-500 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded-sm my-3 focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-trash-alt"></i></button>
        <?php endif; ?>
        <!-- search -->
        <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto border border-gray-300">
          <div class="absolute z-50 pl-3"><i class="fas fa-search text-gray-400 "></i> </div>
          <input type="text" wire:model="search" class="px-3 py-3 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-full pl-10" placeholder="Search, name or type">
        </div>
      </div>
      <!-- end search -->

      <table class="table-auto w-full">
        <thead>
          <tr class="border border-l-0 border-r-0">
            <th class="border-b px-4 py-2 text-transform: uppercase text-xs"><input type="checkbox" id="master" /></th>
            <th class="border-b px-4 py-2 text-transform: uppercase text-xs">User Name</th>
            <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Reported User Name</th>
            <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Report type</th>
            <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Comment</th>
            <?php if(auth()->user()->can('reportuser-view')): ?>
            <th class="border-b px-4 py-2 text-transform: uppercase text-xs">View</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
          <?php $i = 0; ?>
          <?php $__currentLoopData = $record; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php
          $fav_style = "bg-red-600";
          if ($row->view == "1") {
            $fav_style = "bg-green-500";
          }
          $i++; ?>
          <tr>
            <td class="border-b px-4 py-2 text-center"><input type="checkbox" class="del_check" id="del_chk_<?php echo e($i); ?>" id="del_chk" data-id="<?php echo e($row['id']); ?>" data-delete-row-id="<?php echo e($i); ?>" /></td>
            <td class="border-b px-4 py-2 text-center"><?php echo e($row->user_name); ?></td>
            <td class="border-b px-4 py-2 text-center"><?php echo e($row->reported_user_name); ?></td>
            <td class="border-b px-4 py-2 text-center"><?php echo e($row->report_name); ?></td>
            <td class="border-b px-4 py-2 text-center"><?php echo e(Str::limit($row->comment, 25)); ?></td>
            <?php if(auth()->user()->can('reportuser-view')): ?>
            <td class="border-b px-4 py-2 text-center">              
              <button class="items-center px-2 py-1 border border-transparent rounded-md text-xs text-white  tracking-widest  focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150 <?php echo $fav_style; ?>" data-report-id="<?php echo e($row['id']); ?>" id="view_report"><i class="far fa-eye"></i></button>
            </td>
            <?php endif; ?>
          </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
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
                <h3 class="text-lg leading-6 font-bold text-gray-900" id="modal-headline">Item report</h3>
                <div class="mt-2">

                  <div>
                    <label class="block text-gray-700 text-sm font-medium mb-2">Comment:</label>
                    <p id="fill_comment">test</p>
                  </div>

                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="button" id="cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
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
      url: "<?php echo e(URL::to('report-user-comment')); ?>",
      data: {
        id: id
      },
      success: function(data) {
        $("#fill_comment").text(data.message);
      }
    });
  });



  $('#cancel').on('click', function(e) {

    document.querySelector("#report").style.display = "none";

  });

  // end comment view


  $(document).ready(function() {

    // delete Multiple reports.. 

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
        alert("Please select record.");
      } else {

        var check = confirm("You want to delete selected rows?");
        if (check == true) {
          var join_selected_values = allVals.join(",");

          $.ajax({
            type: 'POST',
            dataType: 'json',
            cahe:false,
            url: "<?php echo e(URL::to('delete-user-report')); ?>",
            data: {
              ids: join_selected_values
            },
            success: function(data) {

                if(data.success==true)
                {
                  toastr.success(data.message);
                  window.location.reload(true);
                }
                else{
                  toastr.error(data.message);
                }
              
            }
          });

        }

      }


    });


  });
</script>

<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/report_user/show.blade.php ENDPATH**/ ?>