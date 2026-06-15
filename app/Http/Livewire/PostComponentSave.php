<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\TblCategory;
use App\Models\TblCurrency;
use App\Models\TblCity;
use App\Models\TblCountry;
use App\Models\TblCustomField;
use App\Models\TblFieldsDetail;
use App\Models\TblFieldsOption;
use App\Models\TblPost;
use App\Models\TblPostedAdPackageInfo;
use App\Models\TblPostValue;
use App\Models\TblState;
use App\Models\TblNotifications;
use App\Models\Setting;
use App\Models\TblFollowers;
use App\Models\Package;
use App\Models\User;
use Faker\Provider\Uuid;
use Illuminate\Support\Facades\Auth;
use Livewire\Request;
use Session;
use Livewire\WithPagination; //for pagination
use Livewire\WithFileUploads; //for file upload
use Illuminate\Support\Str; //for slug
use Illuminate\Support\Facades\URL;
use Image;
use Storage;
use App\Models\TblPayment;
use Intervention\Image\Exception\NotReadableException;
use Illuminate\Support\Facades\Response;

class PostComponentSave extends Component
{

    use WithFileUploads; //for file upload

    public $categorylist;
    public $filter;
    public $old_images = array();

    public function render()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $formdata = request()->all();
            $post_id = $formdata['post-id'];
            if ($post_id == 0) {
                $check_pack_info = Package::where('id', $formdata['package_type'])->first();
                if (!empty($check_pack_info)) {
                    if ($check_pack_info->short_name == "free") {
                        $check_with_cnt = $check_pack_info->single_pack_limit;
                        /* Count total no of post added by user */
                        $post_count = TblPostedAdPackageInfo::where('user_id', Auth::id())->sum('publish_count');
                        if (($post_count >= $check_with_cnt) && ($post_id == 0)) {
                            Session()->flash('message', 'Max free ads limit reached!');
                            Session()->flash('class', 'error');
                            redirect('post');
                        } else {
                            $formdata["choosen_package"] = "free";
                            $get_response = $this->postinfo_to_db($formdata);
                            if ($get_response == "success") {
                                Session::flash('message', 'Inserted Successfully new post');
                                return view('livewire.post.show', [
                                    'list' => TblPost::orderBy('id', 'desc')->paginate(10)
                                ]);
                            } else {
                                Session()->flash('message', 'Please try again later!');
                                Session()->flash('class', 'error');
                                redirect('post');
                            }
                        }
                    } else {
                        $formdata["choosen_package"] = "paid";
                        $get_response = $this->postinfo_to_db($formdata);
                        if ($get_response['type'] == "payment") {
                            $currency_symbol = Setting::get_admin_default_currency();
                            $currency_id = $currency_symbol['id'];
                            $inserted_id = $get_response['post_id'];
                            if ($formdata['payment_type'] == "paypal") {
                                redirect('/paypal-payment-process?pack_amt=' . $check_pack_info->price . '&cid=' . $currency_id . '&post_id=' . $inserted_id . '&live_days=' . $check_pack_info->duration . '&package_id=' . $check_pack_info->id . '&payment_type=paypal&coupon_id=');
                            } else if ($formdata['payment_type'] == "stripe") {
                                Session()->flash('message', 'Post added successfully!');
                                Session()->flash('class', 'success');
                                redirect('post');
                                // redirect('/stripe-payment?pack_amt=' . $check_pack_info->price .  '&cid=' . $currency_id . '&post_id=' . $inserted_id . '&live_days=' . $check_pack_info->duration . '&package_id=' . $check_pack_info->id . '&payment_type=stripe&coupon_id=&uid=' . Auth::user()->id . '&paid_for=package');
                            }
                        }
                    }
                } else {
                    Session()->flash('message', 'You cannot add the post without package!');
                    Session()->flash('class', 'error');
                    redirect('post');
                }
            } else {
                $get_response = $this->postinfo_to_db($formdata);
                if ($get_response == "success") {
                    Session::flash('message', 'Updated Successfully');
                    return redirect('/post');
                }else if($get_response == "invalid_image"){
                    Session::flash('message', 'Unsupported image type. Please upload a valid image. ');
                    return redirect('/post');
                }
            }
        }
        return view('livewire.post.show', [
            'list' => TblPost::orderBy('id', 'desc')->paginate(10)
        ]);
    }


    public function update_post_info()
    {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $formdata = request()->all();
            $post_id = $formdata['post-id'];

            if ($post_id == 0) {
              

                $check_pack_info = Package::where('id', $formdata['package_type'])->first();
                
                if (!empty($check_pack_info)) {
                    if ($check_pack_info->short_name == "free") {
                        $check_with_cnt = $check_pack_info->single_pack_limit;
                        /* Count total no of post added by user */
                        $post_count = TblPostedAdPackageInfo::where('user_id', Auth::id())->sum('publish_count');
                        if (($post_count >= $check_with_cnt) && ($post_id == 0)) {
                            Session()->flash('message', 'Max free ads limit reached!');
                            Session()->flash('class', 'error');
                            return redirect('post');
                        } else {
                            $formdata["choosen_package"] = "free";
                            $get_response = $this->postinfo_to_db($formdata);
                            if (is_array($get_response)) {
                                Session::flash('slug', $get_response['slug']);
                                return redirect('post');
                            }else{
                                if ($get_response == "success") {
                                    Session::flash('message', 'Inserted Successfully post 2');
                                    return redirect('post');
                                }else if($get_response == "invalid_image"){
                                    Session()->flash('message', 'Unsupported image type. Please upload a valid image.This image is Not Allowed ');
                                    Session()->flash('class', 'error');
                                    return redirect('/post');
                                } else {
                                    Session()->flash('message', 'Please try again later!');
                                    Session()->flash('class', 'error');
                                    return redirect('post');
                                }
                            }
                        }
                    } else {
                        $formdata["choosen_package"] = "paid";
                        $get_response = $this->postinfo_to_db($formdata);
                        if ($get_response['type'] == "payment") {
                            $currency_symbol = Setting::get_admin_default_currency();
                            $currency_id = $currency_symbol['id'];
                            $inserted_id = $get_response['post_id'];
                            if ($formdata['payment_type'] == "paypal") {
                                return redirect('/paypal-payment-process?pack_amt=' . $check_pack_info->price . '&cid=' . $currency_id . '&post_id=' . $inserted_id . '&live_days=' . $check_pack_info->duration . '&package_id=' . $check_pack_info->id . '&payment_type=paypal&coupon_id=');
                            } else if ($formdata['payment_type'] == "stripe") {
                                // Session()->flash('message', 'Post added successfully!');
                                // Session()->flash('class', 'success');
                                // return redirect('/post');
                                return redirect('/stripe-payment?pack_amt=' . $check_pack_info->price .  '&cid=' . $currency_id . '&post_id=' . $inserted_id . '&live_days=' . $check_pack_info->duration . '&package_id=' . $check_pack_info->id . '&payment_type=stripe&coupon_id=&uid=' . Auth::user()->id . '&paid_for=package');
                            }
                        }
                    }
                } else {
                    Session()->flash('message', 'You cannot add the post without package!');
                    Session()->flash('class', 'error');
                    return redirect('/post');
                }
            } else {
             // dd($formdata);
                $get_response = $this->postinfo_to_db($formdata);
                if ($get_response == "success") {
                    Session::flash('message', 'Updated Successfully');
                    return redirect('/post');
                }else if($get_response == "invalid_image"){
                    Session()->flash('message', 'Unsupported image type. Please upload a valid image.This image is Not Allowed ');
                    Session()->flash('class', 'error');
                    return redirect('/post');
                }
            }
        }
    }

    public function total_post()
    {
        return TblPost::count() + 1;
    }


    public function initial_load()
    {
    }

    public function republish_post()
    {
        $id = request()->id;
        $sold_status = request()->status;
        if (!empty($sold_status)) {
            $sold_post = TblPost::where('id', $id)->first();
            if ($sold_status == "mark_sold") {
                $sold = 1;
            } else {
                $sold = 0;
            }
            $sold_post->update([
                'sold_status' => $sold,
            ]);
        } else {
            // republish here

            $publish_count = 1;
            $node = TblPostedAdPackageInfo::where('post_id', $id)->first();
            $curr_date = date('Y-m-d');
            $check_pack_info = Package::where('lft', 1)->first();
            $package_duration = "+" . $check_pack_info->duration . " days";
            $end_date = date('Y-m-d', strtotime($curr_date . $package_duration));
            $post_count = TblPostedAdPackageInfo::where('user_id', Auth::id())->sum('publish_count');
            $publish_count = $publish_count + $node->publish_count;
            $node->update([
                'start_date' => $curr_date,
                'end_date' => $end_date,
                'publish_count' => $publish_count
            ]);

            // send notification
            $settings = Setting::get_logos();
            $site_name = $settings['name'];

            $user_id = auth()->id();
            $get_user_info = User::where('id', $user_id)->first();
            $get_post_info = TblPost::where('id', $id)->first();
            $slug = url('/post');
            $get_admin = User::role('superadmin')->get();
            $admin_id = $get_admin[0]->id;

            $fcmid = !empty($get_user_info->fcmid) ? $get_user_info->fcmid : "";
            $message = array("notifydata" => array('to_id' => $user_id, 'from_id' => $admin_id, 'message' => "Your post has been republished. post name - " . $get_post_info->title, 'notify_from' => 'republish_post', 'notify_title' => "Post Republished In " . $site_name . "!..", 'post_id' => $id, 'slug' => $slug));

            TblPost::send_push_notification($fcmid, $message);
        }
        return response()->json(['message' => 'updated successfully']);
    }


    public function postinfo_to_db($formdata)
    {
        //dd($formdata);
        $user_id = Auth::user()->id;
        $post_id = $formdata['post-id'];
        $token = $formdata['_token'];
        $title = $formdata['text-title-sst'];
        $video_url = $formdata['text-video-sst'];
        $conv_price = $formdata['number-price-sst'];
        $desc = $formdata['textarea-desc-sst'];
        $cat_id = $formdata['selected_category'];
        $city = $formdata['text-city-sst']; //dummy
        $currency_id_bf = $formdata['currency_id'];
        
        if(!empty($currency_id_bf)){
            $default_crr = Setting::get_admin_default_currency();
            $curr_id = $default_crr['id'];
            $curr_short_code = $default_crr['short_code'];
            $post_currency = TblCurrency::where('id',$currency_id_bf)->value('short_code');
            // Fetching JSON
                $req_url = 'https://v6.exchangerate-api.com/v6/3b135c35e73f91d7427b14c4/pair/'.$post_currency.'/'.$curr_short_code.'/'.$conv_price;

                $response_json = file_get_contents($req_url);

                // Continuing if we got a result
                if(false !== $response_json) {

                    // Try/catch for json_decode operation
                    try {

                        // Decoding
                        $response = json_decode($response_json);

                        // Check for success
                        if('success' === $response->result) {

                            $price = $response->conversion_result;
                            $currency_id = $default_crr['id'];
                        }

                    }
                    catch(Exception $e) {
                        // Handle JSON parse error...
                        $currency_id = $formdata['currency_id'];
                        $price = $formdata['number-price-sst'];
                    }

                }

        }else{

            $currency_id = $formdata['currency_id'];
            $price = $formdata['number-price-sst'];

        }



        $product_condidition = !empty($formdata['product_condition']) ? $formdata['product_condition'] : "";
        $exchangetobuy = !empty($formdata['exchangeToBuy']) ? $formdata['exchangeToBuy'] : 0;
        $FixedPrice = !empty($formdata['FixedPrice']) ? $formdata['FixedPrice'] : 0;
        $InstantBuy = !empty($formdata['InstantBuy']) ? $formdata['InstantBuy'] : 0;
        $paid_id = !empty($formdata['paid_id']) ? $formdata['paid_id'] : "";
         $manual_city_name = $formdata['text-city-sst'] ?? 'N/A';
        //$city_name = $manual_city_name;
        //$main_city_name = $manual_city_name;
        //$city_lat = '0.00';
        //$city_lag = '0.00';
        //$country_long = 'Pakistan'; // Aap koi bhi default value de sakte hain
        //$country_short = 'PK';      // Aap koi bhi default value de sakte hain
        //$state_long = 'Punjab';     // Aap koi bhi default value de sakte hain
        //$state_short = 'PB';
        $city_name = $formdata['city_name'];            // this is local area
        $main_city_name = $formdata['main_city_name'];  // this is city name like chennai, madurai
        $city_lat = $formdata['city_lat'];
        $city_lag = $formdata['city_lag'];
        $country_long = $formdata['country_long'];
        $country_short = $formdata['country_short'];
        $state_long = $formdata['state_long'];
        $state_short = $formdata['state_short'];
        $old_images = !empty($formdata['old_images']) ? $formdata['old_images'] : "";


        $slug = Str::slug($title, "-");
        $choosen_package = !empty($formdata['choosen_package']) ? $formdata['choosen_package'] : "";
        $shipping_rate = !empty($formdata['text-shipping-fee']) ? $formdata['text-shipping-fee'] : "";
        //get country info
        $country_id = "";
        $state_id = "";
        $city_id = "";
        $tbl_country = TblCountry::where('code', $country_short)->where('name', $country_long)->get();
        if ($tbl_country->count() == 0) {
            $country_id = TblCountry::create([
                'code' => $country_short,
                'name' => $country_long
            ])->id;
        } else {
            $country_id = $tbl_country[0]->id;
        }
        //get state info
        $tbl_state = TblState::where('country_id', $country_id)->where('code', $state_short)->where('name', $state_long)->get();
        if ($tbl_state->count() == 0) {
            $state_id = TblState::create([
                'country_id' => $country_id,
                'code' => $state_short,
                'name' => $state_long
            ])->id;
        } else {
            $state_id = $tbl_state[0]->id;
        }
        //get city info
        $tbl_cities = TblCity::where('country_id', $country_id)->where('state_id', $state_id)->where('name', $main_city_name)->where('locality', $city_name)->get();
        if ($tbl_cities->count() == 0) {
            $city_id = TblCity::create([
                'country_id' => $country_id,
                'state_id' => $state_id,
                'locality' => $city_name,
                'name' => $main_city_name,
                'latitude' => $city_lat,
                'logitude' => $city_lag
            ])->id;
        } else {
            $city_id = $tbl_cities[0]->id;
        }

        // Post Image resize and watermark attachment start

        if ($post_id == 0) {
            $predefined_imgs = "";
            if (array_key_exists('images_indhu', $formdata)) {
                $images = $formdata['images_indhu'];
                $imagenamesArr = [];
            try{
                foreach ($images as $key => $j) {
                    $img_index = explode(',', $formdata['selected-img-index']);
                    if (in_array($key, $img_index)) {
                        /* Get watermark image */
                        $settings = Setting::get_logos();
                        /* Get image size settings */
                        $imagesizeSet = Setting::get_image_size_settings();

                        /* Calculate dimensions for list view */
                        $list_size = explode('*', $imagesizeSet['list']);
                        $list_width = $list_size[0];
                        $list_height = $list_size[1];

                        /* Calculate dimensions for detail view */
                        $detail_size = explode('*', $imagesizeSet['detail']);
                        $detail_width = $detail_size[0];
                        $detail_height = $detail_size[1];

                        /* Load the original image */
                        $originalImage = Image::make($j);
                        $save_img = $j->hashName('adpost/predefined');
                        // Apply watermark for original size
                        $path_web_original = $j->hashName('public/adpost/predefined/normal');
                        $originalImage->insert(public_path('storage/' . $settings['watermark']), 'bottom-right', 5, 5);
                        Storage::put($path_web_original, (string) $originalImage->encode());

                        // Resize and apply watermark for list view
                        $path_web_list = $j->hashName('public/adpost/predefined/list');
                        $resizedListImage = $originalImage->resize($list_width, $list_height, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $watermarkWidth = $resizedListImage->width() * (0.01);

                        // Adjust the scale of the watermark as needed
                        $watermarkHeight = $watermarkWidth * (125/126);
                        $watermark = Image::make(public_path('storage/' . $settings['watermark']))->resize($watermarkWidth, $watermarkHeight);
                        $resizedListImage->insert($watermark, 'bottom-right', 5, 5);
                        Storage::put($path_web_list, (string) $resizedListImage->encode());

                        // Resize and apply watermark for detail view
                        $path = $j->hashName('public/adpost/predefined');
                        $resizedDetailImage = $originalImage->resize($detail_width, $detail_height, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $watermarkWidth = $resizedDetailImage->width() * 0.1; // Adjust the scale of the watermark as needed
                        $watermarkHeight = $watermarkWidth * (125/126);
                        $watermark = Image::make(public_path('storage/' . $settings['watermark']))->resize($watermarkWidth, $watermarkHeight);
                        $resizedDetailImage->insert($watermark, 'bottom-right', 10, 10);
                        Storage::put($path, (string) $resizedDetailImage->encode());

                        // Resize and apply watermark for app - list size
                        $path_app_list = $j->hashName('public/adpost/applist');
                        $resizedAppListImage = $originalImage->resize(160, 160, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $watermarkWidth = $resizedAppListImage->width() * 0.1; // Adjust the scale of the watermark as needed
                        $watermarkHeight = $watermarkWidth * (125/126);
                        $watermark = Image::make(public_path('storage/' . $settings['watermark']))->resize($watermarkWidth, $watermarkHeight);
                        $resizedAppListImage->insert($watermark, 'bottom-right', 5, 5);
                        Storage::put($path_app_list, (string) $resizedAppListImage->encode());

                        // Resize and apply watermark for app - detail size
                        $path_app_detail = $j->hashName('public/adpost/appdetail');
                        $resizedAppDetailImage = $originalImage->resize(230, 230, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $watermarkWidth = $resizedAppDetailImage->width() * 0.1; // Adjust the scale of the watermark as needed
                        $watermarkHeight = $watermarkWidth * (125/126);
                        $watermark = Image::make(public_path('storage/' . $settings['watermark']))->resize($watermarkWidth, $watermarkHeight);
                        $resizedAppDetailImage->insert($watermark, 'bottom-right', 10, 10);
                        Storage::put($path_app_detail, (string) $resizedAppDetailImage->encode());

                        array_push($imagenamesArr, $save_img);
                    }
                }

            } catch (NotReadableException $e) {

                // Handle the case where the image is unsupported
                // Log the error or return a response with an error message
                // return Response::json(['error' => 'Unsupported image type. Please upload a valid image.'], 422);
                $response = 'invalid_image';
                return $response;

            }
                $predefined_imgs = implode(',', $imagenamesArr);
            }
        } else {

            $new_imgs = "";
            $check_old_imgs = array();
            $check_new_imgs = array();

            /* remove the deleted image from the storage folder start */
            if (!empty($old_images)) {
                $post_imgs = TblPost::where('id', $post_id)->pluck('images')->toArray();
                foreach ($post_imgs as $post_img) {
                    $imgs = explode(',', $post_img);
                    foreach ($imgs as $img) {
                        $db[] = $img;
                    }
                }
                $matched_imgs = array_intersect($db, $old_images);
                $unmatched_imgs = array_diff($db, $old_images);
                foreach ($unmatched_imgs as $unmatched_img) {
                    $unmatched_img_name = str_replace("adpost/predefined/", '', $unmatched_img);

                    /* remove web normal img file */
                    if (is_file(public_path('/storage/adpost/predefined/normal/' . $unmatched_img_name))) {
                        $path = public_path('/storage/adpost/predefined/normal/' . $unmatched_img_name);
                        unlink($path);
                    }
                    /* remove web list img file */
                    if (is_file(public_path('/storage/adpost/predefined/list/' . $unmatched_img_name))) {
                        $path = public_path('/storage/adpost/predefined/list/' . $unmatched_img_name);
                        unlink($path);
                    }
                    /* remove web detail img file */
                    if (is_file(public_path('/storage/adpost/predefined/' . $unmatched_img_name))) {
                        $path = public_path('/storage/adpost/predefined/' . $unmatched_img_name);
                        unlink($path);
                    }
                    /* remove image file from app list folder */
                    if (is_file(public_path('/storage/adpost/applist/' . $unmatched_img_name))) {
                        $app_list = public_path('/storage/adpost/applist/' . $unmatched_img_name);
                        unlink($app_list);
                    }
                    /* remove image file from app detail folder */
                    if (is_file(public_path('/storage/adpost/appdetail/' . $unmatched_img_name))) {
                        $app_detail = public_path('/storage/adpost/appdetail/' . $unmatched_img_name);
                        unlink($app_detail);
                    }
                }
            }
            /* remove the deleted image from the storage folder end */

            if (array_key_exists('images', $formdata)) {
                $images = $formdata['images'];
                $imagenamesArr = [];

                try {
                
                foreach ($images as $key => $j) {
                    $img_index = explode(',', $formdata['selected-img-index']);
                    if (in_array($key, $img_index)) {
                        /* Get watermark image */
                        $settings = Setting::get_logos();
                        /* Get image size settings */
                        $imagesizeSet = Setting::get_image_size_settings();

                        /* Calculate dimensions for list view */
                        $list_size = explode('*', $imagesizeSet['list']);
                        $list_width = $list_size[0];
                        $list_height = $list_size[1];

                        /* Calculate dimensions for detail view */
                        $detail_size = explode('*', $imagesizeSet['detail']);
                        $detail_width = $detail_size[0];
                        $detail_height = $detail_size[1];

                        /* Load the original image */
                        $originalImage = Image::make($j);
                        $save_img = $j->hashName('adpost/predefined');
                        // Apply watermark for original size
                        $path_web_original = $j->hashName('public/adpost/predefined/normal');
                        $originalImage->insert(public_path('storage/' . $settings['watermark']), 'bottom-right', 5, 5);
                        Storage::put($path_web_original, (string) $originalImage->encode());

                        // Resize and apply watermark for list view
                        $path_web_list = $j->hashName('public/adpost/predefined/list');
                        $resizedListImage = $originalImage->resize($list_width, $list_height, function ($constraint) {
                            $constraint->aspectRatio();
                        });

                        $watermarkWidth = $resizedListImage->width() * (0.01); // Adjust the scale of the watermark as needed
                        $watermarkHeight = $watermarkWidth * (125/126);
                        $watermark = Image::make(public_path('storage/' . $settings['watermark']))->resize($watermarkWidth, $watermarkHeight);
                        $resizedListImage->insert($watermark, 'bottom-right', 5, 5);
                        Storage::put($path_web_list, (string) $resizedListImage->encode());

                        // Resize and apply watermark for detail view
                        $path = $j->hashName('public/adpost/predefined');
                        $resizedDetailImage = $originalImage->resize($detail_width, $detail_height, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $watermarkWidth = $resizedDetailImage->width() * 0.1; // Adjust the scale of the watermark as needed
                        $watermarkHeight = $watermarkWidth * (125/126);
                        $watermark = Image::make(public_path('storage/' . $settings['watermark']))->resize($watermarkWidth, $watermarkHeight);
                        $resizedDetailImage->insert($watermark, 'bottom-right', 10, 10);
                        Storage::put($path, (string) $resizedDetailImage->encode());

                        // Resize and apply watermark for app - list size
                        $path_app_list = $j->hashName('public/adpost/applist');
                        $resizedAppListImage = $originalImage->resize(160, 160, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $watermarkWidth = $resizedAppListImage->width() * 0.1; // Adjust the scale of the watermark as needed
                        $watermarkHeight = $watermarkWidth * (125/126);
                        $watermark = Image::make(public_path('storage/' . $settings['watermark']))->resize($watermarkWidth, $watermarkHeight);
                        $resizedAppListImage->insert($watermark, 'bottom-right', 5, 5);
                        Storage::put($path_app_list, (string) $resizedAppListImage->encode());

                        // Resize and apply watermark for app - detail size
                        $path_app_detail = $j->hashName('public/adpost/appdetail');
                        $resizedAppDetailImage = $originalImage->resize(230, 230, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $watermarkWidth = $resizedAppDetailImage->width() * 0.1; // Adjust the scale of the watermark as needed
                        $watermarkHeight = $watermarkWidth * (125/126);
                        $watermark = Image::make(public_path('storage/' . $settings['watermark']))->resize($watermarkWidth, $watermarkHeight);
                        $resizedAppDetailImage->insert($watermark, 'bottom-right', 10, 10);
                        Storage::put($path_app_detail, (string) $resizedAppDetailImage->encode());

                        array_push($imagenamesArr, $save_img);
                    }
                }
            } catch (NotReadableException $e) {

                $response = 'invalid_image';
                return $response;

            }
                $new_imgs = $imagenamesArr;
            }
            $check_old_imgs = !empty($old_images) ? $old_images : [];
            $check_new_imgs = !empty($new_imgs) ? $new_imgs : [];
            $allimgs = array_merge($check_old_imgs, $check_new_imgs);
            $predefined_imgs = implode(',', $allimgs);
        }

        // Post resize and watermark attachment end

        $skipfields = array('currency_id', 'paid_id', 'text-shipping-fee', 'post-id', '_token', 'choosen_package', 'payment_type', 'package_type', 'text-video-sst', 'text-title-sst', 'number-price-sst', 'textarea-desc-sst', 'post-catid-sst', 'file-images-sst', 'text-city-sst', 'city_name', 'main_city_name', 'city_lat', 'city_lag', 'country_long', 'country_short', 'state_long', 'state_short', 'g-recaptcha-response', 'old_images', 'images', 'selected-img-index', 'product_condition', 'exchangeToBuy', 'FixedPrice', 'InstantBuy', 'images_indhu');
        if (!empty($city_name)) {
            if ($city_name == $main_city_name) {
                $cityNames = $main_city_name;
            } else {
                $cityNames = $city_name . "," . $main_city_name;
            }
        } else {
            $cityNames = $main_city_name;
        }

        $brand_model_fields = [];
        foreach ($formdata as $key => $value) {
            if (in_array($key, $skipfields)) {
                continue;
            }
            //brandwithmodels values imploding

            if ((strpos($key, 'brandwithmodel') == true) || (strpos($key, 'brandswithmodels') == true)) {
                if (strpos($key, 'brandwithmodel') == true) {
                    array_push($brand_model_fields, $value);
                }
                if (strpos($key, 'brandswithmodels') == true) {
                    array_push($brand_model_fields, $value);
                }
            }
        }

        if ($post_id == "0") {
            $settings = Setting::get_logos();
            if ($choosen_package == "free") {
                $active_status = 1;
            } else if (!empty($paid_id)) {
                $active_status = 1;
            } else {
                $active_status = 0;
            }

            //insert process begin
            $alias_val = $slug . '-' . $this->total_post();
            $post_id = TblPost::create([
                'user_id' => $user_id,
                'category_id' => $cat_id,
                'title' => $title,
                'description' => $desc,
                'price' => $price,
                'slug' => $alias_val,
                'city' => $city_id,
                'locality' => $cityNames,
                'images' => $predefined_imgs,
                'currency_id' => $currency_id,
                'active' => $active_status,
                'product_condition' => $product_condidition,
                'exchange_to_buy' => $exchangetobuy,
                'fixed_price' => $FixedPrice,
                'instant_buy' => $InstantBuy,
                'video_url' => $video_url,
                'shipping_rate' => $shipping_rate
            ])->id;
            
            foreach ($formdata as $key => $value) {
                if (in_array($key, $skipfields)) {
                    continue;
                }
                $field_id = explode('_', $key)[0];


                //checkbox values imploding
                if (strpos($key, 'checkbox') == true) {
                    $value = implode(',', $value);
                }

                //brandwithmodels values imploding
                if (strpos($key, 'brandswithmodels') == true) {
                    continue;
                }
                if (strpos($key, 'brandwithmodel') == true) {
                    $value = implode(',', $brand_model_fields);
                }

                //image file upload
                if (strpos($key, 'file') == true) {
                    if (empty($value)) {
                        $value = "";
                    } else {
                        $value = $value->store('adpost', 'public');
                    }
                }
                $value = ($value == null) ? "" : $value;
                TblPostValue::create([
                    'post_id' => $post_id,
                    'field_id' => $field_id,
                    'value' => $value
                ]);
            }

            //insert process end

            $get_pack_info = Package::where('id', $formdata['package_type'])->first();
            $living_days = $get_pack_info->duration;
            $curr_date = date('Y-m-d');
            $end_date = date('Y-m-d', strtotime($curr_date . "+" . $living_days . " days"));
            TblPostedAdPackageInfo::create([
                'user_id' => $user_id,
                'post_id' => $post_id,
                'ad_type' => 'free',
                'start_date' => $curr_date,
                'end_date' => $end_date,
                'active' => '1'
            ]);

            if ($choosen_package == "free") {
                /* Get followers ids and send push notification start*/
                $settings = Setting::get_logos();
                $site_name = $settings['name'];

                $user_ids = TblFollowers::where('seller_id', $user_id)->where('is_followed', 1)->pluck('user_id');
                $get_user_info = User::where('id', $user_id)->first();
                $get_post_info = TblPost::where('id', $post_id)->first();
                $slug = TblPost::get_post_slug($get_post_info->slug);
                $followers = User::select("users.*")->whereIn('users.id', $user_ids)->whereNull('users.deleted_at')->get();
                $fcmid = array();
                foreach ($followers as $follower) {
                    // $fcmid = !empty($follower->fcmid) ? $follower->fcmid : "";
                    // $message = array("notifydata" => array('to_id' => $follower->id, 'from_id' => $user_id, 'message' => "New post added by " . $get_user_info->name . "!. Post Name - " . $title, 'notify_from' => 'new_post', 'notify_title' => "New Post Added In " . $site_name . "!..", 'post_id' => $post_id, 'slug' => $slug));
                    // TblPost::send_push_notification($fcmid, $message);

                    // $mail_data = array("send_maildata" => array('to_id' => $follower->id, 'message' => "New post added by " . $get_user_info->name . "!. Post Name - " . $title, 'subject' => "New Post Added In " . $site_name . "!..", 'ad_url' => $slug));
                    // $mail_key = "success_mail";
                    // Setting::notification_mail($mail_data, $mail_key);
                }
                /* Get followers ids and send push notification end */
                //$response = "success";
                //return $response;
                $response = ['status' => 'success', 'slug' => $alias_val];
                return $response;
            } else if (!empty($paid_id)) {
                $release_from_type_payment = TblPayment::where('post_id', $post_id);
                $release_from_type_payment->update([
                    "active" => "0"
                ]);
                $currency_symbol = Setting::get_admin_default_currency();
                TblPayment::create([
                    "s_payment_id" => $paid_id,
                    "user_id" => $user_id,
                    "post_id" => $post_id,
                    "start_date" => $curr_date,
                    "end_date" => $end_date,
                    "live_days" => $get_pack_info->duration,
                    "package_amount" => $get_pack_info->price,
                    "active" => "1",
                    "payment_loc_ref_id" => "-",
                    "payment_status" => "completed",
                    "payment_type" => "stripe",
                    "package_id" => $get_pack_info->id,
                    "coupon_id" => "",
                    'currency_id' => $currency_symbol->default_currency_id
                ]);
                $release_from_type_free = TblPostedAdPackageInfo::where('post_id', $post_id);
                $release_from_type_free->update([
                    "active" => "0"
                ]);

                /* Get followers ids and send push notification start - for paid post*/
                $settings = Setting::get_logos();
                $site_name = $settings['name'];

                $user_ids = TblFollowers::where('seller_id', $user_id)->where('is_followed', 1)->pluck('user_id');
                $get_user_info = User::where('id', $user_id)->first();
                $get_post_info = TblPost::where('id', $post_id)->first();
                $slug = TblPost::get_post_slug($get_post_info->slug);
                $followers = User::select("users.*")->whereIn('users.id', $user_ids)->whereNull('users.deleted_at')->get();
                $fcmid = array();
                foreach ($followers as $follower) {
                    // $fcmid = !empty($follower->fcmid) ? $follower->fcmid : "";
                    // $message = array("notifydata" => array('to_id' => $follower->id, 'from_id' => $user_id, 'message' => "New post added by " . $get_user_info->name . "!. Post Name - " . $title, 'notify_from' => 'new_post', 'notify_title' => "New Post Added In " . $site_name . "!..", 'post_id' => $post_id, 'slug' => $slug));
                    // TblPost::send_push_notification($fcmid, $message);

                    // $mail_data = array("send_maildata" => array('to_id' => $follower->id, 'message' => "New post added by " . $get_user_info->name . "!. Post Name - " . $title, 'subject' => "New Post Added In " . $site_name . "!..", 'ad_url' => $slug));
                    // $mail_key = "success_mail";
                    // Setting::notification_mail($mail_data, $mail_key);
                }
                /* Get followers ids and send push notification end */


                $response = array(
                    'type' => "payment",
                    'post_id' => $post_id,
                );
                return $response;
            } else {
                $response = array(
                    'type' => "payment",
                    'post_id' => $post_id,
                );
                return $response;
            }
        } else {

            //update begin
            $node = TblPost::find($post_id);

            //new slug update
            $pre_slug = explode('-', $node->slug);
            $pre_slug_id = end($pre_slug);
            $alias_val = $slug . '-' . $pre_slug_id;
            $node->update([
                'user_id' => $user_id,
                'category_id' => $cat_id,
                'title' => $title,
                'description' => $desc,
                'price' => $price,
                'slug' => $alias_val,
                'city' => $city_id,
                'locality' => $cityNames,
                'images' => $predefined_imgs,
                'currency_id' => $currency_id,
                'product_condition' => $product_condidition,
                'exchange_to_buy' => $exchangetobuy,
                'fixed_price' => $FixedPrice,
                'instant_buy' => $InstantBuy,
                'video_url' => $video_url,
                'shipping_rate' => $shipping_rate
            ]);

            $new_form_fields = [];

            foreach ($formdata as $key => $value) {
                if (in_array($key, $skipfields)) {
                    continue;
                }

                if (strpos($key, 'brandswithmodels') == true) {
                    continue;
                }

                $field_id = explode('_', $key)[0];
                //checkbox values imploding -- skipped itself if empty
                if (strpos($key, 'checkbox') == true) {
                    $value = implode(',', $value);
                }
                //brandiwthmodel values imploding -- skipped itself if empty
                if (strpos($key, 'brandwithmodel') == true) {
                    $value = implode(',', $brand_model_fields);
                }

                //image file upload -- skipped itself if empty
                if (strpos($key, 'file') == true) {
                    if (empty($value)) {
                        $value = "";
                    } else {
                        $value = $value->store('adpost', 'public');
                    }
                }

                $post_value_exist = TblPostValue::where('post_id', $post_id)->where('field_id', $field_id)->where('active', '1')->first();
                $value = ($value == null) ? "" : $value;
                if (empty($post_value_exist)) {
                    TblPostValue::create([
                        'post_id' => $post_id,
                        'field_id' => $field_id,
                        'value' => $value
                    ]);
                } else {
                    $post_value_exist->update([
                        'value' => $value
                    ]);
                }
                array_push($new_form_fields, $field_id);
            }

            //if any fields deactivated in TblFieldsDetail, do it same in TblPostValue
            $all_fields_detail = TblFieldsDetail::where('cat_id', $cat_id)->where('active', '1')->get('id');
            $upd1 = TblPostValue::where('post_id', $post_id)->whereNotIn('field_id', $all_fields_detail);
            $upd1->update(['active' => '0']);

            //if any field makes empty from form submit, updating below
            $empty_form_fields = TblPostValue::where('post_id', $post_id)->whereNotIn('field_id', $new_form_fields)->where('active', '1')->get();
            foreach ($empty_form_fields as $r) {
                $field_id = $r['field_id'];
                $field_detail = TblFieldsDetail::where('id', $field_id)->where('active', '1')->get();
                $filetype = $field_detail[0]->type;
                if ($filetype == "file") {
                    continue;
                }
                $node = TblPostValue::where('post_id', $post_id)->where('field_id', $field_id);
                $node->update(['value' => ""]);
            }
            //update end
            $response = "success";
            return $response;
        }
    }
}
