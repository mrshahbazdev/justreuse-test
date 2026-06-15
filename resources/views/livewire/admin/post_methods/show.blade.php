@if(auth()->user()->can('post-method-list'))

<div class="relative md:pt-28 pb-32 pt-12">
    <div class="px-4 md:px-10 mx-auto w-full">
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
        <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto mr-3">
        </div>
        <div class="bg-white p-5 rounded-md px-0 overflow-x-auto">
            <table class="table-auto w-full">
                <thead>
                    <tr class="border border-l-0 border-r-0">
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Name</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Description</th>
                        @if(auth()->user()->can('post-method-active'))
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Active</th>
                        @endif
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Created_at</th>
                    </tr>
                </thead>
                <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
                    <?php $i = 0; ?>
                    @foreach($method_list as $row)
                    <?php $i++; ?>
                    <tr>
                        <td class="border-b px-4 py-2 text-center">{{$row->display_name}}</td>
                        <td class="border-b px-4 py-2 text-center">{{$row->description}}</td>
                        @if(auth()->user()->can('post-method-active'))
                        <td class="border-b px-4 py-2 text-center">
                            <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                <input type="checkbox" name="toggle" id="toggle_{{$i}}" data-row-id="{{$i}}" data-id='{{$row->id}}' value="{{$row->active}}" <?php echo ($row->active == "1") ? "checked=checked" : ""; ?> class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer switchttr" />
                                <label for="toggle" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
                            </div>
                        </td>
                        @endif
                        <td class="border-b px-4 py-2 text-center">{{ $row->created_at->format('d-m-Y h:i a') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pt-5">
            <style>
                /* CHECKBOX TOGGLE SWITCH */
                /* @apply rules for documentation, these do not work as inline style */
                .toggle-checkbox:checked {
                    right: 0;
                    border-color: white;
                }

                .toggle-checkbox:checked+.toggle-label {
                    --bg-opacity: 1;
                    background-color: rgba(47, 119, 64, var(--bg-opacity));
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
                        var state = ($(this).is(":checked") == true) ? "1" : "0";
                        var msg = ($(this).val() == "0") ? "Activate?" : "Deactivate?";
                        if (confirm('Are you sure to ' + msg)) {
                            update_package(id, state);
                        } else {
                            if (state == 0) {
                                $('#toggle').prop('checked', false);
                            } else {
                                $('#toggle').prop('checked', true);
                            }
                            return false;
                        }
                    });

                    function update_package(id, val) {
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: "{{ URL::to('enable_post_method') }}",
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
        </div>
    </div>
</div>
@endif