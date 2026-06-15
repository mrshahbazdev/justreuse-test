<?php
$dir_rtl =  App\Models\Setting::is_dir_rtl();
$main_categories = App\Models\TblCategory::get_all_main_categories();
?>
<div class="float-left w-full px-2">
	<div class="w-full  mt-0 bg-gray-500  float-left relative z-auto mt-px innerpage_category rounded-full">
		<div class="container mx-auto px-4">
			<!-- Desktop -->
			<div class="hidden md:block relative z-40">
				<div class="block">
					<!--<ul class="cate_menu_slider mx-12 z-10 list-reset md:flex flex-1 items-center">-->
					<ul class="mx-12 z-20 list-reset md:flex flex-1 items-center">
						<?php
						$class_dir_slider = ($dir_rtl == "true") ? 'ar' : "";
						$class_dir_submenu = ($dir_rtl == "true") ? 'float-right' : "float-left";
						?>
						<div class="{{$class_dir_slider}} menu_slider_owl owl-carousel owl-theme">
							<div class="item py-2.5">
								<li class="inline-block relative main-menu">
									<a class="block text-base md:text-sm  xl:text-base text-left md:text-center font-normal no-underline p-6 hover:text-green-500 text-gray-700 no-underline ease-linear transition-all duration-500 border-gray-200 border-l w-full float-left poppins-500 px-3" href="#">
										<span class="inline-block align-middle w-8 float-left inner_page_icon">
											<img src="{{URL::to('images/all-categories.png')}}" alt="All Categories">
										</span>
										<span class="inline-block align-middle ml-4 float-left md:mt-2 lg:mt-0 poppins-500">
											{{__('catagories.All Categories')}}</span>
									</a>
									<?php $all_cat_class = ($dir_rtl == "true") ? 'right-0' : "left-0"; ?>
									<ul class="all_cate_dropdown overflow-y-auto h-screen sub_menu absolute z-20 top-full {{$all_cat_class}} bg-white shadow-md px-4 py-2 border border-gray-300">
										@foreach($main_categories as $main_category)
										<?php
										$sub_categories = App\Models\TblCategory::orderBy('list_order', 'asc')->descendantsAndSelf($main_category->id);
										?>
										<?php $title = __($main_category->title); ?>
										<li class="{{$class_dir_submenu}} w-1/4 p-3">
											<a class="text-opacity-100 block px-2 py-2 hover:text-green-500 text-base  xl:text-base font-bold mb-2 " href="<?php echo Session::get('Searchedurl') . "&c=$main_category->slug"; ?>">{{__('catagories.' .$title)}}</a>
											<ul>
												@foreach($sub_categories as $sub_category)
												<?php
												if ($sub_category->parent_id != "") {
													$child = App\Models\TblCategory::where('id', $sub_category->parent_id)->pluck('parent_id')->first();
													if (empty($child)) {
												?>
														<li class="">
															<?php $title = __($sub_category->title); ?>
															<a class="block px-2 mb-1 hover:text-green-500 text-sm lg:text-base " href="<?php echo Session::get('Searchedurl') . "&c=$sub_category->slug"; ?>">{{__('catagories.' .$title)}}</a>
														</li>
												<?php }
												} ?>
												@endforeach
											</ul>
										</li>
										@endforeach
									</ul>
								</li>
							</div>
							@foreach($main_categories as $main_category)
							<?php
							$cat_img = !empty($main_category->image) ? URL::to('storage') . '/' . $main_category->image : URL::to('storage/noimage150.png');
							?>
							<div class="item pt-2.5">
								<li class="inline-block relative main-menu">
									<a class="block text-base md:text-sm  xl:text-base text-left md:text-center font-normal no-underline p-6 hover:text-green-500 text-gray-700 no-underline ease-linear transition-all duration-500 border-gray-200 border-l w-full float-left px-3 poppins-500" href="<?php echo Session::get('Searchedurl') . "&c=$main_category->slug"; ?>">
										<span class="inline-block align-middle w-8 float-left inner_page_icon">
											<img class="md:h-8 lg:h-auto" src="{{$cat_img}}" alt="{{$main_category->title}}">
										</span>
										<?php $title = __($main_category->title); ?>
										<span class="inline-block align-middle ml-4 float-left md:mt-2 lg:mt-0 poppins-500">{{__('catagories.' .$title)}}</span>
									</a>
									<ul class="sub_menu absolute z-20 top-full {{$all_cat_class}} bg-white shadow-md w-64">
										<?php $result = App\Models\TblCategory::whereDescendantOf($main_category->id)->get();
										foreach ($result as $k) {
											$sublink = Session::get('Searchedurl') . "&c=$k->slug";
											$sub_title = $k->title;
										?>
											<li class="border-b border-gray-200"><a class="text-opacity-100 block px-4 py-3 hover:text-green-500 text-sm lg:text-base" href="{{$sublink}}">{{__($sub_title)}}</a></li>
										<?php } ?>
									</ul>
								</li>
							</div>
							@endforeach
						</div>
						<div class="owl-nav custom-owl-buttons"><button type="button" role="presentation" class="owl-prev"><span aria-label="Previous"></span></button><button type="button" role="presentation" class="owl-next"><span aria-label="Next"></span></button></div>
					</ul>
				</div>
				<!--<div class="absolute left-0 right-0 top-0 bottom-0 flex items-center z-0">
								<span class="inline-block bg-green-500 rounded-full text-center leading-9 text-white cate_menu_left_arrow w-9 h-9 slick-arrow absolute left-0 cursor-pointer"><i class="fa fa-angle-left" aria-hidden="true"></i></span>
								<span class="inline-block bg-green-500 rounded-full text-center leading-9 text-white cate_menu_right_arrow w-9 h-9 slick-arrow absolute right-0 cursor-pointer"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
							</div>-->
			</div>
			<!-- Desktop -->
			<!--<div x-data="{ open: false }" class="block w-full-float-left">-->
			<!--<div class="md:hidden shadow-xl bg-white md:shadow-none md:bg-gray-100 w-full lg:w-full block  lg:mt-0 lg:text-left float-left">-->
			<?php if ($dir_rtl == "false") {  ?>
				<div class="md:hidden responsive_menu bg-white">
					<div class="res_menu_close text-2xl text-green-500 font-semibold absolute top-5 right-5 z-50 cursor-pointer">X</div>
					<div class="relative z-40">
						<div class="block">
							<!--<ul class="cate_menu_slider mx-12 z-10 list-reset md:flex flex-1 items-center">-->
							<ul class="z-20 list-reset md:flex flex-1 items-center pt-12">

								<li class="inline-block relative w-full">
									<a class="inline-block text-lg text-left font-normal no-underline py-3 px-4 hover:text-green-500 text-gray-700 no-underline focus:outline-none ease-linear transition-all duration-500 border-gray-200 border-l w-full float-left font-bold" href="#">
										<!--<span class="inline-block align-middle w-8 float-left">
													<img src="{{URL::to('images/all-categories.png')}}" alt="All Categories">
													</span>-->
										<span class="inline-block align-middle float-left">{{__('catagories.All Categories')}}</span>
									</a>
									<ul class="all_cat_menu bg-white shadow-md px-4 inline-block w-full">
										@foreach($main_categories as $main_category)
										<?php
										$sub_categories = App\Models\TblCategory::orderBy('list_order', 'asc')->descendantsAndSelf($main_category->id);
										?>
										<?php $title = __($main_category->title); ?>
										<li class="w-full inline-block sub_cat_menu relative">
											<a class="text-opacity-100 block py-4 hover:text-green-500 text-base font-semibold border-b border-gray-200" href="<?php echo Session::get('Searchedurl') . "&c=$main_category->slug"; ?>">{{__('catagories.' .$title)}}</a>
											<span class="absolute top-4 right-0 px-4 hover:text-green-500 cursor-pointer"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
											<ul class="child_cat_menu hidden">
												@foreach($sub_categories as $sub_category)
												<?php
												if ($sub_category->parent_id != "") {
													$child = App\Models\TblCategory::where('id', $sub_category->parent_id)->pluck('parent_id')->first();
													if (empty($child)) {
												?>
														<?php $title = __($sub_category->title); ?>
														<li class="w-full inline-block border-b border-gray-200">
															<a class="block px-4 py-3 hover:text-green-500 text-sm font-semibold" href="<?php echo Session::get('Searchedurl') . "&c=$sub_category->slug"; ?>">{{__('catagories.' .$title)}}</a>
														</li>
												<?php }
												} ?>
												@endforeach
											</ul>
										</li>
										@endforeach
									</ul>
								</li>
							</ul>
						</div>
						<!--<div class="absolute left-0 right-0 top-0 bottom-0 flex items-center z-0">
								<span class="inline-block bg-green-500 rounded-full text-center leading-9 text-white cate_menu_left_arrow w-9 h-9 slick-arrow absolute left-0 cursor-pointer"><i class="fa fa-angle-left" aria-hidden="true"></i></span>
								<span class="inline-block bg-green-500 rounded-full text-center leading-9 text-white cate_menu_right_arrow w-9 h-9 slick-arrow absolute right-0 cursor-pointer"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
							</div>-->
					</div>
					<!--</div>-->
				</div>
				<!--</div>-->
			<?php } else { ?>
				<div class="md:hidden rtl responsive_menu bg-white" dir="rtl">
					<div class="res_menu_close text-2xl text-green-500 font-semibold absolute top-5 left-5 z-50 cursor-pointer">X</div>
					<div class="relative z-40">
						<div class="block">
							<!--<ul class="cate_menu_slider mx-12 z-10 list-reset md:flex flex-1 items-center">-->
							<ul class="z-20 list-reset md:flex flex-1 items-center pt-12">

								<li class="inline-block relative w-full">
									<a class="inline-block text-lg font-normal no-underline py-3 px-4 hover:text-green-500 text-gray-700 no-underline focus:outline-none ease-linear transition-all duration-500 border-gray-200 border-l w-full float-left font-bold" href="#">
										<!--<span class="inline-block align-middle w-8 float-left">
													<img src="{{URL::to('images/all-categories.png')}}" alt="All Categories">
													</span>-->
										<span class="inline-block align-middle">{{__('catagories.All Categories')}}</span>
									</a>
									<ul class="all_cat_menu bg-white shadow-md px-4 inline-block w-full">
										@foreach($main_categories as $main_category)
										<?php
										$sub_categories = App\Models\TblCategory::orderBy('list_order', 'asc')->descendantsAndSelf($main_category->id);
										?>
										<?php $title = __($main_category->title); ?>
										<li class="w-full inline-block sub_cat_menu relative">
											<a class="text-opacity-100 block py-4 hover:text-green-500 text-base font-semibold border-b border-gray-200" href="<?php echo Session::get('Searchedurl') . "&c=$main_category->slug"; ?>">{{__('catagories.' .$title)}}</a>
											<span class="absolute top-4 left-0 px-4 hover:text-green-500 cursor-pointer"><i class="fa fa-caret-down" aria-hidden="true"></i></span>
											<ul class="child_cat_menu hidden">
												@foreach($sub_categories as $sub_category)
												<?php
												if ($sub_category->parent_id != "") {
													$child = App\Models\TblCategory::where('id', $sub_category->parent_id)->pluck('parent_id')->first();
													if (empty($child)) {
												?>
														<?php $title = __($sub_category->title); ?>
														<li class="w-full inline-block border-b border-gray-200">
															<a class="block px-4 py-3 hover:text-green-500 text-sm font-semibold" href="<?php echo Session::get('Searchedurl') . "&c=$sub_category->slug"; ?>">{{__('catagories.' .$title)}}</a>
														</li>
												<?php }
												} ?>
												@endforeach
											</ul>
										</li>
										@endforeach
									</ul>
								</li>
							</ul>
						</div>
						<!--<div class="absolute left-0 right-0 top-0 bottom-0 flex items-center z-0">
								<span class="inline-block bg-green-500 rounded-full text-center leading-9 text-white cate_menu_left_arrow w-9 h-9 slick-arrow absolute left-0 cursor-pointer"><i class="fa fa-angle-left" aria-hidden="true"></i></span>
								<span class="inline-block bg-green-500 rounded-full text-center leading-9 text-white cate_menu_right_arrow w-9 h-9 slick-arrow absolute right-0 cursor-pointer"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
							</div>-->
					</div>
					<!--</div>-->
				</div>
			<?php } ?>
		</div>
	</div>
</div>