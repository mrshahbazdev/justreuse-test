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

?>

<?php $__env->startSection('meta_title', $meta_title); ?>
<?php $__env->startSection('meta_keywords', $meta_keywords); ?>
<?php $__env->startSection('meta_description', $meta_description); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<div class="root-element-div" <?php echo e($class_dir); ?>>

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

            <div class="lg:col-span-7 flex flex-col gap-8">
                <div class="card p-4 space-y-3">
                    <?php if($is_sold): ?>
                        <div class="absolute top-4 right-4 z-20">
                            <div class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold text-lg shadow-lg">
                                SOLD
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(empty($jsImages) || empty($jsImages[0])): ?>
                        <div class="relative aspect-w-16 aspect-h-12 rounded-xl overflow-hidden bg-gray-100 flex items-center justify-center">
                           <p class="text-gray-500 font-semibold"><?php echo e(__('post_detail.no images to preview')); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="relative aspect-w-16 aspect-h-12 rounded-xl overflow-hidden">
                            <div id="image-loader" class="absolute inset-0 skeleton-loader"></div>
                            <img id="main-image" src="" alt="Main product image" class="w-full h-full object-contain opacity-0 transition-opacity duration-300" loading="lazy">
                            
                            <?php
                            $ad_type = App\Models\TblPost::getAddtype($product[0]->id);
                            $ad_type = ($ad_type == "") ? "" : str_replace('_', ' ', strtoupper($ad_type->ad_type));
                            $ad_type_class = ($ad_type == "") ? "" : "bg-yellow-500";
                            ?>
                            <?php if($ad_type): ?>
                            <div class="absolute left-0 top-0 z-10">
                                <h3 class="text-xs text-white font-semibold relative"><span class="<?php echo e($ad_type_class); ?> px-2 py-1 rounded-tr-lg rounded-br-lg uppercase inline-block"><?php echo e($ad_type); ?></span></h3>
                            </div>
                            <?php endif; ?>

                            <?php if(!empty($product[0]->video_url)): ?>
                            <div class="absolute right-0 top-0 z-10">
                                <button class="inline-block px-2 pt-0 pb-1 rounded-tl-lg rounded-bl-lg text-lg text-white font-normal bg-red-600" id="view_video"><i class="fa fa-youtube-play" aria-hidden="true"></i></button>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div id="thumbnail-gallery" class="grid grid-cols-5 gap-3 mt-2">
                        </div>
                    <?php endif; ?>
                </div>

                <div class="card p-6 lg:p-8">
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-8" id="tab-buttons">
                      		<button data-tab="features" class="tab-btn active" style="box-shadow:none;"><?php echo e(__('post_detail.item details')); ?></button>
                            <?php if(!empty($allFeatures)): ?>
                                <button data-tab="description" class="tab-btn " style="box-shadow:none;"><?php echo e(__('post_detail.description')); ?></button>
                            <?php endif; ?>
                            <?php if(!empty($features) && $product[0]->category_id == '64'): ?>
                               <button data-tab="other-features" class="tab-btn" style="box-shadow:none;">Other Features</button>
                            <?php endif; ?>
                        </nav>
                    </div>
                    <div class="py-6" id="tab-contents">
                        <div id="description" class="tab-content prose max-w-none text-gray-600">
                            <p><?php echo e($product[0]->description); ?></p>
                        </div>

                        <?php if(!empty($allFeatures)): ?>
                        <div id="features" class="tab-content active">
                            <ul id="features-list-short" class="text-base space-y-0"></ul>
                            <div id="features-list-long-wrapper" class="features-hidden">
                                <ul id="features-list-long" class="text-base space-y-0"></ul>
                            </div>
                            <button id="toggle-features-btn" class=" font-semibold mt-4" style="box-shadow:none;">Show More <i class="fa fa-chevron-down text-xs ml-1 transition-transform"></i></button>
                        </div>
                        <?php endif; ?>

                        <?php if(!empty($features) && $product[0]->category_id == '64'): ?>
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
                                <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $title => $details): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                                        <button class="w-full flex justify-between items-center p-4 text-left font-semibold text-gray-700 bg-gray-50 hover:bg-gray-100 transition" style="box-shadow:none;" onclick="toggleAccordion('accordion-<?php echo e(Str::slug($title)); ?>')">
                                            <div class="flex items-center">
                                                <i class="fa <?php echo e(get_icon_for_title($title)); ?> w-5 text-center mr-3 text-green-600"></i>
                                                <span><?php echo e($title); ?></span>
                                            </div>
                                            <i class="fa fa-chevron-down transition-transform"></i>
                                        </button>
                                        <div id="accordion-<?php echo e(Str::slug($title)); ?>" class="accordion-content hidden">
                                            <ul class="px-4 py-2 border-t border-gray-200">
                                                <?php $__currentLoopData = $details; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <li class="grid grid-cols-2 gap-4 border-b py-3 last:border-b-0">
                                                        <div class="flex items-center text-gray-600">
                                                            <i class="fa fa-chevron-right w-5 text-center mr-3 text-gray-400"></i>
                                                            <span class="font-medium"><?php echo e($key); ?></span>
                                                        </div>
                                                        <span class="font-semibold text-gray-800 text-right"><?php echo e(!empty($value) ? $value : '-'); ?></span>
                                                    </li>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="sticky top-28 flex flex-col gap-8">
                    <div class="card p-6">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo e($product[0]->title); ?></h1>
                        <p class="text-gray-500 text-sm mb-6">Ad ID: #JR-<?php echo e($product[0]->ff); ?></p>

                        <?php if($is_sold): ?>
                            <div class="bg-red-50 rounded-xl p-4 mb-6">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-semibold text-red-800">Status</p>
                                        <p class="text-4xl font-bold text-red-700">SOLD</p>
                                    </div>
                                    <div class="text-red-600 text-4xl">
                                        <i class="fa fa-times-circle"></i>
                                    </div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="bg-green-50 rounded-xl p-4 mb-6">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <p class="text-sm font-semibold text-green-800">Price</p>
                                        <p class="text-4xl font-bold text-green-700 <?php echo e($linethrough); ?>"><?php echo $currency_symbol[0]; ?><?php echo e(number_format($product[0]->price)); ?></p>
                                        <?php if($product[0]->currency_id != $settings['default_currency'] ): ?>
                                            <span class="font-bold text-xl block"> <?php echo $user_currency['convert_sym']; ?> <?php echo e($user_currency['convert_cur']); ?> <?php echo $user_currency['convert_code']; ?> </span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="flex flex-col gap-2 items-end">
                                        <?php if($product[0]->fixed_price == 0): ?>
                                        <div class="bg-green-200 text-green-800 text-xs font-bold px-3 py-1 rounded-full">NEGOTIABLE</div>
                                        <?php endif; ?>
                                    
                                        <?php if($product[0]->exchange_to_buy == 1): ?>
                                        <div class="bg-indigo-100 text-indigo-800 text-xs font-bold px-3 py-1 rounded-full">
                                            <?php echo e(('EXCHANGE POSSIBLE')); ?>

                                        </div>
                                        <?php endif; ?>
                                    </div>
                            
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!$is_sold): ?>
                        <div class="flex flex-col gap-3 mb-6">
                            <?php if($currentUserId != $adPostedUserId): ?>
                          <a href="/messages?to=<?php echo e($adPostedUserId); ?>&p=<?php echo e($product[0]->id); ?>"><button  class="btn btn-primary w-full text-lg"><i class="fa fa-comments mr-1"></i><?php echo e(__('post_detail.chat with seller')); ?></button></a>
                              <?php if($product[0]->fixed_price == 0 && auth()->user() && auth()->user()->hasRole('User')): ?>
                                  <button id="user_make_offer" class="btn btn-secondary w-full text-lg">
                                      <i class="fa fa-tag mr-1"></i>  <?php echo e(__('post_detail.make an offer')); ?>

                                  </button>
                              <?php endif; ?>
                          <?php endif; ?>
							
                            <?php if(auth()->user() && $check_is_paid->count()==0 && auth()->user()->hasRole('User')): ?>
                                <?php $urlnew = URL::to('/selectPackage?post=' . $product[0]->id . ''); ?>
                                <?php if($adPostedUserId == $currentUserId): ?>
                                    <a href="<?php echo e($urlnew); ?>" class="btn btn-secondary w-full text-lg"><?php echo e(__('post_detail.sell fast')); ?></a>
                                <?php endif; ?>
                            <?php endif; ?>
                            
                            <?php if($currentUserId == $adPostedUserId && auth()->user() && auth()->user()->hasRole('User')): ?>
                                <?php $insight_id = URL::to('/insights/' . $product[0]->id . ''); ?>
                                <a class="btn btn-secondary w-full text-lg" href="<?php echo e($insight_id); ?>"><?php echo e(__('post_detail.insights')); ?></a>
                            <?php endif; ?>

                        </div>
                        <?php endif; ?>
                         
                         <div class="flex justify-center space-x-6 text-gray-500">
                            <?php if(auth()->guard()->check()): ?>
                                <button id="toggle-favorite-btn" data-post-id="<?php echo e($product[0]->id); ?>" class="transition flex items-center gap-2" style="box-shadow:none;">
                                    <i class="fa <?php echo e($is_favorited ? 'fa-heart text-red-500' : 'fa-heart-o text-gray-500 hover:text-red-500'); ?>"></i>
                                    <span class="button-text"><?php echo e($is_favorited ? 'FAVORITED' : 'Favorite'); ?></span>
                                </button>
                            <?php else: ?>
                                <a href="<?php echo e(route('login')); ?>" class="transition flex items-center gap-2 text-gray-500 hover:text-red-500" style="box-shadow:none;">
                                    <i class="fa fa-heart-o"></i>
                                    <span>Favorite</span>
                                </a>
                            <?php endif; ?>
                            
                             <div class="relative inline-block text-left" x-data="{ open: false }">
                                <button @click="open = !open" class="hover:text-green-600 transition flex items-center gap-2" style="box-shadow:none;">
                                    <i class="fa fa-share-alt"></i>Share
                                </button>
                                <div x-show="open" @click.away="open = false" x-transition class="origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                    <div style="padding: 4px 0;" role="menu" aria-orientation="vertical">
                                      <?php $detail_url = App\Models\TblPost::get_post_slug($product[0]->slug); ?>

                                      <a href="https://twitter.com/share?text=<?php echo e(urlencode($product[0]->title)); ?>&url=<?php echo e($detail_url); ?>"
                                         target="_blank"
                                         style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 14px; color: #374151; text-decoration: none;"
                                         onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background='transparent'">
                                         <i class="fa-brands fa-twitter" style="width: 16px; color: #1DA1F2;"></i> Twitter
                                      </a>

                                      <a href="http://www.facebook.com/share.php?u=<?php echo e($detail_url); ?>"
                                         target="_blank"
                                         style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 14px; color: #374151; text-decoration: none;"
                                         onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background='transparent'">
                                         <i class="fa-brands fa-facebook-f" style="width: 16px; color: #1877F2;"></i> Facebook
                                      </a>

                                      <a href="https://api.whatsapp.com/send?text=<?php echo e(urlencode($product[0]->title . ' - ' . $detail_url)); ?>"
                                         target="_blank"
                                         style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 14px; color: #374151; text-decoration: none;"
                                         onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background='transparent'">
                                         <i class="fa-brands fa-whatsapp" style="width: 16px; color: #25D366;"></i> WhatsApp
                                      </a>

                                      <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo e($detail_url); ?>"
                                         target="_blank"
                                         style="display: flex; align-items: center; gap: 8px; padding: 8px 16px; font-size: 14px; color: #374151; text-decoration: none;"
                                         onmouseover="this.style.background='#F3F4F6'" onmouseout="this.style.background='transparent'">
                                         <i class="fa-brands fa-linkedin-in" style="width: 16px; color: #0077B5;"></i> LinkedIn
                                      </a>
                                  </div>

                                </div>
                              </div>

                            <?php if(auth()->user() && auth()->user()->id != $product[0]->user_id && auth()->user()->hasRole('User')): ?>
                                <button id="report_ad" class="hover:text-yellow-600 transition flex items-center gap-2" style="box-shadow:none;"><i class="fa fa-flag"></i>Report</button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="card p-6">
                        <h3 class="text-xl font-bold mb-4 font-poppins">Seller Information</h3>
                        <div class="flex items-center gap-4">
                            <div class="relative">
                                <?php if($info_user[0]->profile_photo_path !=""): ?>
                                    <img src="<?php echo URL::to('storage/' . $info_user[0]->profile_photo_path); ?>" alt="<?php echo e($info_user[0]->name); ?>" class="w-16 h-16 rounded-full object-cover">
                                <?php else: ?>
                                    <img src="<?php echo URL::to('storage/noimage150.png'); ?>" alt="Default Avatar" class="w-16 h-16 rounded-full object-cover">
                                <?php endif; ?>
                                <?php $active_class = ($info_user[0]->current_chat_status == 'online') ? 'bg-green-500' : 'bg-gray-400'; ?>
                                <span class="absolute bottom-0 right-0 <?php echo e($active_class); ?> border-2 border-white rounded-full w-4 h-4" title="<?php echo e(ucfirst($info_user[0]->current_chat_status)); ?>"></span>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="font-bold text-lg text-gray-800"><?php echo e($info_user[0]->name); ?></p>
                                </div>
                                <p class="text-sm text-gray-500">Member since <?php echo e(\Carbon\Carbon::parse($info_user[0]->created_at)->isoFormat('MMM YYYY')); ?></p>
                                 <div class="mt-1" id="review_list_click" data-seller-id="<?php echo e($adPostedUserId); ?>">
                                    <?php
                                    $seller_rate = App\Models\TblSellerReviews::rate_avg($adPostedUserId);
                                    $seller_count = App\Models\TblSellerReviews::revi_count($adPostedUserId);
                                    $seller_rating = round($seller_rate);
                                    ?>
                                    <div class="flex items-center text-yellow-400">
                                        <?php for($i = 1; $i <= 5; $i++): ?>
                                            <i class="fa fa-star<?php echo e($i <= $seller_rating ? '' : '-o'); ?>"></i>
                                        <?php endfor; ?>
                                        <span class="text-xs text-gray-500 ml-2">(<?php echo e($seller_count); ?> Reviews)</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 gap-3">
                            <a href="<?php echo URL::to('seller-profile/' . $info_user[0]->id ); ?>" class="btn btn-secondary w-full text-sm !py-2.5">All Ads</a>
                            <a href="<?php echo URL::to('seller-profile/' . $info_user[0]->id); ?>" class="btn btn-secondary w-full text-sm !py-2.5">Profile</a>
                        </div>
                    </div>

                    <div class="card p-6">
                        <h3 class="text-xl font-bold mb-2 font-poppins">Location</h3>
                        <p class="text-gray-600 mb-4"><i class="fa fa-map-marker-alt mr-2 text-gray-400"></i><?php echo e($final_city_name); ?></p>
                        <div class="rounded-xl overflow-hidden h-48 border">
                            <div id="map" class="w-full h-full"></div>
                        </div>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-5">
                        <h3 class="font-bold text-blue-900 mb-2 flex items-center gap-2 text-lg"><i class="fa fa-shield"></i>Safety Tips</h3>
                        <ul class="text-blue-800 text-sm space-y-1.5 pl-1">
                            <li><i class="fa fa-check-circle-o text-xs mr-2"></i>Meet in a safe, public place.</li>
                            <li><i class="fa fa-check-circle-o text-xs mr-2"></i>Inspect the item thoroughly before paying.</li>
                            <li><i class="fa fa-check-circle-o text-xs mr-2"></i>Never pay in advance.</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>

        <div class="mt-12 lg:mt-16 border-t pt-12 lg:pt-16">
            <h2 class="text-3xl font-bold mb-6"><?php echo e(__('post_detail.related ads')); ?></h2>
            <?php if(count($related_products) > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                 <?php $__currentLoopData = $related_products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $userExistCheck = App\Models\User::where('id', $d['user_id'])->get()->count(); ?>
                    <?php if($userExistCheck > 0): ?>
                        <?php echo App\Models\Setting::htmlAdBlock($d['id']); ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php else: ?>
                <p class='text-lg font-semibold my-5'>No Related Products Found</p>
            <?php endif; ?>
        </div>
    </main>

   <!-- <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-3 shadow-[0_-2px_10px_rgba(0,0,0,0.05)] z-50">
        <div class="flex justify-between items-center gap-3">
            <div class="flex flex-col">
                <span class="text-sm text-gray-500">Price</span>
                <?php if($is_sold): ?>
                    <span class="font-bold text-xl text-red-600">SOLD</span>
                <?php else: ?>
                    <span class="font-bold text-xl text-green-600"><?php echo e($currency_symbol[0]); ?><?php echo e(number_format($product[0]->price)); ?></span>
                <?php endif; ?>
            </div>
            <?php if(!$is_sold && $currentUserId != $adPostedUserId): ?>
                 <button id="mobile-chat-with-seller" 
                    data-user-id="<?php echo e($adPostedUserId); ?>" 
                    data-post-id="<?php echo e($product[0]->id); ?>"
                    class="btn btn-primary flex-1">Chat Now</button>
            <?php elseif($is_sold): ?>
                <span class="text-gray-500 font-semibold">Item Sold</span>
            <?php else: ?>
                <span class="text-gray-500 font-semibold">This is your Ad</span>
            <?php endif; ?>
        </div>
    </div> !-->
</div>

<div class="fixed z-50 inset-0 overflow-y-auto" id="report" style="display:none">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" role="dialog">
            <div class="bg-white px-6 py-4 sm:px-8 sm:py-6">
                <h3 class="block text-xl text-black font-semibold mb-4"><?php echo e(__('post_detail.item report')); ?></h3>
                <div class="py-2">
                    <?php $__currentLoopData = $report_types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="block text-base font-semibold mb-2"><input type="radio" value="<?php echo e($report->id); ?>" name="re_type" class="re_type mx-2" required><?php echo e($report->name); ?></label>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <div class="py-2">
                    <label class="block text-base font-semibold mb-2"><?php echo e(__('post_detail.comment')); ?>:</label>
                    <textarea id="comment" maxlength="500" placeholder="<?php echo e(__('post_detail.type here')); ?>" rows="4" required class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                    <p class="text-xs text-gray-500 mt-1"><?php echo e(__('post_detail.character limit')); ?>: 500</p>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="btn btn-primary" id="submit"><?php echo e(__('post_detail.send complaint')); ?></button>
                <button type="button" id="cancel" class="btn btn-secondary mt-3 sm:mt-0 sm:mr-3"><?php echo e(__('post_detail.cancel')); ?></button>
            </div>
        </div>
    </div>
</div>

<?php if(!$is_sold && !empty($currentUserId) && ($currentUserId != $adPostedUserId) && $product[0]->fixed_price == 0): ?>
<div class="fixed z-50 inset-0 overflow-y-auto" id="user_make_offer_data" style="display:none">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">​</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" role="dialog">
             <div class="bg-white px-6 py-4 sm:px-8 sm:py-6">
                <h3 class="block text-xl text-black font-semibold mb-4"><?php echo e(__('post_detail.make an offer')); ?></h3>
                 <div class="py-2">
                    <label class="block text-center text-lg mb-4">Asking Price: <span class="text-green-600 font-bold"><?php echo $currency_symbol[0]; ?><?php echo e(number_format($product[0]->price)); ?></span></label>
                    <label class="block text-base font-semibold mb-2"><?php echo e(__('post_detail.your offer price')); ?>:</label>
                    <input type="number" id="ask_price" placeholder="<?php echo e(__('post_detail.enter price')); ?>" required class="w-full border-gray-300 rounded-md shadow-sm" />
                    <textarea id="make_offer_message" placeholder="<?php echo e(__('post_detail.type message here')); ?>" rows="3" required class="mt-4 w-full border-gray-300 rounded-md shadow-sm"></textarea>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="btn btn-primary" id="make_offer_submit"><?php echo e(__('messages.submit')); ?></button>
                <button type="button" id="make_offer_cancel" class="btn btn-secondary mt-3 sm:mt-0 sm:mr-3"><?php echo e(__('post_detail.cancel')); ?></button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php if(!empty($product[0]->video_url)): ?>
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
                <div class="pt-2"><?php echo $video_iframe; ?></div>
            </div>
             <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" id="cancel_video" class="btn btn-secondary"><?php echo e(__('post_detail.cancel')); ?></button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

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

<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo e(config('services.google.maps_api_key')); ?>&libraries=places&callback=initMapd" async defer></script>
<script>
    function initMapd() {
        var val_lat = '<?php echo e($info_location[0]->latitude); ?>';
        var val_lag = '<?php echo e($info_location[0]->logitude); ?>';
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

        if (images.length > 0) {
            function updateMainImage(index) {
                if (!images[index] || !mainImage) return;
                imageLoader.style.display = 'block';
                mainImage.style.opacity = '0';
                mainImage.src = images[index];
                mainImage.onload = () => {
                    imageLoader.style.display = 'none';
                    mainImage.style.opacity = '1';
                };
                document.querySelectorAll('.thumbnail-container').forEach((t, i) => t.classList.toggle('active', i === index));
            }

            images.forEach((src, index) => {
                const thumbContainer = document.createElement('div');
                thumbContainer.className = 'thumbnail-container border-2 rounded-xl transition-all cursor-pointer aspect-w-1 aspect-h-1 overflow-hidden';
                thumbContainer.innerHTML = `<img src="${src}" alt="Thumbnail ${index+1}" class="w-full h-full object-cover" loading="lazy">`;
                thumbContainer.addEventListener('click', () => updateMainImage(index));
                thumbnailGallery.appendChild(thumbContainer);
            });
            updateMainImage(0);
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
                window.location.href = "<?php echo e(route('login')); ?>";
                return;
            }

            var userId = $(this).data('user-id');
            var postId = $(this).data('post-id');
            var button = $(this);
            
            button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Starting Chat...');

            $.ajax({
                type: 'POST',
                url: "<?php echo e(route('minichat.ajax.start')); ?>",
                data: { 
                    _token: "<?php echo e(csrf_token()); ?>", 
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
                        window.location.href = "<?php echo e(route('login')); ?>";
                    } else {
                        toastr.error('An error occurred. Please try again.');
                    }
                }
            });
        });

        function loadChatMessages(chatId) {
            $.ajax({
                type: 'POST',
                url: "<?php echo e(route('minichat.ajax.messages')); ?>",
                data: {
                    _token: "<?php echo e(csrf_token()); ?>",
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
                url: "<?php echo e(route('minichat.ajax.send')); ?>",
                data: {
                    _token: "<?php echo e(csrf_token()); ?>",
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
                window.location.href = "<?php echo e(route('login')); ?>";
                return;
            }

            var button = $(this);
            var postId = button.data('post-id');
            button.prop('disabled', true);

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "<?php echo e(route('toggle.favorite')); ?>",
                data: { _token: "<?php echo e(csrf_token()); ?>", post_id: postId },
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
                type: 'POST', dataType: 'json', url: "<?php echo e(route('report_ad')); ?>",
                data: { _token: "<?php echo e(csrf_token()); ?>", comment: comment, retype: retype, post_id: "<?php echo e($product[0]->id); ?>" },
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
                type: 'POST', dataType: 'json', url: "<?php echo e(URL::to('send_chat')); ?>",
                data: {
                    _token: "<?php echo e(csrf_token()); ?>", chat_message: price, to: "<?php echo e($info_user[0]->id); ?>",
                    post_id: "<?php echo e($product[0]->id); ?>", image: "", make_offer: "yes"
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
  .space-x-6 > :not([hidden]) ~ :not([hidden]) {
    margin-left: 1.5rem;
  }
  .gap-2 {
    padding: 10px  !important;
   }
    
    .skeleton-loader { background-color: #e2e8f0; animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    @keyframes  pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }

    #main-image {
        width: 100%;
        height: 500px;
    }
    
    #thumbnail-gallery .thumbnail-container {
        aspect-ratio: 1 / 1;
        width: 100%;
        overflow: hidden;
    }

    #thumbnail-gallery .thumbnail-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .tab-content { display: none; }
    .tab-content.active { display: block; }
    
    .features-hidden { max-height: 0; overflow: hidden; transition: max-height 0.5s ease-in-out; }
    .features-visible { max-height: 1000px; transition: max-height 0.5s ease-in-out; }

    .accordion-content { transition: max-height 0.3s ease-out; overflow: hidden; max-height: 0; }
    .accordion-content:not(.hidden) { max-height: 1000px; }
    
    .rotate-180 { transform: rotate(180deg); }

    .card { border-radius: 0.75rem; border: 1px solid #e5e7eb; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05); }
    .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; transition: all 0.2s; }
    .btn-primary { background-color: #16a34a; color: white; border: 1px solid transparent; }
    .btn-primary:hover { background-color: #15803d; }
    .btn-secondary { background-color: #f29c11; color: #374151; border: 1px solid #d1d5db; }
    .btn-secondary:hover { background-color: #f9fafb; }

    #mini-chat-modal {
        z-index: 9999;
    }
    
    #chat-messages {
        max-height: 16rem;
        min-height: 16rem;
    }
</style><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/post-detail.blade.php ENDPATH**/ ?>