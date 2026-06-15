<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\TblLanguage;
use App\Models\Languages;


class TblChat extends Model
{

    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['id', 'accept_offer', 'location', 'longitude', 'latitude', 'from_id', 'to_id','read_id', 'msg', 'attachment', 'active', 'read_status', 'post_id', 'deleted_at', 'receiver', 'make_offer','denied_offer'];

    /* Get last chat based on user id */
	public static function getConversations($userId)
    {
        // Step 1: User se judi saari messages nikal lein, sabse nayi pehle
        $allMessages = self::where(function ($query) use ($userId) {
                          $query->where('from_id', $userId)
                                ->orWhere('to_id', $userId);
                      })
                      ->whereNotNull('msg')
                      ->whereNull('deleted_at')
                      ->latest() // Sabse nayi message pehle
                      ->get();

        // Step 2: PHP mein conversations ko uniquely group karein
        return $allMessages->groupBy(function($item) use ($userId) {
            // Doosre user ki ID nikalein
            $otherUserId = ($item->from_id == $userId) ? $item->to_id : $item->from_id;
            // Post ID aur doosre user ki ID se unique key banayein
            return $item->post_id . '-' . $otherUserId;
        })->map(function($group) {
            // Har group se sirf pehli (sabse nayi) message lein
            return $group->first();
        });
    }
  	public function post()
    {
        return $this->belongsTo(TblPost::class, 'post_id');
    }
    public static function getLastChat($sender, $post_id)
    {
        $userid = auth()->user()->id;

        $lastMessage = TblChat::where('post_id', $post_id)
            ->where(function ($q) use ($userid, $sender) {
                $q->where(function ($q2) use ($userid, $sender) {
                    $q2->where('from_id', $userid)->where('to_id', $sender);
                })->orWhere(function ($q3) use ($userid, $sender) {
                    $q3->where('from_id', $sender)->where('to_id', $userid);
                });
            })
            ->orderBy('created_at', 'desc')
            ->first();

        // YEH CHECK BOHOT ZAROORI HAI
        // Agar message milta hai to usko return karein, warna khaali array return kar dein
        if ($lastMessage) {
            return $lastMessage->toArray();
        }

        return []; // Khaali array return karne se error nahi aayega
    }

    /* Get last chat based on user id for app */

    public static function getLastChatApp($userid, $to_id, $post_id)
    {
        $lastchat = TblChat::where('post_id', $post_id)
            ->where(function ($q) use ($to_id, $userid) {
                $q->where('tbl_chats.receiver', $userid)
                    ->orWhere('tbl_chats.receiver', $to_id);
            })
            ->OrderBy('id', 'desc')
            ->select('msg','id', 'read_status', 'attachment','created_at')->first()->toArray();
        return $lastchat;
    }

    /* Get current user unread chat count */

    public static function getUnreadCount($user_id, $to_id, $post_id)
    {
        $unread_count = TblChat::where('post_id', $post_id)
            ->where('from_id', $to_id)
            ->where('to_id', $user_id)
            ->where('read_status', 0)
            ->count();
        return $unread_count;
    }

    /* Get receiver person unread chat count */

    public static function getReceiverUnreadCount($user_id, $to_id, $post_id)
    {
        $unread_count = TblChat::where('post_id', $post_id)
            ->where('from_id', $user_id)
            ->where('to_id', $to_id)
            ->where('read_status', 0)
            ->count();
        return $unread_count;
    }

    /* Get sender */

    public static function getSender($to_id)
    {
        $sender = User::where('id', $to_id)->pluck('name')->first();
        return $sender;
    }

    /* Get post image - detail page */

    public static function getPostImg($post_id)
    {
        $images = TblPost::where('id', $post_id)->pluck('images')->first();
        if (!empty($images)) {
            $imgUrl = explode(',', $images)[0];
            $imgUrl = URL::to('/storage/' . $imgUrl);
            $imgUrlfinal = $imgUrl;
        } else {
            $imgUrlfinal = URL::to('/storage/noimage150.png');
        }
        return $imgUrlfinal;
    }

    /* Get post image  - list page */

    public static function getPostImgForList($post_id)
    {
        $images = TblPost::where('id', $post_id)->pluck('images')->first();
        if (!empty($images)) {
            $imgURLs = explode(',', $images)[0];
            $exp_imgURLs = explode('/', $imgURLs);
            $imgName = end($exp_imgURLs);
            $is_file = base_path() . '/storage/adpost/predefined/list/' . $imgName;
            if (is_file($is_file)) {
                $imgUrl = URL::to('/storage/adpost/predefined/list/' . $imgName);
            } else {
                $imgUrl = URL::to('/storage/adpost/predefined/' . $imgName);
            }
            $imgUrlfinal = $imgUrl;
        } else {
            $imgUrlfinal = URL::to('/storage/noimage150.png');
        }
        return $imgUrlfinal;
    }

    /* check if user blocked by curren loggin user */

    public static function checkBlocked($to, $post)
    {
        $userid = auth()->user()->id;
        $cbekc_block = TblBlockeduser::where('post_id', $post)->where('blocked_id', $to)->where('blocked_by', $userid)->OrderBy('id', 'desc')->pluck('block_status')->first();
        return $cbekc_block;
    }

    /* check if curren loggin user is blocked by the user */

    public static function checkUserBlocked($to, $post,$userid)
    {
        // dd($to,$post,$userid);
         $cbekc_block = TblBlockeduser::where('post_id', $post)->where('blocked_id', $to)->where('blocked_by', $userid)->OrderBy('id', 'desc')->pluck('block_status')->first();
        return $cbekc_block;
    }

    public static function checkBlockedApp($to, $post, $user)
    {
        $cbekc_block = TblBlockeduser::where('post_id', $post)->where('blocked_id', $to)->where('blocked_by', $user)->OrderBy('id', 'desc')->pluck('block_status')->first();
        return $cbekc_block;
    }

    public static function checkChatDate($date ,$lang_code = null)
    {
        if (!isset($lang_code)) {
            $lang_code = TblLanguage::where('default', '=', '1')->value('abbr');
        }

        $current = strtotime(date("Y-m-d"));
        $date = strtotime($date);
        $datediff = $date - $current;
        $difference = floor($datediff / (60 * 60 * 24));
        if ($difference == 0) {
            $today = Languages::where('lang_code', $lang_code)->where('lang_org_text', 'Today')->value('lang_text');
            return  $today ;
        } else if ($difference > 1) {
            return '';
        } else if ($difference < -1) {
            return '';
        } else {
            $yesterday = Languages::where('lang_code', $lang_code)->where('lang_org_text', 'Today')->value('lang_text');
            return $yesterday;
        }
    }

    
    public static function timeAgo($date, $lang_code = null)
    {
        // Check if the language code is provided; if not, fetch the default language code
        if (!isset($lang_code)) {
            $lang_code = TblLanguage::where('default', '=', '1')->value('abbr');
        }
    
        // Translation keys for time units
        $translation_keys = [
            'year' => 'year',
            'years' => 'years',
            'month' => 'month',
            'months' => 'months',
            'day' => 'day',
            'days' => 'days',
            'hour' => 'hour',
            'hours' => 'hours',
            'minute' => 'minute',
            'minutes' => 'minutes',
            'second' => 'second',
            'seconds' => 'seconds',
            'ago' => 'ago'
        ];
    
        // Fetch translations for each time unit
        $translations = [];
        foreach ($translation_keys as $key => $value) {
            $translations[$key] = Languages::where('lang_code', $lang_code)->where('lang_org_text', $value)->value('lang_text');
        }
    
        // Current datetime and the given datetime
        $datetime1 = Carbon::now();
        $datetime2 = date_create($date);
        $diff = date_diff($datetime1, $datetime2);
        $timemsg = '';
    
        // Construct the time message using the translations
        if ($diff->y > 0) {
            $timemsg = $diff->y . ' ' . ($diff->y > 1 ? $translations['years'] : $translations['year']);
        } else if ($diff->m > 0) {
            $timemsg = $diff->m . ' ' . ($diff->m > 1 ? $translations['months'] : $translations['month']);
        } else if ($diff->d > 0) {
            $timemsg = $diff->d . ' ' . ($diff->d > 1 ? $translations['days'] : $translations['day']);
        } else if ($diff->h > 0) {
            $timemsg = $diff->h . ' ' . ($diff->h > 1 ? $translations['hours'] : $translations['hour']);
        } else if ($diff->i > 0) {
            $timemsg = $diff->i . ' ' . ($diff->i > 1 ? $translations['minutes'] : $translations['minute']);
        } else if ($diff->s > 0) {
            $timemsg = $diff->s . ' ' . ($diff->s > 1 ? $translations['seconds'] : $translations['second']);
        }
    
        // Append the "ago" translation
        $timemsgs = $timemsg . ' ' . $translations['ago'];
    
        return $timemsgs;
    }

    // chnaged 24.07.2024
    /* Update Online and last seen status */

    public static function UpdateUserStatus($uid, $status)
    {
        User::where('id', $uid)->update(array('current_chat_status' => $status, 'last_chat_seen' => date('Y-m-d H:i:s')));
    }

    public static function GetUserLastSeen($uid, $lang_code = null)
    {
        // Check if the language code is provided; if not, fetch the default language code
        if (!isset($lang_code)) {
            $lang_code = \Config::get('app.locale');
        }
        
	
        // Fetch translations for the required terms
        $translations = [
            'Today' => Languages::where('lang_code', $lang_code)->where('lang_org_text', 'Today')->value('lang_text'),
            'Yesterday' => Languages::where('lang_code', $lang_code)->where('lang_org_text', 'Yesterday')->value('lang_text'),
            'Online' => Languages::where('lang_code', $lang_code)->where('lang_org_text', 'Online')->value('lang_text'),
        ];
		
        $userlastseen = User::where('id', $uid)->first()->toArray();

        if (!empty($userlastseen['last_chat_seen'])) {
            $current = strtotime(date("Y-m-d"));
            $lastdate = date('Y-m-d', strtotime($userlastseen['last_chat_seen']));
            $date = strtotime($lastdate);
            $datediff = $date - $current;
            $difference = floor($datediff / (60 * 60 * 24));

            if ($userlastseen['current_chat_status'] == "offline") {
                if ($difference == 0) {
                    return $translations['Today'] . ' ' . date('h:i a', strtotime($userlastseen['last_chat_seen']));
                } else if ($difference == -1) {
                    return $translations['Yesterday'] . ' ' . date('h:i a', strtotime($userlastseen['last_chat_seen']));
                } else {
                    return date('d-m-y h:i a', strtotime($userlastseen['last_chat_seen']));
                }
            } else {
                return $translations['Online'];
            }
        }
        return null; // Handle the case where 'last_chat_seen' is empty or not set
    }


  	public static function getTimeZoneUser($datetimeInKolkata){
      
 	
    
    try {
      
        
            $clientTimeZone = session('user_timezone', 'UTC');

          	// Parse the input datetime with the UTC timezone
            $carbonDatetime = Carbon::parse($datetimeInKolkata, 'UTC');

            // Convert to client's timezone
            $carbonDatetimeInClientTimezone = $carbonDatetime->setTimezone($clientTimeZone);

            // Format the datetime in the desired format
            $formattedDatetimeInClientTimezone = $carbonDatetimeInClientTimezone->format('Y-m-d\TH:i:s.u\Z');

           $data =  array(
                'original_datetime' => $datetimeInKolkata,
                'original_timezone' => 'UTC',
                'converted_datetime' => $formattedDatetimeInClientTimezone,
                'client_timezone' => $clientTimeZone,
            );
          return $data;
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }

    return response()->json(['error' => 'Unable to determine client timezone'], 500);
    }
  
  	public static function getTimeZoneUserApp($datetimeInKolkata, $clientTimeZone = null){
      
      if(!isset($clientTimeZone)){
        
        $clientTimeZone = "Asia/Kolkata";
      }
        
 	
    
    try {
      
      		
        	// Parse the input datetime with the UTC timezone
            $carbonDatetime = Carbon::parse($datetimeInKolkata, 'UTC');

            // Convert to client's timezone
            $carbonDatetimeInClientTimezone = $carbonDatetime->setTimezone($clientTimeZone);

            // Format the datetime in the desired format
            $formattedDatetimeInClientTimezone = $carbonDatetimeInClientTimezone->format('Y-m-d\TH:i:s.u\Z');

           $data =  array(
                'original_datetime' => $datetimeInKolkata,
                'original_timezone' => 'UTC',
                'converted_datetime' => $formattedDatetimeInClientTimezone,
                'client_timezone' => $clientTimeZone,
            );
          return $data;
        
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }

    return response()->json(['error' => 'Unable to determine client timezone'], 500);
    }
}
