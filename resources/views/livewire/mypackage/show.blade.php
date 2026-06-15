	<?php
		$dir_rtl =  App\Models\Setting::is_dir_rtl();
		$class_dir = ($dir_rtl=="true")?'dir=rtl':"";
		$class_dir_text_lr = ($dir_rtl=="true")?'text-right':'text-left';
		$class_dir_popup_btn = ($dir_rtl=="true")?'':'sm:space-x-reverse';
	?>
	
	<div class="w-full float-left" {{$class_dir}}>

		<?php 
			$get_meta = App\Models\TblOtherpage::get_meta('mypackage');
			$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
			$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
			$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");

		?>

		@if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description))
			@section('meta_title', $meta_title)
			@section('meta_keywords', $meta_keywords)
			@section('meta_description', $meta_description)
		@endif


		@if ($message = Session::get('message'))
		<div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-yellow-500 alert-{{Session::get('class')}} z-50">
			<span class="text-xl inline-block mr-5 align-middle"><i class="fa fa-bell"></i></span>
			<span class="inline-block align-middle mr-8"><b class="capitalize"></b> {{ $message }}</span>
			<button class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none" onclick="closeAlert(event)"><span>×</span></button>
		</div>
		@endif

		<div class="w-full float-left">
			<div class="container mx-auto px-4">
				<h1 class="text-xl md:text-2xl font-bold text-black my-4 sm:my-6 lg:my-8 uppercase">{{__('messages.Bought Packages')}}</h1>
			</div>
		</div>
		
		<div class="w-full bg-gray-100 h-48 float-left">
			  
		</div>
		
		<div class="w-full float-left">
			<div class="container mx-auto px-4">
				<div class="p-5 w-full bg-white shadow-lg float-left mb-8 sm:mb-12 md:mb-16 lg:mb-20 lg:p-12 relative -mt-36">
					@if(count($list_of_payments) > 0)
					<div class="w-full overflow-auto m-0 p-0">
						<table class="pflex-auto w-full bg-white shadow-xl table-auto">
							<thead>
								<tr>
									<th class="py-2 px-4 border border-gray-300 {{$class_dir_text_lr}} whitespace-nowrap text-base md:text-lg lg:text-xl text-gray-800 font-semibold">{{__('p_choose_package.package detail')}}</th>
									<th class="py-2 px-4 border border-gray-300 {{$class_dir_text_lr}} whitespace-nowrap text-base md:text-lg lg:text-xl text-gray-800 font-semibold">{{__('p_choose_package.ad limit')}}</th>
									<th class="py-2 px-4 border border-gray-300 whitespace-nowrap text-base md:text-lg lg:text-xl text-gray-800 font-semibold">{{__('p_my_banner_ads.created at')}}</th>
									<th class="py-2 px-4 border border-gray-300 whitespace-nowrap text-base md:text-lg lg:text-xl text-gray-800 font-semibold">{{__('p_myads.action')}}</th>
								</tr>
							</thead>
							<tbody>
								<?php $i = 0; ?>
								@foreach($list_of_payments as $lop)
								<?php $i++; ?>
								<tr>
									<td class="py-2 px-4 border border-gray-300  whitespace-nowrap">
										<p class="text-base lg:text-lg font-semibold mb-2 text-black">{{$lop->pack_name}}</p>
										<p class="text-sm text-gray-500 font-semibold mb-2">{{__('p_choose_package.start')}} : <?php echo date('d-m-Y', strtotime($lop->start_date)); ?></p>
										<p class="text-sm text-gray-500 font-semibold mb-2">{{__('p_choose_package.end')}} : <?php echo date('d-m-Y', strtotime($lop->end_date)); ?></p>
										<p class="text-sm text-gray-500 font-semibold">{{__('post_detail.price')}} : <?php echo $lop->currency_hex; ?> {{$lop->package_amount}}</p>
									</td>
									<td class="py-2 px-4 border border-gray-300  whitespace-nowrap">
										<?php
										$remaining_cnt = App\Models\TblBulkPackPayment::get_remaining_ads($lop->id)
										?>
										<p class="text-sm text-gray-500 font-semibold mb-2">{{__('p_choose_package.total')}}: {{$lop->bulk_limit}}</p>
										<p class="text-sm text-gray-500 font-semibold mb-2">{{__('p_choose_package.used')}}: <?php echo $lop->bulk_limit - $remaining_cnt; ?> </p>
										<p class="text-sm text-gray-500 font-semibold mb-2">{{__('p_choose_package.remaining')}}: <?php echo $remaining_cnt; ?></p>

									</td>
									<td class="py-2 px-4 border border-gray-300 text-center whitespace-nowrap">
										<p class="text-sm md:text-base text-black font-semibold">{{$lop->created_at}}</p>
									</td>
									<?php
									$url = URL::to('/mypackage-add') . '?p=' . $lop->id;
									$p_end_date = date('Y-m-d', strtotime($lop->end_date));
									?>
									<td class="py-2 px-4 border border-gray-300 text-center">
										
											<?php if (($remaining_cnt != 0) && ($p_end_date >= date('Y-m-d'))) { ?>
												<button class="items-center whitespace-nowrap bg-green-500 border-2 border-green-500 rounded-md text-sm text-white font-semibold tracking-widest hover:bg-white focus:outline-none disabled:opacity-25 transition ease-in-out duration-500 hover:text-green-500 mx-1 my-1"><a class="px-4 pt-2 pb-3 block" href="<?php echo $url; ?>">{{__('p_choose_package.Assign Ads')}}</a></button>
											<?php } ?>
											<?php if ($lop->bulk_limit - $remaining_cnt != 0) { ?>
												<button type="button" class="view_assied_ads whitespace-nowrap items-center px-4 pt-2 pb-3 bg-gray-700 border-2 border-gray-700 rounded-md text-sm text-white tracking-widest focus:outline-none disabled:opacity-25 transition ease-in-out duration-500 font-semibold hover:bg-white hover:text-gray-700 mx-1 my-1" data-divclass="assignedads_<?php echo $i; ?>">{{__('p_choose_package.View Assigned Ads')}}</button>
												<?php
												$assigned_ads = App\Models\TblBulkPackPayment::get_assigned_ads($lop->id);
												?>
												<textarea class="assignedads_<?php echo $i; ?>" style="display: none;">
																<?php
																foreach ($assigned_ads as $d) {
																	$url = App\Models\TblPost::get_post_slug($d['slug']);
																?>
																	<p><a href="{{$url}}"><?php echo $d['title']; ?></a></p>
																<?php } ?>
															</textarea>
											<?php } else if ($p_end_date <= date('Y-m-d')) { ?>
												<p class="text-sm md:text-base text-gray-500 font-medium">{{__('p_choose_package.buypackage expired')}}</p>
											<?php } ?>
										
									</td>
								</tr>
								@endforeach
							</tbody>
						</table>
					</div>
					<div class="mt-4">
						{{$list_of_payments->links()}}
					</div>

					<!-- View assigned ads popup start --->
					<div class="fixed z-50 inset-0 overflow-y-auto viewads" style="display:none">
						<div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
							<div class="fixed inset-0 transition-opacity" aria-hidden="true">
								<div class="absolute inset-0 bg-gray-500 opacity-75"></div>
							</div>
							<!-- This element is to trick the browser into centering the modal contents. -->
							<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
							<div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
								<div class="bg-white px-6 py-4 sm:px-12 sm:py-8">
									<div class="mb-6">
										<div class="{{$class_dir_text_lr}}">
											<h3 class="block text-xl text-black font-semibold mb-2 sm:mb-4" id="modal-headline">
												{{__('p_choose_package.Assigned ads')}}
											</h3>
											<div class="">
												<div class="getassignedads block text-base text-black font-normal">

												</div>

											</div>
										</div>
									</div>
									
									<div class="sm:flex sm:flex-row-reverse sm:space-x-3 {{$class_dir_popup_btn}}">
										<button type="button" class="cancel mt-3 w-full inline-flex justify-center rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 pb-3 bg-white text-base font-semibold text-gray-700 hover:bg-gray-200 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-all ease-in-out duration-500">
											{{__('post_detail.cancel')}}
										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script>
			function closeAlert(event) {
				let element = event.target;
				while (element.nodeName !== "BUTTON") {
					element = element.parentNode;
				}
				element.parentNode.parentNode.removeChild(element.parentNode);
			}
		</script>
		<script>
			$('.view_assied_ads').on('click', function(e) {
				var atr = "." + $(this).attr('data-divclass');
				$(".viewads .getassignedads").html($(atr).val())
				$(".viewads").css("display", "block");
			});
			$('.cancel').on('click', function(e) {
				$(".viewads").css("display", "none");
			});
		</script>
		@else
		<div class="w-full text-center">
			<img src="<?php echo URL::to('/images/nodata.jpg'); ?>" class="m-auto" />
			<p class="text-base sm:text-lg lg:text-xl text-2xl font-bold mb-8 sm:mb-12">No Records found!</p>
		</div>
		@endif
	</div>