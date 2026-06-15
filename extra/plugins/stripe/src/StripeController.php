<?php

namespace Plugins\Stripe;

use App\Models\Package;
use App\Models\TblBulkPackPayment;
use Livewire\Component;
use App\Models\TblPayment;
use App\Models\TblPostedAdPackageInfo;
use Illuminate\Http\Request;
use App\Models\TblPost;
use App\Models\TblBannerAdvertisement;
use App\Models\User;
use App\Models\TblShippingAddress;
use App\Models\TblBuynowOrder;
use App\Models\TblCurrency;
use App\Models\User_profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Setting;
use Livewire\WithFileUploads; //for file upload
use Stripe;
use Illuminate\Support\Facades\URL;
use Image;
use Storage;
use Stripe\PaymentIntent;

class StripeController
{
    use WithFileUploads; //for file upload

    public function stripe_proceed(Request $request)
    {
        $returnvalue = "failed";
        $post_id = $request->get('post_id');
        $cid = $request->get('cid');
        $currency = TblCurrency::where('id', $cid)->first();
        $short_code = $currency->short_code;

        $is_exist = TblPayment::where('post_id', $post_id)->where('active', '1')->get();
        $check_post_package = TblPost::check_post_expired($post_id);
        if (($check_post_package['expired'] != "Expired" || $check_post_package['expired'] == "") && $is_exist->count() > 0) {
            \Session::put('payment_nofy', 'Payment already done for this post. Please check it.');
            return redirect('/');
        } else {
            $pack_amt  = $request->get('pack_amt');
            $token_id = $request->get('tokenId');
            $user_id = Auth::user()->id;
            $get_user_info = User::where('id', $user_id)->first();
            $stripe = Stripe::charges()->create([
                'description' => 'Self fast - single post package payment',
                'shipping' => [
                    'name' => $get_user_info->name,
                    'address' => [
                        'line1' => '510 Townsend St',
                        'postal_code' => '98140',
                        'city' => 'San Francisco',
                        'state' => 'CA',
                        'country' => 'US',
                    ],
                ],
                'source' => $token_id,
                'currency' => $short_code,
                'amount' => $request->get('pack_amt')
            ]);
            $package_id = $request->get('package_id');
            $payment_type = $request->get('payment_type');
            $live_days = $request->get('live_days');
            $curr_date = date('Y-m-d H:i:s');
            $end_date = date('Y-m-d H:i:s', strtotime($curr_date . "+" . $live_days . " days"));
            $coupon_id = ($request->get('coupon_id') == null) ? "" : $request->get('coupon_id');
            $result = $stripe["status"];

            if ($result == "succeeded") {
                $stripe_paid_id = $stripe["id"];
                $release_from_type_payment = TblPayment::where('post_id', $post_id);
                $release_from_type_payment->update([
                    "active" => "0"
                ]);
                TblPayment::create([
                    "s_payment_id" => $stripe_paid_id,
                    "user_id" => $user_id,
                    "post_id" => $post_id,
                    "start_date" => $curr_date,
                    "end_date" => $end_date,
                    "live_days" => $live_days,
                    "package_amount" => $pack_amt,
                    "active" => "1",
                    "payment_loc_ref_id" => "-",
                    "payment_status" => "completed",
                    "payment_type" => $payment_type,
                    "package_id" => $package_id,
                    "coupon_id" => $coupon_id,
                    'currency_id' => $currency->default_currency_id
                ]);
                $release_from_type_free = TblPostedAdPackageInfo::where('post_id', $post_id);
                $release_from_type_free->update([
                    "active" => "0"
                ]);

                // sent notification
                    $settings = Setting::get_logos();
                    $site_name = $settings['name'];
        
                    $auth_user = auth()->id();
                    $get_user_info = User::where('id', $auth_user)->first();
                    $get_post_info = TblPost::where('id', $post_id)->first();
                    $slug = url('/post');
                    $get_admin = User::role('superadmin')->get();
                    $admin_id = $get_admin[0]->id;
            
                    $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
                    $message = array("notifydata" => array('to_id' => $auth_user, 'from_id' => $admin_id, 'message' => "Your Stripe Payment done successfully for single ad package. Post Name - " . $get_post_info->title, 'notify_from' => 'stripe_single_package', 'notify_title' => "Single Ad Package Bought In ".$site_name." !..", 'post_id' => $post_id, 'slug' => $slug));
            
                    TblPost::send_push_notification($fcmid, $message);
    
                // sent notification end

                \Session::put('payment_nofy', 'Payment done successfully - Stripe');
                $returnvalue = "success";
            }
        }
        return $returnvalue;
    }

    //bulk pack update

    public function stripe_bulk_pack(Request $request)
    {

        $returnvalue = "failed";
        $pack_amt  = $request->get('pack_amt');
        $token_id = $request->get('tokenId');
        $cid = $request->get('cid');
        $currency = TblCurrency::where('id', $cid)->first();
        $short_code = $currency->short_code;

        $stripe = Stripe::charges()->create([
            'description' => 'My Classifieds',
            'shipping' => [
                'name' => 'Jenny Rosen',
                'address' => [
                    'line1' => '510 Townsend St',
                    'postal_code' => '98140',
                    'city' => 'San Francisco',
                    'state' => 'CA',
                    'country' => 'US',
                ],
            ],
            'source' => $token_id,
            'currency' => $short_code,
            'amount' => $request->get('pack_amt')

        ]);

        $user_id = Auth::user()->id;
        $packs = $request->get('package_id');
        $curr_date = date('Y-m-d H:i:s');
        $payment_type = $request->get('payment_type');

        $result = $stripe["status"];


        if ($result == "succeeded") {
            $stripe_paid_id = $stripe["id"];
            foreach ($packs as $p) {

                $data = Package::where('id', $p)->get();

                $live_days = $data[0]['duration'];
                $end_date = date('Y-m-d H:i:s', strtotime($curr_date . "+" . $live_days . " days"));
                $package_id = $p;
                $pack_amt  = $data[0]['price'];

                TblBulkPackPayment::create([
                    "s_payment_id" => $stripe_paid_id,
                    "user_id" => $user_id,
                    "start_date" => $curr_date,
                    "end_date" => $end_date,
                    "live_days" => $live_days,
                    "package_amount" => $pack_amt,
                    "active" => "1",
                    "payment_loc_ref_id" => "-",
                    "payment_status" => "completed",
                    "payment_type" => $payment_type,
                    "package_id" => $package_id,
                    'currency_id' => $currency->default_currency_id
                ]);
            }

            // sent notification

            $settings = Setting::get_logos();
            $site_name = $settings['name'];

            $auth_user = auth()->id();
            $get_user_info = User::where('id', $auth_user)->first();
            $slug = url('/mypackage');
            $get_admin = User::role('superadmin')->get();
            $admin_id = $get_admin[0]->id;
    
            $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
            $message = array("notifydata" => array('to_id' => $auth_user, 'from_id' => $admin_id, 'message' => "Your Stripe Payment done successfully for bulk package.", 'notify_from' => 'stripe_bulk_payment', 'notify_title' => "Bulk Package Bought In ".$site_name." !..", 'post_id' => "", 'slug' => $slug));
    
            TblPost::send_push_notification($fcmid, $message);

            // sent notification end

            //start -clear cart pack
            $cart = Session::get('cart-selected-bulk-packs');
            if (!empty($cart)) {
                Session::forget('cart-selected-bulk-packs');
            }
            Session::put('payment_nofy', 'Payment done successfully for bulk package- Stripe');
            $returnvalue = "success";
        }
        return $returnvalue;
    }


    //banner ads 
    public function bannerads_stripe_proceed(Request $request)
    {
        //$currency_symbol = Setting::get_admin_default_currency();
        //$short_code = $currency_symbol['short_code'];
        // $currency_id = $currency_symbol['id'];

        $returnvalue = "failed";
        $user_id = Auth::user()->id;
        $get_user_info = User::where('id', $user_id)->first();
        $web_banner = "";
        $app_banner = "";
        $req_web_banner = request()->web_banner;
        $req_app_banner = request()->app_banner;
        //$currency_id = request()->cid;


        $settings = Setting::get_logos();
        if (!empty($req_web_banner)) {
            $web_banner = $req_web_banner->hashName('web_banner_ads');
            $path_web_list = $req_web_banner->hashName('public/web_banner_ads');
            $web_list = Image::make($req_web_banner)->resize(null, 350, function ($constraint) {
                $constraint->aspectRatio();
            });
            $web_list->insert(public_path('storage/' . $settings['watermark']), 'bottom-right', 10, 10);
            Storage::put($path_web_list, (string) $web_list->encode());
            //$web_banner = $req_web_banner->store('web_banner_ads', 'public');
        }
        if (!empty($req_app_banner)) {
            $app_banner = $req_app_banner->hashName('app_banner_ads');
            $path_web_list = $req_app_banner->hashName('public/app_banner_ads');
            $web_list = Image::make($req_app_banner)->resize(null, 350, function ($constraint) {
                $constraint->aspectRatio();
            });
            $web_list->insert(public_path('storage/' . $settings['watermark']), 'bottom-right', 10, 10);
            Storage::put($path_web_list, (string) $web_list->encode());
            // $app_banner = $req_app_banner->store('app_banner_ads', 'public');
        }

        if (!empty($app_banner) && !empty($web_banner)) {

            //currency code begin
            $cid = $request->get('currency_id');
            $currency = TblCurrency::where('id', $cid)->first();
            $short_code = $currency->short_code;
            //currency code end

            $token_id = request()->token;
            $total_amount = request()->final_total_amount;
            $stripe = Stripe::charges()->create([
                'description' => 'Classifieds',
                'shipping' => [
                    'name' => $get_user_info->name,
                    'address' => [
                        'line1' => '510 Townsend St',
                        'postal_code' => '98140',
                        'city' => 'San Francisco',
                        'state' => 'CA',
                        'country' => 'US',
                    ],
                ],
                'source' => $token_id,
                'currency' => $short_code,
                'amount' => $total_amount
            ]);
            $result = $stripe["status"];
            if ($result == "succeeded") {
                $stripe_paid_id = $stripe["id"];
                TblBannerAdvertisement::create([
                    "currency_id" => $currency->default_currency_id,
                    "payment_id" => $stripe_paid_id,
                    "user_id" => $user_id,
                    "web_banner" => $web_banner,
                    "app_banner" => $app_banner,
                    "web_link" => request()->web_link,
                    "app_link" => request()->app_link,
                    "start_date" => date('Y-m-d', strtotime(request()->start_date)),
                    "end_date" => date('Y-m-d', strtotime(request()->end_date)),
                    "payment_type" => "stripe",
                    "live_days" => request()->live_days,
                    "page" => request()->banner_display_page,
                    "category_id" => !empty(request()->banner_category) ? request()->banner_category : "",
                    "total_amount" => $total_amount,
                    "payment_status" => "completed"
                ]);

                // sent notification
                    $settings = Setting::get_logos();
                    $site_name = $settings['name'];

                    $auth_user = auth()->id();
                    $get_user_info = User::where('id', $auth_user)->first();
                    $slug = url('/my-banner-ads');
                    $get_admin = User::role('superadmin')->get();
                    $admin_id = $get_admin[0]->id;
            
                    $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
                    $message = array("notifydata" => array('to_id' => $auth_user, 'from_id' => $admin_id, 'message' => "Your Stripe Payment done successfully for Banner Advertisement.", 'notify_from' => 'stripe_banner_add', 'notify_title' => "New Banner Advertisement Applied In ".$site_name." !..", 'post_id' => "", 'slug' => $slug));
            
                    TblPost::send_push_notification($fcmid, $message);

                // sent notification end


                \Session::put('payment_nofy', 'Payment done successfully - Stripe');
                $returnvalue = "success";
            }
        }
        return $returnvalue;
    }

    // buynow orders
    public function buynow_stripe_orders(Request $request)
    {
        $returnvalue = "failed";
        $token_id = request()->tokenId;
        $total_amount = request()->total_amount;
        $address_id = request()->shipping_address_id;
        $post_id = request()->post_id;
        $user_id = Auth::id();
        $get_user_info = User::where('id', $user_id)->first();
        $post_info = TblPost::where('id', $post_id)->first();
        $get_seller_info = User::where('id', $post_info->user_id)->first();
        $currency = TblCurrency::where('id', $post_info->currency_id)->first();
        $get_private_key = User_profile::where('user_id', $post_info->user_id)->pluck('stripe_private_key')->first();
        if (!empty($get_private_key)) {
            // \Stripe\Stripe::setApiKey($get_private_key);
            // try{

            
            // // $stripe = Stripe::charges()->create([
            // //     'description' => 'Payment for banner advertisements',
            // //     'shipping' => [
            // //         'name' => $get_user_info->name . " - " . $get_user_info->email,
            // //         'address' => [
            // //             'line1' => '510 Townsend St',
            // //             'postal_code' => '98140',
            // //             'city' => 'San Francisco',
            // //             'state' => 'CA',
            // //             'country' => 'US',
            // //         ],
            // //     ],
            // //     'source' => $token_id,
            // //     'currency' => 'usd',
            // //     'amount' => $total_amount
            // // ]);
            //   // Create a payment intent instead of directly charging the card
            //   $stripe =\Stripe\Charge::create([
            //     'amount' => $total_amount,
            //     'currency' => 'usd',
            //     'description' => 'Payment for Buy Now Orders',
            //     'payment_method_types' => ['card'],
            //     'confirm' => true,
            //     'payment_method' => $token_id, // Use token as the payment method
            //     'return_url' => 'https://justreused.com', // Change this to your actual return URL

            // ]);
        
           
          
          
            // $result = $stripe["status"];
            // // dd($result);
            // if ($result == "succeeded") {
                $stripe_paid_id = $token_id;
                $address = TblShippingAddress::where('id', '1')->first();
                // dd($address);
                $orderid = TblBuynowOrder::create([
                    'orderId' => "1",
                    'user_id' => $user_id,
                    'post_id' => $post_id,
                    'seller_id' => $post_info->user_id,
                    'shipping_address' => $address_id,
                    'shipping_add_name' => $address->name,
                    'shipping_add_country' => $address->country,
                    'shipping_add_state' => $address->state,
                    'shipping_add_city' => $address->city,
                    'shipping_add_address1' => $address->address_1,
                    'shipping_add_address1' => $address->address_2,
                    'shipping_add_zipcode' => $address->zipcode,
                    'shipping_add_phone_number' => $address->phone_number,
                    'price' => $post_info->price,
                    'shipping_fee' => $post_info->shipping_rate,
                    'total' => $total_amount,
                    'payment_status' => "completed",
                    'payment_id' => $stripe_paid_id,
                    'currency_id' => $currency->default_currency_id
                ])->id;
                $get_last_order = TblBuynowOrder::where('id', $orderid)->first();
                $get_last_order->update([
                    "orderId" => "Order" . $orderid,
                ]);
                // sent notification start
                $settings = Setting::get_logos();
                $site_name = $settings['name'];

                $slug = URL::to('my-buynow/sales');

                $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                $message1 = array("notifydata" => array('to_id' => $post_info->user_id, 'from_id' => $user_id, 'message' => "New buy-now requested by " . $get_user_info->name . "!. Post Name - " . $post_info->title, 'notify_from' => 'buynow_request', 'notify_title' => "New BuyNow Request In ".$site_name." !..", 'post_id' => $post_id, 'slug' => $post_info->slug));

                TblPost::send_push_notification($fcmid, $message1);


                $mail_data = array("send_maildata" => array('to_id' => $post_info->user_id, 'message' => "New buy-now requested by " . $get_user_info->name . "!. Post Name - " . $post_info->title, 'subject' => "New BuyNow Request In ".$site_name." !..", 'ad_url' => $slug));
                $mail_key = "post_buy_now_request";
                Setting::notification_mail($mail_data, $mail_key);


                // sent notification end

                // update post sold status
                $post_sold_status = TblPost::where('id', $post_id)->first();
                $post_sold_status->update([
                    "sold_status" => 1,
                ]);
                \Session::put('payment_nofy', 'Payment done successfully - Stripe');
                $returnvalue = "success";
        //     }
        // }catch (\Exception $e) {
        //     // Handle exceptions
        //     $returnvalue = $e->getMessage();
        //     dd($e,$returnvalue);
        // }
        }
        return $returnvalue;
    }

    public function stripe_refund_payment(Request $request)
    {
        $returnvalue = "failed";
        
            //start check demo user
            $isDemoUser = User::isDemoUser();
            if($isDemoUser["result"]==true)
            {
                return response()->json($returnvalue);
                exit;
            }
            //end check demo user

        $id = request()->id;
        $type = request()->type;
        
        if ($type == "banner_ads") {
            $get_charge_id = TblBannerAdvertisement::where('id', $id)->first();
            if ($get_charge_id->payment_type == "stripe") {
                $refund = Stripe::refunds()->create($get_charge_id->payment_id, $get_charge_id->total_amount, [
                    'reason' => 'requested_by_customer'
                ]);
                if ($refund['status'] == "succeeded") {
                    $get_charge_id->update([
                        'refund_id' => $refund['id'],
                        'status' => "refunded",
                    ]);
                    $returnvalue = "success";

                    // send notification start
                    $settings = Setting::get_logos();
                    $site_name = $settings['name'];

                    $user_id = $get_charge_id->user_id;
                    $admin_user = User::role('SuperAdmin')->first();
                    $slug = URL::to('/my-banner-ads');

                    $get_user_info = User::where('id', $user_id)->first();

                    $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";

                    $message1 = array("notifydata" => array('to_id' => $user_id, 'from_id' => $admin_user->id, 'message' => " Cancelled your banner advertisement request and Your amount has been refunded successfully!", 'notify_from' => 'banner_ads_refund', 'notify_title' => "Cancelled Banner Advertisement Request In ".$site_name." !..", 'post_id' => "", 'slug' => $slug));

                    TblPost::send_push_notification($fcmid, $message1);


                    $mail_data = array("send_maildata" => array('to_id' => $user_id, 'message' => "Cancelled your banner advertisement request and Your amount has been refunded successfully!", 'subject' => "Cancelled Banner Advertisement Request In ".$site_name." !..", 'ad_url' => $slug));
                    $mail_key = "banner_ad_refund";
                    Setting::notification_mail($mail_data, $mail_key);

                    // send notification end


                }
            }
        } else if ($type == "buynow") {
            $get_charge_id = TblBuynowOrder::where('id', $id)->first();
            $get_private_key = User_profile::where('user_id', $get_charge_id->seller_id)->pluck('stripe_private_key')->first();
            if (!empty($get_private_key)) {
                Stripe::setApiKey($get_private_key);
                $refund = Stripe::refunds()->create($get_charge_id->payment_id, $get_charge_id->total, [
                    'reason' => 'requested_by_customer'
                ]);
                if ($refund['status'] == "succeeded") {
                    $get_charge_id->update([
                        'refund_id' => $refund['id'],
                        "order_status" => "cancelled",
                    ]);
                    $update_sold_status = TblPost::where('id', $get_charge_id->post_id)->first();
                    $update_sold_status->update([
                        "sold_status" => 0,
                    ]);
                    $returnvalue = "success";

                    //send mail start
                    $settings = Setting::get_logos();
                    $site_name = $settings['name'];

                    $user_id = $get_charge_id->user_id;
                    $post_id = $get_charge_id->post_id;
                    $seller_id = $get_charge_id->seller_id;
                    $slug = URL::to('/my-buynow/orders');

                    $get_user_info = User::where('id', $user_id)->first();
                    $get_post_info = TblPost::where('id', $post_id)->first();

                    $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
                    $message1 = array("notifydata" => array('to_id' => $user_id, 'from_id' => $seller_id, 'message' => "Cancelled your Buy-now request and Your amount has been refunded successfully!", 'notify_from' => 'buynow_cancel', 'notify_title' => "Post BuyNow cancel On ".$site_name." !..", 'post_id' => $post_id, 'slug' => $slug));

                    TblPost::send_push_notification($fcmid, $message1);


                    $mail_data = array("send_maildata" => array('to_id' => $user_id, 'message' => "Cancelled your Buy-now request and Your amount has been refunded successfully!", 'subject' => "Cancelled Buy-now Request In ".$site_name." !..", 'ad_url' => $slug));
                    $mail_key = "post_buy_now_cancel";
                    Setting::notification_mail($mail_data, $mail_key);


                    //send mail end

                }
            }
        }
        return response()->json($returnvalue);
    }

    public function stripe_proceed_add_post(Request $request)
    {
        $returnvalue = "failed";
        $token_id = request()->token;
        $total_amount = request()->total_amount;
        $get_user_info = User::where('id',Auth::user()->id)->first();
        $currency_symbol = Setting::get_admin_default_currency();
        $stripe = Stripe::charges()->create([
            'description' => 'Add post with package',
            'shipping' => [
                'name' => $get_user_info->name . " - " . $get_user_info->email,
                'address' => [
                    'line1' => '510 Townsend St',
                    'postal_code' => '98140',
                    'city' => 'San Francisco',
                    'state' => 'CA',
                    'country' => 'US',
                ],
            ],
            'source' => $token_id,
            'currency' => $currency_symbol->short_code,
            'amount' => $total_amount
        ]);
        $result = $stripe["status"];
        if ($result == "succeeded") {
            $stripe_paid_id = $stripe["id"];           
            \Session::put('payment_nofy', 'Payment done successfully - Stripe');
            $returnvalue = "success";
        }
        return json_encode(array("result" => $returnvalue, "stripe_id" => $stripe_paid_id));
    }
}
