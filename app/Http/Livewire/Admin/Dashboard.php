<?php

namespace App\Http\Livewire\Admin;

use App\Models\TblPayment;
use App\Models\TblPost;
use App\Models\TblPostMethod;
use App\Models\TblChat;
use App\Models\TblPostedAdPackageInfo;
use Livewire\Component;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Package;
use App\Models\TblBulkPackPayment;
use App\Models\TblExchangedPost;
use App\Models\TblBuynowOrder;

class Dashboard extends Component
{
    public $web_users, $total_users, $total_posts, $paid_ads, $free_ads, $weeksale, $exchanged_post, $buynow_post, $given_away_post;

    public function render()
    {

        $this->paid_ads = $this->get_paid_ads();
        $this->free_ads = $this->get_free_ads();
        $this->total_posts = $this->get_free_ads() + $this->get_paid_ads();

        $this->total_users = User::count();
        $this->web_users = User::role('user')->get()->count();

        $admin = User::role('admin')->get()->count();
        $super_admin = User::role('superadmin')->get()->count();
        $this->admin_users = $admin + $super_admin;

        $this->exchanged_post = TblPost::where('exchange_to_buy', 1)->whereNull('deleted_at')->get()->count();
        $this->buynow_post = TblPost::where('instant_buy', 1)->whereNull('deleted_at')->get()->count();
        $this->given_away_post = TblPost::where('giving_away', 1)->whereNull('deleted_at')->get()->count();

        /* weekly sales report */
        $bulkpackageIDs = Package::WhereNull('deleted_at')->where('bulk_ads', 1)->pluck('id')->toArray();
        $weekdata = array();
        $counts_set1 = array();
        $counts_set2 = array();
        $counts_set3 = array();

        $monday = strtotime("last sunday");
        $monday = date('w', $monday) == date('w') ? $monday + 7 * 86400 : $monday;

        /* SET 1 DATA */
        $sunday = strtotime(date("Y-m-d", $monday) . " +6 days");
        $set1_sd = date("Y-m-d", $monday);
        $set1_ed = date("Y-m-d", $sunday);
        $set1datas = $this->displayDates($set1_sd, $set1_ed);
        foreach ($set1datas as $set1data) {
            $bulkpaymentInfos = TblBulkPackPayment::WhereNull('deleted_at')->where('active', 1)->whereDate('created_at', date('Y-m-d', strtotime($set1data)))->WhereIn('package_id', $bulkpackageIDs)->get();
            $dataset1_dates[] = date('d M', strtotime($set1data));
            $counts_set1[] = count($bulkpaymentInfos);
        }
        $weekdata['dates_set1'] = "'" . implode("', '", $dataset1_dates) . "'";
        $weekdata['counts_set1'] = implode(',', $counts_set1);

        /* SET 2 DATA */
        $sunday = strtotime(date("Y-m-d", $monday) . " -6 days");
        $set2_sd = date("Y-m-d", $monday);
        $set2_ed = date("Y-m-d", $sunday);
        $set2datas = $this->displayDates($set2_ed, $set2_sd);

        foreach ($set2datas as $set2data) {
            $bulkpaymentInfos = TblBulkPackPayment::WhereNull('deleted_at')->where('active', 1)->whereDate('created_at', date('Y-m-d', strtotime($set2data)))->WhereIn('package_id', $bulkpackageIDs)->get();
            $dataset2_dates[] = date('d M', strtotime($set2data));
            $counts_set2[] = count($bulkpaymentInfos);
        }
        $weekdata['dates_set2'] = "'" . implode("', '", $dataset2_dates) . "'";
        $weekdata['counts_set2'] = implode(',', $counts_set2);

        /* SET 3 DATA */
        $sunday = strtotime(date("Y-m-d", $monday) . " -12 days");
        $set3_sd = date("Y-m-d", $monday);
        $set3_ed = date("Y-m-d", $sunday);
        $set3datas = $this->displayDates($set3_ed, $set3_sd);

        foreach ($set3datas as $set3data) {
            $bulkpaymentInfos = TblBulkPackPayment::WhereNull('deleted_at')->where('active', 1)->whereDate('created_at', date('Y-m-d', strtotime($set3data)))->WhereIn('package_id', $bulkpackageIDs)->get();
            $dataset3_dates[] = date('d M', strtotime($set3data));
            $counts_set3[] = count($bulkpaymentInfos);
        }
        $weekdata['dates_set3'] = "'" . implode("', '", $dataset3_dates) . "'";
        $weekdata['counts_set3'] = implode(',', $counts_set3);
        $this->weekdatas = $weekdata;

        /* Total sales report start */

        /* all packages info */

        $allpackIDs = Package::WhereNull('deleted_at')->get();
        $alldata = array();
        $allDataPackName = array();
        $allDataPackCount = array();
        if(!empty($allpackIDs)){
        foreach ($allpackIDs as $allpackID) {
            $packpaymentInfos = TblBulkPackPayment::WhereNull('deleted_at')->where('active', 1)->where('package_id', $allpackID['id'])->get();
            $allDataPackName[] = $allpackID['name'];
            $allDataPackCount[] = count($packpaymentInfos);
        }
       
        $alldata['pack_names'] = "'" . implode("', '", $allDataPackName) . "'";
        $alldata['pack_count'] = implode(',', $allDataPackCount);
        $this->allPackInfos = $alldata;

        }
       
        return view('livewire.admin.dashboard.show');
    }

    public function get_whole_sale_data()
    {
        /* Total sales report start */
        /* weekly sales report */
        $monday = strtotime("last sunday");
        $monday = date('w', $monday) == date('w') ? $monday + 7 * 86400 : $monday;
        if (!empty(request()->weeks) && (request()->weeks == 7)) {
            $current_start_date = strtotime(date("Y-m-d", $monday) . " +6 days");
            $sunday = $current_start_date;
            $this_week_sd = date("Y-m-d", $monday);
            $this_week_ed = date("Y-m-d", $sunday);
            $alldates = $this->displayDates($this_week_sd, $this_week_ed);
        } else if (!empty(request()->weeks) && (request()->weeks == -7)) {
            $current_start_date = strtotime(date("Y-m-d", $monday) . " -6 days");
            $sunday = $current_start_date;
            $this_week_sd = date("Y-m-d", $monday);
            $this_week_ed = date("Y-m-d", $sunday);
            $alldates = $this->displayDates($this_week_ed, $this_week_sd);
        } else if (!empty(request()->weeks) && (request()->weeks == 14)) {
            $current_start_date = strtotime(date("Y-m-d", $monday) . " +12 days");
            $sunday = $current_start_date;
            $this_week_sd = date("Y-m-d", $monday);
            $this_week_ed = date("Y-m-d", $sunday);
            $alldates = $this->displayDates($this_week_sd, $this_week_ed);
        } else {
            $current_start_date = strtotime(date("Y-m-d", $monday) . " +6 days");
            $sunday = $current_start_date;
            $this_week_sd = date("Y-m-d", $monday);
            $this_week_ed = date("Y-m-d", $sunday);
            $alldates = $this->displayDates($this_week_sd, $this_week_ed);
        }
        $weekdata = array();
        $bulkpackageIDs = Package::WhereNull('deleted_at')->where('bulk_type', 1)->pluck('id')->toArray();
        foreach ($alldates as $alldate) {
            $bulkpaymentInfos = TblBulkPackPayment::WhereNull('deleted_at')->where('active', 1)->where('created_at', $alldate)->WhereIn('package_id', $bulkpackageIDs)->get();
            $weekdata[] = array(
                'date' => date('d-m-y', strtotime($alldate)),
                'count' => count($bulkpaymentInfos),
            );
        }

        return json_encode($weekdata);
    }



    public function displayDates($date1, $date2, $format = 'd-m-Y')
    {
        $dates = array();
        $current = strtotime($date1);
        $date2 = strtotime($date2);
        $stepVal = '+1 day';
        while ($current <= $date2) {
            $dates[] = date($format, $current);
            $current = strtotime($stepVal, $current);
        }
        return $dates;
    }

    public function get_paid_ads()
    {

        //begin-fetching records from "current date" between advertisement start_date & end_dates
        $curr_date = date('Y-m-d H:i:s');
        $blockedUsers = User::blocked_users();
        $dataList = TblPayment::join('tbl_posts', 'tbl_posts.id', '=', 'tbl_payments.post_id')
            ->whereNull('tbl_posts.deleted_at')
            ->where('tbl_payments.start_date', '<=', $curr_date)
            ->where('tbl_payments.end_date', '>=', $curr_date)
            ->where('tbl_payments.active', '1')
            ->whereNotIn('tbl_payments.user_id', $blockedUsers)
            ->orderBy('tbl_payments.created_at', 'desc')
            ->get(['tbl_posts.*'])->count();
        return $dataList;
        //end-fetching records from "current date" between advertisement start_date & end_dates

    }



    public function get_free_ads()
    {
        $curr_date = date('Y-m-d H:i:s');

        $post_ids = TblPostedAdPackageInfo::where('active', '1')
            ->where('start_date', '<=', $curr_date)
            ->where('end_date', '>=', $curr_date)
            ->get('post_id')->count();

        return $post_ids;
    }


    public function get_side_data(){

       
        $search = request()->search;
        $dashboard = route('dashboard');
        $post_list = route('admin/post');
        $category = route('admin/category');
        $repost_ads = route('admin/report-ad') ;
        $block_posts = route('admin/blocked-post');
        $user = route('admin/user');
        $role = route('admin/role');
        $permission = route('admin/permissions');
        $settings = route('admin/settings');
        $package = route('admin/package');
        $report_type = route('admin/report-type');
        $payment_methods = route('admin/payment-methods');
        $payment = route('admin/payment');
        $review =  route('admin/review');
        $static_pages = route('admin/staticpage');
        $coupon =  route('admin/coupon') ;
        $languages = route('admin/language');
        $country =  route('admin/country');
        $advertise = route('admin/advertising');
        $blocklist = route('admin/blacklist');
        $contact = route('admin-contact-us');
        $report_user  = route('admin/report-user');
        $home_banner = route('admin/home-banner');
        $banner_ad = route('admin/banner-advertisements');
        $currency = route('admin/currency');
        $email_template = route('admin/email-template');
        $buy_now =  route('admin/buynow-orders');
        $bulk_orders = route('admin/bulk-payments');
        $post_methods =route('admin/post-methods');
        $chat_methods =route('admin/chat-methods');
        $other_pages=route('admin/otherpages');
        $bulk_email = route('admin/bulk-email'); 
        $side_data = array(
            'Dashboard' => $dashboard,
            'Post List' => $post_list,
            'Category' => $category,
            'Report Ads'=>$repost_ads,
            'Blocked Posts'=>$block_posts,
            'User'=>$user,
            'Role'=> $role,
            'Permissions'=>$permission,
            'Settings'=>$settings,
            'Packages'=>$package,
            'Report Type'=>$report_type,
            'Payments Methods'=>$payment_methods,
            'Payments'=>$payment,
            'Reviews'=>$review,
            'Static Page'=>$static_pages,
            'Coupon'=>$coupon,
            'Languages'=>$languages,
            'Country'=>$country,
            'Advertisements'=>$advertise,
            'Blacklist'=>$blocklist,
            'Contact Us'=>$contact,
            'Report Users'=>$report_user,
            'Home Banner' =>$home_banner,
            'Banner Advertisements' =>$banner_ad,
            'Currency'=>$currency,
            'Email Template'=>$email_template,
            'Buy Now Orders'=>$buy_now,
            'Bulk Payments'=>$bulk_orders,
            'Post Methods'=>$post_methods,
            'Chat Methods'=>$chat_methods,
            'Other Pages'=>$other_pages,
            'Bulk Email'=>$bulk_email
        );
        $data = [];
        foreach ($side_data as $key => $value) {
            // Check if the search term is found in the key
            if (stripos($key, $search) !== false) {
                // Return the corresponding key and value
                $data[]= array('name' => $key,
                'url' => $value);
              
            }
        }

        
        return $data;
        

    }
}
