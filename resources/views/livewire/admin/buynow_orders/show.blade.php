<div class="relative md:pt-28 pb-32 pt-12">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="md:flex hidden flex-row flex-wrap items-center lg:ml-auto mr-3">
        </div>
        <div class="bg-white p-5 rounded-md px-0 overflow-x-auto">

            <!-- search and delete check box -->
            <div class="p-2 text-sm font-bold mx-5 items-center bg-gray-100 flex justify-between md:flex-no-wrap flex-wrap pl-2 pr-2 mb-2 rounded">
                <div class="flex flex-row flex-wrap items-center lg:ml-auto border border-gray-300 relative z-0">
                    <div class="absolute z-10 pl-3"><i class="fas fa-search text-gray-400 "></i> </div>
                    <input type="text" wire:model="search" class="px-3 py-3 placeholder-gray-400 text-gray-700 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:shadow-outline w-full pl-10" placeholder="Search by user name">
                </div>
            </div>
            <!-- end search -->
            <div class="double-scroll-div">
                <table class="table-auto w-full">
                    <thead>
                        <tr class="border border-l-0 border-r-0">
                            <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Buyer Info</th>
                            <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Seller Info</th>
                            <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Post Info</th>
                            <th class="border-b px-4 py-2 text-center text-transform: uppercase text-xs">Order Info</th>
                            <th class="border-b px-4 py-2 text-transform: uppercase text-xs">Download Invoice</th>
                        </tr>
                    </thead>
                    <tbody class="border border-b-0 border-l-0 border-r-0 tablelast">
                        @if(auth()->user()->can('buynow-orders-list'))
                        @foreach($BuynowOrders as $BuynowOrder)
                        <tr>
                            <td class="border-b px-4 py-2 text-left">
                                <p class="text-sm pt-1">Name : <b>{{$BuynowOrder->user_name}}</b></p>
                                <p class="text-sm pt-1">Email : <b>{{$BuynowOrder->user_email}}</b></p>
                            </td>
                            <td class="border-b px-4 py-2 text-left">
                                <?php
                                $seller = App\Models\User::where('id', $BuynowOrder->seller_id)->first();
								if(!empty($seller)){
                                    $seller_name = $seller->name;
                                    $seller_email = $seller->email;
                                }else{
                                    $seller_name = "-";
                                    $seller_email = "-";
                                }
                                ?>
                                <p class="text-sm pt-1">Name : <b>{{$seller_name}}</b></p>
                                <p class="text-sm pt-1">Email : <b>{{$seller_email}}</b></p>
                            </td>
                            <td class="border-b px-4 py-2 text-left">
                                <p class="text-sm pt-1">Post Name : <b>{{$BuynowOrder->post_title}}</b></p>
                                <p class="text-sm pt-1">Total Amount : <b><?php echo $BuynowOrder->currency_hex; ?>{{$BuynowOrder->total}}</b></p>
                            </td>
                            <td class="border-b px-4 py-2 text-left">
                                <p class="text-sm pt-1">status : <b>{{$BuynowOrder->order_status}}</b></p>
                                <p class="text-sm pt-1">Order Id : <b>{{$BuynowOrder->orderId}}</b></p>
                                <p class="text-sm pt-1">Delivered Date : <b>
                                        <?php
                                        if ($BuynowOrder->order_status == "delivered") {
                                            echo date('d M Y', strtotime($BuynowOrder->updated_at));
                                        } else {
                                            echo "-";
                                        }
                                        ?>
                                    </b></p>
                            </td>
                            <td class="border-b px-4 py-2 text-center">
                                <a class="items-center px-2 py-1 bg-green-500 border border-transparent rounded-md text-xs text-white tracking-widest hover:bg-orange-500 focus:outline-none focus:border-indigo-900 focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150 ml-5" href="<?php echo URL::to('/admin/view-order-invoice/' . $BuynowOrder->orderId); ?>"><i class="fa fa-eye"></i></a>

                                <a class="items-center px-2 py-1 bg-green-500 border border-transparent rounded-md text-xs text-white tracking-widest hover:bg-orange-500 focus:outline-none focus:border-indigo-900 focus:shadow-outline-indigo disabled:opacity-25 transition ease-in-out duration-150 ml-5" href="<?php echo URL::to('/order-invoice/' . $BuynowOrder->orderId); ?>"><i class="fa fa-download"></i></a>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pt-5">
            {{ $BuynowOrders->links() }}
        </div>
    </div>
    <script>
        $('.double-scroll-div').doubleScroll({
            resetOnWindowResize: true
        });
    </script>
</div>