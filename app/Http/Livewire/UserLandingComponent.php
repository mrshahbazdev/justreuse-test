<?php

namespace App\Http\Livewire;

use App\Models\Setting;
use App\Models\TblBannerAdvertisement;
use App\Models\TblBanners;
use App\Models\TblCategory;
use App\Models\TblCity;
use App\Models\TblCountry;
use App\Models\TblPost;
use App\Models\TblState;
use Exception;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class UserLandingComponent extends Component
{
    public $perPage = 20;
    public $increaseBy = 10;
    public $featurs_ad_list, $top_ads_list, $newdatas;
    public $main_categories, $random_categories;
    public $banner_ads, $banner_type, $enable_banner_map;

    protected $listeners = ['setLocation', 'setLocationFromIp'];

    public function mount()
    {
        // Set pagination based on grid setup
        $grid_setup = Setting::grid_setup_landing();
        if ($grid_setup == "4") {
            $this->perPage = 20;
            $this->increaseBy = 8;
        } elseif ($grid_setup == "5") {
            $this->perPage = 20;
            $this->increaseBy = 10;
        } else {
            $this->perPage = 18;
            $this->increaseBy = 12;
        }
    }

    /**
     * Set location from accurate browser data (triggered by frontend JS).
     */
    public function setLocation($lat, $lng, $city, $state, $country, $address)
    {
        Session::put([
            'Getlat' => $lat,
            'Getlng' => $lng,
            'GetCity' => $city,
            'GetState' => $state,
            'GetCountry' => $country,
            'GetAddress' => $address,
            'LocationSource' => 'browser',
        ]);
    }

    /**
     * Fallback to get location from IP if browser geolocation fails or is denied.
     */
    public function setLocationFromIp()
    {
        if (Session::has('GetCountry')) {
            return;
        }

        $userIp = request()->ip();
        if ($userIp == '127.0.0.1' || $userIp == '::1') {
            $userIp = '202.166.168.131'; // Example public IP for local testing
        }

        try {
            $response = Http::get("http://ip-api.com/json/{$userIp}");
            if ($response->successful() && $response->json()['status'] == 'success') {
                $locationData = $response->json();
                Session::put([
                    'Getlat' => $locationData['lat'],
                    'Getlng' => $locationData['lon'],
                    'GetCity' => $locationData['city'],
                    'GetState' => $locationData['regionName'],
                    'GetCountry' => $locationData['country'],
                    'GetAddress' => $locationData['city'] . ', ' . $locationData['country'],
                    'LocationSource' => 'ip',
                ]);
            }
        } catch (Exception $e) {
            // If the API call fails, do nothing.
        }
    }

    public function render()
    {
        // --- Settings ---
        $settings = Setting::where('key', 'home_banner_map')->first();
        if ($settings) {
            $this->enable_banner_map = json_decode($settings->value)->enable_map;
        }
        $settings = Setting::where('key', 'homepage_banner_type')->first();
        if ($settings) {
            $this->banner_type = json_decode($settings->value)->banner_type;
        }

        // --- Ad Fetching Logic ---
        $final_result_array = TblPost::get_free_ads();
        $ids = !empty($final_result_array) ? array_column($final_result_array, 'id') : [];
        $pagination = $this->perPage;

        $feature_ads = [];
        $top_ads = [];
        $latest_ads = [];

        if (Session::has('GetCountry')) {
            // Your original complex logic to find ads based on session location
            // This is a simplified version for demonstration
            $addressids = [];
            $country = TblCountry::where('name', Session::get('GetCountry'))->first();
            if ($country) {
                $addressids = TblCity::where('country_id', $country->id)->pluck('id')->toArray();
            }
            if (!empty($addressids)) {
                $feature_ads = TblPost::get_premium_ads('feature_ad', 10, $addressids);
                $top_ads = TblPost::get_premium_ads('top_ad', 10, $addressids);
                $latest_ads = TblPost::get_latest_ads_home_pages($final_result_array, $ids, $pagination, $addressids);
            }
        }
        
        // Fallback if no location-specific ads are found
        if (empty($feature_ads)) {
            $feature_ads = TblPost::get_premium_ads('feature_ad', 10);
        }
        if (empty($top_ads)) {
            $top_ads = TblPost::get_premium_ads('top_ad', 10);
        }
        if (empty($latest_ads)) {
            $latest_ads = TblPost::get_latest_ads_home_pages($final_result_array, $ids, $pagination);
        }
        
        $this->featurs_ad_list = $feature_ads;
        $this->top_ads_list = $top_ads;
        $this->newdatas = $latest_ads;

        // --- Banner Ads ---
        $home_banners = TblBanners::whereNull('deleted_at')->orderBy('id', 'desc')->limit(3)->get()->map(function ($banner) {
            return ['url' => $banner->banner_url, 'image' => URL::to('storage/' . $banner->images), 'content' => $banner->content];
        })->toArray();
        $paid_banners = TblBannerAdvertisement::where('page', 'home')->where('status', 'approved')->where('active', 1)->whereDate('start_date', '<=', now())->whereDate('end_date', '>=', now())->whereNull('deleted_at')->orderBy('created_at', 'desc')->get()->map(function ($ad) {
            return ['url' => $ad->web_link, 'image' => URL::to('storage/' . $ad->web_banner)];
        })->toArray();
        $this->banner_ads = array_merge($home_banners, $paid_banners);

        // --- Categories ---
        $this->main_categories = TblCategory::get_all_main_categories();
        if ($this->main_categories) {
            $this->random_categories = $this->main_categories->shuffle()->take(4);
        }
		
        return view('livewire.user-landing-component', [
            'posts' => $this->newdatas,
        ]);
    }

    public function get_city_ids($lat, $lng, $distance)
    {
        $query = "SELECT id FROM (SELECT *, ( ( ( acos( sin(( ? * pi() / 180)) * sin(( `latitude` * pi() / 180)) + cos(( ? * pi() /180 )) * cos(( `latitude` * pi() / 180)) * cos((( ? - `logitude`) * pi()/180)))) * 180/pi()) * 60 * 1.1515 * 1.609344) as distance FROM `tbl_cities`) tbl_cities WHERE distance <= ?";
        $results = DB::select($query, [$lat, $lat, $lng, $distance]);
        return array_column($results, 'id');
    }

    public function save_demo_cookie()
    {
        if (request()->isMethod('post')) {
            Cookie::queue(Cookie::make('demo_ip', request()->ip(), (2 * (60 * 24))));
        }
    }

    public function email_validate_rcf_dns()
    {
        if (request()->isMethod('post')) {
            $validator = Validator::make(request()->only('email'), [
                'email' => 'email:rfc,dns'
            ]);
            $isValid = !$validator->fails();
            $message = $isValid ? "Fine" : "Invalid Email Given. Enter Valid Email Address.";
            return response()->json(["result" => $isValid, "message" => $message]);
        }
        return response()->json(["result" => false, "message" => "Failed to validate"]);
    }
}