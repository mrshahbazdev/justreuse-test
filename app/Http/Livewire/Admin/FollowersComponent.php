<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\TblPost;
use App\Models\TblFollowers;
use App\Models\TblInvitedFriends;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\InviteFriendMail;

class FollowersComponent extends Component {

    public $search;
    
    public function render() {
       
        $seg2 = request()->segment(2);

        $user_ids = TblFollowers::where('seller_id', $seg2)->where('is_followed',1)->pluck('user_id');
        
        $seller_ids = TblFollowers::where('user_id', $seg2)->where('is_followed',1)->pluck('seller_id');  

        $seller_posts = TblPost::select("tbl_posts.id as id","tbl_posts.slug as slug","tbl_posts.title as title","tbl_posts.created_at as created_at","tbl_posts.price as price", "tbl_cities.name as city_name")
        ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
        ->where('tbl_posts.user_id', $seg2)
        ->paginate(12);

        
        $seller_info = User::where('id', $seg2)->whereNull('deleted_at')->first();
        if(empty($seller_info)){
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
                        
        return view('livewire.front-followers', ['followers' => $followers,'followings'=>$followings,'seller_info' => $seller_info, 'seller_posts' => $seller_posts]);
    }

    public function savefollowers() {
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
                    if($check->is_followed == 1){
                        TblFollowers::where('id', $check->id)->update(array('is_followed' => 0));
                        $result = "success";
                        $flag = "0";
                        $message = "Unfollowed successfully!";
                    }else{
                        TblFollowers::where('id', $check->id)->update(array('is_followed' => 1));
                        $result = "success";
                        $flag = "1";
                        $message = "Now you are following the seller!";
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
                }
            }
            return response()->json(['result' => $result, 'flag' => $flag, 'message' => $message]);
            
        }
    }

    public function invite_friend() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {   
            $formdata = request()->all();
            $email_ids = $formdata['email_ids']; 
            $user_id = Auth::user()->id;
            $check = User::where('email', $email_ids)->whereNull('deleted_at')->first();
            if (!empty($check)) {
                $result = "error";
                $message = "Email Id already exist!";       
            } else {
                $check_invite = TblInvitedFriends::where('email', $email_ids)->where('user_id',$user_id)->whereNull('deleted_at')->first();
                if(empty($check_invite)){
                    // $details = [
                        // 'title' => 'Thankyou',
                        // 'body'=>'Invite friend'
                    // ];
					$details = [
                        'title' => 'Your friend has sent a invitation.',
                        'body'=> Auth::user()->name. ' Invite to you. Click below link and register now.!'
                    ];
                    Mail::to($email_ids)->send(new InviteFriendMail($details));
                    TblInvitedFriends::create([
                        'user_id' => $user_id,
                        'email' => $email_ids,
                    ]);   
                                     
                    $result = "success";
                    $message = "Invitation sent successfully!";
                }else{
                    $result = "error";
                    $message = "Already you are invited this email ID!"; 
                }
                               
            }
            return response()->json(['result' => $result, 'message' => $message]);            
        }
    }

    public function destroy($id) {
        // unfollow the seller
        if ($id) {
            TblFollowers::where('id', $id)->update(array('is_followed' => 0));
        }
    }

}
