<?php

namespace Postplugins\Bannerads;

use Livewire\Component;
use App\Models\TblCategory;
use App\Models\TblBannerAdvertisement;
use App\Models\Setting;
use App\Models\TblPaymentsMethod;
use App\Models\TblPostMethod;
use Illuminate\Support\Facades\Auth;
use App\Models\TblCurrency;
use Livewire\WithPagination; //for pagination
use Livewire\WithFileUploads; //for file upload
use Image;
Use Storage;

class BannerAdController extends Component
{
    use WithFileUploads;

    public function check_method_is_active()
    {
        $resut = 0;
        $post_methods = TblPostMethod::get_active_post_methods();
        if (!empty($post_methods)) {
            $check_banner_ads = $post_methods->pluck('name')->toArray();
            if (in_array("bannerads", $check_banner_ads)) {
                $resut = 1;
            }
        }
        return $resut;
    }

    public function front_banner_ads()
    {
        $res = $this->check_method_is_active();
        if ($res == 1) {
            $payment_methods = TblPaymentsMethod::where('active', '1')->get()->toArray();
            $categorylist = TblCategory::orderBy('list_order', "asc")->with('ancestors')->get()->toTree();
            return view('bannerads.src.show', ['categorylist' => $categorylist, 'payment_methods' => $payment_methods]);
        } else {
            abort(404);
        }
    }

    public function get_banner_price()
    {
        $page = request()->page;
        $cat_id = !empty(request()->cat_id) ? request()->cat_id : "";
        if ($page == "home") {
            $get_price = TblBannerAdvertisement::get_banner_ads_price($page, NULL);
            return $get_price;
        } else {
            $get_price = TblBannerAdvertisement::get_banner_ads_price($page, $cat_id);
            return $get_price;
        }
    }

    public function my_banner_ads()
    {
        $res = $this->check_method_is_active();
        if ($res == 1) {
            $bannerads = TblBannerAdvertisement::where('active', 1)->where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(20);
            return view('bannerads.src.my_banner_ads', ["bannerads" => $bannerads]);
        } else {
            abort(404);
        }
    }

    public function save_banner_ads()
    {
        $return_value = "fail";
        $last_inset_id = "";
        $total_amount = "";
        $user_id = Auth::user()->id;
        $web_banner = "";
        $app_banner = "";
        $req_web_banner = request()->web_banner;
        $req_app_banner = request()->app_banner;
        $settings = Setting::get_logos();
        if (!empty($req_web_banner)) {
            $web_banner = $req_web_banner->hashName('web_banner_ads');
            $path_web_list = $req_web_banner->hashName('public/web_banner_ads');
            /* $web_list = Image::make($req_web_banner)->resize(null, 350, function ($constraint) {
                $constraint->aspectRatio();
            }); */
			$web_list = Image::make($req_web_banner);
            $web_list->insert(public_path('storage/' . $settings['watermark']), 'bottom-right', 10, 10);
            Storage::put($path_web_list, (string) $web_list->encode());
            //$web_banner = $req_web_banner->store('web_banner_ads', 'public');
        }
        if (!empty($req_app_banner)) {
            $app_banner = $req_app_banner->hashName('app_banner_ads');
            $path_web_list = $req_app_banner->hashName('public/app_banner_ads');
/*             $web_list = Image::make($req_app_banner)->resize(null, 350, function ($constraint) {
                $constraint->aspectRatio();
            }); */
            $web_list = Image::make($req_app_banner);
            $web_list->insert(public_path('storage/' . $settings['watermark']), 'bottom-right', 10, 10);
            Storage::put($path_web_list, (string) $web_list->encode());
            // $app_banner = $req_app_banner->store('app_banner_ads', 'public');
        }
        if (!empty($app_banner) && !empty($web_banner)) {
            $total_amount = request()->final_total_amount;
            $currency_id = TblCurrency::where('id', $settings['default_currency'])->pluck('default_currency_id')->first();

            $last_inset_id = TblBannerAdvertisement::create([
                "payment_id" => "1",
                "user_id" => $user_id,
                "web_banner" => $web_banner,
                "app_banner" => $app_banner,
                "web_link" => request()->web_link,
                "app_link" => request()->app_link,
                "start_date" => date('Y-m-d', strtotime(request()->start_date)),
                "end_date" => date('Y-m-d', strtotime(request()->end_date)),
                "payment_type" => "paypal",
                "live_days" => request()->live_days,
                "page" => request()->banner_display_page,
                "category_id" => !empty(request()->banner_category) ? request()->banner_category : "",
                "total_amount" => $total_amount,
                "payment_status" => "fail",
                'currency_id' => $currency_id,
                'active' => 0
            ])->id;
            $return_value = "success";
        }
        return json_encode(array("result" => $return_value, "last_id" => $last_inset_id, "total_amount" => $total_amount));
    }
}
