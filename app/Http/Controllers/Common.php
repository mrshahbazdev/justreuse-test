<?php

namespace App\Http\Controllers;

use App\Console\Commands\Userdelete;
use App\Models\Setting;
use App\Models\TblChat;
use App\Models\TblReview;
use App\Models\TblPost;
use App\Models\User;
use App\Models\TblFollowers;
use App\Models\TblNotifications;
use App\Models\TblPayment;
use App\Models\TblPostedAdPackageInfo;
use App\Models\TblPostValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Models\TblSellerReviews;
use Carbon\Carbon;
use App\Models\TblOtherpage;
use App\Models\TblStaticpage;
use App\Models\TblCategory;
use Database\Seeders\UserProfile;
use Illuminate\Support\Facades\Session;

class Common extends Controller
{
    public function ReviewStore()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $formdata = request()->all();
            $post_id = $formdata["post_id"];
            $user_id = $formdata["user_id"];
            $rateval = $formdata["review_ratings"];
            $review_text = $formdata["review_text"];
            $r_url = $formdata["redirect_url"];
            $toUrl = url('/' . $r_url);
            //dd($formdata);
            $checkExist = TblReview::where('post_id', $post_id)->where('user_id', $user_id)->get();
            if ($checkExist->count() == 0) {
                $createe = TblReview::create([
                    "post_id" => $post_id,
                    "user_id" => $user_id,
                    "ratings" => $rateval,
                    "comment" => $review_text,
                    "approved" => '0',
                    "spam" => '0'
                ]);
            }
            return redirect($toUrl);
        }
    }
    public function get_notification_count()
    {
        if (auth()->user()->id) {
            $notifications = TblNotifications::where('to_id', auth()->user()->id)->where('read_status', 0)->count();
            return response()->json(['count' => $notifications]);
        } else {
            return response()->json(['count' => 0]);
        }
    }
    // get unread chat count overall
    public function get_unread_chat_count()
    {
        // $unread_count = TblChat::where('to_id', auth()->user()->id)
        // ->whereNull('deleted_at')
        // ->where('read_status', 0)
        // ->count();
        $userid = auth()->user()->id;
        $chatlists = TblChat::where('tbl_chats.from_id', $userid)
            ->join('tbl_posts', function ($join) {
                $join->on('tbl_chats.post_id', '=', 'tbl_posts.id')
                    ->whereNull('tbl_posts.deleted_at')
                    ->where('tbl_posts.sold_status', 0);
            })
            ->orWhere('tbl_chats.to_id', $userid)
            ->whereNotNull('tbl_chats.msg')
            ->whereNull('tbl_chats.deleted_at')
            ->groupBy('tbl_chats.post_id', 'tbl_chats.receiver')
            ->orderBy('tbl_chats.created_at', 'desc')
            ->get(['tbl_chats.*', 'tbl_posts.title as post_name']);
        $total_unread_count = 0;
        foreach ($chatlists as $chatlist) {
            $visible_posts = TblPost::check_payment_pack_expired($chatlist->post_id);
            if (!empty($visible_posts)) {
                $sender = (($userid == $chatlist->from_id) ? $chatlist->to_id : $chatlist->from_id);
                $unread_count = TblChat::getUnreadCount($userid, $sender, $chatlist->post_id);
                $total_unread_count += $unread_count;
            }
        }
        return response()->json(['count' => $total_unread_count]);
    }
    // send notification for expire post.
    public function notify_today_expire_post()
    {
        $blockedUsers = User::blocked_users();
        // current date expire post start
        /* get if post is there in payment ads */
        $today_date = date("Y-m-d");
        $check_post_payment = TblPayment::where('active', '1')->whereNotIn('user_id', $blockedUsers)->whereDate('end_date', '=', $today_date)->pluck('post_id')->toArray();
        /* get if post is there in free ads */
        $check_post_free = TblPostedAdPackageInfo::where('active', '1')->whereNotIn('user_id', $blockedUsers)->whereDate('end_date', '=', $today_date)->pluck('post_id')->toArray();
        $expire_today_posts = array_merge($check_post_payment, $check_post_free);
        if (!empty($expire_today_posts)) {
            $get_post = TblPost::whereIn('id', $expire_today_posts)
                ->where('active', 1)
                ->where('sold_status', 0)->get();
            foreach ($get_post as $row) {
                $user_id = $row->user_id;
                $post_id = $row->id;
                $post_title = $row->title;
                $slug = url('/post');
                $get_admin = User::role('superadmin')->get();
                $admin_id = $get_admin[0]->id;
                // check if notification sent or not
                $chk_notify = TblNotifications::where('notify_from', 'post_expire_today')->where('post_id', $post_id)->where('from_id', $admin_id)->where('to_id', $user_id)->get();
                if ($chk_notify->count() == 0) {
                    // notification start
                    $settings = Setting::get_logos();
                    $site_name = $settings['name'];
                    // $get_user_info = User::where('id', $user_id)->first();
                    // $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
                    // $message = array("notifydata" => array('to_id' => $user_id, 'from_id' => $admin_id, 'message' => "Your post will expire today!. Post Name - " . $post_title, 'notify_from' => 'post_expire_today', 'notify_title' => "Post expire today In " . $site_name . " !..", 'post_id' => $post_id, 'slug' => $slug));
                    // TblPost::send_push_notification($fcmid, $message);
                    // $mail_data = array("send_maildata" => array('to_id' => $user_id, 'message' => "Your post will expire today!. Post Name - " . $post_title, 'subject' => "Post expire today In " . $site_name . " !..", 'ad_url' => $slug));
                    // $mail_key = "post_expire_today";
                    // Setting::notification_mail($mail_data, $mail_key);
                    // notification end
                    echo "notification sent successfully.";
                    echo "<br>";
                } else {
                    echo "notification already sent.";
                    echo "<br>";
                }
            }
        } else {
            echo "No data";
        }
        // current date expire post end
    }
    public function notify_yesterday_expire_post()
    {
        // previous date expire post start
        $blockedUsers = User::blocked_users();
        $previous_date = date('Y-m-d', strtotime("-1 days"));
        $expired_pay_post = TblPayment::where('active', '1')->whereNotIn('user_id', $blockedUsers)->whereDate('end_date', '=', $previous_date)->pluck('post_id')->toArray();
        $expired_free_post = TblPostedAdPackageInfo::where('active', '1')->whereNotIn('user_id', $blockedUsers)->whereDate('end_date', '=', $previous_date)->pluck('post_id')->toArray();
        $expire_yesterday_posts = array_merge($expired_pay_post, $expired_free_post);
        if (!empty($expire_yesterday_posts)) {
            $get_exp_post = TblPost::whereIn('id', $expire_yesterday_posts)
                ->where('active', 1)
                ->where('sold_status', 0)->get();
            foreach ($get_exp_post as $exp_row) {
                $user_id = $exp_row->user_id;
                $post_id = $exp_row->id;
                $post_title = $exp_row->title;
                $slug = url('/post');
                $get_admin = User::role('superadmin')->get();
                $admin_id = $get_admin[0]->id;
                // check if notification sent or not
                $chk_notify = TblNotifications::where('notify_from', 'post_expired')->where('post_id', $post_id)->where('from_id', $admin_id)->where('to_id', $user_id)->get();
                if ($chk_notify->count() == 0) {
                    // notification start
                    $settings = Setting::get_logos();
                    $site_name = $settings['name'];
                    $get_user_info = User::where('id', $user_id)->first();
                    $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
                    $message = array("notifydata" => array('to_id' => $user_id, 'from_id' => $admin_id, 'message' => "Your post has been expired!. Post Name - " . $post_title, 'notify_from' => 'post_expired', 'notify_title' => "Post expired In " . $site_name . " !..", 'post_id' => $post_id, 'slug' => $slug));
                    TblPost::send_push_notification($fcmid, $message);
                    $mail_data = array("send_maildata" => array('to_id' => $user_id, 'message' => "Your post has been expired!. Post Name - " . $post_title, 'subject' => "Post expired In " . $site_name . " !..", 'ad_url' => $slug));
                    $mail_key = "post_expire_yesterday";
                    Setting::notification_mail($mail_data, $mail_key);
                    // notification end
                    echo "notification sent successfully.!";
                    echo "<br>";
                } else {
                    echo "notification already sent.";
                    echo "<br>";
                }
            }
        }
        // previous date expire post end
    }
    public function cron_delete_posted_ads()
    {
        $today_date = date("Y-m-d");
        $before_two_date = date('Y-m-d', strtotime("-2 days"));
        $after_posted_date = "2022-01-10";
        $get_post_ids = TblPost::whereDate('created_at', '>=', $after_posted_date)->whereDate('created_at', '>=', $before_two_date)->get();
        // $get_post_id="bb63fa08-bc4d-44dd-be7f-3d69859fadf7";
        // $get_post_ids = TblPost::where('id',$get_post_id)->get();
        if (count($get_post_ids) > 0) {
            foreach ($get_post_ids as $post) {
                // image delete
                if (!empty($post->images)) {
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
                $delete_post =  TblPost::where('id', $post->id)->delete();
                // $node = TblPostValue::where('post_id', $post->id)->update(['active' => 0]);
                $node = TblPostValue::where('post_id', $post->id)->delete();
            }
            echo "Posts deleted successfully.";
        } else {
            echo "Posts already deleted.";
        }
    }
    public function show_in_home()
    {
        //start check demo user
        $isDemoUser = User::isDemoUser();
        if ($isDemoUser["result"] == true) {
            return response()->json(['message' => $isDemoUser["message"]]);
        }
        //end check demo user
        $id = request()->id;
        $show_in_home_val = request()->show_in_home_val;
        $active = TblCategory::find($id);
        $active->update([
            'show_in_home' => $show_in_home_val,
        ]);
        return response()->json(['message' => 'show in home']);
    }
    public function enable_disable()
    {
        //start check demo user
        $isDemoUser = User::isDemoUser();
        if ($isDemoUser["result"] == true) {
            return response()->json(['message' => $isDemoUser["message"]]);
        }
        //end check demo user
        $id = request()->id;
        $enable_disable_val = request()->enable_disable_val;
        $active = TblCategory::find($id);
        // dd($active,$id,$enable_disable_val);
        $active->update([
            'enable_disable' => $enable_disable_val,
        ]);
        return response()->json(['message' => 'successfully updated']);
    }
    public function buynow_enable_disable()
    {
        //start check demo user
        $isDemoUser = User::isDemoUser();
        if ($isDemoUser["result"] == true) {
            return response()->json(['message' => $isDemoUser["message"]]);
        }
        //end check demo user
        $id = request()->id;
        $buynow_enable_disable_val = request()->buynow_enable_disable_val;
        $active = TblCategory::find($id);
        // dd($active,$id,$buynow_enable_disable_val);
        $active->update([
            'buynow' => $buynow_enable_disable_val,
        ]);
        TblCategory::where('parent_id', $id)->update([
            'buynow' => $buynow_enable_disable_val,
        ]);
        return response()->json(['message' => 'successfully updated']);
    }
    public function exchange_enable_disable()
    {
        //start check demo user
        $isDemoUser = User::isDemoUser();
        if ($isDemoUser["result"] == true) {
            return response()->json(['message' => $isDemoUser["message"]]);
        }
        //end check demo user
        $id = request()->id;
        $exchange_enable_disable_val = request()->exchange_enable_disable_val;
        $active = TblCategory::find($id);
        // dd($active,$id,$buynow_enable_disable_val);
        $active->update([
            'exchange' => $exchange_enable_disable_val,
        ]);
        TblCategory::where('parent_id', $id)->update([
            'exchange' => $exchange_enable_disable_val,
        ]);
        return response()->json(['message' => 'successfully updated']);
    }
    public function get_buynow()
    {
        //start check demo user
        $isDemoUser = User::isDemoUser();
        if ($isDemoUser["result"] == true) {
            return response()->json(['message' => $isDemoUser["message"]]);
        }
        //end check demo user
        $id = request()->id;
        $result = 0;
        $active = TblCategory::find($id);
        if ($active->buynow == 1) {
            $result = 1;
        } else {
            $result = 0;
        }
        return response()->json(['message' => 'successfully updated', 'is_active' => $result]);
    }
    public function get_exchange()
    {
        //start check demo user
        $isDemoUser = User::isDemoUser();
        if ($isDemoUser["result"] == true) {
            return response()->json(['message' => $isDemoUser["message"]]);
        }
        //end check demo user
        $id = request()->id;
        $result = 0;
        $active = TblCategory::find($id);
        if ($active->exchange == 1) {
            $result = 1;
        } else {
            $result = 0;
        }
        return response()->json(['message' => 'successfully updated', 'is_active' => $result]);
    }
    public function SellerReviewStore()
    {
        //dd('rrrr');
        // dd(request()->all());
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $formdata = request()->all();
            $seller_id = $formdata["seller_id"];
            $user_id = $formdata["user_id"];
            $rateval = $formdata["review_ratings"];
            $review_text = $formdata["review_text"];
            $r_url = $formdata["redirect_url"];
            $toUrl = url('/' . $r_url);
            //dd($formdata);
            $checkExist = TblSellerReviews::where('seller_id', $seller_id)->where('user_id', $user_id)->get();
            $createe = TblSellerReviews::create([
                "seller_id" => $seller_id,
                "user_id" => $user_id,
                "ratings" => $rateval,
                "comment" => $review_text,
                "approved" => '0',
            ]);
            // $seller_review = TblSellerReviews::all();
            // foreach($seller_review as $r){
            //     $ratings[]=$r->ratings;
            // }
            // $avg_rating=array_sum($ratings) / count($ratings);
            return redirect($toUrl);
        }
    }
    public function getLinks()
    {
        $pages = TblOtherpage::get();
        $staticPages = TblStaticpage::get();
        $categories = TblCategory::withDepth()->having('depth', '=', 0)->whereNull('deleted_at')->orderBy('list_order', 'asc')->get();
        $mainCategory = [];
        // foreach ($categories as $category) {
        //     $mainCategory[$category->slug] = []; // Initialize subcategories array
        //     $subCategories = TblCategory::orderBy('list_order', 'asc')
        //         ->descendantsAndSelf($category->id);
        //     foreach ($subCategories as $subcat) {
        //          // Check if subcategory slug is not  the same as main category slug
        //     if ($subcat->slug !== $category->slug) {
        //         // Add subcategory slug to the main category's subcategories array
        //         $mainCategory[$category->slug][] = $subcat->slug;
        //     }
        //     }
        // }
        foreach ($categories as $category) {
            $mainCategory[$category->slug] = [
                'title' => $category->title,
                'subcategories' => [] // Initialize subcategories array
            ];
            $subCategories = TblCategory::orderBy('list_order', 'asc')
                ->descendantsAndSelf($category->id);
            foreach ($subCategories as $subcat) {
                // Check if subcategory slug is not the same as main category slug
                if ($subcat->slug !== $category->slug) {
                    // Add subcategory slug and title to the main category's subcategories array
                    $mainCategory[$category->slug]['subcategories'][] = [
                        'slug' => $subcat->slug,
                        'title' => $subcat->title
                    ];
                }
            }
        }
        return view('sitemap', ['pages' => $pages, 'staticpages' => $staticPages, 'categories' => $mainCategory]);
    }
    public  function xmldata()
    {
        $rootxml = '<?xml version="1.0" encoding="UTF-8"?> 
    
        <sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">	
    
        <sitemap>
    
            <loc>' . URL::to('/sitemap_pages.xml') . '</loc>
    
            <lastmod>' . date('Y-m-d H:i:s') . '</lastmod>
    
        </sitemap>
    
        <sitemap>
    
            <loc>' . URL::to('/sitemap_staticpages.xml') . '</loc>
            
            <lastmod>' . date('Y-m-d H:i:s') . '</lastmod>
    
        </sitemap>
        <sitemap>
    
            <loc>' . URL::to('/sitemap_categories.xml') . '</loc>
    
            <lastmod>' . date('Y-m-d H:i:s') . '</lastmod>
    
        </sitemap>

        <sitemap>
    
            <loc>' . URL::to('/sitemap_posts.xml') . '</loc>

            <lastmod>' . date('Y-m-d H:i:s') . '</lastmod>

        </sitemap>
    
        ';
        $rootxml .= '</sitemapindex>';
        return response($rootxml)->withHeaders([
            'Content-Type' => 'text/xml'
        ]);
    }
    public function getxmlContent()
    {
        $categories = TblCategory::withDepth()->having('depth', '=', 0)->whereNull('deleted_at')->orderBy('list_order', 'asc')->get();
        foreach ($categories as $category) {
            $mainCategory[$category->slug] = [
                'title' => $category->title,
                'subcategories' => [] // Initialize subcategories array
            ];
            $subCategories = TblCategory::orderBy('list_order', 'asc')
                ->descendantsAndSelf($category->id);
            foreach ($subCategories as $subcat) {
                // Check if subcategory slug is not the same as main category slug
                if ($subcat->slug !== $category->slug) {
                    // Add subcategory slug and title to the main category's subcategories array
                    $mainCategory[$category->slug]['subcategories'][] = [
                        'slug' => $subcat->slug,
                        'title' => $subcat->title
                    ];
                }
            }
        }
        $xmlString = '<?xml version="1.0" encoding="UTF-8"?>
        
        <urlset
        
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
        
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($mainCategory as $mainSlug => $mainData) {
            $xmlString .= '<url>';
            if (!empty(Session::get('Searchedurl'))) {
                $catUrl = Session::get('Searchedurl') . "&c=" . urlencode($mainSlug);
            } else {
                $catUrl = URL::to('australia?loc=Australia&country=Australia&state=&city=&c=') . urlencode($mainSlug);
            }
            $xmlString .= '<loc>' . htmlspecialchars($catUrl) . '</loc>';
            $xmlString .= '</url>';
            if (!empty($mainData['subcategories'])) {
                foreach ($mainData['subcategories'] as $subData) {
                    $xmlString .= '<url>';
                    $slug = $subData['slug'];
                    if (!empty(Session::get('Searchedurl'))) {
                        $subcatUrl =  Session::get('Searchedurl') . "&c=" . urlencode($slug);
                    } else {
                        $subcatUrl = URL::to('australia?loc=Australia&country=Australia&state=&city=&c=') .  urlencode($slug);
                    }
                    $xmlString .= '<loc>' . htmlspecialchars($subcatUrl) . '</loc>';
                    $xmlString .= '</url>';
                }
            }
        }
        $xmlString .= '</urlset>';
        return response($xmlString)->withHeaders([
            'Content-Type' => 'text/xml'
        ]);
    }
    public function getpageContent()
    {
        $staticPages = TblStaticpage::get();
        $xmlString = '<?xml version="1.0" encoding="UTF-8"?>
        
        <urlset
        
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
        
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($staticPages as $pagedata) {
            $xmlString .=   '<url>';
            $xmlString .=  '<loc>' . (URL::to('/pages/' . $pagedata->slug)) . '</loc> ';
            $xmlString .=  '</url>';
        }
        $xmlString .= '</urlset>';
        return response($xmlString)->withHeaders([
            'Content-Type' => 'text/xml'
        ]);
    }
    public function getstaticpageContent()
    {
        $pages = TblOtherpage::get();
        $xmlString = '<?xml version="1.0" encoding="UTF-8"?>
        
        <urlset
        
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
        
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($pages as $pagedata) {
            $xmlString .=   '<url>';
            $xmlString .=  '<loc>' . (URL::to($pagedata->slug)) . '</loc> ';
            $xmlString .=  '</url>';
        }
        $xmlString .= '</urlset>';
        return response($xmlString)->withHeaders([
            'Content-Type' => 'text/xml'
        ]);
    }
    public function cron_delete_users()
    {
        try {
            $command = new Userdelete();
            $message = $command->handle();
            echo $message;
        } catch (\Exception $e) {
            echo "Error generating users:" . $e->getMessage();
        }
    }
    public function getposts()
    {
        $today = date("Y-m-d");
        $blockedUsers = User::blocked_users();
        // Get unexpired paid posts
        $unexpired_paid_post_ids = TblPayment::where('active', '1')
            ->whereNotIn('user_id', $blockedUsers)
            ->whereDate('end_date', '>=', $today)
            ->pluck('post_id')->toArray();

        // Get unexpired free posts
        $unexpired_free_post_ids = TblPostedAdPackageInfo::where('active', '1')
            ->whereNotIn('user_id', $blockedUsers)
            ->whereDate('end_date', '>=', $today)
            ->pluck('post_id')->toArray();
        $valid_post_ids = array_merge($unexpired_paid_post_ids, $unexpired_free_post_ids);
        $getpost = TblPost::whereNull('deleted_at')
            ->where('active', 1)
            ->where('sold_status', 0)
            ->whereIn('id', $valid_post_ids)
            ->orderBy('title', 'asc')
            ->get(['slug']);
        $xmlString = '<?xml version="1.0" encoding="UTF-8"?>
    <urlset
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
        xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

        foreach ($getpost as $postdata) {
            $xmlString .= '<url>';
            $xmlString .= '<loc>' . URL::to($postdata->slug) . '</loc>';
            $xmlString .= '</url>';
        }
        $xmlString .= '</urlset>';
        return response($xmlString)->withHeaders([
            'Content-Type' => 'text/xml'
        ]);
    }
}
