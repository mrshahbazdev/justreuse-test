	@extends('layouts.frontendother')
	@section('content')
	
	
	<?php
		
		$dir_rtl =  App\Models\Setting::is_dir_rtl();
		$class_dir = ($dir_rtl=="true")?'dir=rtl':"";
		$class_dir_float_lr = ($dir_rtl=="true")?'float-right':'float-left';
		$class_dir_float_rl = ($dir_rtl=="true")?'float-left':'float-right';
		$class_dir_padding_lr = ($dir_rtl=="true")?'pr-2 sm:pr-4':'pl-2 sm:pl-4';
		$class_dir_text_lr = ($dir_rtl=="true")?'text-right':'text-left';
		$class_dir_popup_btn = ($dir_rtl=="true")?'':'sm:space-x-reverse';
		$class_dir_space_r = ($dir_rtl=="true")?'space-x-reverse':'';
		
	?>
	
	
	<div class="w-full float-left" {{$class_dir}}>
		<div class="w-full float-left">
			<div class="m-auto container px-4">
				<h1 class="text-xl md:text-2xl font-bold text-black mt-4 mb-2 sm:mt-6 sm:mb-3 lg:ml-0 uppercase">{{__('post_detail.review order')}}</h1>
				<p class="text-gray-600 text-base">{{__('post_detail.your order summary and other info')}}</p>
			</div>
		</div>
		
		<div class="w-full float-left">
			<div class="m-auto container px-4">
				<div class="w-full float-left relative mx-auto bg-white rounded mt-4">
		<?php
		$slug = !empty(request()->segment('2')) ? request()->segment('2') : "";
		$post_info = App\Models\TblPost::where('slug', $slug)->first();
		$seller = App\Models\User::where('id', $post_info->user_id)->pluck('name')->first();
		$post_img = App\Models\TblChat::getPostImgForList($post_info->id);
		$currency_symbol = App\Models\TblCurrency::where('id', $post_info->currency_id)->first();
		$total = $post_info->shipping_rate + $post_info->price;

		// meta datas
		$get_meta = App\Models\TblOtherpage::get_meta('revieworder');
		$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
		$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
		$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
		$meta_final_title = $post_info->title. " | ". $meta_title;
		?>

		@if(!empty($meta_final_title) && !empty($meta_keywords) && !empty($meta_description))
			@section('meta_title', $meta_final_title)
			@section('meta_keywords', $meta_keywords)
			@section('meta_description', $meta_description)
		@endif

		<div class="w-full float-left">
			@if(($post_info->instant_buy == 1) && (Auth::id() != $post_info->user_id))
			<!-- address list -->
			<div x-data={show:true} class="rounded-sm cout_address_list shadow w-full float-left">
				<div class="px-6 py-4 w-full flex flex-wrap justify-between" id="headingOne">
					<button @click="show=!show" class="block focus:outline-none py-4 md:py-2 pb-4 text-lg md:text-xl lg:text-2xl text-gray-500 font-bold w-full md:w-9/12 flex" type="button">
						1. {{__('post_detail.select address')}}
					</button>

					<button class="w-full md:w-auto text-lg md:text-xl lg:text-2xl px-4 sm:px-6 lg:px-8 py-1 pb-2 sm:py-2 sm:pb-3 font-semibold border rounded-lg cursor-pointer outline-none focus:outline-none shipping_address_popup border-2 border-green-500 text-green-500 hover:text-white hover:bg-green-500 transition-all ease-linear duration-500">{{__('post_detail.add address')}}</button>
					
				</div>
				<div x-show="show" class="leading-normal w-full float-left">
					<div class="px-6">
						@if(!empty($addresses[0]))
						<div class="flex flex-wrap">
							<?php $i=0; ?>
							@foreach($addresses as $address)
							<?php
							$i++;
							$default_address = "border-2 border-gray-100 ";
							$order_class = "";
							if ($address->default_address == 1) {
								$default_address = "border-l-2 border-gray-500";
								$order_class = "order-first";
							}
							?>
							<div class="border-0 pb-6 rounded w-full items-center {{$order_class}}">
								<div class="rounded-xl bg-gray-100 m-1 mb-4 sm:mb-6 lg:mb-10 items-center w-full lg:w-5/12 {{$default_address}}">
									<div class="">
										<div class="overflow-auto py-3 px-3 border-l-4 border-gray-400 rounded-xl">
											<div class="w-full float-left mb-3">
												<h5 class="font-bold {{$class_dir_padding_lr}} text-lg sm:text-xl uppercase mb-1 {{$class_dir_float_lr}}">{{$address->name}}</h5>
												<div class="relative {{$class_dir_float_rl}}">
													<button type="button" data-saddress-id="{{ $address->id }}" class="edit_user_address text-black leading-8 w-8 h-8 rounded-full py-0 px-1 focus:outline-none text-center border-0"><i class="fa fa-pencil" aria-hidden="true"></i></button>
													@if(($total_address > 1) && ($address->default_address != 1))
													<button type="button" data-saddress-id="{{ $address->id }}" class="delete_user_address text-black leading-8 w-8 h-8 rounded-full py-0 px-1 focus:outline-none text-center border-0"><i class="fa fa-trash" aria-hidden="true"></i></button>
													@endif
												</div>
											</div>
											<div class="w-full float-left">
												<p class="text-lg {{$class_dir_padding_lr}} pb-3 font-normal"><span class="font-medium">{{__('post_detail.address')}}</span> : {{$address->address_1}} <?php echo !empty($address->address_2) ? $address->address_2 : ""; ?></p>
												<p class="text-lg {{$class_dir_padding_lr}} pb-3 font-normal"><span class="font-medium">{{__('post_detail.city & zipcode')}}</span> : {{$address->city}} , {{$address->zipcode}}</p>
												<p class="text-lg {{$class_dir_padding_lr}} pb-3 font-normal"><span class="font-medium">{{__('post_detail.state')}} & {{__('post_detail.country')}}</span> : {{$address->state}} , {{$address->country}}</p>
												<p class="text-lg {{$class_dir_padding_lr}} pb-3 font-normal"><span class="font-medium">{{__('p_profile.phone')}} </span>: {{$address->phone_number}}</p>
											</div>
										</div>
									</div>
									
								</div>
								<div class="text-center w-full">
									<button data-saddress-id="{{ $address->id }}" class="selected_address_id p-0 text-lg md:text-xl lg:text-2xl font-normal capitalize cursor-pointer outline-none focus:outline-none text-green-500 border-b border-green-500 hover:text-green-700">{{__('post_detail.continue')}}</button>
								</div>
							</div>

							<!-- edit address popup start -->
							<div class="fixed z-50 inset-0 overflow-y-auto" id="edit_shipping_address_popup_<?php echo $address->id;?>" style="display:none">
								<div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
									<div class="fixed inset-0 transition-opacity" aria-hidden="true">
										<div class="absolute inset-0 bg-gray-500 opacity-75"></div>
									</div>
									<!-- This element is to trick the browser into centering the modal contents. -->
									<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
									<div class="inline-block align-bottom bg-white rounded-lg text-left overflow-auto shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
										<div class="bg-white px-6 py-4 sm:px-12 sm:py-8">
											<div class="w-full inline-block mb-6 {{$class_dir_text_lr}}">
												<h3 class="text-lg md:text-xl leading-6 font-medium text-gray-900 md:w-7/12" id="modal-headline">
													{{__('post_detail.shipping address')}}
												</h3>
												<div class="py-2 overflow-x-auto overflow-y-scroll my-4" style="height:300px;">
												<input type="hidden" value="{{$address->id}}" class="edit_address_id">
													<div class="form-group mb-4">
														<label class="block text-base text-black font-semibold mb-2">{{__('p_profile.name')}} <span class="text-red-500">*</span></label>
														<input type="text" required id="name_{{$address->id}}" value="{{$address->name}}" class="edit_ship_add_name text-base appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none" />
													</div>
													<div class="form-group mb-4">
														<label class="block text-base text-black font-semibold mb-2"> {{__('post_detail.country')}} <span class="text-red-500">*</span></label>
														
														<select id="select_country_{{$address->id}}" class="select_country edit_ship_add_country text-base appearance-none border-l-2 bg-gray-100 border-gray-400 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none">
															<option value="">Select country</option>
															@foreach($country_list as $r)
															<?php $selected = ($address->country == $r->code) ? "selected='selected'" : ""; ?>
															<option value="{{$r->code}}" <?php echo $selected; ?>>{{$r->name}}</option>
															@endforeach
                            							</select>
													</div>
													<div class="form-group mb-4">
														<label class="block text-base text-black font-semibold mb-2"> {{__('post_detail.address')}}1 <span class="text-red-500">*</span></label>
														<input type="text" required id="address1_{{$address->id}}" value="{{$address->address_1}}" class="edit_ship_add_add1 text-base appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none" />
													</div>
													<div class="form-group mb-4">
														<label class="block text-base text-black font-semibold mb-2"> {{__('post_detail.address')}}2 <span class="text-red-500"></span></label>
														<input type="text" id="address2_{{$address->id}}" value="{{$address->address_2}}" class="edit_ship_add_add2 text-base appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none" />
													</div>
													<div class="form-group mb-4">
														<label class="block text-base text-black font-semibold mb-2"> {{__('post_detail.city')}} <span class="text-red-500">*</span></label>
														<input type="text" required id="city_{{$address->id}}" value="{{$address->city}}" class="edit_ship_add_city text-base appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none" />
													</div>
													<div class="form-group mb-4">
														<label class="block text-base text-black font-semibold mb-2"> {{__('post_detail.state')}} <span class="text-red-500">*</span></label>
														<input type="text" id="state_{{$address->id}}" value="{{$address->state}}" class="edit_ship_add_state text-base appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none" />
													</div>
													<div class="form-group mb-4">
														<label class="block text-base text-black font-semibold mb-2"> {{__('post_detail.zipcode')}} <span class="text-red-500">*</span></label>
														<input type="text" id="zipcode_{{$address->id}}" value="{{$address->zipcode}}" required class="allow-numbers-only edit_ship_add_zip text-base appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none" />
													</div>
													<div class="form-group mb-4">
														<label class="block text-base text-black font-semibold mb-2"> {{__('post_detail.phone number')}} <span class="text-red-500">*</span></label>
														<input type="text" id="phone_number_{{$address->id}}" value="{{$address->phone_number}}" required class="allow-numbers-only edit_ship_add_phone text-base appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none" />
													</div>
												</div>
														
											</div>
											
											<div class="sm:flex sm:flex-row-reverse sm:space-x-3 {{$class_dir_popup_btn}}">
												<button type="button" address-id="{{$address->id}}" class="edit_address_submit w-full inline-block rounded-md border-2 border-green-500 shadow-sm px-4 py-2 pb-3 bg-green-500 text-base font-semibold text-white hover:bg-white hover:text-green-500 focus:outline-none sm:w-auto sm:text-sm transition-all ease-linear duration-500" id="edit_address_submit">
													{{__('messages.submit')}}
												</button>
												<button type="button" id="edit_address_cancel" data-saddress-id="{{ $address->id }}" class="edit_address_cancel mt-3 w-full inline-block rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 pb-3 bg-white text-base font-semibold text-gray-700 hover:bg-gray-200 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-all ease-linear duration-500">
													{{__('post_detail.cancel')}}
												</button>
											</div>
										</div>
									</div>
								</div>
							</div>
							<!-- end edit address popup --> 

							@endforeach
						</div>
						@else
						<div class="text-center">
							<h5 class="text-lg font-semibold mb-4">{{__('post_detail.you haven’t added any shipping address yet')}}.</h5>
							<!--button class="text-md px-6 py-4 font-bold border rounded cursor-pointer float-right outline-none focus:outline-none shipping_address_popup border-2 border-green-500 text-green-500 hover:text-white hover:bg-green-500">{{__('post_detail.add address')}}</button-->
						</div>
						@endif
					</div>
				</div>
			</div>
			<!-- order summary -->
			<div x-data={show:false} class="rounded-sm my-6 cout_order_summary shadow w-full float-left">
				<div class="px-6 py-4" id="headingOne">
					<button data-from="order_summary" class="check_address block focus:outline-none text-lg md:text-xl lg:text-2xl text-gray-500 font-bold w-full flex flex-wrap justify-between" type="button">
						<span class="w-full md:w-auto mb-4 sm:mb-0 flex md:block">2. {{__('post_detail.order summary')}} </span><span class="w-full md:w-auto block"><?php echo "Total amount : "; ?><span class="text-black"><?php echo $currency_symbol->currency_hex . $total; ?></span></span>
					</button>
				</div>
				<div x-show="show" class="leading-normal">
					<div class="p-5 pt-0 sm:p-5">
						<div class="lg:flex lg:flex-wrap mt-2 mb-2 w-full">
							
							<div class="sm:flex sm:flex-wrap sm:space-x-8 sm:{{$class_dir_space_r}} lg:w-8/12 items-center mb-6 md:mb-0">
								<div class="sm:flex items-center sm:h-64 md:w-3/12 bg-gray-100 mb-6 sm:mb-0">
									<img class="rounded mx-auto px-6 max-h-full max-w-full" src="{{$post_img}}" />
								</div>
								
								<div class="sm::w-9/12 mb-6 sm:mb-0">
									<h3 class="text-lg md:text-xl lg:text-2xl mb-3 sm:mb-6 font-bold">{{$post_info->title}}</h3>
									<p class="text-base md:text-lg lg:text-xl text-gray-500 mb-3"><span class="w-40 inline-block italic">{{__('post_detail.item fee')}}</span> : <span class="text-black font-bold"><?php echo $currency_symbol->currency_hex; ?>{{$post_info->price}}</span></p>
									<p class="text-base md:text-lg lg:text-xl text-gray-500 mb-3"><span class="w-40 inline-block italic">{{__('post_detail.shipping rate')}}</span> : <span class="text-black font-bold"><?php echo $currency_symbol->currency_hex; ?>{{$post_info->shipping_rate}}</span></p>
									<p class="text-base md:text-lg lg:text-xl text-gray-500 mb-3"><span class="w-40 inline-block italic">{{__('post_detail.order total')}}</span> : <span class="text-black font-bold"><?php echo $currency_symbol->currency_hex; ?>{{$total}}</span></p>
								</div>
							</div>
							
							<div class="lg:w-4/12 mb-6 md:mb-0">
								@if($dir_rtl =="false")
								<div class="float-left mr-8 mt-1">
									<img class="h-14 w-14 rounded-full object-cover object-center" src="http://127.0.0.1:8000/storage/profile-photos/Gd6OSOlWEzlruebDEgNrtE1epfzyri0kn0Pt2ZUd.jpg">
								</div>
								@else
								<div class="float-right ml-8 mt-1">
									<img class="h-14 w-14 rounded-full object-cover object-center" src="http://127.0.0.1:8000/storage/profile-photos/Gd6OSOlWEzlruebDEgNrtE1epfzyri0kn0Pt2ZUd.jpg">
								</div>
								@endif
								
								<p class="text-lg md:text-xl text-gray-700">
									{{__('p_my_orders_sales.seller')}} : <span class="block"><a class="text-gray-700 text-lg md:text-xl lg:text-2xl font-semibold" href="<?php echo URL::to('/seller-profile/' . $post_info->user_id); ?>">{{$seller}}</a></span>
								</p>
								
							</div>
						</div>
						<input type="hidden" class="shipping_address_id" />
						<input type="hidden" id="buynow_total_order" value="{{$total}}" />
						<input type="hidden" class="selected_post_id" value="{{$post_info->id}}" />
						<input type="hidden" class="post_currency" value="{{$currency_symbol->short_code}}" />
						<div class="w-full text-center">
							<button class="p-0 text-lg md:text-xl lg:text-2xl font-normal capitalize cursor-pointer outline-none focus:outline-none text-green-500 border-b border-green-500 hover:text-green-700 next-payment">{{__('post_detail.continue')}}</button>
						</div>
					</div>
				</div>
			</div>
			<!-- payment method -->
			<div x-data={show:false} class="rounded-sm mt-6 mb-12 count_payment_method shadow w-full float-left">
				<div class="tab w-full overflow-hidden">
					<div class="px-6 py-4 flex flex-wrap justify-between" id="headingOne">
						<button data-from="payment_method" class="check_address block leading-normal focus:outline-none text-lg md:text-xl lg:text-2xl text-gray-500 font-bold w-full flex " type="button">
							3. {{__('post_detail.payment method')}}
						</button>
					</div>
					<div x-show="show" class="overflow-hidden leading-normal">
						<div class="p-5 md:p-10 leading-normal text-center">
							<?php
							$pay_method = 0;
							if (count($payment_methods) > 0) {
								$base_path = base_path() . '/extra/plugins/stripe/src/buynow_order_view.php';
								if (is_file($base_path)) {
									$pay_method = 1;
								} else {
									$pay_method = 0;
								}
							} else {
								$pay_method = 0;
							}
							?>
							@if($pay_method == 1)
							<?php
							
							$seller_publishkey = App\Models\User_profile::where('user_id', $post_info->user_id)->pluck('stripe_public_key')->first();
							
							?>
							<input type="hidden" class="seller_pk" value="{{$seller_publishkey}}" />
							<img class="m-auto max-w-full w-36 sm:w-40 md:w-44 lg:w-52 mb-6" src="{{URL::to('/images/stripe_logo.png')}}" />
							<button class="bg-green-500 text-lg md:text-xl lg:text-2xl text-white p-2 pb-3 sm:p-3 sm:pb-4 px-2 sm:px-8 md:px-16 lg:px-24 w-full sm:w-auto font-semibold rounded-xl cursor-pointer outline-none focus:outline-none border-2 border-green-500 hover:bg-white hover:text-green-500 ease-linear transition-all duration-500 pay-procceed">{{__('post_detail.checkout with stripe')}}</button>
							@else
							<img src="<?php echo URL::to('/images/nodata.jpg'); ?>" class="m-auto rounded-full w-40" />
							<p class="text-lg md:text-xl lg:text-2xl pl-2 pb-1 font-bold">{{__('post_detail.sorry, no payment methods are added here, Please contact admin')}}!</p>
							@endif
						</div>
					</div>
				</div>
			</div>
			@else
			<div class="w-full text-center">
				<img src="<?php echo URL::to('/images/nodata.jpg'); ?>" class="m-auto rounded-full w-40" />
				<p class="text-lg md:text-xl lg:text-2xl pl-2 pb-1 font-bold">{{__('post_detail.invalid data')}}!</p>
			</div>
			@endif
		</div>
	</div>
</div>
</div>
	</div>

	<!-- address popup end start -->
	<div class="fixed z-50 inset-0 overflow-y-auto" id="shipping_address_popup" style="display:none">
		<div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" {{$class_dir}}>
			<div class="fixed inset-0 transition-opacity" aria-hidden="true">
				<div class="absolute inset-0 bg-gray-500 opacity-75"></div>
			</div>
			<!-- This element is to trick the browser into centering the modal contents. -->
			<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
			<div class="inline-block align-bottom bg-white rounded-lg text-left overflow-auto shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
				<div class="bg-white px-6 py-4 sm:px-12 sm:py-8">
					<div class="w-full inline-block mb-6 {{$class_dir_text_lr}}">
						<h3 class="block text-xl text-black font-semibold mb-2 sm:mb-4" id="modal-headline">
							{{__('post_detail.shipping address')}}
						</h3>
						<div class="">
							<div class="py-2 overflow-x-auto overflow-y-scroll my-4" style="height:300px;">
								<div class="form-group mb-4">
									<label class="block text-base text-black font-semibold mb-2">{{__('p_profile.name')}} <span class="text-red-500">*</span></label>
									<input type="text" required class="ship_add_name text-base appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none" />
								</div>
								
								<div class="form-group mb-4">
									<label class="block text-base text-black font-semibold mb-2"> {{__('post_detail.country')}} <span class="text-red-500">*</span></label>
									<select id="select_country" class="select_country ship_add_country text-base appearance-none border-l-2 bg-gray-100 border-gray-400 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none">
										<option value="">Select country</option>
										@foreach($country_list as $r)
										<option value="{{$r->code}}">{{$r->name}}</option>
										@endforeach
                            		</select>
								</div>
								<div class="form-group mb-4">
									<label class="block text-base text-black font-semibold mb-2"> {{__('post_detail.address')}}1 <span class="text-red-500">*</span></label>
									<input type="text" required class="ship_add_add1 text-base shadow appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none" />
								</div>
								<div class="form-group mb-4">
									<label class="block text-base text-black font-semibold mb-2"> {{__('post_detail.address')}}2 <span class="text-red-500"></span></label>
									<input type="text" class="ship_add_add2 text-base shadow appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none" />
								</div>
								<div class="form-group mb-4">
									<label class="block text-base text-black font-semibold mb-2"> {{__('post_detail.city')}} <span class="text-red-500">*</span></label>
									<input type="text" required class="ship_add_city text-base shadow appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none" />
								</div>
								<div class="form-group mb-4">
									<label class="block text-base text-black font-semibold mb-2"> {{__('post_detail.state')}} <span class="text-red-500">*</span></label>
									<input type="text" class="ship_add_state text-base shadow appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none" />
								</div>
								<div class="form-group mb-4">
									<label class="block text-base text-black font-semibold mb-2"> {{__('post_detail.zipcode')}} <span class="text-red-500">*</span></label>
									<input type="text" required class="allow-numbers-only ship_add_zip text-base shadow appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none" />
								</div>
								<div class="form-group mb-4">
									<label class="block text-base text-black font-semibold mb-2"> {{__('post_detail.phone number')}} <span class="text-red-500">*</span></label>
									<input type="text" required class="allow-numbers-only ship_add_phone text-base shadow appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none" />
								</div>
							</div>
						</div>
					</div>
					
					<div class="sm:flex sm:flex-row-reverse sm:space-x-3 {{$class_dir_popup_btn}}">
						<button type="button" class="w-full inline-block rounded-md border-2 border-green-500 shadow-sm px-4 py-2 pb-3 bg-green-500 text-base font-semibold text-white hover:bg-white hover:text-green-500 hover:border-green-500 focus:outline-none sm:w-auto sm:text-sm transition-all ease-linear duration-500" id="address_submit">
							{{__('messages.submit')}}
						</button>
						<button type="button" id="address_cancel" class="mt-3 w-full inline-block rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 pb-3 bg-white text-base font-semibold text-gray-700 hover:bg-gray-200 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-all ease-linear duration-500">
							{{__('post_detail.cancel')}}
						</button>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- address popup -->


	<script>
		//*Allow decimals only
		$(".allow-numbers-only").on("input", function(evt) {
			var self = $(this);
			self.val(self.val().replace(/[^0-9]/gi, ''));
		});
		//*


		// for edit popup
		$('.edit_user_address').on('click', function(e) {
			var popId = $(this).attr("data-saddress-id");
			var user = "{{auth()->user()}}";
			if (user != "") {
				document.querySelector("#edit_shipping_address_popup_"+popId).style.display = "block";
			}
		});

		$('.edit_address_cancel').on('click', function(e) {
			var popId = $(this).attr("data-saddress-id");
			document.querySelector("#edit_shipping_address_popup_"+popId).style.display = "none";
		});
		// for edit popup



		$('.shipping_address_popup').on('click', function(e) {
			var user = "{{auth()->user()}}";
			if (user != "") {
				document.querySelector("#shipping_address_popup").style.display = "block";
			}
		});
		$(".check_address").on('click', function(e) {
			var addreddid = $(".shipping_address_id").val();
			var data_from = $(this).attr("data-from");
			if (addreddid == "" || addreddid == null) {
				toastr.warning('Please choose any shipping address before go to next step.');
				return false;
			} else {
				if (data_from == "order_summary") {
					var att_vals = $(this).parent().parent(".cout_order_summary").attr("x-data");
					if (att_vals == "{show:true}") {
						$(this).parent().parent(".cout_order_summary").attr("x-data", "{show:false}");
					} else {
						$(this).parent().parent(".cout_order_summary").attr("x-data", "{show:true}");
					}
				} else {
					var att_vals = $(this).parent().parent(".count_payment_method").attr("x-data");
					if (att_vals == "{show:true}") {
						$(this).parent().parent(".count_payment_method").attr("x-data", "{show:false}");
					} else {
						$(this).parent().parent(".count_payment_method").attr("x-data", "{show:true}");
					}
				}
			}
		});
		$('#address_cancel').on('click', function(e) {
			document.querySelector("#shipping_address_popup").style.display = "none";
		});

	// edit address start

	$( "body" ).on( "click", ".edit_address_submit", function(e) {

		var add_id = $(this).attr('address-id');
		var name =  $('#name_'+add_id).val(); 
		var country =  $('#select_country_'+add_id).val();
		var address1 =  $('#address1_'+add_id).val();
		var address2 =  $('#address2_'+add_id).val();
		var city =  $('#city_'+add_id).val();
		var state =  $('#state_'+add_id).val();
		var zipcode =  $('#zipcode_'+add_id).val();
		var phone_number =  $('#phone_number_'+add_id).val();
		
		// alert(add_id + name + address1 + address2 + city + state + zipcode + phone_number);
		if (name == "" || country == "" || address1 == "" || city == "" || state == "" || zipcode == "" || phone_number == "") {
				toastr.warning('Please fill all the required fields.');
				return false;
			} else {
				
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: "{{ route('update_shipping_address') }}",
					data: {
						address_id: add_id,
						name: name,
						country: country,
						address1: address1,
						address2: address2,
						city: city,
						state: state,
						zipcode: zipcode,
						phone_number: phone_number
					},

					success: function(data) {
						if (data.message == "success") {
							toastr.success("Updated successfully!");
							window.location.reload();
						}

					}
				});

			}

	});

	// $('#edit_address_submit').on('click', function(e) {
	//         var address_id = $.trim($(".edit_address_id").val());
	//         var name = $.trim($(".edit_ship_add_name").val());
	//         var address1 = $.trim($(".edit_ship_add_add1").val());
	//         var address2 = $.trim($(".edit_ship_add_add2").val());
	//         var city = $.trim($(".edit_ship_add_city").val());
	//         var state = $.trim($(".edit_ship_add_state").val());
	//         var zip = $.trim($(".edit_ship_add_zip").val());
	//         var phone = $.trim($(".edit_ship_add_phone").val());

	//         if (name == "" || address1 == "" || city == "" || state == "" || zip == "" || phone == "") {
	//             toastr.warning('Please fill all the required fields.');
	//             return false;
	//         } else {
				
	//             alert(address_id + name + address1 + address2 + city + state + zip + phone);
	//             // $.ajax({
	//             //     type: 'POST',
	//             //     dataType: 'json',
	//             //     url: "{{ route('update_shipping_address') }}",
	//             //     data: {
	//             //         name: name,
	//             //         address1: address1,
	//             //         address2: address2,
	//             //         city: city,
	//             //         state: state,
	//             //         zip: zip,
	//             //         phone: phone
	//             //     },

	//         }
	//     });
	// edit address end

		$('#address_submit').on('click', function(e) {
			var name = $.trim($(".ship_add_name").val());
			var ship_add_country = $.trim($(".ship_add_country").val());
			var address1 = $.trim($(".ship_add_add1").val()); 
			var address2 = $.trim($(".ship_add_add2").val());
			var city = $.trim($(".ship_add_city").val());
			var state = $.trim($(".ship_add_state").val());
			var zip = $.trim($(".ship_add_zip").val());
			var phone = $.trim($(".ship_add_phone").val());

			if (name == "" || ship_add_country == "" || address1 == "" || city == "" || state == "" || zip == "" || phone == "") {
				toastr.warning('Please fill all the required fields.');
				return false;
			} else {
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: "{{ route('create_shipping_address') }}",
					data: {
						name: name,
						ship_add_country: ship_add_country,
						address1: address1,
						address2: address2,
						city: city,
						state: state,
						zip: zip,
						phone: phone
					},
					success: function(data) {
						if (data.message == "success") {
							document.querySelector("#shipping_address_popup").style.display = "none";
							toastr.success("New address created successfully!");
							$(".ship_add_name").val("");
							$(".ship_add_country").val("");
							$(".ship_add_add1").val("");
							$(".ship_add_add2").val("");
							$(".ship_add_city").val("");
							$(".ship_add_state").val("");
							$(".ship_add_zip").val("");
							$(".ship_add_phone").val("");
							window.location.reload();
						}

					}
				});
			}
		});
		$(".selected_address_id").on('click', function(e) {
			var addreddid = $(this).attr('data-saddress-id');
			$(".shipping_address_id").val(addreddid);
			$(".cout_order_summary").attr("x-data", "{show:true}");
			$(".cout_address_list").attr("x-data", "{show:false}");
		});
		$(".next-payment").on("click", function(e) {
			$(".cout_order_summary").attr("x-data", "{show:false}");
			$(".count_payment_method").attr("x-data", "{show:true}");
		});
		$('.delete_user_address').on('click', function(e) {
			var id = $(this).attr("data-saddress-id");

			var check = confirm("You want to delete this Address?");
			if (check == true) {

			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: "{{ route('delete_shipping_address') }}",
				data: {
					id: id,
				},
				success: function(data) {
					if (data.message == "success") {
						toastr.success("Deleted successfully!");
						window.location.reload();
					} else {
						toastr.warning("Please try again later!");
						window.location.reload();
					}

				}
			});
			}
		});
	</script>
	<?php
	if (count($payment_methods) > 0) {
		foreach ($payment_methods as $p) {
			$base_path = base_path() . '/extra/plugins/' . $p['name'] . '/src/buynow_order_view.php';
			if (is_file($base_path)) {
				include_once($base_path);
			}
		}
	}
	?>
	@endsection