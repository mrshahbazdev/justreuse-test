<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\TblBannerAdvertisement;
use Livewire\WithPagination;
use App\Models\TblPost;
use App\Models\TblPostMethod;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\URL;

class BannerAdvertisementsComponent extends Component
{

    use WithPagination;
    public $search, $data;
    public $bannerads_view_mode = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function check_method_is_active()
    {
        $resut = 0;
        $post_methods = TblPostMethod::get_active_post_methods();
        if (!empty($post_methods)) {
            $check_post_methods = $post_methods->pluck('name')->toArray();
            if (in_array("bannerads", $check_post_methods)) {
                $resut = 1;
            }
        }
        return $resut;
    }

    public function render()
    {
        $res = $this->check_method_is_active();
        if ($res == 1) {
            $BannerAds = TblBannerAdvertisement::where('tbl_banner_advertisements.active', '1')->whereNull('tbl_banner_advertisements.deleted_at')
                ->join('users', 'tbl_banner_advertisements.user_id', '=', 'users.id')
                ->select(['tbl_banner_advertisements.*', 'users.name as user_name', 'users.id as user_id', 'users.email as user_email'])
                ->where(function ($q) {
                    $q->where('users.name', 'like', '%' . $this->search . '%')->orWhere('users.email', 'like', '%' . $this->search . '%');
                })->orderBy('tbl_banner_advertisements.created_at', 'desc')->paginate(10);
            return view('livewire.admin.banner_advertisements.compo', compact('BannerAds'));
        } else {
            abort(404);
        }
    }

    // for back button redirect page
    public function back()
    {
        return redirect()->route('admin/banner-advertisements');
    }

    public function view($id)
    {
        $banner = TblBannerAdvertisement::where('tbl_banner_advertisements.id', $id)->join('users', 'tbl_banner_advertisements.user_id', '=', 'users.id')->select(['tbl_banner_advertisements.*', 'users.name as user_name', 'users.id as user_id', 'users.email as user_email'])->first();
        $this->data = $banner;
        $this->bannerads_view_mode = true;
    }

    public function approve_banner_ads()
    {
                //start check demo user
                $isDemoUser = User::isDemoUser();
                if($isDemoUser["result"]==true)
                {
                    $this->updateMode=false;
                    $data = array('message' => $isDemoUser["message"]);
                    return $data;
                    exit;
                }
                //end check demo user

        $id = request()->id;
        $update_date = request()->update_date;
        $node = TblBannerAdvertisement::find($id);
        if ($update_date == 1) {
            $days = $node->live_days - 1;
            $end_date = date('Y-m-d', strtotime("+" . $days . " days"));
            $node->update([
                'status' => "approved",
                'approved_lately' => 1,
                'approved_start_date' => date('Y-m-d'),
                'approved_end_date' => $end_date
            ]);
        } else {
            $node->update([
                'status' => "approved",
                'approved_start_date' => $node->start_date,
                'approved_end_date' => $node->end_date
            ]);
        }


        // sent notification start
        $settings = Setting::get_logos();
        $site_name = $settings['name'];

        $user_id = $node->user_id;
        $admin_user = User::role('SuperAdmin')->first();
        $slug = URL::to('/my-banner-ads');

        $get_user_info = User::where('id', $user_id)->first();

        $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";

        $message1 = array("notifydata" => array('to_id' => $user_id, 'from_id' => $admin_user->id, 'message' => " Approved your banner advertisement request", 'notify_from' => 'banner_ads_approve', 'notify_title' => "Approved Banner Advertisement Request In ".$site_name." !..", 'post_id' => "", 'slug' => $slug));

        TblPost::send_push_notification($fcmid, $message1);


        $mail_data = array("send_maildata" => array('to_id' => $user_id, 'message' => "Approved your banner advertisement request", 'subject' => "Approved Banner Advertisement Request In ".$site_name." !..", 'ad_url' => $slug));
        $mail_key = "banner_ad_approve";
        Setting::notification_mail($mail_data, $mail_key);

        // sent notification end

        $data = array('message' => "Approved successfully!");
        return  $data;
    }
}
