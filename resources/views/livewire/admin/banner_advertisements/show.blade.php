<div class="relative md:pt-28 pb-32 pt-12">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto mr-3">
        </div>
        <div class="bg-white p-5 rounded-md px-0">

            <!-- search and delete check box -->
            <div class="p-2 text-sm font-bold mx-5 items-center bg-gray-100 flex justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 mb-2 rounded">
                <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto border border-gray-300">
                    <div class="absolute z-50 pl-3"><i class="fas fa-search text-gray-400 "></i> </div>
                    <input type="text" wire:model="search" class="px-3 py-3 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-full pl-10" placeholder="Search by user name">
                </div>
            </div>
            <!-- end search -->
            <div class="double-scroll-div">
                <table class="table-auto w-full">
                    <thead>
                        <tr class="border border-l-0 border-r-0">
                            <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Web Banner</th>
                            <th class="border-b px-4 py-2 text-transform: uppercase text-xs">App Banner</th>
                            <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Posted By</th>
                            <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Status</th>
                            <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Action</th>
                        </tr>
                    </thead>
                    <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
                        @if(auth()->user()->can('banner-ads-list'))
                        @foreach($BannerAds as $BannerAd)
                        <tr>
                            <td class="border-b px-4 py-2 text-center">
                                <img src="<?php echo URL::to('/storage/' . $BannerAd->web_banner); ?>" class="m-auto w-24 h-24 object-cover rounded" />
                            </td>
                            <td class="border-b px-4 py-2 text-center">
                                <img src="<?php echo URL::to('/storage/' . $BannerAd->app_banner); ?>" class="m-auto w-24 h-24 object-cover rounded" />
                            </td>
                            <td class="border-b px-4 py-2 text-center">
                                <p class="text-sm pt-1">Name : <b>{{$BannerAd->user_name}}</b></p>
                                <p class="text-sm pt-1">Email : <b>{{$BannerAd->user_email}}</b></p>
                                <p class="text-sm pt-1">Posted at : <b><?php echo date('d M Y', strtotime($BannerAd->created_at)); ?></b></p>
                            </td>
                            <td class="border-b px-4 py-2 text-center">{{$BannerAd->status}}
                                <?php $check_expired = App\Models\TblBannerAdvertisement::check_is_expired($BannerAd->id); ?>
                                @if($check_expired == 1)
                                <span title="Banner expired"><i class="far fa-check-circle bg-red-400 rounded-full  hover:bg-red-500"></i></span>
                                @elseif($check_expired == 0)
                                <span title="Waiting for approval"><i class="far fa-check-circle bg-yellow-300 rounded-full  hover:bg-yellow-500"></i></span>
                                @elseif($check_expired == 2)
                                <span title="Refunded successfully"><i class="far fa-check-circle bg-pink-300 rounded-full  hover:bg-pink-500"></i></span>
                                @elseif($check_expired == 3)
                                <span title="Currently Running"><i class="far fa-check-circle bg-green-400 rounded-full  hover:bg-green-600"></i></span>
                                @endif
                            </td>
                            @if($BannerAd->status == "pending")
                            <?php
                            $pay = "";
                            if ($BannerAd->payment_type == "stripe") {
                                $pay = "s";
                            } else if ($BannerAd->payment_type == "paypal") {
                                $pay = "p";
                            }
                            ?>
                            <td class="border-b px-4 py-2 text-center">
                                @if(auth()->user()->can('banner-ads-approve'))
                                <button data-created="<?php echo date('Y-m-d', strtotime($BannerAd->created_at)); ?>" data-id="{{$BannerAd->id}}" class="approve items-center px-2 py-1 bg-green-500 border border-transparent rounded-md text-xs text-white tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:shadow-outline-green disabled:opacity-25 transition ease-in-out duration-150">Approve</button>
                                @endif

                                @if(auth()->user()->can('banner-ads-refund'))
                                <button data-id="{{$BannerAd->id}}" data-pay="{{$pay}}" class="refund items-center px-2 py-1 bg-red-500 border border-transparent rounded-md text-xs text-white tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-900 focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150 ml-5">Refund</button>
                                @endif

                                @if(auth()->user()->can('banner-ads-view'))
                                <button class="items-center px-2 py-1 bg-green-500 border border-transparent rounded-md text-xs text-white tracking-widest hover:bg-orange-500 focus:outline-none focus:border-indigo-900 focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150 ml-5" wire:click="view('{{ $BannerAd->id }}')"><i class="fas fa-eye"></i> View</button>
                                @endif
                            </td>
                            @else
                            <td class="border-b px-4 py-2 text-center">
                                 @if(auth()->user()->can('banner-ads-view'))
                                <button class="items-center px-2 py-1 bg-green-500 border border-transparent rounded-md text-xs text-white tracking-widest hover:bg-orange-500 focus:outline-none focus:border-indigo-900 focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150 ml-5" wire:click="view('{{ $BannerAd->id }}')"><i class="fas fa-eye"></i> View</button>
                                @endif
                            </td>
                            @endif
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pt-5">
            {{ $BannerAds->links() }}
        </div>
    </div>
    <div id="overlay"></div>
    <script>
        $("#overlay").hide();
        $('.double-scroll-div').doubleScroll({
            resetOnWindowResize: true
        });
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $(document).on("click", ".approve", function(e) {            
            var id = $(this).attr('data-id');
            var created_at = $(this).attr('data-created');
            var current_date = "<?php echo date('Y-m-d'); ?>";
            var update_date = "0";
            if (current_date > created_at) {
                var check = confirm("The start date of the banner ad will be changed due to late approval?");
                if (check == true) {
                    $("#overlay").css('display', 'block');
                    update_date = "1";
                    approve_banner_ads(id, update_date);
                }
            } else {
                $("#overlay").css('display', 'block');
                approve_banner_ads(id, update_date);
            }
        });

        $(document).on("click", ".refund", function(e) {
            $("#overlay").css('display', 'block');
            var id = $(this).attr('data-id');
            var pay = $(this).attr('data-pay');
            var type = "banner_ads";
            if (pay == "") {
                toastr.warning("Can't refund the payment!");
                window.location.reload();
            } else {
                var check = confirm("Are you sure want to refund the payment?");
                if (check == true) {
                    if (pay == "s") {
                        s_refund(id, type);
                    } else if (pay == "p") {
                        p_refund(id, type);
                    }
                }
            }

        });

        function approve_banner_ads(id, date) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "{{ URL::to('approve-banner-ads') }}",
                data: {
                    id: id,
                    update_date: date,
                },
                success: function(data) {
                    window.location.reload();
                    toastr.success(data.message);
                }
            });
        }

        function s_refund(id, type) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "{{ URL::to('refund_payment_stripe') }}",
                data: {
                    id: id,
                    type: type
                },
                success: function(data) {
                    if (data == "success") {
                        toastr.success("Refunded successfully!");
                        window.location.reload();
                    } else {
                        toastr.warning("Refund has been failed, please try again later!");
                        window.location.reload();
                    }

                }
            });
        }

        function p_refund(id, type) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "{{ URL::to('refund_payment_paypal') }}",
                data: {
                    id: id,
                    type: type
                },
                success: function(data) {
                    if (data == "success") {
                        toastr.success("Refunded successfully!");
                        window.location.reload();
                    } else {
                        toastr.warning("Refund has been failed, please try again later!");
                        window.location.reload();
                    }

                }
            });
        }
    </script>
</div>