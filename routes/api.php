<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan; // Artisan import zaroori hai cache clear k liye
use App\Http\Livewire\ApiComponent;

// 🟢 NEW CONTROLLERS IMPORT (Jo humne banaye hain)
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CustomFieldController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('api')->group(function () {

    // =============================================================
    // ADD POST ROUTES (REST API)
    // =============================================================

    // Public routes (no auth required)
    Route::get('/post/create-data', [PostController::class, 'getCreateData']);
    Route::get('/categories/sub/{id}', [PostController::class, 'getSubCategories']);
    Route::get('/get-custom-fields-api/{id}', [CustomFieldController::class, 'getCustomFields']);

    // Protected routes (require auth token)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/post/upload-image', [PostController::class, 'uploadImage']);
        Route::post('/post/store', [PostController::class, 'store']);
    });


    // --- EXISTING ROUTES BELOW ---

    Route::get('validate_apple_token', [ApiComponent::class, 'validate_apple_token']);

    Route::get('/allcache-clearance', function () {
        Artisan::call('cache:clear');
        Artisan::call('config:cache');
        Artisan::call('config:clear');
        Artisan::call('view:clear');
        return 'Application cache cleared.';
    });

    Route::post('login', [ApiComponent::class, 'login']);
    Route::post('register', [ApiComponent::class, 'register']);
    Route::get('getCategories', [ApiComponent::class, 'getCategories']);
    Route::get('getSubcategories', [ApiComponent::class, 'getSubcategories']);
    Route::get('post_detail/{any}', [ApiComponent::class, 'post_detail']);
    Route::get('post_detail_get/{any}', [ApiComponent::class, 'post_detail_get']);
    Route::get('posts_detail/{any}', [ApiComponent::class, 'posts_detail']);
    Route::get('get_single_post_info/{any}', [ApiComponent::class, 'get_single_post_info']);
    Route::get('home', [ApiComponent::class, 'home']);
    Route::get('searchlist', [ApiComponent::class, 'searchlist']);
    Route::post('forgot_password', [ApiComponent::class, 'forgot_password']);
    Route::get('blacklist', [ApiComponent::class, 'blacklist_words']);
    Route::get('pages/{any}', [ApiComponent::class, 'static_pages']);
    Route::get('report_types_list', [ApiComponent::class, 'report_types']);
    Route::get('user_report_types', [ApiComponent::class, 'user_report_types']);
    Route::get('mypost', [ApiComponent::class, 'mypost']);
    Route::post('update_to_fav', [ApiComponent::class, 'update_to_fav']);
    Route::post('likes-dislikes', [ApiComponent::class, 'likes_and_dislike']);
    Route::post('my_fav_posts', [ApiComponent::class, 'my_fav_posts']);
    Route::get('get_profile', [ApiComponent::class, 'get_profile']);
    Route::get('get-user-details', [ApiComponent::class, 'get_user_details']);
    Route::post('update_profile', [ApiComponent::class, 'update_profile']);

    // Old Add Post Route (Abhi rehne den agar purana app use kar raha hai)
    Route::post('add_post', [ApiComponent::class, 'add_post']);

    Route::post('edit_post/{any}', [ApiComponent::class, 'edit_post']);
    Route::get('delete_post/{any}', [ApiComponent::class, 'delete_post']);
    Route::post('write_review/{any}', [ApiComponent::class, 'write_review']);
    Route::post('change_password', [ApiComponent::class, 'change_password']);
    Route::post('post_report', [ApiComponent::class, 'post_report']);
    Route::post('report_the_user', [ApiComponent::class, 'post_user_report']);
    Route::post('welcome_chat', [ApiComponent::class, 'welcome_chat']);
    Route::post('send_chat', [ApiComponent::class, 'send_chat']);
    Route::get('mychat_list', [ApiComponent::class, 'mychat_list']);
    Route::get('chat_detail', [ApiComponent::class, 'chat_detail']);
    Route::get('chat_detail_refresh', [ApiComponent::class, 'chat_detail_refresh']);
    Route::get('default_currency', [ApiComponent::class, 'get_default_currency']);
    Route::get('get_post_currency/{any}', [ApiComponent::class, 'post_currency']);
    Route::post('remove_post_img', [ApiComponent::class, 'remove_post_img']);
    Route::get('check_review', [ApiComponent::class, 'check_review']);
    Route::post('get_nearby_post_locations', [ApiComponent::class, 'get_nearby_post_locations']);
    Route::get('choose_multiple_package', [ApiComponent::class, 'choose_multiple_package']);
    Route::post('delete_chat', [ApiComponent::class, 'delete_chat']);
    Route::post('block_user', [ApiComponent::class, 'block_user']);
    Route::get('get_custome_fields', [ApiComponent::class, 'get_custome_fields']);
    Route::post('pay_for_bulk_package', [ApiComponent::class, 'pay_for_bulk_package']);
    Route::get('my_bulk_pakages', [ApiComponent::class, 'my_bulk_pakages']);
    Route::get('mybulk_package_assign_ads', [ApiComponent::class, 'mybulk_package_addpost']);
    Route::post('mybulk_package_update_ads', [ApiComponent::class, 'mybulk_package_savepost']);
    Route::get('mybulk_ads_view_assigned_ads/{any}', [ApiComponent::class, 'mybulk_ads_view_assignedads']);
    Route::post('sell_fast', [ApiComponent::class, 'choose_single_package']);
    Route::post('is_valid_coupon', [ApiComponent::class, 'is_valid_coupon']);
    Route::post('pay_for_single_package', [ApiComponent::class, 'pay_for_single_package']);
    Route::get('seller_profile/{any}', [ApiComponent::class, 'seller_profile']);
    Route::post('add_to_follower', [ApiComponent::class, 'add_to_follower']);
    Route::get('my_friends', [ApiComponent::class, 'my_friends']);
    Route::get('seller_friends/{any}', [ApiComponent::class, 'seller_friends']);
    Route::post('invite_friends', [ApiComponent::class, 'invite_friends']);
    Route::get('notification_list', [ApiComponent::class, 'notification_list']);
    Route::post('fcmid_update', [ApiComponent::class, 'fcmid_update']);
    Route::get('chat_read_count', [ApiComponent::class, 'chat_read_count']);
    Route::post('notification_read_status', [ApiComponent::class, 'notification_read_status']);
    Route::post('google_login', [ApiComponent::class, 'google_login']);
    Route::post('facebook_login', [ApiComponent::class, 'facebook_login']);
    Route::post('blocked', [ApiComponent::class, 'user_blocked']);
    Route::post('republish_post', [ApiComponent::class, 'republish_post']);
    Route::get('get_package_list', [ApiComponent::class, 'get_package_list']);
    Route::post('contact_us', [ApiComponent::class, 'contact_us']);
    Route::get('all_package_list', [ApiComponent::class, 'all_package_list']);
    Route::get('check_sms_package', [ApiComponent::class, 'check_sms_package']);
    Route::post('send_otp', [ApiComponent::class, 'send_otp']);
    Route::post('verify_otp', [ApiComponent::class, 'verify_otp']);
    Route::get('get_single_post_packages', [ApiComponent::class, 'get_single_post_packages']);
    Route::get('get_base_url', [ApiComponent::class, 'get_base_url']);
    Route::post('fb_connect', [ApiComponent::class, 'fb_connect']);
    Route::post('fb_disconnect', [ApiComponent::class, 'fb_disconnect']);
    Route::get('home_banners', [ApiComponent::class, 'home_banners']);
    Route::post('update_sold_status', [ApiComponent::class, 'update_sold_status']);
    Route::get('get_available_post_to_exchange', [ApiComponent::class, 'get_available_to_exchange']);
    Route::post('create_exchange', [ApiComponent::class, 'create_exchange']);
    Route::post('update_exchange_status', [ApiComponent::class, 'update_exchange_status']);
    Route::get('my_exchanges_list', [ApiComponent::class, 'my_exchanges_list']);
    Route::get('exchange_detail/{any}', [ApiComponent::class, 'exchange_detail_page']);
    Route::post('add_banner_advertisement', [ApiComponent::class, 'add_banner_advertisement']);
    Route::post('get_banner_price', [ApiComponent::class, 'get_banner_price']);
    Route::get('get_available_payment_methods', [ApiComponent::class, 'get_available_payment_methods']);
    Route::get('my_banner_ads_histroy', [ApiComponent::class, 'my_banner_ads_histroy']);
    Route::get('banner_ads_detail/{any}', [ApiComponent::class, 'banner_ads_detail']);
    Route::post('mobile_verification', [ApiComponent::class, 'send_otp_for_mobile_verification']);
    Route::post('mobile_verification_success', [ApiComponent::class, 'mobile_verification_success']);
    Route::post('add_address', [ApiComponent::class, 'add_address']);
    Route::post('edit_address/{any}', [ApiComponent::class, 'edit_address']);
    Route::get('my_address_list', [ApiComponent::class, 'my_address_list']);
    Route::post('set_default_address', [ApiComponent::class, 'set_default_address']);
    Route::get('get_address/{any}', [ApiComponent::class, 'get_address']);
    Route::post('delete_address', [ApiComponent::class, 'delete_address']);
    Route::post('move_to_checkout', [ApiComponent::class, 'move_to_checkout']);
    Route::post('change_address', [ApiComponent::class, 'change_address']);
    Route::get('my_buynow_orders', [ApiComponent::class, 'my_buynow_orders']);
    Route::get('buynow_order_detail/{any}', [ApiComponent::class, 'buynow_order_detail']);
    Route::get('update_order_status/{any}', [ApiComponent::class, 'update_order_status']);
    Route::post('update_courier_info', [ApiComponent::class, 'update_courier_info']);
    Route::get('get_courier_info/{any}', [ApiComponent::class, 'get_courier_info']);
    Route::post('store_insight', [ApiComponent::class, 'store_insight']);
    Route::get('insight-list/{any}', [ApiComponent::class, 'insight_list']);
    Route::get('currency_list', [ApiComponent::class, 'currency_list']);
    Route::post('product_condition_is_enable/{any}', [ApiComponent::class, 'check_product_condition_is_active']);
    Route::get('get_active_ads_methods/{any}', [ApiComponent::class, 'get_active_ads_methods']);
    Route::get('update_online_status', [ApiComponent::class, 'update_online_status']);
    Route::get('update_offline_status', [ApiComponent::class, 'update_offline_status']);
    Route::get('get_image_limit', [ApiComponent::class, 'get_max_upload_img_limit']);
    Route::post('reset_password', [ApiComponent::class, 'reset_password']);
    Route::get('resend_verify_email', [ApiComponent::class, 'resend_verify_email']);
    Route::get('email_verify', [ApiComponent::class, 'email_verify']);
    Route::get('get_active_map_homepage', [ApiComponent::class, 'get_active_map_homepage']);
    Route::post('add-seller-review', [ApiComponent::class, 'add_seller_review']);
    Route::get('seller-review-list/{any}', [ApiComponent::class, 'seller_review_list']);
    Route::get('default_lang', [ApiComponent::class, 'default_language']);
    Route::get('active_language', [ApiComponent::class, 'active_language']);
    Route::get('language/{any}', [ApiComponent::class, 'language']);
    Route::get('get-language/{any}', [ApiComponent::class, 'getLanguage']);
    Route::get('lang', [ApiComponent::class, 'lang_code_name']);
    Route::post('verification_request', [ApiComponent::class, 'verification_request']);
    Route::get('get_custome_fields_addpost/{any}', [ApiComponent::class, 'get_custome_fields_addpost']);
    Route::get('get_filter_options', [ApiComponent::class, 'get_filter_options']);
    Route::get('search', [ApiComponent::class, 'home_search']);
    Route::post('read-status-update', [ApiComponent::class, 'readStatusUpdate']);
    Route::get('unread_chat', [ApiComponent::class, 'unreadChat']);
    Route::get('recently_viewed_posts/{any}', [ApiComponent::class, 'recently_viewed_posts']);

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});