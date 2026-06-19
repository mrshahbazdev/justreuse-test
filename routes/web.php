<?php
use App\Http\Livewire\Admin\ReportComponent;
use App\Http\Controllers\Common;
use App\Http\Livewire\Admin\PaymentMethodsComponent;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\SavedPostComponent;
use App\Http\Livewire\StripePaymentComponent;
use App\Http\Livewire\LoginWithGoogle;
use App\Http\Livewire\LoginWithFacebook;
use App\Http\Livewire\Admin\EmailTemplateComponent;
use App\Http\Livewire\PaypalPaymentComponent;
use App\Http\Livewire\VerificationrequestComponent;
use App\Http\Livewire\ReportAdComponent;
use Laravel\Jetstream\Http\Controllers\Livewire\UserProfileController;
use App\Http\Livewire\Admin\ReviewComponent;
use Plugins\Paypal\PaypalController;
use App\Http\Livewire\Admin\LanguageComponent;
use App\Http\Livewire\ChoosePackageStepOne;
use Plugins\Stripe\StripeController;
use App\Http\Livewire\Admin\Countries;
use App\Http\Livewire\Admin\Advertisings;
use App\Http\Livewire\Admin\CategoryReorderComponent;
use App\Http\Livewire\ChooseMultiplePackage;
use App\Http\Livewire\PostComponentSave;
use App\Http\Livewire\UserLandingComponent;
use Bannerplugins\Bannermap\BannerMapController;
use App\Http\Livewire\Admin\AdminContactUsComponent;
use App\Http\Livewire\Admin\BannerAdvertisementsComponent;
use App\Http\Livewire\Admin\BulkEmailComponent;
use App\Http\Livewire\Admin\Currencies;
use App\Http\Livewire\ChatList;
use App\Http\Livewire\LoginWithOtp;
use App\Http\Livewire\ApiComponent;
use Illuminate\Support\Facades\Artisan;
use Mobileplugins\Mobilepaypal\MobilePaypalController;
use App\Http\Livewire\Admin\Dashboard;
use App\Http\Livewire\FollowersComponent;
use App\Http\Livewire\Admin\ReportUserComponent;
use App\Http\Livewire\InsightsComponent;
use App\Http\Livewire\MyprofileComponent;
use App\Http\Livewire\Admin\PostMethodsComponent;
use App\Http\Livewire\Admin\CustomFieldsComponentEdit;
use App\Http\Livewire\PostComponentAdd;
use Postplugins\Bannerads\BannerAdController;
use Postplugins\Exchange\ExchangeController;
use Postplugins\Buynow\BuynowController;
use Chatplugins\Ajaxtype\AjaxComponent;
use App\Http\Livewire\Admin\ChatMethodsComponent;
use Chatplugins\Wstype\WstypeComponent;
use App\Http\Controllers\Auth\AppleSigninController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Livewire\MypackageComponentSave;
use App\Http\Controllers\SellerReviewController;
use App\Http\Livewire\Admin\FeaturesComponent;
use App\Models\Setting;
use App\Models\TblCurrency;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Livewire\Messenger;
use App\Http\Controllers\ChatController;
use App\Models\User;
use App\Http\Livewire\FavouriteAds;
use App\Http\Controllers\FavoriteController;
use App\Http\Livewire\MyOrdersSales;
use App\Http\Livewire\BuyBusinessPacks;
use App\Http\Controllers\PaymentController;
use App\Http\Livewire\MyBannerAds;
use App\Http\Livewire\CreateBannerAd;
use App\Http\Livewire\MyExchanges;
use App\Http\Livewire\SelectSinglePackage;
use App\Http\Livewire\Auth\Register;
use App\Http\Livewire\Auth\VerifyOtp;
use App\Http\Livewire\Auth\Login;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Livewire\Auth\ForgotPassword;
use App\Http\Livewire\PostDetailComponent;
use App\Http\Livewire\Admin\AdZones;
use App\Http\Livewire\Admin\AdTemplates;
use App\Http\Livewire\Admin\UserAdvertisements;
use App\Http\Livewire\CreateAdvertisement;
use App\Http\Livewire\User\MyAdvertisements;
use App\Http\Livewire\CheckoutComponent;
use App\Http\Controllers\PaymentSuccessController;
use App\Http\Controllers\AdController;
/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */
// routes/web.php
Route::get('/test-watermark', function() {
    try {
        // Test watermark path
        $watermarkPath = getWatermarkPath();
        
        if (!$watermarkPath || !file_exists($watermarkPath)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Watermark file not found',
                'path' => $watermarkPath
            ]);
        }
        
        // Test image create karein
        $testImage = Image::canvas(800, 600, '#cccccc');
        $testImage->text('TEST IMAGE', 400, 300, function($font) {
            $font->size(48);
            $font->color('#000000');
            $font->align('center');
            $font->valign('middle');
        });
        
        // Watermark apply karein
        $watermark = Image::make($watermarkPath);
        $watermark->resize(150, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $watermark->opacity(70);
        
        $testImage->insert($watermark, 'top-right', 20, 20);
        
        // Save karein
        $filename = 'test_watermark_' . time() . '.jpg';
        Storage::disk('public')->put($filename, (string) $testImage->encode('jpg'));
        
        return response()->json([
            'status' => 'success',
            'message' => 'Watermark test successful',
            'test_image_url' => Storage::url($filename),
            'watermark_path' => $watermarkPath
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

//index page front-end
Route::get('/get-ad/{pageLocation}', [AdController::class, 'getAd']);

Route::get('stripe-payment', CheckoutComponent::class)
    ->middleware('auth') // Yeh yaqeeni banata hai ke sirf logged-in users hi is page ko dekh sakein
    ->name('checkout');
Route::get('/checkout/stripe', [PaymentController::class, 'showStripeForm'])->name('stripe.checkout')->middleware('auth');
//Route::get('/payment/success', [PaymentController::class, 'paymentSuccess'])->name('payment.success')->middleware('auth');
Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success')->middleware('auth');

// Payment success ke liye route (yeh pehle se mojood ho sakta hai)
//Route::get('/payment/success', [App\Http\Controllers\PaymentController::class, 'paymentSuccess'])
//    ->name('payment.success')
//    ->middleware('auth');
/* stripe webview for app */
/* Route::get('stripe-payment', function () {
    return view('stripe_payment');
});*/
Route::get('thankyou', function () {
    return view('thankyou_webview');
});

Route::get('/payment/success', PaymentSuccessController::class)
    ->name('payment.success')
    ->middleware('auth');

Route::get('/my-advertisements', MyAdvertisements::class)
    ->middleware('auth') 
    ->name('my-advertisements');
Route::get('/my-banner-ads', MyAdvertisements::class)
    ->middleware('auth') 
    ->name('my-advertisements');
Route::get('/advertise/create', CreateAdvertisement::class)
    ->middleware('auth') 
    ->name('ads.create');
Route::group(['middleware' => ['auth', 'role:Admin|SuperAdmin'], 'prefix' => 'admin'], function () {
    Route::get('/ad-zones', AdZones::class)->name('admin.ad-zones');
    // Hum baaqi do routes agle steps mein add karenge
    Route::get('/ad-templates', AdTemplates::class)->name('admin.ad-templates');
    Route::get('/user-advertisements', UserAdvertisements::class)->name('admin.user-advertisements');
});
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    // Pehle user ko dhoondein
    $user = User::find($request->route('id'));

    // Agar user mil jaye aur uski email verified nahi hai
    if ($user && ! $user->hasVerifiedEmail()) {
        // To usay OTP page par bhej dein
        return redirect()->route('otp.verify', ['email' => $user->email]);
    }

    // Agar user pehle se verified hai, to usay dashboard par bhej dein
    return redirect()->intended(config('fortify.home').'?verified=1');
    
})->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
Route::get('/forgot-password', ForgotPassword::class)->middleware('guest')->name('password.request');
Route::get('/login', Login::class)
    ->middleware('guest') 
    ->name('login');
Route::get('/verify-otp', VerifyOtp::class)->middleware('guest')->name('otp.verify');
Route::get('/register', Register::class)
    ->middleware('guest') 
    ->name('register');
Route::get('/selectPackage', SelectSinglePackage::class)
    ->middleware(['auth:sanctum', 'role:User', 'verified']) // Yeh yaqeeni banata hai ke sirf logged-in users hi is page ko dekh sakein
    ->name('select-single-package');

Route::post('/add-exchange', [ExchangeController::class, 'add_exchange'])->name('add_exchange');
Route::post('/update-exchange-status', [ExchangeController::class, 'update_exchange_status'])->name('update_exchange_status');
Route::get('/my-exchanges/{tab?}', MyExchanges::class)
    ->middleware('auth') // Yeh yaqeeni banata hai ke sirf logged-in users hi is page ko dekh sakein
    ->name('my-exchanges');

// === PURANE ROUTES KO NAYE ROUTE PAR REDIRECT KAREIN ===
Route::redirect('/my-exchange/incoming', '/my-exchanges/incoming', 301);
Route::redirect('/my-exchange/outgoing', '/my-exchanges/outgoing', 301);
Route::redirect('/my-exchange/successful', '/my-exchanges/successful', 301);
Route::redirect('/my-exchange/failed', '/my-exchanges/failed', 301);


//Route::get('/my-banner-ads', MyBannerAds::class)
  //  ->middleware('auth') 
 //   ->name('my-banner-ads');
//Route::get('/banner-advertise', CreateBannerAd::class)
   // ->middleware('auth') // Yeh yaqeeni banata hai ke sirf logged-in users hi is page ko dekh sakein
  //  ->name('banner-ads.create');
    
    
    
/* front banner advertisement */
//Route::middleware(['auth:sanctum', 'role:User', 'verified'])->get('banner-advertise', [BannerAdController::class, 'front_banner_ads']);
//Route::post('/get_banner_price', [BannerAdController::class, 'get_banner_price'])->name('get_banner_price');
//Route::post('/save_banner_ads', [BannerAdController::class, 'save_banner_ads'])->name('save_banner_ads');



Route::get('/selectPackageMultiple', BuyBusinessPacks::class)
    ->middleware('auth') 
    ->name('buy-business-packs');
Route::get('/my-buynow/{tab?}', function () {
    return view('my-orders-sales');
})->middleware('auth')->name('my-orders-sales');
Route::post('/toggle-favorite', [FavoriteController::class, 'toggle'])->name('toggle.favorite')->middleware('auth');
Route::get('/favourite', function () {
    return view('my-favourites');
})->middleware('auth')->name('my-favourites');
Route::get('/messages', Messenger::class)->middleware('auth')->name('messages');
Route::get('/chat/start/{userId}/{postId}', [ChatController::class, 'startMiniChat'])->name('minichat.start')->middleware('auth');
Route::get('my-followers', function () {
    return view('my-connections');
})->middleware('auth')->name('my-connections');
Route::get('my-followings', function () {
    return view('my-connections');
})->middleware('auth')->name('my-connections');


Route::get('/', function () {
    return view('landing');
})->middleware('localization');
Route::get('/websocket', function () {
    Artisan::call('websocket:init');
});
Route::get('/mail', function () {
    try {
        Mail::raw('Hello World!', function ($msg) {
            $msg->to('mrshahbaznns@gmail.com')->subject('TestEmail');
        });
        // If the email is sent successfully, you can redirect or return a success response
        return 'Email sent successfully';
    } catch (\Exception $e) {
        // If an exception occurs during email sending, catch it and return an error message
        return 'Error sending email: ' . $e->getMessage();
    }
});
Route::get('/send_mail', function () {
    $mail_data = array("send_maildata" => array('to_id' => "218af931-29d9-4c8b-89af-cb6980b2f168", 'message' => "New user contacted you on ", 'subject' => "New chat In Justreused!..", 'ad_url' => ""));
    $mail_key = "post_chat";
    Setting::notification_mail($mail_data, $mail_key);
});
Route::get('convert', function () {
    $default_crr = Setting::get_admin_default_currency();
    $curr_id = $default_crr['id'];
    $curr_short_code = $default_crr['short_code'];
    $post_currency = TblCurrency::where('id', 16)->value('short_code');
    // dd($curr_id,$curr_short_code,$post_currency);
    $req_url = 'https://v6.exchangerate-api.com/v6/3b135c35e73f91d7427b14c4/pair/EUR/GBP/123';
    $response_json = file_get_contents($req_url);
    // Continuing if we got a result
    if (false !== $response_json) {
        // Try/catch for json_decode operation
        try {
            // Decoding
            $response = json_decode($response_json);
            // Check for success
            if ('success' === $response->result) {
                return $response->result;
            }
        } catch (Exception $e) {
            // Handle JSON parse error...
        }
    }
});
Route::post('/set-timezone', function (Request $request) {
    $timezone = $request->input('timezone');
    // Store the timezone in the session
    session(['user_timezone' => $timezone]);
    return response()->json(['success' => true, 'timezone' => $timezone]);
});
Route::get('/allcache-clearance', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    // Artisan::call('make:command composer');
    // Artisan::call('make:model TblPostMethod -m');
    // Artisan::call('migrate');
    return 'Application cache cleared.';
});
/*Route::get('/create-storage', function () {
    Artisan::call('storage:link');
});*/
//login with apple
Route::get('/apple-login', [AppleSigninController::class, 'login']);
Route::post('/callback', [AppleSigninController::class, 'callback']);
//languages
Route::get('locale/{locale}', function ($locale) {
    session()->put('locale', $locale);
    return redirect()->back();
});
//admin languages
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/sublanguage-show', [LanguageComponent::class, 'sublanguage'])->name('sublanguage');
Route::POST('/sublang', [LanguageComponent::class, 'active_sub_languages'])->name('sublang');
Route::POST('/sublang_edit', [LanguageComponent::class, 'edit_sub_languages'])->name('sublang_edit');
Route::Post('/sublang_delete', [LanguageComponent::class, 'delete_sub_languages'])->name('sublang_delete');
Route::get('/add_sublang/{locale}', [LanguageComponent::class, 'add_sublang'])->name('addlang');
Route::post('/add_sublang_store', [LanguageComponent::class, 'add_sublang_store'])->name('sublang_store');
Route::get('/lang_folder', [LanguageComponent::class, 'generateLanguageMapping'])->name('lang_folder');
Route::post('email_validate', [UserLandingComponent::class, 'email_validate_rcf_dns']);
Route::post('save_demo_cookie', [UserLandingComponent::class, 'save_demo_cookie']);
Route::post('/set_current_loc_session', [UserLandingComponent::class, 'set_current_loc_session'])->name('set_current_loc_session');
Route::post('km_ads_from_cur_dist', [BannerMapController::class, 'km_ads_from_cur_dist']);
//admin user profile url
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/my-profile', function () {
    return view('admin/admin_profile');
})->name('admin-myprofile');
Route::middleware(['auth:sanctum', 'role:User', 'verified'])->get('/my-profile', function () {
    return view('myprofile');
})->name('myprofile');
//change password
Route::middleware(['auth:sanctum', 'role:User'])->get('/change-password', function () {
    return view('livewire/myprofile/change_password');
})->name('change-password');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/change-password', function () {
    return view('livewire/admin/myprofile/change_password');
})->name('admin-change-password');
// Route::middleware(['auth:sanctum', 'verified'])->get('/users/profile', function () {
//     return view('livewire/profile/show');
// }); -- old my profile
// update profile verify phone number
Route::post('/profile_send_otp', [MyprofileComponent::class, 'profile_send_otp'])->name('profile_send_otp');
Route::post('/profile_verify_otp', [MyprofileComponent::class, 'profile_verify_otp'])->name('profile_verify_otp');
// Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/users/profile', function () {
//     return view('livewire/admin/profile/show');
// }); -- old my profile admin page
/* Contact us */
Route::get('contact-us', function () {
    return view('contact-us');
});

/* admin side banner ads */
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/banner-advertisements', function () {
    return view('admin.banner-advertisements');
})->name('admin/banner-advertisements');
Route::post('/approve-banner-ads', [BannerAdvertisementsComponent::class, 'approve_banner_ads'])->name('approve_banner_ads');
//logo uploade process updated at 10/17/2024
Route::post('/file', [CustomFieldsComponentEdit::class, 'file_upload'])->name('file');
Route::post('/logo', [CustomFieldsComponentEdit::class, 'logo_upload'])->name('logo');
// Features
Route::get('admin/features', [FeaturesComponent::class, 'render'])->middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->name('features');
Route::post('admin/features/store', [FeaturesComponent::class, 'store'])->middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->name('features.store');
Route::get('admin/features/export', [FeaturesComponent::class, 'store'])->middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->name('features.export');
Route::post('get_brand', [FeaturesComponent::class, 'get_brand'])->middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->name('get_brand');
Route::get('admin/features-map', [FeaturesComponent::class, 'features_map'])->middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->name('features-map');
Route::get('admin/features-map-show', [FeaturesComponent::class, 'features_map_show'])->middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->name('features-map-show');
Route::post('admin/features-map-store', [FeaturesComponent::class, 'features_map_store'])->middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->name('features-map-store');
Route::get('admin/features-map-edit/{id}', [FeaturesComponent::class, 'features_map_edit'])->middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->name('features-map-edit');
Route::post('admin/features-map-update', [FeaturesComponent::class, 'features_map_update'])->middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->name('features-map-update');
Route::get('admin/features-map-delete/{id}', [FeaturesComponent::class, 'features_map_delete'])->middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->name('features-map-delete');
// Order features
Route::get('admin/features-order-list', [FeaturesComponent::class, 'featureOrder'])->middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->name('features-order-list');
Route::post('update-features-order', [FeaturesComponent::class, 'update_features_order'])->middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->name('update-features-order');


Route::middleware(['auth:sanctum', 'role:User', 'verified'])->get('chat', [WstypeComponent::class, 'wschat']);
Route::post('/send_chat', [WstypeComponent::class, 'send_chat'])->name('send_chat');
Route::post('/share_location', [WstypeComponent::class, 'share_location'])->name('share_location');
Route::get('/send_chat_fetch_last_seen', [WstypeComponent::class, 'send_chat_fetch_last_seen'])->name('send_chat_fetch_last_seen');
Route::post('/send_chat_update_last_seen', [WstypeComponent::class, 'send_chat_update_last_seen'])->name('send_chat_update_last_seen');
Route::post('/delete_chat', [WstypeComponent::class, 'delete_chat'])->name('delete_chat');
Route::post('/fetch_user_status', [WstypeComponent::class, 'fetch_user_status'])->name('fetch_user_status');
Route::get('get-unread-chat-count', [WstypeComponent::class, 'get_unread_chat_count']);


Route::middleware(['auth:sanctum', 'role:User', 'verified'])->get('chatting', [AjaxComponent::class, 'chatpage']);
Route::post('/ajax_send_chat_msg', [AjaxComponent::class, 'ajax_send_chat_msg'])->name('ajax_send_chat_msg');
Route::post('/ajax_send_chat_location', [AjaxComponent::class, 'ajax_send_chat_location'])->name('ajax_send_chat_location');
Route::post('/ajax_send_chat_image', [AjaxComponent::class, 'ajax_send_chat_image'])->name('ajax_send_chat_image');
Route::post('/ajax_send_chat_accept_offer', [AjaxComponent::class, 'ajax_send_chat_accept_offer'])->name('ajax_send_chat_accept_offer');
Route::post('/ajax_send_chat_denied_offer', [AjaxComponent::class, 'ajax_send_chat_denied_offer'])->name('ajax_send_chat_denied_offer');
Route::post('/ajax_send_chat_offer_state_check', [AjaxComponent::class, 'ajax_send_chat_offer_state_check'])->name('ajax_send_chat_offer_state_check');
Route::post('/ajax_send_chat_block_delete_chat', [AjaxComponent::class, 'ajax_send_chat_block_delete_chat'])->name('ajax_send_chat_block_delete_chat');
Route::post('/ajax_send_chat_update_last_seen', [AjaxComponent::class, 'ajax_send_chat_update_last_seen'])->name('ajax_send_chat_update_last_seen');
Route::get('/ajax_send_chat_fetch_last_seen', [AjaxComponent::class, 'ajax_send_chat_fetch_last_seen'])->name('ajax_send_chat_fetch_last_seen');
Route::get('/ajax_reload_conversation_area', [AjaxComponent::class, 'ajax_reload_conversation_area'])->name('ajax_reload_conversation_area');
Route::get('/ajax_reload_chatlist_area', [AjaxComponent::class, 'ajax_reload_chatlist_area'])->name('ajax_reload_chatlist_area');
Route::post('/ajax_chat_update_message_read', [AjaxComponent::class, 'ajax_chat_update_message_read'])->name('ajax_chat_update_message_read');
Route::post('/ajax_chat_fetch_readed_ids', [AjaxComponent::class, 'ajax_chat_fetch_readed_ids'])->name('ajax_chat_fetch_readed_ids');
// admin
Route::get('dashboard-search', [Dashboard::class, 'get_side_data'])->name('dashboard-search');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/chat-methods', function () {
    return view('admin.chat-methods');
})->name('admin/chat-methods');
Route::post('/enable_chat_method', [ChatMethodsComponent::class, 'enable_chat_method'])->name('enable_chat_method');
/*ajax chat end */
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/contact-us', function () {
    return view('admin/contact-us');
})->name('admin-contact-us');
Route::post('/view-description', [AdminContactUsComponent::class, 'view_description'])->name('view-description');
Route::post('/delete-contact-us', [AdminContactUsComponent::class, 'delete_contact_us'])->name('delete-contact-us');
//del report-ad
Route::post('/delete-report', [SavedPostComponent::class, 'delete_report'])->name('delete_report');
//del report-user
Route::post('/delete-user-report', [ReportUserComponent::class, 'delete_user_report'])->name('delete_user_report');
//report ad - front
Route::post('/report-ad', [ReportAdComponent::class, 'report_ad'])->name('report_ad');

//crea new checkout
Route::middleware(['auth:sanctum', 'role:User', 'verified'])->get('revieworder/{any}', [BuynowController::class, 'new_checkout']);
Route::post('/create-shipping-address', [BuynowController::class, 'create_shipping_address'])->name('create_shipping_address');
Route::post('/update-shipping-address', [BuynowController::class, 'update_shipping_address'])->name('update_shipping_address');
Route::post('/delete-shipping-address', [BuynowController::class, 'delete_shipping_address'])->name('delete_shipping_address');

Route::middleware(['auth:sanctum', 'role:User', 'verified'])->get('vieworder/{any}', [BuynowController::class, 'buynow_vieworder']);
/* buynow orders admin side */
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/buynow-orders', function () {
    return view('admin.buynow-orders');
})->name('admin/buynow-orders');
Route::post('/update-order-status', [BuynowController::class, 'update_order_status'])->name('update_order_status');
Route::get('/order-invoice/{any}', [BuynowController::class, 'get_order_invoice'])->name('get_order_invoice');
Route::get('/admin/view-order-invoice/{orderId}', [BuynowController::class, 'viewOrderInvoice'])->name('admin/view-order-invoice');
Route::get('back', [BuynowController::class, 'viewBack'])->name('back');
//report user - front
Route::post('/report-user', [FollowersComponent::class, 'report_user'])->name('report_user');
//show report-comment
Route::post('/report-comment', [ReportComponent::class, 'report_comment'])->name('report_comment');
//show report-user-comment
Route::post('/report-user-comment', [ReportUserComponent::class, 'report_user_comment'])->name('report_user_comment');
//approved- ad
Route::post('/approve-review', [ReviewComponent::class, 'approved'])->name('approve_review');
//show review-comment
Route::post('/review-comment', [ReviewComponent::class, 'review_comment'])->name('review_comment');
//del- reviews
Route::post('/delete-review', [SavedPostComponent::class, 'delete_review'])->name('delete_review');
//login with google
Route::get('auth/google', [LoginWithGoogle::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [LoginWithGoogle::class, 'handleGoogleCallback']);
//login with facebook
Route::get('auth/facebook', [LoginWithFacebook::class, 'redirectToFacebook']);
Route::get('auth/facebook/callback', [LoginWithFacebook::class, 'handleFacebookCallback']);
//Route::post('star-rating-store', 'Common@StoreStarRating');
Route::post('review-store', [Common::class, 'ReviewStore']);
//disabled it/ enable it later if required
Route::post('/republish_post', [PostComponentSave::class, 'republish_post'])->name('republish_post');
//update post info
Route::post('/update_post_info', [PostComponentSave::class, 'update_post_info'])->name('update_post_info');
//add fav
Route::post('/savepost', [SavedPostComponent::class, 'save'])->name('savepost');
Route::post('/savefollowers', [FollowersComponent::class, 'savefollowers'])->name('savefollowers');
Route::post('/get_whole_sale_data', [Dashboard::class, 'get_whole_sale_data'])->name('get_whole_sale_data');
//del fav from list page
Route::post('/fav', [SavedPostComponent::class, 'delete_fav_add'])->name('delete_fav');
// My ads
Route::post('/posted_add', [SavedPostComponent::class, 'delete_posted_add'])->name('delete_posted');
//block user route -  admin side
Route::post('/blocked', [SavedPostComponent::class, 'user_blocked'])->name('user-blocked');
Route::middleware(['auth:sanctum', 'role:User', 'verified'])->get('/post', function () {
    return view('post');
})->name('post');
Route::middleware(['auth:sanctum', 'role:User', 'verified'])->get('/post-add', function () {
    return view('post_add');
})->name('post-add');
Route::middleware(['auth:sanctum', 'role:User', 'verified'])->get('/post-edit', function () {
    return view('post_edit');
})->name('post-edit');
Route::middleware(['auth:sanctum', 'role:User', 'verified'])->post('/post-add-save', function () {
    return view('post_add_save');
})->name('post-add-save');

Route::get('get-notification-count', [Common::class, 'get_notification_count']);
Route::get('get-unread-chat-count', [Common::class, 'get_unread_chat_count']);
/* notification list */
Route::middleware(['auth:sanctum', 'verified'])->get('/notifications', function () {
    return view('notifications');
})->name('notifications');
/* front followers */
Route::get('/seller-profile/{seller}', function (User $seller) {
    // Yeh route 'seller-profile/1' jaisi URL ko handle karega
    // Laravel khud hi ID se User model fetch kar lega
    return view('seller-page', ['seller' => $seller]);
})->name('seller.profile'); // Is route ko ek naam de dein
Route::get('/insights/{any}', function () {
    return view('insights');
})->name('insights');

Route::post('get_pack_info', [ChoosePackageStepOne::class, 'get_pack_info'])->name('get_pack_info');

Route::post('update_bulk_pack_cart', [ChooseMultiplePackage::class, 'update_bulk_pack_cart'])->name('update_bulk_pack_cart');
// admin side show bulk payments
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/bulk-payments', function () {
    return view('admin.bulk-payments');
})->name('admin/bulk-payments');
//paypall
Route::get('paypal-payment-process', [PaypalController::class, 'ajax_payment']);
Route::get('paypal-payment-success', [PaypalController::class, 'ajax_success']);
Route::get('paypal-payment-cancel', [PaypalController::class, 'ajax_cancel']);
Route::get('paypal-payment-bulk-package', [PaypalController::class, 'ajax_bulk_payment']);
Route::get('paypal-payment-bulk-success', [PaypalController::class, 'ajax_bulk_payment_success']);
Route::get('bannerad-paypal-payment-process', [PaypalController::class, 'bannerads_payment']);
Route::get('bannerad-paypal-payment-success', [PaypalController::class, 'bannerads_paid_success']);
Route::get('bannerad-paypal-payment-cancel', [PaypalController::class, 'ajax_cancel']);
Route::post('refund_payment_paypal', [PaypalController::class, 'paypal_refund_payment']);
//Plugins - end
// //paypall
Route::post('stripe_proceed', [StripeController::class, 'stripe_proceed']);
Route::post('stripe_proceed_add_post', [StripeController::class, 'stripe_proceed_add_post']);
Route::post('bannerads_stripe_proceed', [StripeController::class, 'bannerads_stripe_proceed']);
Route::post('stripe_bulk_pack', [StripeController::class, 'stripe_bulk_pack']);
Route::post('buynow_stripe_orders', [StripeController::class, 'buynow_stripe_orders']);
Route::post('refund_payment_stripe', [StripeController::class, 'stripe_refund_payment']);

//mobile stripe start
Route::post('app_stripe_bulk_pack', [StripePaymentComponent::class, 'stripe_app_bulk_pay']);
Route::post('app_stripe_single_pack', [StripePaymentComponent::class, 'stripe_app_card_payment']);
Route::post('app_stripe_banner_ads', [StripePaymentComponent::class, 'stripe_app_banner_ads']);
Route::post('app_stripe_buynow', [StripePaymentComponent::class, 'app_stripe_buynow']);
//mobile stripe end
//Ads start
Route::get('packages', function () {
    return view('front_packages_detail');
});
//Ads end
/* mypackages - front end */
Route::middleware(['auth:sanctum', 'role:User', 'verified'])->get('/mypackage', function () {
    return view('mypackage');
})->name('mypackage');
/* mypackages-add-post - front end */
Route::middleware(['auth:sanctum', 'role:User', 'verified'])->get('/mypackage-add', function () {
    return view('mypackage_add');
})->name('mypackage-add');
//Static pages - front-end
Route::get('pages/{any}', function () {
    return view('front_static_pages');
});
Route::get('/loginwithotp', function () {
    return view('front_login_otp');
});
Route::post('/send_otp', [LoginWithOtp::class, 'send_otp'])->name('send_otp');
Route::post('/verify_otp', [LoginWithOtp::class, 'verify_otp'])->name('verify_otp');
// active language
Route::post('/active-language', [LanguageComponent::class, 'active'])->name('active');
// default language
Route::post('/default-language', [LanguageComponent::class, 'default'])->name('default');
Route::post('/show-home-category', [Common::class, 'show_in_home'])->name('show-home-category');
Route::post('/enable-disable-category', [Common::class, 'enable_disable'])->name('enable-disable-category');
Route::post('/buynow-enable-disable-category', [Common::class, 'buynow_enable_disable'])->name('buynow-enable-disable-category');
Route::post('/exchange-enable-disable-category', [Common::class, 'exchange_enable_disable'])->name('exchange-enable-disable-category');
Route::post('/get_buynow', [Common::class, 'get_buynow'])->name('get_buynow');
Route::post('/get_exchange', [Common::class, 'get_exchange'])->name('get_exchange');
//************ ADMIN URLS BEGIN ***********/
// Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
//     return view('admin.dashboard');
// })->name('dashboard');
Route::post('/mypackage-add-save', [MypackageComponentSave::class, 'assignAds'])->name('assignAds');
/*Route::middleware(['auth:sanctum', 'role:User', 'verified'])->post('/mypackage-add-save', function () {
    return view('mypackage_add_save');
})->name('mypackage-add-save');*/
// enable advertising - admin side
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/advertising', function () {
    return view('admin.advertisings');
})->name('admin/advertising');
Route::post('/enable_advertising', [Advertisings::class, 'enable_advertising'])->name('enable_advertising');
// blacklist - admin side
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/blacklist', function () {
    return view('admin.blacklists');
})->name('admin/blacklist');
// enable country - admin side
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/country', function () {
    return view('admin.countries');
})->name('admin/country');
Route::post('/set_country_currency', [Countries::class, 'set_country_currency'])->name('set_country_currency');
Route::post('/enable_country', [Countries::class, 'enable_country'])->name('enable_country');
Route::post('/active-multiple-countries', [Countries::class, 'active_multiple_countries'])->name('active_multiple_countries');
Route::post('/active-all-countries', [Countries::class, 'active_all_countries'])->name('active_all_countries');
// Languages
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/languages', function () {
    return view('admin.language');
})->name('admin/language');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/languages-edit', function () {
    return view('admin.language_edit');
})->name('admin/language-edit');
// couponcode - admin side
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/coupon', function () {
    return view('admin.coupons');
})->name('admin/coupon');
// staticpage - admin side
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/staticpage', function () {
    return view('admin.staticpages');
})->name('admin/staticpage');
// review - admin side
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/reviews', function () {
    return view('admin.review');
})->name('admin/review');
// payments - admin side
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/payments', function () {
    return view('admin.payment');
})->name('admin/payment');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/payment-methods', function () {
    return view('admin.payment-methods');
})->name('admin/payment-methods');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/post-methods', function () {
    return view('admin.post-methods');
})->name('admin/post-methods');
Route::post('/enable_post_method', [PostMethodsComponent::class, 'enable_post_method'])->name('enable_post_method'); //->name('review_comment')
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/payment-methods/keysupdate', function () {
    return view('admin.keys-update');
})->name('admin/keys-update');
Route::post('/enable_package', [PaymentMethodsComponent::class, 'enable_package'])->name('enable_package'); //->name('review_comment')
//report_type - admin side
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/report-type', function () {
    return view('admin.report-type');
})->name('admin/report-type');
//report ad - admin side
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/report-ad', function () {
    return view('admin.report-ad');
})->name('admin/report-ad');
//report user - admin side
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/report-user', function () {
    return view('admin.report-user');
})->name('admin/report-user');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/dashboard', function () {
    return view('mydashboard');
})->name('dashboard');
//Category start
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/category-reorder', function () {
    return view('admin.category_reorder');
})->name('admin/category-reorder');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/category-reorder/{any}', function () {
    return view('admin.category_reorder');
})->name('admin/category-reorder');
Route::post('/update-category-order', [CategoryReorderComponent::class, 'update_category_order'])->name('update-category-order');
//Route::post('/',[SavedPostComponent::class, 'save'])->name('savepost');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/category/{id}/{any}', function () {
    return view('admin.category');
})->name('admin/category');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin', 'cache.headers:etag'])->get('/admin/category', function () {
    return view('admin.category');
})->name('admin/category');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/category-add', function () {
    return view('admin.category_add');
})->name('admin/category-add');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/category-edit', function () {
    return view('admin.category_edit');
})->name('admin/category-edit');
//Category end
//custom-fields start
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/custom-fields', function () {
    return view('admin.custom_fields_edit');
})->name('admin/custom-fields');
//custom-fields end
Route::middleware(['auth:web', 'role:Admin|SuperAdmin'])->get('/admin/role', function () {
    return view('admin.role');
})->name('admin/role');
//role:Admin
Route::middleware(['auth:web', 'role:Admin|SuperAdmin'])->get('/admin/permissions', function () {
    return view('admin.permissions');
})->name('admin/permissions');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/user', function () {
    return view('admin.users');
})->name('admin/user');
/* currency management */
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/currency', function () {
    return view('admin.currencies');
})->name('admin/currency');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/packages', function () {
    return view('admin.packages');
})->name('admin/package');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/settings', function () {
    return view('admin.settings');
})->name('admin/settings');
// post lists
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/posts', function () {
    return view('admin.admin_posts');
})->name('admin/post');
// update post - admin side
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->post('/admin/post-update', function () {
    return view('admin.admin_update_posts');
})->name('admin/post-update');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->post('/admin/key-update', function () {
    return view('admin.admin_key_update');
})->name('admin/key-update');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/home-banner', function () {
    return view('admin.home-banner');
})->name('admin/home-banner');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/email-template', function () {
    return view('admin.email-template');
})->name('admin/email-template');
// otherpage - admin side
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/otherpage', function () {
    return view('admin.otherpages');
})->name('admin/otherpages');
// bulk email list
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/bulk-email', function () {
    return view('admin.bulk-email');
})->name('admin/bulk-email');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/blocked-posts', function () {
    return view('admin.blocked-post');
})->name('admin/blocked-post');
// invite friends front-end
Route::post('/invite_friend', [FollowersComponent::class, 'invite_friend'])->name('invite_friend');
// get models list
Route::post('/get_custom_models', [PostComponentAdd::class, 'modelHtml'])->name('get_custom_models');
Route::post('/get-brand-icon', [PostComponentAdd::class, 'getBrandIcon'])->name('get_brand_icon');
//************ ADMIN URLS END ***********/
// Route::get('post/{any}', function () {
//     return view('front_post_detail');
// });
// cron expired post - sent notification
Route::get('/cron-post-expire-today', [Common::class, 'notify_today_expire_post']);
Route::get('/cron-post-expire-yesterday', [Common::class, 'notify_yesterday_expire_post']);
// cron sent bulk email 
Route::get('/cron-send-bulk-email', [BulkEmailComponent::class, 'send_bulk_email']);
// cron delete posted add
Route::get('/cron-delete-posted-ads', [Common::class, 'cron_delete_posted_ads']);
Route::get('/cron-delete-users', [Common::class, 'cron_delete_users']);

//verification front and admin side
Route::middleware(['auth:sanctum', 'role:User', 'verified'])->get('/verificationrequest', function () {
    return view('verificationRequest');
})->name('verificationrequest');
Route::post('/form_submit', [VerificationrequestComponent::class, 'store'])->name('form_submit');
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/verification-request', [VerificationrequestComponent::class, 'show'])->name('admin/verification-request');
Route::post('admin/verification_request/attachments', [VerificationrequestComponent::class, 'attachments']);
Route::get('admin/verification_request/approved_user', [VerificationrequestComponent::class, 'approved_user']);
Route::POST('/admin/download-file', [VerificationrequestComponent::class, 'downloads'])->name('admin/download-file');
Route::get('admin/verification_request/approve/{id}', [VerificationrequestComponent::class, 'approve'])->name('admin/verification_request/approve');
Route::get('admin/verification_request/decline/{id}', [VerificationrequestComponent::class, 'decline'])->name('admin/verification_request/decline');
Route::post('admin/verification_request/decline-approve/{id}', [VerificationrequestComponent::class, 'decline_approve'])->name('admin/verification_request/decline-approve');
Route::get('/sitemap', [Common::class, 'getLinks']);
Route::get('/sitemap.xml', [Common::class, 'xmldata']);
Route::get('/sitemap_categories.xml', [Common::class, 'getxmlContent']);
Route::get('/sitemap_pages.xml', [Common::class, 'getstaticpageContent']);
Route::get('/sitemap_staticpages.xml', [Common::class, 'getpageContent']);
Route::get('/sitemap_posts.xml', [Common::class, 'getposts']);
// Geo IP lookup (server-side to avoid CORS)
Route::get('/geo-country', function () {
    try {
        $ip = request()->ip();
        $data = @json_decode(file_get_contents("http://ip-api.com/json/{$ip}?fields=countryCode"), true);
        return response()->json(['country_code' => $data['countryCode'] ?? 'us']);
    } catch (\Exception $e) {
        return response()->json(['country_code' => 'us']);
    }
});

// Search suggestions (JSON) — must be before the catch-all
Route::get('/search', function () {
    $q = request('q', '');
    if (empty(trim($q))) {
        return response()->json(['data' => []]);
    }
    $posts = \App\Models\TblPost::where('title', 'like', '%' . $q . '%')
        ->where('active', 1)
        ->where('sold_status', 0)
        ->whereNull('deleted_at')
        ->select('id', 'title', 'category_id')
        ->limit(7)
        ->get()
        ->map(fn($p) => ['id' => $p->id, 'value' => $p->title, 'category_id' => $p->category_id]);
    return response()->json(['data' => $posts]);
});

//search page
Route::get('{any}', function () {
    if (isset($_GET['loc']) || isset($_GET['q'])) {
        return view('front_search_detail');
    } else {
        return view('front_post_detail');
    }
});
Route::post('admin/createemail-templates', [EmailTemplateComponent::class, 'email_tem'])->name('admin/createemail-templates');
// Seller review
Route::post('seller-review-store', [Common::class, 'SellerReviewStore']);
Route::middleware(['auth:sanctum', 'role:Admin|SuperAdmin'])->get('/admin/seller_reviews', [SellerReviewController::class, 'render'])->name('admin/seller_review');
route::post('seller_review_delete/{id}', [SellerReviewController::class, 'delete'])->name('seller_review_delete');
Route::get('seller_review_approve/{id}', [SellerReviewController::class, 'approved'])->name('seller_review_approve');
Route::post('/delete_bulk', [SellerReviewController::class, 'delete_review'])->name('delete_review');
Route::get('/{slug}', PostDetailComponent::class)->name('post.detail');

Route::post('/minichat/ajax-start', [ChatController::class, 'startChat'])->name('minichat.ajax.start');
Route::post('/minichat/send', [ChatController::class, 'sendMessage'])->name('minichat.ajax.send');
Route::post('/minichat/messages', [ChatController::class, 'getChatMessages'])->name('minichat.ajax.messages');
