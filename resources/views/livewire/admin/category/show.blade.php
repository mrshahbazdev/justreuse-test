@if(auth()->user()->can('category-list'))
<div class="relative md:pt-28 pb-32 pt-12">
  <div class="px-4 md:px-10 mx-auto w-full">
  <div class="bg-white shadow-xl sm:rounded-lg px-0 py-5 top-shadow overflow-x-auto">
    <!--------------- flash begin -------------------->
    @if($message = Session::get('message'))
    <?php $result = Session::get('result');
    $color = ($result == "1") ? "bg-green-700" : "bg-yellow-500";
    if ($result == "1") {
      echo "<script>toastr.success('$message');</script>";
    } else {
      echo "<script>toastr.warning('$message');</script>";
    }
    ?>
    @endif
    <!--------------- flash end -------------------->
    <?php  $dc = App\Common::get_currency_code('');?>
    <div>
      <div class="flex flex-wrap items-center bg-gray-100 p-2 py-3 mb-2 mx-2 xl:mx-5">
        <div class="relative">
          @if($ancestors!="")
          <?php
          echo '<ul class="flex text-orange-500 text-sm lg:text-base">';
          echo "<li><a href='" . URL::to('admin/category/') . "'>Category <i class='fa fa-angle-double-right'></i></a></li>";
          $i = 1;
          $addnew_end_title = "";
          foreach ($ancestors as $r) {
            $totcount = $ancestors->count();
            $catname = "&nbsp;" . $r->title;
            $addnew_end_title = $r->title;
            if ($i != $totcount) {
              $url = URL::to('admin/category/' . $r->uuid . '/subcategories');
              echo '<li><a href="' . $url . '">' . $catname . ' <i class="fa fa-angle-double-right"></i> </a></li>';
            } else {
              echo '<li class="text-black">' . $catname . '</li>';
            }
            $i++;
          }
          echo "</ul>";
          ?>
          @endif
        </div>

        <div class="relative w-full max-w-full flex-grow flex-1 text-right">
          <?php
          $reqId = request()->id;
          if ($reqId == "") {
            $addnew_title = "Add category";
            $addnew_url = "admin/category-add";

            $reorder_title = "Reorder category";
            $reorder_url = "admin/category-reorder";
          } else {
            $subcattitle = end($ancestors)[0]['title'];
            $addnew_title = "Add subcategory → " . $addnew_end_title;
            $addnew_url = "admin/category-add?id=" . $reqId;

            $reorder_title = "Reorder subcategory → " . $addnew_end_title;
            $reorder_url = "admin/category-reorder/" . $reqId;
          }
          ?>
          @if(auth()->user()->can('category-create'))
          <button class="bg-green-500 text-white text-sm px-4 py-2 rounded-sm shadow hover:shadow-md outline-none focus:outline-none hover:bg-orange-500"><a href="{{URL::to($addnew_url)}}"  class="capitalize"><i class="fa fa-plus"></i>
              {{ $addnew_title }}</a></button>
          @endif
          @if(auth()->user()->can('category-reorder'))
          <button class="bg-green-500 text-white text-sm px-4 py-2 rounded-sm shadow hover:shadow-md outline-none focus:outline-none hover:bg-orange-500"><a href="{{URL::to($reorder_url)}}" class="capitalize"><i class="fa fa-bars"></i>
              {{ $reorder_title }}</a></button>
          @endif
        </div>
      </div>


      <div class="bg-white p-5 px-0 pt-0">
        <table class="mt-5 border-collapse border-t border-gray-300 min-w-full">
          <thead>
            <tr class="">
              <th>#</th>
              <th class="p-2 border-b border-gray-300 text-transform: uppercase text-xs">Title</th>
              @if(auth()->user()->can('category-edit'))
              <th class="p-2 border-b border-gray-300 text-transform: uppercase text-xs">Subcategories</th>
              @endif
              @if(auth()->user()->can('category-custom-fields-edit'))
              <th class="p-2 border-b border-gray-300 text-transform: uppercase text-xs">Custom Fields</th>
              @endif
              <th class="p-2 border-b border-gray-300 text-transform: uppercase text-xs">Paid Banner Price/Day</th>
              <th class="p-2 border-b border-gray-300 text-transform: uppercase text-xs">Created_On</th>
              @if(auth()->user()->can('category-edit') || auth()->user()->can('category-delete'))
              <th class="p-2 border-b border-gray-300 text-transform: uppercase text-xs">Action</th>
              <th class="p-2 px-1 border-b border-gray-300 text-transform: uppercase text-xs">Enable/Disable</th>
              <th class="p-2 px-1 border-b border-gray-300 text-transform: uppercase text-xs">Buy Now </th>
              <th class="p-2 px-1 border-b border-gray-300 text-transform: uppercase text-xs">Exchange </th>
              <th class="p-2 px-1 border-b border-gray-300 text-transform: uppercase text-xs">Show in home</th>
              @endif
            </tr>
          </thead>
          <tbody class="border-t grid-style tablelast">
            <?php $i=0;?>
            @foreach($list as $row)
            <?php
            $i++;
            $cfld  = App\Models\TblCustomField::where('cat_id', $row->id)->get();
            $fieldcount = ($cfld->count() == 0) ? "0" : $cfld[0]['field_count']; ?>            
            <tr>
              <td class="p-2 border-b border-gray-300 text-center">{{$i}}</td>
              <td class="p-2 border-b border-gray-300 text-center">{{$row->title}}</td>
              @if(auth()->user()->can('category-edit'))
              <td class="p-2 border-b border-gray-300 text-center">                
                <button class="bg-gray-300 text-xs px-1.5 py-1 rounded-lg bg-gradient-to-r hover:bg-orange-500 hover:text-white"><a href="{{URL::to('admin/category/'.$row->uuid.'/subcategories')}}">{{ $row->descendantsOf($row->id)->toTree($row->id)->count() }} subcategories
                  </a></button>
              </td>
              @endif
              @if(auth()->user()->can('category-custom-fields-edit'))
              <td class="p-2 border-b border-gray-300 text-center">
              <?php if(is_null($row->parent_id)){ ?>
                  -
                <?php } else {?>
                <button class="bg-gray-300 text-xs px-1.5 py-1 rounded-lg hover:bg-orange-500 hover:text-white"><a href="{{ URL::to('admin/custom-fields?id='.$row->uuid) }}">{{ $fieldcount }} custom fields</a></button>
                <?php } ?>  
              </td>
              @endif
              <td class="p-2 border-b border-gray-300 text-center w-30"><?php echo $dc['currency_hex'].$row->paid_banner_price;?></td>
              <td class="p-2 border-b border-gray-300 text-center w-30"><?php echo date('d-m-Y',strtotime($row->created_at));?></td>
              
              <td class="p-2 border-b border-gray-300 text-center">
                @if(auth()->user()->can('category-edit'))
                <button class="items-center px-2 py-1 bg-green-500 border border-transparent rounded-sm text-xs text-white  tracking-widest hover:bg-orange-500 active:bg-orange-500 focus:outline-none focus:bg-orange-500 focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150 mx-1 my-1"><a href="{{URL::to('admin/category-edit?id='.$row->uuid)}}"><i class="fa fa-pencil-square-o" aria-hidden="true"></i><i class="far fa-edit"></i> </a></button>
                @endif
                @if(auth()->user()->can('category-delete'))
                <button wire:click="destroy('{{$row->uuid}}','{{$row->parent_id}}')" onclick="confirm('Are you sure you want to delete? \n') || event.stopImmediatePropagation()" class="items-center px-2 py-1 bg-red-500 border border-transparent rounded-sm text-xs text-white tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150 mx-1 my-1"><i class="fa fa-trash-o" aria-hidden="true"></i><i class="far fa-trash-alt"></i></button>
                @endif
              </td>
              <td class="p-2 px-1 border-b border-gray-300 text-center w-30"><label data-toggle="checkbox-toggle" data-handle-size="12">
                <input type="checkbox" data-id="{{$row->id}}" class="en_dis" value="{{$row->enable_disable}}" <?php echo ($row->enable_disable == "1") ? 'checked' : ''; ?> />
              </label></td>
              <td class="p-2 px-1 border-b border-gray-300 text-center w-30"><label data-toggle="checkbox-toggle" data-handle-size="12">
                <input type="checkbox" data-id="{{$row->id}}" class="buynow" value="{{$row->buynow}}" <?php echo ($row->buynow == "1") ? 'checked' : ''; ?> />
              </label></td>
              <td class="p-2 px-1 border-b border-gray-300 text-center w-30"><label data-toggle="checkbox-toggle" data-handle-size="12">
                <input type="checkbox" data-id="{{$row->id}}" class="exchange" value="{{$row->exchange}}" <?php echo ($row->exchange == "1") ? 'checked' : ''; ?> />
              </label></td>
              <td class="p-2 px-1 border-b border-gray-300 text-center w-30"><label data-toggle="checkbox-toggle" data-handle-size="12">
                <input type="checkbox" data-id="{{$row->id}}" class="toggle_showhome" value="{{$row->show_in_home}}" <?php echo ($row->show_in_home == "1") ? 'checked' : ''; ?> />
              </label></td>
              
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <!-- This example requires Tailwind CSS v2.0+ -->
    <div class="fixed z-10 inset-0 overflow-y-auto" id="cnf_delete" style="display:none;">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
          <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
          <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
            <div class="sm:flex sm:items-start">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                <!-- Heroicon name: exclamation -->
                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
              </div>
              <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                  Delete Category
                </h3>
                <div class="mt-2">
                  <p class="text-sm text-gray-500">
                    Are you sure you want to delete?
                  </p>
                </div>
              </div>
            </div>
          </div>
          <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
            <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
              Deactivate
            </button>
            <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
              Cancel
            </button>
          </div>
        </div>
      </div>
    </div>
	</div>
  </div>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

  $(document).on("click", ".toggle_showhome", function(e) {
    var show_in_home = $(this).attr('value');
   
    var id = $(this).attr('data-id');
   
    var show_in_home_val = "";
    show_in_home_val = (show_in_home == "1") ? "0" : "1";
    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: "{{ URL::to('show-home-category') }}",
      data: {
        show_in_home_val: show_in_home_val,
        id: id
      },
      success: function(data) {
        console.log(data);
        location.reload(true);
      }
    });


  });

  $(document).on("click", ".en_dis", function(e) {
    var enable_disable = $(this).attr('value');
   
    var id = $(this).attr('data-id');
   
    var enable_disable_val = "";
    enable_disable_val = (enable_disable == "1") ? "0" : "1";
    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: "{{ URL::to('enable-disable-category') }}",
      data: {
        enable_disable_val: enable_disable_val,
        id: id
      },
      success: function(data) {
        console.log(data);
        location.reload(true);
      }
    });


  });


  $(document).on("click", ".buynow", function(e) {
    var buynow_enable_disable = $(this).attr('value');
   
    var id = $(this).attr('data-id');
   
    var buynow_enable_disable_val = "";
    buynow_enable_disable_val = (buynow_enable_disable == "1") ? "0" : "1";
    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: "{{ URL::to('buynow-enable-disable-category') }}",
      data: {
        buynow_enable_disable_val: buynow_enable_disable_val,
        id: id
      },
      success: function(data) {
        console.log(data);
        location.reload(true);
      }
    });


  });


  $(document).on("click", ".exchange", function(e) {
    var exchange_enable_disable = $(this).attr('value');
   
    var id = $(this).attr('data-id');
   
    var exchange_enable_disable_val = "";
    exchange_enable_disable_val = (exchange_enable_disable == "1") ? "0" : "1";
    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: "{{ URL::to('exchange-enable-disable-category') }}",
      data: {
        exchange_enable_disable_val: exchange_enable_disable_val,
        id: id
      },
      success: function(data) {
        console.log(data);
        location.reload(true);
      }
    });


  });


  $(function() {
    // simple jQuery toggles using Tailwind
    $('[data-toggle="checkbox-toggle"]:not(.checkbox-toggle-tw)').each(function() {
      if ($(this).find('input[type="checkbox"]').length = 1) {
        var chBoxRounded = $(this).data('rounded');
        chBoxRounded = (chBoxRounded !== undefined) ? chBoxRounded : 'rounded-full';
        var chBoxHandleSize = $(this).data('handle-size');
        chBoxHandleSize = (chBoxHandleSize !== undefined) ? chBoxHandleSize : '20';
        var chBoxHandleColor = $(this).data('handle-color');
        chBoxHandleColor = (chBoxHandleColor !== undefined) ? chBoxHandleColor : 'bg-white';
        var chBoxOffColor = $(this).data('off-color');
        chBoxOffColor = (chBoxOffColor !== undefined) ? chBoxOffColor : 'bg-gray-400';
        var chBoxOnColor = $(this).data('on-color');
        chBoxOnColor = (chBoxOnColor !== undefined) ? chBoxOnColor : 'bg-green-500';
        $(this)
          .attr('data-toggle', 'checkbox-toggle')
          .css({
            'width': (chBoxHandleSize * 2.5) + 6 + 'px',
            'padding': '3px',
            'transition': 'all .25s'
          })
          .addClass(chBoxRounded + ' ' + chBoxOffColor + ' inline-flex cursor-pointer align-middle')
          .append('<b class="' + chBoxHandleColor + ' ' + chBoxRounded + ' shadow" style="width: ' + chBoxHandleSize + 'px; height: ' + chBoxHandleSize + 'px; transition: all .25s" />')
          .find('input')
          .addClass('w-px h-px opacity-0 absolute')
          .attr('tabindex', '-1')
          .on('change', function() {
            if ($(this).is(':checked')) {
              $(this).closest('label').removeClass(chBoxOffColor).addClass(chBoxOnColor).find('b').css('transform', 'translate(' + chBoxHandleSize * 1.5 + 'px, 0)');
            } else {
              $(this).closest('label').removeClass(chBoxOnColor).addClass(chBoxOffColor).find('b').css('transform', '');
            }
            if ($(this).is(':disabled')) {
              $(this).closest('label').addClass('opacity-25 pointer-events-none');
            } else {
              $(this).closest('label').removeClass('opacity-25 pointer-events-none');
            }
          }).trigger('change');
      }
    });
    $('[data-toggle="checkbox-toggle"]').attr('tabindex', '0').on('keydown', function(e) {
      if (e.keyCode == 13 || e.keyCode == 32) {
        e.preventDefault();
        $(this).find('input').click();
      }
    });

    // remote en/disable
    // $('#some-ch3').on('change', function(){
    //   $('#some-ch4').prop('disabled', !$(this).is(':checked')).trigger('change');
    // });
  });
</script>
</div>
@endif