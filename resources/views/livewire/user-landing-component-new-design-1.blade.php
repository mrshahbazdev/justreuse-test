<div class="root-element-div">
    <!-- search bar with banners start --->
    <div class="w-full bg-right bg-no-repeat z-10 bg-cover relative top-0 rounded-md float-left" wire:ignore>
        <div class="w-full relative">
            <div class="banner_image">
                <?php
                if ($banner_type == "2") {
                    if (!empty($banner_ads)) { // banner type "2" is enable images option
                        foreach ($banner_ads as $banner_ad) { ?>
                            <a href="<?php echo $banner_ad['url']; ?>" target="_blank">
                                <img src="<?php echo $banner_ad['image']; ?>" alt="banner.jpg" />
                            </a>
                <?php }
                    }
                }
                ?>
            </div>
            <span id="blackOverlay" class="w-full h-full absolute z-0 top-0 bg-opacity-30 bg-black"></span>
        </div>
        @include('layouts.search-bar')
    </div>
    <?php
    if ($banner_type == "1") {       // banner type "1" is enable map option
        if ($this->enable_banner_map == "1") {
            $filepath = base_path() . '/extra/bannerplugins/bannermap/src/view.php';
            if (file_exists($filepath)) {
                include($filepath);
            }
        }
    }
    ?>
    <!-- search bar with banners end -->
    <div class="w-full bg-no-repeat bg-cover bg-center float-left" style="background-image: url('../images/frontend/categories_bg.png');" wire:ignore>
        <div class="container px-4 mx-auto">
            <p class="text-2xl font-bold text-gray-700 py-8">Categories</p>
            <div class="cate_slider w-full flex flex-row pb-32">
                @foreach($main_categories as $main_category)
                <?php
                $cat_img = !empty($main_category->image) ? URL::to('storage') . '/' . $main_category->image : URL::to('storage/noimage150.png');
                ?>
                <div class="text-center w-56">
                    <a href="<?php echo Session::get('Searchedurl') . "&c=$main_category->slug"; ?>">
                        <div class="bg-white rounded-full mx-4 text-center p-8 md:p-8 lg:p-12">
                            <img src="{{$cat_img}}" alt="{{$main_category->title}}">
                        </div>
                        <p class="4xl font-bold text-gray-600 pt-5">{{$main_category->title}}</p>
                    </a>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Feature Ads start -->
    <div class="w-full pb-0 float-left" wire:ignore>
        <div class="container px-4 mx-auto relative">
            <p class="text-2xl font-bold text-gray-700 pb-4">{{__('landing.feature ads')}} </p>
            <?php if (session::has('FeatureAdsBasedOn')) { ?>
                <p class="text-sm mb-4"><?php echo Session::get("FeatureAdsBasedOn"); ?></p>
            <?php } ?>
            <div class="recent_ads w-full flex flex-row ...">
                <?php $i = 0; ?>
                @foreach($featurs_ad_list as $featurs_ad)
                <?php
                $i++;
                $imgUrlfinal = App\Models\TblChat::getPostImgForList($featurs_ad['id']);
                $get_categoryname = App\Models\TblCategory::getCategoryName($featurs_ad['category_id']);
                $slug = App\Models\TblPost::get_post_slug($featurs_ad["slug"]);
                $fav_post = App\Models\TblSavedPosts::check_fav($featurs_ad['id']);
                $currency_symbol = App\Models\TblPost::get_post_currency($featurs_ad['currency_id']);
                $final_city_name = !empty($featurs_ad['locality']) ? $featurs_ad['locality'] : $featurs_ad["city_name"]; // get locality & city 
                $feat_adtype = App\Models\TblPost::getAddtype($featurs_ad['id']);
                ?>

                
                <div class="pb-9 sm:w-2/4 lg:w-1/3 xl:w-1/4 items-center">
                    <div class="border border-gray-200 shadow m-2 relative">
                        <div class="items-center">
                            <div class="mt-1">
                                <?php
                                if (!empty($feat_adtype)) {
                                ?>
                                    <h3 class="text-xs text-white font-semibold relative">
                                        <span class="bg-yellow-500 px-2 py-1 rounded-tr-lg rounded-br-lg uppercase">
                                            <?php echo str_replace('_', ' ', strtoupper($feat_adtype->ad_type)); ?>
                                        </span>
                                    </h3>
                                <?php } ?>
                            </div>
                            <div class="absolute right-0 top-0">
                                <button type="button" id="favourate_post_id_{{ $featurs_ad['id'] }}" data-fav-post-id="{{ $featurs_ad['id'] }}" value="{{ $featurs_ad['title'] }}" class="bg-green-500 text-white leading-8 w-7 h-7 save_favourate rounded-tl-lg rounded-bl-lg  text-center focus:outline-none border-0">
                                    @if(!empty($fav_post))
                                    <i class="fa fa-heart" aria-hidden="true"></i>
                                    @else
                                    <i class="fa fa-heart-o" aria-hidden="true"></i>
                                    @endif
                                </button>
                            </div>
                        </div>
                        <a href="{{$slug}}">
                            <img class="m-auto h-48" src="{{$imgUrlfinal}}" />
                        </a>
                        <div class="px-4">
                            <a href="{{$slug}}">
                                <p class="4xl text-gray-600 pt-4 truncate"><?php echo mb_strimwidth($featurs_ad['title'], 0, 18, ".."); ?></p>
                            </a>
                            <ul>
                                <li class="4xl font-bold text-gray-600 inline-block"><span class="4xl font-bold text-gray-600 pt-5"><?php echo $currency_symbol[0]; ?></span><span>{{$featurs_ad["price"]}}</span></li>
                                <li class="4xl text-gray-600 hidden sm:inline-block float-right">
                                    <p class="text-right"><?php echo mb_strimwidth($get_categoryname, 0, 15, ".."); ?></p>
                                </li>
                            </ul>
                            <p class="4xl text-gray-600 pb-2"><span class="inline-block align-middle"><img class="w-3 sm:w-4" src="{{ URL::to('images/frontend/Group111.png')}}" alt="location" /></span><span class="inline-block align-middle pl-1 text-xs sm:text-base w-9/12 truncate"><?php echo mb_strimwidth($final_city_name, 0, 25, ".."); ?></span></p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="w-2/5 slick_arrow_button float-right cursor-pointer text-right absolute right-0 top-0 pr-6">
                <span class="mr-4 inline-block bg-green-500 rounded-full text-center leading-9 text-white left_arrow w-9 h-9"><i class="fa fa-angle-left" aria-hidden="true"></i></span>
                <span class="inline-block bg-green-500 rounded-full text-center leading-9 text-white right_arrow w-9 h-9"><i class="fa fa-angle-right" aria-hidden="true"></i></span>
            </div>
        </div>
    </div>
    <!-- Feature Ads end -->

    <!-- Top Ads start --->
    <div class="w-full float-left">
        <div class="container px-4 mx-auto" wire:ignore>
            <p class="text-2xl font-bold text-gray-700 pb-4">Top Ads</p>
            <?php if (session::has('TopAdsBasedOn')) { ?>
                <p class="text-sm mb-4"><?php echo Session::get("TopAdsBasedOn"); ?></p>
            <?php } ?>
            <div class="Top_ads w-full flex flex-row ...">
                <?php $i = 0 ?>
                @foreach($top_ads_list as $top_ad_list)
                <?php
                $i++;
                $imgUrlfinal = App\Models\TblChat::getPostImgForList($top_ad_list['id']);
                $get_categoryname = App\Models\TblCategory::getCategoryName($top_ad_list['category_id']);
                $slug = App\Models\TblPost::get_post_slug($top_ad_list["slug"]);
                $fav_post = App\Models\TblSavedPosts::check_fav($top_ad_list['id']);
                $currency_symbol = App\Models\TblPost::get_post_currency($top_ad_list['currency_id']);
                $final_city_name2 = !empty($top_ad_list['locality']) ? $top_ad_list['locality'] : $top_ad_list["city_name"]; // get locality & city 
                $top_adtype = App\Models\TblPost::getAddtype($top_ad_list['id']);
                ?>
                <div class="pb-9 sm:w-2/4 lg:w-1/3 xl:w-1/4 items-center">
                    <div class="border border-gray-200 shadow m-2 relative">
                        <div class="items-center">
                            <div class="mt-1">
                                <?php
                                if (!empty($top_adtype)) {
                                ?>
                                    <h3 class="text-xs text-white font-semibold relative">
                                        <span class="bg-yellow-500 px-2 py-1 rounded-tr-lg rounded-br-lg uppercase">
                                            <?php echo str_replace('_', ' ', strtoupper($top_adtype->ad_type)); ?>
                                        </span>
                                    </h3>
                                <?php } ?>
                            </div>
                            <div class="absolute right-0 top-0">
                                <button type="button" id="favourate_post_id_{{ $top_ad_list['id'] }}" data-fav-post-id="{{ $top_ad_list['id'] }}" value="{{ $top_ad_list['title'] }}" class="bg-green-500 text-white leading-8 w-8 h-8 save_favourate rounded-tl-lg rounded-bl-lg  text-center focus:outline-none border-0">
                                    @if(!empty($fav_post))
                                    <i class="fa fa-heart" aria-hidden="true"></i>
                                    @else
                                    <i class="fa fa-heart-o" aria-hidden="true"></i>
                                    @endif
                                </button>
                            </div>
                        </div>
                        <a href="{{$slug}}">
                            <img class="m-auto h-48" src="{{ $imgUrlfinal }}" alt="post image" />
                        </a>
                        <div class="px-4">
                            <a href="{{$slug}}">
                                <p class="4xl text-gray-600 pt-4 truncate"><?php echo mb_strimwidth($top_ad_list['title'], 0, 18, ".."); ?></p>
                            </a>
                            <ul>
                                <li class="4xl font-bold text-gray-600 inline-block"><span class="4xl font-bold text-gray-600 pt-5"><?php echo $currency_symbol[0]; ?></span><span>{{$top_ad_list["price"]}}</span></li>
                                <li class="4xl text-gray-600 hidden sm:inline-block float-right">
                                    <p class="text-right"><?php echo mb_strimwidth($get_categoryname, 0, 15, ".."); ?> </p>
                                </li>
                            </ul>
                            <p class="4xl text-gray-600 pb-2"><span class="inline-block align-middle"><img class="w-3 sm:w-4" src="{{ URL::to('images/frontend/Group111.png') }}" alt="location"></span><span class="inline-block align-middle pl-1 text-xs sm:text-base w-9/12 truncate"><?php echo mb_strimwidth($final_city_name2, 0, 25, ".."); ?></span></p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    <!-- Top Ads End -->

    <!-- Banner start --->
    <div class="w-full float-left">
        <div class="rounded-md">
            <div class="container px-4  mx-auto">
                <div class="w-full bg-ceter bg-no-repeat bg-cover rounded-md h-48" style="background-image: url('../images/frontend/ad_banner.png')" ;>
                    <p class="w-full text-xs text-lg lg:text-3xl font-bold text-white lg:w-2/4 pl-6 py-8 pr-6 pt-8  md:pt-10 md:pl-20 md:pr-20 md:pb-10 lg:pl-20 lg:py-16 lg:pr-12 lg:pt-12">This is a simple example of a Landing Page you can build using Notus JS.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- Banner end -->

    <!-- latest ads start -->
    <div class="w-full float-left">
        <div class="container px-4 mx-auto">
            <p class="text-2xl font-bold text-gray-700 py-4">{{__('landing.latest ads')}}</p>
            <?php if (session::has('LatestAdsBasedOn')) { ?>
                <p class="text-sm mb-4"><?php echo Session::get("LatestAdsBasedOn"); ?></p>
            <?php } ?>
            <div class="w-full mb-4 lg:mb-0 md:flex md:flex-wrap">
                <?php $i = 0; ?>
                @foreach($posts as $latestpost)
                <?php
                $i++;
                $imgUrlfinal = App\Models\TblChat::getPostImgForList($latestpost->id);
                $slug = App\Models\TblPost::get_post_slug($latestpost->slug);
                $get_categoryname = App\Models\TblCategory::getCategoryName($latestpost->category_id);
                $adtype = App\Models\TblPost::getAddtype($latestpost->id);
                $fav_style = App\Models\TblSavedPosts::check_fav($latestpost->id);
                $currency_symbol = App\Models\TblPost::get_post_currency($latestpost->currency_id);
                $final_city_name1 = !empty($latestpost->locality) ? $latestpost->locality : $latestpost->city_name; // get locality & city 
                ?>
                <div class="w-2/4 sm:w-2/4 lg:w-1/3 xl:w-1/4 pb-4 lg:pb-9 items-center float-left">
                    <div class="border border-gray-200 shadow m-2 relative">
                        <div class="items-center">
                            <div class="absolute right-0 top-0">
                                <button type="button" id="favourate_post_id_{{ $latestpost->id }}" data-fav-post-id="{{ $latestpost->id }}" value="{{$latestpost->title}}" class="bg-green-500 text-white leading-8 w-8 h-8 save_favourate rounded-tl-lg rounded-bl-lg  text-center focus:outline-none border-0">
                                    @if(!empty($fav_style))
                                    <i class="fa fa-heart" aria-hidden="true"></i>
                                    @else
                                    <i class="fa fa-heart-o" aria-hidden="true"></i>
                                    @endif
                                </button>
                            </div>
                        </div>
                        <a href="{{$slug}}">
                            <img class="m-auto h-48" src="{{ $imgUrlfinal }}">
                        </a>
                        <div class="px-4">
                            <a href="{{$slug}}">
                                <p class="4xl text-gray-600 pt-1 truncate"><?php echo mb_strimwidth($latestpost->title, 0, 18, "..."); ?></p>
                            </a>
                            <ul>
                                <li class="4xl font-bold text-gray-600 inline-block"><span class="4xl font-bold text-gray-600 pt-5"><?php echo $currency_symbol[0]; ?></span><span>{{ $latestpost->price }}</span></li>
                                <li class="4xl text-gray-600 hidden sm:inline-block float-right">
                                    <p class="text-right"><?php echo mb_strimwidth($get_categoryname, 0, 15, "..."); ?> </p>
                                </li>
                            </ul>
                            <p class="4xl text-gray-600 pb-2"><span class="inline-block align-middle"><img class="w-3 sm:w-4" src="{{ URL::to('images/frontend/Group111.png') }}"></span><span class="inline-block align-middle pl-1 text-xs sm:text-base w-9/12 truncate"><?php echo mb_strimwidth($final_city_name1, 0, 25, ".."); ?></span></p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @if($posts->hasMorePages())
            <div class="text-center mb-4">
                <button class="text-white px-4 border focus:outline-none rounded py-2 bg-green-500 border-gree-500 rounded-3xl" wire:click="loadMore()">{{__('messages.Load More')}}</button>
            </div>
            @endif
        </div>
    </div>
    <!-- latest ads end -->
</div>