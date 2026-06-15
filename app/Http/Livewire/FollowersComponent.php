<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\TblPost;
use App\Models\Setting;
use App\Models\TblCategory;
use App\Models\TblFollowers;
use App\Models\TblSellerReviews; 
use App\Models\TblInvitedFriends;
use App\Models\TblPayment;
use App\Models\TblPostedAdPackageInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\InviteFriendMail;
use App\Models\ReportType;
use App\Models\TblReportThisUser;
use Exception;
use Illuminate\Support\Facades\URL;
use App\Models\TblBuynowOrder;


class FollowersComponent extends Component
{

    public $search,$isbuy;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
    try{
        $seg2 = request()->segment(2);

        $seg3 = request()->segment(3);
        
        $currentUserId = !empty(auth()->user()->id) ? auth()->user()->id : "";
        $profile_id =  User::find($seg2);
        // dd($currentUserId,$profile_id);
        
        $isbuy = TblBuynowOrder::where('user_id',$currentUserId )
        ->where('seller_id',$profile_id->id)
        ->exists() ;
        $is_buy = 0;
        // dd($isbuy,$currentUserId,$profile_id);
      if($isbuy == 'true'){
        $is_buy = 1;
      }
        
        if (!empty($seg3)) {
            $subcategory = TblCategory::descendantsAndSelf($seg3);
            foreach ($subcategory as $subcat) {
                $sids[] = $subcat->id;
            }
        }

        $user_ids = TblFollowers::where('seller_id', $seg2)->where('is_followed', 1)->pluck('user_id');

        $seller_ids = TblFollowers::where('user_id', $seg2)->where('is_followed', 1)->pluck('seller_id');

        /* get unexpired payment post */
        $payment_ids_array = TblPost::get_unexpired_payment_post_ids();

        /* get unexpired free post */
        $free_ids_array = TblPost::get_unexpired_free_post_ids();

        $final_result_ids = array_merge($payment_ids_array, $free_ids_array);
        
        if(isset($seg3) && !empty($seg3)){

            $seller_posts = TblPost::select("tbl_posts.id as id", "tbl_posts.category_id", "tbl_posts.currency_id as currency_id", "tbl_posts.slug as slug", "tbl_posts.title as title", "tbl_posts.locality as locality", "tbl_posts.created_at as created_at", "tbl_posts.price as price", "tbl_cities.name as city_name")
            ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
            ->where('tbl_posts.user_id', $seg2)
            ->whereIn('tbl_posts.category_id',$sids)
            ->where('tbl_posts.active', 1)
            ->where('tbl_posts.sold_status', 0)
            ->whereIn('tbl_posts.id', $final_result_ids)
            ->paginate(40);

        }else{

            $seller_posts = TblPost::select("tbl_posts.id as id", "tbl_posts.category_id", "tbl_posts.currency_id as currency_id", "tbl_posts.slug as slug", "tbl_posts.title as title", "tbl_posts.locality as locality", "tbl_posts.created_at as created_at", "tbl_posts.price as price", "tbl_cities.name as city_name")
            ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
            ->where('tbl_posts.user_id', $seg2)
            ->where('tbl_posts.active', 1)
            ->where('tbl_posts.sold_status', 0)
            ->whereIn('tbl_posts.id', $final_result_ids)
            ->paginate(40);
        }
        $seller_info = User::where('id', $seg2)->whereNull('deleted_at')->first();
        $seller_id = $seller_info->id;
        if (empty($seller_info)) {
            abort(404);
        }

        $followers = User::select("users.*")
            ->whereIn('users.id', $user_ids)
            ->whereNull('users.deleted_at')
            ->where('users.name', 'like', '%' . $this->search . '%')->get();

        $followings = User::select("users.*")
            ->whereIn('users.id', $seller_ids)
            ->whereNull('users.deleted_at')
            ->where('users.name', 'like', '%' . $this->search . '%')->get();
            

        $report_types = ReportType::where('type', 'user')->get();

        $seller_review_count =TblSellerReviews::where('seller_id',$seller_id)->where('approved','1')->count();

        $seller_review = TblSellerReviews::where('seller_id',$seller_id)
        ->join('users',"tbl_seller_reviews.user_id",'=','users.id')
        ->get();

        $info_id = TblSellerReviews::where('seller_id',$seller_id)->value('user_id');

        $user_info = Db::table('users')->where('id',$info_id)->get();
        // array_push($seller_review, $user_info);
        $avg_rating = TblSellerReviews::rate_avg($seller_id);


        return view('livewire.front-followers', ['followers' => $followers,'is_buy'=> $is_buy ,'followings' => $followings, 'seller_info' => $seller_info, 'seller_posts' => $seller_posts, 'report_types' => $report_types,'seller_review_count' =>$seller_review_count,'review' => $seller_review,'seller_review_user'=>$user_info,'seller_id' => $seller_id,'avg_rating' => $avg_rating,'final_id'=>$final_result_ids]);
    }catch(Exception $e){
        abort(404);
    }
    }


    public function my_followers()
    {


        $seg2 = Auth()->id();

        $user_ids = TblFollowers::where('seller_id', $seg2)->where('is_followed', 1)->pluck('user_id');
        $seller_ids = TblFollowers::where('user_id', $seg2)->where('is_followed', 1)->pluck('seller_id');

        $followers = User::select("users.*")
            ->whereIn('users.id', $user_ids)
            ->whereNull('users.deleted_at')
            ->where('users.name', 'like', '%' . $this->search . '%')->paginate(12);

        $followings = User::select("users.*")
            ->whereIn('users.id', $seller_ids)
            ->whereNull('users.deleted_at')
            ->where('users.name', 'like', '%' . $this->search . '%')->paginate(12);



        return view('livewire.my-followers', ['followers' => $followers, 'followings' => $followings]);
    }


    public function my_followings()
    {


        $seg2 = Auth()->id();

        $user_ids = TblFollowers::where('seller_id', $seg2)->where('is_followed', 1)->pluck('user_id');
        $seller_ids = TblFollowers::where('user_id', $seg2)->where('is_followed', 1)->pluck('seller_id');


        $followings = User::select("users.*")
            ->whereIn('users.id', $seller_ids)
            ->whereNull('users.deleted_at')
            ->where('users.name', 'like', '%' . $this->search . '%')->paginate(12);

        $followers = User::select("users.*")
            ->whereIn('users.id', $user_ids)
            ->whereNull('users.deleted_at')
            ->where('users.name', 'like', '%' . $this->search . '%')->paginate(12);

        return view('livewire.my-followers', ['followers' => $followers, 'followings' => $followings]);
    }



    public function savefollowers()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $formdata = request()->all();
            if (Auth::user() == null) {
                $result = "failed";
                $flag = "0";
                $message = "Please login";
            } else {
                $user_id = Auth::user()->id;
                $seller_id = $formdata['seller_id'];   //post id
                $check = TblFollowers::where('user_id', $user_id)->where('seller_id', $seller_id)->first();
                if (!empty($check)) {
                    if ($check->is_followed == 1) {
                        TblFollowers::where('id', $check->id)->update(array('is_followed' => 0));
                        $result = "success";
                        $flag = "0";
                        $message = "Unfollowed successfully!";
                    } else {
                        TblFollowers::where('id', $check->id)->update(array('is_followed' => 1));
                        $result = "success";
                        $flag = "1";
                        $message = "Now you are following the seller!";

                        // notification start
                        $get_user_info = User::where('id', $user_id)->first();
						$slug = URL::to('seller-profile/'.$user_id);
                        $get_seller_info = User::where('id', $seller_id)->first();
                        //$get_post_info = TblPost::where('id', $post_id)->first();

                        $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
                        $message1 = array("notifydata" => array('to_id' => $seller_id, 'from_id' => $user_id, 'message' => $get_user_info->name . " Following you.!", 'notify_from' => 'following', 'notify_title' => "New Following In Letgo!..", 'post_id' => "", 'slug' => ""));

                        TblPost::send_push_notification($fcmid, $message1);
						
						$mail_data = array("send_maildata" => array('to_id' => $seller_id, 'message' => $get_user_info->name . " Following you.!", 'subject' => "New Following In Letgo!..",'ad_url' => $slug));
						$mail_key = "new_follower";
						Setting::notification_mail($mail_data, $mail_key);
						
                        // notification end

                    }
                } else {
                    TblFollowers::create([
                        'user_id' => $user_id,
                        'seller_id' => $seller_id,
                        'is_followed' => 1
                    ]);
                    $result = "success";
                    $flag = "1";
                    $message = "Now you are following the seller!";

                    // notification start
                    $get_user_info = User::where('id', $user_id)->first();
					$slug = URL::to('seller-profile/'.$user_id);
                    $get_seller_info = User::where('id', $seller_id)->first();
                    //$get_post_info = TblPost::where('id', $post_id)->first();

                    $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";

                    $message1 = array("notifydata" => array('to_id' => $seller_id, 'from_id' => $user_id, 'message' => $get_user_info->name . " Following you.!", 'notify_from' => 'following', 'notify_title' => "New Following In Letgo!..", 'post_id' => "", 'slug' => ""));

                    TblPost::send_push_notification($fcmid, $message1);
					
					$mail_data = array("send_maildata" => array('to_id' => $seller_id, 'message' => $get_user_info->name . " Following you.!", 'subject' => "New Following In Letgo!..",'ad_url' => $slug));
					$mail_key = "new_follower";
					Setting::notification_mail($mail_data, $mail_key);
                    // notification end

                }
            }
            return response()->json(['result' => $result, 'flag' => $flag, 'message' => $message]);
        }
    }

    public function invite_friend()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $formdata = request()->all();
            $email_ids = $formdata['email_ids'];
            $user_id = Auth::user()->id;
            $check = User::where('email', $email_ids)->whereNull('deleted_at')->first();
            if (!empty($check)) {   
               $result = "error";
               $message = "Email Id already exist!";
			} else {
                $check_invite = TblInvitedFriends::where('email', $email_ids)->where('user_id', $user_id)->whereNull('deleted_at')->first();
                if (empty($check_invite)) {
                    //$details = [
                       // 'title' => 'Your friend has sent a invitation.',
                      //  'body' => Auth::user()->name . ' Invite to you. Click below link and register now.!'
                    //];
                   // Mail::to($email_ids)->send(new InviteFriendMail($details));
					
					//send mail start
					$slug = URL::to('seller-profile/'.$user_id);
					$mail_data = array("send_maildata" => array('to_id' => $email_ids, 'message' => Auth::user()->name . ' Invite to you. Click below link and register now.!', 'subject' => "New Friend Invitation In Letgo!..",'ad_url' => $slug));
					$mail_key = "invite_friend";
					Setting::notification_mail($mail_data, $mail_key);
					
					// send mail end
					
                    TblInvitedFriends::create([
                        'user_id' => $user_id,
                        'email' => $email_ids,
                    ]);

                    $result = "success";
                    $message = "Invitation sent successfully!";
                } else {
                    $result = "error";
                    $message = "Already you are invited this email ID!";
                }
            }
            return response()->json(['result' => $result, 'message' => $message]);
        }
    }

    public function destroy($id)
    {
        // unfollow the seller
        if ($id) {
            TblFollowers::where('id', $id)->update(array('is_followed' => 0));
        }
    }



    public function report_user()
    {

        $re_type = request()->retype;
        $user_id = request()->user;
        $comment = request()->comment;
        $reportUser = request()->reportUser;

        $check = TblReportThisUser::where('user_id', $user_id)->where('reported_user_id', $reportUser)->first();

        if (empty($check)) {
            if ($user_id !== $reportUser) {
                TblReportThisUser::create([
                    'user_id' => $user_id,
                    'reported_user_id' => $reportUser,
                    'report_type_id' => $re_type,
                    'comment' => $comment,
                ]);

                $message = "Your report has been taken, we will take action soon as possible..";
            } else {
                $message = "You can't report yourself!";
            }
        } else {

            $message = "You already repoted this user!";
        }

        return response()->json(['message' => $message]);
    }
}
