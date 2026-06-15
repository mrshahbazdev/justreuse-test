<div class="root-element-div">
@if($errors->any())
    <div class="alert alert-danger">
        <p><strong>Opps Something went wrong</strong></p>
        <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
        </ul>
    </div>
@endif
<?php 
	$get_meta = App\Models\TblOtherpage::get_meta('post-add');
	$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
	$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
	$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
	
	
	$dir_rtl =  App\Models\Setting::is_dir_rtl();
	$class_dir = ($dir_rtl=="true")?'dir=rtl':"";
	
	$class_dir_sm_mar_rl = ($dir_rtl=="true")?'sm:ml-10':'sm:mr-10';
	$class_dir_pad_rl = ($dir_rtl=="true")?'pl-3':'pr-3';
	$class_dir_float_lr = ($dir_rtl=="true")?'float-right':'float-left';
	$class_dir_float_rl = ($dir_rtl=="true")?'float-left':'float-right';
	$class_dir_sm_space_r = ($dir_rtl=="true")?'sm:space-x-reverse':'';
	$class_dir_lg_space_r = ($dir_rtl=="true")?'lg:space-x-reverse':'';
	$class_dir_padding_rl = ($dir_rtl=="true")?'pl-0 sm:pl-4 md:pl-8':'pr-0 sm:pr-4 md:pr-8';
	
	

?>

    @if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description))
        @section('meta_title', $meta_title)
        @section('meta_keywords', $meta_keywords)
        @section('meta_description', $meta_description)
	@endif
	
	<!-- Post Ad Details Content -->
	
	<div class="w-full float-left my-6" {{$class_dir}}>
		<div class="container mx-auto px-4">
			<div class="w-full float-left border  border-gray-300 px-4 py-4 xl:px-10 xl:py-7">
				<form class="w-full float-left" action="{{ URL::to('update_post_info') }}" name="post-add" onsubmit="return validateForm()" method="POST" enctype="multipart/form-data">
                @csrf
					<div class="mb-4 sm:mb-8 md:mb-10 lg:mb-12 xl:mb-14">
						<h2 class="text-black text-xl md:text-2xl lg:text-3xl xl:text-4xl poppins-600">{{__('p_myads.post free ads')}}</h2>
					</div>
					<div class="w-full float-left xl:px-16">
						<div class="flex flex-wrap">
                    <!--begin block-->
                                 <!--row 1-->
											
								<div class="sm:flex sm:space-x-2 {{$class_dir_sm_space_r}} lg:space-x-6 {{$class_dir_lg_space_r}} sm:mb-6 md:mb-8 lg:mb-10 w-full">
									<div class="w-full sm:w-1/2 mb-4 sm:mb-0">
										<label class="block text-base text-black  mb-2 sm:mb-4 poppins-500">{{__('p_myads.category')}} <span class="text-red-800">*</span></label>
										<select required id="selected_category" class="selectpicker w-full h-14  px-6 py-4 text-base text-black border-l-2 border-green-800 bg-gray-100 focus:outline-none  placeholder-black" wire:change="onchanging($event.target.value)">
											<option hidden="" disabled="disabled" selected="selected" value="">{{__('p_myads.please select')}}</option>
                                                <?php
                                                $traverse = function ($categorylist, $prefix = '') use (&$traverse) {
                                                foreach ($categorylist as $category) {
                                                $isparent = (is_null($category->parent_id))?"disabled":"";
                                                $catname = $prefix . '| ' . $category->title;
                                                $catid = $category->id;
                                                echo '<option value="' . $catid . '" '.$isparent.'>' . $catname . '</option>';
                                                $traverse($category->children, $prefix . '--');
                                                }
                                                };
                                                $traverse($categorylist);
                                                ?>
										</select>
									</div>
									
									<div class="w-full sm:w-1/2 mb-4 sm:mb-0">
										<label class="block text-base text-black  mb-2 sm:mb-4 poppins-500"> {{__('p_myads.ad title')}} <span class="text-red-800">*</span></label>
										<input type="text" class="check_blacklist_title w-full h-14  px-6 py-4 text-base text-black border-l-2 border-green-800 bg-gray-100 focus:outline-none  placeholder-black" value="" name="text-title-sst" id="text-title-sst" required="required">
									</div>
								</div>
                                <input type="hidden" name="post-id" value="0" />
							
                                 <!--row 2-->
								<div class="sm:flex sm:space-x-2 {{$class_dir_sm_space_r}} lg:space-x-6 {{$class_dir_lg_space_r}} sm:mb-6 md:mb-8 lg:mb-10 w-full">
									
									
									<div class="w-full sm:w-1/2 mb-4 sm:mb-0">
										<label class="block text-base text-black  mb-2 sm:mb-4 poppins-500" htmlfor="grid-password">{{__('p_myads.price')}} <span class="text-red-800">*</span></label>
										<div class="flex flex-row">
											<span class="sub-field text-center bg-grey-lighter rounded rounded-r-none {{$class_dir_pad_rl}} text-xs font-bold w-4/6 xl:w-4/12">
												<select id="currency_id" class="w-full h-14  px-2 py-2 md:px-4 md:py-4 text-sm sm:text-base text-black border-l-2 border-green-800 bg-gray-100 focus:outline-none  placeholder-black text-center ">
                                                <?php
                                                $currency = App\Models\TblCurrency::where('active', '0')->orderBy('id', 'desc')->get();
                                                $settings = App\Models\Setting::get_logos();
                                                $default_curr = $settings['default_currency'];
                                                
                                                // $loop = 0;
                                                foreach ($currency as $currency) {
                                                ?>
                                                    <option value="<?php echo $currency->id ?>" <?php echo ($default_curr == $currency->id) ? "selected" : ""; ?>><?php echo $currency->short_code . " (" . $currency->currency_hex . ")" ?></option>
                                                <?php //$loop++;
                                                } ?>
												</select>
											</span>
                                            <input type="hidden" name="currency_id" value="{{$default_curr}}">
											<input type="number" class="w-full h-14  px-6 py-4 text-base text-black border-l-2 border-green-800 bg-gray-100 focus:outline-none  placeholder-black" value="" name="number-price-sst" id="number-price-sst" min="0" required="required">
										</div>
									</div>
									
									<div class="w-full sm:w-1/2 mb-4 sm:mb-0">
										<label class="block text-base text-black  mb-2 sm:mb-4 poppins-500">{{__('p_myads.city')}} <span class="text-red-800">*</span></label>
										<input type="text" class="w-full h-14  px-6 py-4 text-base text-black border-l-2 border-green-800 bg-gray-100 focus:outline-none  placeholder-black" value="" id="searchTextField" name="text-city-sst" required="required" wire:ignore>
										<input type="hidden" id="cName" name="city_name" size="50" />
										<input type="hidden" id="cityMain" name="main_city_name" size="50" />
										<input type="hidden" id="cityLat" name="city_lat" size="50" />
										<input type="hidden" id="cityLag" name="city_lag" size="50" />
										<input type="hidden" id="country_long" name="country_long" size="50" />
										<input type="hidden" id="country_short" name="country_short" size="50" />
										<input type="hidden" id="state_long" name="state_long" size="50" />
										<input type="hidden" id="state_short" name="state_short" size="50" />
										<input type="hidden" name="post-catid-sst" id="id_post_catid" />
									</div>
									
									
								</div>

                                 <!--row 3-->
								<div class="w-full block mb-4 sm:mb-6 md:mb-8 lg:mb-10">
									<label class="block text-base text-black  mb-2 sm:mb-4 poppins-500">{{__('p_myads.description')}} <span class="text-red-800">*</span></label>
									<textarea class="check_blacklist_detail w-full h-36  px-6 py-4 text-base text-black border-l-4 border-green-800 bg-gray-100 focus:outline-none  placeholder-black" value="" name="textarea-desc-sst" id="textarea-desc-sst" required="required"></textarea>
								</div>
								
                                 <!--row 4-->
								<!--<div class="sm:flex sm:space-x-2 lg:space-x-6 sm:mb-6 md:mb-8 lg:mb-10 w-full">
									
								</div>-->


								 <!--row 5-->
								<div class="w-full block mb-2 sm:mb-6 md:mb-8 lg:mb-10">
									<h2 class="text-black text-lg md:text-xl lg:text-2xl xl:text-3xl font-bold sm:mt-0 mt-2 poppins-600 text-green-500">{{__('p_myads.what is your expectation and details')}}?</h2>
                                    <div id="pc_html"></div>
								</div>
                                

                                <?php $post_methods = App\Models\TblPostMethod::get_active_post_methods(); ?>
								

								 <!--row 6-->
								<div class="w-full mb-4 sm:mb-8 md:mb-10 lg:b-12">
									<ul class="sm:inline-flex w-full">
                                    @foreach($post_methods as $post_method)
                                    @if($post_method->name == "exchange")
										<li class="float-left w-full sm:w-auto mb-4 sm:mb-0">
											<!-- Toggle Exchange to Buy -->
											<label for="Products_exchangeToBuy" class="w-full sm:w-auto float-left cursor-pointer block text-base text-black font-semibold {{$class_dir_sm_mar_rl}}">{{__('post_detail.exchange to buy')}}
												<!-- toggle -->
												<div class="relative mt-0 sm:mt-3 {{$class_dir_float_rl}} sm:float-none">
												<!-- input -->
												<input type="checkbox" id="Products_exchangeToBuy" class="sr-only" name="exchangeToBuy" value="1">
												<!-- line -->
												<div class="block bg-gray-200 w-16 lg:w-20 h-9 lg:h-11 rounded-full"></div>
												<!-- dot -->
												<div class="dot absolute left-1 top-1 bg-gray-400 w-7 lg:w-9 h-7 lg:h-9 rounded-full transition"></div>
												</div>
												<!-- label -->
											</label>
										</li>
                                        @endif
                                        @if($post_method->name == "buynow")
										<li class="float-left w-full sm:w-auto mb-4 sm:mb-0">
											<label for="Products_InstantBuy" class="w-full sm:w-auto float-left cursor-pointer block text-base text-black font-semibold {{$class_dir_sm_mar_rl}}">{{__('p_myads.instant buy')}}
											<!-- toggle -->
												<div class="relative mt-0 sm:mt-3 {{$class_dir_float_rl}} sm:float-none">
												<!-- input -->
												<input type="checkbox" id="Products_InstantBuy" class="sr-only" name="InstantBuy" value="1">
												<!-- line -->
												<div class="block bg-gray-200 w-16 lg:w-20 h-9 lg:h-11 rounded-full"></div>
												<!-- dot -->
												<div class="dot absolute left-1 top-1 bg-gray-400 w-7 lg:w-9 h-7 lg:h-9 rounded-full transition"></div>
												</div>
												<!-- label -->
											</label>
										</li>

                                        @endif
                                        @endforeach
										<li class="float-left w-full sm:w-auto">
											<label for="Products_FixedPrice" class="w-full sm:w-auto float-left cursor-pointer block text-base text-black font-semibold {{$class_dir_sm_mar_rl}}">{{__('p_myads.fixed price')}}
                                                <!-- toggle -->
												<div class="relative mt-0 sm:mt-3 {{$class_dir_float_rl}} sm:float-none">
												<!-- input -->
												<input type="checkbox" id="Products_FixedPrice" class="sr-only" name="FixedPrice" value="1">
												<!-- line -->
												<div class="block bg-gray-200 w-16 lg:w-20 h-9 lg:h-11 rounded-full"></div>
												<!-- dot -->
												<div class="dot absolute left-1 top-1 bg-gray-400 w-7 lg:w-9 h-7 lg:h-9 rounded-full transition"></div>
												</div>
												<!-- label -->
											</label>
										</li>
									</ul>
								</div>

                                 <!--row 7 -- hidden -->
                                <div class="w-full block mb-2 sm:mb-6 md:mb-8 lg:mb-10 shipping_fee">
									<label class="text-black text-lg md:text-xl lg:text-2xl xl:text-3xl font-bold sm:mt-0 mt-2">{{__('p_myads.quick buying information')}}</label>
									<label class="block text-base text-black  mb-2 sm:mb-4 poppins-500">{{__('p_myads.shipping cost')}}</label>
									<input type="text" placeholder="Shipping Cost" class="w-full h-14 rounded-lg px-6 py-4 text-base text-black border-l-2 border-green-800 bg-gray-100 focus:outline-none placeholder-black pac-target-input" name="text-shipping-fee" id="text-shipping-fee">
                                </div>

                                 <!--row 7.1-->
                                <div class="flex flex-wrap mt-3 w-full" id="fb-render"></div>
                                 <!--row 8-->
								
								<div class="w-full block mb-2 sm:mb-6 md:mb-8 lg:mb-10">
									<h2 class="text-black text-lg md:text-xl lg:text-2xl xl:text-3xl font-bold sm:mt-0 mt-2 poppins-600 text-green-500">{{__('p_myads.youtube video embed url')}}</h2>
								</div>
								
                                 <!--row 9-->
								<div class="w-full mb-4 sm:mb-8 md:mb-10 lg:mb-12">
									<label class="block text-base text-black  mb-2 sm:mb-4 poppins-500">{{__('p_myads.product video')}}</label>
									<input type="text" placeholder="Example: https://www.youtube.com/embed/9xwazD5SyVg" class="w-full h-14 rounded-lg px-6 py-4 text-base text-black border-l-2 border-green-800 bg-gray-100 focus:outline-none  placeholder-gray-400" value="" name="text-video-sst" id="text-video-sst">
								</div>

                   

                                <!--row 11-->  
								<div class="w-full flex flex-wrap mb-4 sm:mb-8 md:mb-10">
									

									<div class="w-full lg:w-3/5 mb-4 lg:mb-0 {{$class_dir_float_lr}}">
										<div class="relative w-full float-left {{$class_dir_padding_rl}} mt-2 mb-4 sm:m-0">
											<div class="w-full float-left">
												<div class="w-full rounded-lg bg-gray-100 float-left">
													<div class="w-full p-5 float-left">
														<div class="w-full float-left py-2 post_add_img_upload" wire:ignore="">
																<div class="input-images border-4 border-solid rounded-3xl hover:bg-gray-100 hover:border-gray-300 cursor-pointer relative h-52 sm:h-64 md:h-72 lg:h-80 pt-10 pb-6">
																	<div class="absolute flex flex-col items-center top-0 bottom-0 left-0 right-0 justify-center text-center">
																		<div class="text-4xl font-normal text-gray-200 group-hover:text-gray-600">
																			<i class="fa fa-plus" aria-hidden="true"></i>
																		</div>
																		<p class="text-xl text-gray-500 font-semibold group-hover:text-gray-600">Upload Photos</p>
																	</div>
																</div>
																@include('livewire.common.multiple_image')
								
																<?php
																	$img_limit = App\Models\Setting::get_image_size_settings();
																?>
																<input type="hidden" class="upload_page" value="Add" />
																<input type="hidden" class="max_img_upload_count" value="<?php echo !empty($img_limit['max_image_limit']) ? $img_limit['max_image_limit'] : 5; ?>" />
														</div>
														
													</div>
												</div>
											</div>
										</div>
									</div>

									
									
									<div class="w-full lg:w-2/5 {{$class_dir_float_lr}}">
										<label class="block text-base sm:text-md md:text-lg lg:text-xl text-black font-semibold mb-2 sm:mb-4">{{__('p_myads.package')}}</label>
										<div class="mb-4 md:mb-8">
											<p class="text-base  font-semibold leading-relaxed leading-relaxed">{{__('p_myads.package_description')}}</p>
										</div>
												
											<?php
										$packagesList = App\Models\Package::get_active_packages();
										if (!empty($packagesList) && !empty($payment_methods)) { ?>
										<div class="w-full px-4 md:px-5 lg:px-7 py-4 bg-gray-50 float-left">

											<?php
												$pk_i = 0;
												foreach ($packagesList as $packagesList) {
												if ($pk_i == 0) {
													$checked = "checked";
												} else {
													$checked = "";
												}
											?>
											
											<label for="free-ads_{{$pk_i}}" class="w-full cursor-pointer block text-lg md:text-xl lg:text-2xl text-black font-semibold float-left mb-2 sm:mb-4 md:mb-7">{{$packagesList->name}}

											<?php if ($packagesList->lft == 1) { ?>
											<small class="has-tooltip bg-yellow-500 p-1 text-xs rounded-md inline-block align-middle text-gray-800 "> Free <small class="tooltip"><?php echo "Duration : " . $packagesList->duration . " days, Limit : " . $packagesList->single_pack_limit . " Ad" ?></small></small>
											<?php } else { ?>
												<small class="text-white has-tooltip bg-green-500 p-1 text-xs rounded-md inline-block align-middle"> Upgrade <small class="tooltip"><?php echo "Duration : " . $packagesList->duration . " days, Limit : 1 Ad" ?></small></small>
												<?php } ?>
												
											<div class="relative {{$class_dir_float_rl}}">
												<input type="radio" name="package_type" id="free-ads_{{$pk_i}}" class="sr-only package_type" data-amount="{{$packagesList->price}}" data-payement="{{$packagesList->lft}}" value="<?php echo $packagesList->id ?>" <?php echo $checked; ?>>
												<div class="block bg-gray-200 w-16 lg:w-20 h-9 lg:h-11 rounded-full"></div>
												<div class="dot absolute left-1 top-1 bg-gray-400 w-7 lg:w-9 h-7 lg:h-9 rounded-full transition"></div>
												</div>

												<span class="block text-base font-normal">
												<!--<select class="bg-green-500 bg-opacity-0 appearance-none" name="currency_id" disabled="">
												<?//php
												//$settings = App\Models\Setting::get_logos();
												//$currency = App\Models\TblCurrency::where('active', '0')->orderBy('id', 'desc')->get();
												//foreach ($currency as $currency) {
												?>
													<option value="<?//php echo $currency->id ?>" <?//php echo ($currency->id == $settings['default_currency']) ? "selected" : ""; ?>><?//php echo $currency->currency_hex; ?></option>
												<?//php } ?>
												</select> -->
                                            <?php $pack_curreny = App\Models\Setting::get_admin_default_currency(); ?>
												<?php echo $pack_curreny['currency_hex'];?> {{$packagesList->price}}</span>
											</label>
											<?php $pk_i++; } ?>
											
										</div>
										<?php } ?>
									</div>
								</div>

                            <!--row 12-->
                            <div class="w-full mb-4 sm:mb-8 md:mb-10 lg:mb-12 payment_methods">
                                <label class="block text-base text-black  mb-2 sm:mb-4 poppins-500">{{__('p_myads.choose payment method')}} <span class="text-red-800">*</span></label>
                                <ul class="bg-green-50 p-4">
                                    <?php
                                    $i = 0;
                                    foreach ($payment_methods as $p) {
                                        $checkString = ($i == 0) ? "checked=checked" : "";
                                        $i++;
                                    ?>
                                        <li class="border-b-2 border-gray-200 p-3 pl-0">
                                            <label class="inline-flex items-center mb-2">
                                                <input type="radio" class="payment_type form-radio h-6 w-6 text-green-500" name="payment_type" value="{{$p['name']}}" {{$checkString}}>
                                                <span class="mx-2">{{$p['display_name']}}</span>
                                            </label>
                                        </li>
                                    <?php } ?>
                                </ul>
                                <input type="hidden" id="final_total_amount" />
                                <input type="hidden" name="paid_id" id="paid_id" />
                            </div>

                            <div class="relative flex flex-col min-w-0 break-words w-full mt-0">
                                <div class="rounded-t mb-0 px-0 py-3 border-0">
                                    <div class="block w-full items-center">
                                        <div class="relative max-w-full flex-grow flex-1">
                                            <div class="font-semibold text-base text-gray-800">
                                                <div class="text-left" wire:ignore>
                                                    <?php
                                                     $recaptcha_sitekey = getenv("GOOGLE_RECAPTCHA_SITEKEY");
                                                     $recaptcha_secret = getenv("GOOGLE_RECAPTCHA_SECRETKEY");
                                                   
                                                    ?>
                                                    <div class="g-recaptcha w-full"  data-tabindex=0 data-sitekey="{{$recaptcha_sitekey}}"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-full block float-left text-center mt-4 mb-2 form_save_btn">
                                            <button id="post_json_insert" class="bg-green-500 py-3 px-14 pb-3 inline-block rounded-md text-white text-base  outline-none focus:outline-none hover:text-green-500  poppins-500 hover:bg-white transition ease-in-out duration-700 sm:w-auto w-full mb-4">{{__('p_myads.post')}}</button>
                                            <button id="post_cancel" class="bg-orange py-3 px-14 pb-3 inline-block rounded-md text-white text-base  outline-none focus:outline-none hover:text-green-500  poppins-500 hover:bg-white transition ease-in-out duration-700 sm:w-auto w-full mb-4"><a href="{{URL::to('/post')}}">{{__('p_myads.cancel')}}</a></button>
                                        </div>
                                    </div>
                                </div>
                            </div>


                                    <!--end block-->
							    
                        </div>
					</div>
				</form>

                <div class="w-full inline-block text-center my-2 paymet_save_btn">
					<div class="relative max-w-full flex-grow flex-1 mt-4 text-center">
						<button id="pay_proceed" class="bg-green-500 py-2 px-14 pb-3 inline-block rounded-md text-white text-base font-semibold outline-none focus:outline-none hover:text-green-500 border-2 border-green-500 hover:bg-white transition ease-in-out duration-700 w-full sm:w-auto sm:mb-0 mb-4">{{__('p_myads.proceed to pay')}}</button>
						<button class="bg-green-500 py-2 px-14 pb-3 inline-block rounded-md text-white text-base font-semibold outline-none focus:outline-none hover:text-green-500 border-2 border-green-500 hover:bg-white transition ease-in-out duration-700 w-full sm:w-auto"><a href="{{URL::to('/post')}}">{{__('p_myads.cancel')}}</a></button>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- Post Ad Details Content -->	
</div>



<?php $blacklist_words = App\Models\TblPost::get_blacklist(); ?>
{!! config('app.google_map_script') !!}
<script src="https://www.google.com/recaptcha/api.js" async defer="defer"></script>
<script>
    $(".shipping_fee").hide();
    $(".payment_methods").hide();
    $(".paymet_save_btn").hide();

    function validateForm() {
        var city_name = document.forms["post-add"]["city_name"].value;
        var country_name = document.forms["post-add"]["country_long"].value;
        var state_name = document.forms["post-add"]["state_long"].value;
        var video = document.forms["post-add"]["text-video-sst"].value;
        var adTitle = document.forms["post-add"]["text-title-sst"].value;
        var setPrice = document.forms["post-add"]["number-price-sst"].value;
        var descr = document.forms["post-add"]["textarea-desc-sst"].value;
        var cat = $('#selected_category option').filter(':selected').val();

        if(cat.length == 0)
        {
            toastr.warning("Select Category!");
            $("#selected_category").focus();
            return false; 
        }
        else if(adTitle=="")
        {
            toastr.warning("Enter title!");
            $(".check_blacklist_title")[0].focus();
            return false;
        }
        else if(setPrice=="")
        {
            toastr.warning("Enter price!");
            $("#number-price-sst").focus();
            return false;
        }

        else if (city_name == null || city_name == "") {
            $("#searchTextField").focus();
            $("#searchTextField").val("");
            $("#searchTextField").attr("required", true);
            return false;
        } else if (city_name == country_name) {
            $("#searchTextField").focus();
            $("#searchTextField").val("");
            toastr.warning("Please choose the locality address only!");
            $("#searchTextField").attr("required", true);
            return false;
        } else if (city_name == state_name) {
            $("#searchTextField").focus();
            $("#searchTextField").val("");
            toastr.warning("Please choose the locality address only!");
            $("#searchTextField").attr("required", true);
            return false;
        } else if (country_name == "" || country_name == null) {
            $("#searchTextField").focus();
            $("#searchTextField").val("");
            toastr.warning("Invalid address, please choose valid address!");
            $("#searchTextField").attr("required", true);
            return false;
        } else if (state_name == "" || state_name == null) {
            $("#searchTextField").focus();
            $("#searchTextField").val("");
            toastr.warning("Invalid address, please choose valid address!");
            $("#searchTextField").attr("required", true);
            return false;
        } else if (video != "") {
            if (video != undefined || video != '') {
                var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
                var match = video.match(regExp);
                if (match && match[2].length == 11) {} else {
                    toastr.warning("Invalid youtube url!");
                    return false;
                }
            }
        }

        else if(descr=="")
        {
            toastr.warning("Enter description!");
            $("#textarea-desc-sst").focus();
            return false;
        }
        else{
            return true;
        }
	
		
    }

    $(document).ready(function() {
        $("#Products_InstantBuy").on('change', function(e) {
            if (this.checked) {
                $(".shipping_fee").show();
                $("#text-shipping-fee").attr("required", true);
            } else {
                $(".shipping_fee").hide();
                $("#text-shipping-fee").attr("required", false);
            }
        });
        $(".package_type").on('change', function(e) {
            var free = $(this).attr('data-payement');
            var value = $(this).attr('data-amount');
            if (free == 1) {
                $(".payment_methods").hide();
                $(".form_save_btn").show();
                $('.paymet_save_btn').hide();
            } else {
                $(".payment_methods").show();
                $("#final_total_amount").val(value);
                // $(".form_save_btn").hide();  //already comment
                // $(".paymet_save_btn").show(); //already comment
                // $(".form_save_btn").show();
                
                var typeval = $("input[name='payment_type']:checked").val();
                if(typeval == "stripe")
                {
                    $(".payment_methods").show();
                    $(".form_save_btn").hide();
                    $(".paymet_save_btn").show();
                }else{
                    $(".form_save_btn").show();
                }

                
            }
        });

        $(".payment_type").on('change', function(e) {
            var ptype = $(this).val();
            if(ptype == "stripe")
            {
                $(".payment_methods").show();
                $(".form_save_btn").hide();
                $(".paymet_save_btn").show();
            }else{
                $(".form_save_btn").show();
                $(".paymet_save_btn").hide();
            }
           
        });

    });
    //*Allow decimals only
    $("#text-shipping-fee").on("input", function(evt) {
        var self = $(this);
        self.val(self.val().replace(/[^0-9\.]/g, ''));
        if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
            evt.preventDefault();
        }
    });
    //*

    //prevent enter submit
    window.addEventListener('load', event => {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
    //Select - On change filling process - begin
    window.addEventListener('contentChanged', event => {
        $("#fb-render").html('');
        $("#fb-render").html(event.detail.custJson);
        document.getElementById('id_post_catid').value = event.detail.catId;
        console.log($('#id_post_catid').val());
        $("#pc_html").html('');
        $("#pc_html").html(event.detail.product_condition_html);
        //document.getElementById("post_json_insert").disabled = false;
    });
    //Select - On change filling process - end
    //Save process start  
    jQuery(function($) {
        var options = {
            types: ['(regions)']
        };
        //  componentRestrictions: {country: "us"} //restric country if need
        //for location search map
        var input = document.getElementById('searchTextField');
        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addDomListener(input, 'keydown', function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
            }
        });
        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            $('#cName').val('');
            $('#cityMain').val('');
            $('#cityLat').val('');
            $('#cityLag').val('');
            $('#country_long').val('');
            $('#country_short').val('');
            $('#state_long').val('');
            $('#state_short').val('');
            var place = autocomplete.getPlace();
            const country = place.address_components.find(item => item.types.includes('country'));
            const state = place.address_components.find(item => item.types.includes('administrative_area_level_1'));
                  // Find city based on administrative_area_level_1
                  const cityComponent = place.address_components.find(item => item.types.includes('locality') || item.types.includes('administrative_area_level_2') || item.types.includes('administrative_area_level_1'));
                
                const city = cityComponent ? cityComponent : '';
			console.log(city);
            $('#cName').val(place.name);
            $('#cityMain').val(city.long_name);
            $('#cityLat').val(place.geometry.location.lat());
            $('#cityLag').val(place.geometry.location.lng());
            $('#country_long').val(country.long_name);
            $('#country_short').val(country.short_name);
            $('#state_long').val(state.long_name);
            $('#state_short').val(state.short_name);
        });
        //for location search map
        //getting
        var getUserDataBtn = document.getElementById("post_json_insert");
        getUserDataBtn.addEventListener("click", () => {
			
            if ($("#text-title-sst").val() == "") {
                $("#text-title-sst").focus();
                return false;
            }
            if ($("#number-price-sst").val() == "") {
                $("#number-price-sst").focus();
                return false;
            }
            if ($("#textarea-desc-sst").val() == "") {
                $("#textarea-desc-sst").focus();
                return false;
            }
            if ($("#product-condition-sst").val() == "") {
                $("#product-condition-sst").focus();
                return false;
            }
			
		
	   }, false);
		
		
    });
    window.onload = function() {
        var $recaptcha = document.querySelector('#g-recaptcha-response');
        if ($recaptcha) {
            $recaptcha.setAttribute("required", "required");
        }
    };
    // remove blacklist words
    $(document).ready(function() {
        $(".check_blacklist_title").on('keyup', function(e) {
            var blacklist = <?php echo $blacklist_words; ?>;
            var words = $(".check_blacklist_title").val();
            var str = words.trim().split(" ");
            var lastWord = str[str.length - 1];
            var lowerword = lastWord.toLowerCase();
            var array_index = jQuery.inArray(lowerword, blacklist);
            if (array_index >= 0) {
                $(".check_blacklist_title").val($(".check_blacklist_title").val().replace(lastWord, ''));
            }
        });
    });
    // remove blacklist words
    $(document).ready(function() {
        $(".check_blacklist_detail").on('keyup', function(e) {
            var blacklist = <?php echo $blacklist_words; ?>;
            var words = $(".check_blacklist_detail").val();
            var str = words.trim().split(" ");
            var lastWord = str[str.length - 1];
            var lowerword = lastWord.toLowerCase();
            var array_index = jQuery.inArray(lowerword, blacklist);
            if (array_index >= 0) {
                $(".check_blacklist_detail").val($(".check_blacklist_detail").val().replace(lastWord, ''));
            }
        });
    });

    /* brands and models */
    $("body").delegate(".brands-select", "change", function() {
        var brands = $(this).val();
        if ((brands != "") && (brands != "undefined") && (brands != null)) {
            get_custom_models(brands);
        }
        function get_custom_models(brands) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "{{ route('get_custom_models') }}",
                data: {
                    id: brands,
                },
                success: function(data) {
                    $(".models-select").html(data.data);
                }
            });
        }
    });
</script>
<?php
if (!empty($payment_methods)) {
    foreach ($payment_methods as $p) {
        $base_path = base_path() . '/extra/plugins/' . $p['name'] . '/src/add_post.php';
        if (is_file($base_path)) {
            include_once($base_path);
        }
    }
}
?>
