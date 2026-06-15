<div class="root-element-div">

<?php
/* show the curreny symbol */
$settings = App\Models\Setting::get_logos();
$slected_currency = !empty($product[0]->currency_id) ? $product[0]->currency_id : $settings['default_currency'];
$currency_symbol = App\Models\TblPost::get_post_currency($slected_currency);

$final_city_name = !empty($product[0]->locality) ? $product[0]->locality : $info_location[0]->name; // get locality & city 
?>

<!-- report start -->
<div class="fixed z-10 inset-0 overflow-y-auto" id="report" style="display:none">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>
                    <!-- This element is to trick the browser into centering the modal contents. -->
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                                        {{__('post_detail.item report')}}
                                    </h3>
                                    <div class="mt-2">
                                        <div class="py-2">
                                            @foreach($this->report_types as $report)
                                            <label class="text-sm"><input type="radio" value="{{$report->id}}" name="re_type" class="re_type ml-1 mr-1" required>{{$report->name}}</label><br>
                                            @endforeach
                                        </div>
                                        <div class="py-2">
                                            <label class="block text-gray-700 text-sm font-bold mb-2">{{__('post_detail.comment')}}:</label>
                                            <textarea id="comment" maxlength="500" placeholder="{{__('post_detail.type here')}}" rows="3" required class="text-sm shadow appearance-none border rounded w-full py-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                                            <p class="text-xs font-bold item-center text-gray-800">{{__('post_detail.character limit')}}: 500</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm" id="submit">
                                {{__('post_detail.send complaint')}}
                            </button>
                            <button type="button" id="cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                {{__('post_detail.cancel')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- report end -->



<!--Begin -- block 1-->
<div class="relative w-full float-left mt-6 sm:mt-12 md:mt-16 lg:mt-20 xl:mt-24">
			<div class="container mx-auto px-4">
				<div class="relative w-full float-left">
					<div class="w-full sm:w-7/12 float-left mb-5 sm:mb-8">
                    @if($product[0]->images=="")
                        <p class="text-center p-10 font-semibold">{{__('post_detail.no images to preview')}}</p>
                        @else
						<div class="w-full float-left">
							<div class="px-3 sm:px-7 bg-gray-100">
								<!-- <div class="flex w-full"> -->
									<!-- <div class="items-center"> -->
										<div class="product_image">
                                        <?php
                                    $images = explode(',', $product[0]->images);
                                    foreach ($images as $r) {
                                        $exp_imgURLs = explode('/', $r);
                                        $imgName = end($exp_imgURLs);
                                        $is_file = base_path() . '/storage/app/public/adpost/predefined/list/' . $imgName;
                                        if (is_file($is_file)) {
                                            $imgUrl = URL::to('storage/adpost/predefined/list/' . $imgName);
                                        } else {
                                            $imgUrl = URL::to('storage/adpost/predefined/' . $imgName);
                                        }
                                        $imgUrlfinal = $imgUrl;
                                    ?>
											<div class="prod_img">
												<img class="object-center object-cover mx-auto" style="height: 515px" src="{{ $imgUrlfinal }}" alt="{{ $product[0]->title }}">
											</div>
                                            <?php } ?>
										</div>
									<!-- </div> -->
								<!-- </div> -->
							</div>
						</div>
						
						<div class="w-full float-left mt-5 relative px-4">
							<div class="w-full thumbnail_slider">
								
                            <?php
                                    $images = explode(',', $product[0]->images);
                                    foreach ($images as $r) {
                                        $exp_imgURLs = explode('/', $r);
                                        $imgName = end($exp_imgURLs);
                                        $is_file = base_path() . '/storage/app/public/adpost/predefined/list/' . $imgName;
                                        if (is_file($is_file)) {
                                            $imgUrl = URL::to('storage/adpost/predefined/list/' . $imgName);
                                        } else {
                                            $imgUrl = URL::to('storage/adpost/predefined/' . $imgName);
                                        }
                                        $imgUrlfinal = $imgUrl;
                                    ?>
                            
                            <div class="thumbnail_img mx-2">
									<img class="object-cover object-center mx-auto" src="{{ $imgUrlfinal }}" alt="{{ $product[0]->title }}">
								</div>
                                <?php } ?>
                                
							</div>
							<div class="w-full slick_arrow_button cursor-pointer text-right absolute right-0 left-0 top-1/3">
								<span class="inline-block leading-9 text-left text-xl md:text-2xl lg:text-3xl xl:text-4xl font-bold text-black product_left_arrow slick-arrow absolute left-2 md:left-0"><i class="fa fa-angle-left" aria-hidden="true"></i></span>
								<span class="inline-block leading-9 text-right text-xl md:text-2xl lg:text-3xl xl:text-4xl font-bold text-black product_right_arrow slick-arrow absolute right-2 md:right-0"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
							</div>
						</div>
						@endif
					</div>
					<div class="w-full sm:w-5/12 float-left mb-5 sm:mb-8">
                    <?php
                $currentUserId = !empty(auth()->user()->id) ? auth()->user()->id : "";
                $adPostedUserId = $info_user[0]->id;

                ?>

						<div class="mb-0 px-0 md:px-3 xl:px-5 float-left w-full">
							<div class="mb-0 xl:mb-5 px-2 py-2">
                            <?php
                            $product_condition = App\Models\TblPost::get_product_condition($product[0]->id);
                            ?>
							   <h2 class="text-3xl xl:text-4xl capitalize font-normal px-2 pb-2 text-gray-700">{{ $product[0]->title }}

                               @if(!empty($product_condition))
								  <span class="bg-gray-500 text-white font-semibold rounded text-xs p-1">{{$product_condition}}</span>
                                  @endif
							   </h2>
							   <h2 class="text-3xl xl:text-4xl capitalize font-bold px-2 py-2">
								  <span class="font-bold text-3x1"> <?php echo $currency_symbol[0]; ?>{{ $product[0]->price}} </span>
							   </h2>
							</div>

                            @if(auth()->user() && $check_is_paid->count()==0)
                    <?php
                    $urlnew = URL::to('/selectPackage?post=' . $product[0]->id . '');
                    if ($adPostedUserId == $currentUserId) {
                    ?>


                        <button class="w-full mt-5 pt-2 xl:pt-3 pb-3 xl:pb-4 px-1 md:px-2 text-sm sm:text-xs lg:text-base xl:text-lg font-semibold leading-normal bg-gray-300 border rounded cursor-pointer outline-none focus:outline-none rounded-lg transition ease-in-out duration-700 hover:bg-gray-800 hover:text-white" id="chat-with-seller"><a href="{{$urlnew}}" >
                                {{__('post_detail.sell fast')}}
                            </a></button>

                    <?php } ?>
                    @endif

                            <a href="<?php echo URL::to('seller-profile/' . $info_user[0]->id); ?>" class="w-full inline-block">
							<div class="flex flex-wrap items-center my-2 mb-3 xl:mb-5">
								<div class="w-2/12 px-4 text-center text-green-600 text-3xl xl:text-5xl">
									<i class="fa fa-user"></i>
								</div>
								<div class="w-9/12 px-4">
									<h4 class="text-xl xl:text-2xl capitalize font-bold text-gray-700">{{ $info_user[0]->name }}</h4>
									<p class="text-lg xl:text-xl text-gray-400">Posted By</p>
								</div>
								<div class="w-1/12 text-2xl xl:text-3xl xl:text-5xl text-gray-600">
									<i class="fa fa-angle-right" aria-hidden="true"></i>
								</div>
							</div>
							</a>
							<!-- <div class="border-t border-solid border-gray-200"></div> -->
							<table class="w-full my-2 py-2">
								<tbody>
									<tr>
										<td class="p-2 text-center text-lg xl:text-xl text-green-600"><i class="fa fa-map-marker fa-lg"></i></td>
										<td class="p-2 font-semibold text-base xl:text-xl text-gray-600">{{__('post_detail.location')}}</td>
										<td class="p-2 font-semibold text-base xl:text-xl text-gray-600">{{ $final_city_name}}</td>
									</tr>
									<tr>
                                    <?php $joinedon = date('d M Y', strtotime($info_user[0]->created_at)); ?>
										<td class="p-2 text-center text-xl text-green-600"><i class="fa fa-user fa-lg"></i></td>
										<td class="p-2 font-semibold text-base xl:text-xl text-gray-600">{{__('post_detail.joined')}}</td>
										<td class="p-2 font-semibold text-base xl:text-xl text-gray-600">{{ $joinedon }}</td>
									</tr>
                                    
									<tr>
										<td class="p-2 text-center text-base xl:text-xl text-green-600"><i class="fa fa-envelope fa-lg"></i></td>
										<td class="p-2 font-semibold text-base xl:text-xl text-gray-600">Email</td>
										<td class="p-2 font-semibold text-base xl:text-xl text-gray-600">{{ $info_user[0]->email }}</td>
									</tr>
                                    @if($info_user[0]->show_mobile == 1 )
									<tr>
										<td class="p-2 text-center text-base xl:text-xl text-green-600"><i class="fa fa-phone fa-lg"></i></td>
										<td class="p-2 font-semibold text-base xl:text-xl text-gray-600">Phone</td>
										<td class="p-2 font-semibold text-base xl:text-xl text-gray-600">{{ $info_user[0]->phone }}</td>
									</tr>
                                    @endif
								</tbody>
						   </table>
							
							<div class="flex space-x-2 lg:space-x-6">
                            <?php
                            $insight_id = URL::to('/insights/' . $product[0]->id . '');
                            $post_methods = App\Models\TblPostMethod::get_active_post_methods();
                            ?>
                            <?php if ($currentUserId != $adPostedUserId) { ?>
                                @if(!empty($post_methods))
                                <?php $check_banner_ads = $post_methods->pluck('name')->toArray(); ?>
                                @if(in_array("exchange", $check_banner_ads))
                                @if($product[0]->exchange_to_buy == 1)
								<div class="w-3/6">
								   <button class="w-full mt-5 pt-2 xl:pt-3 pb-3 xl:pb-4 px-1 md:px-2 text-sm sm:text-xs lg:text-base xl:text-lg font-semibold leading-normal cursor-pointer outline-none focus:outline-none rounded-lg border-2 border-gray-600 bg-transparent hover:bg-green-500 hover:border-green-500 hover:text-white transition ease-in-out duration-700" id="user_exchange_buy">{{__('post_detail.exchange to buy')}}</button>
								</div>
                                @endif
                                @endif
                                @if(in_array("buynow", $check_banner_ads))
                                @if($product[0]->instant_buy == 1)
								<div class="w-3/6">
                                    @if(auth()->user())
                                    <?php $checkout_url = URL::to('/revieworder/' . $product[0]->slug); ?>
								   <button class="w-full mt-5 pt-2 xl:pt-3 pb-3 xl:pb-4 px-1 md:px-2 text-sm sm:text-xs lg:text-base xl:text-lg font-semibold leading-normal cursor-pointer outline-none focus:outline-none rounded-lg border-2 border-gray-600 bg-transparent hover:bg-green-500 hover:border-green-500 hover:text-white transition ease-in-out duration-700">
                                   <a href="{{$checkout_url}}">{{__('post_detail.buy now')}}</a>
                                   </button>
                                   @else
								   <button class="w-full mt-5 pt-2 xl:pt-3 pb-3 xl:pb-4 px-1 md:px-2 text-sm sm:text-xs lg:text-base xl:text-lg font-semibold leading-normal cursor-pointer outline-none focus:outline-none rounded-lg border-2 border-gray-600 bg-transparent hover:bg-green-500 hover:border-green-500 hover:text-white transition ease-in-out duration-700" id="user_exchange_buy">{{__('post_detail.buy now')}}</button>
                                   @endif
								</div>
                                @endif
                                @endif
                                @endif
                                <?php } ?>

                                
							</div>	
								
							<div class="flex space-x-2 lg:space-x-6">
                            @if($product[0]->fixed_price == 0)
								<div class="w-3/6 float-left">
								   <button class="w-full mt-5 pt-2 xl:pt-3 pb-3 xl:pb-4 px-1 md:px-2 text-sm sm:text-xs lg:text-base xl:text-lg font-semibold leading-normal cursor-pointer outline-none focus:outline-none rounded-lg border-2 border-gray-600 bg-transparent hover:bg-green-500 hover:border-green-500 hover:text-white transition ease-in-out duration-700" id="user_make_offer">{{__('post_detail.make an offer')}}</button>
								</div>
                                @endif

                                <?php if (auth()->user()) {
                                if (auth()->user()->id != $product[0]->user_id) {?>
								<div class="w-3/6">
								   <button class="w-full mt-5 pt-2 xl:pt-3 pb-3 xl:pb-4 px-1 md:px-2 text-sm sm:text-xs lg:text-base xl:text-lg font-semibold leading-normal cursor-pointer outline-none focus:outline-none rounded-lg border-2 border-gray-600 bg-transparent hover:bg-green-500 hover:border-green-500 hover:text-white transition ease-in-out duration-700" id="report_ad">{{__('post_detail.report this ad')}}</button>
								</div>
                                <?php }}?>
							</div>
							<?php if ($currentUserId != $adPostedUserId) { ?>
							<div class="w-full float-left">
                            <?php
                            $chaturl = URL::to('/chat') . '?to=' . $adPostedUserId . '&p=' . $product[0]->id . '&type=';
                            ?>
							   <button class="w-full mt-5 pt-2 xl:pt-3 pb-3 xl:pb-4 px-1 md:px-2 text-sm sm:text-xs lg:text-base xl:text-lg font-semibold leading-normal bg-gray-300 border rounded cursor-pointer outline-none focus:outline-none rounded-lg transition ease-in-out duration-700 hover:bg-gray-800 hover:text-white" id="chat-with-seller"><a href="<?php echo $chaturl; ?>" >
                                {{__('post_detail.chat with seller')}}
                            </a></button>
							</div>
                            <?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
<!--End -- block 1-->

<!--Begin -- block 2-->
<div class="relative w-full float-left">
			<div class="container mx-auto px-4">
			
				<div class="w-full sm:w-5/12 float-right mb-8">
					<div class="px-0 md:px-3 xl:px-5">
						<div class="mb-8">
                            <div id="map" class="h-72"></div>
                            <script>
                                function initMap() {
                                    // The location of Uluru
                                    var val_lat = '{{ $info_location[0]->latitude }}';
                                    var val_lag = '{{ $info_location[0]->logitude }}';
                                    var mapOptions = {
                                        center: new google.maps.LatLng(parseFloat(val_lat), parseFloat(val_lag)),
                                        zoom: 15
                                    }
                                    var map = new google.maps.Map(document.getElementById("map"), mapOptions);
                                    var marker = new google.maps.Marker({
                                        position: new google.maps.LatLng(parseFloat(val_lat), parseFloat(val_lag)),
                                        map: map,
                                        animation: google.maps.Animation.Drop
                                    });
                                }
                            </script>
                            <!--Map end-->

						</div>
					
					<!-- Recently Viewed products Desktop - slider start -->

                    <div class="w-full float-left sm:block hidden">

        <div class="w-full pb-0 float-left sm:block hidden">
						 <div class="relative">
            <p class="text-2xl font-bold text-gray-700 pb-4">{{__('post_detail.recently viewed ads')}}</p>
            <div class="recently_viewed">
            <?php $i = 0; ?>
            @foreach($recently_viewed_products as $d)
            <?php
            $i++;
            $imgUrlfinal = App\Models\TblChat::getPostImgForList($d['id']);
            $get_categoryname = App\Models\TblCategory::getCategoryName($d['category_id']);
            $slug = App\Models\TblPost::get_post_slug($d["slug"]);
            $published_on = date("d M Y", strtotime($d["created_at"]));
            $fav_style = App\Models\TblSavedPosts::check_fav($d['id']);
            $hearty = ($fav_style==true)?'fa-heart':'fa-heart-o';
            $getPackType = App\Models\TblPayment::where('post_id', $d['id'])->get();
            $ad_type = "";
            if ($getPackType->count() > 0) {
                $packageID = $getPackType[0]->package_id;
                $ad_type = App\Models\Package::where('id', $packageID)->pluck('ad_type')->toArray();
            }
            /* show the curreny symbol */
            $settings = App\Models\Setting::get_logos();
            $slected_currency = !empty($d['currency_id']) ? $d['currency_id'] : $settings['default_currency'];
            $currency_symbol = App\Models\TblPost::get_post_currency($slected_currency);
            ?>
                <div class="pb-9 sm:w-2/4 lg:w-1/3 xl:w-1/4 items-center">
                    <div class="border border-gray-200 shadow m-2 relative">
                        <div class="items-center">
                            <div class="mt-1">
                            <?php if ($ad_type != "") { ?>
                                    <h3 class="text-xs text-white font-semibold relative">
                                        <span class="bg-yellow-500 px-2 py-1 rounded-tr-lg rounded-br-lg uppercase">
                                        <?php echo str_replace('_', ' ', strtoupper($ad_type[0])); ?>
                                        </span>
                                    </h3>
                                <?php } ?>
                            </div>
                            <div class="absolute right-0 top-0">
                                <button type="button" id="favourate_post_id_{{ $d['id'] }}" data-fav-post-id="{{ $d['id'] }}" value="{{ $d['title'] }}" class="bg-green-500 text-white leading-8 w-8 h-8 save_favourate rounded-tl-lg rounded-bl-lg  text-center focus:outline-none border-0">
                                    <i class="fa {{$hearty}}" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <a href="{{$slug}}">
                            <img class="m-auto h-48" src="{{ $imgUrlfinal }}" alt="post image" />
                        </a>
                        <div class="px-4">
                            <a href="{{$slug}}">
                                <p class="4xl text-gray-600 pt-4 truncate"><?php echo mb_strimwidth($d['title'], 0, 18, ".."); ?></p>
                            </a>
                            <ul>
                                <li class="4xl font-bold text-gray-600 inline-block"><span class="4xl font-bold text-gray-600 pt-5"><?php echo $currency_symbol[0]; ?></span><span>{{$d["price"]}}</span></li>
                                <li class="4xl text-gray-600 hidden sm:inline-block float-right">
                                    <p class="text-right"><?php echo mb_strimwidth($get_categoryname, 0, 15, ".."); ?> </p>
                                </li>
                            </ul>
                            <p class="4xl text-gray-600 pb-2"><span class="inline-block align-middle"><img class="w-3 sm:w-4" src="{{ URL::to('images/frontend/Group111.png') }}" alt="location"></span><span class="inline-block align-middle pl-1 text-xs sm:text-base w-9/12 truncate"><?php echo mb_strimwidth($d["city_name"], 0, 25, ".."); ?></span></p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="w-2/5 slick_arrow_button float-right cursor-pointer text-right absolute right-0 top-0 pr-6">
								<span class="mr-4 inline-block bg-green-500 rounded-full text-center leading-9 text-white recent_left_arrow w-9 h-9"><i class="fa fa-angle-left" aria-hidden="true"></i></span>
								<span class="inline-block bg-green-500 rounded-full text-center leading-9 text-white recent_right_arrow w-9 h-9"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
							</div>
                            </div></div>
    </div>

					<!-- Recently Viewed products Desktop - slider end -->
					</div>
				</div>
				
				<div class="relative w-full sm:w-7/12 float-left mb-8 sm:-mt-40 md:-mt-28 lg:mt-0">
					<div class="mb-0 px-2 py-2 float-left w-full">
                    <?php $created_on = date('d M Y', strtotime($product[0]->created_at));?>
					   <p class="text-lg font-semibold">{{ $created_on }} - {{$category_name}} - <i class="fa fa-map-marker fa-lg"></i> {{ $final_city_name }}</p>
					</div>
					<!-- common star rating begin-->
					<div class="px-2 py-2 w-full float-left">
					   <div class="mt-2 items-center">
                       <?php
                        $starwhite = URL::asset('/images/star1.png');
                        $starFill = URL::asset('/images/star2.png');
                        ?>
						<label class="inline-flex items-center mr-2 text-green-500 text-xl"><img src="{{ ($avg_rating>=1)?$starFill:$starwhite }}" class="w-4 h-4" /></label>
						<label class="inline-flex items-center mr-2 text-green-500 text-xl"><img src="{{ ($avg_rating>=2)?$starFill:$starwhite }}" class="w-4 h-4" /></label>
						<label class="inline-flex items-center mr-2 text-green-500 text-xl"><img src="{{ ($avg_rating>=3)?$starFill:$starwhite }}" class="w-4 h-4" /></label>
						<label class="inline-flex items-center mr-2 text-green-500 text-xl"><img src="{{ ($avg_rating>=4)?$starFill:$starwhite }}" class="w-4 h-4" /></label>
						<label class="inline-flex items-center mr-2 text-green-500 text-xl"><img src="{{ ($avg_rating>=5)?$starFill:$starwhite }}" class="w-4 h-4" /></label>
						<label class="inline-flex items-center mr-2">{{ $avg_rating }} Star</label>
					   </div>
					</div>
					

                    <?php
                    $detail_url = App\Models\TblPost::get_post_slug($product[0]->slug);

                    $images = explode(',', $product[0]->images);

                    if (!empty($images[0])) {
                        $imageurl = URL::asset('/storage') . '/' . $images[0];
                    } else {
                        $imageurl = "";
                    }
					$url = URL::to('/'.$product[0]->slug);
                    ?>
                    @section('whatsapp_meta')
                    <meta property="og:image" content="<?php echo $imageurl; ?>">
                    <meta property="og:url" content="<?php echo $detail_url; ?>">
                    <meta property="og:title" content="<?php echo $product[0]->title; ?>">
                    @endsection
					<div class="ml-0 mt-2 px-2 py-2 w-full float-left text-left inline-block">
					   <!-- twitter -->
					   <a class="p-1 bg-blue-400 text-white w-8 h-8 rounded-full outline-none focus:outline-none mr-1 mb-1 inline-block text-center" href="https://twitter.com/share?text=<?php echo $url; ?>" target="_blank">
					   <i class="fa fa-twitter"></i>
					   </a>
					   <!-- facebook -->
					   <a class="p-1 bg-blue-600 text-white w-8 h-8 rounded-full outline-none focus:outline-none mr-1 mb-1 inline-block text-center" rel="nofollow" href="http://www.facebook.com/share.php?u=<?php echo $detail_url; ?>" target="_blank">
					   <i class="fa fa-facebook-f"></i>
					   </a>
					   <!-- whatsapp -->
					   <a class="p-1 bg-green-500 text-white w-8 h-8 rounded-full outline-none focus:outline-none mr-1 mb-1 inline-block text-center" href="https://api.whatsapp.com:/send?text=<?php echo $detail_url; ?>" target="_blank">
					   <i class="fa fa-whatsapp"></i>
					   </a>
					   <!-- linkedin -->
					   <a class="p-1 bg-blue-800 text-white w-8 h-8 rounded-full outline-none focus:outline-none mr-1 mb-1 inline-block text-center" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $detail_url; ?>&title=&summary=&source=" target="_blank">
					   <i class="fa fa-linkedin"></i>
					   </a>
					</div>
				
				
					<div class="relative w-full float-left mb-8">
						<div class="mt-3 px-2 py-2">
							<div class="w-full float-left mb-3 md:mb-4">
								<div class="prod_info_title w-full float-left cursor-pointer">
									<h2 class="font-bold text-lg md:text-xl md:text-2xl xl:text-3xl mb-2 float-left" @click="open = !open">Item Details</h2>
									<div class="float-right text-2xl text-black mr-10">
										<i class="fa fa-angle-down" aria-hidden="true"></i>
									</div>
								</div>
								<div :class="{'flex p-5 border-t border-gray-200': open, 'hidden': !open}" class="prod_info_content hidden w-full float-left mb-2 md:mb-4">
								<?php if (!empty($additional)) { ?>
									<ul>
                                    <?php foreach ($additional as $k) { ?>

                                        <?php
                                                if ($k['type'] == "file") {
                                                    $imageurl2 = URL::asset('/storage') . '/' . $k['value'];
                                                ?>
                                                    <li class="w-full xl:w-2/4 float-left font-semibold text-base mt-2 md:mt-4"><span class="text-gray-600 w-2/4 float-left px-2">{{$k['label']}}</span><span class="text-black w-2/4 float-left px-2"><img src="{{ $imageurl2 }}" /></span></li>
                                                <?php } else { ?>
                
                                                    <li class="w-full xl:w-2/4 float-left font-semibold text-base mt-2 md:mt-4"><span class="text-gray-600 w-2/4 float-left px-2">{{$k['label']}}</span><span class="text-black w-2/4 float-left px-2">{{$k['value']}}</span></li>
                                                <?php } ?>

						
									<?php } ?>
									</ul>
                                    <?php } ?>
								</div>
							</div>
							
							
							<div class="w-full float-left mb-3 md:mb-4">
								<div class="prod_info_title w-full float-left cursor-pointer">
									<h2 class="font-bold text-lg md:text-xl md:text-2xl xl:text-3xl mb-4 float-left">Description</h2>
									<div class="float-right text-2xl text-black mr-10">
										<i class="fa fa-angle-down" aria-hidden="true"></i>
									</div>
								</div>
								<div :class="{'flex p-5 border-t border-gray-200': open, 'hidden': !open}" class="prod_info_content hidden w-full float-left mb-2 md:mb-4">
									<p class="text-base text-black">{{ $product[0]->description }}</p>
								</div>
							</div>
							
							<div class="w-full float-left mb-3 md:mb-4">
								<div class="prod_info_title w-full float-left cursor-pointer">
									<h2 class="font-bold text-lg md:text-xl md:text-2xl xl:text-3xl mb-4 float-left">Reviews</h2>
									
									<div class="float-right text-2xl text-black mr-10">
										<i class="fa fa-angle-down" aria-hidden="true"></i>
									</div>
								</div>
								<div :class="{'flex p-5 border-t border-gray-200': open, 'hidden': !open}" class="prod_info_content hidden w-full float-left mb-2 md:mb-4">

                                    <!--post review begin-->
                                    @if(Auth::user())
                                    <?php
                                    /* check - user already review this post.. */
                                    $chk_user_id = auth()->user()->id;
                                    $chk_post_id = $product[0]->id;
                                    $chk_post_user_id = $product[0]->user_id;
                                    $chk_review = App\Models\TblReview::where('user_id', $chk_user_id)->where('post_id', $chk_post_id)->count();
                                    ?>
                                    @if(!$chk_review > 0 && $chk_user_id!=$chk_post_user_id)
                                    <form action="{{ URL::to('review-store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
									<div class="float-right mb-4">
                                <label class="inline-flex items-center mr-2 text-green-500 text-xl"><input type="hidden" id="ad1_hidden" value="1"><img src="{{ $starwhite }}" onmouseover="change(this.id);" id="ad1" class="w-4 h-4" /></label>
                                <label class="inline-flex items-center mr-2 text-green-500 text-xl"><input type="hidden" id="ad2_hidden" value="2"><img src="{{ $starwhite }}" onmouseover="change(this.id);" id="ad2" class="w-4 h-4" /></label>
                                <label class="inline-flex items-center mr-2 text-green-500 text-xl"><input type="hidden" id="ad3_hidden" value="3"><img src="{{ $starwhite }}" onmouseover="change(this.id);" id="ad3" class="w-4 h-4" /></label>
                                <label class="inline-flex items-center mr-2 text-green-500 text-xl"><input type="hidden" id="ad4_hidden" value="4"><img src="{{ $starwhite }}" onmouseover="change(this.id);" id="ad4" class="w-4 h-4" /></label>
                                <label class="inline-flex items-center mr-2 text-green-500 text-xl"><input type="hidden" id="ad5_hidden" value="5"><img src="{{ $starwhite }}" onmouseover="change(this.id);" id="ad5" class="w-4 h-4" /></label>
									</div>
									<textarea maxlength="500" class="review_text w-full h-36 rounded-lg px-6 py-4 text-base text-black mb-4 border-l-4 border-gray-400 bg-gray-100 focus:outline-none resize-none placeholder-black" placeholder="{{__('post_detail.write your review')}}"  name="review_text" required="required" ></textarea>
                                    <p class="text-xs font-bold item-center text-gray-800">{{__('post_detail.character limit')}}: 500</p>
                                    <input type="hidden" name="review_ratings" id="adrating" />
                                    <input type="hidden" name="post_id" value="{{ $product[0]->id }}" />
                                    <input type="hidden" name="user_id" value="{{Auth::user()->id}}" />
                                    <input type="hidden" name="redirect_url" value="{{ request()->path() }}" />
									<button type="submit()" class="float-right px-6 md:px-10 xl:px-16 py-3 xl:py-4 text-base lg:text-lg xl:text-xl font-semibold leading-normal cursor-pointer outline-none focus:outline-none rounded-lg border-2 border-green-500 bg-transparent hover:bg-green-500 hover:border-green-500 text-green-500 hover:text-white transition ease-in-out duration-700">{{__('post_detail.save')}}</button>
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
                                    @endif
                                @else
                                <div class="text-center">
                                    <button class="bg-green-700 text-white font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 ease-linear transition-all duration-150" type="button">
                                        <a href="{{ URL::to('login') }}">{{__('post_detail.login to post review')}}</a></button>
                                </div>
                                    @endif
                                    <!--post review end-->
									
                                    @foreach($review as $r)
                                    <?php $newform = date('d M Y', strtotime($r->created_at)) ?>
                                    @if(($r->approved == "1"))
									<div class="w-full float-left mt-4 md:mt-8">
										<div class="bg-gray-100 px-4 py-4">
											<div class="bg-white px-4 py-4">
												<div class="mb-2">
													<div class="inline-flex items-center mr-2"><p class="bg-green-500 rounded-lg border text-white text-lg px-3 py-1">{{ $r->ratings }}<i class="fa fa-star-o ml-2"></i></p></div>
													<div class="inline-flex items-center mr-2"><p class="text-lg text-black">{{ $r->name }}</p></div>
												</div>
												
												<div class="mb-2">
													<p class="text-base md:text-lg text-black">{{ $r->comment }}</p>
												</div>
												
												<div class="mb-1">
													<p class="text-base md:text-lg text-black">{{ $newform }}</p>
												</div>
												
											</div>
										</div>
									</div>
                                    @endif
                                @if(auth()->user())
                                @if($r->approved != "1" && auth()->user()->id == $r->user_id)
								
                                <div class="w-full float-left mt-4 md:mt-8">
										<div class="bg-gray-100 px-4 py-4">
											<div class="bg-white px-4 py-4">
												<div class="mb-2">
													<div class="inline-flex items-center mr-2"><p class="bg-green-500 rounded-lg border text-white text-lg px-3 py-1">{{ $r->ratings }}<i class="fa fa-star-o ml-2"></i></p></div>
													<div class="inline-flex items-center mr-2"><p class="text-lg text-black">{{ $r->name }}</p></div>
												</div>
												
												<div class="mb-2">
													<p class="text-base md:text-lg text-black">{{ $r->comment }}</p>
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
					</div>
					
					<!-- Recently Viewed products Mobile - slider start -->
						
<div class="w-full pb-0 float-left sm:hidden">
                 <div class="relative">
    <p class="text-2xl font-bold text-gray-700 pb-4">{{__('post_detail.recently viewed ads')}}</p>
    <div class="recently_viewed_mob">
    <?php $i = 0; ?>
    @foreach($recently_viewed_products as $d)
    <?php
    $i++;
    $imgUrlfinal = App\Models\TblChat::getPostImgForList($d['id']);
    $get_categoryname = App\Models\TblCategory::getCategoryName($d['category_id']);
    $slug = App\Models\TblPost::get_post_slug($d["slug"]);
    $published_on = date("d M Y", strtotime($d["created_at"]));
    $fav_style = App\Models\TblSavedPosts::check_fav($d['id']);
    $hearty = ($fav_style==true)?'fa-heart':'fa-heart-o';
    $getPackType = App\Models\TblPayment::where('post_id', $d['id'])->get();
    $ad_type = "";
    if ($getPackType->count() > 0) {
        $packageID = $getPackType[0]->package_id;
        $ad_type = App\Models\Package::where('id', $packageID)->pluck('ad_type')->toArray();
    }
    /* show the curreny symbol */
    $settings = App\Models\Setting::get_logos();
    $slected_currency = !empty($d['currency_id']) ? $d['currency_id'] : $settings['default_currency'];
    $currency_symbol = App\Models\TblPost::get_post_currency($slected_currency);
    ?>
        <div class="pb-9 sm:w-2/4 lg:w-1/3 xl:w-1/4 items-center">
            <div class="border border-gray-200 shadow m-2 relative">
                <div class="items-center">
                    <div class="mt-1">
                    <?php if ($ad_type != "") { ?>
                            <h3 class="text-xs text-white font-semibold relative">
                                <span class="bg-yellow-500 px-2 py-1 rounded-tr-lg rounded-br-lg uppercase">
                                <?php echo str_replace('_', ' ', strtoupper($ad_type[0])); ?>
                                </span>
                            </h3>
                        <?php } ?>
                    </div>
                    <div class="absolute right-0 top-0">
                        <button type="button" id="favourate_post_id_{{ $d['id'] }}" data-fav-post-id="{{ $d['id'] }}" value="{{ $d['title'] }}" class="bg-green-500 text-white leading-8 w-8 h-8 save_favourate rounded-tl-lg rounded-bl-lg  text-center focus:outline-none border-0">
                            <i class="fa {{$hearty}}" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <a href="{{$slug}}">
                    <img class="m-auto h-48" src="{{ $imgUrlfinal }}" alt="post image" />
                </a>
                <div class="px-4">
                    <a href="{{$slug}}">
                        <p class="4xl text-gray-600 pt-4 truncate"><?php echo mb_strimwidth($d['title'], 0, 18, ".."); ?></p>
                    </a>
                    <ul>
                        <li class="4xl font-bold text-gray-600 inline-block"><span class="4xl font-bold text-gray-600 pt-5"><?php echo $currency_symbol[0]; ?></span><span>{{$d["price"]}}</span></li>
                        <li class="4xl text-gray-600 hidden sm:inline-block float-right">
                            <p class="text-right"><?php echo mb_strimwidth($get_categoryname, 0, 15, ".."); ?> </p>
                        </li>
                    </ul>
                    <p class="4xl text-gray-600 pb-2"><span class="inline-block align-middle"><img class="w-3 sm:w-4" src="{{ URL::to('images/frontend/Group111.png') }}" alt="location"></span><span class="inline-block align-middle pl-1 text-xs sm:text-base w-9/12 truncate"><?php echo mb_strimwidth($d["city_name"], 0, 25, ".."); ?></span></p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="w-2/5 slick_arrow_button float-right cursor-pointer text-right absolute right-0 top-0 pr-6">
                        <span class="mr-4 inline-block bg-green-500 rounded-full text-center leading-9 text-white recent_left_arrow w-9 h-9"><i class="fa fa-angle-left" aria-hidden="true"></i></span>
                        <span class="inline-block bg-green-500 rounded-full text-center leading-9 text-white recent_right_arrow w-9 h-9"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
                    </div>
                    </div></div>

					<!-- Recently Viewed products Mobile - slider end -->
				</div>
			</div>
		</div>
		<!-- common start rating end-->
	
<!--End -- block 2-->


	  

<!-- Begin -- block 3 -->

    <!-- start related products--->
    <div class="w-full float-left">
        <div class="container px-4 mx-auto" wire:ignore>
            <p class="text-2xl font-bold text-gray-700 pb-4">{{__('post_detail.related ads')}}</p>
            <div class="related_products w-full">
            <?php $i = 0; ?>
            @foreach($related_products as $d)
            <?php
            $i++;
            $imgUrlfinal = App\Models\TblChat::getPostImgForList($d['id']);
            $get_categoryname = App\Models\TblCategory::getCategoryName($d['category_id']);
            $slug = App\Models\TblPost::get_post_slug($d["slug"]);
            $published_on = date("d M Y", strtotime($d["created_at"]));
            $fav_style = App\Models\TblSavedPosts::check_fav($d['id']);
            $hearty = ($fav_style==true)?'fa-heart':'fa-heart-o';
            $getPackType = App\Models\TblPayment::where('post_id', $d['id'])->get();
            $ad_type = "";
            if ($getPackType->count() > 0) {
                $packageID = $getPackType[0]->package_id;
                $ad_type = App\Models\Package::where('id', $packageID)->pluck('ad_type')->toArray();
            }
            /* show the curreny symbol */
            $settings = App\Models\Setting::get_logos();
            $slected_currency = !empty($d['currency_id']) ? $d['currency_id'] : $settings['default_currency'];
            $currency_symbol = App\Models\TblPost::get_post_currency($slected_currency);
            ?>

            
                <div class="pb-9 sm:w-2/4 lg:w-1/3 xl:w-1/4 items-center">
                    <div class="border border-gray-200 shadow m-2 relative">
                        <div class="items-center">
                            <div class="mt-1">
                            <?php if ($ad_type != "") { ?>
                                    <h3 class="text-xs text-white font-semibold relative">
                                        <span class="bg-yellow-500 px-2 py-1 rounded-tr-lg rounded-br-lg uppercase">
                                        <?php echo str_replace('_', ' ', strtoupper($ad_type[0])); ?>
                                        </span>
                                    </h3>
                                <?php } ?>
                            </div>
                            <div class="absolute right-0 top-0">
                                <button type="button" id="favourate_post_id_{{ $d['id'] }}" data-fav-post-id="{{ $d['id'] }}" value="{{ $d['title'] }}" class="bg-green-500 text-white leading-8 w-8 h-8 save_favourate rounded-tl-lg rounded-bl-lg  text-center focus:outline-none border-0">
                                    <i class="fa {{$hearty}}" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                        <a href="{{$slug}}">
                            <img class="m-auto h-48" src="{{ $imgUrlfinal }}" alt="post image" />
                        </a>
                        <div class="px-4">
                            <a href="{{$slug}}">
                                <p class="4xl text-gray-600 pt-4 truncate"><?php echo mb_strimwidth($d['title'], 0, 18, ".."); ?></p>
                            </a>
                            <ul>
                                <li class="4xl font-bold text-gray-600 inline-block"><span class="4xl font-bold text-gray-600 pt-5"><?php echo $currency_symbol[0]; ?></span><span>{{$d["price"]}}</span></li>
                                <li class="4xl text-gray-600 hidden sm:inline-block float-right">
                                    <p class="text-right"><?php echo mb_strimwidth($get_categoryname, 0, 15, ".."); ?> </p>
                                </li>
                            </ul>
                            <p class="4xl text-gray-600 pb-2"><span class="inline-block align-middle"><img class="w-3 sm:w-4" src="{{ URL::to('images/frontend/Group111.png') }}" alt="location"></span><span class="inline-block align-middle pl-1 text-xs sm:text-base w-9/12 truncate"><?php echo mb_strimwidth($d["city_name"], 0, 25, ".."); ?></span></p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- End related products -->



    

<!-- End -- block 3 -->




{!! config('app.google_map_script') !!}
        <script type="text/javascript">
        <?php if (!empty($product[0]->video_url)) { ?>
            // view video        
            $('#view_video').on('click', function(e) {
                document.querySelector("#view_video_popup").style.display = "block";
            });
            $('#cancel_video').on('click', function(e) {
                document.querySelector("#view_video_popup").style.display = "none";
                window.location.reload();
            });
            // end video
        <?php } ?>

        // report ad start
        $('#report_ad').on('click', function(e) {
            var user = "{{auth()->user()}}";
            var post_id = "{{$product[0]->id}}";
            if (user == "") {
                window.location.href = "/login";
            } else {
                document.querySelector("#report").style.display = "block";
            }
            $('#submit').on('click', function(e) {
                var comment = $('#comment').val();
                var retype = $('input[name="re_type"]:checked').val();
                if (comment == "") //change if condition
                {
                    toastr.warning('Comment field is required..');
                }
                if (retype == null) {
                    toastr.warning('retype field is required..');
                }
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "{{ route('report_ad') }}",
                    data: {
                        comment: comment,
                        retype: retype,
                        post_id: post_id
                    },
                    success: function(data) {
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
        // report ad end

        <?php if (!empty($currentUserId) && ($currentUserId != $adPostedUserId)) { ?>
            <?php if ($product[0]->exchange_to_buy == 1) { ?>
                $('#user_exchange_buy').on('click', function(e) {
                    var user = "{{auth()->user()}}";
                    if (user != "") {
                        document.querySelector("#user_exchange_buy_data").style.display = "block";
                    }
                });
                $(".selected_post").change(function() {
                    var maxAllowed = 1;
                    var cnt = $("input[name='selected_pid']:checked").length;
                    if (cnt > maxAllowed) {
                        $(this).prop("checked", "");
                        toastr.warning('Cannot select more than one product to exchange!');
                    }
                });

                $('#exchange_submit').on('click', function(e) {
                    var selected_post = $("input[name='selected_pid']:checked").val();
                    var user_id = "<?php echo !empty(auth()->user()) ? auth()->user()->id : ""; ?>"
                    var post_owner = "{{$info_user[0]->id}}";
                    var post_id = "{{$product[0]->id}}";
                    if (selected_post == "" || selected_post == "undefined" || selected_post == null) //change if condition
                    {
                        toastr.warning('Choose any product to exchange!');
                        return false;
                    } else {
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: "{{ route('add_exchange') }}",
                            data: {
                                exchange_id: selected_post,
                                post_id: post_id,
                                post_owner: post_owner,
                                user_id: user_id
                            },
                            success: function(data) {
                                document.querySelector("#user_exchange_buy_data").style.display = "none";
                                $('input:checkbox[name="selected_pid"]').attr('checked', false);
                                if (data.flag == 1) {
                                    toastr.success(data.message);
                                } else {
                                    toastr.warning(data.message);
                                }
                            }
                        });
                    }
                });
                $('#exchange_cancel').on('click', function(e) {
                    document.querySelector("#user_exchange_buy_data").style.display = "none";
                });
            <?php } ?>
            <?php if ($product[0]->fixed_price == 0) { ?>
                $('#user_make_offer').on('click', function(e) {
                    var user = "{{auth()->user()}}";
                    if (user != "") {
                        document.querySelector("#user_make_offer_data").style.display = "block";
                    }
                });
                $('#make_offer_cancel').on('click', function(e) {
                    document.querySelector("#user_make_offer_data").style.display = "none";
                });
                $('#make_offer_submit').on('click', function(e) {
                    var to = "{{$info_user[0]->id}}";
                    var post_id = "{{$product[0]->id}}";
                    var message = $("#make_offer_message").val();
                    var price = $("#ask_price").val();
                    var orginal_price = "<?php echo round($product[0]->price); ?>";
                    var make_offer = "yes";
                    if ((price > orginal_price) || (price == "") || (price == 0)) {
                        toastr.warning('Please enter valid offer price.');
                        return false;
                    } else {
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: "{{ URL::to('send_chat') }}",
                            data: {
                                chat_message: price,
                                to: to,
                                post_id: post_id,
                                image: "",
                                make_offer: make_offer
                            },
                            success: function(data) {
                                document.querySelector("#user_make_offer_data").style.display = "none";
                                $("#ask_price").val("");
                                $("#make_offer_message").val("");
                                toastr.success("Your offer sent successfully!");
                            }
                        });
                    }
                });
            <?php } ?>
        <?php } ?>

        $(window).load(function() {
            $("ul.slides li").css({
                "width": "auto !important"
            });
            // The slider being synced must be initialized first
            $('#carousel').flexslider({
                animation: "slide",
                controlNav: false,
                animationLoop: false,
                slideshow: false,
                itemMargin: 5,
                asNavFor: '#slider'
            });
            $('#slider').flexslider({
                animation: "slide",
                controlNav: false,
                animationLoop: false,
                slideshow: false,
                sync: "#carousel"
            });
        });
    </script>
    <!--slider end-->
    <style>
        #carousel ul.slides li {
            width: auto !important;
        }
    </style>
    <!--star rating-->
    <script type="text/javascript">
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
    <!--star rating-->
    <script type="text/javascript">
        jQuery(document).ready(function() {
            jQuery(".related_products").slick({
                dots: false,
                infinite: false,
                slidesToShow: 3,
                slidesToScroll: 1,
                responsive: [{
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                            infinite: false,
                            dots: false
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                            infinite: false,
                            dots: false
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            infinite: false,
                            dots: false
                        }
                    }
                ]
            });

            jQuery(".recently_viewed_products").slick({
                dots: false,
                infinite: false,
                slidesToShow: 1,
                slidesToScroll: 1,
                responsive: [{
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                            infinite: false,
                            dots: false
                        }
                    },
                    {
                        breakpoint: 600,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 1,
                            infinite: false,
                            dots: false
                        }
                    },
                    {
                        breakpoint: 480,
                        settings: {
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            infinite: false,
                            dots: false
                        }
                    }
                ]
            });


        });

        (function() {
            // store the slider in a local variable
            var $window = $(window),
                flexslider = {
                    vars: {}
                };
            // tiny helper function to add breakpoints
            function getGridSize() {
                return (window.innerWidth < 600) ? 2 : (window.innerWidth < 900) ? 3 : 4;
            }
            $(function() {
                //SyntaxHighlighter.all();
            });
            $window.load(function() {
                $('.flexslider').flexslider({
                    controlNav: false,
                    animation: "slide",
                    animationSpeed: 600,
                    animationLoop: false,
                    itemWidth: 210,
                    itemMargin: 5,
                    minItems: getGridSize(), // use function to pull in initial value
                    maxItems: getGridSize(), // use function to pull in initial value
                    start: function(slider) {
                        $('body').removeClass('loading');
                        flexslider = slider;
                    }
                });
            });
            // check grid size on resize event
            $window.resize(function() {
                var gridSize = getGridSize();
                flexslider.vars.minItems = gridSize;
                flexslider.vars.maxItems = gridSize;
            });
        }());
    </script>
    <script>
        $(window).load(function() {
            var isActiveuser = '<?php echo (!empty(auth()->user())) ? "1" : "0"; ?>';
            $("#user_info_closed").click(function() {
                if (isActiveuser == 0) {
                    toastr.warning('Please login to show the detail..');
                    return false;
                }
                $("#user_info_closed").css('display', 'none');
                $("#user_info_open").css('display', 'block');
            })
            $("#chat_with_seller_closed").click(function() {
                if (isActiveuser == 0) {
                    toastr.warning('Please login to chat with seller..');
                    return false;
                }
                $("#chat_with_seller_closed").css('display', 'none');
                $("#chat_with_seller_open").css('display', 'block');
            })
            $("#user_exchange_buy").click(function() {
                if (isActiveuser == 0) {
                    toastr.warning('Please login to continue..');
                    return false;
                }
            });
            $("#user_instant_buy").click(function() {
                if (isActiveuser == 0) {
                    toastr.warning('Please login to continue..');
                    return false;
                }
            })
        });
    </script>



</div>