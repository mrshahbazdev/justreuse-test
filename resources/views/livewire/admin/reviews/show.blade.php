@if(auth()->user()->can('review-list'))
<div class="relative md:pt-28 pb-32 pt-12">
  <div class="px-4 md:px-10 mx-auto w-full">
    <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto mr-3">

    </div>
    <div class="bg-white p-5 rounded-md px-0 overflow-x-auto">
      @if (session()->has('message'))
      <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3" role="alert">
        <div class="flex">
          <div>
            <p class="text-sm">{{ session('message') }}</p>
          </div>
        </div>
      </div>
      @endif

      <!-- search and delete check box -->
      <div class="text-sm font-bold mx-5 items-center bg-gray-100 flex justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 mb-2 rounded">

        @if(auth()->user()->can('review-delete'))
        <button class="multiple_del bg-green-500 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded-sm my-3 focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-trash-alt"></i></button>
        @endif
        <!-- search -->
        <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto border border-gray-300">
          <div class="absolute z-50 pl-3"><i class="fas fa-search text-gray-400 "></i> </div>
          <input type="text" wire:model="search" class="px-3 py-3 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-full pl-10" placeholder="Search post or name">
        </div>
      </div>
      <!-- end search -->

      <table class="table-auto w-full text-sm">
        <thead>
          <tr class="border border-l-0 border-r-0">
            <th class="border-b px-4 py-2 text-transform: uppercase text-xs"><input type="checkbox" id="master" /></th>
            <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Post Details</th>
            <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Comment</th>
            <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Ratings</th>
            <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Approved</th>
            <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Created_at</th>
            <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Actions</th>
          </tr>
        </thead>
        <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
          <?php $i = 0; ?>
          @foreach($review_list as $row)
          <?php
          $fav_style = "bg-red-600";
          $is_approved = "approve";

          $review_style = "bg-red-600";
          if ($row->approved == "1") {
            $fav_style = "bg-green-500";
            $is_approved = "approved";
          }

          if ($row->view == "1") {
            $review_style = "bg-green-500";
          }
          $slug = App\Models\TblPost::get_post_slug($row->slug);
          $visible_posts = App\Models\TblPost::check_payment_pack_expired($row->post_id); //check package expired start
          $i++;
          ?>
          <tr>
            <td class="border-b px-4 py-2 text-center"><input type="checkbox" class="del_check" id="del_chk_{{$i}}" id="del_chk" data-id="{{$row['id']}}" data-delete-row-id="{{$i}}" /></td>
            <td class="border-b px-4 py-2">
              <a href="{{$slug}}" target="_blank" class="underline">{{$row->title}}
                <?php if (empty($visible_posts)) { ?>
                  <span title="post has been expired"><i class="far fa-check-circle bg-red-500 rounded-full  hover:bg-red-700"></i></span></a>
                <?php } else { ?>
                  <span title="active post"><i class="far fa-check-circle bg-green-300 rounded-full  hover:bg-green-700"></i></span></a>
                <?php } ?>
            </a>
            <p class="text-black text-xs">Posted by: <b>{{$row->name}}</b></p>
            </td>

            <td class="border-b px-4 py-2 text-center">{{ Str::limit($row->comment, 25) }}</td>
            <td class="border-b px-4 py-2 text-center">{{$row->ratings}} star</td>
            <td class="border-b px-4 py-2 text-center">
              @if(auth()->user()->can('review-approve'))
              <button class="view_review items-center px-2 py-1 border border-transparent rounded-md text-xs text-white  tracking-widest  focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150 <?php echo $fav_style; ?>" data-val="{{$row->approved}}" data-id="{{$row['id']}}">{{$is_approved}}</button>
              @endif
            </td>
            <td class="border-b px-4 py-2 text-center">{{ $row->created_at->format('d-m-Y h:i a') }}</td>
            <td class="border-b px-4 py-2 text-center">
              @if(auth()->user()->can('review-view'))
              <button class="view_report items-center px-2 py-1 border border-transparent rounded-md text-xs text-white  tracking-widest  focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150 <?php echo $review_style; ?>" data-report-id="{{$row['id']}}"><i class="far fa-eye"></i></button>
              @endif

              @if(auth()->user()->can('review-delete-single'))
              <button wire:click="deleteReq('{{ $row->id }}')" class="items-center px-2 py-1 bg-red-500 border border-transparent rounded-md text-xs text-white tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150 ml-5"><i class="far fa-trash-alt"></i></a></button>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="pt-5">
      {{ $review_list->links() }}
    </div>
    @if($cnfopen)
    @include('livewire.common.confirmation')
    @endif
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
                <h3 class="text-lg leading-6  text-gray-900 font-bold" id="modal-headline">Item report</h3>
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

  $(document).on("click", ".view_report", function(e) {
    var id = $(this).attr('data-report-id');

    document.querySelector("#report").style.display = "block";

    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: "{{ URL::to('review-comment') }}",
      data: {
        id: id
      },
      success: function(data) {
        if(data.success==true)
        {
          $("#fill_comment").text(data.message);
        }
        toastr.success(data.message);
        
        
      }
    });
  });



  $('#cancel').on('click', function(e) {

    document.querySelector("#report").style.display = "none";
    //window.location.reload();

  });

  // end comment view





  //approved-ads

  $(document).on("click", ".view_review", function(e) {
    var id = $(this).attr('data-id');
    var value = $(this).attr('data-val');

    var approve_val = "";
    if (value == "1") {
      var approve_val = "0";
    } else {
      var approve_val = "1";
    }


    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: "{{ URL::to('approve-review') }}",
      data: {
        id: id,
        value: approve_val
      },
      success: function(data) {
        // alert(data.message);
        if(data.success==true)
        {
          window.location.reload();
        }
        toastr.success(data.message);
      }
    });
  });






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
        // alert("Please select record.");  
        toastr.success("Please select record.");
      } else {

        var check = confirm("You want to delete selected rows?");
        if (check == true) {
          var join_selected_values = allVals.join(",");

          $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "{{ URL::to('delete-review') }}",
            data: {
              ids: join_selected_values
            },
            success: function(data) {
              // alert(data.message);
              window.location.reload();
              toastr.success(data.message);
            }
          });

        }

      }


    });


  });
</script>

@endif