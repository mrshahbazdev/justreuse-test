<?php

namespace Postplugins\Buynow;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\TblPost;
use App\Models\TblShippingAddress;
use App\Models\TblPaymentsMethod;
use App\Models\TblBuynowOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\TblCourierInfo;
use App\Models\TblDefaultCurrency;
use App\Models\Setting;
use App\Models\TblAdminCountry;
use App\Models\TblChat;
use PDF;
use App\Models\TblPostMethod;
use Illuminate\Support\Facades\URL;

class BuynowController
{

    public $incoming, $outgoing, $success, $failed;

    public function render()
    {
    }


    public function check_method_is_active()
    {
        
        $resut = 0;
        $post_methods = TblPostMethod::get_active_post_methods();
        if (!empty($post_methods)) {
            $check_banner_ads = $post_methods->pluck('name')->toArray();
            if (in_array("buynow", $check_banner_ads)) {
                $resut = 1;
            }
        }
        return $resut;
    }


    public function new_checkout()
    {
        $res = $this->check_method_is_active();
        if ($res == 1) {
            $address = TblShippingAddress::where('user_id', Auth::id())->whereNull('deleted_at')->orderBy('id', 'desc')->get();
            $address_count = TblShippingAddress::where('user_id', Auth::id())->whereNull('deleted_at')->count();
            $payment_methods = TblPaymentsMethod::where('active', '1')->get();
            $country_list = TblAdminCountry::where('active', 1)->get();
            return view('buynow.src.new-checkout', ["addresses" => $address, "total_address" => $address_count, "payment_methods" => $payment_methods, "country_list" => $country_list]);
        } else {
            abort(404);
        }
    }

    public function update_shipping_address()
    {
        $id = request()->address_id;
        $s_name = request()->name; 
        $s_country = request()->country;
        $s_address1 = request()->address1;
        $s_address2 = request()->address2;
        $s_city = request()->city;
        $s_state = request()->state;
        $s_zip = request()->zipcode;
        $s_phone = request()->phone_number;

        $get_rec = TblShippingAddress::find($id);

        $get_rec->update([
            "name" => $s_name,
            "country" => $s_country,
            "address_1" => $s_address1,
            "address_2" => $s_address2,
            "city" => $s_city,
            "state" => $s_state,
            "zipcode" => $s_zip,
            'phone_number' => $s_phone,
        ]);
        
        return response()->json(['message' => "success"]);

    }     

    public function create_shipping_address()
    {
        $s_name = request()->name;
        $s_address1 = request()->address1;
        $s_address2 = request()->address2;
        $s_city = request()->city;
        $s_state = request()->state; 
        $s_country = request()->ship_add_country;   //US static country before given
        $s_zip = request()->zip;
        $s_phone = request()->phone;
        $check_address = TblShippingAddress::where('user_id', Auth::id())->whereNull('deleted_at')->first();
        $default_address = 0;
        if (empty($check_address)) {
            $default_address = 1;
        }
        TblShippingAddress::create([
            "user_id" => Auth::id(),
            "name" => $s_name,
            "address_1" => $s_address1,
            "address_2" => $s_address2,
            "country" => $s_country,
            "city" => $s_city,
            "state" => $s_state,
            "zipcode" => $s_zip,
            'phone_number' => $s_phone,
            'default_address' => $default_address
        ]);
        return response()->json(['message' => "success"]);
    }

    public function delete_shipping_address()
    {
        $id = request()->id;
        $check_address = TblShippingAddress::where('id', $id)->first();
        $check_address->delete();
        return response()->json(['message' => "success"]);
    }

    public function update_order_status()
    {
        $oid = request()->oid;
        $ostatus = request()->status;
        $s_date = request()->s_date;
        $s_method = request()->s_method;
        $s_service = request()->s_service;
        $s_track_id = request()->s_track_id;
        $s_add_notes = request()->s_add_notes;

        $settings = Setting::get_logos();
        $site_name = $settings['name'];

        if (!empty(request()->s_track_id)) {
            $check_courier = TblCourierInfo::where('order_id', $oid)->first();
            if (empty($check_courier)) {
                TblCourierInfo::create([
                    "order_id" => $oid,
                    "shipping_date" => date('Y-m-d', strtotime($s_date)),
                    "courier_name" => $s_method,
                    "courier_service" => $s_service,
                    "tracking_id" => $s_track_id,
                    "more_info" => $s_add_notes
                ]);
                $update_node = TblBuynowOrder::where('id', $oid)->first();
                $update_node->update([
                    "order_status" => "shipped",
                ]);

                // sent notification start
                $user_id = $update_node->user_id;
                $seller_id = $update_node->seller_id;
                $post_id = $update_node->post_id;
                $get_user_info = User::where('id', $user_id)->first();
                $get_post_info = TblPost::where('id', $post_id)->first();

                $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
                $message1 = array("notifydata" => array('to_id' => $user_id, 'from_id' => $seller_id, 'message' => "Shipped your Buy-now request on " . $get_post_info->title, 'notify_from' => 'buynow_status', 'notify_title' => "Post BuyNow Status On ".$site_name." !..", 'post_id' => $post_id, 'slug' => $get_post_info->slug, 'order_id' => $oid));

                TblPost::send_push_notification($fcmid, $message1);


                // sent notification end


                return response()->json(['result' => "success", "message" => "Order status updated successfully!"]);
            } else {
                $check_courier->update([
                    "order_id" => $oid,
                    "shipping_date" => date('Y-m-d', strtotime($s_date)),
                    "courier_name" => $s_method,
                    "courier_service" => $s_service,
                    "tracking_id" => $s_track_id,
                    "more_info" => $s_add_notes
                ]);
                return response()->json(['result' => "success", "message" => "Shipping details updated successfully!"]);
            }
        } else {
            $update_node = TblBuynowOrder::where('id', $oid)->first();

            $user_id = $update_node->user_id;
            $seller_id = $update_node->seller_id;
            $post_id = $update_node->post_id;
            $get_user_info = User::where('id', $user_id)->first();
            $get_post_info = TblPost::where('id', $post_id)->first();
            $get_seller_info = User::where('id', $seller_id)->first();

            if ($ostatus == "cancelled") {
                $update_sold_status = TblPost::where('id', $update_node->post_id)->first();
                $update_sold_status->update([
                    "sold_status" => 0,
                ]);

                $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
                $message1 = array("notifydata" => array('to_id' => $user_id, 'from_id' => $seller_id, 'message' => "Cancelled your Buy-now request on " . $get_post_info->title, 'notify_from' => 'buynow_status', 'notify_title' => "Post BuyNow Status On ".$site_name." !..", 'post_id' => $post_id, 'slug' => $get_post_info->slug , 'order_id' => $oid));

                TblPost::send_push_notification($fcmid, $message1);
            }
            $update_node->update([
                "order_status" => $ostatus,
            ]);

            // sent notification start

            if ($ostatus != "delivered") {
                $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
                $message1 = array("notifydata" => array('to_id' => $user_id, 'from_id' => $seller_id, 'message' => $ostatus . " your Buy-now request on " . $get_post_info->title, 'notify_from' => 'buynow_status', 'notify_title' => "Post BuyNow Status On ".$site_name." !..", 'post_id' => $post_id, 'slug' => $get_post_info->slug, 'order_id' => $oid));

                TblPost::send_push_notification($fcmid, $message1);
            }

            if ($ostatus == "delivered") {
                $slug = URL::to('my-buynow/sales');

                $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                $message1 = array("notifydata" => array('to_id' => $seller_id, 'from_id' => $user_id, 'message' => "Delivered your Buy-now request on " . $get_post_info->title, 'notify_from' => 'buynow_status', 'notify_title' => "Post BuyNow Status On ".$site_name." !..", 'post_id' => $post_id, 'slug' => $get_post_info->slug, 'order_id' => $oid));

                TblPost::send_push_notification($fcmid, $message1);

                $mail_data = array("send_maildata" => array('to_id' => $seller_id, 'message' => "Delivered your Buy-now request on " . $get_post_info->title, 'subject' => "Delivered BuyNow Request In ".$site_name." !..", 'ad_url' => $slug));
                $mail_key = "post_buy_now_success";
                Setting::notification_mail($mail_data, $mail_key);
            }


            // sent notification end


            return response()->json(['result' => "success", "message" => "Order status updated successfully!"]);
        }
    }

    public function user_buynow_orders()
    {
        $res = $this->check_method_is_active();
        if ($res == 1) {
            $orders = TblBuynowOrder::where('user_id', Auth::id())->orderBy('id', 'desc')->paginate(20);
            return view('buynow.src.my-orders-sales', ["orders" => $orders, "sales" => []]);
        } else {
            abort(404);
        }
    }

    public function seller_buynow_sales()
    {
        $res = $this->check_method_is_active();
        if ($res == 1) {
            $sales = TblBuynowOrder::where('seller_id', Auth::id())->orderBy('id', 'desc')->paginate(20);
            return view('buynow.src.my-orders-sales', ["orders" => [], "sales" => $sales]);
        } else {
            abort(404);
        }
    }

    public function buynow_vieworder()
    {
        $res = $this->check_method_is_active();
        if ($res == 1) {
            $segment = request()->segment(2);
            $orderDetail = TblBuynowOrder::where('orderId', $segment)->first();
            return view('buynow.src.view-buynow-order', ["orderDetail" => $orderDetail]);
        } else {
            abort(404);
        }
    }

    public function get_order_invoice()
    {
        $res = $this->check_method_is_active();
        if ($res == 1) {
            $segment = request()->segment(2);
            $orderDetail = TblBuynowOrder::where('orderId', $segment)->first();
            $settings = Setting::get_logos();
            $post_info = TblPost::where('id', $orderDetail->post_id)->first();
            $post_img = TblChat::getPostImgForList($orderDetail->post_id);
            $seller_info = User::where('id', $post_info->user_id)->first();
            $user_info = User::where('id', $orderDetail->user_id)->first();
            $tracking_detail = "";
            if ($orderDetail->order_status == "shipped" || $orderDetail->order_status == "delivered") {
                $tracking_detail = TblCourierInfo::where('order_id', $orderDetail->id)->first();
            }
            $currency_symbol = TblDefaultCurrency::where('id', $orderDetail->currency_id)->first();
            $pdf = PDF::loadView('buynow.src.order-invoice-template', ["orderDetail" => $orderDetail, "logo" => $settings, "post_info" => $post_info, "post_img" => $post_img, "seller_info" => $seller_info, "user_info" => $user_info, "tracking_info" => $tracking_detail, "currency_symbol" => $currency_symbol]);
            return $pdf->download('OrderInvoice.pdf');
            //return $pdf->setPaper('a4')->stream();
        } else {
            abort(404);
        }
    }

    public function viewOrderInvoice($orderId)
{
    // Similar logic as get_order_invoice but instead of returning a PDF, pass data to a view
    $orderDetail = TblBuynowOrder::where('orderId', $orderId)->first();
    // Fetch other necessary data
    $segment = request()->segment(3);
            $orderDetail = TblBuynowOrder::where('orderId', $segment)->first();
            $settings = Setting::get_logos();
            $post_info = TblPost::where('id', $orderDetail->post_id)->first();
            $post_img = TblChat::getPostImgForList($orderDetail->post_id);
            $seller_info = User::where('id', $post_info->user_id)->first();
            $user_info = User::where('id', $orderDetail->user_id)->first();
            $tracking_detail = "";
            if ($orderDetail->order_status == "shipped" || $orderDetail->order_status == "delivered") {
                $tracking_detail = TblCourierInfo::where('order_id', $orderDetail->id)->first();
            }
            $currency_symbol = TblDefaultCurrency::where('id', $orderDetail->currency_id)->first();
    return view('buynow.src.order-invoice-view', ["orderDetail" => $orderDetail, "logo" => $settings, "post_info" => $post_info, "post_img" => $post_img, "seller_info" => $seller_info, "user_info" => $user_info, "tracking_info" => $tracking_detail, "currency_symbol" => $currency_symbol]);
}

public function viewBack(){
    return redirect()->route('admin/buynow-orders');
}

}
