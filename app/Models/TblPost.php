<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\Package;
use App\Models\TblCurrency;
use App\Models\TblCity;
use App\Models\TblState;
use App\Models\TblPayment;
use App\Models\TblPostedAdPackageInfo;
use App\Models\TblNotifications;
use App\Models\TblSavedPosts;
use App\Models\TblReview;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Google\Auth\ApplicationDefaultCredentials;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Firebase\JWT\JWT; // <-- Add this line

use Illuminate\Support\Facades\Session;
class TblPost extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'tbl_posts';
    protected $fillable = ['shipping_rate', 'giving_away', 'video_url', 'block_exchange', 'sold_status', 'user_id', 'category_id', 'active', 'title', 'description', 'price', 'slug', 'city', 'locality', 'images', 'currency_id', 'product_condition', 'exchange_to_buy', 'fixed_price', 'instant_buy', 'views_count', 'completeAddress', 'show_number'];
    protected $dates = ['deleted_at'];



    /* get likes count for a post */
    public static function get_likes_count($id)
    {
        $fav_post_cnt = TblSavedPosts::where('post_id', $id)->count();
        return $fav_post_cnt;
    }

    /* get comments count for a post */
    public static function get_comments_count($id)
    {
        $post_comment_cnt = TblReview::where('post_id', $id)->where('approved', 1)->count();
        return $post_comment_cnt;
    }
    /* get unexpired payment post */
    public static function get_unexpired_payment_post_ids()
    {
        $curr_date = date('Y-m-d H:i:s');
        $payment_ids_array = TblPayment::where('active', '1')->where('start_date', '<=', $curr_date)->where('end_date', '>=', $curr_date)->pluck('post_id')->toArray();
        return $payment_ids_array;
    }
    /*check payment package expired for post */
    public static function check_payment_pack_expired($id)
    {
        $package_postids = TblPostedAdPackageInfo::where('post_id', $id)->where('active', '1')->whereDate('end_date', '>=', date("Y-m-d"))->pluck('post_id')->toArray();
        $payment_postids = TblPayment::where('post_id', $id)->where('active', '1')->whereDate('end_date', '>=', date("Y-m-d"))->pluck('post_id')->toArray();
        $all_postids = array_merge($package_postids, $payment_postids);
        $visible_post = TblPost::whereIn('id', $all_postids)->where('active', 1)->where('sold_status', 0)->whereNull('deleted_at')->pluck('id')->toArray();
        return $visible_post;
    }
    /* get unexpired free post */
    public static function get_unexpired_free_post_ids()
    {
        $curr_date = date('Y-m-d H:i:s');
        $free_ids_array = TblPostedAdPackageInfo::where('active', '1')->where('start_date', '<=', $curr_date)->where('end_date', '>=', $curr_date)->pluck('post_id')->toArray();
        return $free_ids_array;
    }
    public static function product_count($main_category = NULL, $latitude = NULL, $longitude = NULL, $distance = NULL, $cat_id = NULL, $city = NULL, $state = NULL, $country = NULL)
    {
        $addressids = $payment_ads_ids = $free_ads_ids = $final_result_ids = array();
        if (!empty($latitude) && !empty($city)) {
            $addressids = TblPost::get_surrounding_city_ids($latitude, $longitude, $distance);
        } else if (!empty($state)) {
            $state_id = TblState::where('name', $state)->where('active', 1)->pluck('id');
            if (count($state_id) > 0) {
                $addressids = TblCity::where('state_id', $state_id)->pluck('id');
            }
        } else if (!empty($country)) {
            $country_id = TblCountry::where('name', $country)->where('active', 1)->pluck('id');
            if (count($country_id) > 0) {
                $addressids = TblCity::where('country_id', $country_id)->pluck('id');
            }
        }
        /* get unexpired payment post */
        $payment_ids_array = TblPost::get_unexpired_payment_post_ids();
        /* get unexpired free post */
        $free_ids_array = TblPost::get_unexpired_free_post_ids();
        $final_result_ids = array_merge($payment_ids_array, $free_ids_array);
        if ($main_category == 1) {
            $sids = array();
            $subcategory = TblCategory::descendantsAndSelf($cat_id);
            foreach ($subcategory as $subcat) {
                $sids[] = $subcat->id;
            }
            if (!empty($addressids)) {
                $post = TblPost::whereIn('category_id', $sids)->whereIn('id', $final_result_ids)->whereIn('city', $addressids)->whereNull('deleted_at')->where('active', '1')->where('sold_status', 0)->count();
                return $post;
            } else {
                $post = TblPost::whereIn('category_id', $sids)->whereIn('id', $final_result_ids)->whereIn('city', array())->whereNull('deleted_at')->where('active', '1')->where('sold_status', 0)->count();
                return $post;
            }
        } else {
            if (!empty($addressids)) {
                $post = TblPost::where('category_id', $cat_id)->whereIn('id', $final_result_ids)->whereIn('city', $addressids)->whereNull('deleted_at')->where('active', '1')->where('sold_status', 0)->count();
                return $post;
            } else {
                $post = TblPost::where('category_id', $cat_id)->whereIn('id', $final_result_ids)->whereIn('city', array())->whereNull('deleted_at')->where('active', '1')->where('sold_status', 0)->count();
                return $post;
            }
        }
    }
    public static function get_post_slug($slug)
    {
        $url = URL::to('/' . $slug);
        return $url;
    }
    public static function city_product_cnt($cat_id = NULL, $city_name = NULL)
    {
        $city_ids = TblCity::where('name', $city_name)->pluck('id');
        $catgory = TblCategory::where('slug', $cat_id)->first();
        $payment_ids_array = TblPost::get_unexpired_payment_post_ids(); // get unexpired payment post //        
        $free_ids_array = TblPost::get_unexpired_free_post_ids(); // get unexpired free post //
        $get_all_posts = array_merge($payment_ids_array, $free_ids_array);
        if (!empty($catgory)) {
            $sids = array();
            $subcategory = TblCategory::descendantsAndSelf($catgory->id);
            foreach ($subcategory as $subcat) {
                $sids[] = $subcat->id;
            }
            $city_post = TblPost::whereIn('category_id', $sids)->whereIn('id', $get_all_posts)->whereIn('city', $city_ids)->whereNull('deleted_at')->where('active', '1')->where('sold_status', 0)->count();
            return $city_post;
        } else {
            $city_post = TblPost::whereIn('id', $get_all_posts)->whereIn('city', $city_ids)->whereNull('deleted_at')->where('active', '1')->where('sold_status', 0)->count();
            return $city_post;
        }
    }
    /* Get current post currency */
    public static function get_post_currency($currency_id)
    {
        $currency_hex = TblCurrency::where('id', $currency_id)->pluck('currency_hex')->toArray();
        if (!empty($currency_hex)) {
            return $currency_hex;
        } else {
            $currency_hex = TblDefaultCurrency::where('id', 1)->pluck('currency_hex')->toArray();
            return $currency_hex;
        }
    }
    public static function get_single_post_information($id)
    {
        $images = TblPost::where('id', $id)->pluck('images')->first();
        $post_info = TblPost::where('id', $id)->get(['title', 'city', 'user_id'])->first();
        $seller_info = User::where('id', $post_info->user_id)->get(['name'])->first();
        $post_img = URL::to('/storage/noimage150.png');
        if (!empty($images)) {
            $imgUrl = explode(',', $images)[0];
            $imgName = str_replace("adpost/predefined/", '', $imgUrl);
            $is_file = base_path() . '/storage/adpost/predefined/list/' . $imgName; //applist
            $post_img = (is_file($is_file)) ? URL::to('/storage/adpost/predefined/list/' . $imgName) : URL::to('/storage/' . $imgUrl);
        }
        $getPackType = TblPayment::where('post_id', $id)->get(['package_id']);
        $ad_type = "";
        if ($getPackType->count() > 0) {
            $packageID = $getPackType[0]->package_id;
            $ad_type = Package::where('id', $packageID)->pluck('ad_type')->toArray();
        }
        $data = array(
            'images' => $post_img,
            'ad_type' => !empty($ad_type[0]) ? $ad_type[0] : "",
            "post_title" => $post_info->title,
            'city' => $post_info->city,
            //'seller_name' => $seller_info->name,
            'seller_id' => $post_info->user_id
        );
        return $data;
    }
    public static function get_single_post_information_old($id)
    {
        $images = TblPost::where('id', $id)->pluck('images')->first();
        $post_info = TblPost::where('id', $id)->first();
        $seller_info = User::where('id', $post_info->user_id)->first();
        if (!empty($images)) {
            $imgUrl = explode(',', $images)[0];
            $imgName = str_replace("adpost/predefined/", '', $imgUrl);
            $is_file = base_path() . '/storage/adpost/applist/' . $imgName;
            if (is_file($is_file)) {
                $post_img = URL::to('/storage/adpost/applist/' . $imgName);
            } else {
                $post_img = URL::to('/storage/' . $imgUrl);
            }
        } else {
            $post_img = URL::to('/storage/noimage150.png');
        }
        $getPackType = TblPayment::where('post_id', $id)->get();
        $ad_type = "";
        if ($getPackType->count() > 0) {
            $packageID = $getPackType[0]->package_id;
            $ad_type = Package::where('id', $packageID)->pluck('ad_type')->toArray();
        }
        $data = array(
            'images' => $post_img,
            'ad_type' => !empty($ad_type[0]) ? $ad_type[0] : "",
            "post_title" => $post_info->title,
            'city' => $post_info->city,
            'seller_name' => $seller_info->name,
            'seller_id' => $post_info->user_id
        );
        return $data;
    }
    // get single post information with deleted post also
    public static function get_single_post_information_with_delete($id)
    {
        $images = TblPost::where('id', $id)->withTrashed()->pluck('images')->first();
        $post_info = TblPost::where('id', $id)->withTrashed()->first();
        $seller_info = User::where('id', $post_info->user_id)->withTrashed()->first();
        if (!empty($images)) {
            $imgUrl = explode(',', $images)[0];
            $imgName = str_replace("adpost/predefined/", '', $imgUrl);
            $is_file = base_path() . '/storage/adpost/applist/' . $imgName;
            if (is_file($is_file)) {
                $post_img = URL::to('/storage/adpost/applist/' . $imgName);
            } else {
                $post_img = URL::to('/storage/' . $imgUrl);
            }
        } else {
            $post_img = URL::to('/storage/noimage150.png');
        }
        $getPackType = TblPayment::where('post_id', $id)->get();
        $ad_type = "";
        if ($getPackType->count() > 0) {
            $packageID = $getPackType[0]->package_id;
            $ad_type = Package::where('id', $packageID)->pluck('ad_type')->toArray();
        }
        $is_deleted = !empty($post_info->deleted_at) ? 1 : 0;
        $check_post_package = TblPost::check_post_expired($id);
        $is_expired = $check_post_package['expired'] == "Expired" ? 1 : 0;
        $data = array(
            'images' => $post_img,
            'ad_type' => !empty($ad_type[0]) ? $ad_type[0] : "",
            "post_title" => $post_info->title,
            'city' => $post_info->city,
            'seller_name' => $seller_info->name,
            'seller_id' => $post_info->user_id,
            'is_deleted' => $is_deleted,  // 1 means deleted else 0
            'is_expired' => $is_expired,  // 1 means expired else 0
        );
        return $data;
    }
    /* Get current post product condition */
    public static function get_product_condition($post_id)
    {
        $get_pro_condition = TblPost::where('id', $post_id)->pluck('product_condition')->toArray();
        if (!empty($get_pro_condition[0])) {
            if ($get_pro_condition[0] == 1) {
                $product_condition = "Like New";
            } else if ($get_pro_condition[0] == 2) {
                $product_condition = "Lightly used";
            } else if ($get_pro_condition[0] == 3) {
                $product_condition = "Heavily used";
            } else {
                $product_condition = "";
            }
        } else {
            $product_condition = "";
        }
        return $product_condition;
    }
    /* Get city based on state */
    public static function getCities($state_id, $latitude = NULL, $longitude = NULL, $dist = NULL)
    {
        $addressids = array();
        if (!empty($latitude)) {
            $addressids = TblPost::get_surrounding_city_ids($latitude, $longitude, $dist);
        }
        if (!empty($addressids)) {
            $cities = TblCity::whereIn('id', $addressids)->groupBy('name')->orderBy('name')->get();
        } else {
            $cities = TblCity::where('state_id', $state_id)->groupBy('name')->orderBy('name')->get();
        }
        return $cities;
    }
    /* Get post package ad type */
    public static function getAddtype($post_id)
    {
        $package = "";
        $payment = TblPayment::where('post_id', $post_id)->where('active', 1)->get(['package_id'])->first();
        if (!empty($payment)) {
            $package = package::where('id', $payment->package_id)->first();
        }
        return $package;
    }
    /* get my(currently logged in user) post  */
    public static function getMyPost($user_id)
    {
        $unexpired_payments = TblPost::get_unexpired_payment_post_ids();
        $unexpired_free = TblPost::get_unexpired_free_post_ids();
        $visible_ids = array_merge($unexpired_payments, $unexpired_free);
        $visible_posts = TblPost::where('user_id', $user_id)->whereNull('deleted_at')->whereIn('id', $visible_ids)->where('active', 1)->where('sold_status', 0)->orderBy('title', 'asc')->get(['title', 'id', 'slug']);
        return $visible_posts;
    }
    /* Get max distance range */
    public static function getMaxDistace()
    {
        $distance_range = Setting::where('key', 'distance_range')->pluck('value');
        $ans = json_decode($distance_range[0], true);
        return $ans['max_distance'];
    }
    /* Get post location */
    public static function getPostloc($cityid)
    {
        $package = "";
        $city = Tblcity::where('id', $cityid)->pluck('name')->first();
        if (!empty($city)) {
            return $city;
        } else {
            return $package;
        }
    }
    /* related post */
    public static function get_related_products($cat_id, $post_id)
    {
        /* get unexpired payment post */
        $payment_ids_array = TblPost::get_unexpired_payment_post_ids();
        /* get unexpired free post */
        $free_ids_array = TblPost::get_unexpired_free_post_ids();
        $get_all_posts = array_merge($payment_ids_array, $free_ids_array);
        $resultdata = TblPost::join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
            ->where('tbl_posts.id', '!=', $post_id)
            ->where('tbl_posts.category_id', $cat_id)
            ->whereIn('tbl_posts.id', $get_all_posts)
            ->where('tbl_posts.active', 1)
            ->where('tbl_posts.sold_status', 0)
            ->orderBy('tbl_posts.created_at', 'desc')
            ->limit(10)
            ->get(['tbl_posts.*', 'tbl_cities.name as city_name'])->toArray();
        return $resultdata;
    }
    /* recently views posts */
    public static function get_recently_viewed_products($viewed_postids, $post_id)
    {
        $resultdata = TblPost::join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
            ->where('tbl_posts.id', '!=', $post_id)
            ->whereIn('tbl_posts.id', $viewed_postids)
            ->where('tbl_posts.active', 1)
            ->where('tbl_posts.sold_status', 0)
            ->orderBy('tbl_posts.created_at', 'desc')
            ->limit(10)
            ->get(['tbl_posts.*', 'tbl_cities.name as city_name'])->toArray();
        return $resultdata;
    }
    /* Get Popular locations based on post views count */
    public static function get_populor_loc()
    {
        $post_views = TblPostView::orderBy('views', 'desc')->get();
        $post_city_id = array();
        $popular_state_id = array();
        $data = array();
        if ($post_views) {
            foreach ($post_views as $post_view) {
                $get_post_city = TblPost::where('id', $post_view->post_id)->whereNull('deleted_at')->first();
                if (!empty($get_post_city)) {
                    $post_city_id[] = $get_post_city->city;
                }
            }
            $post_city = TblCity::whereIn('id', $post_city_id)->where('active', '1')->groupBy('state_id')->get();
            if (!empty($post_city)) {
                foreach ($post_city as $post_city) {
                    $popular_state_id[] = $post_city->state_id;
                }
                $popular_states = TblState::whereIn('id', $popular_state_id)->where('active', '1')->take('5')->get();
                // $popular_states = TblState::whereIn('id', $popular_state_id)->where('active', '1')->groupBy('name')->take('5')->get();
                foreach ($popular_states as $popular_state) {
                    $data[] = $popular_state->name;
                }
            }
        }
        return $data;
    }
    /* Get Trending locations based on post added by user */
    public static function get_trending_loc_default()
    {
        $tre_post_city_id = array();
        $tre_state_id = array();
        $post_city = "";
        $data = array();
        $get_all = TblPost::whereNull('deleted_at')->groupBy('city')->get();
        if (!empty($get_all)) {
            foreach ($get_all as $get_all) {
                $tre_post_city_id[] = $get_all->city;
            }
            $post_city = TblCity::whereIn('id', $tre_post_city_id)->where('active', '1')->groupBy('state_id')->get();
            if (!empty($post_city)) {
                foreach ($post_city as $post_city) {
                    $tre_state_id[] = $post_city->state_id;
                }
                $trending_states = TblState::whereIn('id', $tre_state_id)->where('active', '1')->orderBy('name', 'desc')->take('5')->get(['name']);
                // $trending_states = TblState::whereIn('id', $tre_state_id)->where('active', '1')->groupBy('name')->take('5')->get();
                foreach ($trending_states as $trending_state) {
                    $data[] = $trending_state->name;
                }
            }
        }
        return $data;
    }
    public static function get_trending_loc()
    {
        $get_country = session::get('GetCountry');
        $country = TblCountry::where('name', $get_country)->where('active', 1)->first();
        $data = [];
        if (!empty($country)) {
            $trending_states = TblState::where('country_id', $country->id)->get();
            if ($trending_states->isNotEmpty()) {
                $city_views = [];
                foreach ($trending_states as $state) {
                    $cities_in_state = TblCity::where('state_id', $state->id)->get();
                    foreach ($cities_in_state as $city) {
                        if (isset($city_views[$city->name])) {
                            continue;
                        }
                        $post_exists = TblPost::where('city', $city->id)->exists();
                        if ($post_exists) {
                            $total_views = TblPostView::whereIn(
                                'post_id',
                                TblPost::where('city', $city->id)->pluck('id')
                            )->sum('views');
                            $city_views[$city->name] = [
                                'city_id' => $city->id,
                                'city_name' => $city->name,
                                'views' => $total_views,
                            ];
                        }
                    }
                }
                $data = array_slice($city_views, 0, 5);
            }
        }
        return $data;
    }
    //check blacklist word
    public static function get_blacklist()
    {
        $data = array();
        $blacklist_word = "";
        $get_all = TblBlacklist::whereNull('deleted_at')->pluck('entry')->toArray();
        $yourArray = array_map('strtolower', $get_all);
        return json_encode($yourArray);
    }
    //check free post and package post expired
    public static function check_post_expired($id)
    {
        $from_date = "";
        $to_date = "";
        $ads_type = "";
        $expired = "";
        $is_bulk = "";
        $bluk_type = "";
        $package_price = "";
        /* get free post count */
        $get_post_count = TblPostedAdPackageInfo::where('user_id', Auth::id())->sum('publish_count');
        /* get if post is there in payment ads */
        $post_payment = TblPayment::where('post_id', $id)->where('active', '1')->first();
        /* get if post is there in free ads */
        $post_free = TblPostedAdPackageInfo::where('post_id', $id)->where('active', '1')->first();
        if (!empty($post_payment)) {
            $from_date = date('d M Y', strtotime(DATE($post_payment->start_date)));
            $to_date = date('d M Y', strtotime(DATE($post_payment->end_date)));
            /* check if payment post is expired or not */
            $check_post = TblPayment::where('post_id', $id)->where('active', '1')->whereDate('end_date', '>=', date("Y-m-d"))->get(['package_id', 'is_bulk'])->first();
            if (!empty($check_post)) {
                $package_info = Package::where('id', $check_post->package_id)->get(['bulk_type', 'name', 'price'])->first();
                if ($package_info->bulk_type == 0) {
                    $bluk_type = "";
                } else if ($package_info->bulk_type == 1) {
                    $bluk_type = "Validity based on PACKAGE";
                } else {
                    $bluk_type = "Validity based on ITEM";
                }
                $ads_type = $package_info->name;
                $is_bulk = $check_post->is_bulk;
                $expired = "";
                $package_price = $package_info->price;
            } else {
                $ads_type = "payment";
                $expired = "Expired";
                $is_bulk = 0;
                $bluk_type = "";
                $package_price = "";
            }
        } else if (!empty($post_free)) {
            $from_date = date('d M Y', strtotime(DATE($post_free->start_date)));
            $to_date = date('d M Y', strtotime(DATE($post_free->end_date)));
            /* check if free post is expired or not */
            $check_post = TblPostedAdPackageInfo::where('post_id', $id)->whereDate('end_date', '>=', date("Y-m-d"))->get(['id'])->first();
            if (!empty($check_post)) {
                $ads_type = "free";
                $expired = "";
                $is_bulk = 0;
                $bluk_type = "";
                $package_price = "";
            } else {
                $ads_type = "free";
                $expired = "Expired";
                $is_bulk = 0;
                $bluk_type = "";
                $package_price = "";
            }
        }
        $data = array(
            'from_date' => $from_date,
            'to_date' => $to_date,
            'ads_type' => $ads_type,
            'expired' => $expired,
            'post_count' => $get_post_count,
            'is_bulk' => $is_bulk,
            'bulk_type' => $bluk_type,
            'package_price' => $package_price
        );
        return $data;
    }
    //check free post and package post expired
    public static function check_post_expired_old($id)
    {
        $from_date = "";
        $to_date = "";
        $ads_type = "";
        $expired = "";
        $is_bulk = "";
        $bluk_type = "";
        $package_price = "";
        /* get free post count */
        $get_post_count = TblPostedAdPackageInfo::where('user_id', Auth::id())->sum('publish_count');
        /* get if post is there in payment ads */
        $post_payment = TblPayment::where('post_id', $id)->where('active', '1')->first();
        /* get if post is there in free ads */
        $post_free = TblPostedAdPackageInfo::where('post_id', $id)->where('active', '1')->first();
        if (!empty($post_payment)) {
            $from_date = date('d M Y', strtotime(DATE($post_payment->start_date)));
            $to_date = date('d M Y', strtotime(DATE($post_payment->end_date)));
            /* check if payment post is expired or not */
            $check_post = TblPayment::where('post_id', $id)->where('active', '1')->whereDate('end_date', '>=', date("Y-m-d"))->first();
            if (!empty($check_post)) {
                $package_info = Package::where('id', $check_post->package_id)->first();
                if ($package_info->bulk_type == 0) {
                    $bluk_type = "";
                } else if ($package_info->bulk_type == 1) {
                    $bluk_type = "Validity based on PACKAGE";
                } else {
                    $bluk_type = "Validity based on ITEM";
                }
                $ads_type = $package_info->name;
                $is_bulk = $check_post->is_bulk;
                $expired = "";
                $package_price = $package_info->price;
            } else {
                $ads_type = "payment";
                $expired = "Expired";
                $is_bulk = 0;
                $bluk_type = "";
                $package_price = "";
            }
        } else if (!empty($post_free)) {
            $from_date = date('d M Y', strtotime(DATE($post_free->start_date)));
            $to_date = date('d M Y', strtotime(DATE($post_free->end_date)));
            /* check if free post is expired or not */
            $check_post = TblPostedAdPackageInfo::where('post_id', $id)->whereDate('end_date', '>=', date("Y-m-d"))->first();
            if (!empty($check_post)) {
                $ads_type = "free";
                $expired = "";
                $is_bulk = 0;
                $bluk_type = "";
                $package_price = "";
            } else {
                $ads_type = "free";
                $expired = "Expired";
                $is_bulk = 0;
                $bluk_type = "";
                $package_price = "";
            }
        }
        $data = array(
            'from_date' => $from_date,
            'to_date' => $to_date,
            'ads_type' => $ads_type,
            'expired' => $expired,
            'post_count' => $get_post_count,
            'is_bulk' => $is_bulk,
            'bulk_type' => $bluk_type,
            'package_price' => $package_price
        );
        return $data;
    }
    /* For Home Page */
    public static function get_premium_ads($type, $limit, $addressids = NULL)
    {
        //begin-fetching records from "current date" between advertisement start_date & end_dates
        $curr_date = date('Y-m-d H:i:s');
        $blockedUsers = User::blocked_users();
        if (empty($addressids)) {
            $dataList = TblPayment::join('tbl_posts', 'tbl_posts.id', '=', 'tbl_payments.post_id')
                ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                ->join('packages', 'packages.id', '=', 'tbl_payments.package_id')
                ->join('users', 'users.id', '=', 'tbl_payments.user_id')
                ->whereNull('tbl_posts.deleted_at')
                ->where('tbl_posts.active', 1)
                ->where('tbl_posts.sold_status', 0)
                ->where('packages.ad_type', $type)
                ->where('tbl_payments.start_date', '<=', $curr_date)
                ->where('tbl_payments.end_date', '>=', $curr_date)
                ->where('tbl_payments.active', '1')
                ->whereNotIn('tbl_payments.user_id', $blockedUsers)
                ->limit($limit)
                ->inRandomOrder()
                ->orderBy('tbl_payments.created_at', 'desc')
                ->get(['tbl_posts.id as id', 'tbl_posts.giving_away as giving_away', 'tbl_posts.category_id as category_id', 'tbl_posts.title as title', 'tbl_posts.locality as locality', 'tbl_posts.price as price', 'tbl_posts.currency_id as currency_id', 'tbl_posts.slug as slug', 'tbl_posts.description as description', 'tbl_posts.created_at as created_at', 'tbl_posts.images as images', 'packages.ad_type as ad_type', 'tbl_cities.name as city_name', 'users.name as posted_by',])
                ->toArray();
        } else {
            $dataList = TblPayment::join('tbl_posts', 'tbl_posts.id', '=', 'tbl_payments.post_id')
                ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                ->join('packages', 'packages.id', '=', 'tbl_payments.package_id')
                ->join('users', 'users.id', '=', 'tbl_payments.user_id')
                ->whereNull('tbl_posts.deleted_at')
                ->where('tbl_posts.active', 1)
                ->where('tbl_posts.sold_status', 0)
                ->whereIn('tbl_posts.city', $addressids)
                ->where('packages.ad_type', $type)
                ->where('tbl_payments.start_date', '<=', $curr_date)
                ->where('tbl_payments.end_date', '>=', $curr_date)
                ->where('tbl_payments.active', '1')
                ->whereNotIn('tbl_payments.user_id', $blockedUsers)
                ->limit($limit)
                ->inRandomOrder()
                ->orderBy('tbl_payments.created_at', 'desc')
                ->get(['tbl_posts.id as id', 'tbl_posts.giving_away as giving_away', 'tbl_posts.category_id as category_id', 'tbl_posts.title as title', 'tbl_posts.locality as locality', 'tbl_posts.description as description', 'tbl_posts.price as price', 'tbl_posts.currency_id as currency_id', 'tbl_posts.slug as slug', 'tbl_posts.created_at as created_at', 'tbl_posts.images as images', 'packages.ad_type as ad_type', 'tbl_cities.name as city_name', 'users.name as posted_by',])
                ->toArray();
        }
        return $dataList;
        //end-fetching records from "current date" between advertisement start_date & end_dates
    }
    //begin-fetching paid top records from "current date" between advertisement start_date & end_dates
    public static function get_paid_top_ads()
    {
        $curr_date = date('Y-m-d H:i:s');
        $blockedUsers = User::blocked_users();
        $payment_ids_array = TblPayment::whereNotIn('tbl_payments.user_id', $blockedUsers)
            ->where('tbl_payments.active', '1')
            ->where('tbl_payments.start_date', '<=', $curr_date)
            ->where('tbl_payments.end_date', '>=', $curr_date)
            ->join("packages", function ($join) {
                $join->on("packages.id", "=", "tbl_payments.package_id")
                    ->where("packages.ad_type", "=", "top_ad");
            })
            ->orderBy('tbl_payments.created_at', 'desc')
            ->pluck('tbl_payments.post_id')->toArray();
        return $payment_ids_array;
    }
    //begin-fetching free records from "current date" between advertisement start_date & end_dates
    public static function get_free_ads()
    {
        $curr_date = date('Y-m-d H:i:s');
        $blockedUsers = User::blocked_users();
        $free_ids = TblPostedAdPackageInfo::where('active', '1')
            ->whereNotIn('user_id', $blockedUsers)
            ->where('start_date', '<=', $curr_date)
            ->where('end_date', '>=', $curr_date)
            ->orderBy('created_at', 'desc')
            ->pluck('post_id')->toArray();
        return $free_ids;
    }
    public static function merge_payment_with_free_ads()
    {
        /* get paid top ads only */
        $get_payment_ads = TblPost::get_paid_top_ads();
        /* get free ads only */
        $get_free_ads = TblPost::get_free_ads();
        $get_ads_big_cnt = array(count($get_free_ads), count($get_payment_ads));
        $maxs = array_keys($get_ads_big_cnt, max($get_ads_big_cnt));
        if ($maxs[0] == 0) {
            $big = $get_free_ads;
            $small = $get_payment_ads;
        } else {
            $big = $get_payment_ads;
            $small = $get_free_ads;
        }
        /* show the lowest number records ads type on the top 4 */
        for ($pid = 0; $pid <= count($big); $pid += 4) {
            $ids = (array_slice($small, $pid, 4));
            if ($pid != 0) {
                $pid = $pid + 4;
            }
            foreach ($ids as $k) {
                array_splice($big, $pid, 0, $k);
            }
        }
        $final_result_array = $big;
        return $final_result_array;
    }
    public static function get_latest_ads_home_page($final_result_array, $ids, $pagination, $addressids = NULL)
    {
        if (empty($addressids)) {
            // dd('sdb');
            $latest_ads_data = TblPost::select("tbl_posts.id as id", "tbl_posts.category_id as category_id", "tbl_posts.slug as slug", "tbl_posts.currency_id as currency_id", "tbl_posts.title as title", "tbl_posts.locality as locality", "tbl_posts.created_at as created_at", "tbl_posts.price as price", "tbl_cities.name as city_name")
                ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                ->whereIn('tbl_posts.id', $final_result_array)
                ->where('tbl_posts.active', 1)
                ->where('tbl_posts.sold_status', 0)
                ->whereNull('tbl_posts.deleted_at')
                ->orderByRaw(DB::raw("FIELD(tbl_posts.id, " . $ids . ")"))
                ->paginate($pagination);
            return $latest_ads_data;
        } else {
            $latest_ads_data = TblPost::select("tbl_posts.id as id", "tbl_posts.category_id as category_id", "tbl_posts.slug as slug", "tbl_posts.currency_id as currency_id", "tbl_posts.images as images", "tbl_posts.title as title", "tbl_posts.locality as locality", "tbl_posts.created_at as created_at", "tbl_posts.price as price", "tbl_cities.name as city_name")
                ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                ->whereIn('tbl_posts.id', $final_result_array)
                ->where('tbl_posts.active', 1)
                ->where('tbl_posts.sold_status', 0)
                ->whereNull('tbl_posts.deleted_at')
                ->whereIn('tbl_posts.city', $addressids)
                ->orderByRaw(DB::raw("FIELD(tbl_posts.id, " . $ids . ")"))
                ->paginate($pagination);
            return $latest_ads_data;
        }
    }
    public static function get_latest_ads_home_pages($final_result_array, $ids, $pagination, $addressids = NULL)
    {
        if (empty($ids)) {
            return collect(); // prevent SQL error if ids is empty
        }
        if (empty($addressids)) {
            $latest_ads_data = TblPost::select(
                "tbl_posts.id as id",
                "tbl_posts.category_id as category_id",
                "tbl_posts.slug as slug",
                "tbl_posts.currency_id as currency_id",
                "tbl_posts.title as title",
                "tbl_posts.locality as locality",
                "tbl_posts.created_at as created_at",
                "tbl_posts.price as price",
                "tbl_cities.name as city_name"
            )
                ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                ->whereIn('tbl_posts.id', $final_result_array)
                ->where('tbl_posts.active', 1)
                ->where('tbl_posts.sold_status', 0)
                ->whereNull('tbl_posts.deleted_at')
                // ->orderByRaw(DB::raw("FIELD(tbl_posts.id, " . $ids . ")"))
                ->orderByRaw(DB::raw("FIELD(tbl_posts.id, " . implode(',', $ids) . ")"))
                ->limit(6)
                ->get();
            return $latest_ads_data;
        } else {
            $latest_ads_data = TblPost::select(
                "tbl_posts.id as id",
                "tbl_posts.category_id as category_id",
                "tbl_posts.slug as slug",
                "tbl_posts.currency_id as currency_id",
                "tbl_posts.images as images",
                "tbl_posts.title as title",
                "tbl_posts.locality as locality",
                "tbl_posts.created_at as created_at",
                "tbl_posts.price as price",
                "tbl_cities.name as city_name"
            )
                ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                ->whereIn('tbl_posts.id', $final_result_array)
                ->where('tbl_posts.active', 1)
                ->where('tbl_posts.sold_status', 0)
                ->whereNull('tbl_posts.deleted_at')
                ->whereIn('tbl_posts.city', $addressids)
                // ->orderByRaw(DB::raw("FIELD(tbl_posts.id, " . $ids . ")"))
                ->orderByRaw(DB::raw("FIELD(tbl_posts.id, " . implode(',', $ids) . ")"))
                ->limit(6)
                ->get();
            return $latest_ads_data;
        }
    }
    public static function app_get_latest_ads_home_page($final_result_array, $ids, $limit, $page, $addressids = NULL)
    {
        if (empty($addressids)) {
            $latest_ads_data = TblPost::select("tbl_posts.*", "tbl_cities.name as city_name")
                ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                ->join('users', 'users.id', '=', 'tbl_posts.user_id')
                ->whereIn('tbl_posts.id', $final_result_array)
                ->where('tbl_posts.active', 1)
                ->where('tbl_posts.sold_status', 0)
                ->orderByRaw(DB::raw("FIELD(tbl_posts.id, " . $ids . ")"))
                ->limit($limit)->offset(($page - 1) * $limit)
                ->get(['tbl_posts.id as id', 'tbl_posts.giving_away as giving_away', 'tbl_posts.title as title', "tbl_posts.currency_id as currency_id", 'tbl_posts.locality as locality', 'tbl_posts.description as description', 'tbl_posts.price as price', 'tbl_posts.slug as slug', 'tbl_posts.created_at as created_at', 'tbl_posts.images as images', 'tbl_cities.name as city_name', 'users.name as posted_by']);
            return $latest_ads_data;
        } else {
            $latest_ads_data = TblPost::select("tbl_posts.*", "tbl_cities.name as city_name")
                ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                ->join('users', 'users.id', '=', 'tbl_posts.user_id')
                ->whereIn('tbl_posts.id', $final_result_array)
                ->whereIn('tbl_posts.city', $addressids)
                ->where('tbl_posts.active', 1)
                ->where('tbl_posts.sold_status', 0)
                ->orderByRaw(DB::raw("FIELD(tbl_posts.id, " . $ids . ")"))
                ->limit($limit)->offset(($page - 1) * $limit)
                ->get(['tbl_posts.id as id', "tbl_posts.currency_id as currency_id", 'tbl_posts.giving_away as giving_away', 'tbl_posts.title as title', 'tbl_posts.locality as locality', 'tbl_posts.description as description', 'tbl_posts.price as price', 'tbl_posts.slug as slug', 'tbl_posts.created_at as created_at', 'tbl_posts.images as images', 'tbl_cities.name as city_name', 'users.name as posted_by']);
            return $latest_ads_data;
        }
    }
    public static function app_get_latest_ads_count($final_result_array, $ids, $limit, $page, $addressids = NULL)
    {
        if (empty($addressids)) {
            $latest_ads_data = TblPost::select("tbl_posts.*", "tbl_cities.name as city_name")
                ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                ->join('users', 'users.id', '=', 'tbl_posts.user_id')
                ->whereIn('tbl_posts.id', $final_result_array)
                ->where('tbl_posts.active', 1)
                ->where('tbl_posts.sold_status', 0)
                ->orderByRaw(DB::raw("FIELD(tbl_posts.id, " . $ids . ")"))
                ->get(['tbl_posts.id as id', "tbl_posts.currency_id as currency_id", 'tbl_posts.giving_away as giving_away', 'tbl_posts.title as title', 'tbl_posts.locality as locality', 'tbl_posts.price as price', 'tbl_posts.slug as slug', 'tbl_posts.created_at as created_at', 'tbl_posts.images as images', 'tbl_cities.name as city_name', 'users.name as posted_by']);
            return count($latest_ads_data);
        } else {
            $latest_ads_data = TblPost::select("tbl_posts.*", "tbl_cities.name as city_name")
                ->join('tbl_cities', 'tbl_cities.id', '=', 'tbl_posts.city')
                ->join('users', 'users.id', '=', 'tbl_posts.user_id')
                ->whereIn('tbl_posts.id', $final_result_array)
                ->whereIn('tbl_posts.city', $addressids)
                ->where('tbl_posts.active', 1)
                ->where('tbl_posts.sold_status', 0)
                ->orderByRaw(DB::raw("FIELD(tbl_posts.id, " . $ids . ")"))
                ->get(['tbl_posts.id as id', "tbl_posts.currency_id as currency_id", 'tbl_posts.giving_away as giving_away', 'tbl_posts.title as title', 'tbl_posts.locality as locality', 'tbl_posts.price as price', 'tbl_posts.slug as slug', 'tbl_posts.created_at as created_at', 'tbl_posts.images as images', 'tbl_cities.name as city_name', 'users.name as posted_by']);
            return count($latest_ads_data);
        }
    }
    public static function get_surrounding_city_ids($lat, $lng, $distance)
    {
        $query = "
                SELECT id FROM (
                    SELECT *, 
                        (
                            (
                                (
                                    acos(
                                        sin(( $lat * pi() / 180))
                                        *
                                        sin(( `latitude` * pi() / 180)) + cos(( $lat * pi() /180 ))
                                        *
                                        cos(( `latitude` * pi() / 180)) * cos((( $lng - `logitude`) * pi()/180)))
                                ) * 180/pi()
                            ) * 60 * 1.1515 * 1.609344
                        )
                    as distance FROM `tbl_cities`
                ) tbl_cities
                WHERE distance <= $distance";
        $results = DB::select($query);
        $json_results = json_encode($results);
        $josn_decode_res = json_decode($json_results, true);
        $city_ids = array();
        foreach ($josn_decode_res as $josn_decode_res) {
            $city_ids[] = $josn_decode_res['id'];
        }
        return $city_ids;
    }
    /* Send push notification for posting the new post */
    public static function send_push_notificationOLd($registatoin_ids, $message, $header = null)
    {
        $result = "";
        if (!empty($registatoin_ids)) {
            $registrationIds = $registatoin_ids;
            $dat = array();
            $msg = array(
                'body' => $dat,
                'title' => $message['notifydata']['notify_title'],
                'sound' => 'default',
                'vibrate' => 1,
                'largeIcon' => 'large_icon',
                'smallIcon' => 'small_icon'
            );
            if (array_key_exists("order_id", $message['notifydata'])) {
                $order_id = $message['notifydata']['order_id'];
            } else {
                $order_id = "";
            }
            $fields = array(
                'to' => $registrationIds,
                'content_available' => true,
                'priority' => "high",
                'data' => array("test" => "test", 'notify_from' => $message['notifydata']['notify_from'], 'post_id' => $message['notifydata']['post_id'], 'to_id' => $message['notifydata']['from_id'], 'order_id' => $order_id),
                'notification' => array(
                    'body' => $message['notifydata']['message'],
                    'title' => $message['notifydata']['notify_title'],
                    'sound' => 'default',
                    'click_action' => "FLUTTER_NOTIFICATION_CLICK"
                ),
                'apns' => array(
                    'headers' => array(
                        'apns-priority' => '10'
                    ),
                    'payload' => array(
                        'aps' => array(
                            'sound' => 'default'
                        )
                    )
                )
            );
            if (!isset($headers)) {
                $headers = array(
                    'Authorization: key=AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
                    'Content-Type: application/json',
                );
            }
            /*  $headers = array(
                'Authorization: Key: AAAA-jEx_Ac:APA91bE0yi7bPxnc0mMn6KDPbkpSC3MUMeNojqFzzkNgHgmN1H1Z6pouyCs27belU4Y8txdoPFNVh_bqpnYTvyWJaLu3e2_eyX_eRL7YV3AYPfS-_3E4D2xq5nx2_pNILot9nT1v6AlB',
                'Content-Type: application/json',
            ); */
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            curl_close($ch);
            if ($result === false) {
                $error = curl_error($ch);
                dd($error);
                // Log or handle the error appropriately
            }
        }
        $result = TblNotifications::create([
            'from_id' => $message['notifydata']['from_id'],
            'to_id' => $message['notifydata']['to_id'],
            'post_id' => $message['notifydata']['post_id'],
            'msg' => $message['notifydata']['message'],
            'notify_from' => $message['notifydata']['notify_from'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'slug' => $message['notifydata']['slug'],
        ]);
        return $result;
    }
    //updated on 9/20/2025
    public function getAccessToken()
    {
        try {
            $serviceAccountFile = base_path('/serviceAccountKey.json');
            $tokenUri = 'https://oauth2.googleapis.com/token';

            if (!file_exists($serviceAccountFile)) {
                throw new \Exception("Service account file not found at: {$serviceAccountFile}");
            }

            $credentials = json_decode(file_get_contents($serviceAccountFile), true);
            if (empty($credentials['client_email']) || empty($credentials['private_key'])) {
                throw new \Exception("Invalid service account credentials.");
            }

            $now = time();
            $claimSet = [
                'iss' => $credentials['client_email'],
                'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
                'aud' => $tokenUri,
                'iat' => $now,
                'exp' => $now + 3600,
            ];

            // ✅ Must use RS256
            $jwt = JWT::encode($claimSet, $credentials['private_key'], 'RS256');

            $client = new Client();

            $response = $client->post($tokenUri, [
                'form_params' => [
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $jwt,
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (!isset($data['access_token'])) {
                throw new \Exception("Access token not found in response: " . json_encode($data));
            }

            return $data['access_token'];
        } catch (RequestException $e) {
            // Guzzle request-specific error
            throw new \Exception("HTTP Request failed: " . $e->getMessage());
        } catch (\Exception $e) {
            // Any other error
            throw new \Exception("Error getting access token: " . $e->getMessage());
        }
    }

    // Function to send the FCM notification using the HTTP v1 API
    public static function send_push_notification($registrationToken, $message)
    {
        $accessToken = (new self())->getAccessToken();  // Fetch the OAuth token
        // FCM v1 API URL (replace 'myproject-b5ae1' with your actual Firebase project ID)
        $url = 'https://fcm.googleapis.com/v1/projects/justreused/messages:send';
        // Define the payload
        $fields = [
            'message' => [
                'token' => $registrationToken, // Device token
                'notification' => [
                    'body' => $message['notifydata']['message'],
                    'title' => $message['notifydata']['notify_title']
                ],
                'data' => [
                    'notify_from' => $message['notifydata']['notify_from'],
                    'post_id' => $message['notifydata']['post_id'],
                    'to_id' => $message['notifydata']['from_id'],
                    'order_id' => isset($message['notifydata']['order_id']) ? $message['notifydata']['order_id'] : ''
                ],
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default'
                    ]
                ],
                'apns' => [
                    'headers' => [
                        'apns-priority' => '10'
                    ],
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                            'content-available' => 1
                        ]
                    ]
                ]
            ]
        ];
        // Set headers with the access token
        $headers = [
            'Authorization: Bearer ' . $accessToken,  // Use OAuth token instead of server key
            'Content-Type: application/json',
        ];
        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $result = curl_exec($ch);
        // Check for errors
        if ($result === false) {
            $error = curl_error($ch);
            curl_close($ch);
            throw new Exception("cURL error: " . $error);
        } else {
            curl_close($ch);
        }
        // Log the notification to your database (assuming a Laravel model for notifications)
        $result = TblNotifications::create([
            //'from_id' => $message['notifydata']['from_id'],
            'from_id' => $message['notifydata']['from_id'],
            'to_id' => $message['notifydata']['to_id'],
            //'to_id' => $message['notifydata']['from_id'],
            'post_id' => $message['notifydata']['post_id'],
            'msg' => $message['notifydata']['message'],
            'notify_from' => $message['notifydata']['notify_from'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
            'slug' => $message['notifydata']['slug'],
        ]);
        return $result;
    }
    public static function hide_email_address($email)
    {
        // $email = "balaji@gmail.com";
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            list($first, $last) = explode('@', $email);
            $first = str_replace(substr($first, '0'), str_repeat('*', strlen($first) - 1), $first);
            $last = explode('.', $last);
            $last_domain = str_replace(substr($last['0'], '0'), str_repeat('*', strlen($last['0']) - 1), $last['0']);
            $hideEmailAddress = $first . '@' . $last_domain . '.' . $last['1'];
            return $hideEmailAddress;
        }
    }
    public static function hide_mobile_number($number)
    {
        return substr($number, 0, 2) . '******' . substr($number, -2);
    }
    // seller cat_info
    public static function cat_info($id, $final_result_ids)
    {
        $result = array();
        $products = TblPost::select(
            'tbl_posts.id',
            'tbl_posts.user_id',
            'tbl_posts.category_id',
            'tbl_posts.title',
            'tbl_posts.images',
            'tbl_posts.created_at',
            'tbl_categories.id',
            'tbl_categories.title',
            'tbl_categories.parent_id'
        )->join('tbl_categories', 'tbl_posts.category_id', '=', 'tbl_categories.id')
            ->where('user_id', '=', $id)
            ->where('tbl_posts.active', 1)
            ->whereIn('tbl_posts.id', $final_result_ids)
            ->whereNull('tbl_posts.deleted_at')
            ->orderby('created_at', 'desc')
            ->get();
        // dd($products);
        $main_id = array();
        foreach ($products as $product) {
            $main_id[] = $product->parent_id;
        }
        // dd($main_id);
        $main_count = array_count_values($main_id);
        foreach ($main_count as $key => $value) {
            $data = TblPost::select('tbl_posts.id', 'tbl_posts.user_id', 'tbl_posts.category_id', 'tbl_posts.title', 'tbl_posts.images', 'tbl_posts.created_at', 'tbl_categories.id', 'tbl_categories.parent_id')->Join('tbl_categories', 'tbl_posts.category_id', '=', 'tbl_categories.id')->where('user_id', '=', $id)->where('parent_id', $key)->orderby('created_at', 'desc')->first();
            $image = URL::to('/storage/' . $data->images);
            $img = explode(',', $image);
            $cat_name = TblCategory::where('id', $key)->value('title');
            // $image = URL::to('/storage/' . $img);
            $result[] = array(
                'count' => $value,
                'image' => $img,
                'cate_name' => $cat_name
            );
        }
        return $result;
    }
    //UUID begin
    public $incrementing = false;
    protected $keyType = 'string';
    public static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }
    //check free post and package post expired
    public static function check_post_expired_admin($id)
    {
        $from_date = "";
        $to_date = "";
        $ads_type = "";
        $expired = "";
        $is_bulk = "";
        $bluk_type = "";
        $package_price = "";
        /* get free post count */
        $get_post_count = TblPostedAdPackageInfo::where('user_id', Auth::id())->sum('publish_count');
        /* get if post is there in payment ads */
        $post_payment = TblPayment::where('post_id', $id)->where('active', '1')->first();
        /* get if post is there in free ads */
        $post_free = TblPostedAdPackageInfo::where('post_id', $id)->where('active', '1')->first();
        if (!empty($post_payment)) {
            $from_date = date('d M Y', strtotime(DATE($post_payment->start_date)));
            $to_date = date('d M Y', strtotime(DATE($post_payment->end_date)));
            /* check if payment post is expired or not */
            $check_post = TblPayment::where('post_id', $id)->where('active', '1')->whereDate('end_date', '>=', date("Y-m-d"))->get(['package_id', 'is_bulk'])->first();
            if (!empty($check_post)) {
                $package_info = Package::where('id', $check_post->package_id)->get(['bulk_type', 'name', 'price'])->first();
                if ($package_info->bulk_type == 0) {
                    $bluk_type = "";
                } else if ($package_info->bulk_type == 1) {
                    $bluk_type = "Validity based on PACKAGE";
                } else {
                    $bluk_type = "Validity based on ITEM";
                }
                $ads_type = $package_info->name;
                $is_bulk = $check_post->is_bulk;
                $expired = "";
                $package_price = $package_info->price;
            } else {
                $ads_type = "payment";
                $expired = "Expired";
                $is_bulk = 0;
                $bluk_type = "";
                $package_price = "";
            }
        } else if (!empty($post_free)) {
            $from_date = date('d M Y', strtotime(DATE($post_free->start_date)));
            $to_date = date('d-m-y H:i A', strtotime(DATE($post_free->end_date)));
            //  $to_date = date('d M Y', strtotime(DATE($post_free->end_date)));
            /* check if free post is expired or not */
            $check_post = TblPostedAdPackageInfo::where('post_id', $id)->whereDate('end_date', '>=', date("Y-m-d"))->get(['id'])->first();
            if (!empty($check_post)) {
                $ads_type = "free";
                $expired = "";
                $is_bulk = 0;
                $bluk_type = "";
                $package_price = "";
            } else {
                $ads_type = "free";
                $expired = "Expired";
                $is_bulk = 0;
                $bluk_type = "";
                $package_price = "";
            }
        }
        $data = array(
            'from_date' => $from_date,
            'to_date' => $to_date,
            'ads_type' => $ads_type,
            'expired' => $expired,
            'post_count' => $get_post_count,
            'is_bulk' => $is_bulk,
            'bulk_type' => $bluk_type,
            'package_price' => $package_price
        );
        return $data;
    }
    public static function userCurrencyConversion($user_id, $price, $post_currency)
    {
        $user = User::find($user_id);
        if (!empty($user->preferred_currency)) {
            $user_curr = $user->preferred_currency;
            $user_prefer_currency = TblCurrency::where('id', $user_curr)->value('short_code');
        } else {
            $settings = Setting::get_logos();
            $default_currency = $settings['default_currency'];
            $user_prefer_currency = TblCurrency::where('id', $default_currency)->value('short_code');
        }
        if (empty($user_prefer_currency)) {
            $user_prefer_currency = 'EUR';
        }
        // if(!empty( $user_prefer_currency)){
        //     $settings = Setting::get_logos();
        //     $default_currency = $settings['default_currency'];
        //     $user_prefer_currency = TblCurrency::where('id', $default_currency)->value('short_code');
        // }
        $settings = Setting::get_logos();
        $default_currency = $settings['default_currency'];
        $product_currency = TblCurrency::where('id', $post_currency)->value('short_code');
        $base_currency = TblCurrency::where('id', $default_currency)->value('short_code');
        $settings = Setting::where('key', 'currency_conversion')->get();
        $curr_hex = TblCurrency::where('short_code', $user_prefer_currency)->value('currency_hex');
        if (!empty($curr_hex)) {
            $currency_hex = $curr_hex;
        } else {
            $currency_hex = TblDefaultCurrency::where('id', 1)->value('currency_hex');
        }
        $currency = TblCurrency::where('id', $post_currency)->value('short_code');
        // dd($user_currency,$currency);
        if ($user_prefer_currency == $currency) {
            $data = array(
                'convert_cur' => $price,
                'convert_code' => $user_prefer_currency,
                'convert_sym' => $currency_hex
            );
            return $data;
        }
        $product_currency = $currency;
        if ($settings->isNotEmpty()) {
            $currency_setting_json = $settings->first();
            $currency_setting = json_decode($currency_setting_json['value'], true);
            //print_r($currency_setting); exit;
            $base_pay = $currency_setting[$user_prefer_currency];
            $curr_hex = TblCurrency::where('short_code', $user_prefer_currency)->value('currency_hex');
            if (!empty($curr_hex)) {
                $currency_hex = $curr_hex;
            } else {
                $currency_hex = TblDefaultCurrency::where('id', 1)->value('currency_hex');
            }
            $converted_price = $base_pay * $price;
            $data = array(
                'convert_cur' => $converted_price,
                'convert_code' => $user_prefer_currency,
                'convert_sym' => $currency_hex
            );
            return $data;
        }
    }
    public static function get_currency_code($currency_id)
    {
        $currency_hex = TblCurrency::where('id', $currency_id)->pluck('short_code')->toArray();
        if (!empty($currency_hex)) {
            return $currency_hex;
        } else {
            $currency_hex = TblDefaultCurrency::where('id', 1)->pluck('short_code')->toArray();
            return $currency_hex;
        }
    }
    //updated at 18/03/2025
    public function city()
    {
        return $this->belongsTo(TblCity::class, 'city', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function reviews()
    {
        return $this->hasMany(TblReview::class, 'post_id', 'id');
    }
    public function category()
    {
        return $this->belongsTo(TblCategory::class, 'category_id');
    }

    /**
     * Get the city that owns the post.
     */
    public function city_name()
    {
        return $this->belongsTo(TblCity::class, 'city');
    }

    /**
     * Get all of the custom field values for the Post.
     * یہ تعلق applyCustomFilters کے لیے ضروری ہے
     */
    public function post_values()
    {
        return $this->hasMany(TblPostValue::class, 'post_id');
    }

    /**
     * Custom fields کی بنیاد پر posts کو filter کرنے کے لیے ایک scope.
     * **یہی وہ فنکشن ہے جو پچھلے ایرر کو حل کرتا ہے**
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApplyCustomFilters($query, $filters)
    {
        if (empty($filters)) {
            return $query;
        }

        foreach ($filters as $fieldId => $value) {
            if (!empty($value)) {
                $query->whereHas('post_values', function ($q) use ($fieldId, $value) {
                    $q->where('field_id', $fieldId);

                    if (is_array($value) && isset($value['min']) && isset($value['max'])) {
                        $q->whereBetween('value', [$value['min'], $value['max']]);
                    } elseif (is_array($value)) {
                        $q->where(function ($subQuery) use ($value) {
                            foreach ($value as $val) {
                                if (!empty($val)) {
                                    $subQuery->orWhereRaw("find_in_set(?, value)", [$val]);
                                }
                            }
                        });
                    } else {
                        $q->whereRaw("find_in_set(?, value)", [$value]);
                    }
                });
            }
        }

        return $query;
    }

}
