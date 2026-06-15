@extends('layouts.frontendother')
@section('content')
<?php 
		$post_info = App\Models\TblPost::where('id', $orderDetail->post_id)->first();
		$get_meta = App\Models\TblOtherpage::get_meta('order-details');
		$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
		$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
		$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
		$final_title = $post_info->title. " | ". $meta_title;
		
		$dir_rtl =  App\Models\Setting::is_dir_rtl();
		$class_dir = ($dir_rtl=="true")?'dir=rtl':"";
		$class_dir_float_rl = ($dir_rtl=="true")?'float-left':'float-right';
		$class_dir_text_lr = ($dir_rtl=="true")?'text-right':'text-left';
		$class_dir_text_rl = ($dir_rtl=="true")?'text-left':'text-right';
		$class_dir_space_r = ($dir_rtl=="true")?'space-x-reverse':"";
		$class_dir_sm_space_r = ($dir_rtl=="true")?'sm:space-x-reverse':"";
		$class_dir_popup_btn = ($dir_rtl=="true")?'':'sm:space-x-reverse';
?>

	@if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description))
        @section('meta_title', $final_title)
        @section('meta_keywords', $meta_keywords)
        @section('meta_description', $meta_description)
	@endif

	<div class="w-full float-left" {{$class_dir}}>
		<div class="w-full float-left">
			<div class="m-auto container px-4">
				<h1 class="text-xl md:text-2xl font-bold text-black my-4 sm:my-6 lg:my-8 uppercase">{{__('p_my_orders_sales.order detail')}}</h1>
				<p class="text-gray-600 text-base text-black mb-6">{{__('p_my_orders_sales.order summary and other info')}}</p>
			</div>
		</div>
		
		
		<div class="w-full float-left">
			<div class="bg-gray-100 h-32 md:h-48 w-full float-left">
				<!-- <div class="m-auto container">
					<h1 class="text-2xl font-bold px-8 w-full">
						@if(!empty($orderDetail))
						@if(Auth::id() == $orderDetail->seller_id)
						<span class="float-right font-normal">
							<a class="text-gray-700 text-md" href="<?php //echo URL::to('/my-buynow/sales'); ?>">{{__('p_my_orders_sales.back')}}</a>
						</span>
						@else
						<span class="float-right font-normal">
							<a class="text-gray-700  text-md" href="<?php //echo URL::to('/my-buynow/orders'); ?>">{{__('p_my_orders_sales.back')}}</a>
						</span>
						@endif
						@endif
					</h1>
				</div> -->
			</div>
		</div>
	
		<div class="w-full float-left">
			<div class="container m-auto px-4">
				<div class="w-full bg-white float-left mb-8 sm:mb-12 md:mb-16 lg:mb-20 relative -mt-12 md:-mt-20 mt-4">
					@if(!empty($orderDetail))
					<?php
					$post_info = App\Models\TblPost::where('id', $orderDetail->post_id)->first();
					$post_img = App\Models\TblChat::getPostImgForList($orderDetail->post_id);
					$post_url = App\Models\TblPost::get_post_slug($post_info->slug);
					$user_info = App\Models\User::where('id', $post_info->user_id)->pluck('name')->first();
					$customer = App\Models\User::where('id', $orderDetail->user_id)->first();
					$currency_symbol = App\Models\TblDefaultCurrency::where('id', $orderDetail->currency_id)->first();
					?>
					<div class="bg-white shadow border border-gray-100 mb-4 px-4 sm:px-6 lg:px-12 xl:px-16 py-4 md:py-6 lg:py-9 relative z-10">
						<div class="w-full float-left">
							<?php
							$refund_status = "fail";
							if ($orderDetail->order_status == "pending") {
								$status_class = "text-yellow-400";
							} else if ($orderDetail->order_status == "cancelled") {
								$status_class = "text-red-500";
								if ($orderDetail->refund_id != "") {
									$refund_status = "success";
								}
							} else if ($orderDetail->order_status == "delivered") {
								$status_class = "text-green-500";
							} else if ($orderDetail->order_status == "shipped") {
								$status_class = "text-pink-500";
							} else if ($orderDetail->order_status == "processing") {
								$status_class = "text-blue-500";
							}
							?>
							<a href="<?php echo URL::to('/order-invoice/' . $orderDetail->orderId); ?>" class="mb-3 text-xs md:text-sm text-center {{$class_dir_float_rl}} font-semibold uppercase bg-green-500 rounded text-white block px-6 py-3 pb-4 hover:bg-white border-2 border-transparent hover:border-green-500 hover:text-green-500 ease-linear transition-all duration-500">{{__('p_my_orders_sales.download invoice')}} PDF</a>
							<div class="flex items-center w-full mb-4 flex-wrap justify-between">
								<p class="text-black text-xs sm:text-sm md:text-base font-semibold w-1/2">{{__('p_my_banner_ads.status')}} :
									<span class="uppercase p-1 {{$status_class}}">{{$orderDetail->order_status}}</span>
									@if($refund_status == "success")
									<span class="uppercase p-1">( {{__('p_my_orders_sales.payment refunded successfully')}}. )</span>
									@endif
								</p>
								<p class="text-black text-xs sm:text-sm md:text-base font-semibold w-1/2 {{$class_dir_text_rl}}">{{__('p_my_orders_sales.order initiated on')}} : {!! date('d M Y', strtotime($orderDetail->created_at)) !!}</p>
							</div>
						</div>
						<div class="w-full md:flex items-center float-left"	>
							<div class="sm:flex items-center m-4 mt-0 text-center sm:text-left sm:space-x-4 {{$class_dir_sm_space_r}}">
								<div class="w-full sm:w-auto mb-2 sm:mb-0">
									<div class="flex items-center flex-wrap h-24 w-24 md:h-20 md:w-20 lg:h-24 lg:w-24 mx-auto">
										<a class="inline-block h-full w-full" href="{{$post_url}}">
											<img class="rounded-full object-cover object-center mx-auto border border-gray-400 h-full max-w-full w-full" src="{{$post_img}}" />
										</a>
									</div>
								</div>
								<div class="text-center sm:{{$class_dir_text_lr}}">
									<a href="{{$post_url}}">
										<h3 class="text-xl mb-2 font-semibold hover:text-green-500">{{$post_info->title}}
											<?php
											$product_condition = App\Models\TblPost::get_product_condition($orderDetail->post_id);
											?>
											@if(!empty($product_condition))
											<span class="bg-gray-500 text-white rounded text-xs p-1 inline-block">{{$product_condition}}</span>
											@endif
										</h3>
									</a>
									@if(Auth::id() == $orderDetail->seller_id)
									<p class="text-base font-semibold text-gray-500 mb-2">{{__('p_profile.name')}} : {{$customer->name}}</p>
									<p class="text-base font-semibold text-gray-500 mb-2">{{__('p_profile.email')}} : {{$customer->email}}</p>
									@else
									<p class="text-base font-semibold text-black mb-2">{{__('p_my_orders_sales.seller')}} : <a class="text-green-500 font-semibold" href="<?php echo URL::to('/seller-profile/' . $orderDetail->seller_id); ?>" target="_blank">{{$user_info}}</a></p>
									<p class="text-sm md:text-base font-semibold text-gray-500 mt-2"> {{$post_info->locality}}</p>
									@endif
								</div>
							</div>
						</div>
						<div class="p-3">
							<div class="w-full overflow-auto mb-4">
								<table class="table-auto w-full">
									<thead>
										<tr>
											<td class="px-2 py-4">
												<p class="text-sm md:text-base font-semibold text-gray-500 mb-1">{{__('p_my_orders_sales.payment type')}}:</p>
											</td>
											<td class="px-2 py-4">
												<p class="text-sm md:text-base font-semibold text-gray-500 mb-1">{{__('p_my_orders_sales.transaction id')}}:</p>
											</td>
											<td class="px-2 py-4">
												<p class="text-sm md:text-base font-semibold text-gray-500 mb-1">{{__('p_my_orders_sales.item amount')}}:</p>
											</td>
											<td class="px-2 py-4">
												<p class="text-sm md:text-base font-semibold text-gray-500 mb-1">{{__('p_my_orders_sales.shipping fee')}}:</p>
											</td>
											<td class="px-2 py-4">
												<p class="text-sm md:text-base font-semibold text-gray-500 mb-1">{{__('p_my_orders_sales.total amount')}}:</p>
											</td>
										</tr>
									</thead>
									<tbody>
										<tr class="border-b border-gray-400 border-0 border-t">
											<td class="px-2 py-4">
												<p class="text-sm md:text-base font-semibold text-black">Stripe</p>
											</td>
											<td class="px-2 py-4">
												<p class="text-sm md:text-base font-semibold text-black">{{$orderDetail->payment_id}}</p>
											</td>
											<td class="px-2 py-4">
												<p class="text-sm md:text-base font-semibold text-black"><?php echo $currency_symbol->currency_hex; ?>{{$orderDetail->price}}</p>
											</td>
											<td class="px-2 py-4">
												<p class="text-sm md:text-base font-semibold text-black"><?php echo $currency_symbol->currency_hex; ?>{{$orderDetail->shipping_fee}}</p>
											</td>
											<td class="px-2 py-4">
												<p class="text-sm md:text-base font-semibold text-black"><?php echo $currency_symbol->currency_hex; ?>{{$orderDetail->total}}</p>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							
							@if($orderDetail->order_status == "shipped" || $orderDetail->order_status == "delivered")
							<?php
							$shippid_date = App\Models\TblCourierInfo::where('order_id', $orderDetail->id)->first();
							?>
							
							<div class="w-full overflow-auto">
								<table class="table-auto w-full">
									<thead>
										<tr class="border-b border-gray-400 border-0">
											<td class="px-2 py-4">
												<p class="text-sm md:text-base font-semibold text-gray-500 mb-1">{{__('p_my_orders_sales.shipped date')}}:</p>
											</td>
											<td class="px-2 py-4">
												<p class="text-sm md:text-base font-semibold text-gray-500 mb-1">{{__('p_my_orders_sales.delivery confirmed date')}}:</p>
											</td>
										</tr>
									</thead>
									<tbody>
										<tr class="border-b border-gray-400 border-0">
											<td class="px-2 py-4">
												<p class="text-sm md:text-base font-semibold text-black"><?php echo date('d M Y', strtotime($shippid_date->shipping_date)); ?></p>
											</td>
											<td class="px-2 py-4">
												@if($orderDetail->order_status == "delivered")
												<p class="text-sm md:text-base font-semibold text-black"><?php echo date('d M Y', strtotime($orderDetail->updated_at)); ?></p>
												@else
												<p class="text-sm md:text-base font-semibold text-black">-</p>
												@endif
											</td>
										</tr>
									</tbody>
								</table>
							</div>
							@endif
							<div class="shipping-address pt-6">
								<p class="text-base text-black font-bold mb-2">{{__('p_my_orders_sales.shipping address')}}:</p>
								<p class="text-sm md:text-base font-semibold text-black mb-1"><span class="text-gray-700">{{__('p_profile.name')}}</span> : {{$orderDetail->shipping_add_name}},</p>
								<p class="text-sm md:text-base font-semibold text-black mb-1"><span class="text-gray-800">{{__('p_profile.phone')}}</span> : {{$orderDetail->shipping_add_phone_number}},</p>
								<p class="text-sm md:text-base font-semibold text-black mb-1">{{$orderDetail->shipping_add_address1}} {{$orderDetail->shipping_add_address2}},</p>
								<p class="text-sm md:text-base font-semibold text-black mb-1">{{$orderDetail->shipping_add_city}} , {{$orderDetail->shipping_add_zipcode}},</p>
								<p class="text-sm md:text-base font-semibold text-black mb-1">{{$orderDetail->shipping_add_state}},</p>
								<p class="text-sm md:text-base font-semibold text-black mb-1">{{$orderDetail->shipping_add_country}}</p>
							</div>
						</div>
						
						
						<div class="p-2 border-b-2 border-gray-300 flex space-x-3 {{$class_dir_space_r}}">
							@if($orderDetail->order_status == "shipped" && (Auth::id() == $orderDetail->seller_id))
							<button class="text-white bg-green-500 text-sm font-semibold uppercase rounded shadow outline-none focus:outline-none border-2 border-green-500 hover:bg-white hover:text-green-500 ease-linear transition-all duration-500 px-4 py-2 pb-3 md:px-6 md:py-3 md:pb-4 view_courier_info" data-popup-id="{{$orderDetail->id}}_courier_info_popup" data-id="{{$orderDetail->id}}">{{__('p_my_orders_sales.edit tracking details')}}</button>
							@elseif($orderDetail->order_status == "delivered" && (Auth::id() == $orderDetail->seller_id))
							<button class="text-white bg-green-500 text-sm font-semibold uppercase rounded shadow outline-none focus:outline-none border-2 border-green-500 hover:bg-white hover:text-green-500 ease-linear transition-all duration-500 px-4 py-2 pb-3 md:px-6 md:py-3 md:pb-4 view_courier_info" data-popup-id="{{$orderDetail->id}}_courier_info_popup" data-id="{{$orderDetail->id}}">{{__('p_my_orders_sales.tracking details')}}</button>
							@elseif($orderDetail->order_status == "shipped" || $orderDetail->order_status == "delivered" && (Auth::id() != $orderDetail->seller_id))
							<button class="text-white bg-green-500 text-sm font-semibold uppercase rounded shadow outline-none focus:outline-none border-2 border-green-500 hover:bg-white hover:text-green-500 ease-linear transition-all duration-500 px-4 py-2 pb-3 md:px-6 md:py-3 md:pb-4 view_courier_info" data-popup-id="{{$orderDetail->id}}_courier_info_popup" data-id="{{$orderDetail->id}}">{{__('p_my_orders_sales.tracking details')}}</button>
							@endif
						</div>
						@else
						<div class="w-full text-center">
							<img src="<?php echo URL::to('/images/nodata.jpg'); ?>" class="m-auto rounded-full" />
							<p class="text-2xl pl-2 pb-1 font-bold">{{__('p_myexchange.no data found')}}!</p>
						</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>

@if(!empty($orderDetail))
@if($orderDetail->order_status == "shipped" || $orderDetail->order_status == "delivered")
<?php
$readonly = "";
if ($orderDetail->order_status == "delivered" && (Auth::id() == $orderDetail->seller_id)) {
    $readonly = "readonly";
} else if ((Auth::id() == $orderDetail->user_id) && $orderDetail->order_status == "shipped" || $orderDetail->order_status == "delivered") {
    $readonly = "readonly";
}
$get_courier = App\Models\TblCourierInfo::where('order_id', $orderDetail->id)->first();
?>
<!--- update courier info start --->
<div class="fixed z-50 inset-0 overflow-y-auto" id="{{$orderDetail->id}}_courier_info_popup" style="display:none">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" {{$class_dir}}>
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <!-- This element is to trick the browser into centering the modal contents. -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
            <div class="bg-white px-6 py-4 sm:px-12 sm:py-8">
				<div class="w-full inline-block {{$class_dir_text_lr}}">
					<h3 class="block text-xl text-black font-semibold mb-2 sm:mb-4" id="modal-headline">
						{{__('p_my_orders_sales.shipping details')}}
					</h3>
					<div class="w-full inline-block mt-2">
						<div class="form-group mb-4">
										  
							<label class="block text-sm md:text-base text-gray-500 font-semibold mb-1">{{__('p_my_orders_sales.shipment date')}} :
								@if($readonly == "")
								<span class="text-red-500">*</span>
								@endif
							</label>
							@if($readonly == "")
							<input type="date" placeholder="Shipment date" value="{{$get_courier->shipping_date}}" class="s_date text-sm md:text-base appearance-none border-l-2 border-gray-400 rounded w-full px-2 py-3 bg-gray-100 text-black leading-tight focus:outline-none focus:shadow-outline mt-1" />
							@else
							<p class="text-sm md:text-base text-black font-semibold">{{$get_courier->shipping_date}}</p>
							@endif
						</div>
						<div class="form-group mb-4">
							<label class="block text-sm md:text-base text-gray-500 font-semibold mb-1">{{__('p_my_orders_sales.shipment method')}} :
								@if($readonly == "")
								<span class="text-red-500">*</span>
								@endif
							</label>
							@if($readonly == "")
							<input type="text" placeholder="Enter the courier" value="{{$get_courier->courier_name}}" class="s_method text-sm  md:text-base appearance-none border-l-2 border-gray-400 rounded w-full px-2 py-3 bg-gray-100  text-black leading-tight focus:outline-none focus:shadow-outline mt-1" />
							@else
							<p class="text-sm md:text-base text-black font-semibold">{{$get_courier->courier_name}}</p>
							@endif
						</div>
						<div class="form-group mb-4">
							<label class="block text-sm md:text-base text-gray-500 font-semibold mb-1">{{__('p_my_orders_sales.shipment service')}} :
								@if($readonly == "")
								<span class="text-red-500">*</span>
								@endif
							</label>
							@if($readonly == "")
							<input type="text" placeholder="Shipping services" value="{{$get_courier->courier_service}}" class="s_service text-sm md:text-base appearance-none border-l-2 border-gray-400 rounded w-full px-2 py-3 bg-gray-100 text-black leading-tight focus:outline-none focus:shadow-outline mt-1" />
							@else
							<p class="text-sm md:text-base text-black font-semibold">{{$get_courier->courier_service}}</p>
							@endif
						</div>
						<div class="form-group mb-4">
							<label class="block text-sm md:text-base text-gray-500 font-semibold mb-1">{{__('p_my_orders_sales.tracking id')}} :
								@if($readonly == "")
								<span class="text-red-500">*</span>
								@endif
							</label>
							@if($readonly == "")
							<input type="text" placeholder="Enter tracking id" value="{{$get_courier->tracking_id}}" class="s_track_id text-sm md:text-base appearance-none border-l-2 border-gray-400 rounded w-full px-2 py-3 bg-gray-100 text-black leading-tight focus:outline-none focus:shadow-outline mt-1" />
							@else
							<p class="text-sm md:text-base text-black font-semibold">{{$get_courier->tracking_id}}</p>
							@endif
						</div>
						<div class="form-group mb-4">
							<label class="block text-sm md:text-base text-gray-500 font-semibold mb-1">{{__('p_my_orders_sales.additional notes')}} : </label>
							@if($readonly == "")
							<textarea rows="3" class="s_add_notes mt-1 text-sm md:text-base appearance-none border-l-2 border-gray-400 rounded w-full px-2 py-3 bg-gray-100 text-black leading-tight focus:outline-none focus:shadow-outline">{{$get_courier->more_info}}</textarea>
							@else
							<p class="text-sm md:text-base text-black font-semibold">{{$get_courier->more_info}}</p>
							@endif
						</div>
						<input type="hidden" class="selected_oid" />
					</div>
					
					<div class="sm:flex sm:flex-row-reverse sm:space-x-3 {{$class_dir_popup_btn}}">
						@if($readonly == "")
						<button type="button" class="w-full inline-block rounded-md border-2 border-green-500 shadow-sm px-4 py-2 pb-3 bg-green-500 text-base font-semibold text-white hover:bg-white hover:text-green-500 focus:outline-none sm:w-auto sm:text-sm transition-all ease-linear duration-500" id="save_courier">
							{{__('messages.submit')}}
						</button>
						@endif
						<button data-popup-id="{{$orderDetail->id}}_courier_info_popup" type="button" class="bg-white cancel_courier mt-3 w-full inline-block rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 pb-3 bg-white text-base font-semibold text-gray-700 hover:bg-gray-200 focus:outline-none sm:w-auto sm:text-sm sm:mt-0 mt-3 sm:w-auto transition-all ease-linear duration-500">
							{{__('post_detail.cancel')}}
						</button>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
<!--- update courier info end --->
@endif
<script>
    $('.view_courier_info').on('click', function(e) {
        e.preventDefault();
        var popup_id = "#" + $(this).attr("data-popup-id");
        $(".selected_oid").val($(this).attr("data-id"));
        $(popup_id).show();
    });
    $('.cancel_courier').on('click', function(e) {
        e.preventDefault();
        var popup_id = $(this).attr("data-popup-id");
        $(popup_id).hide();
        window.location.reload();
    });
    $('#save_courier').on('click', function(e) {
        e.preventDefault();
        var s_date = $('.s_date').val();
        var id = $(".selected_oid").val();
        var s_method = $.trim($('.s_method').val());
        var s_service = $.trim($('.s_service').val());
        var s_track_id = $.trim($('.s_track_id').val());
        var s_add_notes = $.trim($('.s_add_notes').val());
        if (s_date == "" || s_method == "" || s_service == "" || s_track_id == "") {
            toastr.warning('Please fill all the required fields!');
            return false;
        } else {
            update_order_status(status, id, s_date, s_method, s_service, s_track_id, s_add_notes);
        }
    });

    function update_order_status(status, id, s_date, s_method, s_service, s_track_id, s_add_notes) {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: "{{ route('update_order_status') }}",
            data: {
                status: status,
                oid: id,
                s_date: s_date,
                s_method: s_method,
                s_service: s_service,
                s_track_id: s_track_id,
                s_add_notes: s_add_notes
            },
            success: function(data) {
                toastr.success(data.message);
                $('.s_date').val("");
                $('.s_method').val("");
                $('.s_service').val("");
                $('.s_track_id').val("");
                $('.s_add_notes').val("");
                $(".selected_oid").val("");
                location.reload();
            }
        });
    }
</script>
@endif
@endsection