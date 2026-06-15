	<div class="w-full float-left">
		@if ($message = Session::get('message'))
		<div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-yellow-500 alert-{{Session::get('class')}} z-50">
			<span class="text-xl inline-block mr-5 align-middle"><i class="fa fa-bell"></i></span>
			<span class="inline-block align-middle mr-8"><b class="capitalize"></b> {{ $message }}</span>
			<button class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none" onclick="closeAlert(event)"><span>×</span></button>
		</div>
		@endif
		
		<div class="w-full float-left">
			<div class="container mx-auto px-4">
				<h1 class="text-xl md:text-2xl font-bold text-black m-3 lg:m-6 lg:ml-0 uppercase">{{__('p_myads.my ads')}}</h1>
			</div>
		</div>
		
		<div class="w-full float-left sm:mt-6 mt-3">
			<div class="container mx-auto px-4">
				<div class="bg-white relative w-full float-left mb-8 sm:mb-12 md:mb-16 lg:mb-20">

					<div class="w-full float-left">
						<h4 class="font-bold uppercase float-left md:float-right mb-4 md:mb-8">
						<?php
						$post_methods = App\Models\TblPostMethod::get_active_post_methods();
						if (!empty($post_methods)) {
						$check_banner_ads = $post_methods->pluck('name')->toArray();
						if (in_array("bannerads", $check_banner_ads)) { ?>
						<a target="_blank" href="<?php echo URL::to('/banner-advertise'); ?>" class="float-left text-white hover:text-green-500 bg-green-500 rounded-md hover:bg-white border border-green-500 focus:outline-none ease-linear transition-all duration-150 text-sm lg:text-base font-semibold px-4 py-3"><i class="fa fa-plus-circle"></i> {{__('p_my_banner_ads.banner advertisement')}}</a>
						<?php
						}
						}
						?>
						</h4>
					</div>

					<!-- search and delete check box -->
					
					<div class="text-sm font-bold w-full items-center lg:flex mb-4 md:mb-6 lg:mb-12 lg:float-none float-left">
						<!--<div class="pl-0 float-left py-0">-->
							<input type="checkbox" class="w-4 h-4 inline-block align-middle" id="master" />
							<label for="master" class="text-sm md:text-lg lg:text-xl text-gray-800 font-semibold inline-block align-middle">&nbsp; {{__('p_myads.select all')}} |</label>
						<!--</div>-->
						<button class="multiple_del items-center px-2 py-1 bg-red-500 border border-transparent rounded-md text-sm lg:text-lg text-white tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150 ml-5 float-right"><i class="fa fa-trash-o mr-1" aria-hidden="true"></i>Delete</button>
						<!-- filter -->
						<div class="lg:flex flex-row flex-wrap items-center lg:ml-auto mt-4 lg:mt-0">
							<select wire:model="pid" class="px-4 py-2 placeholder-gray-400 text-gray-500 rounded text-sm md:text-lg shadow outline-none focus:outline-none focus:shadow-outline w-full border border-gray-200 rounded-md h-11 lg:mr-4">
							<option value="">[-- {{__('p_myads.select package name')}} --]</option>
							@foreach ($packages_list as $packages_list)
							@if($packages_list->short_name == "free")
							<option value="free">{{ $packages_list->name }}</option>
							@else
							<option value="{{ $packages_list->id }}">{{ $packages_list->name }}</option>
							@endif
							@endforeach
							</select>
							
							
						</div>
						<!-- search -->
						<div class="lg:flex flex-row flex-wrap items-center mt-4 lg:mt-0">
							<div class="relative">
								<div class="absolute top-3 left-3"> <i class="fa fa-search text-gray-400 z-20 hover:text-gray-500"></i> </div>
								<input type="text" wire:model="search" class="px-10 py-2 placeholder-gray-400 text-gray-500 rounded text-sm md:text-lg shadow outline-none focus:outline-none focus:shadow-outline w-full border border-gray-200 rounded-md h-11" placeholder="{{__('p_myads.search here')}}">
							</div>
						</div>
					</div>
					<!-- end -->

					<div class="w-full overflow-auto">
						@if(count($list) > 0)
						<table class="w-full table-auto">
							<thead>
								<tr>
									<th class="sm:w-1/12"></th>
									<th class="sm:w-1/4"></th>
									<th class="sm:w-2/5"></th>
									<th class="sm:w-1/4"></th>
								</tr>
							</thead>

							<tbody>
								<?php $i = 0; ?>
									@foreach($list as $post)
									<?php
									$i++;
									$img = App\Models\TblChat::getPostImgForList($post['id']);
									$currency_symbol = App\Models\TblPost::get_post_currency($post['currency_id']);
									$slug = App\Models\TblPost::get_post_slug($post["slug"]);
									$check_post_package = App\Models\TblPost::check_post_expired($post['id']);
									$settings = App\Models\Setting::get_logos();
									$default_currency = App\Models\TblPost::get_post_currency($settings['default_currency']);
									$viewcount = App\Models\TblPostInsight::views_count($post['id']);
								?>
								<tr>
									<td class="p-2 py-5 lg:py-7 border border-l-2 border-gray-300 md:border-0 text-center">
										<input type="checkbox" class="del_check w-4 h-4 z-50" id="del_chk_{{$i}}" id="del_chk" data-id="{{$post['id']}}" data-delete-row-id="{{$i}}" />
									</td>

									<td class="p-2 py-5 lg:py-7 border border-gray-300 md:border-0">
										<a href="{{ $slug }}">
											<img class="m-auto h-16 w-16 sm:h-32 md:h-48 lg:h-72 max-w-xs sm:w-full object-cover object-center" src="{{ $img }}" />
										</a>
									</td>

									<td class="px-3 py-5 lg:py-7 border border-gray-300 md:border-0 text-left align-top">
										<!-- sttaus -->
										<div class="w-full">
											@if($check_post_package['ads_type'] != "free")
											@if($check_post_package['expired'] == "Expired")
											<p class="bg-red-400 text-black rounded-full w-24 py-1 text-center text-xs sm:text-sm font-semibold">{{__('p_myads.expired')}}</p>
										
											@else
											@if($check_post_package['is_bulk'] != 0)
											<p class="bg-yellow-500 text-black rounded-full w-40 py-1 text-center text-xs sm:text-sm font-semibold">{{__('p_myads.bulk package')}} - {{$check_post_package['ads_type']}}</p>
										
											@else
											<p class="bg-yellow-500 text-black rounded-full w-24 py-1 text-center text-xs sm:text-sm font-semibold">{{$check_post_package['ads_type']}}</p>
											
											@endif
											@endif
											@else
											@if($check_post_package['expired'] == "Expired")
											<p class="bg-red-400 text-black rounded-full w-24 py-1 text-center text-xs sm:text-sm font-semibold">{{__('p_myads.expired')}}</p>
											
											@endif
											@endif
											<!--<button id="more" class="visible md:invisible focus:outline-none float-right bg-green-500 text-white py-1 w-20 font-semibold rounded-full" onclick="$('.details').slideToggle(function(){$('#more').html($('.details').is(':visible')?'Close':'Actions');});">Actions</button>-->
											
										</div>
										
										<h3 class="text-lg lg:text-xl font-semibold mt-3 sm:mt-4 capitalize"><a class="hover:text-green-500" href="{{$slug}}">{{$post["title"]}}</a></h3>
										
										<p class="text-lg lg:text-xl font-bold mt-1 mb-1 sm:mt-2 sm:mb-2 lg:mt-3 lg:mb-5"> <?php echo $currency_symbol[0]; ?>{{$post["price"]}}</p>
										
										<div class="w-full float-left lg:mb-2">									
											<p class="text-sm text-gray-500 font-semibold mb-2 mr-16 float-left">
												{{__('p_myads.from')}} : {{$check_post_package['from_date']}}
											</p>
											<p class="text-sm text-gray-500 font-semibold mb-2 mr-2 float-left">
												{{__('p_myads.to')}} : {{$check_post_package['to_date']}}
											</p>
										</div>	
										
										<div class="w-full float-left lg:mb-2">
											@if(!empty($check_post_package['bulk_type']))
											<p class="text-md text-gray-500 font-normal	mb-2 ml-0">{{__('p_myads.package validity')}} : {{$check_post_package['bulk_type']}}</p>
											@endif
											@if($check_post_package['ads_type'] != "free")
											@if($check_post_package['expired'] != "Expired")
											<p class="text-sm text-gray-500 font-semibold mb-2">{{__('p_myads.package price')}} : <?php echo $default_currency[0]; ?>{{$check_post_package['package_price']}}</p>
											@endif
											@endif
										</div>
										
										<div class="w-full float-left lg:mt-2">
											<p class="text-base text-gray-500 font-medium mr-2 capitalize"><i class="fa fa-eye text-green-500 text-lg"></i> {{ $viewcount}} {{__('p_myads.views')}}</p>
										</div>
									</td>

									<td class="p-2 py-5 lg:py-7 border border-gray-300 md:border-0 text-center">
										<div class="flex justify-center">
											<button title="View" class="hover:text-green-500 focus:outline-none  mr-4"><a href="{{$slug}}" target="_blank" class="text-lg hover:text-green-500"><i class="fa fa-eye mr-1" aria-hidden="true"></i></a></button>
											<button title="Edit" class="hover:text-green-500 focus:outline-none mr-4"><a href="{{URL::to('/post-edit?id='.$post->id)}}" class="text-lg hover:text-yellow-500"><i class="fa fa-pencil-square-o mr-1" aria-hidden="true"></i></a></button>
											<button title="Delete" wire:click="destroy('{{$post['id']}}')" onclick="confirm('Are you sure you want to delete?') || event.stopImmediatePropagation()" class="focus:outline-none text-lg hover:text-red-500"><i class="fa fa-trash-o mr-1" aria-hidden="true"></i></button>
										</div>
										<div class="text-center xl:px-12">
											@if($check_post_package['expired'] == "Expired")
											@elseif($post['sold_status'] == 0)
											<button data-status="mark_sold" data-id="{{$post['id']}}" class="republish px-1 sm:px-2 p-2 sm:pb-3 lg:p-3 lg:pb-4 bg-white text-xs sm:text-sm lg:text-base font-bold border-2 border-green-500 text-black mt-3 md:mt-4 hover:bg-green-500 hover:text-white hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 uppercase w-full">{{__('p_myads.mark as sold')}}</button>
											@else
											<?php
											$check_buynow_order = App\Models\TblBuynowOrder::where('post_id', $post['id'])->orderBy('id', 'desc')->pluck('order_status')->first();
											?>
											@if(!empty($check_buynow_order))
											@if($check_buynow_order != "delivered")
											<button class="no-records cursor-not-allowed px-1 sm:px-2 p-2 sm:pb-3 lg:p-3 lg:pb-4 bg-white text-xs sm:text-sm lg:text-base font-bold border-2 border-green-500 text-black mt-3 md:mt-4 hover:bg-green-500 hover:text-white hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 uppercase w-full">{{__('p_myads.back to sale')}}</button>
											@else
											<button data-status="mark_sale" data-id="{{$post['id']}}" class="republish px-1 sm:px-2 p-2 sm:pb-3 lg:p-3 lg:pb-4 bg-white text-xs sm:text-sm lg:text-base font-bold border-2 border-green-500 text-black mt-3 md:mt-4 hover:bg-green-500 hover:text-white hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 uppercase w-full">{{__('p_myads.back to sale')}}</button>
											@endif
											@else
											<button data-status="mark_sale" data-id="{{$post['id']}}" class="republish px-1 sm:px-2 p-2 sm:pb-3 lg:p-3 lg:pb-4 bg-white text-xs sm:text-sm lg:text-base font-bold border-2 border-green-500 text-black mt-3 md:mt-4 hover:bg-green-500 hover:text-white hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 uppercase w-full">{{__('p_myads.back to sale')}}</button>
											@endif
											@endif
											@if($check_post_package['ads_type'] != "free")
											@if($check_post_package['expired'] == "Expired")
											<button class="px-1 sm:px-2 p-2 sm:pb-3 lg:p-3 lg:pb-4 bg-green-500 text-xs sm:text-sm lg:text-base font-bold border-2 border-green-500 text-white mt-3 md:mt-4 hover:bg-white hover:text-green-500 hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 uppercase w-full">
												<a href="{{ URL::to('/selectPackage?post=' . $post['id'] . '') }}">{{__('post_detail.sell fast')}}</a>
											</button>
											@endif
											@else
											<?php
											$check_pack_info = App\Models\Package::where('lft', 1)->first();
											?>
											<?php if (($check_post_package['expired'] == "Expired") && ($check_post_package['post_count'] < $check_pack_info->single_pack_limit)) { ?>
												<button class="republish px-1 sm:px-2 p-2 pb-3 lg:p-3 lg:pb-4 bg-green-500 text-xs sm:text-base font-bold border-2 border-green-500 text-white mt-3 md:mt-4 hover:bg-white hover:text-green-500 hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 uppercase w-full" data-id='{{ $post["id"] }}'>{{__('p_myads.re publish')}}
												</button>
											<?php } ?>
											<button class="px-1 sm:px-2 p-2 sm:pb-3 lg:p-3 lg:pb-4 bg-green-500 text-xs sm:text-base font-bold border-2 border-green-500 text-white mt-3 md:mt-4 hover:bg-white hover:text-green-500 hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 uppercase w-full">
												<a href="{{ URL::to('/selectPackage?post=' . $post['id']) }}">{{__('post_detail.sell fast')}}</a>
											</button>
											@endif
										</div>
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
						{{ $list->links() }}
						@else
						<img src="<?php echo URL::to('/images/nodata.jpg'); ?>" class="m-auto" />
						<p class="text-2xl pl-2 pb-4 font-bold text-center">{{__('p_myexchange.no data found')}}!</p>
						@endif
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

		@media(max-width:500px) {
		.details {
		display: none;
		}
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
	</div>