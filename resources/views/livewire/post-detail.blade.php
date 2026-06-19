<?php

$settings = App\Models\Setting::get_logos();
$slected_currency = !empty($product[0]->currency_id) ? $product[0]->currency_id : $settings['default_currency'];
$currency_symbol = App\Models\TblPost::get_post_currency($slected_currency);
$currency_code = App\Models\TblPost::get_currency_code($slected_currency);

$final_city_name = !empty($product[0]->locality) ? $product[0]->locality : $info_location[0]->name;

$finalcusVal = "";
if (!empty($additional)) {
    foreach ($additional as $cus_field) {
        $cus_label = $cus_field['label'];
        $cus_val = $cus_field['value'];
        $finalcusVal .= $cus_label . " " . $cus_val . ", ";
    }
} else {
    $finalcusVal = $product[0]->title . ", " . $product[0]->price;
}

$meta_key_values = rtrim($finalcusVal, ', ');

$meta_title = $product[0]->title . " | " . $category_name;
$meta_keywords = $meta_key_values;
$meta_description = $product[0]->description;

$dir_rtl =  App\Models\Setting::is_dir_rtl();
$class_dir = ($dir_rtl == "true") ? 'dir=rtl' : "";

$images = explode(',', $product[0]->images);
$jsImages = [];
foreach ($images as $r) {
    $exp_imgURLs = explode('/', $r);
    $imgName = end($exp_imgURLs);
    $is_file = base_path() . '/storage/adpost/predefined/' . $imgName;
    if (is_file($is_file)) {
        $imgUrlEx = '/storage/adpost/predefined/normal/' . $imgName;
        $checkIt = public_path('/storage/adpost/predefined/normal/' . $imgName);
        if (file_exists($checkIt)) {
            $imgUrl = URL::to('/storage/adpost/predefined/normal/' . $imgName);
        } else {
            $imgUrl = URL::to('/storage/adpost/predefined/' . $imgName);
        }
    } else {
        $imgUrl = URL::to('/storage/adpost/predefined/' . $imgName);
    }
    $jsImages[] = $imgUrl;
}

$allFeatures = [];
if (!empty($additional)) {
    foreach ($additional as $k) {
        if ($k['type'] != "file") {
            $label = $k['label'];
            $value = ($k['label'] == "Budget") ? $currency_symbol[0] . $k['value'] : $k['value'];
            $allFeatures[] = ['k' => $label, 'v' => $value];
        }
    }
}
if (!empty($features) && $product[0]->category_id != '64') {
     foreach($features as $key => $value){
        $allFeatures[] = ['k' => $key, 'v' => (!empty($value) ? $value : '-')];
     }
}

$currentUserId = !empty(auth()->user()->id) ? auth()->user()->id : "";
$adPostedUserId = $info_user[0]->id;
$user_currency = App\Models\TblPost::userCurrencyConversion($currentUserId, $product[0]->price, $product[0]->currency_id);
$linethrough = ($slected_currency != $settings['default_currency']) ? 'line-through' : "";

$is_favorited = false;
if (auth()->check()) {
    $is_favorited = App\Models\TblSavedPosts::where('user_id', auth()->id())->where('post_id', $product[0]->id)->exists();
}

$product_condition = '';
$condition_color = 'green';
if (!empty($product[0]->product_condition)) {
    if ($product[0]->product_condition == 1) { $product_condition = 'Like New'; $condition_color = 'green'; }
    elseif ($product[0]->product_condition == 2) { $product_condition = 'Lightly Used'; $condition_color = 'blue'; }
    elseif ($product[0]->product_condition == 3) { $product_condition = 'Heavily Used'; $condition_color = 'orange'; }
}

$posted_ago = \Carbon\Carbon::parse($product[0]->created_at)->diffForHumans();
$views_count = $product[0]->views_count ?? 0;

?>

@section('meta_title', $meta_title)
@section('meta_keywords', $meta_keywords)
@section('meta_description', $meta_description)
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<div class="root-element-div" {{$class_dir}}>

    <main class="pd-main container mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-10">

        {{-- Breadcrumb --}}
        @if(!empty($category_name))
        @php
            $searchedUrl = Session::get('Searchedurl');
            $catSlug = $category_slug ?? '';
            $categoryUrl = $searchedUrl ? $searchedUrl . '&c=' . urlencode($catSlug) : '/' . request()->segment(1) . '?loc=' . urlencode(request('loc', '')) . '&c=' . urlencode($catSlug);
        @endphp
        <nav class="pd-breadcrumb flex items-center text-sm text-gray-500 mb-5 flex-wrap" aria-label="Breadcrumb">
            <a href="{{ url('/') }}" class="hover:text-green-600 transition"><i class="fa fa-home mr-1"></i>Home</a>
            <i class="fa fa-chevron-right text-[10px] mx-2 text-gray-300"></i>
            <a href="{{ $categoryUrl }}" class="hover:text-green-600 transition">{{ $category_name }}</a>
            <i class="fa fa-chevron-right text-[10px] mx-2 text-gray-300"></i>
            <span class="text-gray-800 font-medium truncate max-w-[200px]">{{ $product[0]->title }}</span>
        </nav>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">

            <div class="lg:col-span-7 flex flex-col gap-6">
                {{-- Image Gallery --}}
                <div class="pd-gallery-card relative">
                    @if($is_sold)
                        <div class="absolute top-4 right-4 z-20">
                            <div class="pd-sold-badge"><i class="fa fa-ban mr-1"></i>SOLD</div>
                        </div>
                    @endif
                    
                    @if(empty($jsImages) || empty($jsImages[0]))
                        <div class="pd-no-images">
                            <i class="fa fa-image text-4xl text-gray-300 mb-2"></i>
                            <p class="text-gray-500 font-semibold">{{__('post_detail.no images to preview')}}</p>
                        </div>
                    @else
                        <div class="pd-main-image-wrap" id="main-image-wrap">
                            <div id="image-loader" class="absolute inset-0 skeleton-loader"></div>
                            <img id="main-image" src="" alt="{{ $product[0]->title }}" class="pd-main-image" loading="lazy">
                            
                            {{-- Image navigation arrows --}}
                            <button id="img-prev" class="pd-img-nav pd-img-nav-left" title="Previous"><i class="fa fa-chevron-left"></i></button>
                            <button id="img-next" class="pd-img-nav pd-img-nav-right" title="Next"><i class="fa fa-chevron-right"></i></button>

                            {{-- Image counter --}}
                            <div id="image-counter" class="pd-img-counter"></div>
                            
                            <?php
                            $ad_type = App\Models\TblPost::getAddtype($product[0]->id);
                            $ad_type = ($ad_type == "") ? "" : str_replace('_', ' ', strtoupper($ad_type->ad_type));
                            $ad_type_class = ($ad_type == "") ? "" : "bg-yellow-500";
                            ?>
                            @if($ad_type)
                            <div class="absolute left-0 top-0 z-10">
                                <span class="pd-ad-type-badge {{$ad_type_class}}">{{$ad_type}}</span>
                            </div>
                            @endif

                            @if(!empty($product[0]->video_url))
                            <button class="pd-video-btn" id="view_video" title="Watch Video">
                                <i class="fa fa-play-circle mr-1"></i> Video
                            </button>
                            @endif
                        </div>
                        <div id="thumbnail-gallery" class="pd-thumbnails">
                        </div>
                    @endif
                </div>

                <div class="pd-card pd-tabs-card">
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-6" id="tab-buttons">
                            <button data-tab="features" class="pd-tab-btn active">{{__('post_detail.item details')}}</button>
                            @if(!empty($allFeatures))
                                <button data-tab="description" class="pd-tab-btn">{{__('post_detail.description')}}</button>
                            @endif
                            @if(!empty($features) && $product[0]->category_id == '64')
                               <button data-tab="other-features" class="pd-tab-btn">Other Features</button>
                            @endif
                        </nav>
                    </div>
                    <div class="py-6" id="tab-contents">
                        <div id="description" class="tab-content prose max-w-none text-gray-600">
                            <p>{{ $product[0]->description }}</p>
                        </div>

                        @if(!empty($allFeatures))
                        <div id="features" class="tab-content active">
                            <ul id="features-list-short" class="text-base space-y-0"></ul>
                            <div id="features-list-long-wrapper" class="features-hidden">
                                <ul id="features-list-long" class="text-base space-y-0"></ul>
                            </div>
                            <button id="toggle-features-btn" class="text-green-600 font-semibold mt-4 text-sm hover:text-green-700 transition" style="box-shadow:none;border:none;background:none;cursor:pointer;">Show More <i class="fa fa-chevron-down text-xs ml-1 transition-transform"></i></button>
                        </div>
                        @endif

                        @if(!empty($features) && $product[0]->category_id == '64')
                        <div id="other-features" class="tab-content">
                            <div class="space-y-4">
                                <?php
                                function get_icon_for_title($title) {
                                    $lower_title = strtolower($title);
                                    $icon_map = [
                                        'safety' => 'fa-shield',
                                        'security' => 'fa-lock',
                                        'instrument' => 'fa-dashboard',
                                        'control' => 'fa-sliders',
                                        'comfort' => 'fa-bed',
                                        'convenience' => 'fa-lightbulb-o'
                                    ];
                                    foreach ($icon_map as $key => $icon) {
                                        if (strpos($lower_title, $key) !== false) {
                                            return $icon;
                                        }
                                    }
                                    return 'fa-list-alt';
                                }
                                ?>
                                @foreach($features as $title => $details)
                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        <button class="w-full flex justify-between items-center p-4 text-left font-semibold text-gray-700 bg-gray-50 hover:bg-gray-100 transition" style="box-shadow:none;" onclick="toggleAccordion('accordion-{{ Str::slug($title) }}')">
                                            <div class="flex items-center">
                                                <i class="fa {{ get_icon_for_title($title) }} w-5 text-center mr-3 text-green-600"></i>
                                                <span>{{$title}}</span>
                                            </div>
                                            <i class="fa fa-chevron-down transition-transform"></i>
                                        </button>
                                        <div id="accordion-{{ Str::slug($title) }}" class="accordion-content hidden">
                                            <ul class="px-4 py-2 border-t border-gray-200">
                                                @foreach($details as $key => $value)
                                                    <li class="grid grid-cols-2 gap-4 border-b py-3 last:border-b-0">
                                                        <div class="flex items-center text-gray-600">
                                                            <i class="fa fa-chevron-right w-5 text-center mr-3 text-gray-400"></i>
                                                            <span class="font-medium">{{$key}}</span>
                                                        </div>
                                                        <span class="font-semibold text-gray-800 text-right">{{!empty($value) ? $value : '-'}}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="sticky top-28 flex flex-col gap-6">
                    <div class="pd-card pd-info-card">
                        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 leading-tight">{{ $product[0]->title }}</h1>
                        
                        {{-- Meta row: Ad ID, posted time, views --}}
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 mt-2 mb-4 text-sm text-gray-500">
                            <span>Ad ID: #JR-{{ $product[0]->ff }}</span>
                            <span class="hidden sm:inline">&middot;</span>
                            <span><i class="fa fa-clock-o mr-1"></i>{{ $posted_ago }}</span>
                            @if($views_count > 0)
                            <span class="hidden sm:inline">&middot;</span>
                            <span><i class="fa fa-eye mr-1"></i>{{ number_format($views_count) }} views</span>
                            @endif
                        </div>

                        @if($is_sold)
                            <div class="pd-price-box pd-price-sold">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-semibold text-red-800 mb-1">Status</p>
                                        <p class="text-3xl font-bold text-red-700">SOLD</p>
                                    </div>
                                    <div class="text-red-400 text-5xl opacity-30"><i class="fa fa-times-circle"></i></div>
                                </div>
                            </div>
                        @else
                            <div class="pd-price-box pd-price-available">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="text-xs font-semibold text-green-700 uppercase tracking-wide mb-1">Price</p>
                                        <p class="text-3xl sm:text-4xl font-extrabold text-green-700 {{$linethrough}}"><?php echo $currency_symbol[0]; ?>{{ number_format($product[0]->price) }}</p>
                                        @if($product[0]->currency_id != $settings['default_currency'] )
                                            <span class="font-bold text-lg block text-green-600 mt-0.5"><?php echo $user_currency['convert_sym']; ?> {{ $user_currency['convert_cur']}} <?php echo $user_currency['convert_code']; ?></span>
                                        @endif
                                    </div>
                                    <div class="flex flex-col gap-2 items-end flex-shrink-0">
                                        @if(!empty($product_condition))
                                        <span class="pd-badge pd-badge-{{ $condition_color }}"><i class="fa fa-certificate mr-1 text-[10px]"></i>{{ $product_condition }}</span>
                                        @endif
                                        @if($product[0]->fixed_price == 0)
                                        <span class="pd-badge pd-badge-green">NEGOTIABLE</span>
                                        @endif
                                        @if($product[0]->exchange_to_buy == 1)
                                        <span class="pd-badge pd-badge-indigo">EXCHANGE</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if(!$is_sold)
                        <div class="pd-actions">
                            @if ($currentUserId != $adPostedUserId)
                            <a href="/messages?to={{ $adPostedUserId }}&p={{ $product[0]->id }}" class="pd-btn pd-btn-primary">
                                <i class="fa fa-comments mr-2"></i>{{__('post_detail.chat with seller')}}
                            </a>
                            @if($product[0]->fixed_price == 0 && auth()->user() && auth()->user()->hasRole('User'))
                                <button id="user_make_offer" class="pd-btn pd-btn-offer">
                                    <i class="fa fa-tag mr-2"></i>{{__('post_detail.make an offer')}}
                                </button>
                            @endif
                            @endif

                            @if(auth()->user() && $check_is_paid->count()==0 && auth()->user()->hasRole('User'))
                                <?php $urlnew = URL::to('/selectPackage?post=' . $product[0]->id . ''); ?>
                                @if ($adPostedUserId == $currentUserId)
                                    <a href="{{$urlnew}}" class="pd-btn pd-btn-secondary"><i class="fa fa-rocket mr-2"></i>{{__('post_detail.sell fast')}}</a>
                                @endif
                            @endif
                            
                            @if($currentUserId == $adPostedUserId && auth()->user() && auth()->user()->hasRole('User'))
                                <?php $insight_id = URL::to('/insights/' . $product[0]->id . ''); ?>
                                <a class="pd-btn pd-btn-secondary" href="{{$insight_id}}"><i class="fa fa-bar-chart mr-2"></i>{{__('post_detail.insights')}}</a>
                            @endif
                        </div>
                        @endif
                         
                        <div class="pd-quick-actions">
                            @auth
                                <button id="toggle-favorite-btn" data-post-id="{{ $product[0]->id }}" class="pd-quick-btn">
                                    <i class="fa {{ $is_favorited ? 'fa-heart text-red-500' : 'fa-heart-o' }}"></i>
                                    <span class="button-text">{{ $is_favorited ? 'Saved' : 'Save' }}</span>
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="pd-quick-btn">
                                    <i class="fa fa-heart-o"></i>
                                    <span>Save</span>
                                </a>
                            @endauth
                            
                            <div class="relative inline-block text-left" x-data="{ open: false }">
                                <button @click="open = !open" class="pd-quick-btn">
                                    <i class="fa fa-share-alt"></i><span>Share</span>
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition class="origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                    <div style="padding: 4px 0;" role="menu" aria-orientation="vertical">
                                      <?php $detail_url = App\Models\TblPost::get_post_slug($product[0]->slug); ?>

                                      <a href="https://twitter.com/share?text={{ urlencode($product[0]->title) }}&url={{ $detail_url }}"
                                         target="_blank"
                                         style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 14px; color: #374151; text-decoration: none;"
                                         onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background='transparent'">
                                         <i class="fa-brands fa-twitter" style="width: 16px; color: #1DA1F2;"></i> Twitter
                                      </a>

                                      <a href="http://www.facebook.com/share.php?u={{ $detail_url }}"
                                         target="_blank"
                                         style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 14px; color: #374151; text-decoration: none;"
                                         onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background='transparent'">
                                         <i class="fa-brands fa-facebook-f" style="width: 16px; color: #1877F2;"></i> Facebook
                                      </a>

                                      <a href="https://api.whatsapp.com/send?text={{ urlencode($product[0]->title . ' - ' . $detail_url) }}"
                                         target="_blank"
                                         style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 14px; color: #374151; text-decoration: none;"
                                         onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background='transparent'">
                                         <i class="fa-brands fa-whatsapp" style="width: 16px; color: #25D366;"></i> WhatsApp
                                      </a>

                                      <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ $detail_url }}"
                                         target="_blank"
                                         style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 14px; color: #374151; text-decoration: none;"
                                         onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background='transparent'">
                                         <i class="fa-brands fa-linkedin-in" style="width: 16px; color: #0077B5;"></i> LinkedIn
                                      </a>
                                  </div>

                                </div>
                              </div>

                            @if (auth()->user() && auth()->user()->id != $product[0]->user_id && auth()->user()->hasRole('User'))
                                <button id="report_ad" class="pd-quick-btn"><i class="fa fa-flag"></i><span>Report</span></button>
                            @endif
                        </div>
                    </div>

                    <div class="pd-card pd-seller-card">
                        <h3 class="pd-section-title"><i class="fa fa-user-circle mr-2 text-gray-400"></i>Seller Information</h3>
                        <div class="flex items-center gap-4">
                            <div class="relative flex-shrink-0">
                                @if($info_user[0]->profile_photo_path !="")
                                    <img src="<?php echo URL::to('storage/' . $info_user[0]->profile_photo_path); ?>" alt="{{ $info_user[0]->name }}" class="w-14 h-14 rounded-full object-cover ring-2 ring-gray-100">
                                @else
                                    <img src="<?php echo URL::to('storage/noimage150.png'); ?>" alt="Default Avatar" class="w-14 h-14 rounded-full object-cover ring-2 ring-gray-100">
                                @endif
                                <?php $active_class = ($info_user[0]->current_chat_status == 'online') ? 'bg-green-500' : 'bg-gray-400'; ?>
                                <span class="absolute bottom-0 right-0 {{$active_class}} border-2 border-white rounded-full w-3.5 h-3.5" title="{{ucfirst($info_user[0]->current_chat_status)}}"></span>
                            </div>
                            <div class="min-w-0">
                                <p class="font-bold text-lg text-gray-800 truncate">{{ $info_user[0]->name }}</p>
                                <?php $active_label = ($info_user[0]->current_chat_status == 'online') ? 'Online now' : 'Offline'; ?>
                                <p class="text-xs text-gray-400">{{ $active_label }} &middot; Member since {{ \Carbon\Carbon::parse($info_user[0]->created_at)->isoFormat('MMM YYYY') }}</p>
                                <div class="mt-1" id="review_list_click" data-seller-id="{{$adPostedUserId}}">
                                    <?php
                                    $seller_rate = App\Models\TblSellerReviews::rate_avg($adPostedUserId);
                                    $seller_count = App\Models\TblSellerReviews::revi_count($adPostedUserId);
                                    $seller_rating = round($seller_rate);
                                    ?>
                                    <div class="flex items-center">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fa fa-star{{ $i <= $seller_rating ? '' : '-o' }} text-yellow-400 text-sm"></i>
                                        @endfor
                                        <span class="text-xs text-gray-500 ml-1.5">({{$seller_count}})</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 gap-3">
                            <a href="<?php echo URL::to('seller-profile/' . $info_user[0]->id ); ?>" class="pd-btn pd-btn-outline text-sm"><i class="fa fa-th-large mr-1"></i>All Ads</a>
                            <a href="<?php echo URL::to('seller-profile/' . $info_user[0]->id); ?>" class="pd-btn pd-btn-outline text-sm"><i class="fa fa-user mr-1"></i>Profile</a>
                        </div>
                    </div>

                    <div class="pd-card pd-location-card">
                        <h3 class="pd-section-title"><i class="fa fa-map-marker mr-2 text-gray-400"></i>Location</h3>
                        <p class="text-gray-600 text-sm mb-3">{{ $final_city_name }}</p>
                        <div class="rounded-xl overflow-hidden h-44 border border-gray-200">
                            <div id="map" class="w-full h-full"></div>
                        </div>
                    </div>

                    <div class="pd-safety-card">
                        <h3 class="font-bold text-blue-900 mb-3 flex items-center text-base"><i class="fa fa-shield mr-2 text-blue-500"></i>Safety Tips</h3>
                        <ul class="text-blue-800 text-sm space-y-2">
                            <li class="flex items-start"><i class="fa fa-check-circle text-blue-400 mr-2 mt-0.5 text-xs"></i>Meet in a safe, public place.</li>
                            <li class="flex items-start"><i class="fa fa-check-circle text-blue-400 mr-2 mt-0.5 text-xs"></i>Inspect the item thoroughly before paying.</li>
                            <li class="flex items-start"><i class="fa fa-check-circle text-blue-400 mr-2 mt-0.5 text-xs"></i>Never pay in advance.</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        <div class="mt-10 lg:mt-14 border-t border-gray-200 pt-10 lg:pt-14">
            <h2 class="text-2xl font-bold mb-6 flex items-center"><i class="fa fa-th-large text-gray-400 mr-3"></i>{{__('post_detail.related ads')}}</h2>
            @if(count($related_products) > 0)
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                 @foreach($related_products as $d)
                    <?php $userExistCheck = App\Models\User::where('id', $d['user_id'])->get()->count(); ?>
                    @if ($userExistCheck > 0)
                        <?php echo App\Models\Setting::htmlAdBlock($d['id']); ?>
                    @endif
                @endforeach
            </div>
            @else
                <div class="text-center py-8 text-gray-400">
                    <i class="fa fa-search text-3xl mb-2"></i>
                    <p class="text-base font-medium">No Related Products Found</p>
                </div>
            @endif
        </div>
    </main>

    <div class="pd-mobile-bar lg:hidden">
        <div class="flex justify-between items-center gap-3">
            <div class="flex flex-col min-w-0">
                <span class="text-xs text-gray-500">Price</span>
                @if($is_sold)
                    <span class="font-bold text-lg text-red-600">SOLD</span>
                @else
                    <span class="font-bold text-lg text-green-700">{{$currency_symbol[0]}}{{ number_format($product[0]->price) }}</span>
                @endif
            </div>
            @if (!$is_sold && $currentUserId != $adPostedUserId)
                <a href="/messages?to={{ $adPostedUserId }}&p={{ $product[0]->id }}" class="pd-btn pd-btn-primary flex-1 text-center !py-2.5">
                    <i class="fa fa-comments mr-1"></i>Chat Now
                </a>
            @elseif($is_sold)
                <span class="text-gray-400 font-semibold text-sm">Item Sold</span>
            @else
                <span class="text-gray-400 font-semibold text-sm">Your Ad</span>
            @endif
        </div>
    </div>
</div>

<div class="fixed z-50 inset-0 overflow-y-auto" id="report" style="display:none">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" role="dialog">
            <div class="bg-white px-6 py-4 sm:px-8 sm:py-6">
                <h3 class="block text-xl text-black font-semibold mb-4">{{__('post_detail.item report')}}</h3>
                <div class="py-2">
                    @foreach($report_types as $report)
                    <label class="block text-base font-semibold mb-2"><input type="radio" value="{{$report->id}}" name="re_type" class="re_type mx-2" required>{{$report->name}}</label>
                    @endforeach
                </div>
                <div class="py-2">
                    <label class="block text-base font-semibold mb-2">{{__('post_detail.comment')}}:</label>
                    <textarea id="comment" maxlength="500" placeholder="{{__('post_detail.type here')}}" rows="4" required class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    <p class="text-xs text-gray-500 mt-1">{{__('post_detail.character limit')}}: 500</p>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="btn btn-primary" id="submit">{{__('post_detail.send complaint')}}</button>
                <button type="button" id="cancel" class="btn btn-secondary mt-3 sm:mt-0 sm:mr-3">{{__('post_detail.cancel')}}</button>
            </div>
        </div>
    </div>
</div>

@if (!$is_sold && !empty($currentUserId) && ($currentUserId != $adPostedUserId) && $product[0]->fixed_price == 0)
<div class="fixed z-50 inset-0 overflow-y-auto" id="user_make_offer_data" style="display:none">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" role="dialog">
             <div class="bg-white px-6 py-4 sm:px-8 sm:py-6">
                <h3 class="block text-xl text-black font-semibold mb-4">{{__('post_detail.make an offer')}}</h3>
                 <div class="py-2">
                    <label class="block text-center text-lg mb-4">Asking Price: <span class="text-green-600 font-bold"><?php echo $currency_symbol[0]; ?>{{ number_format($product[0]->price) }}</span></label>
                    <label class="block text-base font-semibold mb-2">{{__('post_detail.your offer price')}}:</label>
                    <input type="number" id="ask_price" placeholder="{{__('post_detail.enter price')}}" required class="w-full border-gray-300 rounded-md shadow-sm" />
                    <textarea id="make_offer_message" placeholder="{{__('post_detail.type message here')}}" rows="3" required class="mt-4 w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="btn btn-primary" id="make_offer_submit">{{__('messages.submit')}}</button>
                <button type="button" id="make_offer_cancel" class="btn btn-secondary mt-3 sm:mt-0 sm:mr-3">{{__('post_detail.cancel')}}</button>
            </div>
        </div>
    </div>
</div>
@endif

@if(!empty($product[0]->video_url))
<?php
$yt_url = $product[0]->video_url;
$video_iframe = '';
if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $yt_url, $match)) {
    $video_id = $match[1];
    $video_iframe = "<iframe class=\"w-full aspect-video\" src=\"https://www.youtube.com/embed/{$video_id}\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>";
}
?>
<div class="fixed z-50 inset-0 overflow-y-auto" id="view_video_popup" style="display:none">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full" role="dialog">
            <div class="bg-white p-4">
                <h3 class="text-xl font-semibold mb-4">Product Video</h3>
                <div class="pt-2">{!! $video_iframe !!}</div>
            </div>
             <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="cancel_video" class="btn btn-secondary">{{__('post_detail.cancel')}}</button>
            </div>
        </div>
    </div>
</div>
@endif

<div id="mini-chat-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="chat-modal-title">
                            Chat with Seller
                        </h3>
                        <div class="mt-4">
                            <div id="chat-messages" class="h-64 overflow-y-auto border border-gray-300 rounded p-4 mb-4 bg-gray-50">
                                <div class="text-center text-gray-500">Starting chat...</div>
                            </div>
                            <div class="flex gap-2">
                                <input type="text" id="chat-input" placeholder="Type your message..." 
                                       class="flex-1 border border-gray-300 rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                                <button id="send-chat-message" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                                    Send
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="close-chat-modal" class="btn btn-secondary">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>                        
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initMapd" async defer></script>
<script>
    function initMapd() {
        var val_lat = '{{ $info_location[0]->latitude }}';
        var val_lag = '{{ $info_location[0]->logitude }}';
        if(val_lat && val_lag) {
            var mapOptions = {
                center: new google.maps.LatLng(parseFloat(val_lat), parseFloat(val_lag)),
                zoom: 15,
                disableDefaultUI: true,
            };
            var map = new google.maps.Map(document.getElementById("map"), mapOptions);
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(parseFloat(val_lat), parseFloat(val_lag)),
                map: map,
                animation: google.maps.Animation.DROP
            });
        }
    }

    function toggleAccordion(contentId) {
        const content = document.getElementById(contentId);
        const icon = content.previousElementSibling.querySelector('i.fa-chevron-down');
        content.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }
    
    function getIconForFeature(featureName) {
        const lowerCaseName = featureName.toLowerCase();
        const iconMap = {
            'brand': 'fa-car',
            'model': 'fa-cogs',
            'year': 'fa-calendar',
            'mileage': 'fa-tachometer',
            'condition': 'fa-star',
            'engine': 'fa-gears',
            'fuel': 'fa-tint',
            'color': 'fa-paint-brush',
            'gearbox': 'fa-sitemap',
            'transmission': 'fa-sitemap',
            'assembly': 'fa-wrench',
            'registration': 'fa-id-card-o',
            'budget': 'fa-money',
            'price': 'fa-money',
            'type': 'fa-tag',
            'doors': 'fa-car',
            'seats': 'fa-users'
        };

        for (const key in iconMap) {
            if (lowerCaseName.includes(key)) {
                return iconMap[key];
            }
        }
        return 'fa-chevron-right';
    }

    document.addEventListener('DOMContentLoaded', () => {
        const images = <?php echo json_encode($jsImages); ?>;
        const allFeatures = <?php echo json_encode($allFeatures); ?>;

        const mainImage = document.getElementById('main-image');
        const imageLoader = document.getElementById('image-loader');
        const thumbnailGallery = document.getElementById('thumbnail-gallery');
        const tabButtons = document.getElementById('tab-buttons');
        const tabContents = document.getElementById('tab-contents');
        const toggleFeaturesBtn = document.getElementById('toggle-features-btn');
        const featuresShort = document.getElementById('features-list-short');
        const featuresLongWrapper = document.getElementById('features-list-long-wrapper');
        const featuresLong = document.getElementById('features-list-long');

        let currentImageIndex = 0;
        const imgCounter = document.getElementById('image-counter');
        const imgPrev = document.getElementById('img-prev');
        const imgNext = document.getElementById('img-next');

        if (images.length > 0) {
            function updateMainImage(index) {
                if (!images[index] || !mainImage) return;
                currentImageIndex = index;
                imageLoader.style.display = 'block';
                mainImage.style.opacity = '0';
                mainImage.src = images[index];
                mainImage.onload = () => {
                    imageLoader.style.display = 'none';
                    mainImage.style.opacity = '1';
                };
                document.querySelectorAll('.pd-thumb').forEach((t, i) => t.classList.toggle('active', i === index));
                if (imgCounter) imgCounter.textContent = (index + 1) + ' / ' + images.length;
            }

            images.forEach((src, index) => {
                const thumbContainer = document.createElement('div');
                thumbContainer.className = 'pd-thumb';
                thumbContainer.innerHTML = `<img src="${src}" alt="Thumbnail ${index+1}" loading="lazy">`;
                thumbContainer.addEventListener('click', () => updateMainImage(index));
                thumbnailGallery.appendChild(thumbContainer);
            });
            updateMainImage(0);

            if (imgPrev) imgPrev.addEventListener('click', () => {
                updateMainImage((currentImageIndex - 1 + images.length) % images.length);
            });
            if (imgNext) imgNext.addEventListener('click', () => {
                updateMainImage((currentImageIndex + 1) % images.length);
            });

            if (images.length <= 1) {
                if (imgPrev) imgPrev.style.display = 'none';
                if (imgNext) imgNext.style.display = 'none';
                if (imgCounter) imgCounter.style.display = 'none';
            }
        }

        if(tabButtons) {
            tabButtons.addEventListener('click', (e) => {
                if (e.target.tagName === 'BUTTON') {
                    const tab = e.target.dataset.tab;
                    if(tabButtons.querySelector('.active')) tabButtons.querySelector('.active').classList.remove('active');
                    e.target.classList.add('active');
                    if(tabContents.querySelector('.active')) tabContents.querySelector('.active').classList.remove('active');
                    if(tabContents.querySelector(`#${tab}`)) tabContents.querySelector(`#${tab}`).classList.add('active');
                }
            });
        }
        
        if(allFeatures.length > 0 && featuresShort) {
            const featuresToShowInitially = 5;
            
            const singleFeatureItemHTML = (item) => {
                const iconClass = getIconForFeature(item.k);
                return `
                    <li class="grid grid-cols-2 gap-4 border-b py-3 last:border-b-0">
                        <div class="flex items-center text-gray-600">
                            <i class="fa ${iconClass} w-5 text-center mr-3 text-gray-400"></i>
                            <span class="font-medium">${item.k}</span>
                        </div>
                        <span class="font-semibold text-gray-800 text-right">${item.v}</span>
                    </li>`;
            };
            
            featuresShort.innerHTML = allFeatures.slice(0, featuresToShowInitially).map(singleFeatureItemHTML).join('');
            
            const longFeatures = allFeatures.slice(featuresToShowInitially);
            if (longFeatures.length > 0) {
                featuresLong.innerHTML = longFeatures.map(singleFeatureItemHTML).join('');
                
                toggleFeaturesBtn.addEventListener('click', () => {
                    const isHidden = featuresLongWrapper.classList.contains('features-hidden');
                    featuresLongWrapper.classList.toggle('features-hidden', !isHidden);
                    featuresLongWrapper.classList.toggle('features-visible', isHidden);
                    toggleFeaturesBtn.querySelector('i').classList.toggle('rotate-180', isHidden);
                    toggleFeaturesBtn.childNodes[0].nodeValue = isHidden ? 'Show Less ' : 'Show More ';
                });
            } else {
                toggleFeaturesBtn.style.display = 'none';
            }
        }
    });

    $(document).ready(function() {
        var isActiveuser = '<?php echo (!empty(auth()->user())) ? "1" : "0"; ?>';
        var currentChatData = null;

        $('#chat-with-seller, #mobile-chat-with-seller').on('click', function(e) {
            e.preventDefault();
            
            if (isActiveuser == "0") {
                window.location.href = "{{ route('login') }}";
                return;
            }

            var userId = $(this).data('user-id');
            var postId = $(this).data('post-id');
            var button = $(this);
            
            button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Starting Chat...');

            $.ajax({
                type: 'POST',
                url: "{{ route('minichat.ajax.start') }}",
                data: { 
                    _token: "{{ csrf_token() }}", 
                    userId: userId, 
                    postId: postId 
                },
                success: function(response) {
                    button.prop('disabled', false).html('<i class="fa fa-comments mr-1"></i> Chat with Seller');
                    
                    if (response.success) {
                        currentChatData = response.chatData;
                        showMiniChatModal(response.chatData);
                        toastr.success('Chat started successfully!');
                    } else {
                        if (response.redirect) {
                            window.location.href = response.redirect;
                        } else {
                            toastr.error(response.message || 'Failed to start chat.');
                        }
                    }
                },
                error: function(xhr) {
                    button.prop('disabled', false).html('<i class="fa fa-comments mr-1"></i> Chat with Seller');
                    
                    if(xhr.status === 401) {
                        toastr.error('Please login to start a chat.');
                        window.location.href = "{{ route('login') }}";
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                }
            });
        });

        function loadChatMessages(chatId) {
            $.ajax({
                type: 'POST',
                url: "{{ route('minichat.ajax.messages') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    chat_id: chatId
                },
                success: function(response) {
                    if (response.success) {
                        displayMessages(response.messages);
                    } else {
                        $('#chat-messages').html('<div class="text-center text-red-500 py-4">Failed to load messages</div>');
                    }
                },
                error: function() {
                    $('#chat-messages').html('<div class="text-center text-red-500 py-4">Error loading messages</div>');
                }
            });
        }

        function sendChatMessage() {
            var message = $('#chat-input').val().trim();
            if (!message) {
                toastr.warning('Please enter a message.');
                return;
            }

            if (!currentChatData) {
                toastr.error('Chat session not found.');
                return;
            }

            var sendButton = $('#send-chat-message');
            sendButton.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

            $.ajax({
                type: 'POST',
                url: "{{ route('minichat.ajax.send') }}",
                data: {
                    _token: "{{ csrf_token() }}",
                    chat_id: currentChatData.chatId,
                    message: message,
                    to_user_id: currentChatData.sellerId
                },
                success: function(response) {
                    sendButton.prop('disabled', false).html('Send');
                    
                    if (response.success) {
                        $('#chat-input').val('');
                        loadChatMessages(currentChatData.chatId);
                    } else {
                        toastr.error(response.message || 'Failed to send message.');
                    }
                },
                error: function(xhr) {
                    sendButton.prop('disabled', false).html('Send');
                    toastr.error('Failed to send message. Please try again.');
                }
            });
        }

        $('#toggle-favorite-btn').on('click', function() {
            if (isActiveuser == "0") {
                window.location.href = "{{ route('login') }}";
                return;
            }

            var button = $(this);
            var postId = button.data('post-id');
            button.prop('disabled', true);

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "{{ route('toggle.favorite') }}",
                data: { _token: "{{ csrf_token() }}", post_id: postId },
                success: function(response) {
                    if (response.status === 'success') {
                        if (response.favorited) {
                            button.find('i').removeClass('fa-heart-o').addClass('fa-heart text-red-500');
                            button.find('.button-text').text('FAVORITED');
                            toastr.success('Ad added to favourites.');
                        } else {
                            button.find('i').removeClass('fa-heart text-red-500').addClass('fa-heart-o');
                            button.find('.button-text').text('Favorite');
                            toastr.info('Ad removed from favourites.');
                        }
                    }
                },
                error: function(xhr) {
                     if(xhr.status === 401){
                         toastr.error('Please login to add to favourites.');
                     } else {
                         toastr.error('An error occurred. Please try again.');
                     }
                },
                complete: function() {
                    button.prop('disabled', false);
                }
            });
        });

        <?php if (!empty($product[0]->video_url)) { ?>
        $('#view_video').on('click', function() {
            $("#view_video_popup").show();
        });
        $('#cancel_video').on('click', function() {
            $("#view_video_popup").hide();
            $('#view_video_popup iframe').attr('src', $('#view_video_popup iframe').attr('src'));
        });
        <?php } ?>

        $('#report_ad').on('click', function() {
            if (isActiveuser == "0") {
                window.location.href = "/login";
            } else {
                $("#report").show();
            }
        });
        $('#submit').on('click', function() {
            var comment = $('#comment').val();
            var retype = $('input[name="re_type"]:checked').val();
            if (comment == "") { toastr.warning('Comment field is required.'); return; }
            if (retype == null) { toastr.warning('Please select a reason for reporting.'); return; }
            $.ajax({
                type: 'POST', dataType: 'json', url: "{{ route('report_ad') }}",
                data: { _token: "{{ csrf_token() }}", comment: comment, retype: retype, post_id: "{{$product[0]->id}}" },
                success: function(data) {
                    $("#report").hide();
                    $('#comment').val('');
                    $('input[name="re_type"]').prop('checked', false);
                    toastr.success(data.message);
                }
            });
        });
        $('#cancel').on('click', function() { $("#report").hide(); });

        $('#user_make_offer').on('click', function() {
            if (isActiveuser != "0") {
                $("#user_make_offer_data").show();
            } else {
                 window.location.href = "/login";
            }
        });
        $('#make_offer_cancel').on('click', function() { $("#user_make_offer_data").hide(); });
        $('#make_offer_submit').on('click', function() {
            var price = $("#ask_price").val();
            var orginal_price = "<?php echo round($product[0]->price); ?>";
            if (!price || parseFloat(price) <= 0 || parseFloat(price) > parseFloat(orginal_price)) {
                toastr.warning('Please enter a valid offer price (cannot be more than the asking price).');
                return false;
            }
             $.ajax({
                type: 'POST', dataType: 'json', url: "{{ URL::to('send_chat') }}",
                data: {
                    _token: "{{ csrf_token() }}", chat_message: price, to: "{{$info_user[0]->id}}",
                    post_id: "{{$product[0]->id}}", image: "", make_offer: "yes"
                },
                success: function(data) {
                    $("#user_make_offer_data").hide();
                    $("#ask_price").val("");
                    $("#make_offer_message").val("");
                    toastr.success("Your offer sent successfully!");
                }
            });
        });
    });
</script>

<style>
    /* ===== Product Detail — Scoped Styles ===== */

    .pd-main { max-width: 1200px; }

    /* Breadcrumb */
    .pd-breadcrumb a { text-decoration: none; }

    /* Cards */
    .pd-card {
        background: #fff;
        border-radius: 1rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px 0 rgba(0,0,0,0.04), 0 1px 2px -1px rgba(0,0,0,0.03);
        padding: 1.25rem;
    }
    .pd-info-card, .pd-tabs-card, .pd-seller-card, .pd-location-card { padding: 1.5rem; }
    @media (min-width: 640px) {
        .pd-info-card, .pd-tabs-card { padding: 1.75rem; }
    }

    .pd-section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    /* Gallery */
    .pd-gallery-card {
        background: #fff;
        border-radius: 1rem;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px 0 rgba(0,0,0,0.04);
        padding: 0.75rem;
        overflow: hidden;
    }
    .pd-main-image-wrap {
        position: relative;
        border-radius: 0.75rem;
        overflow: hidden;
        background: #f8f9fa;
    }
    .pd-main-image {
        width: 100%;
        height: 420px;
        object-fit: contain;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    @media (max-width: 640px) {
        .pd-main-image { height: 280px; }
    }
    .pd-no-images {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 300px;
        background: #f9fafb;
        border-radius: 0.75rem;
    }

    /* Image navigation */
    .pd-img-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: rgba(255,255,255,0.85);
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #374151;
        font-size: 14px;
        opacity: 0;
        transition: opacity 0.2s ease, background 0.2s ease;
        z-index: 5;
    }
    .pd-img-nav:hover { background: #fff; }
    .pd-main-image-wrap:hover .pd-img-nav { opacity: 1; }
    .pd-img-nav-left { left: 12px; }
    .pd-img-nav-right { right: 12px; }

    .pd-img-counter {
        position: absolute;
        bottom: 12px;
        right: 12px;
        background: rgba(0,0,0,0.6);
        color: #fff;
        font-size: 12px;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        z-index: 5;
    }

    .pd-ad-type-badge {
        display: inline-block;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 0 8px 8px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .pd-video-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 10;
        background: #dc2626;
        color: #fff;
        border: none;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        transition: background 0.2s;
    }
    .pd-video-btn:hover { background: #b91c1c; }

    .pd-sold-badge {
        background: #dc2626;
        color: #fff;
        padding: 6px 14px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 14px;
        box-shadow: 0 2px 8px rgba(220,38,38,0.3);
    }

    /* Thumbnails */
    .pd-thumbnails {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 8px;
        margin-top: 10px;
    }
    .pd-thumb {
        aspect-ratio: 1;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.2s ease;
    }
    .pd-thumb:hover { border-color: #d1d5db; transform: scale(1.03); }
    .pd-thumb.active { border-color: #16a34a; box-shadow: 0 0 0 2px rgba(22,163,74,0.2); }
    .pd-thumb img { width: 100%; height: 100%; object-fit: cover; }

    /* Price box */
    .pd-price-box {
        border-radius: 0.75rem;
        padding: 1rem 1.25rem;
        margin-bottom: 1.25rem;
    }
    .pd-price-available { background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border: 1px solid #bbf7d0; }
    .pd-price-sold { background: #fef2f2; border: 1px solid #fecaca; }

    /* Badges */
    .pd-badge {
        display: inline-flex;
        align-items: center;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }
    .pd-badge-green { background: #dcfce7; color: #166534; }
    .pd-badge-blue { background: #dbeafe; color: #1e40af; }
    .pd-badge-orange { background: #ffedd5; color: #9a3412; }
    .pd-badge-indigo { background: #e0e7ff; color: #3730a3; }

    /* Action buttons */
    .pd-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 1.25rem;
    }
    .pd-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem 1.5rem;
        border-radius: 0.625rem;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.2s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
        line-height: 1.4;
    }
    .pd-btn-primary {
        background: linear-gradient(135deg, #16a34a, #15803d);
        color: #fff;
        box-shadow: 0 2px 8px rgba(22,163,74,0.25);
    }
    .pd-btn-primary:hover { background: linear-gradient(135deg, #15803d, #166534); box-shadow: 0 4px 12px rgba(22,163,74,0.3); transform: translateY(-1px); }
    .pd-btn-offer {
        background: #fff;
        color: #16a34a;
        border: 2px solid #16a34a;
    }
    .pd-btn-offer:hover { background: #f0fdf4; }
    .pd-btn-secondary {
        background: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
    }
    .pd-btn-secondary:hover { background: #e5e7eb; }
    .pd-btn-outline {
        background: #fff;
        color: #374151;
        border: 1px solid #d1d5db;
    }
    .pd-btn-outline:hover { background: #f9fafb; border-color: #9ca3af; }

    /* Quick action row (Save, Share, Report) */
    .pd-quick-actions {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        padding-top: 1rem;
        border-top: 1px solid #f3f4f6;
    }
    .pd-quick-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
        color: #6b7280;
        background: transparent;
        border: none;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s;
    }
    .pd-quick-btn:hover { color: #111827; background: #f3f4f6; }

    /* Tabs */
    .pd-tab-btn {
        position: relative;
        padding-bottom: 0.75rem;
        font-weight: 600;
        font-size: 0.95rem;
        color: #9ca3af;
        transition: color 0.2s ease;
        border: none;
        background: none;
        cursor: pointer;
        box-shadow: none !important;
    }
    .pd-tab-btn::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        right: 0;
        height: 3px;
        background: #16a34a;
        border-radius: 3px 3px 0 0;
        transform: scaleX(0);
        transition: transform 0.25s ease;
    }
    .pd-tab-btn:hover { color: #374151; }
    .pd-tab-btn.active { color: #16a34a; }
    .pd-tab-btn.active::after { transform: scaleX(1); }

    .tab-content { display: none; }
    .tab-content.active { display: block; animation: pdFadeIn 0.3s ease; }
    @keyframes pdFadeIn { from { opacity: 0; transform: translateY(6px); } to { opacity: 1; transform: translateY(0); } }

    .features-hidden { max-height: 0; overflow: hidden; transition: max-height 0.5s ease-in-out; }
    .features-visible { max-height: 1000px; transition: max-height 0.5s ease-in-out; }

    .accordion-content { transition: max-height 0.3s ease-out; overflow: hidden; max-height: 0; }
    .accordion-content:not(.hidden) { max-height: 1000px; }
    .rotate-180 { transform: rotate(180deg); }

    /* Safety tips */
    .pd-safety-card {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1px solid #bfdbfe;
        border-radius: 1rem;
        padding: 1.25rem;
    }

    /* Seller card */
    .pd-seller-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }

    /* Mobile sticky bar */
    .pd-mobile-bar {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: #fff;
        border-top: 1px solid #e5e7eb;
        padding: 12px 16px;
        box-shadow: 0 -4px 16px rgba(0,0,0,0.06);
        z-index: 40;
    }

    /* Skeleton loader */
    .skeleton-loader { background-color: #e2e8f0; animation: pdPulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    @keyframes pdPulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }

    /* Legacy compat */
    .card { border-radius: 0.75rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05); }
    .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; transition: all 0.2s; }
    .btn-primary { background-color: #16a34a; color: white; border: 1px solid transparent; }
    .btn-primary:hover { background-color: #15803d; }
    .btn-secondary { background-color: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }
    .btn-secondary:hover { background-color: #e5e7eb; }

    #mini-chat-modal { z-index: 9999; }
    #chat-messages { max-height: 16rem; min-height: 16rem; }

    /* Add bottom padding for mobile sticky bar */
    @media (max-width: 1023px) {
        .pd-main { padding-bottom: 80px; }
    }
</style>