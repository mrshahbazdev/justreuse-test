<?php
namespace App\Http\Livewire;
use Livewire\Component;
use App\Models\TblPayment;
use App\Models\TblPostedAdPackageInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\URL;
use Srmklive\PayPal\Services\ExpressCheckout;
use Srmklive\PayPal\Traits\PayPalTransactions;


class PaypalPaymentComponent extends Component
{
    protected $provider;
    public function __construct()
    {
        $this->provider = new ExpressCheckout();
    }

    public function render()
    {
        return dd('welcom');
    }


//AJAX PROCESS START

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
        "package_id"=>$package_id
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

    return redirect($response['paypal_link']);
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

//AJAX PROCESS END


}
