<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Setting;
use App\Models\TblPost;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Artisan;
use App\Models\TblPostMethod;
use App\Models\User;
use App\Models\TblCurrency;
class Settings extends Component
{

    use WithFileUploads;
    use WithPagination;

    public $setting_id, $purchase_code, $app_watermark, $list_page, $detail_page, $app_watermark_view, $site_title, $meta_title, $meta_desc, $meta_keywords, $email, $phone, $app_logo_view, $favicon_view, $app_logo, $admin_logo, $admin_logo_view, $favicon, $default_currency, $smtp_mail_username, $smtp_mail_password, $google_api_key, $mapbox_api_key,$grid_setup;
    public $free_ads, $free_ads_duration, $twilio_sid, $twilio_token, $twilio_from, $enable_sms;
    public $google_recaptcha_sitekey,$google_recaptcha_secret;
    public $banner_type;
    public $updateMode = false;
    public $freeAdMode = false;
    public $homeBannerMapMode = false;
    public $homepageBannerType = false;
    public $enable_map, $cover_max_distance_km;
    public $cnfopen = 0;
    public $level_1, $level_2, $level_3, $default_amount, $home_page, $max_image_limit;
    public $DistanceAdMode = false, $max_distance, $ImageSizeAdMode = false, $TwilioMode = false, $BannerAdvertisement = false,$Currency=false;
	public $userChatType = false;
    public $banner_type_value;


    public function render()
    {
        $this->update_twilio_settings();
        $post_methods = TblPostMethod::get_active_post_methods();
        $resut = 0;
        if (!empty($post_methods)) {
            $check_post_methods = $post_methods->pluck('name')->toArray();
            if (in_array("bannerads", $check_post_methods)) {
                $resut = 1;
            }
        }
        if ($resut == 1) {
            $setting = Setting::where('active', 1)->where('key', '!=', 'homepage_banner_type')->paginate(10);
        } else {
            $setting = Setting::where('active', 1)->where('key', '!=', 'homepage_banner_type')->where('key', '!=', 'banner_advertisement')->paginate(10);
        }

        return view('livewire.admin.settings.show', [
            'settings' => $setting,
        ]);
    }

    public function update_twilio_settings()
    {
        $folders_list = scandir(base_path() . '/extra/twilioplugins');
        $folders = [];
        $only_folders = [];
        foreach ($folders_list as $f) {
            if ($f == "." || $f == "..") {
                continue;
            }

            $types = [
                'key' => 'twilio_sms',
                'display_name' => ucfirst($f),
                'name' => $f,
                'description' => 'Twilio SMS Keys',
                'active' => '1'
            ];
            $folders[] = $types;
            $only_folders[] = $f;
        }
        //begin - inserting/removing list of payments detail
        foreach ($folders as $j) {
            $path = base_path() . '/extra/twilioplugins/' . $j['name'];
            if (file_exists($path)) {
                $keys_file_path = $path . "/keys.json";
                $get_keys_data = file_get_contents($keys_file_path);
                $isExist = Setting::where('key', $j['key']);
                if ($isExist->count() == 0) {
                    Setting::create([
                        'key' => 'twilio_sms',
                        'name' => $j['display_name'],
                        'description' => $j['description'],
                        'active' => $j['active'],
                        'value' => $get_keys_data,
                        'parent_id' => 0,
                        'lft' => 0,
                        'rgt' => 0,
                        'depth' => 0
                    ]);
                }
            } else {
                $isExist = Setting::where('key', $j['key'])->delete();
            }
        }
        //end - inserting/removing list of payments detail
    }

    // for back button redirect page
    public function back()
    {
        return redirect()->route('admin/settings');
    }

    protected $rules = [
        'free_ads' => 'required',
        'free_ads_duration' => 'required',
    ];

    public function changeBannerType($value)
    {
        $this->banner_type = $value;
    }

    public function changeEnableMap($enablemap)
    {
        $this->enable_map = $enablemap;
    }
    
    public function edit($id)
    {
        
        $this->updateMode = false;
        $this->freeAdMode = false;
        $this->homeBannerMapMode = false;
        $this->homepageBannerType = false;
        $this->DistanceAdMode = false;
        $this->ImageSizeAdMode = false;
        $this->TwilioMode = false;
        $this->BannerAdvertisement = false;
        $this->Currency=false;
        
        $this->userChatType = false;


        $settings = Setting::find($id);
        $value = json_decode($settings->value);
        

        if ($settings->key == 'app') {
            $this->setting_id = $id;
            $this->purchase_code = $value->purchase_code;
            $this->site_title = $value->site_title;
            $this->meta_title = $value->meta_title;
            $this->meta_desc = $value->meta_desc;
            $this->meta_keywords = $value->meta_keywords;
            $this->email = $value->email;
            $this->phone = $value->phone;
            $this->app_logo_view = $value->app_logo;
            $this->admin_logo_view = $value->admin_logo;
            $this->app_watermark_view = $value->app_watermark;
            $this->favicon_view = $value->favicon;
            $this->default_currency = $value->default_currency;
            $this->smtp_mail_username = $value->smtp_mail_username;
            $this->smtp_mail_password = $value->smtp_mail_password;
            $this->google_api_key = $value->google_api_key;
            $this->google_recaptcha_sitekey = $value->google_recaptcha_sitekey;
            $this->google_recaptcha_secret = $value->google_recaptcha_secret;
            $this->mapbox_api_key = $value->mapbox_api_key;
			$this->grid_setup = $value->grid_count;
            $this->updateMode = true;
        } else if ($settings->key == 'free ads') {
            $this->setting_id = $id;
            $this->free_ads = $value->free_ads;
            $this->free_ads_duration = $value->free_ads_duration;
            $this->freeAdMode = true;
        } else if ($settings->key == 'home_banner_map') {
            //get google apikey form app 
            $getapp_key = Setting::where('key','app')->first();
            $google_api_value = json_decode($getapp_key->value);
            // get banner type value
            $getbanner_type = Setting::where('key','homepage_banner_type')->first();
            $ban_type_value = json_decode($getbanner_type->value);
            $this->setting_id = $id;
            $this->enable_map = $value->enable_map;
            $this->cover_max_distance_km = $value->cover_max_distance_km;
            $this->banner_type = $ban_type_value->banner_type;
            $this->google_api_key = $google_api_value->google_api_key;
            $this->mapbox_api_key = $google_api_value->mapbox_api_key;
            $this->homeBannerMapMode = true;
        } else if ($settings->key == 'distance_range') {
            $this->setting_id = $id;
            $this->max_distance = $value->max_distance;
            $this->DistanceAdMode = true;
        } else if ($settings->key == 'image_size_settings') {
            $this->setting_id = $id;
            $this->list_page = $value->list_page;
            $this->detail_page = $value->detail_page;
            $this->max_image_limit = $value->max_image_limit;
            $this->ImageSizeAdMode = true;
        } else if ($settings->key == 'twilio_sms') {
            $this->setting_id = $id;
            $this->twilio_sid = $value->twilio_sid;
            $this->twilio_token = $value->twilio_token;
            $this->twilio_from = $value->twilio_from;
            $this->enable_sms = $value->enable_sms;
            $this->TwilioMode = true;
        } else if ($settings->key == 'banner_advertisement') {
            $this->setting_id = $id;
            $this->level_1 = $value->level_1;
            $this->level_2 = $value->level_2;
            $this->level_3 = $value->level_3;
            $this->default_amount = $value->default_amount;
            $this->home_page = $value->home_page;
            $this->BannerAdvertisement = true;
        } else if ($settings->key == 'homepage_banner_type') {
            $this->setting_id = $id;
            $this->banner_type = $value->banner_type;
            $this->homepageBannerType = true;
        }else if ($settings->key == 'currency_conversion'){
            $this->setting_id = $id;
            $this->Currency=true; 
        }
    }

    public function update()
    {
		//start check demo user
			$isDemoUser = User::isDemoUser();
		    if($isDemoUser["result"]==true)
            {
                session()->flash('message', $isDemoUser["message"]);
				return redirect()->route('admin/settings');
				$this->updateMode = false;
				$this->freeAdMode = false;
				$this->homeBannerMapMode = false;
				$this->DistanceAdMode = false;
				$this->ImageSizeAdMode = false;
				$this->TwilioMode = false;
				exit;
            }
		//end check demo user
		
        $settings = Setting::find($this->setting_id);
        
        // application setup
        if ($settings->key == 'app') {
            $this->validate([
                'site_title' => 'required',
                'meta_title' => 'required',
                'meta_desc' => 'required',
                'meta_keywords' => 'required',
                'email' => 'required',
                'smtp_mail_username' => 'required',
                'smtp_mail_password' => 'required',
                'google_api_key' => 'required',
                'google_recaptcha_sitekey' => 'required',
                'google_recaptcha_secret' => 'required',
                'phone' => 'required',
            ]);
            // insert watermark
            if (!empty($this->app_watermark)) {
                $fileWaterMark = $this->app_watermark->store("setting_images", "public");
            } else {
                $fileWaterMark = $this->app_watermark_view;
            }
            //insert favicon
            if (!empty($this->favicon)) {
                $fileicon = $this->favicon->store("setting_images", "public");
            } else {
                $fileicon =  $this->favicon_view;
            }
            //insert logo
            if (!empty($this->app_logo)) {
                $filelogo = $this->app_logo->store("setting_images", "public");

            } else {
                $filelogo = $this->app_logo_view;
            }
            // admin_logo
            //insert admin logo
            
            if (!empty($this->admin_logo)) {
                $fileAdminlogo = $this->admin_logo->store("setting_images", "public");
            }else {
                $fileAdminlogo = $this->admin_logo_view;
            }

            $settings->update([
                'value' => [
                    'purchase_code' => $this->purchase_code,
                    'app_watermark' => $fileWaterMark,
                    'site_title' => $this->site_title,
                    'meta_title' => $this->meta_title,
                    'meta_desc' => $this->meta_desc,
                    'meta_keywords' => $this->meta_keywords,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'default_currency' => $this->default_currency,
                    'smtp_mail_username' => $this->smtp_mail_username,
                    'smtp_mail_password' => $this->smtp_mail_password,
                    'google_api_key' => $this->google_api_key,
                    'google_recaptcha_sitekey' => $this->google_recaptcha_sitekey,
                    'google_recaptcha_secret' => $this->google_recaptcha_secret,
                    'mapbox_api_key' => $this->mapbox_api_key,
                    'app_logo' => $filelogo,
                    'admin_logo' => $fileAdminlogo,
                    'favicon' => $fileicon,
					'grid_count' =>$this->grid_setup,
                ],
            ]);

            $path = base_path('.env');
            $name = "MAIL_USERNAME";
            $value = $this->smtp_mail_username;
            $password_name = "MAIL_PASSWORD";
            $password_value = $this->smtp_mail_password;
            $google_api_key_name = "GOOGLE_API_KEY";
            $google_api_key_value = $this->google_api_key;
            $google_recaptcha_sitekey_name = "GOOGLE_RECAPTCHA_SITEKEY";
            $google_recaptcha_sitekey_value = $this->google_recaptcha_sitekey;
            $google_recaptcha_secret_name = "GOOGLE_RECAPTCHA_SECRETKEY";
            $google_recaptcha_secret_value = $this->google_recaptcha_secret;

            if (file_exists($path)) {
                // Get all the lines from that file
                $lines = explode("\n", file_get_contents($path));
                $settings = collect($lines)
                    ->filter() // remove empty lines
                    ->transform(function ($item) {
                        return explode("=", $item, 2);
                    }) // separate key and values
                    ->pluck(1, 0); // keys to keys, values to values
                $settings[$name] = $value; // set the new value whether it exists or not
                $rebuilt = $settings->map(function ($value, $name) {
                    return "$name=$value";
                })->implode("\n"); // rebuild the env file
                file_put_contents($path, $rebuilt); // put the new contents
                // for password 
                $lines1 = explode("\n", file_get_contents($path));
                $settings1 = collect($lines1)
                    ->filter() // remove empty lines
                    ->transform(function ($item1) {
                        return explode("=", $item1, 2);
                    }) // separate key and values
                    ->pluck(1, 0); // keys to keys, values to values
                $settings1[$password_name] = $password_value; // set the new value whether it exists or not
                $rebuilt1 = $settings1->map(function ($password_value, $password_name) {
                    return "$password_name=$password_value";
                })->implode("\n"); // rebuild the env file
                file_put_contents($path, $rebuilt1); // put the new contents 
                
                // for google api key update start
                $lines2 = explode("\n", file_get_contents($path));
                $settings2 = collect($lines2)
                    ->filter() // remove empty lines
                    ->transform(function ($item2) {
                        return explode("=", $item2, 2);
                    }) // separate key and values
                    ->pluck(1, 0); // keys to keys, values to values
                $settings2[$google_api_key_name] = $google_api_key_value; // set the new value whether it exists or not
                $rebuilt2 = $settings2->map(function ($google_api_key_value, $google_api_key_name) {
                    return "$google_api_key_name=$google_api_key_value";
                })->implode("\n"); // rebuild the env file
                file_put_contents($path, $rebuilt2); // put the new contents 
                // for google api key update end


                // for google recaptcha sitekey update start
                $lines2 = explode("\n", file_get_contents($path));
                $settings2 = collect($lines2)
                    ->filter() // remove empty lines
                    ->transform(function ($item2) {
                        return explode("=", $item2, 2);
                    }) // separate key and values
                    ->pluck(1, 0); // keys to keys, values to values
                $settings2[$google_recaptcha_sitekey_name] = $google_recaptcha_sitekey_value; // set the new value whether it exists or not
                $rebuilt2 = $settings2->map(function ($google_recaptcha_sitekey_value, $google_recaptcha_sitekey_name) {
                    return "$google_recaptcha_sitekey_name=$google_recaptcha_sitekey_value";
                })->implode("\n"); // rebuild the env file
                file_put_contents($path, $rebuilt2); // put the new contents 
                // for google   recaptcha sitekey update end

                  // for google recaptcha secret update start
                  $lines2 = explode("\n", file_get_contents($path));
                  $settings2 = collect($lines2)
                      ->filter() // remove empty lines
                      ->transform(function ($item2) {
                          return explode("=", $item2, 2);
                      }) // separate key and values
                      ->pluck(1, 0); // keys to keys, values to values
                  $settings2[$google_recaptcha_secret_name] = $google_recaptcha_secret_value; // set the new value whether it exists or not
                  $rebuilt2 = $settings2->map(function ($google_recaptcha_secret_value, $google_recaptcha_secret_name) {
                      return "$google_recaptcha_secret_name=$google_recaptcha_secret_value";
                  })->implode("\n"); // rebuild the env file
                  file_put_contents($path, $rebuilt2); // put the new contents 
                  // for google   recaptcha secret update end
                Artisan::call('config:clear');
            }
            // end env
        } else if ($settings->key == 'free ads') { // free ads limit settings
            $this->validate([
                'free_ads' => 'required',
                'free_ads_duration' => 'required',
            ]);
            $settings->update([
                'value' => [
                    'free_ads' => $this->free_ads,
                    'free_ads_duration' => $this->free_ads_duration,
                ],
            ]);
        } else if ($settings->key == 'home_banner_map') { // home page map banner enable settings
            
            $val = ($this->enable_map == true) ? "1" : "0";
            $dist = $this->cover_max_distance_km;
            $settings->update([
                'value' => [
                    'enable_map' => $val,
                    'cover_max_distance_km' => $dist
                ]
            ]);

            $banner_record = Setting::where('key','homepage_banner_type')->first();
            
            // update map type 
            $banner_record->update([
                'value' => [
                    'banner_type' => $this->banner_type
                ]
            ]);

            // update google api key
            $google_api_record = Setting::where('key','app')->first();
            $app_key_value = json_decode($google_api_record->value);

            $purchase_code = $app_key_value->purchase_code;
            $site_title = $app_key_value->site_title;
            $meta_title = $app_key_value->meta_title;
            $meta_desc = $app_key_value->meta_desc;
            $meta_keywords = $app_key_value->meta_keywords;
            $email = $app_key_value->email;
            $phone = $app_key_value->phone;
            $app_logo_view = $app_key_value->app_logo;
            $admin_logo_view = $app_key_value->admin_logo;
            $app_watermark_view = $app_key_value->app_watermark;
            $favicon_view = $app_key_value->favicon;
            $default_currency = $app_key_value->default_currency;
            $smtp_mail_username = $app_key_value->smtp_mail_username;
            $smtp_mail_password = $app_key_value->smtp_mail_password;
			$grid_setup = $app_key_value->grid_count;

            $google_api_record->update([
                'value' => [
                    'purchase_code' => $purchase_code,
                    'app_watermark' => $app_watermark_view,
                    'site_title' => $site_title,
                    'meta_title' => $meta_title,
                    'meta_desc' => $meta_desc,
                    'meta_keywords' => $meta_keywords,
                    'email' => $email,
                    'phone' => $phone,
                    'default_currency' => $default_currency,
                    'smtp_mail_username' => $smtp_mail_username,
                    'smtp_mail_password' => $smtp_mail_password,
                    'google_api_key' => $this->google_api_key,
                    'google_recaptcha_sitekey' => $this->google_recaptcha_sitekey,
                    'google_recaptcha_secret' => $this->google_recaptcha_secret,
                    'mapbox_api_key' => $this->mapbox_api_key,
                    'app_logo' => $app_logo_view,
                    'admin_logo' => $admin_logo_view,
                    'favicon' => $favicon_view,
					'grid_count' =>$grid_setup,
                ],
            ]);

            $path = base_path('.env');
            $google_api_key_name = "GOOGLE_API_KEY";
            $google_api_key_value = $this->google_api_key;
            $google_recaptcha_sitekey_name = "GOOGLE_RECAPTCHA_SITEKEY";
            $google_recaptcha_sitekey_value = $this->google_recaptcha_sitekey;
            $google_recaptcha_secret_name = "GOOGLE_RECAPTCHA_SECRETKEY";
            $google_recaptcha_secret_value = $this->google_recaptcha_secret;
            if (file_exists($path)) {
                // for google api key update start
                $lines2 = explode("\n", file_get_contents($path));
                $settings2 = collect($lines2)
                    ->filter() // remove empty lines
                    ->transform(function ($item2) {
                        return explode("=", $item2, 2);
                    }) // separate key and values
                    ->pluck(1, 0); // keys to keys, values to values
                $settings2[$google_api_key_name] = $google_api_key_value; // set the new value whether it exists or not
                $rebuilt2 = $settings2->map(function ($google_api_key_value, $google_api_key_name) {
                    return "$google_api_key_name=$google_api_key_value";
                })->implode("\n"); // rebuild the env file
                file_put_contents($path, $rebuilt2); // put the new contents 
                // for google api key update end

                  // for google recaptcha sitekey update start
                  $lines2 = explode("\n", file_get_contents($path));
                  $settings2 = collect($lines2)
                      ->filter() // remove empty lines
                      ->transform(function ($item2) {
                          return explode("=", $item2, 2);
                      }) // separate key and values
                      ->pluck(1, 0); // keys to keys, values to values
                  $settings2[$google_recaptcha_sitekey_name] = $google_recaptcha_sitekey_value; // set the new value whether it exists or not
                  $rebuilt2 = $settings2->map(function ($google_recaptcha_sitekey_value, $google_recaptcha_sitekey_name) {
                      return "$google_recaptcha_sitekey_name=$google_recaptcha_sitekey_value";
                  })->implode("\n"); // rebuild the env file
                  file_put_contents($path, $rebuilt2); // put the new contents 
                  // for google   recaptcha sitekey update end
  
                    // for google recaptcha secret update start
                    $lines2 = explode("\n", file_get_contents($path));
                    $settings2 = collect($lines2)
                        ->filter() // remove empty lines
                        ->transform(function ($item2) {
                            return explode("=", $item2, 2);
                        }) // separate key and values
                        ->pluck(1, 0); // keys to keys, values to values
                    $settings2[$google_recaptcha_secret_name] = $google_recaptcha_secret_value; // set the new value whether it exists or not
                    $rebuilt2 = $settings2->map(function ($google_recaptcha_secret_value, $google_recaptcha_secret_name) {
                        return "$google_recaptcha_secret_name=$google_recaptcha_secret_value";
                    })->implode("\n"); // rebuild the env file
                    file_put_contents($path, $rebuilt2); // put the new contents 
                    // for google   recaptcha secret update end
                Artisan::call('config:clear');
            }
            // end env
        } else if ($settings->key == 'banner_advertisement') { //banner advertisement price settings
            $this->validate([
                'default_amount' => 'required',
            ]);
            $settings->update([
                'value' => [
                    'default_amount' => $this->default_amount,
                    'home_page' => $this->home_page,
                    'level_1' => !empty($this->level_1) ? $this->level_1 : $this->default_amount,
                    'level_2' => !empty($this->level_2) ? $this->level_2 : $this->default_amount,
                    'level_3' => !empty($this->level_3) ? $this->level_3 : $this->default_amount,
                ],
            ]);
        } else if ($settings->key == 'distance_range') { // default ditance range settings
            $this->validate([
                'max_distance' => 'required',
            ]);
            $settings->update([
                'value' => [
                    'max_distance' => $this->max_distance,
                ],
            ]);
        } else if ($settings->key == 'image_size_settings') { // image size settings for web and app
            $this->validate([
                'list_page' => 'required',
                'detail_page' => 'required',
                'max_image_limit' => 'required'
            ]);
            $settings->update([
                'value' => [
                    'list_page' => $this->list_page,
                    'detail_page' => $this->detail_page,
                    'max_image_limit' => $this->max_image_limit
                ],
            ]);
        } else if ($settings->key == 'twilio_sms') { // twillo sms setting
            $this->validate([
                'twilio_sid' => 'required',
                'twilio_token' => 'required',
                'twilio_from' => 'required',
            ]);
            $settings->update([
                'value' => [
                    'twilio_sid' => $this->twilio_sid,
                    'twilio_token' => $this->twilio_token,
                    'twilio_from' => $this->twilio_from,
                    'enable_sms' => $this->enable_sms,
                ],
            ]);
            $path = base_path('.env');
            $name_sid = "TWILIO_SID";
            $value_sid = $this->twilio_sid;
            $name_token = "TWILIO_TOKEN";
            $value_token = $this->twilio_token;
            $name_from = "TWILIO_FROM";
            $value_from = $this->twilio_from;
            if (file_exists($path)) {
                // for sid update start
                $lines = explode("\n", file_get_contents($path));
                $settings = collect($lines)
                    ->filter() // remove empty lines
                    ->transform(function ($item) {
                        return explode("=", $item, 2);
                    }) // separate key and values
                    ->pluck(1, 0); // keys to keys, values to values
                $settings[$name_sid] = $value_sid; // set the new value whether it exists or not
                $rebuilt = $settings->map(function ($value_sid, $name_sid) {
                    return "$name_sid=$value_sid";
                })->implode("\n"); // rebuild the env file
                file_put_contents($path, $rebuilt); // put the new contents
                // for sid update end
                // for token update start
                $lines1 = explode("\n", file_get_contents($path));
                $settings1 = collect($lines1)
                    ->filter() // remove empty lines
                    ->transform(function ($item1) {
                        return explode("=", $item1, 2);
                    }) // separate key and values
                    ->pluck(1, 0); // keys to keys, values to values
                $settings1[$name_token] = $value_token; // set the new value whether it exists or not
                $rebuilt1 = $settings1->map(function ($value_token, $name_token) {
                    return "$name_token=$value_token";
                })->implode("\n"); // rebuild the env file
                file_put_contents($path, $rebuilt1); // put the new contents
                // for token update end
                // for from update start
                $lines2 = explode("\n", file_get_contents($path));
                $settings2 = collect($lines2)
                    ->filter() // remove empty lines
                    ->transform(function ($item2) {
                        return explode("=", $item2, 2);
                    }) // separate key and values
                    ->pluck(1, 0); // keys to keys, values to values
                $settings2[$name_from] = $value_from; // set the new value whether it exists or not
                $rebuilt2 = $settings2->map(function ($value_from, $name_from) {
                    return "$name_from=$value_from";
                })->implode("\n"); // rebuild the env file
                file_put_contents($path, $rebuilt2); // put the new contents
                // for from update end
            }
            Artisan::call('config:clear');
        } else if ($settings->key == 'homepage_banner_type') { // default ditance range settings
            $this->validate([
                'banner_type' => 'required',
            ]);
            $settings->update([
                'value' => [
                    'banner_type' => $this->banner_type
                ],
            ]);
        }

        

        session()->flash('message', 'Updated Successfully.');
        return redirect()->route('admin/settings');
        $this->updateMode = false;
        $this->freeAdMode = false;
        $this->homeBannerMapMode = false;
        $this->DistanceAdMode = false;
        $this->ImageSizeAdMode = false;
        $this->TwilioMode = false;
    }

    public function convert_currency(){

        $settings = Setting::get_logos();
        $default_currency = $settings['default_currency'];
        $settings = Setting::find($this->setting_id);
        

        $default_currency_code = TblCurrency::where('id', $default_currency)->value('short_code');
     
        $req_url = 'https://v6.exchangerate-api.com/v6/3b135c35e73f91d7427b14c4/latest/'.$default_currency_code;
        $response_json = file_get_contents($req_url);
        
        // Continuing if we got a result
        if(false !== $response_json) {
        
            // Try/catch for json_decode operation
            try {
        
                // Decoding
                $response = json_decode($response_json);
        
                $conversion_rates_json = json_encode($response->conversion_rates);
        
        // Save the JSON string in the database
        $settings->update(['value' => $conversion_rates_json]);
        
            }
            catch(Exception $e) {
                // Handle JSON parse error...
            }
        
        }

        session()->flash('message', 'Updated Successfully.');
    }
}
