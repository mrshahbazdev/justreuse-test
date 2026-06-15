<?php

namespace App\Http\Livewire;

use App\Models\TblCategory;
use App\Models\TblCity;
use App\Models\TblFieldsDetail;
use App\Models\TblFieldsOption;
use App\Models\TblPost;
use App\Models\TblPostValue;
use App\Models\TblReview;
use App\Models\TblNotifications;
use App\Models\User;
use App\Models\TblPostView;
use App\Models\TblPostInsight;
use Exception;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use DB;
use App\Models\Package;
use App\Models\ReportType;
use App\Models\TblPayment;
use App\Models\TblPostedAdPackageInfo;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str; //for slug

class PostDetail extends Component
{

    public $product, $additional = [], $info_location, $category_name, $info_user, $ancestors;
    //review form fields
    public $avg_rating = "0.0", $reviews;
    public $related_products;
    public $check_is_paid;
    public $current_pack_name = "";
    public $report_types = "";
    public $recently_viewed_products = array();

    public function render()
    {
        try {
            $seg2 = request()->segment(1);
            $this->product = TblPost::where('slug', $seg2)->get();
            $city_id = $this->product[0]->city;
            $post_id = $this->product[0]->id;
            $postDetail = TblPost::where('id', $post_id)->get();

            // $blockedUsers = User::blocked_users();
            // $check_blockUser = $postDetail->whereIn('user_id', $blockedUsers)->count();

            // if ($check_blockUser != 0) {
            //     abort(404);
            // }

            $get = User::where('id',$this->product[0]->user_id)->first();
            if($get->deleted_at!=null || $get->is_blocked > 0)
            {
                abort(404);
            }

            //check package expired or payment expired for the post
            $visible_posts = TblPost::check_payment_pack_expired($this->product[0]->id);
            if (empty($visible_posts)) {
                abort(404);
            }

            /* Update notification read status start */
            if (!empty(Auth::user()->id)) {
                TblNotifications::where('to_id', Auth::user()->id)->where('slug', $seg2)->update(array('read_status' => 1));
            }



            if (!empty(Auth::user()->id)) {
                $userid = Auth::user()->id;
                $today = date("Y-m-d");
                //$this->recently_viewed_products = TblPost::whereIn('id',$rpt_type)->get();
                $viewed_postids = TblPostInsight::where('user_id', $userid)->whereDate('created_at', $today)->pluck('post_id')->toArray();
                $this->recently_viewed_products = TblPost::get_recently_viewed_products($viewed_postids, $post_id);
            }

            /* Update notification read status end */
            $client_ip = $_SERVER['REMOTE_ADDR'];
            $tbl_info = TblPostView::where('post_id', $post_id)->where('ip_address', $client_ip)->get();
            $curr_date = date('Y-m-d');
            if ($tbl_info->count() <= 0) {
                TblPostView::create([
                    'post_id' => $post_id,
                    'ip_address' => $client_ip,
                    'last_viewed_on' => $curr_date,
                    'views' => 1,
                ]);
				//update count
                //TblPost::where('id', $post_id)->update(['views_count'=> DB::raw('views_count+1')]);
                TblPost::where('id', $post_id)->increment('views_count');

            } else {
                $last_viewed_on = $tbl_info[0]->last_viewed_on;
                $viewed_id = $tbl_info[0]->id;
                $new_count = ($tbl_info[0]->views) + 1;
                if ($curr_date > $last_viewed_on) {
                    $node = TblPostView::where('id', $viewed_id);
                    $node->update([
                        'last_viewed_on' => $curr_date,
                        'views' => $new_count,
                    ]);
				//update count
                //TblPost::where('id', $post_id)->update(['views_count'=> DB::raw('views_count+1')]);
                TblPost::where('id', $post_id)->increment('views_count');
                }
            }

            // insert record in post insight table start

            $chk_post = TblPost::where('id', $post_id)->first();

            if (!empty(Auth::user()->id)) {
                if ($chk_post->user_id != Auth::user()->id) {

                    $auth_user_id = Auth::user()->id;
                    $curr_city = Session::has("CurrLoggedCity") ? Session::get("CurrLoggedCity") : "";
                    $curr_lat = Session::has("CurrLoggedLat") ? Session::get("CurrLoggedLat") : "";
                    $curr_lng = Session::has("CurrLoggedLng") ? Session::get("CurrLoggedLng") : "";

                    // dd(Session::get("CurrLoggedCity"));
                    $chk_record = TblPostInsight::where('post_id', $post_id)->where('ip_address', $client_ip)->where("user_id", $auth_user_id)->whereDate('visited_date', $curr_date)->get();

                    if ($chk_record->count() == 0) {
                        TblPostInsight::create([
                            'user_id' => $auth_user_id,
                            'post_id' => $post_id,
                            'ip_address' => $client_ip,
                            'visited_date' => $curr_date,
                            'views' => 1,
                            'city' => $curr_city,
                            'latitude' => $curr_lat,
                            'logitude' => $curr_lng,
                        ]);
                    } else {
                        $record_id = $chk_record[0]->id;
                        $view_count = ($chk_record[0]->views) + 1;
                        $node1 = TblPostInsight::where('id', $record_id);
                        $node1->update([
                            'views' => $view_count,
                            'city' => $curr_city,
                            'latitude' => $curr_lat,
                            'logitude' => $curr_lng,
                        ]);
                    }
                }
            }
            // insert record in post insight table start

            //Post location info
            $this->info_location = TblCity::join('tbl_countries', 'tbl_cities.country_id', '=', 'tbl_countries.id')
                ->join('tbl_states', 'tbl_cities.state_id', '=', 'tbl_states.id')
                ->where('tbl_cities.id', $city_id)
                ->get(['tbl_cities.*', 'tbl_countries.code as country_short', 'tbl_countries.name as country_long', 'tbl_states.code as state_short', 'tbl_states.name as state_long']);
            $cat_id = $this->product[0]->category_id;
            $this->ancestors = TblCategory::ancestorsAndSelf($cat_id);
            //begin - to get package info
            $this->check_is_paid = TblPayment::where('post_id', $post_id)->where('active', '1')->get();
            $this->current_pack_name = "";
            if ($this->check_is_paid->count() > 0) {
                $packid = $this->check_is_paid[0]->package_id;
                $this->current_pack_name = Package::where('id', $packid)->get();
            }
            //end - to get package info
            $this->category_name = TblCategory::find($cat_id)->title;
            $this->category_slug = TblCategory::find($cat_id)->slug;
            $user_id = $this->product[0]->user_id;
            $this->info_user = User::leftjoin('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                ->where('users.id', $user_id)
                ->get(['users.*', 'user_profiles.phone', 'user_profiles.show_mobile']);
            $this->getAdditionalInfo($post_id);
            //star rating calculation begin
            $this->avg_rating = TblReview::rate_avg($post_id);
            $review = TblReview::join('users', 'tbl_reviews.user_id', '=', 'users.id')
                ->get(['tbl_reviews.*', 'users.name'])
                ->where('post_id', $post_id);
            //star rating calculation end
            //begin -related products
            $this->related_products = TblPost::get_related_products($cat_id, $post_id);
            //end -related products
            //report types data
            $this->report_types = ReportType::where('type', 'post')->get();
            return view('livewire.post-detail', compact('review'));
        } catch (Exception $e) {
            abort(404);
        }
    }

    /* Get custome filed info based on category */

    public function getAdditionalInfo($post_id)
    {
        $post_detail = TblPostValue::where('post_id', $post_id)->where('active', '1')->get();
        foreach ($post_detail as $j) {
            $field_id = $j['field_id'];
            $post_value = $j['value'];
            $tbl_fields = TblFieldsDetail::where('id', $field_id)->first();
            $tbl_fields_type = $tbl_fields->type;
            $tbl_fields_label = $tbl_fields->name;
            if ($tbl_fields_type == "select" || $tbl_fields == "autocomplete") {
                if ($tbl_fields->form_field_name == "brandwithmodel") {
                    $tbl_fields_label = "Brand & Model";
                    $brand_id = explode(',', $post_value)[0];
                    $get_options = TblFieldsOption::where('id', 'Like', '%' . $brand_id . '%')->pluck('key')->first();
                    $post_value = $get_options . ', ' . Str::title(str_replace('-', ' ', explode(',', $post_value)[1]));
                } else {
                    $get_options = TblFieldsOption::where('field_id', $field_id)->where('value', $post_value)->get();
                    $post_value = $get_options[0]->key;
                }
            }
            if ($tbl_fields_type == "checkbox-group") {
                $checkedvalues = "";
                $post_value = explode(',', $post_value);
                foreach ($post_value as $k) {
                    $get_options = TblFieldsOption::where('field_id', $field_id)->where('value', $k)->get();
                    $checkedvalues .= $get_options[0]->key . ",";
                }
                $post_value = rtrim($checkedvalues, ',');
            }
            $arraydet = array('type' => $tbl_fields_type, 'label' => $tbl_fields_label, 'value' => $post_value);
            array_push($this->additional, $arraydet);
        }
    }
}
