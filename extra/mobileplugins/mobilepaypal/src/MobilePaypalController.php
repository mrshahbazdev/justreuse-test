<?php

namespace Mobileplugins\Mobilepaypal;

use App\Models\Package;
use App\Models\TblBulkPackPayment;
use App\Models\TblPayment;
use App\Models\TblPostedAdPackageInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Srmklive\PayPal\Services\ExpressCheckout;
use Illuminate\Support\Facades\URL;
use App\Models\TblPaymentsMethod;

class MobilePaypalController
{
    

    protected $provider;
    public function __construct()
    {
        // get paypal config info from payment table
        $payment_configuration = TblPaymentsMethod::where('name',"paypal")->first();
        $keys_from_db = json_decode($payment_configuration->keys_value,true); 
        if (str_contains($keys_from_db[3]["PAYPAL_MODE"], 'sandbox')) {
           $sandbox = array(
               "username" => $keys_from_db[0]["PAYPAL_USERNAME"],
               "password" => $keys_from_db[1]["PAYPAL_PASSWORD"],
               "secret" => $keys_from_db[2]["PAYPAL_SECRET_KEY"],
               "app_id"=>"APP-80W284485P519543T",
               "certificate" => "",
          );
          $live = array(
              "username"=>"",
              "password"=>"",
              "secret"=>"",
              "certificate"=>"",
              "app_id"=>""
          );
        }else if (str_contains($keys_from_db[3]["PAYPAL_MODE"], 'live')) {
           $live = array(
                "username" => $keys_from_db[0]["PAYPAL_USERNAME"],
                "password" => $keys_from_db[1]["PAYPAL_PASSWORD"],
                "secret" => $keys_from_db[2]["PAYPAL_SECRET_KEY"],
                "app_id"=>"APP-80W284485P519543T",
                "certificate" => "",
           );
           $sandbox = array(
               "username"=>"",
               "password"=>"",
               "secret"=>"",
               "certificate"=>"",
               "app_id"=>""
           );
       }

       $configdata = array(
           "mode"=>strtolower($keys_from_db[3]["PAYPAL_MODE"]),
           "sandbox"=>$sandbox,
           "live" => $live,
           'payment_action' => 'Sale', // Can only be 'Sale', 'Authorization' or 'Order'
           'currency'       => 'USD',
           'billing_type'   => 'MerchantInitiatedBilling',
           'notify_url'     => '', // Change this accordingly for your application.
           'locale'         => '', // force gateway language  i.e. it_IT, es_ES, en_US ... (for express checkout only)
           'validate_ssl'   => true, // Validate SSL when creating api client.
       );
       $this->provider = new ExpressCheckout();
       $this->provider->setApiCredentials($configdata);  
    }



public function ajax_payment()
{
    $request = request()->all();
    
    $post_id =$request['post_id'];
    $is_exist = TblPayment::where('post_id',$post_id)->where('active','1')->get();
    if($is_exist->count()>0){  
        \Session::put('payment_nofy', 'Payment already done for this post. Please check it.');
        return redirect('/');
    }
    else{
    $pack_amt  = $request['pack_amt'];
    $rand_str = substr(uniqid(),0,8);
    $rand_str = str_shuffle($rand_str);
    $billnum = $rand_str.'-'.date('Ymd:his');

    //insert first process
    $user_id = Auth::user()->id;
    $live_days = $request['live_days'];
    $curr_date = date('Y-m-d H:i:s');
    $end_date = date('Y-m-d H:i:s', strtotime($curr_date."+".$live_days." days"));
    $package_id = $request['package_id'];
    $payment_type = $request['payment_type'];
    $payment_loc_ref_id = $billnum;
    $coupon_id = ($request['coupon_id']==null)?"":$request['coupon_id'];

    TblPayment::create([
        "s_payment_id"=>"",
        "user_id"=>$user_id,
        "post_id"=>$post_id,
        "start_date"=>$curr_date,
        "end_date"=>$end_date,
        "live_days"=>$live_days,
        "package_amount"=>$pack_amt,
        "active"=>"0",
        "payment_loc_ref_id"=>$payment_loc_ref_id,
        "payment_type"=>$payment_type,
        "package_id"=>$package_id,
        "coupon_id"=>$coupon_id
    ]);
    //insert first process


    $data = [];
    $data['items'] = [
        [
            'name' => 'myclassified'.$billnum.'.com',
            'price' => $pack_amt,
            'desc'  => 'Description for myclassified'.$billnum.'.com',
            'qty' => 1
        ]

    ];

    $data['invoice_id'] = $billnum;
    $data['invoice_description'] = "Order #{$data['invoice_id']} Invoice";
    $data['return_url'] = URL::to('paypal-payment-success');
    $data['cancel_url'] = URL::to('payment/cancel');
    $data['total'] = $pack_amt;

    //$provider = new ExpressCheckout;
    $response = $this->provider->setExpressCheckout($data);
    $response = $this->provider->setExpressCheckout($data, true);

    if($response['ACK']=="Success"){
        return redirect($response['paypal_link']);
    }else{
        \Session::put('payment_nofy', 'Paypal Merchant account is invalid, please try again later- Paypal');
        return redirect('/');
       }     
    }

}

public function ajax_success(Request $request)
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

     $rde = $this->provider->doExpressCheckoutPayment($data, $token, $payerId);

     
     
     $tran_id = $rde['PAYMENTINFO_0_TRANSACTIONID'];
     $trans_status = $rde['PAYMENTINFO_0_PAYMENTSTATUS'];

    if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
        //dd('Your payment was successfully. You can create success page here.');
        
        $upnode = TblPayment::where('payment_loc_ref_id',$invoice_id);
        $upnode->update([
            's_payment_id'=>$tran_id,
            'payment_status'=>$trans_status,
            'active'=>'1'
        ]);
        $post_id = $upnode->get()[0]['post_id'];
        $release_from_type_free = TblPostedAdPackageInfo::where('post_id',$post_id);
        $release_from_type_free->update([
            "active"=>"0"
        ]);


        \Session::put('payment_nofy', 'Payment done successfully - Paypal');

        return redirect('/');

        //return "success";

    }
    dd('Something is wrong.');
}

public function ajax_cancel()
{
    \Session::put('payment_nofy', 'Payment cancelled - Paypal');
    return redirect('/');
}
//AJAX PROCESS END





//Bulk process begin
public function ajax_bulk_payment()
{
    $request = request()->all();

    $packs = explode(',',$request['package_id']);   
    // dd(explode(',',$package_id));
    // exit;
    //$pack_amt  = $request['pack_amt'];
    $rand_str = substr(uniqid(),0,8);
    $rand_str = str_shuffle($rand_str);
    $billnum = $rand_str.'-'.date('Ymd:his');
    $payment_loc_ref_id = $billnum;
    //insert first process
    $user_id = $request['uid'];
    $curr_date = date('Y-m-d H:i:s');
    //$curr_date = date('Y-m-d H:i:s',strtotime('-2 day',strtotime($curr_date)));//for testing purpose used this lie
    
    $payment_type = $request['payment_type'];

    foreach($packs as $p)
    {
        $data = Package::where('id',$p)->get();
       
        // dd($data);
        
        $live_days = $data[0]['duration'];
        $end_date = date('Y-m-d H:i:s', strtotime($curr_date."+".$live_days." days"));
        $package_id = $p;
        
        $pack_amt  = $data[0]['price'];
    
        TblBulkPackPayment::create([
            "s_payment_id"=>"",
            "user_id"=>$user_id,
            "start_date"=>$curr_date,
            "end_date"=>$end_date,
            "live_days"=>$live_days,
            "package_amount"=>$pack_amt,
            "active"=>"0",
            "payment_loc_ref_id"=>$payment_loc_ref_id,
            "payment_type"=>$payment_type,
            "package_id"=>$package_id
        ]);
    }

    //insert first process


    $data = [];
    $data['items'] = [
        [
            'name' => 'myclassified bulkpack'.$billnum.'.com',
            'price' => $pack_amt,
            'desc'  => 'Description for bulk pack myclassified'.$billnum.'.com',
            'qty' => 1
        ]

    ];

    $data['invoice_id'] = $billnum;
    $data['invoice_description'] = "Order #{$data['invoice_id']} Invoice";
    $data['return_url'] = URL::to('paypal-payment-bulk-success');
    $data['cancel_url'] = URL::to('payment/cancel');
    $data['total'] = $pack_amt;

    //$provider = new ExpressCheckout;
    $response = $this->provider->setExpressCheckout($data);
    $response = $this->provider->setExpressCheckout($data, true);

    if($response['ACK']=="Success"){
        return redirect($response['paypal_link']);
    }else{
        \Session::put('payment_nofy', 'Paypal Merchant account is invalid, please try again later- Paypal');
        return redirect('/');
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

     $rde = $this->provider->doExpressCheckoutPayment($data, $token, $payerId);

     
     
     $tran_id = $rde['PAYMENTINFO_0_TRANSACTIONID'];
     $trans_status = $rde['PAYMENTINFO_0_PAYMENTSTATUS'];

    if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
        //dd('Your payment was successfully. You can create success page here.');
        
        $upnode = TblBulkPackPayment::where('payment_loc_ref_id',$invoice_id);
        $upnode->update([
            's_payment_id'=>$tran_id,
            'payment_status'=>$trans_status,
            'active'=>'1'
        ]);
        // $post_id = $upnode->get()[0]['post_id'];
        // $release_from_type_free = TblPostedAdPackageInfo::where('post_id',$post_id);
        // $release_from_type_free->update([
        //     "active"=>"0"
        // ]);

        //start -clear cart pack
        $cart = Session::get('cart-selected-bulk-packs');
        if(!empty($cart)){
            Session::forget('cart-selected-bulk-packs');
        }
        //end - clear cart pack

        Session::put('payment_nofy', 'Payment done successfully for bulk package - Paypal');

        return redirect('/');

        //return "success";

    }
    dd('Something is wrong.');
}
//Bulk process end



}