<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Package;
use App\Models\TblPost;
use App\Models\TblBulkPackPayment;
use App\Models\TblPayment;
use App\Models\User;
use App\Models\TblPostedAdPackageInfo;
use App\Models\TblBannerAdvertisement;
use App\Models\TblPostCheckout;
use App\Models\TblShippingAddress;
use App\Models\TblBuynowOrder;
use App\Models\TblCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User_profile;
use Illuminate\Support\Facades\URL;
use App\Models\Setting;
use Stripe;

class StripePaymentComponent extends Component
{
    public function render()
    {
        return view('livewire.stripe_payment.stripe-payment-component');
    }

    public function stripe_app_bulk_pay(Request $request)
    {

        $returnvalue = "failed";
        $pack_amt  = $request->get('pack_amt');
        $token_id = $request->get('tokenId');
        $default_currency = 1;
        $short_code = "USD";
        if (!empty($cid)) {
            $get_currency = TblCurrency::where('id', $cid)->first();
            $default_currency = $get_currency->default_currency_id;
            $short_code = $get_currency->short_code;
        }
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
        $user_id = $request->get('uid');

        $packs = explode(',', $request->get('package_id'));
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
                    'currency_id' => $default_currency
                ]);
            }

                // sent notification

                $settings = Setting::get_logos();
                $site_name = $settings['name'];
    
                $auth_user = $user_id;
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

    public function stripe_app_card_payment(Request $request)
    {
        $returnvalue = "failed";
        $post_id = $request->get('post_id');
        $cid = $request->get('cid');
        $check_post_package = TblPost::check_post_expired($post_id);
        $default_currency = 1;
        $short_code = "USD";
        if (!empty($cid)) {
            $get_currency = TblCurrency::where('id', $cid)->first();
            $default_currency = $get_currency->default_currency_id;
            $short_code = $get_currency->short_code;
        }


        $is_exist = TblPayment::where('post_id', $post_id)->where('active', '1')->get();
        if (($check_post_package['expired'] != "Expired" || $check_post_package['expired'] == "") && $is_exist->count() > 0) {
            \Session::put('payment_nofy', 'Payment already done for this post. Please check it.');
            $returnvalue = "failed";
        } else {
            $pack_amt  = $request->get('pack_amt');
            $token_id = $request->get('tokenId');
            $user_id = $request->get('uid');
            $get_user_info = User::where('id', $user_id)->first();
            $stripe = Stripe::charges()->create([
                'description' => 'Software development services',
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
            $coupon_id = !empty($request->get('coupon_id')) ? $request->get('coupon_id') : "";
            $live_days = $request->get('live_days');
            $curr_date = date('Y-m-d H:i:s');
            $end_date = date('Y-m-d H:i:s', strtotime($curr_date . "+" . $live_days . " days"));
            $result = $stripe["status"];
            if ($result == "succeeded") {
                $stripe_paid_id = $stripe["id"];
                $release_from_type_payment = TblPayment::where('post_id', $post_id);
                $release_from_type_payment->update([
                    "active" => "0"
                ]);
                $last_insert_id = TblPayment::create([
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
                    'currency_id' => $default_currency
                ])->id;
                $release_from_type_payment = TblPayment::where('id', $last_insert_id);
                $release_from_type_payment->update([
                    "active" => "1"
                ]);

                $release_from_type_free = TblPostedAdPackageInfo::where('post_id', $post_id);
                $release_from_type_free->update([
                    "active" => "0"
                ]);

                // sent notification
                    $settings = Setting::get_logos();
                    $site_name = $settings['name'];
        
                    $auth_user = $user_id;
                    $get_user_info = User::where('id', $auth_user)->first();
                    $get_post_info = TblPost::where('id', $post_id)->first();
                    $slug = url('/post');
                    $get_admin = User::role('superadmin')->get();
                    $admin_id = $get_admin[0]->id;
            
                    $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
                    $message = array("notifydata" => array('to_id' => $auth_user, 'from_id' => $admin_id, 'message' => "Your Stripe Payment done successfully for single ad package. Post Name - " . $get_post_info->title, 'notify_from' => 'stripe_single_package', 'notify_title' => "Single Ad Package Bought In ".$site_name." !..", 'post_id' => $post_id, 'slug' => $slug));
            
                    TblPost::send_push_notification($fcmid, $message);

                // sent notification end

                \Session::put('payment_nofy', 'Payment done successfully - Card');
                $returnvalue = "success";
            }
        }
        return $returnvalue;
    }

    public function stripe_app_banner_ads(Request $request)
    {
        $returnvalue = "failed";
        $lid = $request->get("lid");
        if (!empty($lid)) {
            $token_id = $request->get('tokenId');
            $total_amount = $request->get('pack_amt');
            $user_id = $request->get('uid');
            $get_user_info = User::where('id', $user_id)->first();
            $cid = $request->get('cid');
            $default_currency = 1;
            $short_code = "USD";
            if (!empty($cid)) {
                $get_currency = TblCurrency::where('id', $cid)->first();
                $default_currency = $get_currency->default_currency_id;
                $short_code = $get_currency->short_code;
            }
            $stripe = Stripe::charges()->create([
                'description' => 'Payment for banner advertisements',
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
                'currency' => $short_code,
                'amount' => $total_amount
            ]);
            $result = $stripe["status"];
            if ($result == "succeeded") {
                $stripe_paid_id = $stripe["id"];
                $get_last_data = TblBannerAdvertisement::where('id', $lid)->first();
                $get_last_data->update([
                    "active" => "1",
                    "payment_loc_ref_id" => "-",
                    "payment_status" => "completed",
                    "payment_id" => $stripe_paid_id,
                    "currency_id" => $default_currency
                ]);
                \Session::put('payment_nofy', 'Payment done successfully - Stripe');
                $returnvalue = "success";
            }
        }
        return $returnvalue;
    }

    public function app_stripe_buynow(Request $request)
    {
        $returnvalue = "failed";
        $lid = $request->get("lid");
        if (!empty($lid)) {
            $token_id = $request->get('tokenId');
            $total_amount = $request->get('pack_amt');
            $user_id = $request->get('uid');
            $currency_id = $request->get('cid');
            $get_currency = TblCurrency::where('id', $currency_id)->first();
            $currencyid = !empty($get_currency->short_code) ? $get_currency->short_code : "USD";
            $get_user_info = User::where('id', $user_id)->first();
            $get_checkout = TblPostCheckout::where('id', $lid)->first();
            $post_info = TblPost::where('id', $get_checkout->post_id)->first();
            $get_seller_info = User::where('id', $post_info->user_id)->first();
            $get_private_key = User_profile::where('user_id', $post_info->user_id)->pluck('stripe_private_key')->first();
            if (!empty($get_private_key)) {
                Stripe::setApiKey($get_private_key);
                $stripe = Stripe::charges()->create([
                    'description' => 'Payment for banner advertisements',
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
                    'currency' => $currencyid,
                    'amount' => $total_amount
                ]);
                $result = $stripe["status"];
                if ($result == "succeeded") {
                    $stripe_paid_id = $stripe["id"];
                    $address = TblShippingAddress::where('id', $get_checkout->shipping_address)->first();
                    $orderid = TblBuynowOrder::create([
                        'orderId' => "1",
                        'user_id' => $user_id,
                        'post_id' => $get_checkout->post_id,
                        'seller_id' => $get_checkout->seller_id,
                        'shipping_address' => $get_checkout->shipping_address,
                        'shipping_add_name' => $address->name,
                        'shipping_add_country' => $address->country,
                        'shipping_add_state' => $address->state,
                        'shipping_add_city' => $address->city,
                        'shipping_add_address1' => $address->address_1,
                        'shipping_add_address2' => $address->address_2,
                        'shipping_add_zipcode' => $address->zipcode,
                        'shipping_add_phone_number' => $address->phone_number,
                        'price' => $get_checkout->price,
                        'shipping_fee' => $get_checkout->shipping_fee,
                        'total' => $get_checkout->order_total,
                        'payment_status' => "completed",
                        'payment_id' => $stripe_paid_id,
                        'currency_id' => $get_currency->default_currency_id
                    ])->id;
                    $get_last_order = TblBuynowOrder::where('id', $orderid)->first();
                    $get_last_order->update([
                        "orderId" => "Order" . $orderid,
                    ]);

                    // sent notification start
                    $slug = URL::to('my-buynow/sales');

                    $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                    $message1 = array("notifydata" => array('to_id' => $post_info->user_id, 'from_id' => $user_id, 'message' => "New buy-now requested by " . $get_user_info->name . "!. Post Name - " . $post_info->title, 'notify_from' => 'buynow_request', 'notify_title' => "New BuyNow Request In Letgo!..", 'post_id' => $get_checkout->post_id, 'slug' => $post_info->slug));

                    TblPost::send_push_notification($fcmid, $message1);


                    $mail_data = array("send_maildata" => array('to_id' => $post_info->user_id, 'message' => "New buy-now requested by " . $get_user_info->name . "!. Post Name - " . $post_info->title, 'subject' => "New BuyNow Request In Letgo!..", 'ad_url' => $slug));
                    $mail_key = "post_buy_now_request";
                    Setting::notification_mail($mail_data, $mail_key);


                    // sent notification end



                    // update post sold status
                    $post_sold_status = TblPost::where('id', $get_checkout->post_id)->first();
                    $post_sold_status->update([
                        "sold_status" => 1,
                    ]);
                    $get_checkout->delete();
                    \Session::put('payment_nofy', 'Payment done successfully - Stripe');
                    $returnvalue = "success";
                }
            }
        }
        return $returnvalue;
    }
}
