<?php

namespace Chatplugins\Wstype;

use App\Models\TblChat;
use App\Models\TblPost;
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

class WstypeComponent extends Component
{
    public $chatdetail = "", $search;
    use WithFileUploads; //for file upload


    public function wschat()
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
            // return redirect(URL::to('/chatting'));
            abort(404);
        }

        if (!empty(request()->p))
        {
            $chk_delete_post = TblPost::onlyTrashed()->where('id', request()->p)->get();
                if(count($chk_delete_post) > 0)
                {
                    return redirect('/chat');   
                }
        }

        $check_new = TblChat::where('from_id', $userid)->where('to_id', request()->to)->where('post_id', request()->p)->pluck('id')->first();
        if (!empty(request()->p) && (request()->type == null)) {
            if (empty($check_new)) {
                $last_id = TblChat::create([
                    'post_id' => request()->p,
                    'msg' => "Welcome!",
                    'from_id' => $userid,
                    'to_id' => request()->to,
                    'receiver' => $userid
                ])->id;

                // notification start
                $settings = Setting::get_logos();
                $site_name = $settings['name'];

                $post_id = request()->p;
                $get_user_info = User::where('id', $userid)->first();
                $get_post_info = TblPost::where('id', $post_id)->first();
                $get_seller_info = User::where('id', request()->to)->first();
                // $slug = URL::to('/chat');
                $slug = URL::to('/wschat?to='.$userid.'&p='.$post_id.'&type=old');

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

                    if(count($chatdetail) == 0)
                    {
                        return redirect('/chat');
                    }  
                    
            } else if ($check_blocked->block_status == 0) {
                $chatdetail = TblChat::where('post_id', request()->p)
                    ->where('tbl_chats.created_at', "<=", date('Y-m-d H:i:s', strtotime($check_blocked->created_at)))
                    ->orWhere('tbl_chats.created_at', ">=", date('Y-m-d H:i:s', strtotime($check_blocked->updated_at)))
                    ->where(function ($q) {
                        $q->where('tbl_chats.receiver', auth()->user()->id)
                            ->orWhere('tbl_chats.receiver', request()->to);
                    })
                    ->orderBy('created_at')
                    ->get()->groupBy(function ($q) {
                        return $q->created_at->format('Y-m-d');
                    });

                    if(count($chatdetail) == 0)
                    {
                        return redirect('/chat');
                    }

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

                    if(count($chatdetail) == 0)
                    {
                        return redirect('/chat');
                    }
            }
        } else {
            $chatdetail = "";
        }

        
        $search = !empty($this->search) ? $this->search : '';

        $chatlist = TblChat::where('tbl_chats.from_id', $userid)
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

        // get make offer chat
        $get_make_offer = TblChat::where('tbl_chats.post_id', request()->p)
            ->where('tbl_chats.make_offer', "=", 1)
            ->where(function ($q) {
                $q->where('tbl_chats.receiver', auth()->user()->id)
                    ->orWhere('tbl_chats.receiver', request()->to);
            })
            ->orderBy('tbl_chats.id', 'desc')
            ->get(['tbl_chats.id', 'tbl_chats.from_id'])->first();

        $get_accept_offer = TblChat::where('tbl_chats.post_id', request()->p)
            ->where('tbl_chats.accept_offer', "=", 1)
            ->where(function ($q) {
                $q->where('tbl_chats.receiver', auth()->user()->id)
                    ->orWhere('tbl_chats.receiver', request()->to);
            })
            ->orderBy('tbl_chats.id', 'desc')
            ->get(['tbl_chats.id'])->first();

        $get_denied_offer = TblChat::where('tbl_chats.post_id', request()->p)
            ->where('tbl_chats.denied_offer', "=", 1)
            ->where(function ($q) {
                $q->where('tbl_chats.receiver', auth()->user()->id)
                    ->orWhere('tbl_chats.receiver', request()->to);
            })
            ->orderBy('tbl_chats.id', 'desc')
            ->get(['tbl_chats.id'])->first();    

        $check_last_chat_id = TblChat::where('tbl_chats.post_id', request()->p)
            ->where(function ($q) {
                $q->where('tbl_chats.receiver', auth()->user()->id)
                    ->orWhere('tbl_chats.receiver', request()->to);
            })
            ->orderBy('tbl_chats.id', 'desc')
            ->pluck('tbl_chats.id')->first();

            // dd($get_denied_offer, $get_accept_offer);
        return view('wstype.src.viewfiles.chat-list', ['chatlists' => $chatlist, 'get_accept_offer' => $get_accept_offer, 'get_denied_offer' => $get_denied_offer,'details' => $chatdetail, 'get_make_offer' => $get_make_offer, 'last_chat_id' => $check_last_chat_id,'google_api_key'=>$google_api_key]);

    }

    public function send_chat()
    {
        $msg = request()->chat_message;
        $to = request()->to;
        $post_id = request()->post_id;
        $user_id = auth()->user()->id;
        $image = request()->image;
        $rowid = request()->row_id;
        // denied_offer
        $check_post_owner = TblPost::where('id', $post_id)->pluck('user_id')->first();


        
        if ($check_post_owner == $user_id) {
            $receiver = $to;
        } else {
            $receiver = $user_id;
        }
        if (isset(request()->make_offer)) {
            $make_offer = !empty(request()->make_offer) ? 1 : 0;
        } else {
            $make_offer = 0;
        }

        if (isset(request()->accept_offer)) {
            $accept_offer = !empty(request()->accept_offer) ? 1 : 0;
        } else {
            $accept_offer = 0;
        }

        if (isset(request()->denied_offer)) {
            $denied_offer = !empty(request()->denied_offer) ? 1 : 0;
        } else {
            $denied_offer = 0;
        }




        //$make_offer = 1 means msg comes from make offer, so need to show the edit offer button and $make_offer = 0 means msg comes from normal chat
        if (!empty($msg)) {
            TblChat::create([
                'post_id' => $post_id,
                'msg' => $msg,
                'from_id' => $user_id,
                'to_id' => $to,
                'receiver' => $receiver,
                'make_offer'=>$make_offer
            ]);
        } else if (!empty($image)) {
            $nameimg = $image->store('chatimage', 'public');
            TblChat::create([
                'post_id' => $post_id,
                'from_id' => $user_id,
                'to_id' => $to,
                'receiver' => $receiver,
                'attachment' => $nameimg,
                'make_offer'=>$make_offer
            ]);
        }

        if (($make_offer == 1) || ($accept_offer == 1) || ($denied_offer == 1)) {
            // sent notification start	
            $settings = Setting::get_logos();
            $site_name = $settings['name'];

            $get_user_info = User::where('id', $user_id)->first();
            $get_post_info = TblPost::where('id', $post_id)->first();
            $get_seller_info = User::where('id', $to)->first();
            // $slug = URL::to('/chat');
            $slug = URL::to('/chat?to='.$user_id.'&p='.$post_id.'&type=old');

            if ($make_offer == 1) {
                $notify_from = "offer_request";
                $notify_title = "New Offer Request In ".$site_name."!.";
                $offer_msg = $get_user_info->name . " sent offer request on your product " . $get_post_info->title;
            } 
            elseif($denied_offer == 1){
                $notify_from = "deny_offer";
                $notify_title = "Offer Request Denied In ".$site_name."!.";
                $offer_msg = "offer request Denied for this post -  " . $get_post_info->title;
            }
            else {
                $notify_from = "accept_offer";
                $notify_title = "Offer Request Accepted In ".$site_name."!.";
                $offer_msg = "offer request has been accepted for this post -  " . $get_post_info->title;
            }

            $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
            $message1 = array("notifydata" => array('to_id' => $to, 'from_id' => $user_id, 'message' => $offer_msg, 'notify_from' => $notify_from, 'notify_title' => $notify_title, 'post_id' => $post_id, 'slug' => $slug));
            TblPost::send_push_notification($fcmid, $message1);

            $mail_data = array("send_maildata" => array('to_id' => $to, 'message' => $offer_msg, 'subject' => $notify_title, 'ad_url' => $slug));
            $mail_key = "make_offer";
            Setting::notification_mail($mail_data, $mail_key);
            // sent notification end
        }
        $lastchat = TblChat::getLastChat($to, $post_id);
        $unread_count = TblChat::getUnreadCount($user_id, $to, $post_id);
        $data = array('lastchat' => $lastchat, 'post_id' => $post_id, 'to_id' => $to, 'unread_count' => $unread_count);
        return $data;
    }
    
    /*public function send_chat_old()
    {
        $msg = request()->chat_message;
        $to = request()->to;
        $post_id = request()->post_id;
        $user_id = auth()->user()->id;
        $image = request()->image;
        // denied_offer
        $check_post_owner = TblPost::where('id', $post_id)->pluck('user_id')->first();
        if ($check_post_owner == $user_id) {
            $receiver = $to;
        } else {
            $receiver = $user_id;
        }
        if (isset(request()->make_offer)) {
            $make_offer = !empty(request()->make_offer) ? 1 : 0;
        } else {
            $make_offer = 0;
        }

        if (isset(request()->accept_offer)) {
            $accept_offer = !empty(request()->accept_offer) ? 1 : 0;
        } else {
            $accept_offer = 0;
        }

        if (isset(request()->denied_offer)) {
            $denied_offer = !empty(request()->denied_offer) ? 1 : 0;
        } else {
            $denied_offer = 0;
        }
        //$make_offer = 1 means msg comes from make offer, so need to show the edit offer button and $make_offer = 0 means msg comes from normal chat
        if (!empty($msg)) {
            TblChat::create([
                'post_id' => $post_id,
                'msg' => $msg,
                'from_id' => $user_id,
                'to_id' => $to,
                'receiver' => $receiver,
                'make_offer' => $make_offer,
                'accept_offer' => $accept_offer,
                'denied_offer' => $denied_offer,
            ]);
        } else if (!empty($image)) {
            $nameimg = $image->store('chatimage', 'public');
            TblChat::create([
                'post_id' => $post_id,
                'from_id' => $user_id,
                'to_id' => $to,
                'receiver' => $receiver,
                'attachment' => $nameimg
            ]);
        }

        if (($make_offer == 1) || ($accept_offer == 1)) {
            // sent notification start	
            $get_user_info = User::where('id', $user_id)->first();
            $get_post_info = TblPost::where('id', $post_id)->first();
            $get_seller_info = User::where('id', $to)->first();
            $slug = URL::to('/chat');

            if ($make_offer == 1) {
                $notify_from = "offer_request";
                $notify_title = "New Offer Request In Letgo!.";
                $offer_msg = $get_user_info->name . " sent offer request on your product " . $get_post_info->title;
            } 
            elseif($denied_offer == 1){
                $notify_from = "deny_offer";
                $notify_title = "Offer Request Denied In Letgo!.";
                $offer_msg = "offer request Denied for this post -  " . $get_post_info->title;
            }
            else {
                $notify_from = "accept_offer";
                $notify_title = "Offer Request Accepted In Letgo!.";
                $offer_msg = "offer request has been accept for this post -  " . $get_post_info->title;
            }

            $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
            $message1 = array("notifydata" => array('to_id' => $to, 'from_id' => $user_id, 'message' => $offer_msg, 'notify_from' => $notify_from, 'notify_title' => $notify_title, 'post_id' => $post_id, 'slug' => $get_post_info->slug));
            TblPost::send_push_notification($fcmid, $message1);

            $mail_data = array("send_maildata" => array('to_id' => $to, 'message' => $offer_msg, 'subject' => $notify_title, 'ad_url' => $slug));
            $mail_key = "make_offer";
            Setting::notification_mail($mail_data, $mail_key);
            // sent notification end
        }
        $lastchat = TblChat::getLastChat($to, $post_id);
        $unread_count = TblChat::getUnreadCount($user_id, $to, $post_id);
        $data = array('lastchat' => $lastchat, 'post_id' => $post_id, 'to_id' => $to, 'unread_count' => $unread_count);
        return $data;
    }*/


    public function share_location()
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
        TblChat::create([
            'post_id' => $post_id,
            'from_id' => $user_id,
            'to_id' => $to,
            'receiver' => $receiver,
            'make_offer' => 0,
            "location" => $loc,
            "latitude" => $lat,
            "longitude" => $long
        ]);
        return response()->json(['message' => "success"]);
    }



    public function delete_chat()
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

    public function fetch_user_status()
    {
        $userlastseen = User::where('id', request()->id)->select('last_chat_seen', 'current_chat_status')->first()->toArray();
        if (!empty($userlastseen['last_chat_seen'])) {
            $current = strtotime(date("Y-m-d"));
            $lastdate = date('Y-m-d', strtotime($userlastseen['last_chat_seen']));
            $date = strtotime($lastdate);
            $datediff = $date - $current;
            $difference = floor($datediff / (60 * 60 * 24));
            if ($difference == 0) {
                return response()->json(['message' => 'Today ' . date('h:i a', strtotime($userlastseen['last_chat_seen'])), 'value' => $userlastseen['current_chat_status']]);
            } else if ($difference > 1) {
                return response()->json(['message' => date('d-m-y h:i a', strtotime($userlastseen['last_chat_seen'])), 'value' => $userlastseen['current_chat_status']]);
            } else if ($difference < -1) {
                return response()->json(['message' => date('d-m-y h:i a', strtotime($userlastseen['last_chat_seen'])), 'value' => $userlastseen['current_chat_status']]);
            } else {
                return response()->json(['message' => 'Yesterday ' . date('h:i a', strtotime($userlastseen['last_chat_seen'])), 'value' => $userlastseen['current_chat_status']]);
            }
        }
    }


    public function send_chat_fetch_last_seen()
    {
        $id = request()->id;

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
                $result = array("result"=>"online","last_seen"=>"Online");
            }
            else{
                
                $checkIt = User::where('id', $id)->get();
                if(count($checkIt)>0){
                    //$ls = TblChat::lastSeen($checkIt[0]->last_chat_seen);
                    $ls = TblChat::GetUserLastSeen($id);
                    $result = array("result"=>"offline","last_seen"=>$ls);
                }
                else{
                    $result = array("result"=>"offline","last_seen"=>"Unavailable");
                }
                
            }
            return $result;
        }

    }


    public function send_chat_update_last_seen()
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

    
    





}
