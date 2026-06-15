<div class="relative md:pt-5 pb-2 pt-12">
    @if ($message = Session::get('message'))
    <div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-yellow-500 alert-{{Session::get('class')}}">
        <span class="text-xl inline-block mr-5 align-middle"><i class="fa fa-bell"></i></span>
        <span class="inline-block align-middle mr-8"><b class="capitalize"></b> {{ $message }}</span>
        <button class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none" onclick="closeAlert(event)"><span>×</span></button>
    </div>
    @endif
    <div class="flex flex-wrap">
        <div class="container m-auto px-5">
            <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 border border-gray-300 rounded bg-gray-100">
                <div class="px-4 py-6">
                    <h4 class="text-2xl pb-3 font-bold px-1"><i class="fa fa-file"></i>&nbsp; {{__('p_myads.my ads')}}
                        <?php
                        $post_methods = App\Models\TblPostMethod::get_active_post_methods();
                        if (!empty($post_methods)) {
                            $check_banner_ads = $post_methods->pluck('name')->toArray();
                            if (in_array("bannerads", $check_banner_ads)) { ?>
                                <a target="_blank" href="<?php echo URL::to('/banner-advertise'); ?>" class="float-right text-sm hover:bg-yellow-600 bg-yellow-500 px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150"><i class="fa fa-plus-circle"></i> {{__('p_my_banner_ads.banner advertisement')}}</a>
                        <?php
                            }
                        }

                        ?>
                    </h4>

                    <hr>
                    <!-- search and delete check box -->
                    <div class="text-sm font-bold w-full w-full mx-autp items-center flex justify-between md:flex-no-wrap flex-wrap md:px-0 px-0 py-3">
                        <div class="pl-0 float-left py-0">
                            <input type="checkbox" id="master" />
                            <label for="master">&nbsp; {{__('p_myads.select all')}} |</label>
                        </div>
                        <button class="multiple_del float-left items-center px-2 py-1 bg-red-500 border border-transparent rounded-md text-xs text-white tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150 ml-5"><i class="fa fa-trash-o mr-1" aria-hidden="true"></i>{{__('p_myads.delete')}}</button>
                        <!-- filter -->
                        <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto mr-3 border border-gray-300">
                            <select wire:model="pid" class="px-3 py-2 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-full">
                                <option value="">[-- {{__('p_myads.select package name')}} --]</option>
                                <?php foreach ($packages_list as $packages_list) { ?>
                                    <option value="<?php echo $packages_list->id; ?>"><?php echo $packages_list->name ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <!-- search -->
                        <div class="md:flex hidden flex-row flex-wrap items-center mr-3 border border-gray-300">
                            <input type="text" wire:model="search" class="px-3 py-2 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-full" placeholder="{{__('p_myads.search here')}}">
                        </div>
                    </div>
                    <!-- end -->

                    <div class="w-full overflow-auto m-0 p-0 bg-white">
                        @if(count($list) > 0)
                        <table class="pflex-auto w-full">
                            <thead>
                                <tr>
                                    <th class="p-2 border border-gray-300"></th>
                                    <th class="p-2 border border-gray-300">{{__('p_myads.photo')}}</th>
                                    <th class="p-2 border border-gray-300 text-left">{{__('p_myads.ads details')}}</th>
                                    <th class="p-2 border border-gray-300 whitespace-nowrap">{{__('p_myads.price')}}</th>
                                    <th class="p-2 border border-gray-300 whitespace-nowrap">{{__('p_myads.ads type')}}</th>
                                    <th class="p-2 border border-gray-300 whitespace-nowrap text-left">{{__('p_myads.expiration date')}}</th>
                                    <th class="p-2 border border-gray-300 whitespace-nowrap">{{__('p_myads.sold status')}}</th>
                                    <th class="p-2 border border-gray-300 whitespace-nowrap">{{__('p_myads.action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 0; ?>
                                @foreach($list as $row)
                                <?php
                                $i++;
                                $chkimgUrlfinal = App\Models\TblChat::getPostImgForList($row['id']);
                                if ($chkimgUrlfinal != "" && $chkimgUrlfinal != null) {
                                    $imgUrlfinal = $chkimgUrlfinal;
                                } else {
                                    $imgUrlfinal = App\Models\TblChat::getPostImg($row['id']);
                                }
                                // alter image view code end
                                $posted_on = date('d M Y', strtotime($row['created_at']));
                                $viewcount = App\Models\TblPostInsight::views_count($row['id']);
                                $check_post_package = App\Models\TblPost::check_post_expired($row['id']);

                                if ($check_post_package['expired'] == "Expired") {
                                    $exp_class = "bg-gray-200";
                                } else {
                                    $exp_class = "";
                                }
                                $slug = App\Models\TblPost::get_post_slug($row["slug"]);
                                ?>
                                <tr class="<?php echo $exp_class; ?>">
                                    <td class="p-2 border border-gray-300 text-center"><input type="checkbox" class="del_check" id="del_chk_{{$i}}" id="del_chk" data-id="{{$row['id']}}" data-delete-row-id="{{$i}}" /> </td>
                                    <td class="p-2 border border-gray-300" align="center">
                                        <img style="max-width: 30%;" class="" src="{{$imgUrlfinal}}">
                                    </td>
                                    <td class="p-2 border border-gray-300 text-left">
                                        <p>
                                            <a href="{{$slug}}"><strong class="text-indigo-600 text-sm">{{$row["title"]}}</strong></a>
                                        </p>
                                        <p><strong class="text-xs"><i class="fa fa-eye"></i> {{__('p_myads.views')}} - {{ $viewcount}}</strong></p>
                                    </td>
                                    <td class="p-2 border border-gray-300 text-center" style="white-space:nowrap">
                                        <p class="text-red-700 mb-2">{{$check_post_package['expired']}}</p>
                                        <?php
                                        $settings = App\Models\Setting::get_logos();
                                        $slected_currency = !empty($row['currency_id']) ? $row['currency_id'] : $settings['default_currency'];
                                        $currency_symbol = App\Models\TblPost::get_post_currency($slected_currency);
                                        $default_currency = App\Models\TblPost::get_post_currency($settings['default_currency']);
                                        ?>
                                        <?php echo $currency_symbol[0]; ?> {{$row["price"]}}
                                    </td>
                                    <td class="p-2 border border-gray-300 text-center">
                                        <?php if ($check_post_package['ads_type'] != "free") { ?>
                                            <?php if ($check_post_package['expired'] == "Expired") { ?>
                                                <?php
                                                $currentUserId = auth()->user()->id;
                                                $urlnew = URL::to('/selectPackage?post=' . $row['id'] . '');
                                                ?>
                                                <button class="p-1 bg-yellow-500 text-sm font-bold border rounded">
                                                    <a href="{{$urlnew}}">{{__('post_detail.sell fast')}}</a>
                                                </button>
                                            <?php } else { ?>
                                                <span class="font-bold">
                                                    <?php if ($check_post_package['is_bulk'] != 0) { ?>
                                                        {{__('p_myads.bulk package')}} -
                                                    <?php } ?>
                                                    {{$check_post_package['ads_type']}}
                                                </span>
                                                <p class="text-sm">
                                                    {{$check_post_package['bulk_type']}}
                                                </p>
                                                <p>{{__('p_myads.price')}} : <?php echo $default_currency[0]; ?>{{$check_post_package['package_price']}}</p>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <?php
                                            $check_pack_info = App\Models\Package::where('lft', 1)->first();
                                            ?>
                                            <?php if (($check_post_package['expired'] == "Expired") && ($check_post_package['post_count'] < $check_pack_info->single_pack_limit)) { ?>
                                                <button class="republish p-1 bg-yellow-500 text-sm font-bold border rounded" data-id='<?php echo $row['id']; ?>'>{{__('p_myads.re publish')}}
                                                </button>
                                            <?php } ?>
                                            <?php
                                            $currentUserId = auth()->user()->id;
                                            $urlnew = URL::to('/selectPackage?post=' . $row['id'] . '');
                                            ?>
                                            <button class="p-1 bg-yellow-500 text-sm font-bold border rounded">
                                                <a href="{{$urlnew}}">{{__('post_detail.sell fast')}}</a>
                                            </button>
                                        <?php } ?>
                                    </td>
                                    <td class="p-2 border border-gray-300 text-left" style="white-space:nowrap">
                                        <p class="text-sm pb-2">
                                            {{__('p_myads.from')}} : <strong class="text-black text-sm">
                                                <?php echo $check_post_package['from_date'] ?>
                                            </strong>
                                        </p>
                                        <p class="text-sm">
                                            {{__('p_myads.to')}} :
                                            <strong class="text-black text-sm">
                                                <?php echo $check_post_package['to_date'] ?>
                                            </strong>
                                        </p>
                                    </td>
                                    <td class="p-2 border border-gray-300 text-center" style="white-space:nowrap">
                                        @if($check_post_package['expired'] == "Expired")
                                        <button class="p-2 bg-red-500 text-sm font-bold border rounded text-white cursor-default">{{__('p_myads.expired')}}</button>
                                        @elseif($row['sold_status'] == 0)
                                        <button data-status="mark_sold" data-id="{{$row['id']}}" class="republish p-2 bg-yellow-500 text-sm font-bold border rounded flex hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:shadow-outline-yellow disabled:opacity-25 transition ease-in-out duration-150"><img src="<?php echo URL::to('/images/sold-out.png'); ?>" class="mr-1 mt-1" />{{__('p_myads.mark as sold')}}</button>
                                        @else
                                        <?php
                                        $check_buynow_order = App\Models\TblBuynowOrder::where('post_id', $row['id'])->orderBy('id', 'desc')->pluck('order_status')->first();
                                        ?>
                                        @if(!empty($check_buynow_order))
                                        @if($check_buynow_order != "delivered")
                                        <button class="no-records cursor-not-allowed p-2 bg-yellow-500 text-sm font-bold border rounded flex hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:shadow-outline-yellow disabled:opacity-25 transition ease-in-out duration-150"><img src="<?php echo URL::to('/images/sold-out.png'); ?>" class="mr-1 mt-1" />{{__('p_myads.back to sale')}}</button>
                                        @else
                                        <button data-status="mark_sale" data-id="{{$row['id']}}" class="republish p-2 bg-yellow-500 text-sm font-bold border rounded flex hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:shadow-outline-yellow disabled:opacity-25 transition ease-in-out duration-150"><img src="<?php echo URL::to('/images/coupon.png'); ?>" class="mr-1 mt-1" />{{__('p_myads.back to sale')}}</button>
                                        @endif
                                        @else
                                        <button data-status="mark_sale" data-id="{{$row['id']}}" class="republish p-2 bg-yellow-500 text-sm font-bold border rounded flex hover:bg-yellow-700 active:bg-yellow-900 focus:outline-none focus:shadow-outline-yellow disabled:opacity-25 transition ease-in-out duration-150"><img src="<?php echo URL::to('/images/coupon.png'); ?>" class="mr-1 mt-1" />{{__('p_myads.back to sale')}}</button>
                                        @endif
                                        @endif
                                    </td>
                                    <td class="p-2 border border-gray-300 text-center" style="white-space:nowrap">
                                        <button class="items-center px-2 py-1 bg-indigo-500 border border-transparent rounded-md text-xs text-white  tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150"><i class="fa fa-eye mr-1" aria-hidden="true"></i><a href="{{$slug}}" target="_blank">{{__('p_myads.view')}} </a></button>
                                        <button class="items-center px-2 py-1 bg-indigo-500 border border-transparent rounded-md text-xs text-white  tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150 ml-5"><i class="fa fa-pencil-square-o mr-1" aria-hidden="true"></i><a href="{{URL::to('/post-edit?id='.$row->id)}}">{{__('p_myads.edit')}}</a></button>
                                        <button wire:click="destroy('{{$row['id']}}')" onclick="confirm('Are you sure you want to delete?') || event.stopImmediatePropagation()" class="items-center px-2 py-1 bg-red-500 border border-transparent rounded-md text-xs text-white tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150 ml-5"><i class="fa fa-trash-o mr-1" aria-hidden="true"></i>{{__('p_myads.delete')}}</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        @else
                        <img src="<?php echo URL::to('/images/nodata.jpg'); ?>" class="m-auto" />
                        <p class="text-2xl pl-2 pb-4 font-bold text-center">{{__('p_myexchange.no data found')}}!</p>
                        @endif
                    </div>
                </div>
                <div class="px-4 py-2 border border-gray-200 sm:px-6 mt-4 mm">
                    {{ $list->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .alert-error span {
        color: #000;
    }

    .alert-error {
        background: #ffc4c4;
        border-top: 2px solid palevioletred;
    }
</style>
<!--------------- flash begin -------------------->
<script>
    function closeAlert(event) {
        let element = event.target;
        while (element.nodeName !== "BUTTON") {
            element = element.parentNode;
        }
        element.parentNode.parentNode.removeChild(element.parentNode);
    }
</script>
<!--------------- flash end -------------------->
<script>
    /* Republish post */
    $(document).ready(function() {
        $(".republish").click(function(e) {
            var id = $(this).attr('data-id');
            var status = $(this).attr('data-status');
            if ((status != "") && (status != "undefined") && (status != null)) {
                if (confirm('Are you sure to set the post as an sold?')) {
                    republish_post(id, status);
                } else {
                    return false;
                }
            } else {
                if (confirm('Are you sure to republish the post?')) {
                    republish_post(id, status);
                } else {
                    return false;
                }
            }
        });

        function republish_post(id, status) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "{{ URL::to('republish_post') }}",
                data: {
                    id: id,
                    status: status
                },
                success: function(data) {
                    toastr.success(data.message);
                    location.reload();
                }
            });
        }
    });

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(".no-records").on("click", function() {
        toastr.warning("Before proceed to sale, please cancel all your sale orders!");
        return false;
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
                toastr.warning("Please select row!");
            } else {
                var check = confirm("You want to delete this row?");
                if (check == true) {
                    var join_selected_values = allVals.join(",");
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: "{{ route('delete_posted') }}",
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