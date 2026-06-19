<?php

namespace App\Http\Livewire;

use App\Models\TblCategory;
use App\Models\TblCity;
use App\Models\TblFieldsDetail;
use App\Models\TblFieldsOption;
use App\Models\TblPost;
use App\Models\Feature;
use App\Models\FeaturesMappingGroup;
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
use Illuminate\Support\Str;
use App\Models\TblSavedPosts;

class PostDetail extends Component
{
    public $product, $info_location, $category_name, $info_user, $ancestors, $category_id;
    public $additional = [];
    public $avg_rating = "0.0", $reviews;
    public $related_products;
    public $check_is_paid;
    public $current_pack_name = "";
    public $report_types = "";
    public $recently_viewed_products = [];
    public $query_data = [];
    public $get_features = [];
    public $is_favorited;
    public $is_sold = false;

    public function checkIfFavorited()
    {
        if (empty($this->product) || $this->product->isEmpty() || !Auth::check()) {
            $this->is_favorited = false;
            return;
        }
        
        $post_id = $this->product[0]->id;
    
        $this->is_favorited = TblSavedPosts::where('user_id', Auth::id())
                                        ->where('post_id', $post_id)
                                        ->exists();
    }

    public function checkIfSold()
    {
        if (empty($this->product) || $this->product->isEmpty()) {
            $this->is_sold = false;
            return;
        }
        
        $post_id = $this->product[0]->id;
        
        // Use the correct column name 'sold_status' from your table
        $this->is_sold = TblPost::where('id', $post_id)
                              ->where('sold_status', 1) // Assuming 1 means sold, 0 means available
                              ->exists();
    }
    
    public function toggleFavorite()
    {
        if (empty($this->product) || $this->product->isEmpty()) {
            $this->dispatchBrowserEvent('show-toast', ['type' => 'error', 'message' => 'Post data not loaded.']);
            return;
        }

        if (!Auth::check()) {
            $this->dispatchBrowserEvent('show-toast', ['type' => 'error', 'message' => 'Please login to favorite this post.']);
            return redirect()->route('login');
        }

        // Check if item is sold
        if ($this->is_sold) {
            $this->dispatchBrowserEvent('show-toast', ['type' => 'error', 'message' => 'This item has been sold and cannot be favorited.']);
            return;
        }
    
        $post_id = $this->product[0]->id;
        $user_id = Auth::id();
    
        $favorite = TblSavedPosts::where('user_id', $user_id)
                                 ->where('post_id', $post_id)
                                 ->first();
    
        if ($favorite) {
            $favorite->delete();
            $this->is_favorited = false;
            $this->dispatchBrowserEvent('show-toast', ['type' => 'info', 'message' => 'Removed from favorites!']);
        } else {
            TblSavedPosts::create([ 
                'user_id' => $user_id,
                'post_id' => $post_id,
            ]); 
            $this->is_favorited = true;
            $this->dispatchBrowserEvent('show-toast', ['type' => 'success', 'message' => 'Added to favorites!']);
        }
    }
    
    public function render()
    {
        try {
            $seg2 = request()->segment(1);
            $this->product = TblPost::where('slug', $seg2)->get();

            if ($this->product->isEmpty()) {
                abort(404);
            }
            
            $this->checkIfFavorited(); 
            $this->checkIfSold(); // Check if item is sold

            $city_id = $this->product[0]->city;
            $post_id = $this->product[0]->id;
            $is_exchangeable = $this->product[0]->exchange_to_buy ?? 0;

            if ($is_exchangeable == 1) {
                $this->additional[] = [
                    'type' => 'boolean',
                    'label' => 'Exchange to Buy',
                    'value' => 'Yes, willing to exchange for other items',
                ];
            }
            
            $postDetail = TblPost::where('id', $post_id)->get();

            $get = User::where('id', $this->product[0]->user_id)->first();

            if (!$get || $get->deleted_at !== null || $get->is_blocked > 0) {
                abort(404);
            }
            
            $visible_posts = TblPost::check_payment_pack_expired($this->product[0]->id);
            //if (empty($visible_posts)) {
              //  abort(404, 'Post payment or package expired');
            //}
            
            if (!empty(Auth::user()->id)) {
                TblNotifications::where('to_id', Auth::user()->id)
                    ->where('slug', $seg2)
                    ->update(['read_status' => 1]);
            }

            if (!empty(Auth::user()->id)) {
                $userid = Auth::user()->id;
                $today = date("Y-m-d");
                $viewed_postids = TblPostInsight::where('user_id', $userid)
                    ->whereDate('created_at', $today)
                    ->pluck('post_id')
                    ->toArray();
                $this->recently_viewed_products = TblPost::get_recently_viewed_products($viewed_postids, $post_id);
            }

            $client_ip = $_SERVER['REMOTE_ADDR'];
            $tbl_info = TblPostView::where('post_id', $post_id)
                ->where('ip_address', $client_ip)
                ->get();

            $curr_date = date('Y-m-d');
            if ($tbl_info->isEmpty()) {
                TblPostView::create([
                    'post_id' => $post_id,
                    'ip_address' => $client_ip,
                    'last_viewed_on' => $curr_date,
                    'views' => 1,
                ]);
                TblPost::where('id', $post_id)->increment('views_count');
            } else {
                $last_viewed_on = $tbl_info[0]->last_viewed_on;
                $viewed_id = $tbl_info[0]->id;
                $new_count = ($tbl_info[0]->views) + 1;
                
                TblPostView::where('id', $viewed_id)->update([
                    'last_viewed_on' => $curr_date,
                    'views' => $new_count,
                ]);
                TblPost::where('id', $post_id)->increment('views_count');
            }

            if (!empty(Auth::user()->id)) {
                $chk_post = TblPost::where('id', $post_id)->first();
                if ($chk_post && $chk_post->user_id != Auth::user()->id) {
                    $auth_user_id = Auth::user()->id;
                    $curr_city = Session::has("CurrLoggedCity") ? Session::get("CurrLoggedCity") : "";
                    $curr_lat = Session::has("CurrLoggedLat") ? Session::get("CurrLoggedLat") : "";
                    $curr_lng = Session::has("CurrLoggedLng") ? Session::get("CurrLoggedLng") : "";

                    $chk_record = TblPostInsight::where('post_id', $post_id)
                        ->where('ip_address', $client_ip)
                        ->where("user_id", $auth_user_id)
                        ->whereDate('visited_date', $curr_date)
                        ->get();

                    if ($chk_record->isEmpty()) {
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
                        TblPostInsight::where('id', $record_id)->update([
                            'views' => $view_count,
                            'city' => $curr_city,
                            'latitude' => $curr_lat,
                            'logitude' => $curr_lng,
                        ]);
                    }
                }
            }

            $this->info_location = TblCity::join('tbl_countries', 'tbl_cities.country_id', '=', 'tbl_countries.id')
                ->join('tbl_states', 'tbl_cities.state_id', '=', 'tbl_states.id')
                ->where('tbl_cities.id', $city_id)
                ->get(['tbl_cities.*', 'tbl_countries.code as country_short', 'tbl_countries.name as country_long', 'tbl_states.code as state_short', 'tbl_states.name as state_long']);

            $cat_id = $this->product[0]->category_id;
            $this->category_id = $cat_id;
            $this->ancestors = TblCategory::ancestorsAndSelf($cat_id);

            $this->check_is_paid = TblPayment::where('post_id', $post_id)
                ->where('active', '1')
                ->get();

            $this->current_pack_name = "";
            if ($this->check_is_paid->isNotEmpty()) {
                $packid = $this->check_is_paid[0]->package_id;
                $this->current_pack_name = Package::where('id', $packid)->get();
            }

            $this->category_name = TblCategory::find($cat_id)->title ?? '';
            $this->category_slug = TblCategory::find($cat_id)->slug ?? '';

            $user_id = $this->product[0]->user_id;
            $this->info_user = User::leftjoin('user_profiles', 'user_profiles.user_id', '=', 'users.id')
                ->where('users.id', $user_id)
                ->get(['users.*', 'user_profiles.phone', 'user_profiles.show_mobile']);

            $this->getAdditionalInfo($post_id);
            $this->get_features = $this->getFeatures($this->query_data);
            
            $this->avg_rating = TblReview::rate_avg($post_id);

            $this->review = TblReview::join('users', 'tbl_reviews.user_id', '=', 'users.id')
                ->where('post_id', $post_id)
                ->get(['tbl_reviews.*', 'users.name']);

            $this->related_products = TblPost::get_related_products($cat_id, $post_id);
            $this->report_types = ReportType::where('type', 'post')->get();

            add_action("apm_main", function () {
                echo view('livewire.post-detail', [
                    'related_products' => $this->related_products,
                    'review' => $this->review,
                    'info_location' => $this->info_location,
                    'product' => $this->product,
                    'category_name' => $this->category_name,
                    'category_slug' => $this->category_slug ?? '',
                    'report_types' => $this->report_types,
                    'info_user' => $this->info_user,
                    'check_is_paid' => $this->check_is_paid,
                    'recently_viewed_products' => $this->recently_viewed_products,
                    'avg_rating' => $this->avg_rating,
                    'additional' => $this->additional,
                    'features' => $this->get_features,
                    'is_favorited' => $this->is_favorited,
                    'is_sold' => $this->is_sold
                ])->render();
            }, 20, 1);

            return view('livewire.sample_content');
        } catch (Exception $e) {
            abort(404, $e->getMessage());
        }
    }

    public function getFeatures(array $query_data)
    {
        $featureMap = FeaturesMappingGroup::where('cat_id', $this->category_id)
            ->orderBy('list_order', 'asc')
            ->get();

        $featureItems = [];
        foreach ($featureMap as $featuresMap) {
            $categoryName = $this->cleanCategoryName($featuresMap->features_title);
            $featureItems[$categoryName][] = $this->cleanFeatureItems(
                explode(',', $featuresMap->features_items)
            );
        }

        $query = $this->buildFeatureQuery($query_data);

        $featuresdata = [];
        if ($query && $query->isNotEmpty()) {
            $features = $query->first()->toArray();
            $other_features = json_decode($features['other_features'] ?? '[]', true) ?? [];

            if ($this->category_id == '64') {
                $featuresdata = $this->processVehicleFeatures($featureItems, $other_features);
            } else {
                $featuresdata = $this->processGeneralFeatures($other_features);
            }
        }

        return $featuresdata;
    }

    protected function cleanCategoryName($name)
    {
        return preg_replace('/^(catagories|categories)\./i', '', $name);
    }

    protected function cleanFeatureItems(array $items)
    {
        return array_map(function($item) {
            return $this->cleanCategoryName($item);
        }, $items);
    }

    protected function buildFeatureQuery(array $query_data)
    {
        if (!empty($query_data['brand']) && !empty($query_data['model'])) {
            return Feature::where('cat_id', $this->category_id)
                ->where('make', $query_data['brand'])
                ->where('model', $query_data['model'])
                ->limit(50)
                ->get();
        }

        if (!empty($query_data['label_name'])) {
            return Feature::where('cat_id', $this->category_id)
                ->where('label_name', $query_data['label_name'])
                ->limit(50)
                ->get();
        }

        if (!empty($query_data['breed'])) {
            return Feature::where('cat_id', $this->category_id)
                ->where('dog_breed_group', $query_data['breed'])
                ->limit(50)
                ->get();
        }

        return null;
    }

    protected function processVehicleFeatures($featureItems, $other_features)
    {
        $featuresdata = [];
        
        foreach ($featureItems as $category => $fields) {
            foreach ($fields as $fieldGroup) {
                $categoryResults = [];
                foreach ($fieldGroup as $field) {
                    if (isset($other_features[$field])) {
                        $cleanField = preg_replace('/^catagories\./i', '', $field);
                        $cleanField = $this->formatString($cleanField);
                        $categoryResults[$cleanField] = $other_features[$field];
                    }
                }
                if (!empty($categoryResults)) {
                    $featuresdata[$category] = $categoryResults;
                }
            }
        }
        
        return $featuresdata;
    }

    protected function processGeneralFeatures($other_features)
    {
        $featuresdata = [];
        
        foreach ($other_features as $key => $value) {
            $cleanKey = preg_replace('/^catagories\./i', '', $key);
            $cleanKey = $this->formatString($cleanKey);
            
            if (!empty($value)) {
                $featuresdata[$cleanKey] = $value;
            }
        }
        
        return $featuresdata;
    }

    protected function cleanFeatureData($data)
    {
        $cleaned = [];
        foreach ($data as $key => $value) {
            $cleanKey = preg_replace('/^catagories\./i', '', $key);
            $cleanKey = $this->formatString($cleanKey);
            $cleaned[$cleanKey] = $value;
        }
        return $cleaned;
    }

    public function formatString($value)
    {
        $value = str_replace('_', ' ', $value);
        $value = ucwords($value);
        return preg_replace('/\bL\b/', 'l', $value);
    }

    protected function capitalizeKeys($array)
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            $newKey = ucfirst($key);
            if (is_array($value)) {
                $newArray[$newKey] = $this->capitalizeKeys($value);
            } else {
                $newArray[$newKey] = $value;
            }
        }
        return $newArray;
    }

    public function getAdditionalInfo($post_id)
    {
        $post_detail = TblPostValue::where('post_id', $post_id)
            ->where('active', '1')
            ->get();

        $query_data = [];
        $brand = "";
        $model = "";
        $breed = "";
        $label_name = "";

        foreach ($post_detail as $j) {
            $field_id = $j['field_id'] ?? null;
            $post_value = $j['value'] ?? null;
            
            if (!$field_id) {
                continue;
            }

            $tbl_fields = TblFieldsDetail::where('id', $field_id)->first();
            
            if (!$tbl_fields) {
                continue;
            }

            $tbl_fields_type = $tbl_fields->type ?? null;
            $tbl_fields_label = $tbl_fields->name ?? null;
            $form_field_name = $tbl_fields->form_field_name ?? null;

            if (in_array($tbl_fields_type, ['select', 'autocomplete'])) {
                if ($form_field_name == "brandwithmodel") {
                    $tbl_fields_label = "Brand & Model";
                    $values = explode(',', $post_value);
                    $brand_id = $values[0] ?? null;
                    
                    if ($brand_id) {
                        $get_options = TblFieldsOption::where('id', 'Like', '%' . $brand_id . '%')
                            ->pluck('key')
                            ->first();
                        
                        if ($get_options) {
                            $brand = $get_options;
                            $new_post_value = $get_options; 

                            if (isset($values[1]) && !empty($values[1])) {
                                $model = Str::title(str_replace('-', ' ', $values[1]));
                                $new_post_value .= ', ' . $model;
                            }
                            $post_value = $new_post_value;
                        }
                    }
                } else {
                    $get_options = TblFieldsOption::where('field_id', $field_id)
                        ->where('value', $post_value)
                        ->get();

                    if ($tbl_fields_label == "Dog Breed Group") {
                        $breed = TblFieldsOption::where('field_id', $field_id)
                            ->where('value', $post_value)
                            ->value('key');
                    }

                    if ($tbl_fields_label == "Label Or Name") {
                        $label_name = TblFieldsOption::where('field_id', $field_id)
                            ->where('value', $post_value)
                            ->value('key');
                    }

                    if ($get_options->isNotEmpty()) {
                        $post_value = $get_options[0]->key ?? $post_value;
                    }
                }
            }

            if ($tbl_fields_type == "checkbox-group") {
                $checkedvalues = [];
                $values = explode(',', $post_value);

                foreach ($values as $k) {
                    $option = TblFieldsOption::where('field_id', $field_id)
                        ->where('value', $k)
                        ->first();
                    
                    if ($option && $option->key) {
                        $checkedvalues[] = $option->key;
                    }
                }

                $post_value = implode(',', $checkedvalues);
            }

            if ($tbl_fields_type && $tbl_fields_label) {
                $this->additional[] = [
                    'type' => $tbl_fields_type,
                    'label' => $tbl_fields_label,
                    'value' => $post_value
                ];
            }
        }

        $this->query_data = [
            'brand' => $brand,
            'model' => $model,
            'breed' => $breed,
            'label_name' => $label_name
        ];
    }
}