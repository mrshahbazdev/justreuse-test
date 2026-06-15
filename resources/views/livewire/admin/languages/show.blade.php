@if(auth()->user()->can('language-list'))
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
      <div class="text-sm font-bold items-center flex justify-between md:flex-no-wrap flex-wrap md:px-2 px-2 py-0 mx-5 bg-gray-100 mb-2">
        @if(auth()->user()->can('language-create'))
        <button wire:click="create()" class="bg-green-500 hover:bg-orange-500 text-white font-bold py-2 px-4 rounded my-3">Create Language</button>
        @endif
       
      </div> 

 <!-- search -->
      <!-- end search -->

      <table class="table-auto w-full text-sm">
        <thead>
          <tr class="border border-l-0 border-r-0">
            <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Code</th>
            <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Language Name</th>
            <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Direction</th>
            <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Active</th>
            <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Default</th>
            <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Actions</th>
          </tr>
        </thead>
        <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
          @foreach($language as $row)
          <tr>
            <td class="border-b px-4 py-2 text-center">{{$row->abbr}}</td>
            <td class="border-b px-4 py-2 text-center">{{$row->name}}</td>
            <td class="border-b px-4 py-2 text-center">{{$row->direction}}</td>
            <td class="border-b px-4 py-2 text-center">
              @if(auth()->user()->can('language-edit'))
              <label data-toggle="checkbox-toggle" data-handle-size="12">
                <input type="checkbox" data-id="{{$row->id}}" class="toggle_active" value="{{$row->active}}" <?php echo ($row->active == "1") ? 'checked' : ''; ?> />
              </label>
              @endif
            </td>
            <td class="border-b px-4 py-2 text-center">
              @if(auth()->user()->can('language-edit'))
              <label data-toggle="checkbox-toggle" data-handle-size="12">
                <input type="checkbox" data-id="{{$row->id}}" class="toggle_default" value="{{$row->default}}" <?php echo ($row->default == "1") ? 'checked' : ''; ?> />
              </label>
              @endif
            </td>
            <td class="border-b px-4 py-2 text-center">
              @if(auth()->user()->can('language-edit'))
              <button class="items-center px-2 py-1 bg-green-500 border border-transparent rounded-sm text-xs text-white  tracking-widest hover:bg-orange-500 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150"><a href="{{URL::to('/admin/languages-edit?id='.$row->id)}}"><i class="far fa-edit"></i></a></button>
              @endif
              @if(auth()->user()->can('language-delete'))
              <button wire:click="deleteReq('{{ $row->id }}')" class="items-center px-2 py-1 bg-red-500 border border-transparent rounded-sm text-xs text-white tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150 ml-5"><i class="far fa-trash-alt"></i></a></button>
              @endif
              @if(auth()->user()->can('language-language'))
              <button  class="items-center px-2 py-1 bg-green-500 border border-transparent rounded-sm text-xs text-white  tracking-widest hover:bg-orange-500 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150"><a href="{{URL::to('/admin/sublanguage-show?locale='.$row->locale)}}"><i class="fa fa-language"></i></a></button>
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="pt-5">
      {{ $language->links() }}
    </div>
  </div>
  @if($cnfopen)
  @include('livewire.common.confirmation')
  @endif
</div>
<script>
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });
  $(document).on("click", ".toggle_active", function(e) {
    var active_id = $(this).attr('value');
    var id = $(this).attr('data-id');
    var active_val = "";
    active_val = (active_id == "1") ? "0" : "1";

    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: "{{ URL::to('active-language') }}",
      data: {
        active_val: active_val,
        id: id
      },
      success: function(data) {
        location.reload(true);
      }
    });

  });

  $(document).on("click", ".toggle_default", function(e) {
    var default_id = $(this).attr('value');
    var id = $(this).attr('data-id');

    var default_val = "";
    default_val = (default_id == "1") ? "0" : "1";
    $.ajax({
      type: 'POST',
      dataType: 'json',
      url: "{{ URL::to('default-language') }}",
      data: {
        default_val: default_val,
        id: id
      },
      success: function(data) {
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



@endif