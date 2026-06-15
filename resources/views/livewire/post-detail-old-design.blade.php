<div class="root-element-div">
    <link rel="stylesheet" href="{{ URL::to('css/flexslider_demo.css') }}">
    <link rel="stylesheet" href="{{ URL::to('css/flexslider.css') }}">
    <section class="relative w-full h-full py-10 min-h-screen">
        <div class="container mx-auto px-4">
            <div x-data="{ mobileMenuOpen : false }" class="flex flex-wrap">
                <div class="px-4 py-2 w-full border mb-10 border-gray-200">
                    <?php
                    echo '<ul class="flex text-indigo-600 text-sm lg:text-base">';
                    echo "<li><a class=text-sm href='" . URL::to('/') . "'><i class='fa fa-home'></i> / </li>";
                    $i = 1;
                    $addnew_end_title = "";
                    foreach ($ancestors as $r) {
                        $catname = "&nbsp;" . $r->title;
                        $addnew_end_title = $r->title;
                        $country_url = URL::to('/') . "/" . str_replace(" ", "_", strtolower($info_location[0]->country_long)) . "?c=" . $r->slug . "&loc=" . $info_location[0]->country_long . "&s=&city=&state=&country=" . $info_location[0]->country_long;
                        echo '<li><a class=text-sm href="' . $country_url . '">' . $catname . ' / </a></li>';
                    }
                    echo "<li class='text-gray-600'>&nbsp;" . $product[0]->title . "</li>";
                    echo "</ul>";
                    $final_city_name = !empty($product[0]->locality) ? $product[0]->locality : $info_location[0]->name; // get locality & city 
                    ?>
                </div>
                <!--LEFT PORTION -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-block mb-6 lg:hidden w-16 h-8 bg-gray-200 text-gray-600 p-1">
                    Sidebar
                </button>

                <div class="w-full lg:w-8/12 md:w-12/12 ml-auto mr-auto  rounded">
                    <!--slider begin-->
                    <div class="ml-2 mr-2 px-2 py-2 border border-solid border-gray-200 rounded items-center">
                        @if($product[0]->images=="")
                        <p class="text-center p-10 font-semibold">{{__('post_detail.no images to preview')}}</p>
                        @else
                        <div class="flex flex-wrap items-center">
                            <div class="relative w-full px-2 max-w-full flex-grow flex">
                                @if($current_pack_name!="")
                                <h3 class="text-xs text-black w-10/12">
                                    <span class="rounded bg-yellow-500 px-2 py-1 uppercase">
                                        <?php echo str_replace('_', ' ', strtoupper($current_pack_name[0]->ad_type)); ?>
                                    </span>
                                </h3>
                                @endif
                                @if(!empty($product[0]->video_url))
                                <button class="text-blue bg-yellow-500 font-bold text-xs p-1 rounded cursor-pointer w-2/12 shadow hover:shadow-md outline-none focus:outline-none" id="view_video"><i class="fa fa-youtube" aria-hidden="true"></i> product video</button>
                                @endif
                            </div>
                        </div>
                        <section class="slider">
                            <div id="slider" class="flexslider">
                                <ul class="slides bg-gray-900">
                                    <?php
                                    $images = explode(',', $product[0]->images);
                                    foreach ($images as $r) {
                                        $imageurl = URL::asset('/storage') . '/' . $r;
                                    ?>
                                        <li data-thumb="{{ $imageurl }}">
                                            <img src="{{ $imageurl }}" style="margin-left:auto;margin-right:auto;width:auto;height:auto;" />
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div id="carousel" class="flexslider">
                                <ul class="slides">
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
                                        <li data-thumb="{{ $imgUrlfinal }}">
                                            <img src="{{ $imgUrlfinal }}" />
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </section>
                        @endif
                    </div>
                    <!--slider end-->
                    <?php
                    /* show the curreny symbol */
                    $settings = App\Models\Setting::get_logos();
                    $slected_currency = !empty($product[0]->currency_id) ? $product[0]->currency_id : $settings['default_currency'];
                    $currency_symbol = App\Models\TblPost::get_post_currency($slected_currency);
                    ?>
                    <div class="mb-0 px-2 py-2">
                        <h2 class=" text-3xl capitalize font-bold px-2 py-2 border-b border-solid border-gray-200">{{ $product[0]->title }}
                            <?php
                            $product_condition = App\Models\TblPost::get_product_condition($product[0]->id);
                            ?>
                            @if(!empty($product_condition))
                            <span class="bg-gray-500 text-white rounded text-xs p-1">{{$product_condition}}</span>
                            @endif
                            <span class="font-semibold text-3x1 float-right"> <?php echo $currency_symbol[0]; ?>{{ $product[0]->price}} </span>
                        </h2>
                        <?php
                        $created_on = date('d M Y', strtotime($product[0]->created_at));
                        ?>
                        <p class="text-md font-semibold py-2">{{ $created_on }} - {{$category_name}} - <i class="fa fa-map-marker fa-lg"></i> {{ $final_city_name }}</p>
                    </div>
                    <!-- common star rating begin-->
                    <div class="px-2 py-2">
                        <?php
                        $starwhite = URL::asset('/images/star1.png');
                        $starFill = URL::asset('/images/star2.png');
                        ?>
                        <div class="mt-2 items-center">
                            <label class="inline-flex items-center mr-2"><img src="{{ ($avg_rating>=1)?$starFill:$starwhite }}" class="w-4 h-4" /></label>
                            <label class="inline-flex items-center mr-2"><img src="{{ ($avg_rating>=2)?$starFill:$starwhite }}" class="w-4 h-4" /></label>
                            <label class="inline-flex items-center mr-2"><img src="{{ ($avg_rating>=3)?$starFill:$starwhite }}" class="w-4 h-4" /></label>
                            <label class="inline-flex items-center mr-2"><img src="{{ ($avg_rating>=4)?$starFill:$starwhite }}" class="w-4 h-4" /></label>
                            <label class="inline-flex items-center mr-2"><img src="{{ ($avg_rating>=5)?$starFill:$starwhite }}" class="w-4 h-4" /></label>
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

                    <div class="ml-0 mt-5 px-2 py-2 w-full text-left">
                        <!-- twitter -->
                         <a class="p-2 pr-1 pt-1 pb-1 bg-blue-400 text-white w-8 h-8 rounded-full outline-none focus:outline-none mr-1 mb-1" href="https://twitter.com/share?text=<?php echo $url; ?>" target="_blank">
                            <i class="fa fa-twitter"></i>
                        </a>
                        <!-- facebook -->
                        <a class="p-2 pr-1 pt-1 pb-1 bg-blue-600 text-white w-8 h-8 rounded-full outline-none focus:outline-none mr-1 mb-1" rel="nofollow" href="http://www.facebook.com/share.php?u=<?php echo $detail_url; ?>" target="_blank">
                            <i class="fa fa-facebook-f"></i>
                        </a>
                        <!-- whatsapp -->
                        <a class="p-2 pr-1 pt-1 pb-1 bg-green-500 text-white w-8 h-8 rounded-full outline-none focus:outline-none mr-1 mb-1" href="https://api.whatsapp.com:/send?text=<?php echo $detail_url; ?>" target="_blank">
                            <i class="fa fa-whatsapp"></i>
                        </a>
                        <!-- linkedin -->
                        <a class="p-2 pr-1 pt-1 pb-1 bg-blue-800 text-white w-8 h-8 rounded-full outline-none focus:outline-none mr-1 mb-1" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $detail_url; ?>&title=&summary=&source=" target="_blank">
                            <i class="fa fa-linkedin"></i>
                        </a>
                    </div>

                    <!-- common start rating end-->
                    <div class="mt-3 px-2 py-2">
                        <div data-controller="tabs" data-tabs-active-tab="-mb-px border-l border-t border-r rounded-t">
                            <ul class="list-reset flex border-b">
                                <li class="-mb-px mr-1" data-target="tabs.tab" data-action="click->tabs#change">
                                    <a class="bg-white rounded-t-xl inline-block py-2 px-4 text-indigo-600 hover:text-indigo-700 font-bold no-underline" href="#">{{__('post_detail.detail')}}</a>
                                </li>
                                <li class="mr-1" data-target="tabs.tab" data-action="click->tabs#change">
                                    <a class="bg-white rounded-t-xl inline-block py-2 px-4 text-indigo-600 hover:text-indigo-700 font-bold no-underline" href="#">{{__('post_detail.review')}}</a>
                                </li>
                            </ul>
                            <div class="hidden rounded-b-xl py-4 px-4 border-l border-b border-r" data-target="tabs.panel">
                                <div>
                                    <div class="flex flex-wrap items-center">
                                        <div class="relative w-full max-w-full flex-grow flex-1">
                                            <h2 class="font-semibold text-base text-gray-800"><i class="fa fa-map-marker fa-lg"></i> {{__('post_detail.location')}}: {{ $final_city_name}}</h2>
                                        </div>

                                        <div class="relative w-full px-4 max-w-full flex-grow flex-1 text-right">
                                            <h3 class="font-semibold text-base text-gray-800">{{__('post_detail.price')}}: <?php echo $currency_symbol[0]; ?>{{ $product[0]->price}}</h3>
                                        </div>
                                    </div>
                                    <div class="h-0 my-4 border border-solid border-gray-200"></div>
                                    <h4 class="text-base text-gray-800">{{ $product[0]->description }}</h4>
                                </div>

                                <div class="h-0  my-4"></div>

                                <?php if (!empty($additional)) { ?>
                                    <div class="mt-2">
                                        <h3 class="font-semibold text-base text-indigo-600"><i class="fa fa-navicon fa-lg"></i> {{__('post_detail.additional details')}}:</h3>
                                        <?php foreach ($additional as $k) { ?>
                                            <div class="flex flex-wrap py-2 px-2 bg-gray-100 mt-2 border-0 rounded items-center">
                                                <?php
                                                if ($k['type'] == "file") {
                                                    $imageurl2 = URL::asset('/storage') . '/' . $k['value'];
                                                ?>
                                                    <div class="flex flex-col lg:flex-row list-none mr-auto"><label>{{$k['label']}}</label></div>
                                                    <div class="flex flex-col lg:flex-row list-none lg:ml-auto"><label><img src="{{ $imageurl2 }}" /></label></div>
                                                <?php } else { ?>
                                                    <div class="flex flex-col lg:flex-row list-none mr-auto"><label>{{$k['label']}}</label></div>
                                                    <div class="flex flex-col lg:flex-row list-none lg:ml-auto"><label>{{$k['value']}}</label></div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <!--review begin-->
                            <div class="hidden rounded-b-xl py-4 px-4 border-l border-b border-r bg-gray-100" data-target="tabs.panel">
                                @foreach($review as $r)
                                @if(($r->approved == "1"))
                                <div class="mb-2 text-sm text-left border-b border-solid border-gray-200 bg-white p-2">
                                    <div class="mb-2 text-left">
                                        <label class="bg-green-700 text-white text-xs px-2 py-1 rounded">{{ $r->ratings }}<i class="fa fa-star-o ml-1" aria-hidden="true"></i></label> {{ $r->name }}
                                    </div>
                                    <div class="mb-2 text-left"><label>{{ $r->comment }}</label></div>
                                    <?php $newform = date('d M Y', strtotime($r->created_at)) ?>
                                    <div class="mb-2 text-xs text-left"><label>{{ $newform }}</label></div>
                                </div>
                                @endif
                                @if(auth()->user())
                                @if($r->approved != "1" && auth()->user()->id == $r->user_id)
                                <div class="mb-2 text-sm text-left border-b border-solid border-gray-200 bg-white p-2">
                                    <div class="mb-2 text-left">
                                        <label class="bg-green-700 text-white text-xs px-2 py-1 rounded">{{ $r->ratings }}<i class="fa fa-star-o ml-1" aria-hidden="true"></i></label> {{ $r->name }}
                                    </div>
                                    <div class="mb-2 text-left"><label>{{ $r->comment }}</label></div>
                                    <?php $newform = date('d M Y', strtotime($r->created_at)) ?>
                                    <div class="mb-2 text-xs text-left"><label>{{ $newform }}</label>
                                        <p class="text-red-600">{{__('post_detail.your comment is waiting for approval')}}.</p>
                                    </div>
                                </div>
                                @endif
                                @endif
                                @endforeach
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
                                    <div class="flex flex-wrap mt-3">
                                        <textarea type="text" maxlength="500" class="review_text px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" rows="4" name="review_text" required="required" placeholder="{{__('post_detail.write your review')}}"></textarea>
                                        <p class="text-xs font-bold item-center text-gray-800">{{__('post_detail.character limit')}}: 500</p>
                                        <input type="hidden" name="review_ratings" id="adrating" />
                                        <input type="hidden" name="post_id" value="{{ $product[0]->id }}" />
                                        <input type="hidden" name="user_id" value="{{Auth::user()->id}}" />
                                        <input type="hidden" name="redirect_url" value="{{ request()->path() }}" />
                                    </div>
                                    <div class="text-right mt-2 items-center">
                                        <?php $starwhite = URL::asset('/images/star1.png'); ?>
                                        <label class="inline-flex items-center mr-2"><input type="hidden" id="ad1_hidden" value="1">
                                            <img src="{{ $starwhite }}" onmouseover="change(this.id);" id="ad1" class="w-4 h-4" /></label>
                                        <label class="inline-flex items-center mr-2"><input type="hidden" id="ad2_hidden" value="2">
                                            <img src="{{ $starwhite }}" onmouseover="change(this.id);" id="ad2" class="w-4 h-4" /></label>
                                        <label class="inline-flex items-center mr-2"><input type="hidden" id="ad3_hidden" value="3">
                                            <img src="{{ $starwhite }}" onmouseover="change(this.id);" id="ad3" class="w-4 h-4" /></label>
                                        <label class="inline-flex items-center mr-2"><input type="hidden" id="ad4_hidden" value="4">
                                            <img src="{{ $starwhite }}" onmouseover="change(this.id);" id="ad4" class="w-4 h-4" /></label>
                                        <label class="inline-flex items-center mr-2"><input type="hidden" id="ad5_hidden" value="5">
                                            <img src="{{ $starwhite }}" onmouseover="change(this.id);" id="ad5" class="w-4 h-4" /></label>
                                        <label class="inline-flex items-center mr-2">
                                            <button type="submit()" class="bg-indigo-500 text-white active:bg-indigo-500 font-bold  text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 ease-linear transition-all duration-150">{{__('post_detail.save')}}</button></label>
                                    </div>
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
                            </div>
                            <!--review end-->
                        </div>
                    </div>
                    <!-- related products - slider start -->
                    <?php if (!empty($related_products)) { ?>
                        <div class="px-2 w-full sm-w-11/12 py-2">
                            <div class="max-w-6xl mx-auto mb-10" wire:ignore>
                                <div class="flex flex-wrap px-0 py-4">
                                    <h4 class="flex-auto font-bold text-2xl">{{__('post_detail.related ads')}} </h4>
                                </div>
                                <div class="related_products slider">
                                    <?php $i = 0; ?>
                                    @foreach($related_products as $d)
                                    <?php
                                    $i++;
                                    $imgUrlfinal = App\Models\TblChat::getPostImgForList($d['id']);
                                    $slug = App\Models\TblPost::get_post_slug($d["slug"]);
                                    $published_on = date("d M Y", strtotime($d["created_at"]));
                                    $fav_style = App\Models\TblSavedPosts::check_fav($d['id']);
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
                                    <div class="m-0 border-0 border-gray-500 rounded">
                                        <div class="border rounded-xl bg-gray-300 m-1 items-center">
                                            <div class="flex flex-wrap items-center">
                                                <div class="relative w-full px-2 max-w-full flex-grow flex-1">
                                                    <h3 class="text-xs text-black">
                                                        <?php if ($ad_type != "") { ?>
                                                            <span class="bg-yellow-500 px-1 py-1 uppercase">
                                                                <?php echo str_replace('_', ' ', strtoupper($ad_type[0])); ?>
                                                            </span><?php } ?>
                                                    </h3>
                                                </div>
                                                <div class="relative w-full px-4 pr-1 max-w-full flex-grow flex-1 text-right">
                                                    
                                                    <button type="button" id="favourate_post_id_{{$d['id']}}" data-fav-post-id="{{ $d['id'] }}" value="{{$d['title']}}" class="text-white leading-8 w-8 h-8 save_favourate rounded-full py-0 px-1 focus:outline-none text-center border-0 <?php echo $fav_style; ?> "><i class="fa fa-heart-o" aria-hidden="true"></i></button>
                                                </div>
                                            </div>
                                            <a href="{{$slug}}">
                                                <img style="max-width: 80%;margin: 15px auto;height:150px;" src="{{$imgUrlfinal}}" />
                                                <div class="overflow-auto rounded-t-none border rounded-xl flex-wrap py-3 px-3 bg-white items-center">
                                                    <p class="text-lg pl-2 float-left pb-1 font-bold"><?php echo mb_strimwidth($d['title'], 0, 16, "..."); ?></p>
                                                    <p class="text-sm pl-2 float-right pb-1"><?php echo $currency_symbol[0]; ?>{{$d["price"]}}</p>
                                                    <p class="pl-1 clear-left text-xs float-left mb-1"><i class="fa fa-map-marker fa-xs">&nbsp;</i>{{$d["city_name"]}}</p>
                                                    <p class="pr-1 text-xs float-right mb-1 text-right">&nbsp;{{$published_on}}</p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <!-- related products - slider end -->
                </div>
                <!--RIGHT PORTION -->
                <?php
                $currentUserId = !empty(auth()->user()->id) ? auth()->user()->id : "";
                $adPostedUserId = $info_user[0]->id;

                ?>

                <div class="lg:block lg:relative lg:w-4/12 md:w-8/12 md:pl-0 ml-auto mr-auto lg:pl-10 pl-0" :class="{ 'show' : mobileMenuOpen , 'hidden' : !mobileMenuOpen , 'h-screen w-9/12 px-0 overflow-y-scroll border rounded-lg border-indigo-500 bg-white z-40 absolute top-0 mt-40' : mobileMenuOpen}" @click.away="mobileMenuOpen = false">
                    @if(auth()->user() && $check_is_paid->count()==0)
                    <?php
                    $urlnew = URL::to('/selectPackage?post=' . $product[0]->id . '');
                    if ($adPostedUserId == $currentUserId) {
                    ?>
                        <div class="ml-2 mb-2 px-2 py-2 border border-solid border-gray-200 rounded">
                            <button class="w-full p-2 bg-yellow-500 text-lg font-bold border rounded"><a href="{{$urlnew}}" class="p-2 bg-yellow-500 text-lg font-bold">{{__('post_detail.sell fast')}}</a></button>
                        </div>
                    <?php } ?>
                    @endif
                    <div class="mb-5 px-2 py-2 border border-solid border-gray-200 rounded-xl shadow-md">
                        <a href="<?php echo URL::to('seller-profile/' . $info_user[0]->id); ?>">
                            <div class="flex flex-wrap items-center my-2">
                                <div class="w-full lg:w-2/12 px-4">
                                    <i class="fa fa-user text-3xl fa-lg"></i>
                                </div>
                                <div class="w-full lg:w-9/12 px-4">
                                    <p class="text-sm">{{__('post_detail.posted by')}}</p>
                                    <h4 class="text-2xl capitalize font-bold">{{ $info_user[0]->name }}</h4>
                                </div>
                                <div class="w-full lg:w-1/12">
                                    <i class="fa fa-angle-right text-5xl text-gray-600" aria-hidden="true"></i>
                                </div>
                            </div>
                        </a>
                        <div class="border-t border-solid border-gray-200"></div>
                        <table class="w-full mt-2 py-2">
                            <tbody>
                                <tr>
                                    <td class="p-2 border-0 border-gray-300 text-center"><i class="fa fa-map-marker fa-lg"></i></td>
                                    <td class="p-2 border-0 border-gray-300">{{__('post_detail.location')}}</td>
                                    <td class="p-2 border-0 border-gray-300">{{ $final_city_name }}</td>
                                </tr>
                                <tr>
                                    <td class="p-2 border-0 border-gray-300 text-center"><i class="fa fa-user fa-lg"></i></td>
                                    <td class="p-2 border-0 border-gray-300">{{__('post_detail.joined')}}</td>
                                    <?php $joinedon = date('d M Y', strtotime($info_user[0]->created_at)); ?>
                                    <td class="p-2 border-0 border-gray-300">{{ $joinedon }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="border-t border-solid border-gray-200"></div>
                        <div id="user_info_closed" class="text-center">
                            <p class="mt-2 w-full text-blue underline font-bold text-xs px-4 py-2 cursor-pointer">{{__('post_detail.show detail')}}</p>
                        </div>
                        <div class="w-full mt-2 py-2 text-center" id="user_info_open" style="display:none;">
                            @if($info_user[0]->show_mobile == 1 )
                            <p class="w-full bg-yellow-500 text-black active:bg-yellow-500 mb-2 font-bold text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 ease-linear transition-all duration-150">
                                <i class="fa fa-phone fa-lg mr-1"></i>{{ $info_user[0]->phone }}
                            </p>
                            @endif
                            <p class="w-full text-black font-bold text-xs px-4 py-2 rounded shadow-md outline-none focus:outline-none mr-1 ease-linear transition-all duration-150"><i class="fa fa-envelope-open-o fa-lg mr-2"></i>{{ $info_user[0]->email }}</p>
                        </div>
                        <?php if ($currentUserId != $adPostedUserId) { ?>
                            <div id="chat_with_seller_closed" class="text-center">
                                <p class="mt-2 w-full text-blue underline font-bold text-xs px-4 py-2 cursor-pointer">{{__('post_detail.chat with seller')}}</p>
                            </div>
                        <?php } ?>
                        <div class="w-full mt-2 py-2 text-center" id="chat_with_seller_open" style="display:none;">
                            <?php
                            $chaturl = URL::to('/chat') . '?to=' . $adPostedUserId . '&p=' . $product[0]->id . '&type=';
                            ?>
                            <a href="<?php echo $chaturl; ?>" class="w-full bg-yellow-500 text-black active:bg-yellow-500 font-bold text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 ease-linear transition-all duration-150">
                                {{__('post_detail.chat with seller')}}
                            </a>
                        </div>
                    </div>
                    <!-- report this ad -->

                    <?php
                    $insight_id = URL::to('/insights/' . $product[0]->id . '');
                    $post_methods = App\Models\TblPostMethod::get_active_post_methods();
                    ?>
                    @if($currentUserId == $adPostedUserId)
                    <div class="ml-2 mb-2 px-2 py-2 border border-solid border-gray-200 rounded">
                        <button class="w-full p-2 bg-yellow-500 text-lg font-bold border rounded"><a href="{{$insight_id}}" target="_blank" class="p-2 bg-yellow-500 text-lg font-bold">{{__('post_detail.insights')}}</a></button>
                    </div>
                    @endif
                    <?php if ($currentUserId != $adPostedUserId) { ?>
                        @if(!empty($post_methods))
                        <?php $check_banner_ads = $post_methods->pluck('name')->toArray(); ?>
                        @if(in_array("exchange", $check_banner_ads))
                        @if($product[0]->exchange_to_buy == 1)
                        <div class="ml-2 mb-2 px-2 py-2 border border-solid border-gray-200 rounded">
                            <button class="w-full p-2 bg-yellow-500 text-lg font-bold border rounded cursor-pointer outline-none focus:outline-none" id="user_exchange_buy">{{__('post_detail.exchange to buy')}}</button>
                        </div>
                        @endif
                        @endif
                        @if(in_array("buynow", $check_banner_ads))
                        @if($product[0]->instant_buy == 1)
                        <div class="ml-2 mb-2 px-2 py-2 border border-solid border-gray-200 rounded">
                            @if(auth()->user())
                            <?php $checkout_url = URL::to('/revieworder/' . $product[0]->slug); ?>
                            <a href="{{$checkout_url}}" class="block text-center w-full p-2 bg-yellow-500 text-lg font-bold border rounded cursor-pointer outline-none focus:outline-none">{{__('post_detail.buy now')}}</a>
                            @else
                            <button class="w-full p-2 bg-yellow-500 text-lg font-bold border rounded cursor-pointer outline-none focus:outline-none" id="user_instant_buy">{{__('post_detail.buy now')}}</button>
                            @endif
                        </div>
                        @endif
                        @endif
                        @endif
                        @if($product[0]->fixed_price == 0)
                        <div class="ml-2 mb-2 px-2 py-2 border border-solid border-gray-200 rounded">
                            <button class="w-full p-2 bg-yellow-500 text-lg font-bold border rounded cursor-pointer outline-none focus:outline-none" id="user_make_offer">{{__('post_detail.make an offer')}}</button>
                        </div>
                        @endif
                    <?php } ?>

                    <?php
                    if (auth()->user()) {
                        if (auth()->user()->id != $product[0]->user_id) {
                    ?>
                            <div class="ml-2 mr-2 mb-2 px-2 py-2 border border-solid border-gray-200 rounded-xl shadow-md">
                                <button class="w-full text-blue underline font-bold text-xs cursor-pointer outline-none focus:outline-none" id="report_ad">{{__('post_detail.report this ad')}}</button>
                            </div>
                    <?php
                        }
                    }
                    ?>
                    <div class="ml-0 mt-2 px-2 py-2 border border-solid border-gray-200 rounded-xl shadow-md">
                        <h2>{{__('post_detail.location')}}</h2>
                        <div>
                            <!--Map begin-->
                            <div id="map" class="w-full" style="height:400px;"></div>
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
                    </div>

                    <!-- recent viewd products - slider start -->
                    <?php if (!empty($recently_viewed_products)) { ?>
                        <div class="px-2 w-full sm-w-11/12 py-2">
                            <div class="max-w-6xl mx-auto mb-10" wire:ignore>
                                <div class="flex flex-wrap px-0 py-4">
                                    <h4 class="flex-auto font-bold text-xl">{{__('post_detail.recently viewed ads')}}</h4>
                                </div>
                                <div class="recently_viewed_products slider">
                                    <?php $i = 0; ?>
                                    @foreach($recently_viewed_products as $d)
                                    <?php
                                    $i++;
                                    $imgUrlfinal = App\Models\TblChat::getPostImgForList($d['id']);
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
                                    <div class="m-0 border-0 border-gray-500 rounded">
                                        <div class="border rounded-xl bg-gray-300 m-1 items-center">
                                            <div class="flex flex-wrap items-center">
                                                <div class="relative w-full px-2 max-w-full flex-grow flex-1">
                                                    <h3 class="text-xs text-black">
                                                        <?php if ($ad_type != "") { ?>
                                                            <span class="bg-yellow-500 px-1 py-1 uppercase">
                                                                <?php echo str_replace('_', ' ', strtoupper($ad_type[0])); ?>
                                                            </span><?php } ?>
                                                    </h3>
                                                </div>
                                                <div class="relative w-full px-4 pr-1 max-w-full flex-grow flex-1 text-right">
                                                    <button type="button" id="favourate_post_id_{{$d['id']}}" data-fav-post-id="{{ $d['id'] }}" value="{{$d['title']}}" class="text-white leading-8 w-8 h-8 save_favourate rounded-full py-0 px-1 focus:outline-none text-center border-0 <?php echo $fav_style; ?> "><i class="fa {{$hearty}}" aria-hidden="true"></i></button>
                                                </div>
                                            </div>
                                            <a href="{{$slug}}">
                                                <img style="max-width: 80%;margin: 15px auto;height:150px;" src="{{$imgUrlfinal}}" />
                                                <div class="overflow-auto rounded-t-none border rounded-xl flex-wrap py-3 px-3 bg-white items-center">
                                                    <p class="text-lg pl-2 float-left pb-1 font-bold"><?php echo mb_strimwidth($d['title'], 0, 16, "..."); ?></p>
                                                    <p class="text-sm pl-2 float-right pb-1"><?php echo $currency_symbol[0]; ?>{{$d["price"]}}</p>
                                                    <p class="pl-1 clear-left text-xs float-left mb-1"><i class="fa fa-map-marker fa-xs">&nbsp;</i>{{$d["city_name"]}}</p>
                                                    <p class="pr-1 text-xs float-right mb-1 text-right">&nbsp;{{$published_on}}</p>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <!-- recent viewd products - slider end -->




                </div>
            </div>
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
            @if(!empty($product[0]->video_url))
            <!--- product video start --->
            <div class="fixed z-10 inset-0 overflow-y-auto" id="view_video_popup" style="display:none">
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
                                        Product Video
                                    </h3>
                                    <div class="mt-2">
                                        <iframe width="420" height="345" src="{{$product[0]->video_url}}"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" id="cancel_video" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                {{__('post_detail.cancel')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!--- product video end --->
            @endif

            @if (!empty($currentUserId) && ($currentUserId != $adPostedUserId))
            <?php
            $visible_posts = App\Models\TblPost::getMyPost($currentUserId);
            ?>
            <!-- exchage to buy start -->
            <div class="fixed z-10 inset-0 overflow-y-auto" id="user_exchange_buy_data" style="display:none">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>
                    <!-- This element is to trick the browser into centering the modal contents. -->
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left h-2/5 overflow-auto shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <div class="flex flex-wrap">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900 w-7/12" id="modal-headline">
                                            {{__('post_detail.exchange to buy')}}
                                        </h3>
                                        <div class="sm:flex">
                                            @if(!empty($visible_posts))
                                            <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm" id="exchange_submit">
                                                {{__('messages.submit')}}
                                            </button>
                                            @endif
                                            <button type="button" id="exchange_cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                                {{__('post_detail.cancel')}}
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        @if(!empty($visible_posts))
                                        <div class="py-2">
                                            <div class="flex flex-wrap mt-2 mb-2">
                                                @foreach($visible_posts as $visible_post)
                                                <?php
                                                $imgUrlfinal = App\Models\TblChat::getPostImgForList($visible_post->id);
                                                $slug = App\Models\TblPost::get_post_slug($visible_post->slug);
                                                ?>
                                                <div class="w-full sm:w-1/4 lg:w-2/6 xl:w-2/6 items-center mb-2">
                                                    <div class="border rounded border-gray-50 p-2">
                                                        <input type="checkbox" name="selected_pid" class="selected_post absolute left-24 w-4 h-4" value="{{$visible_post->id}}" />
                                                        <a href="{{$slug}}">
                                                            <img class="h-24 w-full" src="{{$imgUrlfinal}}" />
                                                            <p class="text-center text-sm">{{$visible_post->title}}</p>
                                                        </a>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- exchage to buy end -->
            @endif

            @if (!empty($currentUserId) && ($currentUserId != $adPostedUserId))
            @if($product[0]->fixed_price == 0)
            <!-- make an offer -->
            <div class="fixed z-10 inset-0 overflow-y-auto" id="user_make_offer_data" style="display:none">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                    </div>
                    <!-- This element is to trick the browser into centering the modal contents. -->
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                    <div class="inline-block align-bottom bg-white rounded-lg text-left h-2/5 overflow-auto shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="">
                                <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                    <div class="flex flex-wrap">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900 w-7/12" id="modal-headline">
                                            {{__('post_detail.make an offer')}}
                                        </h3>
                                    </div>
                                    <div class="mt-2">
                                        <div class="py-2">
                                            <label class="block text-gray-700 text-xl font-bold mb-2 text-center">{{__('post_detail.ask price')}} <span class="text-red-600 font-bold text-md"><?php echo round($product[0]->price); ?> $</span></label>
                                            <label class="block text-gray-700 text-xs font-bold mb-2">{{__('post_detail.your offer price')}}:</label>
                                            <input type="text" id="ask_price" placeholder="{{__('post_detail.enter price')}}" required class="text-sm shadow appearance-none border rounded w-full p-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
                                            <p class="text-xs font-bold item-center text-gray-800"></p>
                                            <textarea id="make_offer_message" placeholder="{{__('post_detail.type message here')}}" rows="3" required class="mt-2 text-sm shadow appearance-none border rounded w-full p-2 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>

                                        </div>
                                    </div>
                                    <div class="sm:flex mt-4">
                                        <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm" id="make_offer_submit">
                                            {{__('messages.submit')}}
                                        </button>
                                        <button type="button" id="make_offer_cancel" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                            {{__('post_detail.cancel')}}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            <!-- make an offer -->
            @endif

        </div>
    </section>
    <!--slider start-->
    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
    <!-- FlexSlider -->
    <script src="{{ URL::to('js/jquery.flexslider.js') }}"></script>
    <script src="{{ URL::to('js/slick.min.js') }}"></script>
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