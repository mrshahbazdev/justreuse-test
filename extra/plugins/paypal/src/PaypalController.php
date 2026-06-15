<?php

namespace Plugins\Paypal;

use App\Models\Package;
use App\Models\TblBulkPackPayment;
use App\Models\TblPayment;
use App\Models\TblPost;
use App\Models\User;
use App\Models\Setting; 
use App\Models\TblPostedAdPackageInfo;
use App\Models\TblCurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Srmklive\PayPal\Services\ExpressCheckout;
use Illuminate\Support\Facades\URL;
use App\Models\TblPaymentsMethod;
use App\Models\TblBannerAdvertisement;

class PaypalController
{

    protected $provider;
    public function __construct()
    {
        // get paypal config info from payment table
        $payment_configuration = TblPaymentsMethod::where('name', "paypal")->first();
        $keys_from_db = json_decode($payment_configuration->keys_value, true);
        if (str_contains($keys_from_db[3]["PAYPAL_MODE"], 'sandbox')) {
            $sandbox = array(
                "username" => $keys_from_db[0]["PAYPAL_USERNAME"],
                "password" => $keys_from_db[1]["PAYPAL_PASSWORD"],
                "secret" => $keys_from_db[2]["PAYPAL_SECRET_KEY"],
                "app_id" => "APP-80W284485P519543T",
                "certificate" => "",
            );
            $live = array(
                "username" => "",
                "password" => "",
                "secret" => "",
                "certificate" => "",
                "app_id" => ""
            );
        } else if (str_contains($keys_from_db[3]["PAYPAL_MODE"], 'live')) {
            $live = array(
                "username" => $keys_from_db[0]["PAYPAL_USERNAME"],
                "password" => $keys_from_db[1]["PAYPAL_PASSWORD"],
                "secret" => $keys_from_db[2]["PAYPAL_SECRET_KEY"],
                "app_id" => "APP-80W284485P519543T",
                "certificate" => "",
            );
            $sandbox = array(
                "username" => "",
                "password" => "",
                "secret" => "",
                "certificate" => "",
                "app_id" => ""
            );
        }
        if (isset($_GET['cid'])) {
            Session::forget('paypal_currency');
            $currency = TblCurrency::where('id', $_GET['cid'])->pluck('short_code')->first();
            Session::put(['paypal_currency' => $currency]);
            $currency_code = $currency;
        } else {
            $currency_code = "USD";
        }


        $configdata = array(
            "mode" => strtolower($keys_from_db[3]["PAYPAL_MODE"]),
            "sandbox" => $sandbox,
            "live" => $live,
            'payment_action' => 'Sale', // Can only be 'Sale', 'Authorization' or 'Order'
            'currency'       =>  $currency_code,
            'billing_type'   => 'MerchantInitiatedBilling',
            'notify_url'     => '', // Change this accordingly for your application.
            'locale'         => '', // force gateway language  i.e. it_IT, es_ES, en_US ... (for express checkout only)
            'validate_ssl'   => true, // Validate SSL when creating api client.
        );

        $this->provider = new ExpressCheckout();

        $this->provider->setApiCredentials($configdata);

        $this->provider->setCurrency($currency_code);
    }

    //AJAX PROCESS START

    public function ajax_payment()
    {
        $request = request()->all();
        $post_id = $request['post_id'];
        $cid = !empty($request['cid']) ? $request['cid'] : "";
        $default_currency_id = 1;
        if (!empty($cid)) {
            $default_currency_id = TblCurrency::where('id', $cid)->pluck('default_currency_id')->first();
        }

        $is_exist = TblPayment::where('post_id', $post_id)->where('active', '1')->get();
        $check_post_package = TblPost::check_post_expired($post_id);
        if (($check_post_package['expired'] != "Expired" || $check_post_package['expired'] == "") && $is_exist->count() > 0) {
            \Session::put('payment_nofy', 'Payment already done for this post. Please check it.');
            return redirect('/');
        } else {
            $pack_amt  = $request['pack_amt'];
            $rand_str = substr(uniqid(), 0, 8);
            $rand_str = str_shuffle($rand_str);
            $billnum = $rand_str . '-' . date('Ymd:his');

            //insert first process
            if (isset($request['uid'])) {
                $user_id = $request['uid'];
                $from_app = 1;
                $from_type = !empty($request['from_type']) ? $request['from_type'] : "";
            } else {
                $user_id = Auth::user()->id;
                $from_app = 0;
                $from_type = "";
            }
            Session::forget('from_app');
            Session::forget('paypal_uid');
            Session::forget('from_type');
            Session::forget('paypal_packid');
            Session::forget('paypal_cid');

            $live_days = $request['live_days'];
            $curr_date = date('Y-m-d H:i:s');
            $end_date = date('Y-m-d H:i:s', strtotime($curr_date . "+" . $live_days . " days"));
            $package_id = $request['package_id'];
            $payment_type = $request['payment_type'];
            $payment_loc_ref_id = $billnum;
            $coupon_id = ($request['coupon_id'] == null) ? "" : $request['coupon_id'];
            $lastInsertedData = TblPayment::latest()->first();
            $order_id = $this->generate_payment_order_id($lastInsertedData->order_id);
            Session::put(['paypal_uid' => $user_id]);
            Session::put(['paypal_packid' => $package_id]);
            Session::put(['from_app' => $from_app]);
            Session::put(['from_type' => $from_type]);
            Session::put(['paypal_cid' => $cid]);

            TblPayment::create([
                "s_payment_id" => "",
                "user_id" => $user_id,
                "post_id" => $post_id,
                "start_date" => $curr_date,
                "end_date" => $end_date,
                "live_days" => $live_days,
                "package_amount" => $pack_amt,
                "active" => "0",
                "payment_loc_ref_id" => $payment_loc_ref_id,
                "payment_type" => $payment_type,
                "package_id" => $package_id,
                'order_id' => $order_id,
                "coupon_id" => $coupon_id,
                'currency_id' => $default_currency_id
            ]);
            //insert first process
            $data = [];
            $data['items'] = [
                [
                    'name' => 'myclassified' . $billnum . '.com',
                    'price' => $pack_amt,
                    'desc'  => 'Description for myclassified' . $billnum . '.com',
                    'qty' => 1
                ]

            ];

            $data['invoice_id'] = $billnum;
            $data['invoice_description'] = "Order #{$data['invoice_id']} Invoice";
            $data['return_url'] = URL::to('paypal-payment-success');
            $data['cancel_url'] = URL::to('paypal-payment-cancel');
            $data['total'] = $pack_amt;

            $response = $this->provider->setExpressCheckout($data);
            $response = $this->provider->setExpressCheckout($data, true);
            if ($response['ACK'] == "Success") {
                return redirect($response['paypal_link']);
            } else {
                if ($from_app == 1) {
                    return redirect('/thankyou?status=failed&type=single&pack_id=' . $package_id . '&cid=' . $cid . '&uid=' . $user_id . '&amount=' . $pack_amt . '&from_type=' . $from_type . '&paid_for=package');
                } else {
                    \Session::put('payment_nofy', 'Paypal Merchant account is invalid, please try again later- Paypal');
                    return redirect('/');
                }
            }
        }
    }

    public function generate_payment_order_id($last_order_id) {
        // If no existing orders, return the first order ID
        if (empty($last_order_id)) {
            return "ORDA00001";
        }
    
        // Extract the numeric part and increment
        $number = intval(substr($last_order_id, 4)) + 1;
    
        // Generate the next order ID
        $next_order_id = "ORDA" . str_pad($number, 5, "0", STR_PAD_LEFT);
    
        return $next_order_id;
    }

    public function ajax_success(Request $request)
    {

        $response = $this->provider->getExpressCheckoutDetails($request->token);

        $token = request()->token;
        $payerId = request()->PayerID;
        $NAME0 = $response['L_NAME0'];
        $PRICE0 = $response['L_AMT0'];
        $DESC0 = $response['L_DESC0'];
        $QTY0 = $response['L_QTY0'];

        $invoice_id = $response['PAYMENTREQUEST_0_INVNUM'];
        $invoice_desc = $response['PAYMENTREQUEST_0_DESC'];
        $total = $response['PAYMENTREQUEST_0_AMT'];

        $data = [];
        $data['items'] = [
            [
                'name' => $NAME0,
                'price' => $PRICE0,
                'desc'  => $DESC0,
                'qty' => $QTY0
            ]

        ];
        $data['invoice_id'] = $invoice_id;
        $data['invoice_description'] = $invoice_desc;
        $data['total'] = $total;
        if (Session::has('paypal_cid')) {
            $currency_code = TblCurrency::where('id', Session::get('paypal_cid'))->pluck('short_code')->first();
            $this->provider->setCurrency($currency_code);
        }
        $rde = $this->provider->doExpressCheckoutPayment($data, $token, $payerId);
        $from_app = Session::get('from_app');
        $package_id = Session::get('paypal_packid');
        $user_id = Session::get('paypal_uid');
        $cid = Session::get('paypal_cid');

        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {

            $tran_id = $rde['PAYMENTINFO_0_TRANSACTIONID'];
            $trans_status = $rde['PAYMENTINFO_0_PAYMENTSTATUS'];

            $upnode = TblPayment::where('payment_loc_ref_id', $invoice_id);
            $upnode->update([
                's_payment_id' => $tran_id,
                'payment_status' => $trans_status,
                'active' => '1'
            ]);
            $post_id = $upnode->get()[0]['post_id'];
            $release_from_type_free = TblPostedAdPackageInfo::where('post_id', $post_id);
            $release_from_type_free->update([
                "active" => "0"
            ]);
            $update_post = TblPost::where('id', $post_id);
            $update_post->update([
                "active" => "1"
            ]);
            if ($from_app == 1) {

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
                $message = array("notifydata" => array('to_id' => $auth_user, 'from_id' => $admin_id, 'message' => "Your paypal Payment done successfully for single ad package. Post Name - " . $get_post_info->title, 'notify_from' => 'paypal_single_package', 'notify_title' => "Single Ad Package Bought In ".$site_name." !..", 'post_id' => $post_id, 'slug' => $slug));
        
                TblPost::send_push_notification($fcmid, $message);
    
                // sent notification end

                $from_type = Session::get('from_type');
                return redirect('/thankyou?status=success&type=single&pack_id=' . $package_id . '&cid=' . $cid . '&uid=' . $user_id . '&amount=' . $PRICE0 . '&from_type=' . $from_type . '&paid_for=package');
            } else {
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
                $message = array("notifydata" => array('to_id' => $auth_user, 'from_id' => $admin_id, 'message' => "Your paypal Payment done successfully for single ad package. Post Name - " . $get_post_info->title, 'notify_from' => 'paypal_single_package', 'notify_title' => "Single Ad Package Bought In ".$site_name." !..", 'post_id' => $post_id, 'slug' => $slug));
        
                TblPost::send_push_notification($fcmid, $message);
    
                // sent notification end

                \Session::put('payment_nofy', 'Payment done successfully - Paypal');
                return redirect('/');
            }
        } else {
            if ($from_app == 1) {
                $from_type = Session::get('from_type');
                return redirect('/thankyou?status=failed&type=single&pack_id=' . $package_id . '&cid=' . $cid . '&uid=' . $user_id . '&amount=' . $PRICE0 . '&from_type=' . $from_type . '&paid_for=package');
            } else {
                \Session::put('payment_nofy', 'Please try again later - Paypal');
                return redirect('/');
            }
        }
    }

    public function ajax_cancel()
    {
        \Session::put('payment_nofy', 'Payment cancelled - Paypal');
        return redirect('/');
    }
    //AJAX PROCESS END
   public function generate_order_id($last_order_id) {
        // If no existing orders, return the first order ID
        if (empty($last_order_id)) {
            return "ORDB00001";
        }
    
        // Extract the numeric part and increment
        $number = intval(substr($last_order_id, 4)) + 1;
    
        // Generate the next order ID
        $next_order_id = "ORDB" . str_pad($number, 5, "0", STR_PAD_LEFT);
    
        return $next_order_id;
    }
    //Bulk process begin
    public function ajax_bulk_payment()
    {
       
        // dd($this->generate_order_id('ORDB59999'));

        $request = request()->all();
        $packs = explode(',', $request['package_id']);
        $rand_str = substr(uniqid(), 0, 8);
        $rand_str = str_shuffle($rand_str);
        $billnum = $rand_str . '-' . date('Ymd:his');
        $payment_loc_ref_id = $billnum;
        Session::forget('from_app');
        Session::forget('paypal_uid');
        Session::forget('from_app');
        Session::forget('paypal_cid');

        $cid = !empty($request['cid']) ? $request['cid'] : "";
        $default_currency_id = 1;
        if (!empty($cid)) {
            $default_currency_id = TblCurrency::where('id', $cid)->pluck('default_currency_id')->first();
        }

        //insert first process
        if (isset($request['uid'])) {
            $user_id = $request['uid'];
            Session::put('payment_from', 'App_Paypal');
            $from_app = 1;
        } else {
            $user_id = Auth::user()->id;
            $from_app = 0;
        }

        Session::put(['paypal_uid' => $user_id]);
        Session::put(['paypal_packid' => $request['package_id']]);
        Session::put(['from_app' => $from_app]);
        Session::put(['paypal_cid' => $cid]);

        $curr_date = date('Y-m-d H:i:s');
        $payment_type = $request['payment_type'];
        $passing_pack_amt = 0;
        foreach ($packs as $p) {
            $data = Package::where('id', $p)->get();
            $live_days = $data[0]['duration'];
            $end_date = date('Y-m-d H:i:s', strtotime($curr_date . "+" . $live_days . " days"));
            $package_id = $p;
            $pack_amt  = $data[0]['price'];
            $passing_pack_amt = $passing_pack_amt + $data[0]['price'];
            $lastInsertedData = TblBulkPackPayment::latest()->first();
            $order_id = $this->generate_order_id($lastInsertedData->order_id);
            TblBulkPackPayment::create([
                "s_payment_id" => "",
                "user_id" => $user_id,
                "start_date" => $curr_date,
                "end_date" => $end_date,
                "live_days" => $live_days,
                "package_amount" => $pack_amt,
                "active" => "0",
                "payment_loc_ref_id" => $payment_loc_ref_id,
                "payment_type" => $payment_type,
                "package_id" => $package_id,
                'order_id'=> $order_id,
                "currency_id" => $default_currency_id
            ]);
        }

        //insert first process
        $data = [];
        $data['items'] = [
            [
                'name' => 'myclassified bulkpack ' . $billnum . '.com',
                'price' => $passing_pack_amt,
                'desc'  => 'Description for bulk pack myclassified ' . $billnum . '.com',
                'qty' => 1
            ]

        ];

        $data['invoice_id'] = $billnum;
        $data['invoice_description'] = "Order #{$data['invoice_id']} Invoice";
        $data['return_url'] = URL::to('paypal-payment-bulk-success');
        $data['cancel_url'] = URL::to('paypal-payment-cancel');
        $data['total'] = $passing_pack_amt;

        //$provider = new ExpressCheckout;
        $response = $this->provider->setExpressCheckout($data);
        $response = $this->provider->setExpressCheckout($data, true);

        $package_id = Session::get('paypal_packid');
        $user_id = Session::get('paypal_uid');

        if ($response['ACK'] == "Success") {
            return redirect($response['paypal_link']);
        } else {
            if ($from_app == 1) {
                return redirect('/thankyou?status=failed&type=bulk&pack_id=' . $package_id . '&cid=' . $cid . '&uid=' . $user_id . '&amount=' . $pack_amt . '&paid_for=package');
            } else {
                \Session::put('payment_nofy', 'Paypal Merchant account is invalid, please try again later- Paypal');
                return redirect('/');
            }
        }
    }

    public function ajax_bulk_payment_success(Request $request)
    {
        //$provider = new ExpressCheckout;
        $response = $this->provider->getExpressCheckoutDetails($request->token);
        $token = request()->token;
        $payerId = request()->PayerID;
        $NAME0 = $response['L_NAME0'];
        $PRICE0 = $response['L_AMT0'];
        $DESC0 = $response['L_DESC0'];
        $QTY0 = $response['L_QTY0'];

        $invoice_id = $response['PAYMENTREQUEST_0_INVNUM'];
        $invoice_desc = $response['PAYMENTREQUEST_0_DESC'];
        $total = $response['PAYMENTREQUEST_0_AMT'];
        $data = [];
        $data['items'] = [
            [
                'name' => $NAME0,
                'price' => $PRICE0,
                'desc'  => $DESC0,
                'qty' => $QTY0
            ]

        ];
        $data['invoice_id'] = $invoice_id;
        $data['invoice_description'] = $invoice_desc;
        $data['total'] = $total;
        if (Session::has('paypal_cid')) {
            $currency_code = TblCurrency::where('id', Session::get('paypal_cid'))->pluck('short_code')->first();
            $this->provider->setCurrency($currency_code);
        }
        $rde = $this->provider->doExpressCheckoutPayment($data, $token, $payerId);

        $from_app = Session::get('from_app');
        $package_id = Session::get('paypal_packid');
        $user_id = Session::get('paypal_uid');
        $cid = Session::get('paypal_cid');

        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {

            $tran_id = $rde['PAYMENTINFO_0_TRANSACTIONID'];
            $trans_status = $rde['PAYMENTINFO_0_PAYMENTSTATUS'];
            $upnode = TblBulkPackPayment::where('payment_loc_ref_id', $invoice_id);
            $upnode->update([
                's_payment_id' => $tran_id,
                'payment_status' => $trans_status,
                'active' => '1'
            ]);

            //start -clear cart pack
            $cart = Session::get('cart-selected-bulk-packs');
            if (!empty($cart)) {
                Session::forget('cart-selected-bulk-packs');
            }
            //end - clear cart pack           

            if ($from_app == 1) {
                
                // sent notification

                $settings = Setting::get_logos();
                $site_name = $settings['name'];

                $auth_user = $user_id;
                $get_user_info = User::where('id', $auth_user)->first();
                $slug = url('/mypackage');
                $get_admin = User::role('superadmin')->get();
                $admin_id = $get_admin[0]->id;
        
                $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
                $message = array("notifydata" => array('to_id' => $auth_user, 'from_id' => $admin_id, 'message' => "Your Paypal Payment done successfully for bulk package.", 'notify_from' => 'paypal_bulk_payment', 'notify_title' => "Bulk Package Bought In ".$site_name." !..", 'post_id' => "", 'slug' => $slug));
        
                TblPost::send_push_notification($fcmid, $message);

                // sent notification end

                return redirect('/thankyou?status=success&type=bulk&pack_id=' . $package_id . '&cid=' . $cid . '&uid=' . $user_id . '&amount=' . $total . '&paid_for=package');
            } else {
                // sent notification

                $settings = Setting::get_logos();
                $site_name = $settings['name'];

                $auth_user = auth()->id();
                $get_user_info = User::where('id', $auth_user)->first();
                $slug = url('/mypackage');
                $get_admin = User::role('superadmin')->get();
                $admin_id = $get_admin[0]->id;
        
                $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
                $message = array("notifydata" => array('to_id' => $auth_user, 'from_id' => $admin_id, 'message' => "Your Paypal Payment done successfully for bulk package.", 'notify_from' => 'paypal_bulk_payment', 'notify_title' => "Bulk Package Bought In ".$site_name." !..", 'post_id' => "", 'slug' => $slug));
        
                TblPost::send_push_notification($fcmid, $message);

                // sent notification end
                Session::put('payment_nofy', 'Payment done successfully for bulk package - Paypal');
                return redirect('/');
            }
        } else {
            if ($from_app == 1) {
                return redirect('/thankyou?status=failed&type=bulk&pack_id=' . $package_id . '&cid=' . $cid . '&uid=' . $user_id . '&amount=' . $total . '&paid_for=package');
            } else {
                Session::put('payment_nofy', 'Payment Failed - Paypal');
                return redirect('/');
            }
        }
    }
    //Bulk process end


    //banner ads start here
    public function bannerads_payment()
    {
        $request = request()->all();

        $last_id = $request['id'];
        $is_exist = TblBannerAdvertisement::where('id', $last_id)->where('active', '0')->count();
        if (empty($is_exist)) {
            \Session::put('payment_nofy', 'Please try again later, banner ads info not saved properly');
            return redirect('/');
        } else {
            $total_amount  = $request['total_amount'];
            $rand_str = substr(uniqid(), 0, 8);
            $rand_str = str_shuffle($rand_str);
            $billnum = $rand_str . '-' . date('Ymd:his');
            if (isset($request['uid'])) {
                $user_id = $request['uid'];
                $from_app = 1;
                $from_type = !empty($request['from_type']) ? $request['from_type'] : "";
            } else {
                $user_id = Auth::user()->id;
                $from_app = 0;
                $from_type = "";
            }
            Session::forget('from_app');
            Session::forget('paypal_uid');
            Session::forget('from_type');
            Session::forget('last_id');
            Session::forget('paypal_cid');

            $payment_loc_ref_id = $billnum;
            $cid = !empty($request['cid']) ? $request['cid'] : "";
            $currency = TblCurrency::where('id', $cid)->first();

            Session::put(['paypal_uid' => $user_id]);
            Session::put(['last_id' => $last_id]);
            Session::put(['from_app' => $from_app]);
            Session::put(['from_type' => $from_type]);
            Session::put(['paypal_cid' => $cid]);

            $data = [];
            $data['items'] = [
                [
                    'name' => 'myclassified' . $billnum . '.com',
                    'price' => $total_amount,
                    'desc'  => 'Description for myclassified' . $billnum . '.com',
                    'qty' => 1
                ]
            ];

            $upnode = TblBannerAdvertisement::where('id', $last_id);
            $upnode->update([
                'payment_loc_ref_id' => $billnum,
                'currency_id'=>$currency->default_currency_id
            ]);

            $data['invoice_id'] = $billnum;
            $data['invoice_description'] = "Order #{$data['invoice_id']} Invoice";
            $data['return_url'] = URL::to('bannerad-paypal-payment-success');
            $data['cancel_url'] = URL::to('bannerad-paypal-payment-cancel');
            $data['total'] = $total_amount;

            $response = $this->provider->setExpressCheckout($data);
            $response = $this->provider->setExpressCheckout($data, true);

            if ($response['ACK'] == "Success") {
                return redirect($response['paypal_link']);
            } else {
                if ($from_app == 1) {
                    return redirect('/thankyou?status=failed&type=bannerads&lid=' . $last_id . '&cid=' . $cid . '&uid=' . $user_id . '&amount=' . $total_amount . '&paid_for=bannerads');
                } else {
                    \Session::put('payment_nofy', 'Paypal Merchant account is invalid, please try again later- Paypal');
                    return redirect('/');
                }
            }
        }
    }

    public function bannerads_paid_success(Request $request)
    {
        $response = $this->provider->getExpressCheckoutDetails($request->token);

        $token = request()->token;
        $payerId = request()->PayerID;
        $NAME0 = $response['L_NAME0'];
        $PRICE0 = $response['L_AMT0'];
        $DESC0 = $response['L_DESC0'];
        $QTY0 = $response['L_QTY0'];

        $invoice_id = $response['PAYMENTREQUEST_0_INVNUM'];
        $invoice_desc = $response['PAYMENTREQUEST_0_DESC'];
        $total = $response['PAYMENTREQUEST_0_AMT'];

        $data = [];
        $data['items'] = [
            [
                'name' => $NAME0,
                'price' => $PRICE0,
                'desc'  => $DESC0,
                'qty' => $QTY0
            ]

        ];
        $data['invoice_id'] = $invoice_id;
        $data['invoice_description'] = $invoice_desc;
        $data['total'] = $total;
        if (Session::has('paypal_cid')) {
            $currency_code = TblCurrency::where('id', Session::get('paypal_cid'))->pluck('short_code')->first();
            $this->provider->setCurrency($currency_code);
        }

        $from_app = Session::get('from_app');
        $last_id = Session::get('last_id');
        $user_id = Session::get('paypal_uid');
        $cid = Session::get('paypal_cid');

        $rde = $this->provider->doExpressCheckoutPayment($data, $token, $payerId);

        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {

            $tran_id = $rde['PAYMENTINFO_0_TRANSACTIONID'];
            $trans_status = $rde['PAYMENTINFO_0_PAYMENTSTATUS'];

            $upnode = TblBannerAdvertisement::where('payment_loc_ref_id', $invoice_id);
            $upnode->update([
                'payment_id' => $tran_id,
                'payment_status' => $trans_status,
                'active' => '1'
            ]);

            if ($from_app == 1) {
                return redirect('/thankyou?status=success&type=bulk&lid=' . $last_id . '&cid=' . $cid . '&uid=' . $user_id . '&amount=' . $total . '&paid_for=bannerads');
            } else {

            // sent notification
                $settings = Setting::get_logos();
                $site_name = $settings['name'];

                $auth_user = auth()->id();
                $get_user_info = User::where('id', $auth_user)->first();
                $slug = url('/my-banner-ads');
                $get_admin = User::role('superadmin')->get();
                $admin_id = $get_admin[0]->id;
        
                $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
                $message = array("notifydata" => array('to_id' => $auth_user, 'from_id' => $admin_id, 'message' => "Your Paypal Payment done successfully for Banner Advertisement.", 'notify_from' => 'paypal_banner_add', 'notify_title' => "New Banner Advertisement Applied In ".$site_name." !..", 'post_id' => "", 'slug' => $slug));
        
                TblPost::send_push_notification($fcmid, $message);

		    // sent notification end

                Session::put('payment_nofy', 'Payment done successfully - Paypal');
                return redirect('/');
            }
        } else {
            if ($from_app == 1) {
                return redirect('/thankyou?status=failed&type=bannerads&lid=' . $last_id . '&cid=' . $cid . '&uid=' . $user_id . '&amount=' . $total . '&paid_for=bannerads');
            } else {
                Session::put('payment_nofy', 'payment has been failed - Paypal');
                return redirect('/');
            }
        }
    }


    public function paypal_refund_payment(Request $request)
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
            if ($get_charge_id->payment_type == "paypal") {
                $transactionId = $get_charge_id->payment_id;
                $response = $this->provider->refundTransaction($transactionId);
                if ($response["ACK"] == "Success") {
                    $get_charge_id->update([
                        'refund_id' => $response['REFUNDTRANSACTIONID'],
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
            //$get_post_info = TblPost::where('id', $post_id)->first();

            $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";

            $message1 = array("notifydata" => array('to_id' => $user_id, 'from_id' => $admin_user->id, 'message' => " Cancelled your banner advertisement request and Your amount has been refunded successfully!", 'notify_from' => 'banner_ads_refund', 'notify_title' => "Cancelled Banner Advertisement Request In ".$site_name." !..", 'post_id' => "", 'slug' => ""));

            TblPost::send_push_notification($fcmid, $message1);


            $mail_data = array("send_maildata" => array('to_id' => $user_id, 'message' => "Cancelled your banner advertisement request and Your amount has been refunded successfully!", 'subject' => "Cancelled Banner Advertisement Request In ".$site_name." !..", 'ad_url' => $slug));
            $mail_key = "banner_ad_refund";
            Setting::notification_mail($mail_data, $mail_key);

            // send notification end

                }
            }
        } else {
        }
        return response()->json($returnvalue);
    }
}
