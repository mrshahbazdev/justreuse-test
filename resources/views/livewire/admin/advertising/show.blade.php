@if(auth()->user()->can('advertising-list'))
<div class="py-4">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="bg-white overflow-x-auto shadow-xl sm:rounded-lg p-5 px-0">
            @if (session()->has('message'))
            <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md my-3 top-shadow" role="alert">
                <div class="flex">
                <div>
                    <p class="text-sm">{{ session('message') }}</p>
                </div>
                </div>
            </div>
            @endif
            <table class="table-auto w-full">
                <thead>
                    <tr class="border border-l-0 border-r-0">
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Position</th>
                        @if(auth()->user()->can('advertising-active'))
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Active</th>
                        @endif
                        @if(auth()->user()->can('advertising-edit'))
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Action</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
                    <?php $i = 0; ?>
                    @foreach($list as $row)
                    <?php $i++; ?>
                    <tr align="center">
                        <td class="border-b px-4 py-2">{{$row->position}}</td>
                        @if(auth()->user()->can('advertising-active'))
                        <td class="border-b px-4 py-2">
                            <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                <input type="checkbox" name="toggle" id="toggle_{{$i}}" data-row-id="{{$i}}" data-id='{{$row->id}}' value="{{$row->active}}" <?php echo ($row->active == "1") ? "checked=checked" : ""; ?> class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer switchttr" />
                                <label for="toggle" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                            </div>
                        </td>
                        @endif
                        @if(auth()->user()->can('advertising-edit'))
                        <td class="border-b px-4 py-2">                            
                            <button wire:click="edit('{{ $row->id }}')" class="bg-green-500 hover:bg-orange-500 text-white text-xs py-1.5 px-3 rounded-sm focus:outline-none focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150"><i class="far fa-edit"></i> </button>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $list->links() }}
        </div>
    </div>
    <style>
        .alert-error {
            background: #ffc4c4;
            border-color: palevioletred;
        }

        .toggle-checkbox:checked {
            right: 0;
            border-color: white;
        }

        .toggle-checkbox:checked+.toggle-label {
            background-color: #6875F5;
        }
    </style>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $(document).ready(function() {
            $(".switchttr").click(function(e) {
                var state = $(this).attr('value');
                var id = $(this).attr('data-id');
                var rowid = $(this).attr('data-row-id');
                var state = $("#toggle_" + rowid).val();
                var msg = (state == "0") ? "Activate?" : "Deactivate?";
                if (confirm('Are you sure to ' + msg)) {
                    var finalvalue = (state == "0") ? "1" : "0";
                    update_advertising(id, finalvalue);
                    $(this).attr('value', finalvalue);
                } else {
                    if (state == 0) {
                        $('#toggle').prop('checked', false);
                    } else {
                        $('#toggle').prop('checked', true);
                    }
                    return false;
                }
            });

            function update_advertising(id, val) {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "{{ URL::to('enable_advertising') }}",
                    data: {
                        id: id,
                        active: val
                    },
                    success: function(data) {
                        toastr.success(data.message);
                    }
                });
            }
        });
    </script>
    @if($cnfopen)
    @include('livewire.common.confirmation')
    @endif
</div>
@endif