<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

// Models
use App\Models\TblCategory;
use App\Models\TblPost;
use App\Models\TblPostValue;
use App\Models\Package;
use App\Models\TblCountry;
use App\Models\TblState;
use App\Models\TblCity;
use App\Models\TblPaymentsMethod;
use App\Models\TblCurrency;
use App\Models\TblPostedAdPackageInfo;
use App\Models\Setting;
use App\Models\TblFieldsDetail;

class PostController extends Controller
{
    // ==================================================
    // 1. GET INITIAL DATA (Categories, Packages, etc.)
    // ==================================================
    public function getCreateData()
    {
        $parentCategories = TblCategory::whereNull('parent_id')->orderBy('list_order', 'asc')->get();
        $packages = Package::get_active_packages();
        $currencies = TblCurrency::where('active', '0')->orderBy('id', 'desc')->get();
        $paymentMethods = TblPaymentsMethod::where('active', '1')->get();
        
        $settings = Setting::get_logos();
        $defaultCurrency = $settings['default_currency'] ?? 1;

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $parentCategories,
                'packages' => $packages,
                'currencies' => $currencies,
                'payment_methods' => $paymentMethods,
                'default_currency' => $defaultCurrency
            ]
        ]);
    }

    // ==================================================
    // 2. GET SUB CATEGORIES
    // ==================================================
    public function getSubCategories($id)
    {
        $childCategories = TblCategory::where('parent_id', $id)
            ->orderBy('list_order', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $childCategories
        ]);
    }

    // ==================================================
    // 3. UPLOAD IMAGE
    // ==================================================
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|max:10240',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first()], 422);
        }

        try {
            $image = $request->file('image');
            $settings = Setting::get_logos();
            $watermarkPath = !empty($settings['watermark']) ? public_path('storage/' . $settings['watermark']) : null;
            $watermarkExists = $watermarkPath && file_exists($watermarkPath);

            $img = Image::make($image->getRealPath());

            $filename = Str::random(15) . '.jpg';

            // Get image size settings
            $imagesizeSet = Setting::get_image_size_settings();
            $list_width = 222;
            $list_height = 156;
            $detail_width = 500;
            $detail_height = 350;

            if (isset($imagesizeSet['list']) && str_contains($imagesizeSet['list'], "*")) {
                $listSize = explode('*', $imagesizeSet['list']);
                $list_width = $listSize[0];
                $list_height = $listSize[1];
            }
            if (isset($imagesizeSet['detail']) && str_contains($imagesizeSet['detail'], "*")) {
                $detailSize = explode('*', $imagesizeSet['detail']);
                $detail_width = $detailSize[0];
                $detail_height = $detailSize[1];
            }

            // Save Normal (full size with watermark)
            $normalImg = clone $img;
            if ($watermarkExists) {
                $normalImg->insert($watermarkPath, 'bottom-right', 10, 10);
            }
            Storage::disk('public')->put('adpost/predefined/normal/' . $filename, (string) $normalImg->encode());

            // Save List (thumbnail)
            $listImg = Image::make($image->getRealPath())->resize($list_width, $list_height, function ($c) { $c->aspectRatio(); });
            if ($watermarkExists) {
                $listImg->insert($watermarkPath, 'bottom-right', 10, 10);
            }
            Storage::disk('public')->put('adpost/predefined/list/' . $filename, (string) $listImg->encode());

            // Save Detail (medium)
            $detailImg = Image::make($image->getRealPath())->resize($detail_width, $detail_height, function ($c) { $c->aspectRatio(); });
            if ($watermarkExists) {
                $detailImg->insert($watermarkPath, 'bottom-right', 10, 10);
            }
            $fullPath = 'adpost/predefined/' . $filename;
            Storage::disk('public')->put($fullPath, (string) $detailImg->encode());

            // Save App List
            $appListImg = clone $img;
            if ($watermarkExists) {
                $appListImg->insert($watermarkPath, 'bottom-right', 10, 10);
            }
            Storage::disk('public')->put('adpost/applist/' . $filename, (string) $appListImg->encode());

            // Save App Detail
            $appDetailImg = clone $img;
            if ($watermarkExists) {
                $appDetailImg->insert($watermarkPath, 'bottom-right', 10, 10);
            }
            Storage::disk('public')->put('adpost/appdetail/' . $filename, (string) $appDetailImg->encode());

            return response()->json([
                'success' => true,
                'path' => $fullPath,
                'url' => asset('storage/' . $fullPath)
            ]);

        } catch (\Exception $e) {
            Log::error('Image upload failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Image upload failed: ' . $e->getMessage()], 500);
        }
    }

    // ==================================================
    // 4. STORE POST (Final Submission)
    // ==================================================
    public function store(Request $request)
    {
        // Determine user: authenticated user OR user_id from request
        $userId = Auth::id();
        if (!$userId && $request->has('user_id')) {
            $userId = $request->user_id;
        }
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'user_id is required when not authenticated.'], 422);
        }

        // Validation — state fields optional, currency defaults, images optional
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:255',
            'category_id' => 'required|exists:tbl_categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'required',
            'package_id' => 'required',
            'country_short' => 'required',
            'country_long' => 'required',
            'city_name' => 'required',
            'main_city_name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $settings = Setting::get_logos();

            // Currency: use provided or default
            $currencyId = $request->currency_id ?? ($settings['default_currency'] ?? 1);

            // Auto-resolve parent category to first sub-category
            $categoryId = $request->category_id;
            $category = TblCategory::find($categoryId);
            if ($category) {
                $hasChildren = TblCategory::where('parent_id', $categoryId)->exists();
                if ($hasChildren) {
                    $firstChild = TblCategory::where('parent_id', $categoryId)->orderBy('list_order', 'asc')->first();
                    if ($firstChild) {
                        $categoryId = $firstChild->id;
                    }
                }
            }

            // Location Logic
            $locationData = $this->processLocationData($request);

            // Package Logic
            $package = null;
            $active_status = 0;

            if ($request->package_id == 'free') {
                // Legacy "free" string support
                $active_status = 1;
                $package = Package::where('short_name', 'free')->first();
            } else {
                $package = Package::find($request->package_id);
                if ($package && strtolower($package->short_name) == 'free') {
                    // Check free post limit
                    $postCount = TblPostedAdPackageInfo::where('user_id', $userId)->sum('publish_count');
                    if ($package->single_pack_limit && $postCount >= $package->single_pack_limit) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Free post limit reached! Max ' . $package->single_pack_limit . ' ads allowed.'
                        ], 422);
                    }
                    $active_status = 1;
                }
            }

            $slug = Str::slug($request->title, "-") . '-' . (TblPost::count() + 1);

            // Images: use provided or empty string (optional)
            $uploadedImages = $request->uploaded_images ?? [];
            $imagesString = !empty($uploadedImages) ? implode(',', $uploadedImages) : '';

            // Locality string
            $cityNames = '';
            $cityName = $request->city_name ?? '';
            $mainCityName = $request->main_city_name ?? '';
            if ($cityName && $mainCityName && $cityName != $mainCityName) {
                $cityNames = $cityName . ',' . $mainCityName;
            } else {
                $cityNames = $cityName ?: $mainCityName;
            }

            // Create Post
            $post = TblPost::create([
                'user_id' => $userId,
                'category_id' => $categoryId,
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'slug' => $slug,
                'city' => $locationData['city_id'],
                'locality' => $cityNames,
                'images' => $imagesString,
                'currency_id' => $currencyId,
                'active' => $active_status,
                'product_condition' => $request->product_condition ?? 1,
                'exchange_to_buy' => $request->exchange_to_buy ?? 0,
                'fixed_price' => $request->fixed_price ?? 0,
                'instant_buy' => $request->instant_buy ?? 0,
                'video_url' => $request->video_url ?? '',
                'shipping_rate' => $request->shipping_rate ?? 0,
                'completeAddress' => $request->complete_address ?? '',
                'show_number' => $request->show_number ?? 0,
                'giving_away' => $request->giving_away ?? 0,
            ]);

            // Save Custom Fields
            if ($request->has('custom_fields')) {
                $this->saveCustomFields($post->id, $request->input('custom_fields'));
            }

            // Always create TblPostedAdPackageInfo (same as old API)
            $livingDays = ($package && $package->duration) ? $package->duration : 30;
            $currDate = date('Y-m-d');
            $endDate = date('Y-m-d', strtotime($currDate . '+' . $livingDays . ' days'));

            TblPostedAdPackageInfo::create([
                'user_id' => $userId,
                'post_id' => $post->id,
                'ad_type' => ($active_status == 1) ? 'free' : 'paid',
                'start_date' => $currDate,
                'end_date' => $endDate,
                'active' => '1'
            ]);

            // Build image URLs for response
            $imageUrls = [];
            foreach ($uploadedImages as $imgPath) {
                $imageUrls[] = asset('storage/' . $imgPath);
            }

            // Response based on package type
            if ($active_status == 1) {
                return response()->json([
                    'success' => true,
                    'type' => 'success',
                    'message' => 'Post created successfully!',
                    'post_id' => $post->id,
                    'slug' => $post->slug,
                    'url' => URL::to('/ad/' . $post->slug),
                    'images' => $imageUrls,
                    'active' => true,
                ]);
            }

            // Paid package — post created but needs payment
            $currency = Setting::get_admin_default_currency();
            $cid = $currency['id'] ?? 1;

            return response()->json([
                'success' => true,
                'type' => 'payment',
                'message' => 'Post created. Payment required to activate.',
                'post_id' => $post->id,
                'slug' => $post->slug,
                'amount' => $package ? $package->price : 0,
                'package_id' => $package ? $package->id : null,
                'payment_url' => URL::to('/paypal-payment-process?pack_amt=' . ($package ? $package->price : 0) . '&cid=' . $cid . '&post_id=' . $post->id . '&live_days=' . $livingDays . '&package_id=' . ($package ? $package->id : '') . '&payment_type=paypal&coupon_id=&uid=' . $userId . '&from_type=add-post'),
            ]);

        } catch (\Exception $e) {
            Log::error('Post store error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ==================================================
    // 5. GET MY POSTS
    // ==================================================
    public function myPosts(Request $request)
    {
        $userId = Auth::id();
        if (!$userId && $request->has('user_id')) {
            $userId = $request->user_id;
        }
        if (!$userId) {
            return response()->json(['success' => false, 'message' => 'user_id is required.'], 422);
        }

        $posts = TblPost::where('user_id', $userId)
            ->whereNull('deleted_at')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($post) {
                $images = [];
                if (!empty($post->images)) {
                    foreach (explode(',', $post->images) as $img) {
                        $img = trim($img);
                        if (!empty($img)) {
                            $images[] = asset('storage/' . $img);
                        }
                    }
                }
                $category = TblCategory::find($post->category_id);
                return [
                    'id' => $post->id,
                    'title' => $post->title,
                    'price' => $post->price,
                    'slug' => $post->slug,
                    'url' => URL::to('/ad/' . $post->slug),
                    'images' => $images,
                    'active' => (bool) $post->active,
                    'sold_status' => (bool) $post->sold_status,
                    'category' => $category ? $category->title : null,
                    'views_count' => $post->views_count ?? 0,
                    'created_at' => $post->created_at->toDateTimeString(),
                ];
            });

        return response()->json(['success' => true, 'data' => $posts]);
    }

    // ==================================================
    // 6. GET SINGLE POST
    // ==================================================
    public function getPost($id)
    {
        $post = TblPost::find($id);
        if (!$post) {
            return response()->json(['success' => false, 'message' => 'Post not found.'], 404);
        }

        $images = [];
        if (!empty($post->images)) {
            foreach (explode(',', $post->images) as $img) {
                $img = trim($img);
                if (!empty($img)) {
                    $images[] = asset('storage/' . $img);
                }
            }
        }

        $category = TblCategory::find($post->category_id);
        $currency = TblCurrency::find($post->currency_id);
        $city = TblCity::find($post->city);

        // Custom fields
        $customFields = TblPostValue::where('post_id', $post->id)->get()->map(function ($pv) {
            $field = TblFieldsDetail::find($pv->field_id);
            return [
                'field_id' => $pv->field_id,
                'field_name' => $field ? $field->name : null,
                'value' => $pv->value,
            ];
        });

        // Package info
        $packageInfo = TblPostedAdPackageInfo::where('post_id', $post->id)->where('active', '1')->first();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $post->id,
                'title' => $post->title,
                'description' => $post->description,
                'price' => $post->price,
                'slug' => $post->slug,
                'url' => URL::to('/ad/' . $post->slug),
                'images' => $images,
                'active' => (bool) $post->active,
                'sold_status' => (bool) $post->sold_status,
                'category' => $category ? $category->title : null,
                'category_id' => $post->category_id,
                'currency' => $currency ? $currency->short_code : null,
                'currency_symbol' => $currency ? $currency->currency_hex : null,
                'locality' => $post->locality,
                'city' => $city ? $city->name : null,
                'product_condition' => $post->product_condition,
                'exchange_to_buy' => (bool) $post->exchange_to_buy,
                'fixed_price' => (bool) $post->fixed_price,
                'instant_buy' => (bool) $post->instant_buy,
                'video_url' => $post->video_url,
                'views_count' => $post->views_count ?? 0,
                'custom_fields' => $customFields,
                'package' => $packageInfo ? [
                    'ad_type' => $packageInfo->ad_type,
                    'start_date' => $packageInfo->start_date,
                    'end_date' => $packageInfo->end_date,
                ] : null,
                'created_at' => $post->created_at->toDateTimeString(),
            ]
        ]);
    }

    // ==================== HELPER FUNCTIONS ====================

    private function processLocationData($request)
    {
        // Country
        $country = TblCountry::firstOrCreate(
            ['code' => $request->country_short],
            ['name' => $request->country_long]
        );

        // State (optional)
        $stateCode = $request->state_short ?? '';
        $stateName = $request->state_long ?? '';
        $state = null;
        if (!empty($stateCode) && !empty($stateName)) {
            $state = TblState::firstOrCreate(
                ['country_id' => $country->id, 'code' => $stateCode],
                ['name' => $stateName]
            );
        }

        // City
        $lat = $request->latitude ?? $request->city_lat ?? 0;
        $lng = $request->longitude ?? $request->city_lag ?? 0;

        $cityQuery = [
            'country_id' => $country->id,
            'state_id' => $state ? $state->id : 0,
            'name' => $request->main_city_name,
            'locality' => $request->city_name ?? $request->main_city_name,
        ];

        $city = TblCity::firstOrCreate($cityQuery, [
            'latitude' => $lat,
            'logitude' => $lng // Typo in original DB column
        ]);

        $cityNames = ($request->city_name ?? '') . ',' . ($request->main_city_name ?? '');

        return [
            'city_id' => $city->id,
            'cityNames' => $cityNames
        ];
    }

    private function saveCustomFields($post_id, $customFieldsData)
    {
        // Accept string (JSON) or array
        if (is_string($customFieldsData)) {
            $customFieldsData = json_decode($customFieldsData, true);
        }

        if (!is_array($customFieldsData)) return;

        $brandModelData = [];

        foreach ($customFieldsData as $key => $value) {
            if ($value === null || $value === '') continue;

            $parts = explode('_', $key, 2);
            if (count($parts) < 2) continue;

            $field_id = $parts[0];
            $field_slug = $parts[1];

            if ($field_slug === 'brandwithmodel') {
                $brandModelData[$field_id]['brand'] = $value;
                continue;
            }
            if ($field_slug === 'brandswithmodels') {
                $brandModelData[$field_id]['model'] = $value;
                continue;
            }

            TblPostValue::create([
                'post_id' => $post_id,
                'field_id' => $field_id,
                'value' => is_array($value) ? implode(',', $value) : $value,
                'active' => 1,
            ]);
        }

        foreach ($brandModelData as $f_id => $data) {
            $brand = $data['brand'] ?? '';
            $model = $data['model'] ?? '';
            if ($brand) {
                TblPostValue::create([
                    'post_id' => $post_id,
                    'field_id' => $f_id,
                    'value' => $brand . ',' . $model,
                    'active' => 1,
                ]);
            }
        }
    }
}
