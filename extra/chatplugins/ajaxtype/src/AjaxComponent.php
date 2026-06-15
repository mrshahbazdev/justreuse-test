<?php

namespace Chatplugins\Ajaxtype;

use App\Models\TblChat;
use App\Models\TblPost;
use App\Models\Languages;
use App\Models\User;
use App\Models\Setting;
use App\Models\TblBlockeduser;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

use Livewire\WithFileUploads; //for file upload

use function PHPUnit\Framework\isNull;

class AjaxComponent extends Component
{
    public $chatdetail = "", $search;
    use WithFileUploads; //for file upload

    public function chatpage()
    {

        $google_api_key = "";
        $tb = Setting::where('key','app')->first();
        if($tb!=null || $tb !="")
        {
            $st = json_decode($tb->value);
            $google_api_key = $st->google_api_key;
        }
        
       
        $userid = auth()->user()->id;

        if(request()->to == $userid){
            return redirect(URL::to('/chatting'));
        }

        $check_new = TblChat::where('from_id', $userid)->where('to_id', request()->to)->where('post_id', request()->p)->pluck('id')->first();
        if (!empty(request()->p) && (request()->type == null)) {
            if (empty($check_new)) {
                TblChat::create([
                    'post_id' => request()->p,
                    'msg' => "Welcome!",
                    'from_id' => $userid,
                    'to_id' => request()->to,
                    'receiver' => $userid
               ]);

                // notification start
                $settings = Setting::get_logos();
                $site_name = $settings['name'];

                $post_id = request()->p;
                $get_user_info = User::where('id', $userid)->first();
                $get_post_info = TblPost::where('id', $post_id)->first();
                $get_seller_info = User::where('id', request()->to)->first();
                // $slug = URL::to('/chat');
                $slug = URL::to('/chatting?to='.$userid.'&p='.$post_id.'&type=old');

                $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";

                $message1 = array("notifydata" => array('to_id' => request()->to, 'from_id' => $userid, 'message' => "contacted you on " . $get_post_info->title, 'notify_from' => 'chat', 'notify_title' => "New chat In ".$site_name."!..", 'post_id' => $post_id, 'slug' => $slug));

                TblPost::send_push_notification($fcmid, $message1);


                $mail_data = array("send_maildata" => array('to_id' => request()->to, 'message' => "New user contacted you on " . $get_post_info->title, 'subject' => "New chat In ".$site_name."!..", 'ad_url' => $slug));
                $mail_key = "post_chat";
                Setting::notification_mail($mail_data, $mail_key);

                // notification end

            }
        }
        if (!empty(request()->p)) {

            $chk_delete_post = TblPost::onlyTrashed()->where('id', request()->p)->get();
                if(count($chk_delete_post) > 0)
                {
                    return redirect('/chatting');   
                }
            
            $node = TblChat::where('tbl_chats.to_id', $userid)
                ->where('tbl_chats.post_id', '=', request()->p)->pluck('id')->toArray();
            DB::table('tbl_chats')->whereIn('id', $node)->update(array('read_status' => 1));
            $check_blocked = TblBlockeduser::where('post_id', request()->p)->where('blocked_id', request()->to)->where('blocked_by', auth()->user()->id)->first();
            if (empty($check_blocked)) {
                $chatdetail = TblChat::where('post_id', request()->p)
                    ->where(function ($q) {
                        $q->where('tbl_chats.receiver', auth()->user()->id)
                            ->orWhere('tbl_chats.receiver', request()->to);
                    })
                    ->orderBy('created_at')
                    ->get()->groupBy(function ($q) {
                        return $q->created_at->format('Y-m-d');
                    });
            } else if ($check_blocked->block_status == 0) {

                $chatdetail = TblChat::where('post_id', request()->p)
                    ->where(function ($q) {
                        $q->where('tbl_chats.receiver', auth()->user()->id)
                            ->orWhere('tbl_chats.receiver', request()->to);
                    })
                    ->orderBy('created_at')
                    ->get()->groupBy(function ($q) {
                        return $q->created_at->format('Y-m-d');
                    });                

                /*$chatdetail = TblChat::where('post_id', request()->p)
                    ->where('tbl_chats.created_at', "<=", date('Y-m-d H:i:s', strtotime($check_blocked->created_at)))
                    ->orWhere('tbl_chats.created_at', ">=", date('Y-m-d H:i:s', strtotime($check_blocked->updated_at)))
                    ->where(function ($q) {
                        $q->where('tbl_chats.receiver', auth()->user()->id)
                            ->orWhere('tbl_chats.receiver', request()->to);
                    })
                    ->orderBy('created_at')
                    ->get()->groupBy(function ($q) {
                        return $q->created_at->format('Y-m-d');
                    });*/
            } else {
                $chatdetail = TblChat::where('post_id', request()->p)
                    ->where('tbl_chats.created_at', "<=", date('Y-m-d H:i:s', strtotime($check_blocked->created_at)))
                    ->where(function ($q) {
                        $q->where('tbl_chats.receiver', auth()->user()->id)
                            ->orWhere('tbl_chats.receiver', request()->to);
                    })
                    ->orderBy('created_at')
                    ->get()->groupBy(function ($q) {
                        return $q->created_at->format('Y-m-d');
                    });
            }
        } else {
            $chatdetail = "";
        }
        /*$search = $this->search;
        $chatlist = TblChat::where('tbl_chats.from_id', $userid)
            ->join('tbl_posts', 'tbl_chats.post_id', '=', 'tbl_posts.id')
            ->where('tbl_posts.title', 'like', '%' . $search . '%')
            ->orWhere('tbl_chats.to_id', $userid)
            ->whereNotNull('tbl_chats.msg')
            ->whereNull('tbl_chats.deleted_at')
            ->whereNull('tbl_posts.deleted_at')
            ->groupBy('tbl_chats.post_id', 'tbl_chats.receiver')
            ->get(['tbl_chats.*', 'tbl_posts.title as post_name']);*/

            $search = $this->search;
            $curr_date = date('Y-m-d H:i:s');
            // $chatlist = TblChat::where('tbl_chats.from_id', $userid)
            // ->join('tbl_posts', function ($join) use ($search) {
            //     $join->on('tbl_chats.post_id', '=', 'tbl_posts.id')
            //         ->whereNull('tbl_posts.deleted_at')
            //         ->where('tbl_posts.title', 'like', '%' . $search . '%')
            //         ->where('tbl_posts.sold_status', 0);
            // })
            // ->join('tbl_payments', function ($joins) use ($curr_date) {
            //     $joins->on('tbl_chats.post_id', '=', 'tbl_payments.post_id')
            //     ->where('tbl_payments.active', 1)
            //     ->where('tbl_payments.start_date', '<=', $curr_date)
            //     ->where('tbl_payments.end_date', '>=', $curr_date);
            // })
            // ->orWhere('tbl_chats.to_id', $userid)
            // ->whereNotNull('tbl_chats.msg')
            // ->whereNull('tbl_chats.deleted_at')      
            // ->groupBy('tbl_chats.post_id', 'tbl_chats.receiver')
            // ->orderBy('tbl_chats.created_at', 'desc')
            // ->get(['tbl_chats.*', 'tbl_posts.title as post_name']);

            //$curr_date = date('Y-m-d H:i:s');
            DB::enableQueryLog();

            // $chatlist = TblChat::where('tbl_chats.from_id', $userid)
            // ->join('tbl_posts', function ($join) use ($search) {
            //     $join->on('tbl_chats.post_id', '=', 'tbl_posts.id')
            //         ->whereNull('tbl_posts.deleted_at')
            //         ->where('tbl_posts.title', 'like', '%' . $search . '%')
            //         ->where('tbl_posts.sold_status', 0);
            // })
            // ->orWhere('tbl_chats.to_id', $userid)
            // ->whereNotNull('tbl_chats.msg')
            // ->whereNull('tbl_chats.deleted_at')      
            // ->groupBy('tbl_chats.post_id', 'tbl_chats.receiver')
            // ->orderBy('tbl_chats.created_at', 'desc')
            // ->get(['tbl_chats.*', 'tbl_posts.title as post_name']);


            $chatlistCheck = TblChat::where('tbl_chats.from_id', $userid)
            ->join('tbl_posts', function ($join) use ($search) {
                $join->on('tbl_chats.post_id', '=', 'tbl_posts.id')
                    ->whereNull('tbl_posts.deleted_at')
                    ->where('tbl_posts.title', 'like', '%' . $search . '%')
                    ->where('tbl_posts.sold_status', 0);
            })
            ->orWhere('tbl_chats.to_id', $userid)
            ->whereNotNull('tbl_chats.msg')
            ->whereNull('tbl_chats.deleted_at')
            ->groupBy('tbl_chats.post_id', 'tbl_chats.receiver')
            ->orderBy('tbl_chats.created_at', 'desc')
            ->get(['tbl_chats.*', 'tbl_posts.title as post_name']);
            
            $chatlist = TblChat::where(function ($query) use ($userid) {
                $query->where('tbl_chats.from_id', $userid)
                      ->orWhere('tbl_chats.to_id', $userid);
            })
            ->join('tbl_posts', function ($join) use ($search) {
                $join->on('tbl_chats.post_id', '=', 'tbl_posts.id')
                     ->whereNull('tbl_posts.deleted_at')
                     ->where('tbl_posts.title', 'like', '%' . $search . '%')
                     ->where('tbl_posts.sold_status', 0);
            })
            ->whereNotNull('tbl_chats.msg')
            ->whereNull('tbl_chats.deleted_at')
            ->groupBy('tbl_chats.post_id', 'tbl_chats.receiver')
            ->orderByRaw('MAX(tbl_chats.created_at) DESC')
            ->get(['tbl_chats.*', 'tbl_posts.title as post_name']);
                // $chatlist = TblChat::join('tbl_posts', function ($join) use ($search) {
                //     $join->on('tbl_chats.post_id', '=', 'tbl_posts.id')
                //         ->whereNull('tbl_posts.deleted_at')
                //         ->where('tbl_posts.title', 'like', '%' . $search . '%')
                //         ->where('tbl_posts.sold_status', 0);
                // })
                // ->where(function ($query) use ($userid) {
                //     $query->where('tbl_chats.from_id', $userid)
                //         ->orWhere('tbl_chats.to_id', $userid);
                // })
                // ->whereNotNull('tbl_chats.msg')
                // ->whereNull('tbl_chats.deleted_at')
                // ->groupBy('tbl_chats.post_id', 'tbl_chats.receiver')
                // ->orderBy('tbl_chats.created_at', 'desc')
                // ->get(['tbl_chats.*', 'tbl_posts.title as post_name']);
                $query = DB::getQueryLog();
                // dd($query);
                // dd($chatlist,$chatlistCheck);  
        // get make offer chat
        $get_make_offer = TblChat::where('tbl_chats.post_id', request()->p)
            ->where('tbl_chats.make_offer', "=", 1)
            ->where(function ($q) {
                $q->where('tbl_chats.receiver', auth()->user()->id)
                    ->orWhere('tbl_chats.receiver', request()->to);
            })
            ->orderBy('tbl_chats.id', 'desc')
            ->get(['tbl_chats.id', 'tbl_chats.from_id'])->first();
        $check_last_chat_id = TblChat::where('tbl_chats.post_id', request()->p)
            ->where(function ($q) {
                $q->where('tbl_chats.receiver', auth()->user()->id)
                    ->orWhere('tbl_chats.receiver', request()->to);
            })
            ->orderBy('tbl_chats.created_at', 'desc')
            ->pluck('tbl_chats.id')->first();
        return view('ajaxtype.src.viewfiles.ajax-chat-list', ['chatlists' => $chatlist, 'details' => $chatdetail, 'get_make_offer' => $get_make_offer, 'last_chat_id' => $check_last_chat_id,'google_api_key'=>$google_api_key]);
    }

    
    public function ajax_send_chat_update_last_seen()
    {
        $id = auth()->user()->id;
        $datetime = date('Y-m-d H:i:s');
        $update = User::where('id', $id)->update(['last_chat_seen' => $datetime]);
        if($update)
        {
            $result = array("result"=>true);
        }
        else{
            $result = array("result"=>false);
        }
        return $result;
    }

    public function ajax_send_chat_fetch_last_seen()
    {
        $id = request()->id;
      
        if (!isset($lang_code)) {
            $lang_code = \Config::get('app.locale');
        }
        
      
      	$online = Languages::where('lang_code', $lang_code)->where('lang_org_text', 'Online')->value('lang_text');
		
        if(empty($id)){
            $result = array("result"=>"offline","last_seen"=>"Unavailable");
        }
        else{
            //$id = auth()->user()->id;
            $datetime = date('Y-m-d H:i:s');
            $pre_time = date('Y-m-d H:i:s',strtotime('-1 minutes'));
            $checkIt = User::where('id', $id)->whereBetween('last_chat_seen',[$pre_time,$datetime])->get();
            //$ls = TblChat::GetUserLastSeen($id);
            if(count($checkIt)>0)
            {
                $result = array("result"=>"online","last_seen"=> $online );
            }
            else{
                
                $checkIt = User::where('id', $id)->get();
                if(count($checkIt)>0){
                    //$ls = TblChat::lastSeen($checkIt[0]->last_chat_seen);
                    $ls = TblChat::GetUserLastSeen($id);
                    $result = array("result"=>"offline","last_seen"=> $ls);
                }
                else{
                    $result = array("result"=>"offline","last_seen"=>"Unavailable");
                }
                
            }
            return $result;
        }

    }

    public function ajax_send_chat_msg()
    {
        $msg = request()->msg;
        $user_id = request()->sender_id;
        $to = request()->receiver_id;
        $post_id = request()->post_id;
        $make_offer = request()->make_offer;

        $sender="yes";
        $check_post_owner = TblPost::where('id', $post_id)->pluck('user_id')->first();
        if ($check_post_owner == $user_id) {
            $receiver = $to;
            $sender="yes";
        } else {
            $receiver = $user_id;
            $sender="no";
        }
      
         $slug = URL::to('/chatting?to=' . $user_id . '&p=' . $post_id . '&type=old');
      $settings = Setting::get_logos();
       $headers = array(
                    'Authorization: key=AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
    
                    'Content-Type: application/json',
                );
      $site_name = $settings['name'];
      $get_user_info = User::where('id', $user_id)->first();
      $get_post_info = TblPost::where('id', $post_id)->first();
      $get_seller_info = User::where('id', $to)->first();
      $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
      $message1 = array("notifydata" => array('to_id' => $to, 'from_id' => $user_id, 'message' => $get_user_info->name . " sent you a text - " . $msg, 'notify_from' => 'chat', 'notify_title' => "New chat in " . $site_name . " !..", 'post_id' => $post_id, 'slug' => $slug));
      TblPost::send_push_notification($fcmid, $message1,$headers);


           $created = TblChat::create([
                'post_id'=>$post_id,
                'msg'=>$msg,
                'from_id'=>$user_id,
                'to_id'=>$to,
                'receiver'=>$receiver,
                'make_offer' => $make_offer
            ]);


        if($created)
        {
            $result = array("result"=>"success");
            if($make_offer == 1)
            {
                // sent notification start	
                $settings = Setting::get_logos();
                $site_name = $settings['name'];

                $get_user_info = User::where('id', $user_id)->first();
                $get_post_info = TblPost::where('id', $post_id)->first();
                $get_seller_info = User::where('id', $to)->first();
                $slug = URL::to('/chatting?to='.$user_id.'&p='.$post_id.'&type=old');

                $notify_from = "offer_request";
                $notify_title = "New Offer Request In ".$site_name."!.";
                $offer_msg = $get_user_info->name . " sent offer request on your product " . $get_post_info->title;

                $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                $message1 = array("notifydata" => array('to_id' => $to, 'from_id' => $user_id, 'message' => $offer_msg, 'notify_from' => $notify_from, 'notify_title' => $notify_title, 'post_id' => $post_id, 'slug' => $slug));
                TblPost::send_push_notification($fcmid, $message1);
    
                $mail_data = array("send_maildata" => array('to_id' => $to, 'message' => $offer_msg, 'subject' => $notify_title, 'ad_url' => $slug));
                $mail_key = "make_offer";
                Setting::notification_mail($mail_data, $mail_key);
                // sent notification end
            }

        }
        else{
            $result = array("result"=>"failed");
        }
        return $result;
    }
    public function ajax_send_chat_location()
    {
        $loc = request()->loc;
        $to = request()->to;
        $post_id = request()->post_id;
        $user_id = auth()->user()->id;
        $lat = request()->lat;
        $long = request()->long;
        $check_post_owner = TblPost::where('id', $post_id)->pluck('user_id')->first();
        if ($check_post_owner == $user_id) {
            $receiver = $to;
        } else {
            $receiver = $user_id;
        }
        $created = TblChat::create([
            'post_id' => $post_id,
            'from_id' => $user_id,
            'to_id' => $to,
            'receiver' => $receiver,
            'make_offer' => 0,
            "location" => $loc,
            "latitude" => $lat,
            "longitude" => $long
        ]);
        if($created)
        {
            $result = array("result"=>"success");
        }
        else{
            $result = array("result"=>"failed");
        }
        return $result;

    }

    public function ajax_send_chat_image()
    {

        $to = request()->to;
        $post_id = request()->post_id;
        $user_id = auth()->user()->id;
        $check_post_owner = TblPost::where('id', $post_id)->pluck('user_id')->first();
        if ($check_post_owner == $user_id) {
            $receiver = $to;
        } else {
            $receiver = $user_id;
        }

        $image = request()->image_file;
        $nameimg = $image->store('chatimage', 'public');
        $created = TblChat::create([
            'post_id' => $post_id,
            'from_id' => $user_id,
            'to_id' => $to,
            'receiver' => $receiver,
            'attachment' => $nameimg
        ]);
    
        
        if($created)
        {
            $result = array("result"=>"success");
        }
        else{
            $result = array("result"=>"failed");
        }
        return $result;

    }

    public function ajax_send_chat_accept_offer()
    {
        $id = request()->id;
        $update = TblChat::where('id', $id)->update(['accept_offer' => 1]);
        if($update)
        {
            $result = array("result"=>true);
        // sent notification start	
        $settings = Setting::get_logos();
        $site_name = $settings['name'];

                $get_chat_detail = TblChat::where('id', $id)->first();
                $to = $get_chat_detail->from_id;
                $post_id = $get_chat_detail->post_id;
                $user_id = $get_chat_detail->to_id;    // sent notification to "from_id"
            
                $get_user_info = User::where('id', $user_id)->first();
                $get_post_info = TblPost::where('id', $post_id)->first();
                $get_seller_info = User::where('id', $to)->first();
                // $slug = URL::to('/chat');
                $slug = URL::to('/chatting?to='.$user_id.'&p='.$post_id.'&type=old');

                $notify_from = "accept_offer";
                $notify_title = "Offer Request Accepted In ".$site_name."!.";
                $offer_msg = "offer request has been accepted for this post -  " . $get_post_info->title;

                $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                $message1 = array("notifydata" => array('to_id' => $to, 'from_id' => $user_id, 'message' => $offer_msg, 'notify_from' => $notify_from, 'notify_title' => $notify_title, 'post_id' => $post_id, 'slug' => $slug));
                TblPost::send_push_notification($fcmid, $message1);
    
                $mail_data = array("send_maildata" => array('to_id' => $to, 'message' => $offer_msg, 'subject' => $notify_title, 'ad_url' => $slug));
                $mail_key = "make_offer";
                Setting::notification_mail($mail_data, $mail_key);
                // sent notification end

        }
        else{
            $result = array("result"=>false);
        }
        return $result;
    }

    public function ajax_send_chat_denied_offer()
    {
        $id = request()->id;
        $update = TblChat::where('id', $id)->update(['denied_offer' => 1]);
        if($update)
        {
            $result = array("result"=>true);

            // sent notification start	
            $settings = Setting::get_logos();
            $site_name = $settings['name'];
            
            $get_chat_detail = TblChat::where('id', $id)->first();
            $to = $get_chat_detail->from_id;
            $post_id = $get_chat_detail->post_id;
            $user_id = $get_chat_detail->to_id;    // sent notification to "from_id"

            $get_user_info = User::where('id', $user_id)->first();
            $get_post_info = TblPost::where('id', $post_id)->first();
            $get_seller_info = User::where('id', $to)->first();
            // $slug = URL::to('/chat');
            $slug = URL::to('/chatting?to='.$user_id.'&p='.$post_id.'&type=old');

            $notify_from = "deny_offer";
            $notify_title = "Offer Request Denied In ".$site_name."!.";
            $offer_msg = "offer request Denied for this post -  " . $get_post_info->title;

            $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
            $message1 = array("notifydata" => array('to_id' => $to, 'from_id' => $user_id, 'message' => $offer_msg, 'notify_from' => $notify_from, 'notify_title' => $notify_title, 'post_id' => $post_id, 'slug' => $slug));
            TblPost::send_push_notification($fcmid, $message1);

            $mail_data = array("send_maildata" => array('to_id' => $to, 'message' => $offer_msg, 'subject' => $notify_title, 'ad_url' => $slug));
            $mail_key = "make_offer";
            Setting::notification_mail($mail_data, $mail_key);
            // sent notification end

        }
        else{
            $result = array("result"=>false);
        }
        return $result;
    }

    public function ajax_send_chat_offer_state_check()
    {
        $id = request()->id;
        $update = TblChat::where('id', $id)->get()->first();

            $result = array("result"=>true, "denied_value"=>$update->denied_offer, "accept_value"=>$update->accept_offer);

        return $result;
    }


    public function ajax_reload_conversation_area()
    {

        $google_api_key = "";
        $tb = Setting::where('key','app')->first();
        if($tb!=null || $tb !="")
        {
            $st = json_decode($tb->value);
            $google_api_key = $st->google_api_key;
        }


        $userid = auth()->user()->id;
        $last_rec_id = request()->last_recid;
        if (!empty(request()->p)) {

            $check_blocked = TblBlockeduser::where('post_id', request()->p)->where('blocked_id', request()->to)->where('blocked_by', auth()->user()->id)->first();
            
            if (empty($check_blocked)) {
                $chatdetail = TblChat::where('id','>',$last_rec_id)
                    ->where('post_id', request()->p)
                    ->where(function ($q) {
                        $q->where('tbl_chats.receiver', auth()->user()->id)
                            ->orWhere('tbl_chats.receiver', request()->to);
                    })
                    ->orderBy('created_at')
                    ->get()->groupBy(function ($q) {
                        return $q->created_at->format('Y-m-d');
                    });

                    $check_chatlist_is_deleted = TblChat::where('post_id', request()->p)
                    ->where(function ($q) {
                        $q->where('tbl_chats.receiver', auth()->user()->id)
                            ->orWhere('tbl_chats.receiver', request()->to);
                    })
                    ->orderBy('created_at')
                    ->count();
                    
            } else if ($check_blocked->block_status == 0) {

                $chatdetail = TblChat::where('id','>',$last_rec_id)
                    ->where('post_id', request()->p)
                    ->where(function ($q) {
                        $q->where('tbl_chats.receiver', auth()->user()->id)
                            ->orWhere('tbl_chats.receiver', request()->to);
                    })
                    ->orderBy('created_at')
                    ->get()->groupBy(function ($q) {
                        return $q->created_at->format('Y-m-d');
                    });
                    
                    $check_chatlist_is_deleted = TblChat::where('post_id', request()->p)
                    ->where(function ($q) {
                        $q->where('tbl_chats.receiver', auth()->user()->id)
                            ->orWhere('tbl_chats.receiver', request()->to);
                    })
                    ->orderBy('created_at')
                    ->count();

                /*$chatdetail = TblChat::where('id','>',$last_rec_id)
                ->where('post_id', request()->p)
                    ->where('tbl_chats.created_at', "<=", date('Y-m-d H:i:s', strtotime($check_blocked->created_at)))
                    ->orWhere('tbl_chats.created_at', ">=", date('Y-m-d H:i:s', strtotime($check_blocked->updated_at)))
                    ->where(function ($q) {
                        $q->where('tbl_chats.receiver', auth()->user()->id)
                            ->orWhere('tbl_chats.receiver', request()->to);
                    })
                    ->orderBy('created_at')
                    ->get()->groupBy(function ($q) {
                        return $q->created_at->format('Y-m-d');
                    });*/
                    
            } else {
                $chatdetail = TblChat::where('id','>',$last_rec_id)
                ->where('post_id', request()->p)
                    ->where('tbl_chats.created_at', "<=", date('Y-m-d H:i:s', strtotime($check_blocked->created_at)))
                    ->where(function ($q) {
                        $q->where('tbl_chats.receiver', auth()->user()->id)
                            ->orWhere('tbl_chats.receiver', request()->to);
                    })
                    ->orderBy('created_at')
                    ->get()->groupBy(function ($q) {
                        return $q->created_at->format('Y-m-d');
                    });

                    $check_chatlist_is_deleted = TblChat::where('post_id', request()->p)
                        ->where('tbl_chats.created_at', "<=", date('Y-m-d H:i:s', strtotime($check_blocked->created_at)))
                        ->where(function ($q) {
                            $q->where('tbl_chats.receiver', auth()->user()->id)
                                ->orWhere('tbl_chats.receiver', request()->to);
                        })
                        ->orderBy('created_at')
                        ->count(); 
                    
            }
        } else {
            $chatdetail = "";
            $check_chatlist_is_deleted = 0; 
        }
    //check_chatlist_is_deleted - if count 0 means no msg send to this chat, else shown total msg count.

        $html = view('ajaxtype.src.viewfiles.ajax-reolad-conversation-area', ['details' => $chatdetail,'google_api_key'=>$google_api_key]);
        $resultdata = array('success' => true, 'last'=>$last_rec_id, 'html'=>strval($html), 'total_msg_count' => $check_chatlist_is_deleted);

        //$k = array(["sdk"=>"welcome"]);
        return response()->json($resultdata);
    }

    public function ajax_reload_chatlist_area()
    {
        $userid = auth()->user()->id;
        $search = request()->q;

        $curr_date = date('Y-m-d H:i:s');
        // $chatlist = TblChat::where('tbl_chats.from_id', $userid)
        // ->join('tbl_posts', function ($join) use ($search) {
        //     $join->on('tbl_chats.post_id', '=', 'tbl_posts.id')
        //         ->whereNull('tbl_posts.deleted_at')
        //         ->where('tbl_posts.title', 'like', '%' . $search . '%')
        //         ->where('tbl_posts.sold_status', 0);
        // })
        // ->orWhere('tbl_chats.to_id', $userid)
        // ->whereNotNull('tbl_chats.msg')
        // ->whereNull('tbl_chats.deleted_at')      
        // ->groupBy('tbl_chats.post_id', 'tbl_chats.receiver')
        // ->orderBy('tbl_chats.created_at', 'desc')
        // ->get(['tbl_chats.*', 'tbl_posts.title as post_name']);

        $chatlistCheck = TblChat::where('tbl_chats.from_id', $userid)
        ->join('tbl_posts', function ($join) use ($search) {
            $join->on('tbl_chats.post_id', '=', 'tbl_posts.id')
                ->whereNull('tbl_posts.deleted_at')
                ->where('tbl_posts.title', 'like', '%' . $search . '%')
                ->where('tbl_posts.sold_status', 0);
        })
        ->orWhere('tbl_chats.to_id', $userid)
        ->whereNotNull('tbl_chats.msg')
        ->whereNull('tbl_chats.deleted_at')
        ->groupBy('tbl_chats.post_id', 'tbl_chats.receiver')
        ->orderBy('tbl_chats.created_at', 'desc')
        ->get(['tbl_chats.*', 'tbl_posts.title as post_name']);
        $chatlist = TblChat::where(function ($query) use ($userid) {
            $query->where('tbl_chats.from_id', $userid)
                  ->orWhere('tbl_chats.to_id', $userid);
        })
        ->join('tbl_posts', function ($join) use ($search) {
            $join->on('tbl_chats.post_id', '=', 'tbl_posts.id')
                 ->whereNull('tbl_posts.deleted_at')
                 ->where('tbl_posts.title', 'like', '%' . $search . '%')
                 ->where('tbl_posts.sold_status', 0);
        })
        ->whereNotNull('tbl_chats.msg')
        ->whereNull('tbl_chats.deleted_at')
        ->groupBy('tbl_chats.post_id', 'tbl_chats.receiver')
        ->orderByRaw('MAX(tbl_chats.created_at) DESC')
        ->get(['tbl_chats.*', 'tbl_posts.title as post_name']);

    $html = view('ajaxtype.src.viewfiles.ajax-reolad-chatlist-area', ['chatlists' => $chatlist]);
    //$html = $chatlist;
    $resultdata = array('success' => true, 'html'=>strval($html));

    //$k = array(["sdk"=>"welcome"]);
    return response()->json($resultdata);    
    
        /*$search = $this->search;
        $chatlist = TblChat::where('tbl_chats.from_id', $userid)
            ->join('tbl_posts', 'tbl_chats.post_id', '=', 'tbl_posts.id')
            ->where('tbl_posts.title', 'like', '%' . $search . '%')
            ->orWhere('tbl_chats.to_id', $userid)
            ->whereNotNull('tbl_chats.msg')
            ->whereNull('tbl_chats.deleted_at')
            ->whereNull('tbl_posts.deleted_at')
            ->groupBy('tbl_chats.post_id', 'tbl_chats.receiver')
            ->get(['tbl_chats.*', 'tbl_posts.title as post_name']);*/


            // $curr_date = date('Y-m-d H:i:s');
            // $chatlist = TblChat::where('tbl_chats.from_id', $userid)
            // ->join('tbl_posts', function ($join) use ($search) {
            //     $join->on('tbl_chats.post_id', '=', 'tbl_posts.id')
            //         ->whereNull('tbl_posts.deleted_at')
            //         ->where('tbl_posts.title', 'like', '%' . $search . '%')
            //         ->where('tbl_posts.sold_status', 0);
            // })
            // ->join('tbl_payments', function ($joins) use ($curr_date) {
            //     $joins->on('tbl_chats.post_id', '=', 'tbl_payments.post_id')
            //     ->where('tbl_payments.active', 1)
            //     ->where('tbl_payments.start_date', '<=', $curr_date)
            //     ->where('tbl_payments.end_date', '>=', $curr_date);
            // })
            // ->orWhere('tbl_chats.to_id', $userid)
            // ->whereNotNull('tbl_chats.msg')
            // ->whereNull('tbl_chats.deleted_at')      
            // ->groupBy('tbl_chats.post_id', 'tbl_chats.receiver')
            // ->orderBy('tbl_chats.created_at', 'desc')
            // ->get(['tbl_chats.*', 'tbl_posts.title as post_name']);

    
    }

    public function ajax_chat_update_message_read()
    {
        $msg_ids = request()->msg_ids;
        $updated = TblChat::whereIn('id', $msg_ids)->update(['read_status' => 1]);

        
        if($updated==true){
            $resultdata = array('success' => true,'message'=>$msg_ids);
        }else{
            $resultdata = array('success' => false,'message'=>$msg_ids);
        }
        
        return response()->json($resultdata);
    }

    public function ajax_chat_fetch_readed_ids()
    {
        $msg_ids = request()->msg_ids;
        $updated = TblChat::whereIn('id', $msg_ids)->where('read_status','1')->get()->pluck('id');

        $resultdata = array('success' => true,'ids'=>$updated,'message'=>$msg_ids);

        return response()->json($resultdata);
    }
    public function ajax_send_chat_block_delete_chat()
    {
        $ids = explode('@', request()->id);
        $to_id = $ids[0];
        $post_id = $ids[1];
        $from_id = auth()->user()->id;
        if (request()->type == "delete") {
            TblChat::where('post_id', $post_id)
                ->where('from_id', $from_id)
                ->where('to_id', $to_id)
                ->orWhere('from_id', $to_id)
                ->Where('to_id', $from_id)->delete();
            return response()->json(['message' => 'deleted successfully', 'type' => request()->type]);
        } else {
            $check_block = TblBlockeduser::where('post_id', $post_id)
                ->where('blocked_id', $to_id)
                ->where('blocked_by', $from_id)
                ->first();
            if (!empty($check_block)) {
                if ($check_block->block_status == 1) {
                    $block = 0;
                    $message = 'Unblocked successfully';
                } else {
                    $block = 1;
                    $message = 'Blocked successfully';
                }
                TblBlockeduser::where('id', $check_block->id)->update(array('block_status' => $block));
            } else {
                $block = 1;
                $message = 'Blocked successfully';
                TblBlockeduser::create([
                    'post_id' => $post_id,
                    'blocked_by' => $from_id,
                    'blocked_id' => $to_id,
                    'block_status' => $block,
                ]);
            }

            return response()->json(['message' => $message, 'type' => request()->type]);
        }
    }




}
