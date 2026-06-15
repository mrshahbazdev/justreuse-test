<?php

namespace App\Http\Livewire;

use App\Models\Setting;
use App\Models\TblBuynowOrder;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\TblPost;
use App\Models\TblReportThisAd;
use App\Models\TblExchangedPost;
use App\Models\TblPostValue;
use App\Models\TblSavedPosts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TblReview;
use Illuminate\Support\Facades\URL;

class SavedPostComponent extends Component {

    public function render() {
    }

    //testing component
    public function save() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $formdata = request()->all();
            if (Auth::user() == null) {
                $result = "failed";
                $flag = "0";
                $message = "Please login";
            } else {

                $settings = Setting::get_logos();
                $site_name = $settings['name'];

                $user_id = Auth::user()->id;
                $post_id = $formdata['post_id'];   //post id
                $check = TblSavedPosts::where('user_id', $user_id)->where('post_id', $post_id)->get();
                if ($check->count() > 0) {
                    $bb = TblSavedPosts::where('post_id', $post_id)->get()[0]->id;
                    TblSavedPosts::find($bb)->delete();
                    $result = "success";
                    $flag = "0";
                    $message = "Removed Favorite successfully..";

                // notification start
                    // $get_user_info = User::where('id', $user_id)->first();
                    // $get_post_info = TblPost::where('id', $post_id)->first();
                    // $get_seller_info = User::where('id', $get_post_info->user_id)->first();
                    // $slug = TblPost::get_post_slug($get_post_info->slug);

                    // $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";
    
                    // $message2 = array("notifydata" => array('to_id' => $get_post_info->user_id,'from_id'=>$user_id, 'message' => "Post removed from whish list by ".$get_user_info->name."!. Post Name - " . $get_post_info->title, 'notify_from' => 'post_like_remove', 'notify_title' => "Post removed from whish list In ".$site_name."!..",'post_id' => $post_id,'slug' => $slug));
            
                    // TblPost::send_push_notification($fcmid, $message2);
                 // notification end

                } else {
                    TblSavedPosts::create([
                        'user_id' => $user_id,
                        'post_id' => $post_id,
                    ]);
                    $result = "success";
                    $flag = "1";
                    $message = "Added to Favorite successfully..";
			// notification start
                //     $get_user_info = User::where('id', $user_id)->first();
                //     $get_post_info = TblPost::where('id', $post_id)->first();
                //     $get_seller_info = User::where('id', $get_post_info->user_id)->first();
                //     $slug = TblPost::get_post_slug($get_post_info->slug);

                // $fcmid = !empty($get_seller_info->fcmid) ? $get_seller_info->fcmid : "";

                // $message1 = array("notifydata" => array('to_id' => $get_post_info->user_id,'from_id'=>$user_id, 'message' => "Liked your post by ".$get_user_info->name."!. Post Name - " . $get_post_info->title, 'notify_from' => 'post_like', 'notify_title' => "Like a post In ".$site_name."!..",'post_id' => $post_id,'slug' => $slug));

                // TblPost::send_push_notification($fcmid, $message1);
            // notification end
					
                }
            }
            return response()->json(['result' => $result, 'flag' => $flag, 'message' => $message]);
        }
    }

    // delete all favourite post here...
    public function delete_fav_add() {
        $ids = request()->ids;
        TblSavedPosts::whereIn('id', explode(",", $ids))->delete();
        return response()->json(['message' => "Removed Favorite successfully.."]);
    }

    public function delete_posted_add() {

        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            return response()->json(['message' => $isDemoUser["message"]]);
        }
        
        $ids = request()->ids;
        $post_ids = explode(",", $ids);
        
        $i = 0;
        foreach($post_ids as $id)
        {
            
            $incomming = TblExchangedPost::where('post_id', $id)->where(function ($q) {
                $q->where('status', 'pending')
                    ->orWhere('status', 'accepted');
            })->pluck('status')->toArray();
        
            
            $check_buynow_order = TblBuynowOrder::where('post_id', $id)->orderBy('id', 'desc')->pluck('order_status')->first();

            if(!empty($check_buynow_order) && $check_buynow_order != "delivered")
            {
                $i += 1;
            }else if(!empty($incomming) && count($incomming) != 0)
            {
                $i += 1;
            }
            else{

                // delete process start

                $post = TblPost::where('id', $id)->first();
                // image delete
             if(!empty($post->images))
             {
                $url = URL::to('storage/') . '/';
                $old_img = str_replace($url, '', $post->images);
                /* remove the deleted image from the storage folder start */
                if (strpos($post->images, 'applist') !== false) {
                    $unmatched_img_name = str_replace("adpost/applist/", '', $old_img);
                } else {
                    $unmatched_img_name = str_replace("adpost/predefined/", '', $old_img);
                }

                /* remove web normal img file */
                if (is_file(base_path() . '/storage/app/public/adpost/predefined/normal/' . $unmatched_img_name)) {
                    $path = base_path() . '/storage/app/public/adpost/predefined/normal/' . $unmatched_img_name;
                    unlink($path);
                }

                /* remove web list img file */
                if (is_file(base_path() . '/storage/app/public/adpost/predefined/list/' . $unmatched_img_name)) {
                    $path = base_path() . '/storage/app/public/adpost/predefined/list/' . $unmatched_img_name;
                    unlink($path);
                }

                /* remove web image file */
                if (is_file(base_path() . '/storage/app/public/adpost/predefined/' . $unmatched_img_name)) {
                    $path = base_path() . '/storage/app/public/adpost/predefined/' . $unmatched_img_name;
                    unlink($path);
                }
                /* remove image file from app list folder */
                if (is_file(base_path() . '/storage/app/public/adpost/applist/' . $unmatched_img_name)) {
                    $app_list = base_path() . '/storage/app/public/adpost/applist/' . $unmatched_img_name;
                    unlink($app_list);
                }
                /* remove image file from app detail folder */
                if (is_file(base_path() . '/storage/app/public/adpost/appdetail/' . $unmatched_img_name)) {
                    $app_detail = base_path() . '/storage/app/public/adpost/appdetail/' . $unmatched_img_name;
                    unlink($app_detail);
                }
                $post_imgs = TblPost::where('id', $post->id)->first();
                $array = explode(',', $post_imgs['images']);
                $array = array_map(function ($value) {
                    return str_replace("adpost/predefined/", '', $value);
                }, $array);
                $array = \array_diff($array, [$unmatched_img_name]);
                $data = array();
                $newimg = array();
                foreach ($array as $img) {
                    if (!empty($img)) {
                        $newimg[] = "adpost/predefined/" . $img;
                        $data[] = URL::to('storage/adpost/predefined/' . $img);
                    }
                }
                $final_imgs = !empty($newimg) ? implode(',', $newimg) : '';
                $post_imgs->update([
                    'images' => $final_imgs
                ]);
            }
                // image delete
                
                $post_del = TblPost::where('id', $id)->delete();
                $post_del_value = TblPostValue::where('post_id', $id)->update(['active' => 0]);

            }


        }
        // loop end
    
        if($i != 0)
        {
            return response()->json(['message' => "Some posts can't deleted. because these posts have sales orders or incoming exchanges."]);
        }else{
            return response()->json(['message' => "Removed Posted-Add successfully.."]);
        }
    }

    //block user - admin side
    public function user_blocked() {
        $block_user = request()->blocked;
        $user_id = request()->id;
        $user = User::find($user_id);
        $slug = url('/');
        $user->update([
            'is_blocked' => $block_user,
        ]);
        if ($block_user == 1) {

            $mail_data = array("send_maildata" => array('to_id' => $user_id, 'message' => "Your account has been deleted for some reason. Contact us if you want to activate your account.", 'subject' => "Account Deleted by Admin",'ad_url' => $slug));
            $mail_key = "admin_account_deleted";
            Setting::notification_mail($mail_data, $mail_key);

            return response()->json(['message' => "User Blocked successfully.."]);
        } else {

            $mail_data = array("send_maildata" => array('to_id' => $user_id, 'message' => "Your account has been activated successfully.", 'subject' => "Account Activated by Admin",'ad_url' => $slug));
            $mail_key = "admin_account_activated";
            Setting::notification_mail($mail_data, $mail_key);

            return response()->json(['message' => "User Un-Blocked successfully.."]);
        }
    }

    //delete report-ad
    public function delete_report() {
        $ids = request()->ids;
        TblReportThisAd::whereIn('id', explode(",", $ids))->delete();
        return response()->json(['message' => "Removed Select Reports successfully.."]);
    }

    //delete reviews
    public function delete_review() {
        $ids = request()->ids;
        TblReview::whereIn('id', explode(",", $ids))->delete();
        return response()->json(['message' => "Removed Select Reviews successfully.."]);
    } 

}
