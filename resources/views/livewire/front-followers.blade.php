<?php
$settings = App\Models\Setting::get_logos();
$meta_title = $seller_info->name . " | " . $settings['name'];
//dd($seller_info->id);

$dir_rtl =  App\Models\Setting::is_dir_rtl();
$class_dir = ($dir_rtl == "true") ? 'dir=rtl' : "";
$class_dir_float_lr = ($dir_rtl == "true") ? 'float-right' : 'float-left';
$class_dir_padding_lr = ($dir_rtl == "true") ? 'pr-0 lg:pr-6 lg:pr-6' : 'pl-0 lg:pl-2';
$class_dir_text_lr = ($dir_rtl == "true") ? 'text-right' : 'text-left';
$class_dir_popup_btn = ($dir_rtl == "true") ? '' : 'sm:space-x-reverse';
$class_dir_flw = ($dir_rtl == "true") ? 'ml-4' : 'mr-4';
$id = auth()->user();
//dd($id);
?>

@section('meta_title', $meta_title)

<div class="w-full float-left py-4 md:py-5 bg-light-green " {{$class_dir}}>
	<div class="container m-auto px-4">
		<div class="w-full float-left ">
			<div class="w-full {{$class_dir_float_lr}} md:w-4/12 lg:w-3/12 relative mb-6">
				<div class="w-full float-left">

					<!-- //starts for user or company profile checking -->
					<!--dealer-box-->
					<?php
					//dd($seller_info->id);
					$seller_checking = App\Models\Verificationrequest::where('user_id',  $seller_info->id)->where('is_company', 'yes')->exists();
					// dd($seller_checking);
					?>
				
					<div class="bg-white py-3 px-2 shadow-md  rounded-md border float-left items-center my-2  xl:mb-1 w-full">
						<div class="w-full float-left">
							<div class="float-left w-full flex mb-2 justify-center">
								<div class="rounded-full items-center w-14 h-14 bg-gray-100  text-center text-green-600 text-3xl xl:text-5xl ">
									<?php
									$currentUserId = "";
									//dd($currentUserId);
									$profile_url = !empty($seller_info->profile_photo_path) ? URL::to('storage/' . $seller_info->profile_photo_path) : URL::asset('storage/profile-avatar.jpg');
									if ($id == null) {
										$adPostedUserId = $seller_info->id;
										if (!empty($currentUserId) && ($currentUserId != $adPostedUserId)) {
											//dd('se');
											$enable = 1;
											$check_is_follow = App\Models\TblFollowers::check_is_follow($currentUserId, $adPostedUserId);
											$follow_text = ($check_is_follow == true) ? "Unfollow" : "Follow";
										} else {
											$enable = 0;
										}
                                        $joinedon = \Carbon\Carbon::parse($seller_info->created_at)->isoFormat('DD MMM YYYY');
										
									} else {
										$currentUserId = !empty(auth()->user()->id) ? auth()->user()->id : "";
										$adPostedUserId = $seller_info->id;
										if (!empty($currentUserId) && ($currentUserId != $adPostedUserId)) {
											$enable = 1;
											$check_is_follow = App\Models\TblFollowers::check_is_follow($currentUserId, $adPostedUserId);
											$follow_text = ($check_is_follow == true) ? "Unfollow" : "Follow";
										} else {
											$enable = 0;
										}
									}
									$joinedon = \Carbon\Carbon::parse($seller_info->created_at)->isoFormat('DD MMM YYYY');
									?>
									<input type="hidden" id="" name="" postid="{{$adPostedUserId}}">
									<img class="w-14 h-14  mx-auto object-cover object-center rounded-full" src="{{ $profile_url }}" />
								</div>
								<div class="lg:w-9/12 px-4 pr-0">

									<h3 class="text-xl xl:text-2xl capitalize poppins-600 text-dark-gray">{{$seller_info->name}}</h3>
									<h3 class="text-sm xl:text-xs text-text-dark-gray bg-light-green radius-36 inline-block px-2 ">{{__('post_detail.joined')}}: {{$joinedon}}</h3>
									<div class="mt-1 items-center ml-1" >
										<?php
										$seller_rate = App\Models\TblSellerReviews::rate_avg($adPostedUserId);
										$seller_count = App\Models\TblSellerReviews::revi_count($adPostedUserId);

										$seller_rating = round($seller_rate);
										$starwhite = URL::asset('/images/star1.png');
										$starFill = URL::asset('/images/star2.png');
										?>
										<label class="inline-flex items-center mr-1 text-green-500 text-base"><img src="{{ ($seller_rating>=1)?$starFill:$starwhite }}" class="w-3 h-3 lg:w-4 lg:h-4" /></label>
										<label class="inline-flex items-center mr-1 text-green-500 text-base"><img src="{{ ($seller_rating>=2)?$starFill:$starwhite }}" class="w-3 h-3 lg:w-4 lg:h-4" /></label>
										<label class="inline-flex items-center mr-1 text-green-500 text-base"><img src="{{ ($seller_rating>=3)?$starFill:$starwhite }}" class="w-3 h-3 lg:w-4 lg:h-4" /></label>
										<label class="inline-flex items-center mr-1 text-green-500 text-base"><img src="{{ ($seller_rating>=4)?$starFill:$starwhite }}" class="w-3 h-3 lg:w-4 lg:h-4" /></label>
										<label class="inline-flex items-center mr-1 text-green-500 text-base"><img src="{{ ($seller_rating>=5)?$starFill:$starwhite }}" class="w-3 h-3 lg:w-4 lg:h-4" /></label>
										<label title="{{$seller_count}} Reviews" class="inline-flex items-center text-xs mr-1">({{$seller_count}})</label>
									</div>
								</div>

							</div>
							<?php
							if ($seller_info->current_chat_status == 'online') {

								$active = 'bg-green-500';
							} else {
								$active = 'bg-red-500';
							}
							?>
							<div class="w-full flex lg:justify-between items-center">
								<span class="text-sm bg-gray-100 px-3 py-1 rounded"><span class="inline-block {{$active}} rounded mr-2  p-1"></span> {{__('post_detail.'.$seller_info->current_chat_status)}}</span>
								<span class="text-sm bg-gray-100 px-3 py-1 rounded ">{{App\Models\TblChat::timeAgo($seller_info->created_at)}} on JustreUsed</span>
								<!-- <a class=" inline-block text-center  bg-gray-200 flex item-center rounded-md h-8  w-8" href="#">
								<img class="inline-block w-5 mx-auto object-contain" src="{{ URL::to('images/frontend/king.png') }}" /></a> -->
							</div>
							<?php $phone = !empty($seller_info->phone) ? $seller_info->phone : ""; ?>
							<?php
							$urlnew = URL::to('/chatting');
							// $phone = !empty($seller_info->phone)?$seller_info->phone:"";

							// if ($adPostedUserId == $currentUserId) {
							?>
							<div class="w-full float-left mt-2">
								<p><a href="tel:{{$phone}}" class="my-1 block bg-green-500 text-white text-center text-xl rounded-md p-2.5 poppins-600"><span class="text-2xl inline-block mr-2 fa fa-phone"></span>Show Contact</a></p>

								<!-- <p class="my-1 block bg-green-500 text-white text-center text-xl rounded-md p-2.5 poppins-600 review_list_click">
								Seller Review<span class="float-right font-bold text-gray-500" id="review_list_click"></span>
								</p> -->

								<p class="cursor-pointer my-1 mt-2 block bg-green-500 text-white text-center text-xl rounded-md p-2.5 poppins-600 hidden">
									Seller Review<span class="float-right font-bold text-white-500" id="seller_reviews"></span>
								</p>


								<!-- <p class="py-1"><a class="block border text-mygreen border-green-500 text-center text-xl rounded-md p-2 poppins-600 " href=""><span><img class="inline-block mr-2" src="{{ URL::to('images/frontend/comment.png') }}" /></span>Start chat</a></p> -->
							</div>
							<?php if ($enable == 1) { ?>
								<button data-seller-id="{{ $adPostedUserId }}" id="seller_{{ $adPostedUserId }}" class="hidden my-1 save_to_followers bg-green-500 w-full p-2 pb-3 text-white text-xl font-normal text-center rounded-md mb-6 border-2 border-green-500 hover:bg-white hover:text-green-500 ease-linear transition-all duration-500">
									<?php echo $follow_text; ?>
								</button>
							<?php } ?>

							<?php if ($enable == 0 && !empty($currentUserId)) { ?>
								<button class="  invite_friends my-1 block bg-green-500 text-white text-center text-xl rounded-md p-2.5 poppins-600 w-full">
									{{__('messages.invite friends')}}
								</button>
							<?php } ?>
							<p class="text-center">
								<?php if ($enable == 1) { ?>
									<a href="#" class="cursor-pointer my-2 text-xl text-mygreen bg-white shadow-md border border-green-500 p-2 rounded block float-left w-full " id="report_user">{{__('messages.report user')}}</a>
								<?php } ?>
							</p>

						</div>

						<div class="w-full float-left flex mt-2 ">
							<p class="px-1 text-center poppins-500 capitalize cursor-pointer" id="followers_list_click">
								{{__('messages.followers')}}<span class="pt-1 float-left w-full font-bold   text-gray-700 text-center px-2 rounded block" id="seller_followers"><?php echo count($followers); ?></span>
							</p>
							<p class="px-1 text-center poppins-500 capitalize cursor-pointer" id="followings_list_click">
								{{__('messages.following')}} <span class="pt-1 float-left w-full font-bold   text-gray-700 text-center px-2 rounded block"><?php echo count($followings); ?></span>
							</p>

							<!--review-rating-->
							<input type="hidden" value="<?php echo count($followers); ?>" class="seller_followers" />
							<div class="my-1 items-center ml-0 mt-0 mb-2 text-center cursor-pointer " id="review_list_click">
								<?php
								$adPostedUserId = $seller_info->id;
								$seller_rate = App\Models\TblSellerReviews::rate_avg($adPostedUserId);
								$seller_count = App\Models\TblSellerReviews::revi_count($adPostedUserId);

								$seller_rating = round($seller_rate);
								$starwhite = URL::asset('/images/star1.png');
								$starFill = URL::asset('/images/star2.png');
								?>
								<p class="px-1 text-center poppins-500 capitalize">Reviews</p>
								<label title="{{$seller_count}} Reviews" class="inline-flex items-center  poppins-50 mr-1">({{$seller_count}})</label>
								<input type="hidden" value="<?php echo ($is_buy); ?>" class="is_buy" />
							</div>

						</div>

						<?php if ($enable == 1) { ?>
						<div class="follow_button w-full float-left mt-2 cursor-pointer">
							<a data-seller-id="{{ $adPostedUserId }}" id="seller_{{ $adPostedUserId }}" class=" save_to_followers my-1 block bg-green-500 text-white text-center text-base rounded-md p-2.5 poppins-600"><?php echo $follow_text; ?></a>
						</div>
						<?php } ?>
						
					</div>
					
				</div>
			</div>
			<div class="w-full lg:w-9/12 {{$class_dir_padding_lr}} md:w-8/12 ml-auto mr-auto float-left ">
				<div class="float-left w-full bg-white p-3 rounded mb-1 hidden">
					<div class="category_slider_box flex items-center justigy-between gap-2">
						<?php
						$adPostedUserId = $seller_info->id;
						//$user = auth()->user()->id;
						$main_id = array();
						$products = App\Models\TblPost::select('tbl_posts.id', 'tbl_posts.user_id', 'tbl_posts.category_id', 'tbl_posts.title', 'tbl_posts.images', 'tbl_posts.created_at', 'tbl_categories.id', 'tbl_categories.parent_id')->Join('tbl_categories', 'tbl_posts.category_id', '=', 'tbl_categories.id')->whereIn('tbl_posts.id', $final_id)
							->where('user_id', '=', $adPostedUserId)->where('active', 1)->orderby('created_at', 'desc')->get();
						foreach ($products as $product) {
							$main_id[] = $product->parent_id;
						}
						$main_count = array_count_values($main_id);
						//dd($main_count);
						?>
						<div class="category_box_line w-36 ">
							<a href="{{url('seller-profile/'.$seller_info->id)}}">
								<div class="border rounded-md w-28 h-32 flex items-center justify-center">
									<span class="text-light-gray poppins-500 text-sm text-center">{{count($products)}} Ads- All Categories</span>
								</div>
							</a>
						</div>
						<!--category-loop-->
						@foreach($main_count as $key => $value)
						<div class="category_box_line w-36 cat_filter" data-id="{{$key}}">
							<div class="group border rounded-md w-38  flex items-center flex-wrap transition ease-in-out justify-center hover:bg-green-500 hover:text-white">
								<div class="rounded-md ">
									<?php
									$data = App\Models\TblPost::select('tbl_posts.id', 'tbl_posts.user_id', 'tbl_posts.category_id', 'tbl_posts.title', 'tbl_posts.images', 'tbl_posts.created_at', 'tbl_categories.id', 'tbl_categories.parent_id')->Join('tbl_categories', 'tbl_posts.category_id', '=', 'tbl_categories.id')->where('user_id', '=', $adPostedUserId)->where('parent_id', $key)->orderby('created_at', 'desc')->first();
									$img = explode(',', $data->images);
									$cat_name = App\Models\TblCategory::where('id', $key)->value('title');
									?>
									<img class="inline-block  p-1 h-24 rounded-md rounded-md" src="{{ URL::to('storage/'.$img['0']) }}">
								</div>
								<span class="text-light-gray transition ease-in-out group-hover:text-white poppins-500 text-xs p-1 h-9 flex items-center justify-center">
									{{$value}} Ads -{{$cat_name}}
								</span>
							</div>
						</div>

						@endforeach
						<!--end-->
					</div>
				</div>


				<div class="float-left w-full ">
					<div class="w-full float-left">
						<h1 class="text-black font-bold text-xl mb-6 lg:mb-10">{{__('messages.Published Ads')}}</h1>
					</div>
				</div>
				<!---grid-list-->
				<?php $dir_rtl =  App\Models\Setting::is_dir_rtl();
				$class_dir = ($dir_rtl == "true") ? 'dir=rtl' : '';
				$class_dir_float = ($dir_rtl == "true") ? 'float-right' : 'float-left';
				$class_dir_filter = ($dir_rtl == "true") ? 'float-left' : 'float-right';
				$class_dir_padd = ($dir_rtl == "true") ? 'pr-0 lg:pr-6' : 'pl-0 lg:pl-4	';
				$class_dir_txt_right = ($dir_rtl == "true") ? 'text-right' : '';
				$class_dir_btn_filters = ($dir_rtl == "true") ? 'flex flex-row-reverse justify-end' : 'text-left';
				$class_sort_by_txt = ($dir_rtl == "false") ? "float-right" : "";
				$class_dir_add_cls = ($dir_rtl == "true") ? 'ar' : "";
				$sort_by = (!empty(request()->sort) ? request()->sort : "");
				?>
				<div class="w-full float-right h-14 md:h-auto flex items-center justify-between hidden">
					<div class="flex items-center">
						<button type="button" class="mr-1 px-2 py-1  hover:bg-green-500" id="grid"><i class="fa fa-th"></i></button>
						<button type="button" class="mr-1 px-2 py-1  hover:bg-green-500" id="list"><i class="fa fa-th-list"></i></button>
					</div>
					<!-- <button type="button" class="mx-1 px-2 py-1  hover:bg-green-500" id="map"><i class="fa fa-map-marker"></i></button> -->
					<div class="{{$class_dir_filter}} float-right flex items-center">
						<?php $sort_select = 'selected class="font-bold text-gray-900"'; ?>
						<label class="text-gray-700 text-sm sm:text-base  mr-2 sm:mr-4 mt-0 inline-block text-dark-gray poppins-500">{{__('p_search.sort_by_colon')}} </label>
						<select id="sort_by" class="text-dark-gray poppins-500 focus:outline-none bg-transparent py-2 sm:py-3 px-3 sm:px-4 rounded-md text-sm sm:text-base capitalize {{$class_sort_by_txt}} text-gray-400 font-semibold">
							<option value="post-desc" <?php if ($sort_by == 'post-desc') {
															echo $sort_select;
														} ?>>{{__('p_search.recently_posted')}}</option>
							<option value="price-asc" <?php if ($sort_by == 'price-asc') {
															echo $sort_select;
														} ?>>{{__('p_search.price_low_to_high')}}</option>
							<option value="price-desc" <?php if ($sort_by == 'price-desc') {
															echo $sort_select;
														} ?>>{{__('p_search.price_high_to_low')}}</option>
							<option value="most-viewed" <?php if ($sort_by == 'most-viewed') {
															echo $sort_select;
														} ?>>{{__('p_search.popular')}}</option>
						</select>
					</div>
				</div>
				<!--end--->
				<!--grid view-->
				<div id="grid-show">
					@foreach($seller_posts as $ads)
					<!--<div class=" w-1/2 md:w-1/3 sm:w-1/3 lg:w-1/3 xl:w-1/3 pb-3 lg:pb-6 float-left">-->
					<div class=" w-1/2 md:w-1/3 sm:w-1/3 lg:w-1/3 xl:w-1/3 pb-3 lg:pb-6 {{$class_dir_float_lr}}">
						<?php echo $k = App\Models\Setting::htmlAdBlock($ads->id); ?>
					</div>
					@endforeach
				</div>
				<!--end-->

				<!-- list view-->
				<div id="list-show" style='display:none'>
					@foreach($seller_posts as $ads)

					<?php echo $k = App\Models\Setting::viewblock($ads->id); ?>

					@endforeach
				</div>
				<!--end-->

				<div class="w-full float-left">{{ $seller_posts->links() }}</div>
			</div>
		</div>
	</div>
</div>



<!-- Followers list -->
<div class="fixed z-50 inset-0 overflow-y-auto" id="followers_list" style="display:none">
	<div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" {{$class_dir}}>
		<div class="fixed inset-0 transition-opacity" aria-hidden="true">
			<div class="absolute inset-0 bg-gray-500 opacity-75"></div>
		</div>
		<!-- This element is to trick the browser into centering the modal contents. -->
		<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
		<div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
			<div class="bg-white px-6 py-4 sm:px-12 sm:py-8">
				<div class="mb-6">
					<div class="{{$class_dir_text_lr}}">
						<h3 class="block text-xl text-black font-semibold mb-4" id="modal-headline">
							{{__('messages.followers')}}
						</h3>
						<div class="">
							<?php if (count($followers) > 0) { ?>
								@foreach($followers as $follow)
								<?php
								if (!empty($follow->profile_photo_path)) {
									$follower_dp = URL::to('storage/' . $follow->profile_photo_path);
								} else {
									$follower_dp = URL::asset('storage/profile-avatar.jpg');
								}
								?>
								<div class=" py-2 sm:py-4 border-b-2 border-gray-200">
									<a href="<?php echo URL::to('seller-profile/' . $follow->id) ?>">
										<div class="flex items-center">
											<img class="w-10 h-10 md:w-12 md:h-12 {{$class_dir_flw}} rounded-full " src="<?php echo $follower_dp; ?>" />
											<div>
												<h3 class="text-base text-black capitalize">{{$follow->name}}</h3>
											</div>
										</div>
									</a>
								</div>
								@endforeach
							<?php } else { ?>
								<div class="text-left">
									<h4 class="text-black text-base md:text-lg capitalize">0 {{__('messages.followers')}}</h4>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>

				<div class="sm:flex sm:flex-row-reverse sm:space-x-3 {{$class_dir_popup_btn}}">
					<button type="button" id="close_followers_list" class="mt-3 w-full inline-flex justify-center rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 pb-3 bg-white text-base font-semibold text-gray-700 hover:bg-gray-200 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition ease-in-out duration-500">
						{{__('messages.close')}}
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- followers end -->


<!-- Followings list -->
<div class="fixed z-50 inset-0 overflow-y-auto" id="followings_list" style="display:none">
	<div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" {{$class_dir}}>
		<div class="fixed inset-0 transition-opacity" aria-hidden="true">
			<div class="absolute inset-0 bg-gray-500 opacity-75"></div>
		</div>
		<!-- This element is to trick the browser into centering the modal contents. -->
		<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
		<div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
			<div class="bg-white px-6 py-4 sm:px-12 sm:py-8">
				<div class="mb-6">
					<div class="{{$class_dir_text_lr}}">
						<h3 class="block text-xl text-black font-semibold mb-4" id="modal-headline">
							{{__('messages.following')}}
						</h3>
						<div class="">
							<?php if (count($followings) > 0) { ?>
								@foreach($followings as $following)
								<?php
								if (!empty($following->profile_photo_path)) {
									$following_dp = URL::to('storage/' . $following->profile_photo_path);
								} else {
									$following_dp = URL::asset('storage/profile-avatar.jpg');
								}
								?>
								<div class=" py-2 sm:py-4 border-b-2 border-gray-200">
									<a href="<?php echo URL::to('seller-profile/' . $following->id) ?>">
										<div class="flex items-center">
											<img class="w-10 h-10 md:w-12 md:h-12 {{$class_dir_flw}} rounded-full " src="<?php echo $following_dp; ?>" />
											<div>
												<h3 class="text-base text-black capitalize">{{$following->name}}</h3>
											</div>
										</div>
									</a>
								</div>
								@endforeach
							<?php } else { ?>
								<div class="text-left">
									<h4 class="text-black text-base md:text-lg">0 {{__('messages.following')}}</h4>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>

				<div class="sm:flex sm:flex-row-reverse sm:space-x-3 {{$class_dir_popup_btn}}">
					<button type="button" id="close_following_list" class="mt-3 w-full inline-flex justify-center rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 py-3 bg-white text-base font-semibold text-gray-700 hover:bg-gray-200 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-all ease-in-out duration-500">
						{{__('messages.close')}}
					</button>
				</div>

			</div>

		</div>
	</div>
</div>
<!-- Followings end -->

<!-- Invite friends start --->
<div class="fixed z-50 inset-0 overflow-y-auto" id="invite_friends" style="display:none">
	<div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" {{$class_dir}}>
		<div class="fixed inset-0 transition-opacity" aria-hidden="true">
			<div class="absolute inset-0 bg-gray-500 opacity-75"></div>
		</div>
		<!-- This element is to trick the browser into centering the modal contents. -->
		<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
		<div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
			<div class="bg-white px-6 py-4 sm:px-12 sm:py-8">
				<div class="mb-6">
					<div class="{{$class_dir_text_lr}}">
						<h3 class="block text-xl text-black font-semibold mb-2 sm:mb-4" id="modal-headline">
							{{__('messages.invite friends')}}
						</h3>
						<div class="">
							<div class="py-2">
								<h3 class="text-base text-black font-semibold mb-4">{{__('messages.hi you can invite your friends through email')}}</h3>
								<p class="text-sm md:text-base mt-2 mb-1">{{__('messages.please enter email id here')}}</p>
								<input type="text" name="email_ids" id="email_ids" class="px-2 py-3 text-gray-700 bg-gray-100 rounded text-sm md:text-base shadow focus:outline-none w-full border-l-2 border-gray-400" />
							</div>
						</div>
					</div>
				</div>

				<div class="sm:flex sm:flex-row-reverse sm:space-x-3 {{$class_dir_popup_btn}}">
					<button type="submit" id="invite_frd_submit" class="w-full inline-flex justify-center rounded-md border-2 border-transparent shadow-sm px-4 py-2 pb-3 bg-green-500 text-base font-semibold text-white hover:bg-white hover:text-green-500 hover:border-green-500 focus:outline-none sm:w-auto sm:text-sm transition-all ease-in-out duration-500">
						{{__('messages.submit')}}
					</button>

					<button type="button" id="invite_frd_list" class="mt-3 w-full inline-flex justify-center rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 pb-3 bg-white text-base font-semibold text-gray-700 hover:bg-gray-200 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-all ease-in-out duration-500">
						{{__('messages.close')}}
					</button>
				</div>
			</div>

		</div>
	</div>
</div>
<!-- Invite friends end --->

<!-- report this user start --->

<div class="fixed z-50 inset-0 overflow-y-auto" id="report" style="display:none">
	<div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" {{$class_dir}}>
		<div class="fixed inset-0 transition-opacity" aria-hidden="true">
			<div class="absolute inset-0 bg-gray-500 opacity-75"></div>
		</div>
		<!-- This element is to trick the browser into centering the modal contents. -->
		<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
		<div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
			<div class="bg-white px-4 py-4 sm:px-4 sm:py-8">
				<div class="mb-6">
					<div class="{{$class_dir_text_lr}}">
						<h3 class="block text-xl text-black font-semibold mb-2 sm:mb-4" id="modal-headline">
							{{__('messages.report user')}}
						</h3>
						<div class="">
							<div class="py-2">
								@foreach($report_types as $report)
								<label class="block text-base text-black font-semibold mb-2"><input type="radio" value="{{$report->id}}" name="re_type" class="re_type mx-2" required>{{$report->name}}</label>
								@endforeach
							</div>
							<div class="py-2">
								<label class="block text-base text-black font-semibold mb-2 sm:mb-4">{{__('messages.comment')}}:</label>
								<textarea id="comment" maxlength="500" placeholder="Type here" rows="3" required class="text-base appearance-none border-0 border-l-2 border-gray-400 bg-gray-100 rounded w-full p-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
								<p class="text-xs font-medium item-center text-black">{{__('messages.character limit')}}: 500</p>
							</div>
						</div>
					</div>
				</div>
				<div class="sm:flex sm:flex-row-reverse sm:space-x-3 {{$class_dir_popup_btn}}">
					<button type="button" class="w-full inline-flex justify-center rounded-md border-2 border-transparent shadow-sm px-4 py-2 pb-3 bg-green-500 text-base font-semibold text-white hover:bg-white hover:text-green-500 hover:border-green-500 focus:outline-none sm:w-auto sm:text-sm transition-all ease-in-out duration-500" id="submit">
						{{__('messages.send complaint')}}
					</button>
					<button type="button" id="cancel" class="w-full inline-flex justify-center rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 pb-3 bg-white text-base font-semibold text-gray-700 hover:bg-gray-200 focus:outline-none sm:mt-0 mt-3 sm:w-auto sm:text-sm transition-all ease-in-out duration-500">
						{{__('messages.cancel')}}
					</button>
				</div>
			</div>

		</div>
	</div>
</div>
<div class="fixed z-50 inset-0 overflow-y-auto" id="seller_review_list" style="display:none">
	<div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" {{$class_dir}}>
		<div class="fixed inset-0 transition-opacity" aria-hidden="true">
			<div class="absolute inset-0 bg-gray-500 opacity-75"></div>
		</div>
		<!-- This element is to trick the browser into centering the modal contents. -->
		<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
		<div class=" popup_top relative top-16 inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
			<div class="bg-white px-4 py-4 sm:px-4 sm:py-8 lg:pb-4">
				<div class="mb-6">
					<div class="{{$class_dir_text_lr}}">
						<h3 class="block text-xl poppins-500 text-gray-700  mb-2" id="modal-headline">
							Seller Review
						</h3>



						<div class="">

							<!--  -->

							<!--post review begin-->
							@if(Auth::user())
							@if(auth()->user()->hasRole('User') == true && $is_buy == 1)
							<?php
							/* check - user already review this post.. */
							$chk_user_id = auth()->user()->id;
							// $chk_post_id = $product[0]->id;
							// $chk_post_user_id = $product[0]->user_id;
							// $chk_review = App\Models\TblReview::where('user_id', $chk_user_id)->where('post_id', $chk_post_id)->count();
							?>

							<form action="{{ URL::to('seller-review-store') }}" method="POST" enctype="multipart/form-data">
								@csrf
								<?php $starwhite = URL::asset('/images/star1.png');
								$starFill = URL::asset('/images/star2.png'); ?>
								<div class="float-right mb-2 mt-3">
									<label class="inline-flex items-center mr-2 text-green-500 text-xl"><input type="hidden" id="ad1_hidden" value="1"><img src="{{ $starwhite }}" onmouseover="change(this.id);" id="ad1" class="w-3 h-3 lg:w-4 lg:h-4" /></label>
									<label class="inline-flex items-center mr-2 text-green-500 text-xl"><input type="hidden" id="ad2_hidden" value="2"><img src="{{ $starwhite }}" onmouseover="change(this.id);" id="ad2" class="w-3 h-3 lg:w-4 lg:h-4" /></label>
									<label class="inline-flex items-center mr-2 text-green-500 text-xl"><input type="hidden" id="ad3_hidden" value="3"><img src="{{ $starwhite }}" onmouseover="change(this.id);" id="ad3" class="w-3 h-3 lg:w-4 lg:h-4" /></label>
									<label class="inline-flex items-center mr-2 text-green-500 text-xl"><input type="hidden" id="ad4_hidden" value="4"><img src="{{ $starwhite }}" onmouseover="change(this.id);" id="ad4" class="w-3 h-3 lg:w-4 lg:h-4" /></label>
									<label class="inline-flex items-center mr-2 text-green-500 text-xl"><input type="hidden" id="ad5_hidden" value="5"><img src="{{ $starwhite }}" onmouseover="change(this.id);" id="ad5" class="w-3 h-3 lg:w-4 lg:h-4" /></label>
								</div>
								<textarea maxlength="500" class="review_text w-full h-28 rounded-lg px-6 py-4 text-base text-black mb-1 border-l-4 border-gray-400 bg-gray-100 focus:outline-none resize-none placeholder-black" placeholder="{{__('post_detail.write your review')}}" name="review_text" required="required"></textarea>
								<p class="text-xs font-bold item-center text-gray-800 inline-block">{{__('post_detail.character limit')}}: 500</p>
								<input type="hidden" name="review_ratings" id="adrating" />

								<input type="hidden" name="seller_id" value="{{$seller_info->id}}" />
								<input type="hidden" name="user_id" value="{{Auth::user()->id}}" />
								<input type="hidden" name="redirect_url" value="{{ request()->path() }}" />
								<button type="submit()" class="float-right px-3 py-1 text-base poppins-500 cursor-pointer outline-none focus:outline-none rounded-md border border-green-500 bg-transparent hover:bg-green-500 hover:border-green-500 text-green-500 hover:text-white transition-all ease-linear duration-500">{{__('post_detail.save')}}</button>
							</form>

							<?php
							$blacklist_words = App\Models\TblPost::get_blacklist();
							?>
							<!--review end-->
							<script>
								// remove blacklist words
								$(document).ready(function() {
									$(".review_text").on('keyup', function(e) {
										var blacklist = <?php echo $blacklist_words; ?>;
										var words = $(".review_text").val();
										var str = words.trim().split(" ");
										var lastWord = str[str.length - 1];
										var lowerword = lastWord.toLowerCase();
										var array_index = jQuery.inArray(lowerword, blacklist);
										if (array_index >= 0) {
											$(".review_text").val($(".review_text").val().replace(lastWord, ''));
										}
									});
								});
							</script>
							@else
							<div class="text-center">
								
								<p class="text-green-500 font-bold uppercase text-md	 rounded shadow outline-none text-center py-4 focus:outline-none mr-1 transition-all ease-linear duration-500">You need to buy from the seller to give review</p>
							</div>
							@endif
							@else
							<div class="text-center">
								<button class="bg-green-700 text-white font-bold uppercase text-xs rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 transition-all ease-linear duration-500" type="button">
									<a class="px-4 py-2 block" href="{{ URL::to('login') }}">{{__('post_detail.login to post review')}}</a></button>
							</div>
							@endif

							<div class="float-left w-full custom_scroll mt-3 review_middel_box h-64 overflow-y-auto pb-4">
								@foreach($review as $r)
								<?php
								// print_r($r);
								$newform = date('d M Y', strtotime($r->created_at)) ?>
								@if(($r->approved == "1"))
								<div class="w-full float-left mt-40md:mt-2 test border border-b-1 border-t-0 border-r-0 border-l-0 ">
									<div class="w-full float-left">
										<div class="bg-white  w-full float-left lg:px-4 py-2 lg:py-3">
											<div class="float-left w-full">
												<div class="float-left rev_left w-full  lg:w-1/4">
													<div class="mb-1 w-full">
														<?php
														$avg_rating = $r->ratings;
														$starwhite = URL::asset('/images/star1.png');
														$starFill = URL::asset('/images/star2.png');
														?>
														<label class="inline-flex items-center mr-1 text-green-500 text-xl"><img src="{{ ($avg_rating>=1)?$starFill:$starwhite }}" class="w-3 h-3 lg:w-4 lg:h-4" /></label>
														<label class="inline-flex items-center mr-1 text-green-500 text-xl"><img src="{{ ($avg_rating>=2)?$starFill:$starwhite }}" class="w-3 h-3 lg:w-4 lg:h-4" /></label>
														<label class="inline-flex items-center mr-1 text-green-500 text-xl"><img src="{{ ($avg_rating>=3)?$starFill:$starwhite }}" class="w-3 h-3 lg:w-4 lg:h-4" /></label>
														<label class="inline-flex items-center mr-1 text-green-500 text-xl"><img src="{{ ($avg_rating>=4)?$starFill:$starwhite }}" class="w-3 h-3 lg:w-4 lg:h-4" /></label>
														<label class="inline-flex items-center mr-1 text-green-500 text-xl"><img src="{{ ($avg_rating>=5)?$starFill:$starwhite }}" class="w-3 h-3 lg:w-4 lg:h-4" /></label>
													</div>
													<div class="mb-1 w-full">
														<p class="text-base capitalize text-black poppins-100">{{ $r->name }}</p>
													</div>
													<div class="mb-1 w-full">
														<p class="text-sm poppins-400 text-gray-400">{{ $newform }}</p>
													</div>
												</div>
												<div class="float-left rev_right md:pl-2">
													<div class="mb-2">
														<p class="text-base text-black">{{ $r->comment }}</p>
													</div>
												</div>
											</div>
											<?php
											$user_id = $r->user_id;
											$seller_id = $r->seller_id;
											$user_profile = App\Models\User_profile::where('user_id', $user_id)->first();
											// dd($user_profile->phone);
											$phone = !empty($user_profile->phone) ? $user_profile->phone : "";
											$urlnew = URL::to('/chatting');
											?>
								
										</div>

									</div>
								</div>
								@endif
								@if(auth()->user())
								@if($r->approved != "1" && auth()->user()->id == $r->user_id)
							</div>
							<div class="w-full float-left mt-4 md:mt-8">
								<div class="bg-gray-100 px-4 py-4">
									<div class="bg-white px-4 py-4">
										<div class="mb-2">
											<div class="inline-flex items-center mr-2">
												<p class="bg-green-500 rounded-lg border text-white text-lg px-3 py-1">{{ $r->ratings }}<i class="fa fa-star-o ml-2"></i></p>
											</div>
											<div class="inline-flex items-center mr-2">
												<p class="text-lg text-black font-semibold">{{ $r->name }}</p>
											</div>
										</div>

										<div class="mb-2">
											<p class="text-base text-black">{{ $r->comment }}</p>
										</div>

										<div class="mb-1">
											<p class="text-base md:text-lg text-black">{{ $newform }}</p>
										</div>

										<div>
											<p class="text-base md:text-lg text-red-500">{{__('post_detail.your comment is waiting for approval')}}.</p>
										</div>
									</div>
								</div>
							</div>

							@endif
							@endif
							@endforeach


						</div>
					</div>
				</div>

				<div class="sm:flex sm:flex-row-reverse sm:space-x-3 {{$class_dir_popup_btn}} absolute right-5 top-0 lg:top-5">
					<button type="button" id="close_review_list" class="mt-3 justify-center rounded-md   px-3 py-1.5 bg-green-500 text-base font-semibold text-white hover:bg-gray-200 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-all ease-in-out duration-500  ">
						<i class="fa fa-times" aria-hidden="true"></i>
					</button>
				</div>

			</div>

		</div>

	</div>
</div>

<!-- report this user end --->

<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	/* Followers list popup start */
	$('#followers_list_click').on('click', function(e) {
		document.querySelector("#followers_list").style.display = "block";
	});
	$('#close_followers_list').on('click', function(e) {
		document.querySelector("#followers_list").style.display = "none";
	});
	/* Followers list popup end */
	/* Following list popup start */
	$('#close_following_list').on('click', function(e) {
		document.querySelector("#followings_list").style.display = "none";
	});
	$('#followings_list_click').on('click', function(e) {
		document.querySelector("#followings_list").style.display = "block";
	});
	/* Following list popup end */

	/* Invite friends start*/

	$('.invite_friends').on('click', function(e) {
		var user = "{{auth()->user()}}";
		if (user == "") {
			window.location.href = "/login";
		} else {
			document.querySelector("#invite_friends").style.display = "block";
		}
		$('#invite_frd_submit').on('click', function(e) {
			var email_ids = $('#email_ids').val();
			if (email_ids == "") {
				toastr.warning('Email Id field is required..');
			} else {
				var mailformat = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;
				if (email_ids.match(mailformat)) {
					$.ajax({
						type: 'POST',
						dataType: 'json',
						url: "{{ route('invite_friend') }}",
						data: {
							email_ids: email_ids
						},
						success: function(data) {

							$('#email_ids').val('');
							if (data.result == "error") {
								toastr.warning(data.message);
							} else {
								document.querySelector("#invite_friends").style.display = "none";
								toastr.success(data.message);
							}

						}
					});
				} else {
					toastr.warning('Invalid Email Id');
				}
			}
		});
		$('#invite_frd_list').on('click', function(e) {
			document.querySelector("#invite_friends").style.display = "none";
		});
	});

	//category filter start 
	$('.cat_filter').on('click', function() {

		var cat_id = $(this).attr("data-id");
		var path = window.location.href.replace(/\/\d+$/, "") + "/" + cat_id;
		window.location = path;

	})
	//category filter end

	/* Invite friends end */
	$(".save_to_followers").click(function(e) {
		var val = $(this).attr("data-seller-id");
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: "{{ route('savefollowers') }}",
			data: {
				seller_id: val
			},
			success: function(data) {
				if (data.result == "failed" && data.flag == "0") {
					toastr.warning(data.message);
				} else if (data.result == "success" && data.flag == "1") {
					var s_cnt = parseInt($(".seller_followers").val()) + 1;
					$(".seller_followers").val(s_cnt);
					$("#seller_followers").text(s_cnt);

					$("#seller_" + val).text("Unfollow");
					toastr.success(data.message);
					location.reload();
				} else {
					var s_cnt = parseInt($(".seller_followers").val()) - 1;
					$(".seller_followers").val(s_cnt);
					$("#seller_followers").text(s_cnt);
					$("#seller_" + val).text("Follow");
					toastr.success(data.message);
					location.reload();
				}

			}
		});
	});


	/* report user start */
	$('#report_user').on('click', function(e) {

		var user = "{{auth()->user()}}";
		var reportUser = $(this).attr("postid");
		if (user == "") {
			window.location.href = "/login";
		} else {
			document.querySelector("#report").style.display = "block";
		}
		$('#submit').on('click', function(e) {
			var comment = $('#comment').val();
			var retype = $('input[name="re_type"]:checked').val();
			if (retype == null) {
				toastr.warning('report type field is required..');
				return;
			}
			if (comment == "") //change if condition
			{
				toastr.warning('Comment field is required..');
				return;
			}
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: "{{ route('report_user') }}",
				data: {
					comment: comment,
					retype: retype,
					user: user,
					reportUser: reportUser
				},
				success: function(data) {
					window.location.reload();
					document.querySelector("#report").style.display = "none";
					$('#comment').val('');
					$('input[name="re_type"]').attr('checked', false);
					toastr.success(data.message);
				}
			});
		});
		$('#cancel').on('click', function(e) {
			document.querySelector("#report").style.display = "none";
		});
	});

	$(".monday").click(function() {
		$("#monday").toggle();
		$("#monday_close").toggle();
		$('#tuesday').hide();
		$('#tuesday_close').hide();
		$('#wednesday').hide();
		$('#wednesday_close').hide();
		$('#thursday').hide();
		$('#thursday_close').hide();
		$('#friday').hide();
		$('#friday_close').hide();
		$('#saturday').hide();
		$('#saturday_close').hide();
		$('#sunday').hide();
		$('#sunday_close').hide();
	});

	$(".tuesday").click(function() {
		$('#monday').hide();
		$('#monday_close').hide();
		$("#tuesday").toggle('');
		$("#tuesday_close").toggle();
		$('#wednesday').hide();
		$('#wednesday_close').hide();
		$('#thursday').hide();
		$('#thursday_close').hide();
		$('#friday').hide();
		$('#friday_close').hide();
		$('#saturday').hide();
		$('#saturday_close').hide();
		$('#sunday').hide();
		$('#sunday_close').hide();
	});

	$(".wednesday").click(function() {

		$("#wednesday").toggle();
		$("#wednesday_close").toggle();
		$('#monday').hide();
		$('#monday_close').hide();
		$('#tuesday').hide();
		$('#tuesday_close').hide();
		$('#thursday').hide();
		$('#thursday_close').hide();
		$('#friday').hide();
		$('#friday_close').hide();
		$('#saturday').hide();
		$('#saturday_close').hide();
		$('#sunday').hide();
		$('#sunday_close').hide();
	});
	$(".thursday").click(function() {
		$("#thursday").toggle();
		$("#thursday_close").toggle();
		$('#monday').hide();
		$('#monday_close').hide();
		$('#tuesday').hide();
		$('#tuesday_close').hide();
		$('#wednesday').hide();
		$('#wednesday_close').hide();
		$('#friday').hide();
		$('#friday_close').hide();
		$('#saturday').hide();
		$('#saturday_close').hide();
		$('#sunday').hide();
		$('#sunday_close').hide();
	});
	$(".friday").click(function() {
		$("#friday").toggle();
		$("#friday_close").toggle();
		$('#monday').hide();
		$('#monday_close').hide();
		$('#tuesday').hide();
		$('#tuesday_close').hide();
		$('#wednesday').hide();
		$('#wednesday_close').hide();
		$('#thursday').hide();
		$('#thursday_close').hide();
		$('#saturday').hide();
		$('#saturday_close').hide();
		$('#sunday').hide();
		$('#sunday_close').hide();
	});
	$(".saturday").click(function() {
		$('#saturday').toggle();
		$("#saturday_close").toggle();
		$('#monday').hide();
		$('#monday_close').hide();
		$('#tuesday').hide();
		$('#tuesday_close').hide();
		$('#wednesday').hide();
		$('#wednesday_close').hide();
		$('#thursday').hide();
		$('#thursday_close').hide();
		$('#friday').hide();
		$('#friday_close').hide();
		$('#sunday').hide();
		$('#sunday_close').hide();
	});
	$(".sunday").click(function() {
		$('#sunday').toggle();
		$("#sunday_close").toggle();
		$('#monday').hide();
		$('#monday_close').hide();
		$('#tuesday').hide();
		$('#tuesday_close').hide();
		$('#wednesday').hide();
		$('#wednesday_close').hide();
		$('#thursday').hide();
		$('#thursday_close').hide();
		$('#friday').hide();
		$('#friday_close').hide();
		$('#saturday').hide();
		$('#saturday_close').hide();
	});


	$(document).ready(function() {

		$('#monday').hide();
		$('#monday_close').hide();
		$('#tuesday').hide();
		$('#tuesday_close').hide();
		$('#wednesday').hide();
		$('#wednesday_close').hide();
		$('#thursday').hide();
		$('#thursday_close').hide();
		$('#friday').hide();
		$('#friday_close').hide();
		$('#saturday').hide();
		$('#saturday_close').hide();
		$('#sunday').hide();
		$('#sunday_close').hide();

	});

	$("#grid").click(function() {
		$('#grid-show').show();
		$('#list-show').hide();

	});
	$("#list").click(function() {
		$('#list-show').show();
		$('#grid-show').hide();
	});
	/* report user end */
	$(document).ready(function() {

		$('#review_list_click').on('click', function(e) {

			var is_buy = $('.is_buy').val();
			// console.log(is_buy);
			// if(is_buy == 1)
			// {
				document.querySelector("#seller_review_list").style.display = "block";
			// }else{

			// 	alert('you need to buy from the seller to give review');
			// }
			// 
		});
		$('#close_review_list').on('click', function(e) {
			document.querySelector("#seller_review_list").style.display = "none";
		});





		// 	$("#seller_review_list").hide();
		// $('#review_list_click').on('click', function(e) {
		// 	$("#seller_review_list").show();
		// 		});
		// 	$('#close_review_list').on('click', function(e) {
		// 		$("#seller_review_list").hide();	
		// 	});
	});
	var staryellow = "{{ URL::asset('/images/star2.png') }}";
	var starwhite = "{{ URL::asset('/images/star1.png') }}";

	function change(id) {
		var cname = "ad";
		var ab = document.getElementById(id + "_hidden").value;
		document.getElementById(cname + "rating").value = ab;
		//set input values dynamicaly
		var element = document.getElementById(cname + "rating");
		element.dispatchEvent(new Event('input'));
		for (var i = ab; i >= 1; i--) {
			document.getElementById(cname + i).src = staryellow;
		}
		var id = parseInt(ab) + 1;
		for (var j = id; j <= 5; j++) {
			document.getElementById(cname + j).src = starwhite;
		}
	}
</script>