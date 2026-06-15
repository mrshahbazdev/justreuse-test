<?php

namespace Postplugins\Exchange;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\TblPost;
use App\Models\Setting;
use App\Models\TblExchangedPost;
use App\Models\TblPostMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class ExchangeController
{

    public $incoming, $outgoing, $success, $failed;

    public function check_method_is_active()
    {
        $resut = 0;
        $post_methods = TblPostMethod::get_active_post_methods();
        if (!empty($post_methods)) {
            $check_banner_ads = $post_methods->pluck('name')->toArray();
            if (in_array("exchange", $check_banner_ads)) {
                $resut = 1;
            }
        }
        return $resut;
    }


    public function exchange_incoming()
    {
        $res = $this->check_method_is_active();
        if ($res == 1) {
            $incomming = TblExchangedPost::where('post_owner_id', Auth::id())->where(function ($q) {
                $q->where('status', 'pending')
                    ->orWhere('status', 'accepted');
            })->orderBy('created_at', 'desc')->paginate(20);
            return view('exchange.src.my-exchange', ['incomings' => $incomming, 'outgoings' => [], 'successes' => [], 'faileds' => []]);
        } else {
            abort(404);
        }
    }

    public function exchange_outgoing()
    {
        $res = $this->check_method_is_active();
        if ($res == 1) {
            $outgoing = TblExchangedPost::where('user_id', Auth::id())->where(function ($q) {
                $q->where('status', 'pending')
                    ->orWhere('status', 'accepted');
            })->orderBy('created_at', 'desc')->paginate(20);
            return view('exchange.src.my-exchange', ['outgoings' => $outgoing, 'incomings' => [], 'successes' => [], 'faileds' => []]);
        } else {
            abort(404);
        }
    }

    public function exchange_successful()
    {
        $res = $this->check_method_is_active();
        if ($res == 1) {
            $success = TblExchangedPost::where('status', 'success')->where(function ($q) {
                $q->where('user_id', Auth::id())
                    ->orWhere('post_owner_id', Auth::id());
            })->orderBy('created_at', 'desc')->paginate(20);
            return view('exchange.src.my-exchange', ['outgoings' => [], 'incomings' => [], 'successes' => $success, 'faileds' => []]);
        } else {
            abort(404);
        }
    }

    public function exchange_failed()
    {
        $res = $this->check_method_is_active();
        if ($res == 1) {
            $failed = TblExchangedPost::where(function ($q) {
                $q->where('status', 'cancelled')
                    ->orWhere('status', 'declined')
                    ->orWhere('status', 'failed');
            })->where(function ($q) {
                $q->where('user_id', Auth::id())
                    ->orWhere('post_owner_id', Auth::id());
            })->orderBy('created_at', 'desc')->paginate(20);
            return view('exchange.src.my-exchange', ['outgoings' => [], 'incomings' => [], 'successes' => [], 'faileds' => $failed]);
        } else {
            abort(404);
        }
    }

    public function add_exchange()
    {
        $exchange_id = request()->exchange_id;
        $post_id = request()->post_id;
        $user_id = request()->user_id;
        $post_owner = request()->post_owner;
        $check_exchange = TblExchangedPost::where('user_id', $user_id)->where('post_id', $post_id)->where('exchanged_post_id', $exchange_id)->first();
        if (empty($check_exchange)) {
            TblExchangedPost::create([
                'user_id' => $user_id,
                'post_owner_id' => $post_owner,
                'exchanged_post_id' => $exchange_id,
                'post_id' => $post_id,
                'block_exchange' => 0
            ]);

            // notification start
            $get_user_info = User::where('id', $user_id)->first();
            $get_post_info = TblPost::where('id', $post_id)->first();
            $get_seller_info = User::where('id', $get_post_info->user_id)->first();
            $slug = URL::to('my-exchange/incoming');
            $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
            $message1 = array("notifydata" => array('to_id' => $get_post_info->user_id, 'from_id' => $user_id, 'message' => " sent exchange request to your product. Post Name - " . $get_post_info->title, 'notify_from' => 'post_exchange', 'notify_title' => "New post exchange In Letgo!..", 'post_id' => $post_id, 'slug' => $get_post_info->slug));
            TblPost::send_push_notification($fcmid, $message1);

            $mail_data = array("send_maildata" => array('to_id' => $get_post_info->user_id, 'message' => $get_user_info->name . " sent exchange request to your product. Post Name - " . $get_post_info->title, 'subject' => "New post exchange In Letgo!..", 'ad_url' => $slug));
            $mail_key = "post_exchange_request";
            Setting::notification_mail($mail_data, $mail_key);
            // notification end
            return response()->json(['message' => "Exchange created successfully!", 'flag' => 1]);
        } else if ($check_exchange->block_exchange == 1) {
            return response()->json(['message' => "You can't exchange this product, this product already blocked by the seller!", 'flag' => 0]);
        } else if ($check_exchange->status != "success") {
            $update_exchange = TblExchangedPost::find($check_exchange->id);
            $update_exchange->update(['status' => 'pending',]);
            // notification start
            $get_user_info = User::where('id', $user_id)->first();
            $get_post_info = TblPost::where('id', $post_id)->first();
            $get_seller_info = User::where('id', $get_post_info->user_id)->first();
            $slug = URL::to('my-exchange/incoming');
            $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
            $message1 = array("notifydata" => array('to_id' => $get_post_info->user_id, 'from_id' => $user_id, 'message' => " sent exchange request to your product. Post Name - " . $get_post_info->title, 'notify_from' => 'post_exchange', 'notify_title' => "New post exchange In Letgo!..", 'post_id' => $post_id, 'slug' => $get_post_info->slug));
            TblPost::send_push_notification($fcmid, $message1);
            $mail_data = array("send_maildata" => array('to_id' => $get_post_info->user_id, 'message' => $get_user_info->name . " sent exchange request to your product. Post Name - " . $get_post_info->title, 'subject' => "New post exchange In Letgo!..", 'ad_url' => $slug));
            $mail_key = "post_exchange_request";
            Setting::notification_mail($mail_data, $mail_key);
            // notification end
            return response()->json(['message' => "Exchange created successfully!.", 'flag' => 1]);
        } else {
            return response()->json(['message' => "You already created exchange using this product!, please choose some another product.", 'flag' => 0]);
        }
    }

    public function update_exchange_status()
    {
        $exchange_id = request()->exchange_id;
        $status = request()->status;
        $check_exchange = TblExchangedPost::find($exchange_id);
        if ($status == "block") {
            // block the failed exchange post
            $block = TblPost::find($check_exchange->exchanged_post_id);
            $check_exchange->update(['block_exchange' => 1,]);
            $block->update(['block_exchange' => 1,]);
        } else if ($status == "unblock") {
            // unblock the failed exchange post
            $unblock = TblPost::find($check_exchange->exchanged_post_id);
            $check_exchange->update(['block_exchange' => 0,]);
            $unblock->update(['block_exchange' => 0,]);
        } else {
            $check_exchange->update(['status' => $status,]);

            //status notification start
            $user_id = Auth::id();
            $get_user_info = User::where('id', $user_id)->first();
            $get_post_info = TblPost::where('id', $check_exchange->post_id)->first();
            
            $slug = URL::to('my-exchange/incoming');

            if ($user_id == $check_exchange->user_id) {
                $to_id = $check_exchange->post_owner_id;
            } else {
                $to_id = $check_exchange->user_id;
            }

            $get_seller_info = User::where('id', $to_id)->first();
            $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";

            $message1 = array("notifydata" => array('to_id' => $to_id, 'from_id' => $user_id, 'message' => $status . " your exchange request on " . $get_post_info->title, 'notify_from' => 'post_exchange_status', 'notify_title' => "Post exchange status In Letgo!..", 'post_id' => $check_exchange->post_id, 'slug' => $get_post_info->slug));

            TblPost::send_push_notification($fcmid, $message1);


            $mail_data = array("send_maildata" => array('to_id' => $to_id, 'message' => $status . " your exchange request on " . $get_post_info->title, 'subject' => "Post exchange status In Letgo!..", 'ad_url' => $slug));
            $mail_key = "post_exchange_status";
            Setting::notification_mail($mail_data, $mail_key);
            //status notification end

        }
        if ($status == "success") {
            // update the post status to sold for exchanged post
            $ex_post_sold = TblPost::find($check_exchange->exchanged_post_id);
            $ex_post_sold->update([
                'sold_status' => 1
            ]);
            // update the post status to sold for orginal post
            $post_sold = TblPost::find($check_exchange->post_id);
            $post_sold->update([
                'sold_status' => 1
            ]);
            $message = "Exchanged successfully!..";

            //success notification start
            $user_id = Auth::id();
            $get_user_info = User::where('id', $user_id)->first();
            $get_post_info = TblPost::where('id', $check_exchange->post_id)->first();
            $slug = URL::to('my-exchange/successful');

            if ($user_id == $check_exchange->user_id) {
                $to_id = $check_exchange->post_owner_id;
            } else {
                $to_id = $check_exchange->user_id;
            }
            $get_seller_info = User::where('id', $to_id)->first();
            $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";

            $message1 = array("notifydata" => array('to_id' => $to_id, 'from_id' => $user_id, 'message' => " completed your exchange request on " . $get_post_info->title, 'notify_from' => 'post_exchange_complete', 'notify_title' => "Post exchange complete In Letgo!..", 'post_id' => $check_exchange->post_id, 'slug' => $get_post_info->slug));

            TblPost::send_push_notification($fcmid, $message1);

            $mail_data = array("send_maildata" => array('to_id' => $to_id, 'message' => "Completed your exchange request on " . $get_post_info->title, 'subject' => "Post exchange complete In Letgo!..", 'ad_url' => $slug));
            $mail_key = "post_exchange_success";
            Setting::notification_mail($mail_data, $mail_key);

            //success notification end


        } else {
            $message = $status . " successfully!..";
        }
        return response()->json(['message' => $message]);
    }
}
