@if(auth()->user()->can('country-list'))
<div class="py-4">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="bg-white overflow-x-auto shadow-xl sm:rounded-lg py-5 top-shadow px-0">
            <div class="p-2 text-sm font-bold mx-5 items-center bg-gray-100 flex justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 mb-2 rounded">
            @if(auth()->user()->can('country-active-multiple'))
                <button class="multiple_active bg-green-500 hover:bg-orange-500 text-white  py-2 px-4 rounded-sm my-3 focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150" title="active multiple countries">Submit</button>
            @endif 
            @if(auth()->user()->can('country-active-all'))   
                <button class="all_active mr-2 ml-2 bg-green-500 hover:bg-orange-500 text-white  py-2 px-4 rounded-sm my-3 focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150" title="active all countries">Active all countries</button>
            @endif
            @if(auth()->user()->can('country-deactive-all'))
                <button class="all_deactive bg-green-500 hover:bg-orange-500 text-white  py-2 px-4 rounded-sm my-3 focus:outline-none focus:shadow-outline-orange disabled:opacity-25 transition ease-in-out duration-150" title="deactive all countries">De-active all countries</button>
            @endif
                <!-- search -->
                <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto border border-gray-300">
                    <div class="absolute z-50 pl-3"><i class="fas fa-search text-gray-400 "></i> </div>
                    <input type="text" wire:model="search" class="px-3 py-3 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-full pl-10" placeholder="Search">
                </div>
            </div>
            <table class="table-auto w-full">
                <thead>
                    <tr class="border border-l-0 border-r-0">
                    @if(auth()->user()->can('country-active-multiple'))
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs"><input type="checkbox" id="master" /></th>
                        @endif
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Code</th>
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Name</th>
                        @if(auth()->user()->can('country-currency-change'))
                        <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Currency</th>
                        @endif
                        <!-- <th class="border-b px-4 py-2">Active</th> -->
                    </tr>
                </thead>
                <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
                    <?php $i = 0; ?>
                    @foreach($list as $row)
                    <?php $i++; ?>
                    <tr align="center">
                    @if(auth()->user()->can('country-active-multiple'))
                        <td class="border-b px-4 py-2 text-center"><input type="checkbox" class="active_check" <?php echo ($row->active == "1") ? "checked=checked" : ""; ?> id="del_chk_{{$i}}" id="del_chk" data-id="{{$row['id']}}" data-delete-row-id="{{$i}}" /></td>
                        @endif
                        <td class="border-b px-4 py-2">{{$row->code}}</td>
                        <td class="border-b px-4 py-2">{{$row->name}}</td>
						@if(auth()->user()->can('country-currency-change'))
                        <td class="border-b px-4 py-2">
                            <select id="select_currency_{{$row->id}}" class="select_currency" data-row-id='{{$row->id}}'>
                                <option value="">Select Currency</option>
                                @foreach($currency_list as $r)
                                <?php $selected = ($row->currency_code == $r->short_code) ? "selected='selected'" : ""; ?>
                                <option value="{{$r->short_code}}" <?php echo $selected; ?>><?php echo $r->currency_hex; ?> {{$r->currency_name.' - '.$r->short_code}}</option>
                                @endforeach
                            </select>
                        </td>
						@endif
                        <!-- <td class="border-b px-4 py-2">
                            <div class="relative inline-block w-10 mr-2 align-middle select-none transition duration-200 ease-in">
                                <!-- <input type="checkbox" readonly name="toggle" id="toggle_{{$i}}" data-row-id="{{$i}}" data-id='{{$row->id}}' value="{{$row->active}}" <?php //echo ($row->active == "1") ? "checked=checked" : ""; 
                                                                                                                                                                            ?> class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer switchttr" /> -->
                                <!-- <label for="toggle" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label> -->
                            <!-- </div>
                        </td> -->

                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $list->links() }}
            </div>
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
                    update_country(id, finalvalue);
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

            function update_country(id, val) {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "{{ URL::to('enable_country') }}",
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




        $(document).ready(function() {

            // active Multiple countries.. 

            $('#master').on('click', function(e) {

                if ($(this).is(':checked', true)) {
                    $(".active_check").prop('checked', true);
                } else {
                    $(".active_check").prop('checked', false);
                }
            });

            $("body").on("change", ".select_currency", function(e) {
                //$(".select_currency").on('change', function(e) {
                var _country_id = $(this).attr("data-row-id");
                var _currency_code = $("#select_currency_" + _country_id).val();

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "{{ URL::to('set_country_currency') }}",
                    data: {
                        country_id: _country_id,
                        currency_code: _currency_code
                    },
                    success: function(data) {
                        toastr.success(data.message);
                    }
                });

                //alert($currency_code+'--'+$country_id);
            });

            $('.multiple_active').on('click', function(e) {

                var allVals = [];
                var pageallVals = [];

                $(".active_check:checked").each(function() {
                    allVals.push($(this).attr('data-id'));
                });

                $(".active_check").each(function() { // get all ids in this page record. uncheck id also
                    pageallVals.push($(this).attr('data-id'));
                });


                if (allVals.length <= 0) {
                    // alert("Please select record.");  
                    // toastr.success("Please select country.");

                    var check = confirm("You want to de-active countries?");
                    if (check == true) {
                        var join_selected_values = allVals.join(",");
                        var join_all_page_values = pageallVals.join(",");


                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: "{{ URL::to('active-multiple-countries') }}",
                            data: {
                                ids: join_selected_values,
                                allpageids: join_all_page_values
                            },
                            success: function(data) {
                                // alert(data.message);
                                window.location.reload();
                                toastr.success(data.message);
                            }
                        });

                    }


                } else {

                    var check = confirm("You want to active selected countries?");
                    if (check == true) {
                        var join_selected_values = allVals.join(",");
                        var join_all_page_values = pageallVals.join(",");


                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: "{{ URL::to('active-multiple-countries') }}",
                            data: {
                                ids: join_selected_values,
                                allpageids: join_all_page_values
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



        // active all countries
        $('.all_active').on('click', function(e) {

            var check = confirm("You want to active all countries?");

            if (check == true) {

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "{{ URL::to('active-all-countries') }}",
                    data: {
                        value: 1
                    },
                    success: function(data) {
                        // alert(data.message);
                        window.location.reload(true);
                        toastr.success(data.message);
                    }
                });

            }


        });


        // de-active all countries
        $('.all_deactive').on('click', function(e) {

            var check = confirm("You want to de-active all countries?");

            if (check == true) {

                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "{{ URL::to('active-all-countries') }}",
                    data: {
                        value: 0
                    },
                    success: function(data) {
                        // alert(data.message);
                        window.location.reload(true);
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